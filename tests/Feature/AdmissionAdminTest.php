<?php

namespace Tests\Feature;

use App\Models\Admission;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class AdmissionAdminTest extends TestCase
{
    use RefreshDatabase;

    public const VALID_DATA = [
        'academic_year' => '২৬',
        'roll_no' => '12',
        'section' => 'ক',
        'batch' => 'প্রভাতী',
        'admission_date' => '2026-02-01',
        'form_collect_date' => '2026-01-20',
        'form_submit_date' => '2026-01-25',
        'class_level' => '১ম শ্রেণি',
        'student_name_bn' => 'মোঃ রাকিব হাসান',
        'student_name_en' => 'MD RAKIB HASAN',
        'dob' => '2018-05-12',
        'age' => '৮',
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
        'reference_sign' => 'স্বাক্ষর',
        'status' => 'pending',
    ];

    #[Test]
    public function admin_admission_list_requires_authentication(): void
    {
        $this->get(route('admin.admission.index'))
            ->assertRedirect(route('login'));
    }

    #[Test]
    public function admin_can_view_admission_list(): void
    {
        /** @var User $admin */
        $admin = User::factory()->create(['role' => 'admin', 'email' => 'admin@example.com']);

        Admission::factory()->create([
            'student_name_bn' => 'আয়েশা সিদ্দিকা',
            'class_level' => ['২য় শ্রেণি'],
        ]);

        $this->actingAs($admin)
            ->get(route('admin.admission.index'))
            ->assertOk()
            ->assertSee('আয়েশা সিদ্দিকা')
            ->assertSee('২য় শ্রেণি');
    }

    #[Test]
    public function admin_create_modal_defaults_academic_year_based_on_month(): void
    {
        /** @var User $admin */
        $admin = User::factory()->create(['role' => 'admin', 'email' => 'admin@example.com']);

        $nextYearTwoDigits = self::toBanglaDigits((string) ((now()->addYear()->year) % 100));
        $currentYearTwoDigits = self::toBanglaDigits((string) (now()->year % 100));

        foreach ([3, 12] as $month) {
            $this->travelTo(now()->setDate(now()->year, $month, 15));

            $this->actingAs($admin)
                ->get(route('admin.admission.index'))
                ->assertOk()
                ->assertSee("academic_year: '{$nextYearTwoDigits}'", false);
        }

        foreach ([1, 2] as $month) {
            $this->travelTo(now()->setDate(now()->year, $month, 15));

            $this->actingAs($admin)
                ->get(route('admin.admission.index'))
                ->assertOk()
                ->assertSee("academic_year: '{$currentYearTwoDigits}'", false);
        }
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
    public function admin_can_create_admission_with_generated_number(): void
    {
        /** @var User $admin */
        $admin = User::factory()->create(['role' => 'admin', 'email' => 'admin@example.com']);
        $year = now()->format('n') >= 3 ? now()->addYear()->format('y') : now()->format('y');

        $this->actingAs($admin)
            ->post(route('admin.admission.store'), [
                ...self::VALID_DATA,
                'student_photo' => UploadedFile::fake()->image('student.jpg', 200, 200),
            ])
            ->assertRedirectToRoute('admin.admission.index');

        $this->assertDatabaseHas('admissions', [
            'admission_no' => "{$year}-1001",
            'student_name_bn' => 'মোঃ রাকিব হাসান',
            'batch' => json_encode(['প্রভাতী']),
            'class_level' => json_encode(['১ম শ্রেণি']),
            'created_by' => $admin->id,
        ]);
    }

    #[Test]
    public function admin_update_generates_admission_number_when_class_assigned(): void
    {
        /** @var User $admin */
        $admin = User::factory()->create(['role' => 'admin', 'email' => 'admin@example.com']);
        $year = now()->format('n') >= 3 ? now()->addYear()->format('y') : now()->format('y');

        $admission = Admission::factory()->create(['admission_no' => null]);

        $this->actingAs($admin)
            ->put(route('admin.admission.update', $admission), [
                ...self::VALID_DATA,
                'student_photo' => UploadedFile::fake()->image('student.jpg', 200, 200),
            ])
            ->assertRedirectToRoute('admin.admission.index');

        $this->assertDatabaseHas('admissions', [
            'id' => $admission->id,
            'admission_no' => "{$year}-1001",
            'class_level' => json_encode(['১ম শ্রেণি']),
        ]);
    }

    #[Test]
    public function admin_can_view_admission_json(): void
    {
        /** @var User $admin */
        $admin = User::factory()->create(['role' => 'admin', 'email' => 'admin@example.com']);

        $admission = Admission::factory()->create([
            'admission_no' => 'ADM-26-0099',
            'student_name_bn' => 'আয়েশা সিদ্দিকা',
        ]);

        $this->actingAs($admin)
            ->get(route('admin.admission.show', $admission))
            ->assertOk()
            ->assertJson([
                'id' => $admission->id,
                'admission_no' => 'ADM-26-0099',
                'student_name_bn' => 'আয়েশা সিদ্দিকা',
                'status_label' => 'পেন্ডিং',
            ]);
    }

    #[Test]
    public function admin_can_fetch_admission_for_editing(): void
    {
        /** @var User $admin */
        $admin = User::factory()->create(['role' => 'admin', 'email' => 'admin@example.com']);

        $admission = Admission::factory()->create([
            'batch' => ['দিবা'],
            'class_level' => ['কেজি'],
        ]);

        $this->actingAs($admin)
            ->get(route('admin.admission.edit', $admission))
            ->assertOk()
            ->assertJson([
                'id' => $admission->id,
                'batch' => 'দিবা',
                'class_level' => 'কেজি',
            ]);
    }

    #[Test]
    public function admin_can_update_admission(): void
    {
        /** @var User $admin */
        $admin = User::factory()->create(['role' => 'admin', 'email' => 'admin@example.com']);

        $admission = Admission::factory()->create(['student_name_bn' => 'পুরনো নাম']);

        $this->actingAs($admin)
            ->put(route('admin.admission.update', $admission), [
                ...self::VALID_DATA,
                'student_photo' => UploadedFile::fake()->image('student.jpg', 200, 200),
                'student_name_bn' => 'নতুন নাম',
                'batch' => 'দিবা',
            ])
            ->assertRedirectToRoute('admin.admission.index');

        $this->assertDatabaseHas('admissions', [
            'id' => $admission->id,
            'student_name_bn' => 'নতুন নাম',
            'batch' => json_encode(['দিবা']),
            'admission_no' => $admission->admission_no,
        ]);
    }

    #[Test]
    public function admin_can_update_status(): void
    {
        /** @var User $admin */
        $admin = User::factory()->create(['role' => 'admin', 'email' => 'admin@example.com']);

        $admission = Admission::factory()->create(['status' => 'pending']);

        $this->actingAs($admin)
            ->patch(route('admin.admission.status', $admission), ['status' => 'approved'])
            ->assertRedirect();

        $this->assertDatabaseHas('admissions', [
            'id' => $admission->id,
            'status' => 'approved',
        ]);
    }

    #[Test]
    public function admin_can_print_admission_pdf(): void
    {
        /** @var User $admin */
        $admin = User::factory()->create(['role' => 'admin', 'email' => 'admin@example.com']);

        $admission = Admission::factory()->create([
            'admission_no' => 'ADM-26-0042',
            'student_name_bn' => 'আয়েশা সিদ্দিকা',
        ]);

        $this->actingAs($admin)
            ->get(route('admin.admission.pdf', $admission))
            ->assertOk()
            ->assertSee('ADM-26-0042')
            ->assertSee('আয়েশা সিদ্দিকা')
            ->assertSee('ভর্তি আবেদন ফরম');
    }

    #[Test]
    public function admin_can_download_admissions_csv(): void
    {
        /** @var User $admin */
        $admin = User::factory()->create(['role' => 'admin', 'email' => 'admin@example.com']);

        foreach (['ADM-26-0101', 'ADM-26-0102', 'ADM-26-0103'] as $no) {
            Admission::factory()->create(['admission_no' => $no]);
        }

        $this->actingAs($admin)
            ->get(route('admin.admission.download'))
            ->assertOk()
            ->assertHeaderContains('Content-Type', 'text/csv')
            ->assertHeaderContains('Content-Disposition', 'attachment');
    }

    #[Test]
    public function admin_can_delete_admission(): void
    {
        /** @var User $admin */
        $admin = User::factory()->create(['role' => 'admin', 'email' => 'admin@example.com']);

        $admission = Admission::factory()->create(['admission_no' => 'ADM-26-0077']);

        $this->actingAs($admin)
            ->delete(route('admin.admission.destroy', $admission))
            ->assertRedirectToRoute('admin.admission.index');

        $this->assertDatabaseMissing('admissions', [
            'id' => $admission->id,
        ]);
    }
}
