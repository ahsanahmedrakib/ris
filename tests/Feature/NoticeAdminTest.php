<?php

namespace Tests\Feature;

use App\Models\Notice;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class NoticeAdminTest extends TestCase
{
    use RefreshDatabase;

    private ?User $adminUser = null;

    private function admin(): User
    {
        return $this->adminUser ??= User::factory()->create(['role' => 'admin', 'email' => 'notice-admin@example.com']);
    }

    #[Test]
    public function admin_can_view_notices_index(): void
    {
        Notice::create([
            'title' => 'সাধারণ ছুটি ঘোষণা',
            'content' => 'আগামী সপ্তাহে সাধারণ ছুটি।',
            'type' => 'holiday',
            'category' => 'holiday',
            'published_by' => $this->admin()->id,
            'is_active' => true,
        ]);

        $this->actingAs($this->admin())
            ->get('/admin/notices')
            ->assertOk()
            ->assertSee('নোটিশ ব্যবস্থাপনা')
            ->assertSee('সাধারণ ছুটি ঘোষণা');
    }

    #[Test]
    public function admin_can_store_notice_through_modal(): void
    {
        $this->actingAs($this->admin())
            ->post('/admin/notices', [
                'title' => 'বার্ষিক পরীক্ষার সময়সূচি',
                'content' => 'বার্ষিক পরীক্ষা শুরুর তারিখ ঘোষণা।',
                'type' => 'notice',
                'category' => 'exam',
                'target_role' => 'all',
                'is_active' => true,
            ])
            ->assertRedirect(route('admin.notices.index'));

        $this->assertDatabaseHas('notices', [
            'title' => 'বার্ষিক পরীক্ষার সময়সূচি',
            'category' => 'exam',
            'is_active' => true,
        ]);
    }

    #[Test]
    public function admin_can_fetch_notice_json_for_view_modal(): void
    {
        $notice = Notice::create([
            'title' => 'ভর্তি বিজ্ঞপ্তি',
            'content' => 'নতুন শিক্ষাবর্ষের ভর্তি চলছে।',
            'type' => 'notice',
            'category' => 'admission',
            'target_role' => 'all',
            'published_by' => $this->admin()->id,
            'is_active' => true,
        ]);

        $this->actingAs($this->admin())
            ->getJson("/admin/notices/{$notice->id}")
            ->assertOk()
            ->assertJsonPath('title', 'ভর্তি বিজ্ঞপ্তি')
            ->assertJsonPath('category', 'admission')
            ->assertJsonPath('category_label', 'ভর্তি')
            ->assertJsonPath('type_label', 'নোটিশ')
            ->assertJsonPath('target_label', 'সকল')
            ->assertJsonPath('status_label', 'সক্রিয়');
    }

    #[Test]
    public function admin_can_fetch_notice_json_for_edit_modal(): void
    {
        $notice = Notice::create([
            'title' => 'পুরাতন শিরোনাম',
            'content' => 'পুরাতন বিবরণ।',
            'type' => 'notice',
            'category' => 'general',
            'target_role' => 'parent',
            'published_by' => $this->admin()->id,
            'is_active' => false,
        ]);

        $this->actingAs($this->admin())
            ->getJson("/admin/notices/{$notice->id}/edit")
            ->assertOk()
            ->assertJsonPath('title', 'পুরাতন শিরোনাম')
            ->assertJsonPath('type', 'notice')
            ->assertJsonPath('category', 'general')
            ->assertJsonPath('target_role', 'parent')
            ->assertJsonPath('is_active', false);
    }

    #[Test]
    public function admin_can_update_notice(): void
    {
        $notice = Notice::create([
            'title' => 'আপডেটের আগের শিরোনাম',
            'content' => 'বিবরণ।',
            'type' => 'notice',
            'category' => 'general',
            'published_by' => $this->admin()->id,
            'is_active' => true,
        ]);

        $this->actingAs($this->admin())
            ->put("/admin/notices/{$notice->id}", [
                'title' => 'আপডেটের পরের শিরোনাম',
                'content' => 'নতুন বিবরণ।',
                'type' => 'event',
                'category' => 'exam',
                'target_role' => 'all',
                'is_active' => false,
            ])
            ->assertRedirect(route('admin.notices.index'));

        $this->assertDatabaseHas('notices', [
            'id' => $notice->id,
            'title' => 'আপডেটের পরের শিরোনাম',
            'type' => 'event',
            'category' => 'exam',
            'is_active' => false,
        ]);
    }

    #[Test]
    public function admin_can_download_notices_csv(): void
    {
        Notice::create([
            'title' => 'সিভিএসের জন্য নোটিশ',
            'content' => 'ডাউনলোড পরীক্ষার নোটিশ।',
            'type' => 'notice',
            'category' => 'general',
            'published_by' => $this->admin()->id,
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->admin())
            ->get('/admin/notices/download')
            ->assertOk()
            ->assertHeader('content-type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet')
            ->assertHeaderContains('content-disposition', '.xlsx');

        $this->assertXlsxContainsText($response->getContent(), 'শিরোনাম');
    }

    private function assertXlsxContainsText(string $xlsx, string $needle): void
    {
        $temp = tempnam(sys_get_temp_dir(), 'xlsx_test_');

        file_put_contents($temp, $xlsx);

        try {
            $zip = new \ZipArchive;
            $this->assertTrue($zip->open($temp) === true, 'Downloaded file is not a valid XLSX archive.');
            $shared = $zip->getFromName('xl/sharedStrings.xml');
            $this->assertStringContainsString($needle, $shared, 'Shared strings do not contain expected text.');
            $zip->close();
        } finally {
            @unlink($temp);
        }
    }
}
