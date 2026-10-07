<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Visit;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
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
    public function long_facebook_in_app_browser_user_agent_is_recorded(): void
    {
        $userAgent = 'Mozilla/5.0 (iPhone; CPU iPhone OS 26_6_2 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Mobile/23G90 Safari/604.1 [FBAN/FBIOS;FBAV/581.0.0.64.71;FBBV/1080178719;FBDV/iPhone12,3;FBMD/iPhone;FBSN/iOS;FBSV/26.6.2;FBSS/3;FBID/phone;FBLC/en_US;FBOP/5;FBRV/1085250500;IABMV/1]';
        $url = route('scholarship').'?fbclid=IwZXh0bgNhZW0CMTEAcGRvZgVmZGlkFlD8RnGjZSih93efeoUP69fdp-zu3UtzcnRjBmFwcF9pZAo2NjI4NTY4Mzc5AAEeFHwmc0S0xO_7761bYqJ1eeiKhroWqOvxIM_iEs5AQ48J9lbsAH2VhidcMSE_aem_0NSPknit6DxjCSa7SbWB9A';

        $this->withHeaders(['User-Agent' => $userAgent])
            ->get($url)
            ->assertOk();

        $this->assertDatabaseHas('visits', [
            'user_agent' => $userAgent,
            'url' => $url,
        ]);
    }

    #[Test]
    public function visits_user_agent_and_url_columns_are_text(): void
    {
        $this->assertSame('text', strtolower(Schema::getColumnType('visits', 'user_agent')));
        $this->assertSame('text', strtolower(Schema::getColumnType('visits', 'url')));
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
