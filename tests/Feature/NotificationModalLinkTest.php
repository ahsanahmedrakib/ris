<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Clicking a notification used to land on the JSON endpoint that feeds a list
 * view's modal, for example /admin/scholarship/69/show, so the browser showed
 * a wall of raw JSON. The link now targets the listing page with ?view=<id>
 * and the page opens the matching record in its own modal.
 */
class NotificationModalLinkTest extends TestCase
{
    use RefreshDatabase;

    private const MIGRATION = '2026_09_27_184434_rewrite_notification_links_to_modal_urls.php';

    #[Test]
    #[DataProvider('listingPages')]
    public function the_listing_page_opens_the_record_named_in_the_query(string $route): void
    {
        $html = $this->render($route);

        $this->assertStringContainsString(
            'RisAdmin.takeViewParam()',
            $html,
            "{$route} never reads the ?view= parameter.",
        );
        $this->assertStringContainsString(
            'this.openViewModal(pendingView)',
            $html,
            "{$route} does not open the record named in the query string.",
        );
    }

    /**
     * @return array<string, array<int, string>>
     */
    public static function listingPages(): array
    {
        return [
            'scholarship' => ['admin.scholarship.index'],
            'admission' => ['admin.admission.index'],
            'testimonials' => ['admin.testimonials.index'],
            'contact messages' => ['admin.contact-messages.index'],
        ];
    }

    #[Test]
    public function the_helper_is_exported_and_only_accepts_a_numeric_id(): void
    {
        $html = $this->render('admin.scholarship.index');

        $this->assertStringContainsString(
            'return { submitForm, refreshTable, toast, csrf, statusRow, takeViewParam };',
            $html,
            'takeViewParam is not exported by RisAdmin, so the pages cannot call it.',
        );

        // The id ends up interpolated into a fetch URL, so anything that is
        // not a plain integer has to be discarded rather than escaped.
        $this->assertMatchesRegularExpression(
            '~if \(raw === null \|\| !\s?/\^\\\\d\+\$/\.test\(raw\)\) return null;~',
            $html,
            'takeViewParam does not reject a non-numeric record id.',
        );
    }

    #[Test]
    #[DataProvider('storedNotificationUrls')]
    public function stored_notifications_point_at_the_listing_page(string $type, string $legacyUrl, string $expected): void
    {
        $id = 'n-'.$type;
        $this->store($id, $type, $legacyUrl);

        $this->migration()->up();

        $data = $this->payload($id);

        $this->assertSame($expected, $data['url']);
        $this->assertSame('t', $data['title'], 'The migration must not disturb the rest of the payload.');
    }

    #[Test]
    #[DataProvider('storedNotificationUrls')]
    public function rolling_back_restores_the_original_link(string $type, string $legacyUrl): void
    {
        $id = 'r-'.$type;
        $this->store($id, $type, $legacyUrl);

        $migration = $this->migration();
        $migration->up();
        $migration->down();

        $this->assertSame(
            parse_url($legacyUrl, PHP_URL_PATH),
            $this->payload($id)['url'],
            "Rolling back did not restore the {$type} endpoint.",
        );
    }

    #[Test]
    public function a_notification_for_an_unknown_type_is_left_alone(): void
    {
        $this->store('x', 'something-else', '/admin/whatever/5/show');

        $this->migration()->up();

        $this->assertSame('/admin/whatever/5/show', $this->payload('x')['url']);
    }

    #[Test]
    public function an_already_converted_link_is_not_converted_twice(): void
    {
        $this->store('y', 'scholarship', '/admin/scholarship?view=69');

        $this->migration()->up();

        $this->assertSame('/admin/scholarship?view=69', $this->payload('y')['url']);
    }

    /**
     * @return array<string, array<int, string>>
     */
    public static function storedNotificationUrls(): array
    {
        return [
            'scholarship' => ['scholarship', 'https://app.test/admin/scholarship/69/show', '/admin/scholarship?view=69'],
            'admission' => ['admission', 'https://app.test/admin/admission/7/show', '/admin/admission?view=7'],
            'testimonial' => ['testimonial', 'https://app.test/admin/testimonials/3', '/admin/testimonials?view=3'],
            'contact' => ['contact', 'https://app.test/admin/contact-messages/11', '/admin/contact-messages?view=11'],
        ];
    }

    private function migration(): Migration
    {
        $file = database_path('migrations/'.self::MIGRATION);

        $this->assertFileExists($file);

        return require $file;
    }

    private function store(string $id, string $type, string $url): void
    {
        DB::table('notifications')->insert([
            'id' => $id,
            'type' => 'DatabaseNotification',
            'notifiable_type' => User::class,
            'notifiable_id' => 1,
            'data' => json_encode(['type' => $type, 'title' => 't', 'message' => 'm', 'url' => $url]),
            'read_at' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    private function payload(string $id): array
    {
        return (array) json_decode((string) DB::table('notifications')->where('id', $id)->value('data'), true);
    }

    private function render(string $route): string
    {
        $admin = User::factory()->create(['role' => 'admin']);

        return (string) $this->actingAs($admin)
            ->get(route($route))
            ->assertSuccessful()
            ->getContent();
    }
}
