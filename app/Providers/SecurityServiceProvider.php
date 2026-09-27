<?php

namespace App\Providers;

use App\Support\RateLimit;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use RuntimeException;

class SecurityServiceProvider extends ServiceProvider
{
    /**
     * Named rate limiters for the endpoints worth attacking.
     *
     * The limits are deliberately layered: a per-identity limit blunts
     * credential stuffing against a single account, while a per-IP limit stops
     * an attacker spreading the same attempt across many accounts.
     */
    public function boot(): void
    {
        $this->guardProductionConfiguration();

        RateLimiter::for('login', fn (Request $request): array => [
            Limit::perMinute(5)->by(RateLimit::byField($request, 'email', 'login')),
            Limit::perHour(30)->by(RateLimit::byIp($request, 'login-ip')),
        ]);

        RateLimiter::for('api-login', fn (Request $request): array => [
            Limit::perMinute(10)->by(RateLimit::byField($request, 'email', 'api-login')),
            Limit::perHour(60)->by(RateLimit::byIp($request, 'api-login-ip')),
        ]);

        RateLimiter::for('api', fn (Request $request): Limit => Limit::perMinute(60)
            ->by(RateLimit::byUser($request, 'api')));

        RateLimiter::for('password', fn (Request $request): Limit => Limit::perMinute(5)
            ->by(RateLimit::byUser($request, 'password')));

        RateLimiter::for('admission', fn (Request $request): array => [
            Limit::perHour(3)->by(RateLimit::byIp($request, 'admission')),
            Limit::perDay(10)->by(RateLimit::byIp($request, 'admission-day')),
        ]);

        RateLimiter::for('contact', fn (Request $request): Limit => Limit::perHour(5)
            ->by(RateLimit::byIp($request, 'contact')));

        RateLimiter::for('public-form', fn (Request $request): Limit => Limit::perHour(10)
            ->by(RateLimit::byIp($request, 'public-form')));

        RateLimiter::for('export', fn (Request $request): array => [
            Limit::perMinute(5)->by(RateLimit::byUser($request, 'export')),
            Limit::perDay(50)->by(RateLimit::byUser($request, 'export-day')),
        ]);

        RateLimiter::for('destructive', fn (Request $request): Limit => Limit::perMinute(20)
            ->by(RateLimit::byUser($request, 'destructive')));
    }

    /**
     * Refuse to serve production traffic with settings that would hand out
     * secrets. A misconfigured .env is far more likely than a deliberate
     * choice here, so fail loudly at boot instead of quietly leaking.
     */
    protected function guardProductionConfiguration(): void
    {
        if (! $this->app->environment('production')) {
            return;
        }

        $problems = [];

        if (Config::get('app.debug')) {
            $problems[] = 'APP_DEBUG must be false.';
        }

        if (Config::get('app.key') === '' || Config::get('app.key') === null) {
            $problems[] = 'APP_KEY is not set.';
        }

        if (! Config::get('session.secure')) {
            $problems[] = 'SESSION_SECURE_COOKIE must be true in production.';
        }

        if (! Config::get('session.http_only')) {
            $problems[] = 'SESSION_HTTP_ONLY must be true in production.';
        }

        if (Config::get('session.encrypt') !== true) {
            $problems[] = 'SESSION_ENCRYPT must be true in production.';
        }

        if (Config::get('auth.default_admin.password') !== null) {
            $problems[] = 'DEFAULT_ADMIN_PASSWORD should be unset once the first administrator exists.';
        }

        if ($problems !== []) {
            throw new RuntimeException(
                "Refusing to boot in production:\n - ".implode("\n - ", $problems)
            );
        }
    }
}
