<?php

namespace Tests\Feature;

use App\Models\AcademicYear;
use App\Models\ClassRoom;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class StudentAdminTest extends TestCase
{
    use RefreshDatabase;

    private function school(): array
    {
        $year = AcademicYear::create([
            'name' => '2026',
            'start_date' => '2026-01-01',
            'end_date' => '2026-12-31',
            'is_current' => true,
        ]);

        $class = ClassRoom::create([
            'name' => '১ম',
            'section' => 'ক',
            'academic_year_id' => $year->id,
        ]);

        return ['year' => $year, 'class' => $class];
    }

    private function createStudent(string $name, string $admissionNo): Student
    {
        $school = $this->school();

        $user = User::create([
            'name' => $name,
            'email' => strtolower(str_replace(' ', '', $name)).'@school.local',
            'phone' => '01712345678',
            'password' => 'secret',
            'role' => 'student',
            'is_active' => true,
        ]);

        return Student::create([
            'user_id' => $user->id,
            'admission_no' => $admissionNo,
            'class_id' => $school['class']->id,
            'section' => 'ক',
            'roll_no' => 1,
            'date_of_birth' => '2018-05-12',
            'gender' => 'male',
            'blood_group' => 'O+',
            'address' => 'গোপালগঞ্জ',
            'guardian_name' => 'আব্দুল করিম',
            'guardian_phone' => '01712345679',
            'is_active' => true,
        ]);
    }

    #[Test]
    public function admin_students_download_requires_authentication(): void
    {
        $this->get(route('admin.students.download'))
            ->assertRedirect(route('login'));
    }

    #[Test]
    public function admin_can_download_students_as_xlsx(): void
    {
        /** @var User $admin */
        $admin = User::factory()->create(['role' => 'admin', 'email' => 'admin@example.com']);

        $this->createStudent('আয়েশা সিদ্দিকা', 'ADM-26-0101');
        $this->createStudent('মোঃ রাকিব হাসান', 'ADM-26-0102');

        $response = $this->actingAs($admin)
            ->get(route('admin.students.download'))
            ->assertOk()
            ->assertHeader('content-type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet')
            ->assertHeaderContains('content-disposition', 'attachment');

        $this->assertXlsxContainsText($response->getContent(), 'ADM-26-0101');
        $this->assertXlsxContainsText($response->getContent(), 'মোঃ রাকিব হাসান');
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
