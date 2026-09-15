<?php

namespace Tests\Feature;

use App\Models\ActivityLog;
use App\Models\Notice;
use App\Models\ScholarshipRegistration;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class AdminTrashActivityTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::factory()->create(['role' => 'admin']);
    }

    private function makeNotice(User $user): Notice
    {
        return Notice::create([
            'title' => 'টেস্ট নোটিশ',
            'content' => 'টেস্ট কনটেন্ট',
            'type' => 'notice',
            'target_role' => 'all',
            'published_by' => $user->id,
            'published_at' => now(),
            'is_active' => true,
        ]);
    }

    #[Test]
    public function admin_can_view_activity_logs_page(): void
    {
        $this->actingAs($this->admin())
            ->get(route('admin.activity-logs.index'))
            ->assertOk()
            ->assertSee('অ্যাক্টিভিটি লগ');
    }

    #[Test]
    public function admin_actions_are_recorded_in_activity_logs(): void
    {
        $admin = $this->admin();

        $this->actingAs($admin);

        $this->makeNotice($admin);

        $this->assertDatabaseHas('activity_logs', [
            'action' => 'create',
            'subject_type' => Notice::class,
            'user_id' => $admin->id,
        ]);
    }

    #[Test]
    public function soft_deleted_records_appear_in_trash_and_can_be_restored(): void
    {
        $admin = $this->admin();
        $notice = $this->makeNotice($admin);

        $this->actingAs($admin)
            ->delete(route('admin.notices.destroy', $notice))
            ->assertRedirect();

        $this->assertSoftDeleted('notices', ['id' => $notice->id]);

        $this->get(route('admin.trash.index'))
            ->assertOk()
            ->assertSee('টেস্ট নোটিশ');

        $this->post(route('admin.trash.restore', ['notice', $notice->id]))
            ->assertRedirect();

        $this->assertNotSoftDeleted('notices', ['id' => $notice->id]);
    }

    #[Test]
    public function trashed_record_can_be_permanently_deleted(): void
    {
        $admin = $this->admin();
        $notice = $this->makeNotice($admin);

        $this->actingAs($admin)
            ->delete(route('admin.notices.destroy', $notice));

        $this->delete(route('admin.trash.force-delete', ['notice', $notice->id]))
            ->assertRedirect();

        $this->assertDatabaseMissing('notices', ['id' => $notice->id]);
    }

    #[Test]
    public function scholarship_registration_delete_is_soft_and_restorable(): void
    {
        $admin = $this->admin();
        $registration = ScholarshipRegistration::factory()->create();

        $this->actingAs($admin)
            ->delete(route('admin.scholarship.destroy', $registration))
            ->assertRedirect();

        $this->assertSoftDeleted('scholarship_registrations', ['id' => $registration->id]);

        $this->get(route('admin.trash.index'))
            ->assertOk()
            ->assertSee($registration->registration_no);

        $this->post(route('admin.trash.restore', ['scholarship', $registration->id]))
            ->assertRedirect();

        $this->assertNotSoftDeleted('scholarship_registrations', ['id' => $registration->id]);
    }

    #[Test]
    public function login_accepts_username_instead_of_email(): void
    {
        $user = User::factory()->create([
            'username' => 'saiful',
            'email' => 'saiful@ris.edu.bd',
            'password' => 'Admin@123',
            'role' => 'admin',
        ]);

        $this->post(route('login'), [
            'email' => 'saiful',
            'password' => 'Admin@123',
        ])->assertRedirect(route('admin.dashboard'));

        $this->assertAuthenticatedAs($user);
    }

    #[Test]
    public function activity_log_model_stores_changes_on_update(): void
    {
        $admin = $this->admin();
        $notice = $this->makeNotice($admin);

        $this->actingAs($admin);

        $notice->update(['title' => 'আপডেটেড নোটিশ']);

        $log = ActivityLog::where('subject_type', Notice::class)
            ->where('subject_id', $notice->id)
            ->where('action', 'update')
            ->first();

        $this->assertNotNull($log);
        $this->assertSame('আপডেটেড নোটিশ', $log->properties['title']);
    }
}
