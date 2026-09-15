<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Visit;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class VisitorTrackingTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function visitor_is_recorded_by_ip_address(): void
    {
        $this->get(route('home'))->assertOk();

        $this->assertDatabaseCount('visits', 1);
        $this->assertDatabaseHas('visits', ['ip_address' => '127.0.0.1']);
    }

    #[Test]
    public function same_ip_same_day_is_not_counted_again(): void
    {
        $this->withServerVariables(['REMOTE_ADDR' => '1.2.3.4'])
            ->get(route('home'))
            ->assertOk();

        $this->withServerVariables(['REMOTE_ADDR' => '1.2.3.4'])
            ->get(route('about'))
            ->assertOk();

        $this->assertDatabaseCount('visits', 1);
    }

    #[Test]
    public function different_ips_same_day_are_counted_separately(): void
    {
        $this->withServerVariables(['REMOTE_ADDR' => '1.2.3.4'])->get(route('home'));
        $this->withServerVariables(['REMOTE_ADDR' => '5.6.7.8'])->get(route('home'));

        $this->assertDatabaseCount('visits', 2);
    }

    #[Test]
    public function same_ip_on_another_day_is_counted(): void
    {
        $this->withServerVariables(['REMOTE_ADDR' => '1.2.3.4'])
            ->get(route('home'))
            ->assertOk();

        $this->travelTo(now()->addDay()->startOfDay());

        $this->withServerVariables(['REMOTE_ADDR' => '1.2.3.4'])
            ->get(route('home'))
            ->assertOk();

        $this->assertDatabaseCount('visits', 2);
    }

    #[Test]
    public function counts_report_unique_ips(): void
    {
        Visit::factory()->create(['ip_address' => '1.1.1.1', 'visited_at' => today()]);
        Visit::factory()->create(['ip_address' => '2.2.2.2', 'visited_at' => today()]);
        Visit::factory()->create(['ip_address' => '3.3.3.3', 'visited_at' => today()->subDay()]);

        $this->assertSame(2, Visit::todayUnique());
        $this->assertSame(3, Visit::totalUnique());
    }

    #[Test]
    public function footer_shows_visitor_counts_in_bengali(): void
    {
        Visit::factory()->create(['ip_address' => '1.1.1.1', 'visited_at' => today()]);
        Visit::factory()->create(['ip_address' => '2.2.2.2', 'visited_at' => today()->subDay()]);

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('আজকের দর্শক')
            ->assertSee('মোট দর্শক')
            ->assertSee('data-count="1"', false)
            ->assertSee('data-count="2"', false);
    }

    #[Test]
    public function admin_pages_do_not_record_visits(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'email' => 'admin@example.com']);

        $this->actingAs($admin)
            ->get(route('admin.dashboard'))
            ->assertOk();

        $this->assertDatabaseCount('visits', 0);
    }
}
