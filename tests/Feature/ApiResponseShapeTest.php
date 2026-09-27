<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\AcademicYear;
use App\Models\ClassRoom;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Locks down the shape of the v1 API.
 *
 * Moving the controllers from hand-built arrays to Eloquent resources changed
 * the wire format: collections are now wrapped in a data envelope, and the
 * endpoints that paginate also carry links and meta. That is the contract
 * external clients rely on, so it is asserted here rather than left to drift.
 */
class ApiResponseShapeTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Endpoints that page their results.
     *
     * @return array<string, array<int, string>>
     */
    public static function paginatedRoutes(): array
    {
        return [
            'students' => ['api.students.index'],
            'attendance' => ['api.attendance.index'],
            'exams' => ['api.exams.index'],
            'notices' => ['api.notices.index'],
            'books' => ['api.books.index'],
            'fees' => ['api.fees.index'],
            'staff' => ['api.staff.index'],
            'payroll' => ['api.payroll.index'],
        ];
    }

    /**
     * Endpoints that deliberately return every record, because they back
     * dropdowns and reference lists that a client needs in full.
     *
     * @return array<string, array<int, string>>
     */
    public static function unpaginatedRoutes(): array
    {
        return [
            'classes' => ['api.classes.index'],
            'subjects' => ['api.subjects.index'],
            'transport' => ['api.transport.index'],
        ];
    }

    #[Test]
    #[DataProvider('paginatedRoutes')]
    public function paginated_endpoints_return_a_pagination_envelope(string $route): void
    {
        $response = $this->actingAs($this->admin(), 'api')->getJson(route($route));

        $response->assertSuccessful();
        $response->assertJsonStructure([
            'data',
            'links' => ['first', 'last', 'prev', 'next'],
            'meta' => ['current_page', 'from', 'last_page', 'path', 'per_page', 'to', 'total'],
        ]);
    }

    #[Test]
    #[DataProvider('unpaginatedRoutes')]
    public function unpaginated_endpoints_return_a_plain_data_array(string $route): void
    {
        $response = $this->actingAs($this->admin(), 'api')->getJson(route($route));

        $response->assertSuccessful();
        $response->assertJsonStructure(['data' => []]);
        $this->assertIsArray($response->json('data'));
        $this->assertArrayNotHasKey('meta', $response->json());
    }

    #[Test]
    public function a_collection_item_is_wrapped_under_data(): void
    {
        $student = $this->student();

        $response = $this->actingAs($this->admin(), 'api')
            ->getJson(route('api.students.index'));

        $response->assertSuccessful();
        $response->assertJsonPath('data.0.id', $student->id);
        $this->assertIsArray($response->json('data.0'));
    }

    #[Test]
    public function show_endpoints_return_a_single_wrapped_object(): void
    {
        $student = $this->student();

        $response = $this->actingAs($this->admin(), 'api')
            ->getJson(route('api.students.show', $student));

        $response->assertSuccessful();
        $response->assertJsonPath('data.id', $student->id);
        $response->assertJsonMissingPath('id');
    }

    #[Test]
    public function resources_never_leak_model_internals(): void
    {
        $student = $this->student();

        $response = $this->actingAs($this->admin(), 'api')
            ->getJson(route('api.students.show', $student));

        $response->assertSuccessful();

        foreach (['password', 'remember_token', 'token_version'] as $leaked) {
            $this->assertArrayNotHasKey($leaked, $response->json('data'), "{$leaked} leaked in payload");
        }
    }

    #[Test]
    public function a_password_is_never_exposed_by_the_auth_endpoints(): void
    {
        $user = $this->admin();

        $login = $this->postJson(route('api.auth.login'), [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $login->assertSuccessful();
        $this->assertArrayNotHasKey('password', $login->json('user'));

        $me = $this->withToken($login->json('token'))->getJson(route('api.auth.me'));
        $me->assertSuccessful();
        $this->assertArrayNotHasKey('password', $me->json('user'));
    }

    private function admin(): User
    {
        return User::factory()->create(['role' => UserRole::Admin->value]);
    }

    private function student(): Student
    {
        $year = AcademicYear::create([
            'name' => '২০২৬',
            'start_date' => '2026-01-01',
            'end_date' => '2026-12-31',
            'is_current' => true,
        ]);

        $classRoom = ClassRoom::create([
            'name' => 'Class 1',
            'section' => 'A',
            'academic_year_id' => $year->id,
        ]);

        $user = User::factory()->create(['role' => UserRole::Student->value]);

        return Student::create([
            'user_id' => $user->id,
            'admission_no' => '2026-1001',
            'class_id' => $classRoom->id,
            'section' => 'A',
            'roll_no' => 1,
            'date_of_birth' => '2018-01-10',
            'gender' => 'male',
            'address' => 'ঢাকা',
            'guardian_name' => 'পিতা',
            'guardian_phone' => '01700000000',
        ]);
    }
}
