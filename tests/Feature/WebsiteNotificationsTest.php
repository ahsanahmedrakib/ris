<?php

namespace Tests\Feature;

use App\Livewire\Scholarship\RegistrationForm as ScholarshipRegistrationForm;
use App\Models\Admission;
use App\Models\User;
use App\Notifications\NewSubmission;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class WebsiteNotificationsTest extends TestCase
{
    use RefreshDatabase;

    public const VALID_ADMISSION_DATA = [
        'academic_year' => '২৬',
        'student_name_bn' => 'মোঃ রাকিব হাসান',
        'student_name_en' => 'MD. RAKIB HASAN',
        'dob' => '2019-05-10',
        'age' => '৬',
        'nationality' => 'বাংলাদেশী',
        'religion' => 'ইসলাম',
        'blood_group' => 'O+',
        'father_name_bn' => 'আব্দুল করিম',
        'father_name_en' => 'ABDUL KARIM',
        'father_occupation' => 'চাকরিজীবী',
        'mother_name_bn' => 'ফাতেমা বেগম',
        'mother_name_en' => 'FATEMA BEGUM',
        'mother_occupation' => 'গৃহিণী',
        'present_address' => 'গোপালগঞ্জ, বাংলাদেশ',
        'permanent_address' => 'গোপালগঞ্জ, বাংলাদেশ',
        'phone' => '01712345678',
        'emergency_contact' => '01712345679',
        'legal_guardian_name' => 'মোঃ করিম উদ্দিন',
        'legal_guardian_occupation' => 'ব্যবসায়ী',
        'legal_guardian_relation' => 'চাচা',
        'legal_guardian_address' => 'গোপালগঞ্জ',
        'local_guardian_name' => 'মোঃ রহমান',
        'local_guardian_occupation' => 'শিক্ষক',
        'local_guardian_relation' => 'চাচা',
        'local_guardian_address' => 'গোপালগঞ্জ',
        'local_guardian_phone' => '01712345680',
        'prev_school_name' => 'সরকারি প্রাথমিক বিদ্যালয়',
        'prev_school_address' => 'গোপালগঞ্জ',
        'prev_marks' => '৮০',
        'reference' => 'মোঃ আহমেদ',
        'reference_phone' => '01712345681',
        'class_level' => ['নার্সারি'],
        'batch' => ['প্রভাতী'],
    ];

    public const VALID_SCHOLARSHIP_DATA = [
        'studentName' => 'মোঃ রাকিব হাসান',
        'fatherName' => 'আব্দুল করিম',
        'motherName' => 'ফাতেমা বেগম',
        'schoolName' => 'কাটাখালি',
        'classNo' => 1,
        'rollNo' => '12',
        'mobileNo' => '01712345678',
        'bkashNo' => '01912345678',
    ];

    private function createAdmins(): array
    {
        return [
            User::factory()->create(['role' => 'admin', 'email' => 'admin1@example.com']),
            User::factory()->create(['role' => 'admin', 'email' => 'admin2@example.com']),
        ];
    }

    #[Test]
    public function admission_submission_notifies_all_active_admins(): void
    {
        $admins = $this->createAdmins();
        User::factory()->create(['role' => 'teacher', 'email' => 'teacher@example.com']);

        Storage::fake('public');

        $this->post(route('admission.store'), [
            ...self::VALID_ADMISSION_DATA,
            'student_photo' => UploadedFile::fake()->image('student.jpg', 200, 200),
        ])->assertRedirect(route('admission'));

        $admission = Admission::first();

        foreach ($admins as $admin) {
            $this->assertEquals(1, $admin->notifications()->count());
            $this->assertEquals('admission', $admin->notifications()->first()->data['type']);
            $this->assertEquals(route('admin.admission.show', $admission), $admin->notifications()->first()->data['url']);
        }

        $teacher = User::where('role', 'teacher')->first();
        $this->assertEquals(0, $teacher->notifications()->count());
    }

    #[Test]
    public function contact_submission_notifies_admins(): void
    {
        $admins = $this->createAdmins();

        $this->post(route('contact.send'), [
            'name' => 'রাকিব',
            'email' => 'rakib@example.com',
            'phone' => '01712345678',
            'subject' => 'ভর্তি সম্পর্কে জানতে চাই',
            'message' => 'ভর্তি প্রক্রিয়া সম্পর্কে জানতে চাই।',
        ])->assertRedirect(route('contact'));

        foreach ($admins as $admin) {
            $this->assertEquals(1, $admin->notifications()->count());
            $this->assertEquals('contact', $admin->notifications()->first()->data['type']);
            $this->assertEquals(route('admin.contact-messages.show', 1), $admin->notifications()->first()->data['url']);
        }
    }

    #[Test]
    public function testimonial_submission_notifies_admins(): void
    {
        $admins = $this->createAdmins();

        $this->post(route('testimonials.submit'), [
            'name' => 'রাকিব',
            'message' => 'স্কুলটি খুবই ভালো।',
            'rating' => 5,
        ])->assertRedirect(route('testimonials'));

        foreach ($admins as $admin) {
            $this->assertEquals(1, $admin->notifications()->count());
            $this->assertEquals('testimonial', $admin->notifications()->first()->data['type']);
            $this->assertEquals(route('admin.testimonials.show', 1), $admin->notifications()->first()->data['url']);
        }
    }

    #[Test]
    public function public_scholarship_registration_notifies_admins(): void
    {
        $admins = $this->createAdmins();

        Livewire::test(ScholarshipRegistrationForm::class)
            ->set(self::VALID_SCHOLARSHIP_DATA)
            ->call('submit')
            ->call('confirm')
            ->assertSet('saved', true);

        foreach ($admins as $admin) {
            $this->assertEquals(1, $admin->notifications()->count());
            $this->assertEquals('scholarship', $admin->notifications()->first()->data['type']);
        }
    }

    #[Test]
    public function admin_created_scholarship_registration_does_not_notify(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'email' => 'admin@example.com']);

        Livewire::actingAs($admin)
            ->test(ScholarshipRegistrationForm::class, ['adminMode' => true])
            ->set(self::VALID_SCHOLARSHIP_DATA)
            ->call('submit')
            ->call('confirm')
            ->assertRedirectToRoute('admin.scholarship.index');

        $this->assertEquals(0, $admin->notifications()->count());
    }

    #[Test]
    public function admin_can_view_notifications_list(): void
    {
        /** @var User $admin */
        $admin = User::factory()->create(['role' => 'admin', 'email' => 'admin@example.com']);
        $admin->notify(new NewSubmission('contact', 'নতুন কনটাক্ট মেসেজ', 'রাকিব মেসেজ পাঠিয়েছেন।', route('admin.contact-messages.show', 1)));

        $this->actingAs($admin)
            ->get(route('admin.notifications.index'))
            ->assertOk()
            ->assertSee('নতুন কনটাক্ট মেসেজ');
    }

    #[Test]
    public function reading_notification_marks_as_read_and_redirects_to_target(): void
    {
        /** @var User $admin */
        $admin = User::factory()->create(['role' => 'admin', 'email' => 'admin@example.com']);
        $target = route('admin.contact-messages.show', 1);
        $admin->notify(new NewSubmission('contact', 'নতুন কনটাক্ট মেসেজ', 'রাকিব মেসেজ পাঠিয়েছেন।', $target));

        $notification = $admin->notifications()->first();

        $this->actingAs($admin)
            ->get(route('admin.notifications.read', $notification))
            ->assertRedirect($target);

        $this->assertNotNull($notification->fresh()->read_at);
    }

    #[Test]
    public function admin_cannot_read_another_users_notification(): void
    {
        /** @var User $owner */
        $owner = User::factory()->create(['role' => 'admin', 'email' => 'owner@example.com']);
        /** @var User $other */
        $other = User::factory()->create(['role' => 'admin', 'email' => 'other@example.com']);
        $owner->notify(new NewSubmission('contact', 'নতুন কনটাক্ট মেসেজ', 'বার্তা।', route('admin.contact-messages.show', 1)));

        $notification = $owner->notifications()->first();

        $this->actingAs($other)
            ->get(route('admin.notifications.read', $notification))
            ->assertForbidden();
    }

    #[Test]
    public function read_all_marks_every_notification_as_read(): void
    {
        /** @var User $admin */
        $admin = User::factory()->create(['role' => 'admin', 'email' => 'admin@example.com']);
        $admin->notify(new NewSubmission('contact', 'নতুন কনটাক্ট মেসেজ', 'বার্তা ১।', route('admin.contact-messages.show', 1)));
        $admin->notify(new NewSubmission('admission', 'নতুন ভর্তি আবেদন', 'আবেদন ১।', route('admin.admission.show', 1)));

        $this->actingAs($admin)
            ->post(route('admin.notifications.read-all'))
            ->assertRedirect();

        $this->assertEquals(0, $admin->unreadNotifications()->count());
        $this->assertEquals(2, $admin->notifications()->count());
    }
}
