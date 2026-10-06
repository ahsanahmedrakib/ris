<?php

use App\Core\Http\Middleware\CheckRole;
use App\Core\Http\Middleware\EnsureUserIsCurrent;
use App\Core\Http\Middleware\ForceJsonResponse;
use App\Core\Http\Middleware\SecurityHeaders;
use App\Core\Http\Middleware\TrackVisitor;
use App\Http\Middleware\ConvertRedirectsToJson;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withCommands([
        // Laravel discovers this directory on its own in 11 and later, but only
        // when the directory is named here. Without it seo:og-image never
        // registers and the social preview image cannot be regenerated after a
        // detail in config/seo.php changes.
        __DIR__.'/../app/Console/Commands',
    ])
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'role' => CheckRole::class,
            'force.json' => ForceJsonResponse::class,
            'track.visitor' => TrackVisitor::class,
            'security.headers' => SecurityHeaders::class,
            'user.current' => EnsureUserIsCurrent::class,
        ]);

        $middleware->web(append: [
            ConvertRedirectsToJson::class,
            EnsureUserIsCurrent::class,
            SecurityHeaders::class,
        ]);

        $middleware->api(prepend: [
            ForceJsonResponse::class,
        ]);

        $middleware->api(append: [
            EnsureUserIsCurrent::class,
            SecurityHeaders::class,
        ]);

        $middleware->encryptCookies(except: [
            'jwt_token',
        ]);

        // Behind a load balancer, the request arrives with the proxy's IP. Without
        // this every generated URL and every rate limit bucket is keyed off the
        // proxy, so one school could exhaust another's quota and HTTPS detection
        // fails outright.
        $middleware->trustProxies(
            at: array_filter(explode(',', (string) env('TRUSTED_PROXIES', ''))),
            headers: Request::HEADER_X_FORWARDED_FOR
                | Request::HEADER_X_FORWARDED_HOST
                | Request::HEADER_X_FORWARDED_PORT
                | Request::HEADER_X_FORWARDED_PROTO
                | Request::HEADER_X_FORWARDED_AWS_ELB,
        );

        $middleware->trustHosts(
            at: array_filter(explode(',', (string) env('TRUSTED_HOSTS', ''))),
        );
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->expectsJson(),
        );
    })->create();
