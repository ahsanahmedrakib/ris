<?php

namespace Tests\Feature;

use App\Models\AcademicYear;
use App\Models\ClassRoom;
use App\Models\FeeStructure;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class FeeStructureAdminTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @return array{year: AcademicYear, class: ClassRoom}
     */
    private function school(bool $current = true): array
    {
        $year = AcademicYear::create([
            'name' => $current ? '2026' : '2025',
            'start_date' => $current ? '2026-01-01' : '2025-01-01',
            'end_date' => $current ? '2026-12-31' : '2025-12-31',
            'is_current' => $current,
        ]);

        $class = ClassRoom::create([
            'name' => $current ? '১ম' : '২য়',
            'section' => 'ক',
            'academic_year_id' => $year->id,
        ]);

        return ['year' => $year, 'class' => $class];
    }

    private function structure(array $school, string $feeType = 'tuition', float $amount = 2000): FeeStructure
    {
        return FeeStructure::create([
            'class_id' => $school['class']->id,
            'academic_year_id' => $school['year']->id,
            'fee_type' => $feeType,
            'amount' => $amount,
            'due_date' => '2026-03-31',
        ]);
    }

    #[Test]
    public function admin_can_create_fee_structure(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $school = $this->school();

        $this->actingAs($admin)
            ->post(route('admin.fees.structures.store'), [
                'class_id' => $school['class']->id,
                'academic_year_id' => $school['year']->id,
                'fee_type' => 'tuition',
                'amount' => 2500,
                'due_date' => '2026-03-31',
            ])
            ->assertRedirect(route('admin.fees.structures'));

        $this->assertDatabaseHas('fee_structures', [
            'class_id' => $school['class']->id,
            'fee_type' => 'tuition',
            'amount' => 2500,
        ]);
    }

    #[Test]
    public function admin_can_view_structures_and_edit_pages(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $school = $this->school();
        $structure = $this->structure($school);

        $this->actingAs($admin)
            ->get(route('admin.fees.structures'))
            ->assertOk()
            ->assertSee($school['class']->name);

        $this->actingAs($admin)
            ->get(route('admin.fees.structures.edit', $structure))
            ->assertOk()
            ->assertJson(['id' => $structure->id, 'amount' => '2000.00']);
    }

    #[Test]
    public function admin_can_update_fee_structure(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $school = $this->school();
        $structure = $this->structure($school);

        $this->actingAs($admin)
            ->put(route('admin.fees.structures.update', $structure), [
                'class_id' => $school['class']->id,
                'academic_year_id' => $school['year']->id,
                'fee_type' => 'transport',
                'amount' => 1500,
                'description' => 'মাসিক পরিবহন ফি',
                'due_date' => '2026-04-10',
            ])
            ->assertRedirect(route('admin.fees.structures'));

        $this->assertDatabaseHas('fee_structures', [
            'id' => $structure->id,
            'fee_type' => 'transport',
            'amount' => 1500,
            'description' => 'মাসিক পরিবহন ফি',
        ]);
    }

    #[Test]
    public function admin_can_delete_fee_structure(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $school = $this->school();
        $structure = $this->structure($school);

        $this->actingAs($admin)
            ->delete(route('admin.fees.structures.destroy', $structure))
            ->assertRedirect(route('admin.fees.structures'));

        $this->assertSoftDeleted('fee_structures', ['id' => $structure->id]);
    }

    #[Test]
    public function website_shows_only_current_academic_year_fees(): void
    {
        $current = $this->school(true);
        $previous = $this->school(false);

        $this->structure($current, 'tuition', 1111);
        $this->structure($previous, 'tuition', 9999);

        $this->get(route('academic.fees'))
            ->assertOk()
            ->assertSee('1,111')
            ->assertDontSee('9,999');
    }

    #[Test]
    public function website_fees_page_renders_without_current_academic_year(): void
    {
        $this->get(route('academic.fees'))
            ->assertOk()
            ->assertSee('এখনো কোনো ফি কাঠামো নেই');
    }
}
