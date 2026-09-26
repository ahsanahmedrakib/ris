<?php

namespace Tests\Feature;

use App\Livewire\Scholarship\RegistrationForm as ScholarshipRegistrationForm;
use App\Models\Admission;
use App\Support\UniqueConstraintViolation;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use PDOException;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ConcurrentSubmissionSafetyTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @var array<string, mixed>
     */
    private const ADMISSION_DATA = [
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
        'prev_roll_no' => '৩',
        'prev_marks' => '৮০',
        'reference' => 'মোঃ আহমেদ',
        'reference_phone' => '01712345681',
        'class_level' => ['নার্সারি'],
        'batch' => ['প্রভাতী'],
    ];

    /**
     * @var array<string, mixed>
     */
    private const SCHOLARSHIP_DATA = [
        'studentName' => 'মোঃ রাকিব হাসান',
        'fatherName' => 'আব্দুল করিম',
        'motherName' => 'ফাতেমা বেগম',
        'schoolName' => 'কাটাখালি',
        'classNo' => 1,
        'rollNo' => '12',
        'mobileNo' => '01712345678',
        'bkashNo' => '01912345678',
    ];

    private static function admissionNumberPrefix(): string
    {
        return (now()->format('n') >= 3 ? now()->addYear()->format('y') : now()->format('y')).'-N';
    }

    private static function queryException(int $code, int $driverCode, string $message): QueryException
    {
        $previous = new PDOException($message, $code);
        $previous->errorInfo = ['23000', $driverCode, $message];

        return new QueryException(
            'mysql',
            'insert into `admissions` (`admission_no`) values (?)',
            ['2601-N005'],
            $previous
        );
    }

    #[Test]
    public function unique_violation_helper_matches_sqlstate_and_driver_code(): void
    {
        $this->assertTrue(UniqueConstraintViolation::matches(
            self::queryException(23000, 1062, "SQLSTATE[23000]: Integrity constraint violation: 1062 Duplicate entry '2601-N005' for key 'admissions.admission_no_unique'")
        ));

        $this->assertTrue(UniqueConstraintViolation::matches(
            self::queryException(23000, 0, 'Integrity constraint violation, no driver code available')
        ));

        $this->assertFalse(UniqueConstraintViolation::matches(
            self::queryException(0, 1045, 'SQLSTATE[HY000]: General error: 1045 Access denied for user')
        ));

        $this->assertFalse(UniqueConstraintViolation::matches(
            self::queryException(0, 1146, "SQLSTATE[42S02]: Base table or view not found: 1146 Table 'x' doesn't exist")
        ));
    }

    #[Test]
    public function unique_violation_helper_does_not_depend_on_english_error_text(): void
    {
        // A localized or MariaDB flavoured host reports the same failure without
        // the word "unique" in the message, which silently disabled the retry.
        $localized = self::queryException(0, 1062, "ডুপ্লিকেট এন্ট্রি '2601-N005'");

        $this->assertStringNotContainsString('unique', strtolower($localized->getMessage()));
        $this->assertTrue(UniqueConstraintViolation::matches($localized));
    }

    #[Test]
    public function next_admission_number_follows_highest_serial_not_newest_row(): void
    {
        $prefix = self::admissionNumberPrefix();

        // Row order and serial order deliberately disagree: the row with the
        // highest id carries serial 001, so ordering by id would hand back an
        // already used number.
        Admission::factory()->create(['admission_no' => $prefix.'003']);
        Admission::factory()->create(['admission_no' => $prefix.'002']);
        Admission::factory()->create(['admission_no' => $prefix.'001']);

        $this->assertSame($prefix.'004', Admission::nextAdmissionNo('নার্সারি'));

        $newest = Admission::orderByDesc('id')->first();
        $this->assertSame($prefix.'001', $newest->admission_no, 'Precondition: newest row is not the highest serial.');
    }

    #[Test]
    public function next_admission_number_ignores_numbers_from_other_classes_and_years(): void
    {
        $prefix = self::admissionNumberPrefix();

        Admission::factory()->create(['admission_no' => $prefix.'007']);
        Admission::factory()->create(['admission_no' => '1999-A042']);
        Admission::factory()->create(['admission_no' => 'malformed-number']);

        $this->assertSame($prefix.'008', Admission::nextAdmissionNo('নার্সারি'));
    }

    #[Test]
    public function scholarship_registration_is_saved_when_the_admin_notification_fails(): void
    {
        Notification::shouldReceive('send')->andThrow(new \RuntimeException('notification table unavailable'));

        $year = now()->format('y');

        Livewire::test(ScholarshipRegistrationForm::class)
            ->set(self::SCHOLARSHIP_DATA)
            ->call('submit')
            ->call('confirm')
            ->assertSet('saved', true)
            ->assertSet('confirming', false)
            ->assertSet('registrationNo', "{$year}-1001")
            ->assertHasNoErrors();

        $this->assertDatabaseCount('scholarship_registrations', 1);
    }

    #[Test]
    public function confirming_a_saved_registration_twice_does_not_create_a_duplicate(): void
    {
        Notification::fake();

        $component = Livewire::test(ScholarshipRegistrationForm::class)
            ->set(self::SCHOLARSHIP_DATA)
            ->call('submit')
            ->call('confirm')
            ->assertSet('saved', true);

        $component->call('confirm')->assertSet('saved', true);

        $this->assertDatabaseCount('scholarship_registrations', 1);
        $this->assertDatabaseHas('scholarship_registrations', ['registration_no' => now()->format('y').'-1001']);
    }

    #[Test]
    public function public_admission_is_saved_with_its_photo_when_the_admin_notification_fails(): void
    {
        Storage::fake('public');
        Notification::shouldReceive('send')->andThrow(new \RuntimeException('notification table unavailable'));

        $response = $this->post(route('admission.store'), [
            ...self::ADMISSION_DATA,
            'student_photo' => UploadedFile::fake()->image('student.jpg', 200, 200),
        ]);

        // The row is committed, so the applicant must be told it worked even
        // though the admin notification could not be delivered.
        $response->assertRedirect(route('admission'))
            ->assertSessionHas('success');

        $admission = Admission::firstOrFail();

        $this->assertSame('MD. RAKIB HASAN', $admission->student_name_en);
        $this->assertNotNull($admission->student_photo);
        Storage::disk('public')->assertExists($admission->student_photo);
    }

    #[Test]
    public function repeated_public_admission_submission_is_rejected_instead_of_duplicated(): void
    {
        Storage::fake('public');

        $payload = [
            ...self::ADMISSION_DATA,
            'student_photo' => UploadedFile::fake()->image('student.jpg', 200, 200),
        ];

        $this->post(route('admission.store'), $payload)
            ->assertRedirect(route('admission'))
            ->assertSessionHas('success');

        $this->post(route('admission.store'), $payload)
            ->assertSessionHas('error');

        $this->assertDatabaseCount('admissions', 1);
    }

    #[Test]
    public function a_different_child_in_the_same_family_can_still_apply(): void
    {
        Storage::fake('public');

        $this->post(route('admission.store'), [
            ...self::ADMISSION_DATA,
            'student_photo' => UploadedFile::fake()->image('student.jpg', 200, 200),
        ])->assertSessionHas('success');

        $this->post(route('admission.store'), [
            ...self::ADMISSION_DATA,
            'student_name_en' => 'MD. RAKIB HASAN JR',
            'student_name_bn' => 'মোঃ রাকিব হাসান জুনিয়র',
            'student_photo' => UploadedFile::fake()->image('student.jpg', 200, 200),
        ])->assertSessionHas('success');

        $this->assertDatabaseCount('admissions', 2);
    }
}
