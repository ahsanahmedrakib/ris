<?php

namespace Tests\Feature;

use App\Models\AcademicYear;
use App\Models\ClassRoom;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class StudentPublicProfileTest extends TestCase
{
    use RefreshDatabase;

    private function createActiveStudent(): Student
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

        $user = User::create([
            'name' => 'আয়েশা সিদ্দিকা',
            'email' => 'ayesha@school.local',
            'phone' => '01712345678',
            'password' => 'secret',
            'role' => 'student',
            'is_active' => true,
        ]);

        return Student::create([
            'user_id' => $user->id,
            'admission_no' => 'ADM-26-0101',
            'class_id' => $class->id,
            'section' => 'ক',
            'roll_no' => 1,
            'date_of_birth' => '2018-05-12',
            'gender' => 'male',
            'blood_group' => 'O+',
            'address' => 'গোপালগঞ্জ',
            'guardian_name' => 'আব্দুল করিম',
            'guardian_phone' => '01712345679',
            'guardian_email' => 'guardian@school.local',
            'is_active' => true,
        ]);
    }

    #[Test]
    public function id_card_contains_a_qr_code_rendered_as_base64_data_uri(): void
    {
        $student = $this->createActiveStudent();

        $this->get(route('student.id-card', $student))
            ->assertOk()
            ->assertSee('src="data:image/png;base64,', false)
            ->assertSee('আয়েশা সিদ্দিকা');
    }

    #[Test]
    public function id_card_uppercases_and_ignores_removed_fields(): void
    {
        $student = $this->createActiveStudent();
        $student->user->update(['name' => 'rakib hasan']);

        $this->get(route('student.id-card', $student))
            ->assertOk()
            ->assertDontSee('ছাত্র পরিচয়পত্র')
            ->assertDontSee('পূর্ণ তথ্য')
            ->assertDontSee('পুরুষ')
            ->assertSee('RAKIB HASAN');
    }

    #[Test]
    public function public_profile_requires_no_authentication_and_shows_full_information(): void
    {
        $student = $this->createActiveStudent();

        $this->get(route('student.profile', $student))
            ->assertOk()
            ->assertSee('আয়েশা সিদ্দিকা')
            ->assertSee('ADM-26-0101')
            ->assertSee('আব্দুল করিম')
            ->assertSee('01712345679')
            ->assertSee('O+')
            ->assertSee('গোপালগঞ্জ');
    }
}
