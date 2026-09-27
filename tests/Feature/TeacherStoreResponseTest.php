<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class TeacherStoreResponseTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function a_json_request_creating_a_teacher_receives_the_temporary_password(): void
    {
        Storage::fake('public');

        $this->actingAs($this->admin())
            ->postJson(route('admin.teachers.store'), $this->payload())
            ->assertOk()
            ->assertJsonStructure(['message', 'temporary_password']);
    }

    #[Test]
    public function a_form_request_creating_a_teacher_is_redirected(): void
    {
        Storage::fake('public');

        $this->actingAs($this->admin())
            ->post(route('admin.teachers.store'), $this->payload())
            ->assertRedirect(route('admin.teachers.index'))
            ->assertSessionHas('success');
    }

    #[Test]
    public function a_duplicate_email_answers_json_with_a_422_and_creates_nothing(): void
    {
        Storage::fake('public');

        $email = 'taken@example.com';
        User::factory()->create(['email' => $email]);

        $this->actingAs($this->admin())
            ->postJson(route('admin.teachers.store'), $this->payload(['email' => $email]))
            ->assertStatus(422);

        $this->assertSame(1, User::where('email', $email)->count());
    }

    private function admin(): User
    {
        return User::factory()->create(['role' => 'admin']);
    }

    /**
     * @param  array<string, mixed>  $overrides
     * @return array<string, mixed>
     */
    private function payload(array $overrides = []): array
    {
        return array_merge([
            'name' => 'শিক্ষক',
            'email' => 'teacher'.fake()->unique()->numberBetween(1, 999999).'@example.com',
            'phone' => '01700000000',
            'photo' => UploadedFile::fake()->image('teacher.jpg'),
            'designation' => 'প্রভাসী',
            'subject' => 'বাংলা',
            'qualification' => 'এমএ',
            'institute' => 'ঢাবি',
            'joining_date' => '2024-01-01',
        ], $overrides);
    }
}
