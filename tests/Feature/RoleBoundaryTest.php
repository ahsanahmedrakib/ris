<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\AcademicYear;
use App\Models\ClassRoom;
use App\Models\FeeStructure;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class RoleBoundaryTest extends TestCase
{
    use RefreshDatabase;

    private function teacher(): User
    {
        return User::factory()->create(['role' => UserRole::Teacher->value]);
    }

    private function admin(): User
    {
        return User::factory()->create(['role' => UserRole::Admin->value]);
    }

    private function parent(): User
    {
        return User::factory()->create(['role' => UserRole::Parent->value]);
    }

    /**
     * A teacher must not reach anything touching money, staff accounts, applicant
     * PII, the audit trail or irreversible deletes.
     *
     * @return array<string, array{string, string}>
     */
    public static function adminOnlyRoutes(): array
    {
        return [
            'user management' => ['admin.users.index', 'get'],
            'teacher management' => ['admin.teachers.index', 'get'],
            'fee structures' => ['admin.fees.structures', 'get'],
            'fee payments' => ['admin.fees.payments', 'get'],
            'payroll' => ['admin.payroll.index', 'get'],
            'transport' => ['admin.transport.index', 'get'],
            'staff records' => ['admin.staff.index', 'get'],
            'activity log' => ['admin.activity-logs.index', 'get'],
            'admissions' => ['admin.admission.index', 'get'],
            'scholarships' => ['admin.scholarship.index', 'get'],
            'contact messages' => ['admin.contact-messages.index', 'get'],
            'trash' => ['admin.trash.index', 'get'],
            'site content' => ['admin.about.index', 'get'],
            'fee report' => ['admin.reports.fees', 'get'],
            'staff report' => ['admin.reports.staff', 'get'],
        ];
    }

    #[Test]
    #[DataProvider('adminOnlyRoutes')]
    public function a_teacher_is_forbidden_from_admin_only_pages(string $routeName, string $method): void
    {
        $this->actingAs($this->teacher())
            ->call($method, route($routeName))
            ->assertForbidden();
    }

    /**
     * @return array<string, array{string, string}>
     */
    public static function sharedStaffRoutes(): array
    {
        return [
            'dashboard' => ['admin.dashboard', 'get'],
            'students' => ['admin.students.index', 'get'],
            'classes' => ['admin.classes.index', 'get'],
            'subjects' => ['admin.subjects.index', 'get'],
            'attendance' => ['admin.attendance.index', 'get'],
            'exams' => ['admin.exams.index', 'get'],
            'results' => ['admin.results.index', 'get'],
            'notices' => ['admin.notices.index', 'get'],
            'class routines' => ['admin.class-routines.index', 'get'],
            'library' => ['admin.library.index', 'get'],
            'academic report' => ['admin.reports.students', 'get'],
        ];
    }

    #[Test]
    #[DataProvider('sharedStaffRoutes')]
    public function a_teacher_may_reach_shared_teaching_pages(string $routeName, string $method): void
    {
        $this->actingAs($this->teacher())
            ->call($method, route($routeName))
            ->assertSuccessful();
    }

    #[Test]
    public function an_admin_keeps_access_to_every_admin_page(): void
    {
        foreach (self::adminOnlyRoutes() as [$routeName, $method]) {
            $this->actingAs($this->admin())
                ->call($method, route($routeName))
                ->assertSuccessful();
        }
    }

    #[Test]
    public function a_parent_cannot_reach_any_admin_page(): void
    {
        foreach (self::adminOnlyRoutes() as [$routeName, $method]) {
            $this->actingAs($this->parent())
                ->call($method, route($routeName))
                ->assertForbidden();
        }
    }

    #[Test]
    public function api_students_allow_teachers_but_api_fees_do_not(): void
    {
        $year = AcademicYear::create([
            'name' => '2026',
            'start_date' => '2026-01-01',
            'end_date' => '2026-12-31',
            'is_current' => true,
        ]);

        $class = ClassRoom::create(['name' => 'Class 1', 'section' => 'A', 'academic_year_id' => $year->id]);

        Subject::create(['name' => 'Bangla', 'code' => 'BNG', 'class_id' => $class->id]);

        FeeStructure::create([
            'class_id' => $class->id,
            'academic_year_id' => $year->id,
            'name' => 'Tuition',
            'fee_type' => 'tuition',
            'amount' => 1000,
        ]);

        $teacher = $this->teacher();

        $this->actingAs($teacher, 'api')
            ->getJson(route('api.students.index'))
            ->assertSuccessful();

        $this->actingAs($teacher, 'api')
            ->getJson(route('api.fees.index'))
            ->assertForbidden();
    }

    #[Test]
    public function a_parent_may_not_reach_the_api_at_all(): void
    {
        $this->actingAs($this->parent(), 'api')
            ->getJson(route('api.students.index'))
            ->assertForbidden();
    }
}
