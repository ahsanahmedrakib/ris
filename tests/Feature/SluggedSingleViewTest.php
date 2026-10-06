<?php

namespace Tests\Feature;

use App\Models\CampusEvent;
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
    public function a_notice_gets_a_serial_slug(): void
    {
        $notice = $this->createNotice(['title' => 'জাতীয় বিজ্ঞান মেলা ২০২৫']);

        // Title translations were dropped; every row is addressed by the
        // model prefix plus its primary key.
        $this->assertSame('notice-'.$notice->id, $notice->slug);
    }

    #[Test]
    public function a_campus_event_gets_a_serial_slug(): void
    {
        $item = CampusEvent::factory()->create(['title' => 'বিজ্ঞান ও প্রযুক্তি মেলা', 'date' => now()]);

        $this->assertSame('campus-event-'.$item->id, $item->slug);
    }

    #[Test]
    public function an_explicit_slug_is_left_alone_by_serial_generation(): void
    {
        $item = CampusEvent::create([
            'title' => 'Science Fair',
            'slug' => 'science-fair-2026',
            'date' => now(),
        ]);

        $this->assertSame('science-fair-2026', $item->slug);
    }

    #[Test]
    public function the_slug_of_a_soft_deleted_row_is_not_reused(): void
    {
        $first = $this->createNotice();
        $first->delete();

        // The row is gone from the listing but its slug still sits in the
        // unique index. The serial is bound to the primary key, so the next
        // record steps to its own id instead of reusing the freed one.
        $second = $this->createNotice();

        $this->assertNotSame($first->slug, $second->slug);
        $this->assertSame('notice-'.$second->id, $second->slug);
    }

    #[Test]
    public function two_records_with_the_same_title_get_different_slugs(): void
    {
        $first = $this->createNotice();
        $second = $this->createNotice();

        $this->assertNotSame($first->slug, $second->slug);
        $this->assertSame('notice-'.$second->id, $second->slug);
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
    public function campus_event_is_reachable_by_its_slug(): void
    {
        $item = CampusEvent::factory()->create(['title' => 'Science Fair 2026']);

        $this->get('/campus-events/'.$item->slug)
            ->assertOk()
            ->assertSee('Science Fair 2026');
    }

    #[Test]
    public function campus_events_have_an_index_page_that_lists_every_item(): void
    {
        $item = CampusEvent::factory()->create(['title' => 'Science Fair 2026', 'is_active' => true]);

        $this->get('/campus-events')
            ->assertOk()
            ->assertSee('Science Fair 2026')
            ->assertSee(route('campus-events.single', $item->slug), escape: false);
    }

    #[Test]
    public function the_index_page_has_a_canonical_and_collection_schema(): void
    {
        CampusEvent::factory()->create(['title' => 'Science Fair 2026', 'is_active' => true]);

        $this->get('/campus-events')
            ->assertOk()
            ->assertSee('<link rel="canonical" href="'.route('campus-events').'">', escape: false)
            ->assertSee('CollectionPage', escape: false);
    }

    #[Test]
    public function the_homepage_campus_card_links_to_the_single_view(): void
    {
        $item = CampusEvent::factory()->create(['title' => 'Science Fair 2026', 'is_active' => true]);

        $this->get('/')
            ->assertOk()
            ->assertSee(route('campus-events.single', $item->slug), escape: false);
    }

    #[Test]
    public function an_inactive_campus_event_cannot_be_opened_by_its_slug(): void
    {
        $item = CampusEvent::factory()->create(['is_active' => false]);

        $this->get('/campus-events/'.$item->slug)->assertNotFound();
    }

    #[Test]
    public function old_campus_life_urls_permanently_redirect_to_campus_events(): void
    {
        $item = CampusEvent::factory()->create(['title' => 'Science Fair 2026', 'is_active' => true]);

        $this->get('/campus-life')->assertRedirect(route('campus-events'));
        $this->get('/campus-life/'.$item->slug)->assertRedirect(route('campus-events.single', $item->slug));
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

        $this->assertSame($notice->fresh()->slug, $notice->slug);
    }
}
