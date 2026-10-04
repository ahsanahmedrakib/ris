<?php

namespace Tests\Feature;

use App\Models\CampusNews;
use App\Models\Notice;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class SluggedSingleViewTest extends TestCase
{
    use RefreshDatabase;

    private ?User $adminUser = null;

    private function admin(): User
    {
        return $this->adminUser ??= User::factory()->create(['role' => 'admin', 'email' => 'slug-admin@example.com']);
    }

    private function createNotice(array $attributes = []): Notice
    {
        return Notice::create(array_merge([
            'title' => 'Annual Exam Schedule',
            'content' => 'The annual examination begins next week.',
            'type' => 'notice',
            'category' => 'exam',
            'published_by' => $this->admin()->id,
            'published_at' => now()->subDay(),
            'is_active' => true,
        ], $attributes));
    }

    #[Test]
    public function a_notice_gets_a_slug_from_its_title(): void
    {
        $notice = $this->createNotice();

        $this->assertSame('annual-exam-schedule', $notice->slug);
    }

    #[Test]
    public function a_title_with_nothing_to_transliterate_falls_back_to_the_identifier(): void
    {
        // Bengali titles transliterate to something usable, but a title of
        // nothing but punctuation and symbols slugifies to an empty string, and
        // that case still has to produce a routable address.
        $notice = $this->createNotice(['title' => '★']);

        $this->assertSame('notice-'.$notice->id, $notice->slug);
    }

    #[Test]
    public function two_records_with_the_same_title_get_different_slugs(): void
    {
        $first = $this->createNotice();
        $second = $this->createNotice();

        $this->assertNotSame($first->slug, $second->slug);
        $this->assertSame('annual-exam-schedule-2', $second->slug);
    }

    #[Test]
    public function editing_a_title_leaves_the_published_address_alone(): void
    {
        $notice = $this->createNotice();
        $slug = $notice->slug;

        $notice->update(['title' => 'A Completely Different Title']);

        $this->assertSame($slug, $notice->fresh()->slug);
    }

    #[Test]
    public function the_notice_page_is_reachable_by_its_slug(): void
    {
        $notice = $this->createNotice();

        $this->get('/notices/'.$notice->slug)
            ->assertOk()
            ->assertSee('Annual Exam Schedule');
    }

    #[Test]
    public function the_notice_board_links_to_the_single_view(): void
    {
        $notice = $this->createNotice();

        $this->get('/notices')
            ->assertOk()
            ->assertSee(route('notices.single', $notice->slug), escape: false);
    }

    #[Test]
    public function an_inactive_notice_cannot_be_opened_by_its_slug(): void
    {
        $notice = $this->createNotice(['is_active' => false]);

        $this->get('/notices/'.$notice->slug)->assertNotFound();
    }

    #[Test]
    public function a_notice_that_is_not_published_yet_cannot_be_opened(): void
    {
        $notice = $this->createNotice(['published_at' => now()->addWeek()]);

        $this->get('/notices/'.$notice->slug)->assertNotFound();
    }

    #[Test]
    public function campus_life_is_reachable_by_its_slug(): void
    {
        $item = CampusNews::factory()->create(['title' => 'Science Fair 2026']);

        $this->get('/campus-life/'.$item->slug)
            ->assertOk()
            ->assertSee('Science Fair 2026');
    }

    #[Test]
    public function an_inactive_campus_life_item_cannot_be_opened_by_its_slug(): void
    {
        $item = CampusNews::factory()->create(['is_active' => false]);

        $this->get('/campus-life/'.$item->slug)->assertNotFound();
    }

    #[Test]
    public function an_admin_can_set_a_readable_slug_by_hand(): void
    {
        $this->actingAs($this->admin())
            ->post('/admin/notices', [
                'title' => 'Science Fair',
                'slug' => 'Science Fair 2026!',
                'content' => 'The fair is on Friday.',
                'type' => 'notice',
                'category' => 'general',
                'is_active' => true,
            ])
            ->assertRedirect(route('admin.notices.index'));

        // Typed with capitals and a space, stored as the address it will be.
        $this->assertDatabaseHas('notices', ['slug' => 'science-fair-2026']);
    }

    #[Test]
    public function a_slug_already_taken_by_another_record_is_rejected(): void
    {
        $notice = $this->createNotice();

        $this->actingAs($this->admin())
            ->post('/admin/notices', [
                'title' => 'Another notice',
                'slug' => $notice->slug,
                'content' => 'Body copy.',
                'type' => 'notice',
                'category' => 'general',
                'is_active' => true,
            ])
            ->assertSessionHasErrors('slug');
    }

    #[Test]
    public function blanking_the_slug_on_edit_keeps_the_current_address(): void
    {
        $notice = $this->createNotice();

        $this->actingAs($this->admin())
            ->put('/admin/notices/'.$notice->id, [
                'title' => 'Annual Exam Schedule Revised',
                'slug' => '',
                'content' => 'The revised schedule is out.',
                'type' => 'notice',
                'category' => 'exam',
                'is_active' => true,
            ])
            ->assertRedirect(route('admin.notices.index'));

        $this->assertSame('annual-exam-schedule', $notice->fresh()->slug);
    }
}
