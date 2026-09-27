<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\RateLimiter;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class RateLimitTest extends TestCase
{
    use RefreshDatabase;

    private function forgetLimiter(string ...$keys): void
    {
        foreach ($keys as $key) {
            RateLimiter::clear($key);
        }
    }

    #[Test]
    public function repeated_failed_logins_are_throttled(): void
    {
        $this->forgetLimiter('login', 'login-ip');

        $user = User::factory()->create([
            'role' => UserRole::Admin->value,
            'email' => 'admin@school.test',
            'password' => 'correct-horse-battery',
        ]);

        for ($attempt = 0; $attempt < 5; $attempt++) {
            $this->post(route('login.submit'), [
                'email' => $user->email,
                'password' => 'wrong-password',
            ]);
        }

        $response = $this->post(route('login.submit'), [
            'email' => $user->email,
            'password' => 'correct-horse-battery',
        ]);

        $response->assertStatus(429);
    }

    #[Test]
    public function a_successful_login_is_not_throttled(): void
    {
        $this->forgetLimiter('login', 'login-ip');

        $user = User::factory()->create([
            'role' => UserRole::Admin->value,
            'email' => 'admin@school.test',
            'password' => 'correct-horse-battery',
        ]);

        $this->post(route('login.submit'), [
            'email' => $user->email,
            'password' => 'correct-horse-battery',
        ])->assertRedirect();

        $this->assertAuthenticatedAs($user);
    }

    #[Test]
    public function throttling_is_scoped_per_email_so_one_account_cannot_lock_another(): void
    {
        $this->forgetLimiter('login', 'login-ip');

        $victim = User::factory()->create([
            'role' => UserRole::Admin->value,
            'email' => 'victim@school.test',
            'password' => 'correct-horse-battery',
        ]);

        for ($attempt = 0; $attempt < 5; $attempt++) {
            $this->post(route('login.submit'), [
                'email' => 'attacker@school.test',
                'password' => 'wrong-password',
            ]);
        }

        $this->post(route('login.submit'), [
            'email' => $victim->email,
            'password' => 'correct-horse-battery',
        ])->assertRedirect();

        $this->assertAuthenticatedAs($victim);
    }

    #[Test]
    public function public_contact_submissions_are_throttled(): void
    {
        $this->forgetLimiter('contact');

        for ($attempt = 0; $attempt < 5; $attempt++) {
            $this->post(route('contact.send'), [
                'name' => 'Visitor '.$attempt,
                'email' => 'visitor'.$attempt.'@school.test',
                'message' => 'Hello there.',
            ]);
        }

        $this->post(route('contact.send'), [
            'name' => 'Visitor 6',
            'email' => 'visitor6@school.test',
            'message' => 'Hello there.',
        ])->assertStatus(429);
    }

    #[Test]
    public function api_requests_are_throttled_per_authenticated_user(): void
    {
        $this->forgetLimiter('api');

        $teacher = User::factory()->create(['role' => UserRole::Teacher->value]);

        for ($request = 0; $request < 60; $request++) {
            $this->actingAs($teacher, 'api')->getJson(route('api.classes.index'));
        }

        $this->actingAs($teacher, 'api')
            ->getJson(route('api.classes.index'))
            ->assertStatus(429);
    }
}
