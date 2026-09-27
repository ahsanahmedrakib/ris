<?php

namespace Tests\Feature;

use App\Features\Auth\Notifications\ResetPasswordNotification;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class PasswordResetTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function the_forgot_password_page_renders(): void
    {
        $this->freshWeb()->get(route('password.request'))->assertSuccessful()->assertSee('পাসওয়ার্ড রিসেট');
    }

    #[Test]
    public function a_reset_link_is_sent_for_a_known_address(): void
    {
        Notification::fake();

        $user = $this->user();

        $this->freshWeb()->post(route('password.email'), ['email' => $user->email])
            ->assertSessionHas('status');

        Notification::assertSentTo($user, ResetPasswordNotification::class);
    }

    #[Test]
    public function an_unknown_address_gets_the_identical_response(): void
    {
        Notification::fake();

        $known = $this->user();

        $this->freshWeb()->post(route('password.email'), ['email' => $known->email])->assertSessionHasNoErrors();
        $forKnown = session('status');

        $this->freshWeb()->post(route('password.email'), ['email' => 'nobody@example.com'])->assertSessionHasNoErrors();
        $forUnknown = session('status');

        // Identical wording, so the form cannot be used to enumerate accounts.
        $this->assertNotNull($forKnown);
        $this->assertSame($forKnown, $forUnknown);

        Notification::assertSentToTimes($known, ResetPasswordNotification::class, 1);
    }

    #[Test]
    public function the_reset_form_renders_with_the_token(): void
    {
        $this->freshWeb()->get(route('password.reset', ['token' => 'a-token', 'email' => 'user@example.com']))
            ->assertSuccessful()
            ->assertSee('a-token', escape: false);
    }

    #[Test]
    public function a_password_can_be_reset_with_a_valid_token(): void
    {
        Notification::fake();

        $user = $this->user();
        $token = $this->issueToken($user);

        $this->post(route('password.update'), [
            'token' => $token,
            'email' => $user->email,
            'password' => 'N3w-Str0ng-Pass!',
            'password_confirmation' => 'N3w-Str0ng-Pass!',
        ])->assertRedirect(route('login'));

        $this->assertTrue(Hash::check('N3w-Str0ng-Pass!', $user->fresh()->password));
    }

    #[Test]
    public function resetting_retires_existing_tokens_and_sessions(): void
    {
        Notification::fake();

        $user = $this->user();
        $token = $this->issueToken($user);
        $versionBefore = (int) $user->token_version;

        $this->freshWeb()->post(route('password.email'), ['email' => $user->email]);

        $apiToken = $this->postJson(route('api.auth.login'), [
            'email' => $user->email,
            'password' => 'password',
        ])->assertSuccessful()->json('token');

        $this->withToken($apiToken)->getJson(route('api.auth.me'))->assertSuccessful();

        $this->freshWeb()->post(route('password.update'), [
            'token' => $token,
            'email' => $user->email,
            'password' => 'N3w-Str0ng-Pass!',
            'password_confirmation' => 'N3w-Str0ng-Pass!',
        ])->assertRedirect(route('login'));

        // A reset must retire the old credentials exactly once. Bumping twice
        // would still invalidate them, but it hides a redundant write and makes
        // the stored version impossible to reason about.
        $this->assertSame($versionBefore + 1, (int) $user->fresh()->token_version);

        $this->withToken($apiToken)
            ->getJson(route('api.auth.me'))
            ->assertForbidden();
    }

    #[Test]
    public function an_invalid_token_is_rejected(): void
    {
        $user = $this->user();

        $this->post(route('password.update'), [
            'token' => 'not-a-real-token',
            'email' => $user->email,
            'password' => 'N3w-Str0ng-Pass!',
            'password_confirmation' => 'N3w-Str0ng-Pass!',
        ])->assertSessionHasErrors('email');

        $this->assertTrue(Hash::check('password', $user->fresh()->password));
    }

    #[Test]
    public function a_token_for_another_address_is_rejected(): void
    {
        Notification::fake();

        $user = $this->user();
        $other = $this->user();
        $token = $this->issueToken($other);

        $this->post(route('password.update'), [
            'token' => $token,
            'email' => $user->email,
            'password' => 'N3w-Str0ng-Pass!',
            'password_confirmation' => 'N3w-Str0ng-Pass!',
        ])->assertSessionHasErrors('email');

        $this->assertTrue(Hash::check('password', $user->fresh()->password));
    }

    #[Test]
    public function a_weak_password_is_rejected(): void
    {
        Notification::fake();

        $user = $this->user();
        $token = $this->issueToken($user);

        $this->post(route('password.update'), [
            'token' => $token,
            'email' => $user->email,
            'password' => 'short',
            'password_confirmation' => 'short',
        ])->assertSessionHasErrors('password');

        $this->assertTrue(Hash::check('password', $user->fresh()->password));
    }

    #[Test]
    public function the_token_cannot_be_reused(): void
    {
        Notification::fake();

        $user = $this->user();
        $token = $this->issueToken($user);

        $this->post(route('password.update'), [
            'token' => $token,
            'email' => $user->email,
            'password' => 'N3w-Str0ng-Pass!',
            'password_confirmation' => 'N3w-Str0ng-Pass!',
        ])->assertRedirect(route('login'));

        $this->post(route('password.update'), [
            'token' => $token,
            'email' => $user->email,
            'password' => 'An0ther-Pass!2026',
            'password_confirmation' => 'An0ther-Pass!2026',
        ])->assertSessionHasErrors('email');

        $this->assertTrue(Hash::check('N3w-Str0ng-Pass!', $user->fresh()->password));
    }

    #[Test]
    public function an_authenticated_user_cannot_reach_the_reset_pages(): void
    {
        $this->actingAs($this->user())
            ->get(route('password.request'))
            ->assertRedirect();
    }

    /**
     * Simulates a fresh browser request.
     *
     * A test reuses the container, so the guard cached by an earlier API request
     * (and the default guard that Laravel's Authenticate middleware switched to
     * `api`) would otherwise leak into this web request.
     */
    private function freshWeb(): static
    {
        Auth::forgetGuards();
        Auth::shouldUse('web');
        $this->flushHeaders();

        return $this;
    }

    /**
     * Requests a real reset link and pulls the signed token back out of the URL
     * the notification would have emailed.
     */
    private function issueToken(User $user): string
    {
        $this->freshWeb()->post(route('password.email'), ['email' => $user->email])
            ->assertSessionHasNoErrors();

        $notification = Notification::sent($user, ResetPasswordNotification::class)->last();

        $this->assertNotNull($notification, 'No reset notification was sent.');

        $url = (new \ReflectionProperty($notification, 'url'))->getValue($notification);

        // The token is a route parameter, so it travels in the path.
        $path = (string) parse_url($url, PHP_URL_PATH);

        $this->assertSame(1, preg_match('#/reset-password/([^/?]+)$#', $path, $matches), "Unexpected reset URL: {$url}");

        return $matches[1];
    }

    private function user(): User
    {
        return User::factory()->create(['role' => 'admin']);
    }
}
