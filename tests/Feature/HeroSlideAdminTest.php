<?php

namespace Tests\Feature;

use App\Models\HeroSlide;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class HeroSlideAdminTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::factory()->create(['role' => 'admin', 'email' => 'hero-admin@example.com']);
    }

    #[Test]
    public function admin_can_view_hero_slides_index(): void
    {
        HeroSlide::create([
            'title' => 'শিক্ষাই আলোকিত ভবিষ্যতের পথ',
            'subtitle' => 'সেরা শিক্ষা অভিজ্ঞতা।',
            'image' => 'hero-slides/hero.jpg',
            'btn_text' => 'ভর্তি করুন',
            'link' => 'admission',
            'sort_order' => 0,
            'is_active' => true,
        ]);

        $this->actingAs($this->admin())
            ->get('/admin/hero-slides')
            ->assertOk()
            ->assertSee('হিরো স্লাইডার')
            ->assertSee('শিক্ষাই আলোকিত ভবিষ্যতের পথ');
    }

    #[Test]
    public function admin_can_create_hero_slide(): void
    {
        Storage::fake('public');

        $this->actingAs($this->admin())
            ->post('/admin/hero-slides', [
                'title' => 'আধুনিক শিক্ষা পদ্ধতি',
                'subtitle' => 'ডিজিটাল ক্লাসরুম',
                'image' => UploadedFile::fake()->image('hero.jpg', 1920, 800),
                'btn_text' => 'ভর্তি করুন',
                'link' => 'admission',
                'sort_order' => 2,
                'is_active' => true,
            ])
            ->assertRedirect(route('admin.hero-slides.index'));

        $slide = HeroSlide::where('title', 'আধুনিক শিক্ষা পদ্ধতি')->firstOrFail();

        Storage::disk('public')->assertExists($slide->image);

        $this->assertDatabaseHas('hero_slides', [
            'title' => 'আধুনিক শিক্ষা পদ্ধতি',
            'link' => 'admission',
            'sort_order' => 2,
            'is_active' => true,
        ]);
    }

    #[Test]
    public function admin_can_update_hero_slide(): void
    {
        Storage::fake('public');

        $slide = HeroSlide::create([
            'title' => 'পুরোনো শিরোনাম',
            'subtitle' => 'পুরোনো বিবরণ',
            'image' => 'hero-slides/old.jpg',
            'btn_text' => 'নির্বাচন করুন',
            'link' => 'about',
            'sort_order' => 0,
            'is_active' => true,
        ]);

        $this->actingAs($this->admin())
            ->put("/admin/hero-slides/{$slide->id}", [
                'title' => 'নতুন শিরোনাম',
                'subtitle' => 'নতুন বিবরণ',
                'image' => UploadedFile::fake()->image('new.jpg', 1920, 800),
                'btn_text' => 'যোগাযোগ করুন',
                'link' => 'contact',
                'sort_order' => 1,
                'is_active' => false,
            ])
            ->assertRedirect(route('admin.hero-slides.index'));

        $slide->refresh();

        Storage::disk('public')->assertExists($slide->image);
        Storage::disk('public')->assertMissing('hero-slides/old.jpg');

        $this->assertDatabaseHas('hero_slides', [
            'id' => $slide->id,
            'title' => 'নতুন শিরোনাম',
            'link' => 'contact',
            'is_active' => false,
        ]);
    }

    #[Test]
    public function admin_can_update_hero_slide_without_replacing_image(): void
    {
        Storage::fake('public');

        $slide = HeroSlide::create([
            'title' => 'একটি স্লাইড',
            'subtitle' => null,
            'image' => 'hero-slides/keep.jpg',
            'btn_text' => null,
            'link' => null,
            'sort_order' => 0,
            'is_active' => true,
        ]);

        $this->actingAs($this->admin())
            ->put("/admin/hero-slides/{$slide->id}", [
                'title' => 'হালনাগাদ স্লাইড',
                'is_active' => true,
            ])
            ->assertRedirect(route('admin.hero-slides.index'));

        $this->assertDatabaseHas('hero_slides', [
            'id' => $slide->id,
            'title' => 'হালনাগাদ স্লাইড',
            'image' => 'hero-slides/keep.jpg',
        ]);
    }

    #[Test]
    public function admin_can_toggle_hero_slide_active_status(): void
    {
        $slide = HeroSlide::create([
            'title' => 'টগল স্লাইড',
            'subtitle' => null,
            'image' => 'hero-slides/toggle.jpg',
            'btn_text' => null,
            'link' => null,
            'sort_order' => 0,
            'is_active' => true,
        ]);

        $this->actingAs($this->admin())
            ->patch("/admin/hero-slides/{$slide->id}/toggle-active")
            ->assertOk()
            ->assertJson(['is_active' => false]);

        $this->assertDatabaseHas('hero_slides', ['id' => $slide->id, 'is_active' => false]);
    }

    #[Test]
    public function admin_can_delete_hero_slide(): void
    {
        $slide = HeroSlide::create([
            'title' => 'মুছে ফেলার স্লাইড',
            'subtitle' => null,
            'image' => 'hero-slides/delete.jpg',
            'btn_text' => null,
            'link' => null,
            'sort_order' => 0,
            'is_active' => true,
        ]);

        $this->actingAs($this->admin())
            ->delete("/admin/hero-slides/{$slide->id}")
            ->assertRedirect(route('admin.hero-slides.index'));

        $this->assertSoftDeleted('hero_slides', ['id' => $slide->id]);
    }

    #[Test]
    public function website_shows_default_slides_when_no_db_data(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee('hero-1.jpg')
            ->assertSee('hero-10.jpg')
            ->assertSee('শিক্ষাই আলোকিত ভবিষ্যতের পথ');
    }

    #[Test]
    public function website_shows_db_slides_when_present(): void
    {
        HeroSlide::create([
            'title' => 'অ্যাডমিন থেকে যোগ করা স্লাইড',
            'subtitle' => 'এটি ডাটাবেজ থেকে আসছে।',
            'image' => 'hero-slides/custom.jpg',
            'btn_text' => 'যোগাযোগ করুন',
            'link' => 'contact',
            'sort_order' => 0,
            'is_active' => true,
        ]);

        $this->get('/')
            ->assertOk()
            ->assertSee('অ্যাডমিন থেকে যোগ করা স্লাইড')
            ->assertDontSee('hero-10.jpg');
    }

    #[Test]
    public function inactive_slides_are_not_shown_on_website(): void
    {
        HeroSlide::create([
            'title' => 'নিষ্ক্রিয় স্লাইড',
            'subtitle' => null,
            'image' => 'hero-slides/inactive.jpg',
            'btn_text' => null,
            'link' => null,
            'sort_order' => 0,
            'is_active' => false,
        ]);

        $this->get('/')
            ->assertOk()
            ->assertSee('hero-10.jpg')
            ->assertDontSee('নিষ্ক্রিয় স্লাইড');
    }
}
