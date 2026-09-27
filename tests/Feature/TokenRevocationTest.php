<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class TokenRevocationTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function an_active_user_reaches_the_admin_panel(): void
    {
        $this->login($admin = $this->admin());

        $this->fresh()->get(route('admin.dashboard'))->assertSuccessful();
    }

    #[Test]
    public function deactivating_a_user_invalidates_their_existing_web_session(): void
    {
        $admin = $this->admin();

        $this->login($admin);
        $this->fresh()->get(route('admin.dashboard'))->assertSuccessful();

        $admin->update(['is_active' => false]);

        $this->fresh()->get(route('admin.dashboard'))->assertRedirect(route('login'));
    }

    #[Test]
    public function deactivating_a_user_invalidates_their_existing_api_token(): void
    {
        $admin = $this->admin();
        $token = $this->apiToken($admin);

        $this->fresh()->withToken($token)->getJson(route('api.students.index'))->assertSuccessful();

        $admin->update(['is_active' => false]);

        $this->fresh()->withToken($token)->getJson(route('api.students.index'))->assertForbidden();
    }

    #[Test]
    public function changing_a_password_bumps_the_token_version(): void
    {
        $admin = $this->admin();
        $before = (int) $admin->token_version;

        $admin->update(['password' => 'An0ther-Str0ng-Pass!2026']);

        $this->assertSame($before + 1, (int) $admin->fresh()->token_version);
    }

    #[Test]
    public function changing_own_password_keeps_the_current_session_alive(): void
    {
        $admin = $this->admin();

        $this->login($admin);

        $this->fresh()->put(route('admin.profile.password'), [
            'current_password' => 'password',
            'password' => 'An0ther-Str0ng-Pass!2026',
            'password_confirmation' => 'An0ther-Str0ng-Pass!2026',
        ])->assertSessionHas('success');

        // The session used to change the password is re-stamped and survives.
        $this->fresh()->get(route('admin.dashboard'))->assertSuccessful();
    }

    #[Test]
    public function changing_a_password_retires_sessions_issued_before_it(): void
    {
        $admin = $this->admin();

        $this->login($admin);
        $this->fresh()->get(route('admin.dashboard'))->assertSuccessful();

        $admin->update(['password' => 'Str0ng-Passw0rd!2026']);

        $this->fresh()->get(route('admin.dashboard'))->assertRedirect(route('login'));
    }

    #[Test]
    public function changing_a_password_retires_api_tokens_issued_before_it(): void
    {
        $admin = $this->admin();
        $token = $this->apiToken($admin);

        $this->fresh()->withToken($token)->getJson(route('api.students.index'))->assertSuccessful();

        $admin->update(['password' => 'Str0ng-Passw0rd!2026']);

        $this->fresh()->withToken($token)->getJson(route('api.students.index'))->assertForbidden();
    }

    #[Test]
    public function changing_a_role_bumps_the_token_version(): void
    {
        $user = User::factory()->create(['role' => 'teacher']);
        $before = (int) $user->token_version;

        $user->update(['role' => 'admin']);

        $this->assertSame($before + 1, (int) $user->fresh()->token_version);
    }

    #[Test]
    public function a_token_issued_before_a_role_change_is_rejected(): void
    {
        $teacher = User::factory()->create(['role' => 'teacher']);
        $token = $this->apiToken($teacher);

        // Fees are admin-only, so the stale-role token is refused.
        $this->fresh()->withToken($token)->getJson(route('api.fees.index'))->assertForbidden();

        $teacher->update(['role' => 'admin']);

        // The stale token does not inherit the new role: it is refused until
        // the user authenticates again and receives a fresh token.
        $this->fresh()->withToken($token)->getJson(route('api.fees.index'))->assertForbidden();
    }

    #[Test]
    public function a_freshly_issued_token_works_after_a_role_change(): void
    {
        $teacher = User::factory()->create(['role' => 'teacher']);
        $this->apiToken($teacher);

        $teacher->update(['role' => 'admin']);

        $this->fresh()->withToken($this->apiToken($teacher))
            ->getJson(route('api.fees.index'))
            ->assertSuccessful();
    }

    #[Test]
    public function the_token_version_is_never_serialised(): void
    {
        $admin = $this->admin();

        $this->fresh()->withToken($this->apiToken($admin))
            ->getJson(route('api.auth.me'))
            ->assertSuccessful()
            ->assertJsonMissingPath('user.token_version');
    }

    #[Test]
    public function revoke_tokens_can_be_called_explicitly(): void
    {
        $user = $this->admin();
        $before = (int) $user->token_version;

        $user->revokeTokens();

        $this->assertSame($before + 1, (int) $user->fresh()->token_version);
    }

    /**
     * Simulates a fresh HTTP request.
     *
     * A test reuses the container across `$this->get()` calls, so the cached
     * auth guard keeps handing back the user object it loaded before the row
     * changed. Production builds a new guard for every request.
     */
    private function fresh(): static
    {
        Auth::forgetGuards();
        Auth::shouldUse('web');

        return $this;
    }

    /**
     * Logs in through the real route so the session is stamped with a token
     * version, exactly as a browser would receive it.
     */
    private function login(User $user): void
    {
        $this->post(route('login.submit'), [
            'email' => $user->email,
            'password' => 'password',
        ])->assertRedirect();
    }

    /**
     * Mints a genuine JWT so the API middleware validates a real claim rather
     * than an in-memory guard instance.
     */
    private function apiToken(User $user): string
    {
        return $this->postJson(route('api.auth.login'), [
            'email' => $user->email,
            'password' => 'password',
        ])->assertSuccessful()->json('token');
    }

    private function admin(): User
    {
        return User::factory()->create(['role' => 'admin']);
    }
}
