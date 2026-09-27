<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class UserManagementTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::factory()->create(['role' => 'admin', 'is_active' => true]);
    }

    #[Test]
    public function admin_can_create_an_admin_user(): void
    {
        $this->actingAs($this->admin());

        $this->post(route('admin.users.store'), [
            'name' => 'Rakib Hasan',
            'username' => 'rakib',
            'email' => 'rakib@school.local',
            'phone' => '01712345678',
            'password' => 'secret123',
            'password_confirmation' => 'secret123',
        ])->assertRedirect(route('admin.users.index'));

        $this->assertDatabaseHas('users', [
            'name' => 'Rakib Hasan',
            'username' => 'rakib',
            'email' => 'rakib@school.local',
            'role' => 'admin',
            'is_active' => true,
        ]);

        $this->assertTrue(Hash::check('secret123', User::where('email', 'rakib@school.local')->value('password')));
    }

    #[Test]
    public function admin_can_update_an_admin_user(): void
    {
        $user = User::factory()->create(['role' => 'admin', 'email' => 'old@school.local']);

        $this->actingAs($this->admin());

        $this->put(route('admin.users.update', $user), [
            'name' => 'Updated Name',
            'username' => 'updated',
            'email' => 'updated@school.local',
            'phone' => '01987654321',
            'password' => '',
            'password_confirmation' => '',
        ])->assertRedirect(route('admin.users.index'));

        $user->refresh();

        $this->assertSame('Updated Name', $user->name);
        $this->assertSame('updated', $user->username);
        $this->assertSame('updated@school.local', $user->email);
        $this->assertSame('01987654321', $user->phone);
    }

    #[Test]
    public function admin_can_update_own_password_when_provided(): void
    {
        $user = User::factory()->create(['role' => 'admin', 'password' => 'oldpass12']);

        $this->actingAs($this->admin());

        $this->put(route('admin.users.update', $user), [
            'name' => $user->name,
            'username' => $user->username,
            'email' => $user->email,
            'phone' => '',
            'password' => 'newpass123',
            'password_confirmation' => 'newpass123',
        ])->assertRedirect(route('admin.users.index'));

        $this->assertTrue(Hash::check('newpass123', $user->refresh()->password));
    }

    #[Test]
    public function admin_can_delete_another_admin_user(): void
    {
        $user = User::factory()->create(['role' => 'admin']);

        $this->actingAs($this->admin());

        $this->delete(route('admin.users.destroy', $user))
            ->assertRedirect(route('admin.users.index'));

        $this->assertSoftDeleted('users', ['id' => $user->id]);
    }

    #[Test]
    public function admin_cannot_delete_own_account(): void
    {
        $admin = $this->admin();

        $this->actingAs($admin);

        $this->delete(route('admin.users.destroy', $admin))
            ->assertRedirect();

        $this->assertDatabaseHas('users', ['id' => $admin->id]);
    }

    #[Test]
    public function admin_can_toggle_another_admin_active_status(): void
    {
        $user = User::factory()->create(['role' => 'admin', 'is_active' => true]);

        $this->actingAs($this->admin());

        $response = $this->patchJson(route('admin.users.toggle-active', $user))
            ->assertOk();

        $this->assertFalse($response->json('is_active'));
        $this->assertFalse((bool) $user->refresh()->is_active);
    }

    #[Test]
    public function teacher_cannot_access_admin_user_management(): void
    {
        /** @var User $teacher */
        $teacher = User::factory()->create(['role' => 'teacher', 'is_active' => true]);

        $this->actingAs($teacher);

        $this->get(route('admin.users.index'))->assertForbidden();
        $this->post(route('admin.users.store'))->assertForbidden();
    }

    #[Test]
    public function logging_in_never_creates_an_administrator(): void
    {
        // Provisioning used to run inside the login request, so an anonymous
        // POST to /login conjured a known-password admin whenever the users
        // table was empty.
        $this->assertSame(0, User::count());

        $this->post(route('login.submit'), [
            'email' => 'admin@ris.local',
            'password' => 'password',
        ]);

        $this->assertSame(0, User::count());
        $this->assertGuest();
    }

    #[Test]
    public function the_provision_command_refuses_to_use_the_known_default_password(): void
    {
        config(['auth.default_admin.password' => 'password']);

        $this->artisan('app:provision-admin', ['--password' => 'password'])
            ->assertFailed();

        $this->assertSame(0, User::count());
    }

    #[Test]
    public function the_provision_command_creates_an_administrator_with_a_strong_password(): void
    {
        config(['auth.default_admin.password' => null]);

        $this->artisan('app:provision-admin', [
            '--name' => 'Head Teacher',
            '--username' => 'head',
            '--email' => 'head@school.test',
            '--password' => 'Str0ng-Passw0rd!2026',
        ])->assertSuccessful();

        $admin = User::where('email', 'head@school.test')->firstOrFail();

        $this->assertSame('admin', $admin->role);
        $this->assertTrue($admin->is_active);
        $this->assertTrue(Hash::check('Str0ng-Passw0rd!2026', $admin->password));
    }

    #[Test]
    public function the_provision_command_does_nothing_once_an_admin_exists(): void
    {
        $this->admin();

        $this->artisan('app:provision-admin', [
            '--email' => 'second@school.test',
            '--password' => 'Str0ng-Passw0rd!2026',
        ])->assertSuccessful();

        $this->assertNull(User::where('email', 'second@school.test')->first());
    }
}
