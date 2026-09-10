<?php

namespace Tests\Feature;

use App\Livewire\Scholarship\RegistrationForm as ScholarshipRegistrationForm;
use App\Models\ScholarshipRegistration;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ScholarshipRegistrationTest extends TestCase
{
    use RefreshDatabase;

    public const VALID_DATA = [
        'studentName' => 'মোঃ রাকিব হাসান',
        'fatherName' => 'আব্দুল করিম',
        'motherName' => 'ফাতেমা বেগম',
        'schoolName' => 'কাটাখালি',
        'classNo' => 1,
        'rollNo' => '12',
        'mobileNo' => '01712345678',
        'bkashNo' => '01912345678',
    ];

    #[Test]
    public function next_registration_number_uses_expected_format(): void
    {
        $year = now()->format('y');

        $this->assertSame("{$year}-1001", ScholarshipRegistration::nextRegistrationNo(1));
        $this->assertSame("{$year}-2001", ScholarshipRegistration::nextRegistrationNo(2));
        $this->assertSame("{$year}-5001", ScholarshipRegistration::nextRegistrationNo(5));
    }

    #[Test]
    public function next_registration_number_increments_serially_per_class(): void
    {
        $year = now()->format('y');

        ScholarshipRegistration::factory()->create([
            'class_no' => 1,
            'serial_no' => 1,
            'registration_no' => "{$year}-1001",
        ]);

        $this->assertSame("{$year}-1002", ScholarshipRegistration::nextRegistrationNo(1));
        $this->assertSame("{$year}-3001", ScholarshipRegistration::nextRegistrationNo(3));
    }

    #[Test]
    public function public_scholarship_page_loads(): void
    {
        $this->get(route('scholarship'))
            ->assertOk()
            ->assertSee('মেধাবৃত্তি');
    }

    #[Test]
    public function selecting_class_generates_registration_number_in_realtime(): void
    {
        $year = now()->format('y');

        Livewire::test(ScholarshipRegistrationForm::class)
            ->set('classNo', 3)
            ->assertSet('registrationNo', "{$year}-3001");
    }

    #[Test]
    public function public_form_validates_required_inputs(): void
    {
        Livewire::test(ScholarshipRegistrationForm::class)
            ->call('submit')
            ->assertHasErrors([
                'studentName' => 'required',
                'fatherName' => 'required',
                'motherName' => 'required',
                'schoolName' => 'required',
                'classNo' => 'required',
                'rollNo' => 'required',
                'mobileNo' => 'required',
                'bkashNo' => 'required_if',
            ])
            ->assertSet('confirming', false);
    }

    #[Test]
    public function public_form_rejects_invalid_mobile_number(): void
    {
        Livewire::test(ScholarshipRegistrationForm::class)
            ->set('mobileNo', '12')
            ->call('submit')
            ->assertHasErrors(['mobileNo']);
    }

    #[Test]
    public function public_form_opens_confirmation_modal_before_saving(): void
    {
        Livewire::test(ScholarshipRegistrationForm::class)
            ->set(self::VALID_DATA)
            ->call('submit')
            ->assertHasNoErrors()
            ->assertSet('confirming', true);

        $this->assertDatabaseCount('scholarship_registrations', 0);
    }

    #[Test]
    public function confirming_registration_creates_record_with_generated_number(): void
    {
        $year = now()->format('y');

        Livewire::test(ScholarshipRegistrationForm::class)
            ->set(self::VALID_DATA)
            ->call('submit')
            ->call('confirm')
            ->assertSet('saved', true);

        $this->assertDatabaseHas('scholarship_registrations', [
            'student_name' => 'মোঃ রাকিব হাসান',
            'class_no' => 1,
            'registration_no' => "{$year}-1001",
            'serial_no' => 1,
            'mobile_no' => '01712345678',
            'bkash_no' => '01912345678',
            'payment_method' => 'bkash',
            'status' => 'pending',
        ]);

        $this->assertDatabaseCount('scholarship_registrations', 1);
    }

    #[Test]
    public function admin_can_create_registration_and_gets_redirected(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'email' => 'admin@example.com']);

        Livewire::actingAs($admin)
            ->test(ScholarshipRegistrationForm::class, ['adminMode' => true])
            ->set(self::VALID_DATA)
            ->call('submit')
            ->call('confirm')
            ->assertRedirectToRoute('admin.scholarship.index');

        $this->assertDatabaseHas('scholarship_registrations', [
            'student_name' => 'মোঃ রাকিব হাসান',
            'created_by' => $admin->id,
        ]);
    }

    #[Test]
    public function public_form_requires_bkash_number_when_payment_is_bkash(): void
    {
        Livewire::test(ScholarshipRegistrationForm::class)
            ->set(self::VALID_DATA)
            ->set('bkashNo', '')
            ->call('submit')
            ->assertHasErrors(['bkashNo']);
    }

    #[Test]
    public function admin_can_create_registration_with_cash_payment(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'email' => 'admin@example.com']);

        Livewire::actingAs($admin)
            ->test(ScholarshipRegistrationForm::class, ['adminMode' => true])
            ->set(self::VALID_DATA)
            ->set('paymentMethod', 'cash')
            ->set('bkashNo', '')
            ->call('submit')
            ->call('confirm')
            ->assertRedirectToRoute('admin.scholarship.index');

        $this->assertDatabaseHas('scholarship_registrations', [
            'student_name' => 'মোঃ রাকিব হাসান',
            'payment_method' => 'cash',
            'bkash_no' => null,
        ]);
    }

    #[Test]
    public function admin_panel_generates_registration_number_when_class_selected(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'email' => 'admin@example.com']);
        $year = now()->format('y');

        Livewire::actingAs($admin)
            ->test(ScholarshipRegistrationForm::class, ['adminMode' => true])
            ->set('classNo', 3)
            ->assertSet('registrationNo', "{$year}-3001");
    }

    #[Test]
    public function admin_panel_shows_validation_errors_and_opens_modal(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'email' => 'admin@example.com']);

        Livewire::actingAs($admin)
            ->test(ScholarshipRegistrationForm::class, ['adminMode' => true])
            ->call('submit')
            ->assertHasErrors([
                'studentName',
                'fatherName',
                'motherName',
                'schoolName',
                'classNo',
                'rollNo',
                'mobileNo',
            ])
            ->assertSet('confirming', false);

        Livewire::actingAs($admin)
            ->test(ScholarshipRegistrationForm::class, ['adminMode' => true])
            ->set(self::VALID_DATA)
            ->call('submit')
            ->assertHasNoErrors()
            ->assertSet('confirming', true);
    }

    #[Test]
    public function admin_registration_list_requires_authentication(): void
    {
        $this->get(route('admin.scholarship.index'))
            ->assertRedirect(route('login'));
    }

    #[Test]
    public function admin_registration_list_shows_registrations(): void
    {
        /** @var User $admin */
        $admin = User::factory()->create(['role' => 'admin', 'email' => 'admin@example.com']);

        ScholarshipRegistration::factory()->create([
            'student_name' => 'আয়েশা সিদ্দিকা',
            'class_no' => 2,
        ]);

        $this->actingAs($admin)
            ->get(route('admin.scholarship.index'))
            ->assertOk()
            ->assertSee('আয়েশা সিদ্দিকা');
    }

    #[Test]
    public function admin_can_open_create_registration_page(): void
    {
        /** @var User $admin */
        $admin = User::factory()->create(['role' => 'admin', 'email' => 'admin@example.com']);

        $this->actingAs($admin)
            ->get(route('admin.scholarship.create'))
            ->assertOk()
            ->assertSee('মেধাবৃত্তি');
    }
}
