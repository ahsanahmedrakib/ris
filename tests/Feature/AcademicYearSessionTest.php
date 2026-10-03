<?php

namespace Tests\Feature;

use App\Models\AcademicYear;
use App\Models\User;
use App\Support\NumberConverter;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class AcademicYearSessionTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function session_year_uses_current_year_in_january_and_february(): void
    {
        $this->assertSame(2026, AcademicYear::sessionYear(Carbon::createFromDate(2026, 1, 15)));
        $this->assertSame(2026, AcademicYear::sessionYear(Carbon::createFromDate(2026, 2, 28)));
    }

    #[Test]
    public function session_year_uses_next_year_from_march_through_december(): void
    {
        $this->assertSame(2027, AcademicYear::sessionYear(Carbon::createFromDate(2026, 3, 1)));
        $this->assertSame(2027, AcademicYear::sessionYear(Carbon::createFromDate(2026, 9, 18)));
        $this->assertSame(2027, AcademicYear::sessionYear(Carbon::createFromDate(2026, 11, 30)));
        $this->assertSame(2027, AcademicYear::sessionYear(Carbon::createFromDate(2026, 12, 1)));
        $this->assertSame(2027, AcademicYear::sessionYear(Carbon::createFromDate(2026, 12, 31)));
    }

    #[Test]
    public function a_session_is_named_with_a_single_year(): void
    {
        $session = AcademicYear::currentSession();

        $this->assertSame(
            (string) NumberConverter::toBangla((string) AcademicYear::sessionYear()),
            $session->name,
        );
        $this->assertStringNotContainsString('-', $session->name);
    }

    #[Test]
    public function only_the_current_session_is_flagged_current(): void
    {
        AcademicYear::create([
            'name' => '২০২৫',
            'start_date' => '2025-01-01',
            'end_date' => '2025-12-31',
            'is_current' => true,
        ]);

        $session = AcademicYear::currentSession();

        $this->assertTrue($session->is_current);
        $this->assertSame(1, AcademicYear::where('is_current', true)->count());
        $this->assertSame($session->id, AcademicYear::where('is_current', true)->value('id'));
    }

    #[Test]
    public function dropdown_pages_auto_create_current_session_academic_year(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $label = (string) NumberConverter::toBangla((string) AcademicYear::sessionYear());

        $this->actingAs($admin)->get(route('admin.fees.structures'))->assertOk()->assertSee($label);
        $this->actingAs($admin)->get(route('admin.classes.index'))->assertOk()->assertSee($label);
        $this->actingAs($admin)->get(route('admin.exams.index'))->assertOk()->assertSee($label);

        $this->assertSame(
            1,
            AcademicYear::where('name', 'LIKE', $label.'%')->count(),
        );
    }

    #[Test]
    public function dropdown_skips_creation_when_session_year_already_exists(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        AcademicYear::create([
            'name' => (string) NumberConverter::toBangla((string) AcademicYear::sessionYear()),
            'start_date' => date('Y').'-01-01',
            'end_date' => date('Y').'-12-31',
            'is_current' => true,
        ]);

        $this->actingAs($admin)->get(route('admin.fees.structures'))->assertOk();

        $this->assertSame(1, AcademicYear::count());
    }
}
