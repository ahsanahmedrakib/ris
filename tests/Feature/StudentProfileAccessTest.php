<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\AcademicYear;
use App\Models\ClassRoom;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class StudentProfileAccessTest extends TestCase
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
            'role' => UserRole::Student->value,
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

    private function createUser(string $role, string $email): User
    {
        return User::create([
            'name' => ucfirst($role),
            'email' => $email,
            'password' => 'secret',
            'role' => $role,
            'is_active' => true,
        ]);
    }

    #[Test]
    public function guests_cannot_reach_the_student_profile(): void
    {
        $student = $this->createActiveStudent();

        $this->get(route('student.profile', $student))
            ->assertRedirect();
    }

    #[Test]
    public function guests_cannot_reach_the_id_card(): void
    {
        $student = $this->createActiveStudent();

        $this->get(route('student.id-card', $student))
            ->assertRedirect();
    }

    #[Test]
    public function guests_cannot_enumerate_student_profiles_by_incrementing_ids(): void
    {
        $student = $this->createActiveStudent();

        foreach (range($student->id, $student->id + 4) as $id) {
            $this->get("/student/{$id}/profile")->assertRedirect();
        }
    }

    #[Test]
    public function an_admin_sees_the_full_student_record(): void
    {
        $student = $this->createActiveStudent();
        $admin = $this->createUser(UserRole::Admin->value, 'admin@school.local');

        $this->actingAs($admin)
            ->get(route('student.profile', $student))
            ->assertOk()
            ->assertSee('আয়েশা সিদ্দিকা')
            ->assertSee('ADM-26-0101')
            ->assertSee('আব্দুল করিম')
            ->assertSee('01712345679')
            ->assertSee('O+')
            ->assertSee('গোপালগঞ্জ');
    }

    #[Test]
    public function a_teacher_sees_the_full_student_record(): void
    {
        $student = $this->createActiveStudent();
        $teacher = $this->createUser(UserRole::Teacher->value, 'teacher@school.local');

        $this->actingAs($teacher)
            ->get(route('student.profile', $student))
            ->assertOk()
            ->assertSee('আয়েশা সিদ্দিকা');
    }

    #[Test]
    public function a_parent_sees_their_own_child(): void
    {
        $student = $this->createActiveStudent();
        $parent = $this->createUser(UserRole::Parent->value, 'parent@school.local');
        $parent->parentStudents()->attach($student->id, ['relation' => 'father']);

        $this->actingAs($parent)
            ->get(route('student.profile', $student))
            ->assertOk()
            ->assertSee('আয়েশা সিদ্দিকা');
    }

    #[Test]
    public function a_parent_cannot_see_an_unrelated_student(): void
    {
        $student = $this->createActiveStudent();
        $parent = $this->createUser(UserRole::Parent->value, 'parent@school.local');

        $this->actingAs($parent)
            ->get(route('student.profile', $student))
            ->assertForbidden();
    }

    #[Test]
    public function a_parent_cannot_see_another_parents_child_id_card(): void
    {
        $student = $this->createActiveStudent();
        $parent = $this->createUser(UserRole::Parent->value, 'parent@school.local');

        $this->actingAs($parent)
            ->get(route('student.id-card', $student))
            ->assertForbidden();
    }

    #[Test]
    public function a_student_sees_their_own_profile(): void
    {
        $student = $this->createActiveStudent();

        $this->actingAs($student->user)
            ->get(route('student.profile', $student))
            ->assertOk()
            ->assertSee('আয়েশা সিদ্দিকা');
    }

    #[Test]
    public function a_student_cannot_see_a_classmates_profile(): void
    {
        $this->createActiveStudent();
        $other = $this->createUser(UserRole::Student->value, 'other@school.local');

        $this->actingAs($other)
            ->get(route('student.profile', Student::first()))
            ->assertForbidden();
    }

    #[Test]
    public function id_card_contains_a_qr_code_rendered_as_base64_data_uri(): void
    {
        $student = $this->createActiveStudent();
        $admin = $this->createUser(UserRole::Admin->value, 'admin@school.local');

        $this->actingAs($admin)
            ->get(route('student.id-card', $student))
            ->assertOk()
            ->assertSee('src="data:image/png;base64,', false)
            ->assertSee('আয়েশা সিদ্দিকা');
    }

    #[Test]
    public function id_card_uppercases_and_ignores_removed_fields(): void
    {
        $student = $this->createActiveStudent();
        $student->user->update(['name' => 'rakib hasan']);
        $admin = $this->createUser(UserRole::Admin->value, 'admin@school.local');

        $this->actingAs($admin)
            ->get(route('student.id-card', $student))
            ->assertOk()
            ->assertDontSee('ছাত্র পরিচয়পত্র')
            ->assertDontSee('পূর্ণ তথ্য')
            ->assertDontSee('পুরুষ')
            ->assertSee('RAKIB HASAN');
    }
}
