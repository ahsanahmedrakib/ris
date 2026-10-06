<?php

namespace Tests\Feature;

use App\Models\CampusEvent;
use App\Models\Faq;
use App\Models\Notice;
use App\Models\SchoolStatistic;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class SeoTest extends TestCase
{
    use RefreshDatabase;

    private ?User $adminUser = null;

    private function admin(): User
    {
        return $this->adminUser ??= User::factory()->create([
            'role' => 'admin',
            'email' => 'seo-admin@example.com',
        ]);
    }

    private function notice(array $attributes = []): Notice
    {
        return Notice::create(array_merge([
            'title' => 'Mid-Term Exam Schedule',
            'content' => 'The mid-term examination begins next Monday.',
            'type' => 'notice',
            'category' => 'exam',
            'published_by' => $this->admin()->id,
            'published_at' => now()->subDay(),
            'is_active' => true,
        ], $attributes));
    }

    #[Test]
    public function the_home_page_has_a_title_description_canonical_and_school_schema(): void
    {
        $response = $this->get('/');

        $response->assertOk()
            ->assertSee('<title>', escape: false)
            ->assertSee('<meta name="description"', escape: false)
            ->assertSee('<link rel="canonical" href="'.route('home').'">', escape: false)
            ->assertSee('application/ld+json', escape: false)
            ->assertSee('"@type":"School"', escape: false);
    }

    #[Test]
    public function the_home_page_publishes_visible_faqs_as_faq_schema(): void
    {
        $faq = Faq::create([
            'question' => 'What classes does your school offer?',
            'answer' => 'Nursery through secondary education.',
            'is_active' => true,
            'sort_order' => 1,
        ]);

        $response = $this->get('/');

        $response->assertOk()
            ->assertSee('"@type":"FAQPage"', escape: false)
            ->assertSee($faq->question, escape: false);
    }

    #[Test]
    public function each_static_page_has_its_own_title_and_canonical(): void
    {
        $cases = [
            '/about' => route('about'),
            '/admission' => route('admission'),
            '/scholarship' => route('scholarship'),
            '/contact' => route('contact'),
            '/notices' => route('notices'),
            '/teachers' => route('teachers'),
            '/testimonials' => route('testimonials'),
            '/gallery' => route('gallery'),
            '/class-routine' => route('class-routine'),
            '/academic/calendar' => route('academic.calendar'),
            '/academic/fees' => route('academic.fees'),
            '/academic/facilities' => route('academic.facilities'),
        ];

        foreach ($cases as $path => $canonical) {
            $response = $this->get($path);

            $response->assertOk()
                ->assertSee('<title>', escape: false)
                ->assertSee('<link rel="canonical" href="'.$canonical.'">', escape: false);
        }
    }

    #[Test]
    public function a_notice_page_uses_its_own_title_and_article_schema(): void
    {
        $notice = $this->notice();

        $this->get('/notices/'.$notice->slug)
            ->assertOk()
            ->assertSee(mb_substr($notice->title, 0, 40), escape: false)
            ->assertSee('<link rel="canonical" href="'.route('notices.single', $notice->slug).'">', escape: false)
            ->assertSee('"@type":"Article"', escape: false);
    }

    #[Test]
    public function campus_events_use_article_schema_and_their_own_metadata(): void
    {
        $item = CampusEvent::factory()->create([
            'title' => 'Annual Science Fair 2026',
            'description' => 'Students built working solar models at the campus fair.',
            'date' => now(),
            'is_active' => true,
        ]);

        $this->get('/campus-events/'.$item->slug)
            ->assertOk()
            ->assertSee('<link rel="canonical" href="'.route('campus-events.single', $item->slug).'">', escape: false)
            ->assertSee('"@type":"Article"', escape: false);
    }

    #[Test]
    public function the_sitemap_lists_static_and_dynamic_urls(): void
    {
        $notice = $this->notice();

        CampusEvent::factory()->create([
            'title' => 'Sports Day',
            'is_active' => true,
            'date' => now(),
        ]);

        $response = $this->get('/sitemap.xml');

        $response->assertOk()
            ->assertHeader('Content-Type', 'application/xml; charset=UTF-8')
            ->assertSee('<loc>'.route('home').'</loc>', escape: false)
            ->assertSee('<loc>'.route('about').'</loc>', escape: false)
            ->assertSee('<loc>'.route('notices.single', $notice->slug).'</loc>', escape: false);
    }

    #[Test]
    public function school_statistics_are_only_published_as_schema_when_they_exist(): void
    {
        SchoolStatistic::create([
            'total_students' => 250,
            'total_teachers' => 18,
            'total_classes' => 10,
            'total_staff' => 5,
            'founding_year' => 2010,
        ]);

        $this->get('/')
            ->assertOk()
            ->assertSee('"numberOfStudents"', escape: false)
            ->assertSee('"value":250', escape: false);
    }
}
