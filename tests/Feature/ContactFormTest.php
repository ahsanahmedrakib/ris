<?php

namespace Tests\Feature;

use App\Models\ContactMessage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ContactFormTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function ajax_submission_returns_json_message_without_redirect(): void
    {
        $response = $this->postJson(route('contact.send'), [
            'name' => 'রাকিব',
            'phone' => '01712345678',
            'subject' => 'ভর্তি সম্পর্কে জানতে চাই',
            'message' => 'ভর্তি প্রক্রিয়া সম্পর্কে জানতে চাই।',
        ]);

        $response->assertOk()
            ->assertExactJson(['message' => 'আপনার বার্তা সফলভাবে পাঠানো হয়েছে। আমরা শীঘ্রই যোগাযোগ করব।']);

        $this->assertDatabaseHas('contact_messages', [
            'name' => 'রাকিব',
            'phone' => '01712345678',
        ]);
    }

    #[Test]
    public function ajax_submission_returns_json_validation_errors(): void
    {
        $this->postJson(route('contact.send'), [
            'name' => 'রাকিব',
            'subject' => 'ভর্তি সম্পর্কে জানতে চাই',
            'message' => 'ভর্তি প্রক্রিয়া সম্পর্কে জানতে চাই।',
        ])->assertUnprocessable()
            ->assertJsonValidationErrors('phone');

        $this->assertSame(0, ContactMessage::count());
    }

    #[Test]
    public function contact_page_renders(): void
    {
        $this->get('/contact')
            ->assertOk()
            ->assertSee('আমাদের লিখুন')
            ->assertSee('contactForm()')
            ->assertSee('validateField');
    }

    #[Test]
    public function phone_number_is_required(): void
    {
        $this->post(route('contact.send'), [
            'name' => 'রাকিব',
            'email' => 'rakib@example.com',
            'subject' => 'ভর্তি সম্পর্কে জানতে চাই',
            'message' => 'ভর্তি প্রক্রিয়া সম্পর্কে জানতে চাই।',
        ])->assertSessionHasErrors('phone');

        $this->assertDatabaseCount('contact_messages', 0);
    }

    #[Test]
    public function email_is_optional(): void
    {
        $this->post(route('contact.send'), [
            'name' => 'রাকিব',
            'phone' => '01712345678',
            'subject' => 'ভর্তি সম্পর্কে জানতে চাই',
            'message' => 'ভর্তি প্রক্রিয়া সম্পর্কে জানতে চাই।',
        ])->assertRedirect(route('contact'));

        $this->assertDatabaseHas('contact_messages', [
            'name' => 'রাকিব',
            'phone' => '01712345678',
            'email' => null,
        ]);
    }

    #[Test]
    public function email_must_be_valid_when_provided(): void
    {
        $this->post(route('contact.send'), [
            'name' => 'রাকিব',
            'email' => 'not-an-email',
            'phone' => '01712345678',
            'subject' => 'ভর্তি সম্পর্কে জানতে চাই',
            'message' => 'ভর্তি প্রক্রিয়া সম্পর্কে জানতে চাই।',
        ])->assertSessionHasErrors('email');

        $this->assertSame(0, ContactMessage::count());
    }
}
