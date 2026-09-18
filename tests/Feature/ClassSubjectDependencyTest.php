<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\AcademicYear;
use App\Models\ClassRoom;
use App\Models\Exam;
use App\Models\ExamResult;
use App\Models\FeeStructure;
use App\Models\Student;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ClassSubjectDependencyTest extends TestCase
{
    use RefreshDatabase;

    private const EXAM_TYPE = 'final';

    private function admin(): User
    {
        return User::factory()->create(['role' => 'admin']);
    }

    /**
     * @return array{year: AcademicYear, class: ClassRoom, teacher: User}
     */
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

        $teacher = User::factory()->create(['role' => 'teacher']);

        return ['year' => $year, 'class' => $class, 'teacher' => $teacher];
    }

    private function subject(array $school): Subject
    {
        return Subject::create([
            'name' => 'গণিত',
            'code' => 'MATH-101',
            'class_id' => $school['class']->id,
            'teacher_id' => $school['teacher']->id,
        ]);
    }

    private function exam(array $school, Subject $subject, bool $withStudent = false): array
    {
        $exam = Exam::create([
            'name' => 'বার্ষিক পরীক্ষা',
            'type' => self::EXAM_TYPE,
            'class_id' => $school['class']->id,
            'academic_year_id' => $school['year']->id,
            'start_date' => '2026-11-01',
            'end_date' => '2026-11-15',
            'total_marks' => 100,
            'passing_marks' => 33,
        ]);

        $student = null;

        if ($withStudent) {
            $user = User::factory()->create(['role' => UserRole::Student->value]);
            $student = Student::create([
                'user_id' => $user->id,
                'admission_no' => '2026-1001',
                'class_id' => $school['class']->id,
                'section' => 'ক',
                'roll_no' => 1,
                'date_of_birth' => '2018-01-10',
                'gender' => 'male',
                'address' => 'ঢাকা',
                'guardian_name' => 'পিতা',
                'guardian_phone' => '01700000000',
            ]);
        }

        return ['exam' => $exam, 'student' => $student];
    }

    #[Test]
    public function class_with_subjects_cannot_be_deleted(): void
    {
        $admin = $this->admin();
        $school = $this->school();
        $this->subject($school);

        $this->actingAs($admin)
            ->delete(route('admin.classes.destroy', $school['class']->id))
            ->assertRedirect()
            ->assertSessionHas('error');

        $this->assertNotSoftDeleted('classes', ['id' => $school['class']->id]);
    }

    #[Test]
    public function class_with_exam_cannot_be_deleted(): void
    {
        $admin = $this->admin();
        $school = $this->school();
        $this->exam($school, $this->subject($school));

        $this->actingAs($admin)
            ->delete(route('admin.classes.destroy', $school['class']->id))
            ->assertRedirect()
            ->assertSessionHas('error');

        $this->assertNotSoftDeleted('classes', ['id' => $school['class']->id]);
    }

    #[Test]
    public function class_can_be_deleted_when_no_dependencies_exist(): void
    {
        $admin = $this->admin();
        $school = $this->school();

        $this->actingAs($admin)
            ->delete(route('admin.classes.destroy', $school['class']->id))
            ->assertRedirect()
            ->assertSessionHas('success');

        $this->assertSoftDeleted('classes', ['id' => $school['class']->id]);
    }

    #[Test]
    public function subject_with_results_cannot_be_deleted(): void
    {
        $admin = $this->admin();
        $school = $this->school();
        $subject = $this->subject($school);
        $data = $this->exam($school, $subject, withStudent: true);

        ExamResult::create([
            'exam_id' => $data['exam']->id,
            'student_id' => $data['student']->id,
            'subject_id' => $subject->id,
            'marks_obtained' => 80,
            'grade' => 'A',
            'entered_by' => $admin->id,
        ]);

        $this->actingAs($admin)
            ->delete(route('admin.subjects.destroy', $subject->id))
            ->assertRedirect()
            ->assertSessionHas('error');

        $this->assertNotSoftDeleted('subjects', ['id' => $subject->id]);
    }

    #[Test]
    public function subject_can_be_deleted_when_no_dependencies_exist(): void
    {
        $admin = $this->admin();
        $school = $this->school();
        $subject = $this->subject($school);

        $this->actingAs($admin)
            ->delete(route('admin.subjects.destroy', $subject->id))
            ->assertRedirect()
            ->assertSessionHas('success');

        $this->assertSoftDeleted('subjects', ['id' => $subject->id]);
    }

    #[Test]
    public function exam_results_can_be_stored_and_updated_with_auto_grade(): void
    {
        $admin = $this->admin();
        $school = $this->school();
        $subject = $this->subject($school);
        $data = $this->exam($school, $subject, withStudent: true);

        $postUrl = route('admin.exams.results.store', $data['exam']->id);

        $this->actingAs($admin)
            ->post($postUrl, [
                'result' => [$data['student']->id => [$subject->id => 85]],
            ])
            ->assertRedirect(route('admin.exams.results', $data['exam']))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('exam_results', [
            'exam_id' => $data['exam']->id,
            'student_id' => $data['student']->id,
            'subject_id' => $subject->id,
            'marks_obtained' => 85,
            'grade' => 'A+',
        ]);

        $this->actingAs($admin)
            ->post($postUrl, [
                'result' => [$data['student']->id => [$subject->id => 70]],
            ])
            ->assertRedirect(route('admin.exams.results', $data['exam']))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('exam_results', [
            'exam_id' => $data['exam']->id,
            'student_id' => $data['student']->id,
            'subject_id' => $subject->id,
            'marks_obtained' => 70,
            'grade' => 'A',
        ]);
    }

    #[Test]
    public function result_below_passing_marks_gets_f_grade(): void
    {
        $admin = $this->admin();
        $school = $this->school();
        $subject = $this->subject($school);
        $data = $this->exam($school, $subject, withStudent: true);

        $this->actingAs($admin)
            ->post(route('admin.exams.results.store', $data['exam']->id), [
                'result' => [$data['student']->id => [$subject->id => 25]],
            ]);

        $this->assertDatabaseHas('exam_results', [
            'exam_id' => $data['exam']->id,
            'student_id' => $data['student']->id,
            'subject_id' => $subject->id,
            'grade' => 'F',
        ]);
    }

    #[Test]
    public function exam_results_page_renders_with_students_subjects_and_prefill(): void
    {
        $admin = $this->admin();
        $school = $this->school();
        $subject = $this->subject($school);
        $data = $this->exam($school, $subject, withStudent: true);

        $this->actingAs($admin)
            ->post(route('admin.exams.results.store', $data['exam']->id), [
                'result' => [$data['student']->id => [$subject->id => 85]],
            ]);

        $this->actingAs($admin)
            ->get(route('admin.exams.results', $data['exam']))
            ->assertOk()
            ->assertSee($data['student']->user->name)
            ->assertSee($subject->name);
    }

    #[Test]
    public function exam_index_page_shows_result_entry_action(): void
    {
        $admin = $this->admin();
        $school = $this->school();
        $this->exam($school, $this->subject($school));

        $this->actingAs($admin)
            ->get(route('admin.exams.index'))
            ->assertOk()
            ->assertSee('ফলাফল');
    }

    #[Test]
    public function class_list_is_ordered_serially(): void
    {
        $year = AcademicYear::create([
            'name' => '2025',
            'start_date' => '2025-01-01',
            'end_date' => '2025-12-31',
            'is_current' => true,
        ]);

        foreach (['৫ম', 'প্লে', 'কেজি', '১ম', 'নার্সারি', '২য়', '৩য়', '৪র্থ'] as $name) {
            ClassRoom::create(['name' => $name, 'section' => 'ক', 'academic_year_id' => $year->id]);
        }

        $this->assertSame(
            ['প্লে', 'নার্সারি', 'কেজি', '১ম', '২য়', '৩য়', '৪র্থ', '৫ম'],
            ClassRoom::pluck('name')->all()
        );

        $this->actingAs($this->admin())
            ->get(route('admin.classes.index'))
            ->assertOk()
            ->assertSeeInOrder(['প্লে', 'নার্সারি', 'কেজি', '১ম', '২য়', '৩য়', '৪র্থ', '৫ম']);
    }

    #[Test]
    public function exam_marks_accept_bangla_digits(): void
    {
        $admin = $this->admin();
        $school = $this->school();

        $this->actingAs($admin)
            ->from(route('admin.exams.index'))
            ->post(route('admin.exams.store'), [
                'name' => 'বার্ষিক পরীক্ষা',
                'type' => self::EXAM_TYPE,
                'class_id' => $school['class']->id,
                'academic_year_id' => $school['year']->id,
                'start_date' => '2026-11-01',
                'end_date' => '2026-11-15',
                'total_marks' => '১০০',
                'passing_marks' => '৩৩',
            ])
            ->assertRedirect(route('admin.exams.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('exams', [
            'name' => 'বার্ষিক পরীক্ষা',
            'total_marks' => 100,
            'passing_marks' => 33,
        ]);
    }

    #[Test]
    public function public_results_page_shows_class_marks_table(): void
    {
        $school = $this->school();
        $subject = $this->subject($school);
        $exam = $this->exam($school, $subject, withStudent: true);

        ExamResult::create([
            'exam_id' => $exam['exam']->id,
            'student_id' => $exam['student']->id,
            'subject_id' => $subject->id,
            'marks_obtained' => 85,
            'grade' => ExamResult::calculateGrade(85, 100, 33),
            'entered_by' => $this->admin()->id,
        ]);

        $this->get(route('academic.results', [
            'class_id' => $school['class']->id,
            'exam_id' => $exam['exam']->id,
        ]))
            ->assertOk()
            ->assertSee('বার্ষিক পরীক্ষা')
            ->assertSee('85.0')
            ->assertSee($exam['student']->user->name);
    }

    #[Test]
    public function admin_results_page_lists_all_student_marks(): void
    {
        $admin = $this->admin();
        $school = $this->school();
        $subject = $this->subject($school);
        $exam = $this->exam($school, $subject, withStudent: true);

        ExamResult::create([
            'exam_id' => $exam['exam']->id,
            'student_id' => $exam['student']->id,
            'subject_id' => $subject->id,
            'marks_obtained' => 85,
            'grade' => ExamResult::calculateGrade(85, 100, 33),
            'entered_by' => $admin->id,
        ]);

        $this->actingAs($admin)
            ->get(route('admin.results.index'))
            ->assertOk()
            ->assertSee('বার্ষিক পরীক্ষা')
            ->assertSee($exam['student']->user->name)
            ->assertSee('এক্সেল ডাউনলোড');
    }

    #[Test]
    public function admin_results_export_returns_xlsx(): void
    {
        $admin = $this->admin();
        $school = $this->school();
        $subject = $this->subject($school);
        $exam = $this->exam($school, $subject, withStudent: true);

        ExamResult::create([
            'exam_id' => $exam['exam']->id,
            'student_id' => $exam['student']->id,
            'subject_id' => $subject->id,
            'marks_obtained' => 85,
            'grade' => ExamResult::calculateGrade(85, 100, 33),
            'entered_by' => $admin->id,
        ]);

        $this->actingAs($admin)
            ->get(route('admin.results.export'))
            ->assertOk()
            ->assertHeader('content-type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet')
            ->assertHeader('content-disposition', 'attachment; filename="all_results_'.now('Asia/Dhaka')->format('Y-m-d_H-i').'.xlsx"');
    }

    #[Test]
    public function empty_result_payload_is_rejected(): void
    {
        $admin = $this->admin();
        $school = $this->school();
        $subject = $this->subject($school);
        $data = $this->exam($school, $subject, withStudent: true);

        $this->actingAs($admin)
            ->from(route('admin.exams.results', $data['exam']))
            ->post(route('admin.exams.results.store', $data['exam']->id), [
                'result' => [],
            ])
            ->assertRedirect(route('admin.exams.results', $data['exam']))
            ->assertSessionHas('error');

        $this->assertDatabaseCount('exam_results', 0);
    }

    #[Test]
    public function website_fees_page_shows_dynamic_fee_table(): void
    {
        $school = $this->school();

        FeeStructure::create([
            'class_id' => $school['class']->id,
            'academic_year_id' => $school['year']->id,
            'fee_type' => 'tuition',
            'amount' => 1500,
            'due_date' => '2026-12-31',
        ]);

        $this->get(route('academic.fees'))
            ->assertOk()
            ->assertSee($school['class']->name)
            ->assertSee('বেতন')
            ->assertSee('৳1,500');
    }
}
