<?php

namespace Tests\Feature;

use App\Models\ScholarshipRegistration;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ReproUpdateTest extends TestCase
{
    use RefreshDatabase;

    private function headers(): array
    {
        return [
            'Accept' => 'application/json',
            'X-Requested-With' => 'XMLHttpRequest',
        ];
    }

    private function createRegistration(): ScholarshipRegistration
    {
        return ScholarshipRegistration::factory()->create([
            'student_name' => 'fefee', 'father_name' => 'x', 'mother_name' => 'y',
            'school_name' => 'z', 'class_no' => 1, 'roll_no' => '44',
            'mobile_no' => '01765555555', 'bkash_no' => null,
            'payment_method' => 'cash', 'status' => 'pending',
        ]);
    }

    private function fields(): array
    {
        return [
            'student_name' => 'fefee', 'father_name' => 'x', 'mother_name' => 'y',
            'school_name' => 'z', 'class_no' => '1', 'roll_no' => '44',
            'mobile_no' => '01765555555', 'bkash_no' => '',
            'payment_method' => 'cash', 'status' => 'pending',
        ];
    }

    #[Test]
    public function browser_post_with_method_spoofing_succeeds(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $reg = $this->createRegistration();

        $this->actingAs($admin)
            ->post(route('admin.scholarship.update', $reg), [
                '_method' => 'PUT',
                ...$this->fields(),
            ], $this->headers())
            ->assertOk()
            ->assertJsonPath('type', 'success');

        $this->assertSame('01765555555', $reg->fresh()->mobile_no);
    }

    #[Test]
    public function raw_put_with_multipart_loses_all_request_data_like_php_does(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $reg = $this->createRegistration();

        // PHP does not parse multipart/form-data bodies for PUT/PATCH/DELETE,
        // so a raw fetch(url, {method:'PUT', body: FormData}) arrives with no
        // request data and Laravel rejects every required field. This is the
        // exact bug the AJAX submit helper had (fixed by going through POST
        // + _method spoofing).
        $boundary = '----RisBoundary42';
        $body = collect(array_merge(['_method' => 'PUT'], $this->fields()))
            ->map(fn ($value, $key) => "--{$boundary}\r\nContent-Disposition: form-data; name=\"{$key}\"\r\n\r\n{$value}\r\n")
            ->implode('');
        $body .= "--{$boundary}--\r\n";

        $this->actingAs($admin)
            ->call('PUT', route('admin.scholarship.update', $reg), [], [], [], [
                'HTTP_ACCEPT' => 'application/json',
                'HTTP_X-REQUESTED-WITH' => 'XMLHttpRequest',
                'CONTENT_TYPE' => 'multipart/form-data; boundary='.$boundary,
            ], $body)
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['student_name', 'father_name', 'mother_name', 'school_name', 'class_no', 'roll_no', 'mobile_no', 'payment_method']);
    }

    #[Test]
    public function put_with_all_fields_empty_returns_422_with_all_errors(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $reg = ScholarshipRegistration::factory()->create([
            'mobile_no' => '01765555555', 'payment_method' => 'cash',
        ]);

        $this->actingAs($admin)
            ->put(route('admin.scholarship.update', $reg), [
                '_method' => 'PUT',
                'student_name' => '', 'father_name' => '', 'mother_name' => '',
                'school_name' => '', 'class_no' => '', 'roll_no' => '',
                'mobile_no' => '', 'bkash_no' => '',
                'payment_method' => '', 'status' => '',
            ], $this->headers())
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['student_name', 'father_name', 'mother_name', 'school_name', 'class_no', 'roll_no', 'mobile_no', 'payment_method', 'status']);
    }
}
