<?php

namespace Tests\Feature;

use App\Models\SchoolStatistic;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class SchoolStatisticAdminTest extends TestCase
{
    use RefreshDatabase;

    private ?User $adminUser = null;

    private function admin(): User
    {
        return $this->adminUser ??= User::factory()->create(['role' => 'admin', 'email' => 'school-stats-admin@example.com']);
    }

    #[Test]
    public function admin_can_view_school_statistics_index_without_data(): void
    {
        $this->actingAs($this->admin())
            ->get('/admin/school-statistics')
            ->assertOk()
            ->assertSee('আমাদের স্কুল এক নজরে')
            ->assertDontSee('value="'.SchoolStatistic::defaults()['total_students'].'"', false);
    }

    #[Test]
    public function admin_can_store_school_statistics(): void
    {
        $this->actingAs($this->admin())
            ->post('/admin/school-statistics', [
                'total_students' => 650,
                'total_teachers' => 30,
                'total_classes' => 10,
                'total_staff' => 15,
                'founding_year' => 2012,
            ])
            ->assertSessionHas('success');

        $this->assertDatabaseHas('school_statistics', [
            'total_students' => 650,
            'total_teachers' => 30,
            'total_classes' => 10,
            'total_staff' => 15,
            'founding_year' => 2012,
        ]);
    }

    #[Test]
    public function admin_cannot_store_duplicate_statistics(): void
    {
        SchoolStatistic::create(SchoolStatistic::defaults());

        $this->actingAs($this->admin())
            ->post('/admin/school-statistics', [
                'total_students' => 999,
                'total_teachers' => 99,
                'total_classes' => 9,
                'total_staff' => 9,
                'founding_year' => 2010,
            ])
            ->assertSessionHas('error');

        $this->assertSame(1, SchoolStatistic::count());
    }

    #[Test]
    public function admin_can_update_school_statistics(): void
    {
        $statistic = SchoolStatistic::create(SchoolStatistic::defaults());

        $this->actingAs($this->admin())
            ->put("/admin/school-statistics/{$statistic->id}", [
                'total_students' => 720,
                'total_teachers' => 35,
                'total_classes' => 12,
                'total_staff' => 18,
                'founding_year' => 2011,
            ])
            ->assertSessionHas('success');

        $this->assertDatabaseHas('school_statistics', [
            'id' => $statistic->id,
            'total_students' => 720,
            'founding_year' => 2011,
        ]);
    }

    #[Test]
    public function school_statistics_store_requires_valid_data(): void
    {
        $this->actingAs($this->admin())
            ->post('/admin/school-statistics', [
                'total_students' => -5,
                'total_teachers' => 'abc',
                'total_classes' => '',
                'total_staff' => null,
                'founding_year' => 3000,
            ])
            ->assertSessionHasErrors([
                'total_students',
                'total_teachers',
                'total_classes',
                'total_staff',
                'founding_year',
            ]);

        $this->assertDatabaseCount('school_statistics', 0);
    }

    #[Test]
    public function public_homepage_shows_default_statistics_when_database_is_empty(): void
    {
        $defaults = SchoolStatistic::defaults();

        $this->get('/')
            ->assertOk()
            ->assertSee('আমাদের স্কুল এক নজরে')
            ->assertSee((string) $defaults['total_students'])
            ->assertSee((string) $defaults['total_teachers'])
            ->assertSee((string) $defaults['total_classes'])
            ->assertSee((string) $defaults['total_staff']);
    }

    #[Test]
    public function public_homepage_prefers_database_statistics_over_defaults(): void
    {
        SchoolStatistic::create([
            'total_students' => 1234,
            'total_teachers' => 43,
            'total_classes' => 15,
            'total_staff' => 22,
            'founding_year' => 2008,
        ]);

        $experience = max((int) date('Y') - 2008, 1);

        $this->get('/')
            ->assertOk()
            ->assertSee('1234')
            ->assertSee('43')
            ->assertSee('15')
            ->assertSee('22')
            ->assertSee((string) $experience);
    }

    #[Test]
    public function admin_index_never_shows_default_statistics(): void
    {
        $this->actingAs($this->admin())
            ->get('/admin/school-statistics')
            ->assertOk()
            ->assertDontSee('value="500"', false)
            ->assertDontSee('value="20"', false)
            ->assertDontSee('value="2015"', false);
    }

    #[Test]
    public function admin_shows_only_database_statistics(): void
    {
        SchoolStatistic::create([
            'total_students' => 777,
            'total_teachers' => 33,
            'total_classes' => 11,
            'total_staff' => 14,
            'founding_year' => 2009,
        ]);

        $this->actingAs($this->admin())
            ->get('/admin/school-statistics')
            ->assertOk()
            ->assertSee('value="777"', false)
            ->assertSee('value="33"', false)
            ->assertDontSee('value="500"', false);
    }

    #[Test]
    public function school_statistics_routes_require_admin_role(): void
    {
        $teacher = User::factory()->create(['role' => 'teacher']);

        $this->actingAs($teacher)
            ->get('/admin/school-statistics')
            ->assertForbidden();

        $this->assertDatabaseCount('school_statistics', 0);
    }
}
