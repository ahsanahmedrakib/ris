<?php

namespace Tests\Feature;

use App\Models\AcademicYear;
use App\Models\ClassRoom;
use App\Models\ClassRoutine;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ClassRoutineAdminTest extends TestCase
{
    use RefreshDatabase;

    /** @return array{class: ClassRoom, subject: Subject, teacher: User} */
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

        $teacher = User::factory()->create(['role' => 'teacher', 'is_active' => true]);

        $subject = Subject::create([
            'name' => 'গণিত',
            'code' => 'MATH-101',
            'class_id' => $class->id,
            'teacher_id' => $teacher->id,
        ]);

        return ['class' => $class, 'subject' => $subject, 'teacher' => $teacher];
    }

    private function admin(): User
    {
        return User::factory()->create(['role' => 'admin', 'is_active' => true]);
    }

    /** @param array{class: ClassRoom, subject: Subject, teacher: User} $school */
    private function routinePayload(array $school, string $day = 'saturday'): array
    {
        return [
            'class_id' => $school['class']->id,
            'subject_id' => $school['subject']->id,
            'teacher_id' => $school['teacher']->id,
            'day_of_week' => $day,
            'start_time' => '08:00',
            'end_time' => '09:00',
            'room_no' => 'রুম-১০১',
        ];
    }

    #[Test]
    public function admin_class_routine_list_requires_authentication(): void
    {
        $this->get(route('admin.class-routines.index'))
            ->assertRedirect(route('login'));
    }

    #[Test]
    public function admin_can_create_class_routine_with_bangla_data(): void
    {
        $admin = $this->admin();
        $school = $this->school();

        $this->actingAs($admin)
            ->post(route('admin.class-routines.store'), $this->routinePayload($school))
            ->assertRedirect(route('admin.class-routines.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('class_routines', [
            'class_id' => $school['class']->id,
            'subject_id' => $school['subject']->id,
            'teacher_id' => $school['teacher']->id,
            'day_of_week' => 1,
            'start_time' => '08:00',
            'end_time' => '09:00',
            'room_no' => 'রুম-১০১',
        ]);
    }

    #[Test]
    public function admin_can_update_class_routine(): void
    {
        $admin = $this->admin();
        $school = $this->school();

        $routine = ClassRoutine::create($this->routinePayload($school));

        $this->actingAs($admin)
            ->put(route('admin.class-routines.update', $routine), [
                ...$this->routinePayload($school, 'thursday'),
                'start_time' => '10:00',
                'end_time' => '11:00',
            ])
            ->assertRedirect(route('admin.class-routines.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('class_routines', [
            'id' => $routine->id,
            'day_of_week' => 6,
            'start_time' => '10:00',
            'end_time' => '11:00',
        ]);
    }

    #[Test]
    public function admin_class_routine_page_has_day_dropdown_without_friday(): void
    {
        $admin = $this->admin();
        $school = $this->school();
        ClassRoutine::create($this->routinePayload($school, 'wednesday'));

        $this->actingAs($admin)
            ->get(route('admin.class-routines.index'))
            ->assertOk()
            ->assertSee('শনিবার')
            ->assertSee('রবিবার')
            ->assertSee('সোমবার')
            ->assertSee('মঙ্গলবার')
            ->assertSee('বুধবার')
            ->assertSee('বৃহস্পতিবার')
            ->assertDontSee('শুক্রবার')
            ->assertDontSee('value="friday"', false);
    }

    #[Test]
    public function admin_cannot_create_class_routine_on_friday(): void
    {
        $admin = $this->admin();
        $school = $this->school();

        $this->actingAs($admin)
            ->post(route('admin.class-routines.store'), $this->routinePayload($school, 'friday'))
            ->assertSessionHasErrors('day_of_week');

        $this->assertDatabaseCount('class_routines', 0);
    }

    #[Test]
    public function admin_can_view_class_routine_as_json(): void
    {
        $admin = $this->admin();
        $school = $this->school();
        $routine = ClassRoutine::create($this->routinePayload($school, 'sunday'));

        $this->actingAs($admin)
            ->get(route('admin.class-routines.show', $routine))
            ->assertOk()
            ->assertJson([
                'class_name' => '১ম',
                'subject_name' => 'গণিত',
                'day_label' => 'রবিবার',
                'room_no' => 'রুম-১০১',
            ]);
    }

    #[Test]
    public function public_class_routine_page_shows_weekly_grid_with_bangla_day(): void
    {
        $school = $this->school();
        ClassRoutine::create($this->routinePayload($school, 'monday'));

        $this->get(route('class-routine'))
            ->assertOk()
            ->assertSee('ক্লাশ রুটিন')
            ->assertSee('গণিত')
            ->assertSee('সোমবার')
            ->assertSee('রুম-১০১');
    }

    #[Test]
    public function public_class_routine_page_filters_by_class(): void
    {
        $school = $this->school();

        [$otherClass, $otherSubject] = $this->secondClass($school['class']);

        ClassRoutine::create($this->routinePayload($school, 'saturday'));

        ClassRoutine::create([
            'class_id' => $otherClass->id,
            'subject_id' => $otherSubject->id,
            'teacher_id' => $school['teacher']->id,
            'day_of_week' => 'sunday',
            'start_time' => '09:00',
            'end_time' => '10:00',
            'room_no' => 'রুম-২০২',
        ]);

        $this->get(route('class-routine', ['class_id' => $otherClass->id]))
            ->assertOk()
            ->assertSee('বিজ্ঞান')
            ->assertSee('রুম-২০২')
            ->assertDontSee('গণিত')
            ->assertDontSee('রুম-১০১');
    }

    #[Test]
    public function admin_can_filter_class_routines_by_class(): void
    {
        $admin = $this->admin();
        $school = $this->school();

        [$otherClass, $otherSubject] = $this->secondClass($school['class']);

        ClassRoutine::create($this->routinePayload($school, 'saturday'));

        ClassRoutine::create([
            'class_id' => $otherClass->id,
            'subject_id' => $otherSubject->id,
            'teacher_id' => $school['teacher']->id,
            'day_of_week' => 'sunday',
            'start_time' => '09:00',
            'end_time' => '10:00',
            'room_no' => 'রুম-২০২',
        ]);

        $this->actingAs($admin)
            ->get(route('admin.class-routines.index', ['class_id' => $otherClass->id]))
            ->assertOk()
            ->assertSee('রুম-২০২')
            ->assertDontSee('রুম-১০১');
    }

    #[Test]
    public function admin_can_download_class_routines_as_excel(): void
    {
        $admin = $this->admin();
        $school = $this->school();
        ClassRoutine::create($this->routinePayload($school, 'saturday'));

        $response = $this->actingAs($admin)->get(route('admin.class-routines.download'));

        $response->assertOk();
        $response->assertHeader('content-type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $this->assertStringContainsString('class_routines_', $response->headers->get('content-disposition'));
    }

    /**
     * @return array{0: ClassRoom, 1: Subject}
     */
    private function secondClass(ClassRoom $class): array
    {
        $otherClass = ClassRoom::create([
            'name' => '২য়',
            'section' => 'খ',
            'academic_year_id' => $class->academic_year_id,
        ]);

        $otherSubject = Subject::create([
            'name' => 'বিজ্ঞান',
            'code' => 'SCI-101',
            'class_id' => $otherClass->id,
            'teacher_id' => User::factory()->create(['role' => 'teacher', 'is_active' => true])->id,
        ]);

        return [$otherClass, $otherSubject];
    }
}
