<?php

namespace Tests\Feature;

use App\Models\AcademicYear;
use App\Models\ClassRoom;
use App\Models\HeroSlide;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Blade;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class SkeletonLoadingTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::factory()->create(['role' => 'admin', 'email' => 'skeleton-admin@example.com']);
    }

    #[Test]
    public function website_layout_ships_the_page_skeleton_loader(): void
    {
        $this->get(route('home'))
            ->assertOk()
            ->assertSee('id="ris-page-skeleton"', false)
            ->assertSee('ris-skeleton-page', false)
            ->assertSee('skeleton', false);
    }

    #[Test]
    public function website_skeleton_loader_is_driven_by_the_navigation_script(): void
    {
        $script = file_get_contents(resource_path('js/app.js'));

        $this->assertStringContainsString('window.RisSkeleton', $script);
        $this->assertStringContainsString("getElementById('ris-page-skeleton')", $script);
    }

    #[Test]
    public function admin_table_refresh_renders_skeleton_rows(): void
    {
        HeroSlide::create([
            'title' => 'স্কেলিটন টেস্ট স্লাইড',
            'subtitle' => 'ব্যবস্থাপনা',
            'image' => 'hero-slides/hero.jpg',
            'btn_text' => 'ভর্তি করুন',
            'link' => 'admission',
            'sort_order' => 0,
            'is_active' => true,
        ]);

        $this->actingAs($this->admin())
            ->get(route('admin.hero-slides.index'))
            ->assertOk()
            ->assertSee('data-table-body', false)
            ->assertSee('showTableSkeleton', false)
            ->assertSee('skeleton h-4', false);
    }

    #[Test]
    public function admin_view_and_edit_modals_use_skeletons_while_loading(): void
    {
        $this->actingAs($this->admin())
            ->get(route('admin.hero-slides.index'))
            ->assertOk()
            ->assertSee('py-10 px-6', false)
            ->assertSee('shrink-0 w-14 h-14', false)
            ->assertDontSee('animate-spin', false);
    }

    #[Test]
    public function class_routine_page_ships_a_skeleton_for_in_place_loading(): void
    {
        AcademicYear::create([
            'name' => '2026',
            'start_date' => '2026-01-01',
            'end_date' => '2026-12-31',
            'is_current' => true,
        ]);

        ClassRoom::create([
            'name' => '১ম',
            'section' => 'ক',
            'academic_year_id' => AcademicYear::first()->id,
        ]);

        $this->get(route('class-routine'))
            ->assertOk()
            ->assertSee('id="routine-skeleton"', false)
            ->assertSee('id="routine-grid"', false)
            ->assertSee('showSkeleton', false);
    }

    #[Test]
    public function skeleton_modal_component_renders_placeholder_blocks(): void
    {
        $html = Blade::render('<x-skeleton.modal />');

        $this->assertStringContainsString('skeleton', $html);
        $this->assertStringNotContainsString('animate-spin', $html);
    }
}
