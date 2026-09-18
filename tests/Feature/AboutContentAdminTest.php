<?php

namespace Tests\Feature;

use App\Models\AboutContent;
use App\Models\CoreValue;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class AboutContentAdminTest extends TestCase
{
    use RefreshDatabase;

    private ?User $adminUser = null;

    private function admin(): User
    {
        return $this->adminUser ??= User::factory()->create(['role' => 'admin', 'email' => 'about-admin@example.com']);
    }

    #[Test]
    public function admin_can_view_about_content_index(): void
    {
        AboutContent::create([
            'type' => 'mission',
            'title' => 'আমাদের মিশন',
            'content' => 'মানসম্মত শিক্ষা প্রদান।',
        ]);

        $this->actingAs($this->admin())
            ->get('/admin/about')
            ->assertOk()
            ->assertSee('মিশন ও ভিশন')
            ->assertSee('মানসম্মত শিক্ষা প্রদান।');
    }

    #[Test]
    public function admin_can_store_mission_and_vision(): void
    {
        $this->actingAs($this->admin())
            ->post('/admin/about/mission-vision', [
                'type' => 'mission',
                'title' => 'আমাদের মিশন',
                'content' => 'প্রতিটি শিশুর সর্বোত্তম বিকাশ।',
            ])
            ->assertSessionHas('success');

        $this->actingAs($this->admin())
            ->post('/admin/about/mission-vision', [
                'type' => 'vision',
                'title' => 'আমাদের ভিশন',
                'content' => 'শীর্ষস্থানীয় শিক্ষাপ্রতিষ্ঠান।',
            ])
            ->assertSessionHas('success');

        $this->assertDatabaseHas('about_contents', ['type' => 'mission']);
        $this->assertDatabaseHas('about_contents', ['type' => 'vision']);
    }

    #[Test]
    public function admin_cannot_store_duplicate_type(): void
    {
        AboutContent::create([
            'type' => 'mission',
            'title' => 'আমাদের মিশন',
            'content' => 'পুরাতন।',
        ]);

        $this->actingAs($this->admin())
            ->post('/admin/about/mission-vision', [
                'type' => 'mission',
                'title' => 'নতুন মিশন',
                'content' => 'নতুন।',
            ])
            ->assertSessionHas('error');

        $this->assertSame(1, AboutContent::where('type', 'mission')->count());
    }

    #[Test]
    public function admin_can_update_mission(): void
    {
        $mission = AboutContent::create([
            'type' => 'mission',
            'title' => 'পুরাতন মিশন',
            'content' => 'পুরাতন বিবরণ।',
        ]);

        $this->actingAs($this->admin())
            ->put("/admin/about/mission-vision/{$mission->id}", [
                'type' => 'mission',
                'title' => 'নতুন মিশন',
                'content' => 'নতুন বিবরণ।',
            ])
            ->assertSessionHas('success');

        $this->assertDatabaseHas('about_contents', [
            'id' => $mission->id,
            'title' => 'নতুন মিশন',
            'content' => 'নতুন বিবরণ।',
        ]);
    }

    #[Test]
    public function admin_can_delete_mission(): void
    {
        $mission = AboutContent::create([
            'type' => 'mission',
            'title' => 'আমাদের মিশন',
            'content' => 'মুছে ফেলার বিবরণ।',
        ]);

        $this->actingAs($this->admin())
            ->delete("/admin/about/mission-vision/{$mission->id}")
            ->assertSessionHas('success');

        $this->assertDatabaseMissing('about_contents', ['id' => $mission->id]);
    }

    #[Test]
    public function admin_can_manage_core_values(): void
    {
        $this->actingAs($this->admin())
            ->post('/admin/about/core-values', [
                'title' => 'নৈতিকতা',
                'description' => 'সততা ও সম্মান।',
                'sort_order' => 1,
                'is_active' => true,
            ])
            ->assertSessionHas('success');

        $coreValue = CoreValue::firstOrFail();
        $this->assertTrue($coreValue->is_active);

        $this->actingAs($this->admin())
            ->put("/admin/about/core-values/{$coreValue->id}", [
                'title' => 'নৈতিকতা',
                'description' => 'সততা, সম্মান ও দায়িত্বশীলতা।',
                'sort_order' => 2,
                'is_active' => true,
            ])
            ->assertSessionHas('success');

        $this->assertSame(2, $coreValue->fresh()->sort_order);

        $this->actingAs($this->admin())
            ->patch("/admin/about/core-values/{$coreValue->id}/toggle-active")
            ->assertOk()
            ->assertJsonPath('is_active', false);

        $this->actingAs($this->admin())
            ->delete("/admin/about/core-values/{$coreValue->id}")
            ->assertSessionHas('success');

        $this->assertSoftDeleted('core_values', ['id' => $coreValue->id]);
    }

    #[Test]
    public function public_about_page_shows_dynamic_content(): void
    {
        AboutContent::create([
            'type' => 'mission',
            'title' => 'ডাইনামিক মিশন',
            'content' => 'ডাইনামিক মিশনের বিবরণ।',
        ]);

        CoreValue::create([
            'title' => 'ডাইনামিক মূল্যবোধ',
            'description' => 'মূল্যবোধের বিবরণ।',
            'is_active' => true,
            'sort_order' => 0,
        ]);

        $this->get('/about')
            ->assertOk()
            ->assertSee('ডাইনামিক মিশন')
            ->assertSee('ডাইনামিক মিশনের বিবরণ।')
            ->assertSee('ডাইনামিক মূল্যবোধ');
    }

    #[Test]
    public function public_about_page_shows_default_content_when_database_is_empty(): void
    {
        $defaults = AboutContent::defaults();

        $this->get('/about')
            ->assertOk()
            ->assertSee($defaults['mission']['title'])
            ->assertSee($defaults['mission']['content'])
            ->assertSee($defaults['vision']['title'])
            ->assertSee($defaults['vision']['content'])
            ->assertSee('আমাদের মূল নীতিসমূহ')
            ->assertSee(CoreValue::defaults()[0]['title']);
    }

    #[Test]
    public function public_about_page_prefers_database_content_over_defaults(): void
    {
        AboutContent::create([
            'type' => 'mission',
            'title' => 'ডেটাবেজ মিশন',
            'content' => 'ডেটাবেজ মিশনের বিবরণ।',
        ]);

        $this->get('/about')
            ->assertOk()
            ->assertSee('ডেটাবেজ মিশন')
            ->assertSee('ডেটাবেজ মিশনের বিবরণ।')
            ->assertDontSee(AboutContent::defaults()['mission']['content']);
    }

    #[Test]
    public function admin_about_index_never_shows_default_content(): void
    {
        $this->actingAs($this->admin())
            ->get('/admin/about')
            ->assertOk()
            ->assertDontSee(AboutContent::defaults()['mission']['content'])
            ->assertDontSee(AboutContent::defaults()['vision']['content'])
            ->assertDontSee(CoreValue::defaults()[0]['description']);
    }
}
