<?php

namespace Tests\Feature;

use App\Models\Admission;
use App\Models\ScholarshipRegistration;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\TestResponse;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * The status dropdowns in these two tables save in the background, because
 * submitting them natively reloaded the page and discarded scroll position,
 * filters and any open modal to change a single cell.
 *
 * These tests pin the two halves of that behaviour: the view must not fall back
 * to a native submit, and the endpoint must answer the fetch with JSON rather
 * than a redirect the browser would try to follow.
 */
class InlineStatusUpdateTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @return array<string, array<int, string>>
     */
    public static function tables(): array
    {
        return [
            'scholarship' => ['admin.scholarship.index'],
            'admission' => ['admin.admission.index'],
        ];
    }

    #[Test]
    #[DataProvider('tables')]
    public function the_status_table_still_renders(string $route): void
    {
        $this->actingAs($this->admin())->get(route($route))->assertSuccessful();
    }

    #[Test]
    public function the_scholarship_table_saves_without_a_native_submit(): void
    {
        ScholarshipRegistration::factory()->create(['status' => 'pending']);

        $html = (string) $this->actingAs($this->admin())
            ->get(route('admin.scholarship.index'))
            ->assertSuccessful()
            ->getContent();

        // The exact old handler. Matching the bare string would also hit the
        // pagination per_page filter and the comment explaining the change.
        $this->assertStringNotContainsString('@change="$el.form.submit()"', $html);
        $this->assertStringContainsString('RisAdmin.statusRow(', $html);
        $this->assertStringContainsString('@change="save()"', $html);
        $this->assertStringContainsString(':class="badge"', $html);
    }

    #[Test]
    public function the_admission_table_saves_without_a_native_submit(): void
    {
        Admission::factory()->create(['status' => 'pending']);

        $html = (string) $this->actingAs($this->admin())
            ->get(route('admin.admission.index'))
            ->assertSuccessful()
            ->getContent();

        $this->assertStringNotContainsString('@change="$el.form.submit()"', $html);
        $this->assertStringContainsString('RisAdmin.statusRow(', $html);
        $this->assertStringContainsString('@change="save()"', $html);
        $this->assertStringContainsString(':class="badge"', $html);
    }

    #[Test]
    public function the_admission_row_scope_drives_the_admit_button(): void
    {
        // The admit button used to be server-rendered from the status alone, so
        // a background save could never reveal it. It is now bound to the row
        // scope the status dropdown writes to.
        Admission::factory()->create(['status' => 'pending']);

        $html = (string) $this->actingAs($this->admin())
            ->get(route('admin.admission.index'))
            ->assertSuccessful()
            ->getContent();

        $this->assertStringContainsString('x-show="canAdmit"', $html);
    }

    #[Test]
    public function the_scholarship_status_endpoint_answers_a_fetch_with_json(): void
    {
        $registration = ScholarshipRegistration::factory()->create(['status' => 'pending']);

        $response = $this->saveStatus('admin.scholarship.status', $registration, 'approved');

        $response->assertOk();
        $response->assertJsonStructure(['message', 'type', 'redirect']);
        $this->assertSame('success', $response->json('type'));

        $this->assertDatabaseHas('scholarship_registrations', [
            'id' => $registration->id,
            'status' => 'approved',
        ]);
    }

    #[Test]
    public function the_admission_status_endpoint_answers_a_fetch_with_json(): void
    {
        $admission = Admission::factory()->create(['status' => 'pending']);

        $response = $this->saveStatus('admin.admission.status', $admission, 'approved');

        $response->assertOk();
        $response->assertJsonStructure(['message', 'type', 'redirect']);

        $this->assertDatabaseHas('admissions', [
            'id' => $admission->id,
            'status' => 'approved',
        ]);
    }

    #[Test]
    public function the_scholarship_status_endpoint_rejects_an_unknown_status(): void
    {
        $registration = ScholarshipRegistration::factory()->create(['status' => 'pending']);

        $this->saveStatus('admin.scholarship.status', $registration, 'nonsense')->assertStatus(422);

        $this->assertDatabaseHas('scholarship_registrations', [
            'id' => $registration->id,
            'status' => 'pending',
        ]);
    }

    #[Test]
    public function a_non_admin_cannot_change_a_status(): void
    {
        $teacher = User::factory()->create(['role' => 'teacher']);
        $registration = ScholarshipRegistration::factory()->create(['status' => 'pending']);

        $this->actingAs($teacher)
            ->post(
                route('admin.scholarship.status', $registration),
                ['_method' => 'PATCH', 'status' => 'approved'],
                ['Accept' => 'application/json'],
            )
            ->assertForbidden();

        $this->assertDatabaseHas('scholarship_registrations', [
            'id' => $registration->id,
            'status' => 'pending',
        ]);
    }

    /**
     * Replays the request the dropdown actually sends.
     *
     * A plain PATCH would not test the real path: the browser cannot send a
     * multipart body with PATCH, so the row posts a FormData with a _method
     * spoof instead. This asserts that Laravel still resolves the verb, that the
     * redirect is converted to JSON for the fetch, and that neither relies on
     * the page reloading.
     */
    private function saveStatus(string $route, Model $model, string $status): TestResponse
    {
        return $this->actingAs($this->admin())->post(
            route($route, $model),
            ['_method' => 'PATCH', 'status' => $status],
            ['Accept' => 'application/json', 'X-Requested-With' => 'XMLHttpRequest'],
        );
    }

    #[Test]
    public function the_status_helper_is_exposed_on_the_admin_bundle(): void
    {
        // The dropdowns call RisAdmin.statusRow, so a missing export would throw
        // on every page load rather than fail quietly.
        $html = (string) $this->actingAs($this->admin())->get(route('admin.scholarship.index'))->getContent();

        $this->assertStringContainsString('function statusRow(', $html);
        $this->assertStringContainsString('submitForm, refreshTable, toast, csrf, statusRow', $html);
    }

    private function admin(): User
    {
        return User::factory()->create(['role' => 'admin']);
    }
}
