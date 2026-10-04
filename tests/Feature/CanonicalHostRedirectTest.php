<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class CanonicalHostRedirectTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function a_request_on_another_host_is_permanently_redirected_to_the_canonical_url(): void
    {
        config()->set('app.canonical_url', 'https://ris.edu.bd');

        $this->get('http://ris.cleverapps.io/admission?step=2')
            ->assertStatus(301)
            ->assertRedirect('https://ris.edu.bd/admission?step=2');
    }

    #[Test]
    public function the_canonical_host_is_served_normally(): void
    {
        config()->set('app.canonical_url', 'https://ris.edu.bd');

        $this->get('http://ris.edu.bd')->assertSuccessful();
    }

    #[Test]
    public function the_host_comparison_ignores_case_and_a_configured_port(): void
    {
        config()->set('app.canonical_url', 'RIS.edu.bd');

        $this->get('http://ris.edu.bd')->assertSuccessful();
    }

    #[Test]
    public function the_health_check_is_never_redirected(): void
    {
        config()->set('app.canonical_url', 'https://ris.edu.bd');

        // The platform polls this path to decide whether the instance is alive,
        // and it treats a redirect as a failure, so redirecting it would have
        // it restarting a healthy instance forever.
        $this->get('http://ris.cleverapps.io/up')->assertSuccessful();
    }

    #[Test]
    public function certificate_validation_is_never_redirected(): void
    {
        config()->set('app.canonical_url', 'https://ris.edu.bd');

        $this->get('http://ris.cleverapps.io/.well-known/acme-challenge/token')
            ->assertNotFound();
    }

    #[Test]
    public function a_non_get_request_is_redirected_without_losing_its_method_or_body(): void
    {
        config()->set('app.canonical_url', 'https://ris.edu.bd');

        // 301 tells a client to resubmit a POST as a GET, which drops the body.
        $this->post('http://ris.cleverapps.io/api/login', ['email' => 'a@b.test'])
            ->assertStatus(308)
            ->assertRedirect('https://ris.edu.bd/api/login');
    }

    #[Test]
    public function nothing_is_redirected_while_the_canonical_url_is_unset(): void
    {
        config()->set('app.canonical_url', '');

        $this->get('http://ris.cleverapps.io')->assertSuccessful();
    }
}
