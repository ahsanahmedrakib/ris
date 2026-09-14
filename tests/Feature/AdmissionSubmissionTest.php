<?php

namespace Tests\Feature;

use App\Models\Admission;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class AdmissionSubmissionTest extends TestCase
{
    use RefreshDatabase;

    public const VALID_DATA = [
        'academic_year' => '২৬',
        'student_name_bn' => 'মোঃ রাকিব হাসান',
        'student_name_en' => 'MD. RAKIB HASAN',
        'dob' => '2019-05-10',
        'age' => '৬',
        'nationality' => 'বাংলাদেশী',
        'religion' => 'ইসলাম',
        'blood_group' => 'O+',
        'father_name_bn' => 'আব্দুল করিম',
        'father_name_en' => 'ABDUL KARIM',
        'father_occupation' => 'চাকরিজীবী',
        'mother_name_bn' => 'ফাতেমা বেগম',
        'mother_name_en' => 'FATEMA BEGUM',
        'mother_occupation' => 'গৃহিণী',
        'present_address' => 'গোপালগঞ্জ, বাংলাদেশ',
        'permanent_address' => 'গোপালগঞ্জ, বাংলাদেশ',
        'phone' => '01712345678',
        'emergency_contact' => '01712345679',
        'legal_guardian_name' => 'মোঃ করিম উদ্দিন',
        'legal_guardian_occupation' => 'ব্যবসায়ী',
        'legal_guardian_relation' => 'চাচা',
        'legal_guardian_address' => 'গোপালগঞ্জ',
        'local_guardian_name' => 'মোঃ রহমান',
        'local_guardian_occupation' => 'শিক্ষক',
        'local_guardian_relation' => 'চাচা',
        'local_guardian_address' => 'গোপালগঞ্জ',
        'local_guardian_phone' => '01712345680',
        'prev_school_name' => 'সরকারি প্রাথমিক বিদ্যালয়',
        'prev_school_address' => 'গোপালগঞ্জ',
        'prev_marks' => '৮০',
        'reference' => 'মোঃ আহমেদ',
        'reference_phone' => '01712345681',
        'class_level' => ['নার্সারি'],
        'batch' => ['প্রভাতী'],
    ];

    #[Test]
    public function admission_page_loads_with_form(): void
    {
        $this->get(route('admission'))
            ->assertOk()
            ->assertSee('প্রাথমিক আবেদন ফরম')
            ->assertSee('আবেদন জমা দিন');
    }

    private static function toBanglaDigits(string $number): string
    {
        return strtr($number, [
            '0' => '০',
            '1' => '১',
            '2' => '২',
            '3' => '৩',
            '4' => '৪',
            '5' => '৫',
            '6' => '৬',
            '7' => '৭',
            '8' => '৮',
            '9' => '৯',
        ]);
    }

    #[Test]
    public function admission_page_defaults_academic_year_to_next_year_from_march_through_december(): void
    {
        $nextYearTwoDigits = self::toBanglaDigits((string) ((now()->addYear()->year) % 100));

        foreach ([3, 6, 12] as $month) {
            $this->travelTo(now()->setDate(now()->year, $month, 15));

            $this->get(route('admission'))
                ->assertOk()
                ->assertSee('value="'.$nextYearTwoDigits.'"', false);
        }
    }

    #[Test]
    public function admission_page_defaults_academic_year_to_current_year_in_january_and_february(): void
    {
        $currentYearTwoDigits = self::toBanglaDigits((string) (now()->year % 100));

        foreach ([1, 2] as $month) {
            $this->travelTo(now()->setDate(now()->year, $month, 15));

            $this->get(route('admission'))
                ->assertOk()
                ->assertSee('value="'.$currentYearTwoDigits.'"', false);
        }
    }

    #[Test]
    public function next_admission_number_uses_class_and_academic_year_format(): void
    {
        $year = now()->format('n') >= 3 ? now()->addYear()->format('y') : now()->format('y');

        $this->assertSame("{$year}-1001", Admission::nextAdmissionNo('১ম শ্রেণি'));
        $this->assertSame("{$year}-1001", Admission::nextAdmissionNo('১ম'));
        $this->assertSame("{$year}-2001", Admission::nextAdmissionNo('২য় শ্রেণি'));
        $this->assertSame("{$year}-5001", Admission::nextAdmissionNo('৫ম'));
        $this->assertSame("{$year}-P001", Admission::nextAdmissionNo('প্লে'));
        $this->assertSame("{$year}-N001", Admission::nextAdmissionNo('নার্সারি'));
        $this->assertSame("{$year}-K001", Admission::nextAdmissionNo('কেজি'));
        $this->assertNull(Admission::nextAdmissionNo(null));
    }

    #[Test]
    public function next_admission_number_increments_serially_per_class(): void
    {
        $year = now()->format('n') >= 3 ? now()->addYear()->format('y') : now()->format('y');

        Admission::factory()->create(['admission_no' => "{$year}-1001"]);
        Admission::factory()->create(['admission_no' => "{$year}-1002"]);

        $this->assertSame("{$year}-1003", Admission::nextAdmissionNo('১ম শ্রেণি'));
        $this->assertSame("{$year}-2001", Admission::nextAdmissionNo('২য় শ্রেণি'));
        $this->assertSame("{$year}-P001", Admission::nextAdmissionNo('প্লে'));
    }

    #[Test]
    public function public_form_validates_required_inputs(): void
    {
        $this->post(route('admission.store'), [])
            ->assertSessionHasErrors([
                'academic_year',
                'student_name_bn',
                'student_name_en',
                'dob',
                'age',
                'nationality',
                'religion',
                'blood_group',
                'father_name_bn',
                'father_name_en',
                'father_occupation',
                'mother_name_bn',
                'mother_name_en',
                'mother_occupation',
                'present_address',
                'permanent_address',
                'phone',
                'emergency_contact',
                'legal_guardian_name',
                'legal_guardian_occupation',
                'legal_guardian_relation',
                'legal_guardian_address',
                'local_guardian_name',
                'local_guardian_occupation',
                'local_guardian_relation',
                'local_guardian_address',
                'local_guardian_phone',
                'reference',
                'reference_phone',
                'student_photo',
            ]);

        $this->assertDatabaseCount('admissions', 0);
    }

    #[Test]
    public function store_creates_admission_without_admission_number(): void
    {
        Storage::fake('public');

        $response = $this->post(route('admission.store'), [
            ...self::VALID_DATA,
            'student_photo' => UploadedFile::fake()->image('student.jpg', 200, 200),
            'email' => 'test@example.com',
        ]);

        $response->assertRedirect(route('admission'));

        $this->assertDatabaseHas('admissions', [
            'status' => 'pending',
            'student_name_bn' => 'মোঃ রাকিব হাসান',
            'father_name_bn' => 'আব্দুল করিম',
            'mother_name_bn' => 'ফাতেমা বেগম',
            'email' => 'test@example.com',
        ]);

        $this->assertNull(Admission::first()->admission_no);
        $this->assertDatabaseCount('admissions', 1);
    }

    #[Test]
    public function store_returns_json_for_ajax_requests(): void
    {
        Storage::fake('public');

        $this->post(route('admission.store'), [
            ...self::VALID_DATA,
            'student_photo' => UploadedFile::fake()->image('student.jpg', 200, 200),
        ], [
            'Accept' => 'application/json',
            'X-Requested-With' => 'XMLHttpRequest',
        ])
            ->assertOk()
            ->assertJson(fn ($json) => $json->where('message', fn ($m) => str_contains($m, 'সফলভাবে জমা হয়েছে'))->missing('errors'));

        $this->assertDatabaseCount('admissions', 1);
    }

    #[Test]
    public function store_returns_json_validation_errors_for_ajax_requests(): void
    {
        $this->post(route('admission.store'), [], [
            'Accept' => 'application/json',
            'X-Requested-With' => 'XMLHttpRequest',
        ])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['student_name_bn', 'father_name_bn', 'student_photo']);
    }

    #[Test]
    public function store_saves_uploaded_photo(): void
    {
        Storage::fake('public');

        $this->post(route('admission.store'), [
            ...self::VALID_DATA,
            'student_photo' => UploadedFile::fake()->image('student.jpg', 200, 200),
        ]);

        $photo = Admission::first()->student_photo;

        $this->assertNotNull($photo);
        Storage::disk('public')->assertExists($photo);
    }

    #[Test]
    public function previous_class_details_are_optional(): void
    {
        Storage::fake('public');

        $data = self::VALID_DATA;
        unset($data['prev_school_name'], $data['prev_school_address'], $data['prev_marks']);

        $this->post(route('admission.store'), [
            ...$data,
            'student_photo' => UploadedFile::fake()->image('student.jpg', 200, 200),
        ])->assertRedirect(route('admission'));

        $this->assertDatabaseCount('admissions', 1);
    }

    #[Test]
    public function reference_signature_is_optional(): void
    {
        Storage::fake('public');

        $data = self::VALID_DATA;
        unset($data['reference_sign']);

        $this->post(route('admission.store'), [
            ...$data,
            'student_photo' => UploadedFile::fake()->image('student.jpg', 200, 200),
        ])->assertRedirect(route('admission'));

        $this->assertDatabaseCount('admissions', 1);
        $this->assertNull(Admission::first()->reference_sign);
    }

    #[Test]
    public function store_rejects_invalid_email(): void
    {
        $this->post(route('admission.store'), [
            ...self::VALID_DATA,
            'student_photo' => UploadedFile::fake()->image('student.jpg', 200, 200),
            'email' => 'not-an-email',
        ])->assertSessionHasErrors('email');

        $this->assertDatabaseCount('admissions', 0);
    }

    #[Test]
    public function store_rejects_invalid_photo_type(): void
    {
        Storage::fake('public');

        $this->post(route('admission.store'), [
            ...self::VALID_DATA,
            'student_photo' => UploadedFile::fake()->create('document.pdf', 100),
        ])->assertSessionHasErrors('student_photo');

        $this->assertDatabaseCount('admissions', 0);
    }
}
