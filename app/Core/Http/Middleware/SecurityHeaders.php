<?php

namespace App\Core\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeaders
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        foreach ($this->headersFor($request) as $header => $value) {
            if (! $response->headers->has($header)) {
                $response->headers->set($header, $value);
            }
        }

        // Removes the header when something upstream set it, but note that PHP
        // re-adds X-Powered-By itself after the response is built, whenever
        // expose_php is on. That one can only be switched off in php.ini, which
        // public/.user.ini does for CGI/FastCGI hosts.
        $response->headers->remove('X-Powered-By');

        return $response;
    }

    /**
     * @return array<string, string>
     */
    protected function headersFor(Request $request): array
    {
        $headers = [
            'X-Content-Type-Options' => 'nosniff',
            'X-Frame-Options' => 'SAMEORIGIN',
            'Referrer-Policy' => 'strict-origin-when-cross-origin',
            'X-Permitted-Cross-Domain-Policies' => 'none',
            'Cross-Origin-Opener-Policy' => 'same-origin',
            'Cross-Origin-Resource-Policy' => 'same-origin',
            'Content-Security-Policy' => $this->contentSecurityPolicy($request),
        ];

        // HSTS is only honoured over HTTPS, so it is only ever sent over HTTPS.
        if ($request->isSecure() && Config::get('security.hsts')) {
            $headers['Strict-Transport-Security'] = Config::get('security.hsts_max_age', 31536000).'; includeSubDomains';
        }

        return $headers;
    }

    /**
     * The policy allows this app's own assets plus whatever the configured
     * third parties need. Images are wide open because uploads are served
     * straight from the public directory and can be any image type.
     */
    protected function contentSecurityPolicy(Request $request): string
    {
        $extra = (array) Config::get('security.csp', []);
        $isDevelopment = (bool) ($extra['development'] ?? false);

        $directives = [
            "default-src 'self'",
            "base-uri 'self'",
            "form-action 'self'",
            "frame-ancestors 'self'",
            "object-src 'none'",
            // 'unsafe-eval' is required, not incidental. Livewire ships Alpine,
            // and Alpine compiles x-data/x-model/x-show expressions with
            // `new Function`. This app leans on those directives heavily, so a
            // policy without it leaves production with silently dead bindings
            // and forms that submit nothing. It is a far smaller risk than
            // 'unsafe-inline', which is what actually lets injected markup run.
            // Dropping it needs Livewire's CSP build (livewire.csp_safe) plus
            // expressions simple enough for its interpreter to handle.
            "script-src 'self' 'unsafe-eval'",
            "style-src 'self' 'unsafe-inline'",
            "img-src 'self' data: blob:",
            "font-src 'self' data:",
            "connect-src 'self'",
            "frame-src 'self'",
        ];

        $map = [
            'script-src' => 'script_src',
            'style-src' => 'style_src',
            'img-src' => 'img_src',
            'font-src' => 'font_src',
            'connect-src' => 'connect_src',
            'frame-src' => 'frame_src',
        ];

        foreach ($map as $directive => $key) {
            $sources = trim((string) ($extra[$key] ?? ''));

            if ($sources !== '') {
                $directives = array_map(
                    static fn (string $line): string => $line === $directive
                        ? $line.' '.$sources
                        : $line,
                    $directives,
                );
            }
        }

        if ($isDevelopment) {
            // With `npm run dev` the app is served from one origin while every
            // asset comes from the Vite dev server on another, so the styles,
            // fonts and images it serves are all cross-origin and 'self' does
            // not cover them. Scripts and the hot-reload websocket need the
            // same relaxation, and the dev server only speaks plain HTTP, so
            // http and https are allowed here and nowhere else.
            $directives = array_map(
                static fn (string $line): string => match (true) {
                    str_starts_with($line, 'script-src') => "script-src 'self' 'unsafe-eval' 'unsafe-inline' ws: wss: http: https:",
                    str_starts_with($line, 'style-src') => "style-src 'self' 'unsafe-inline' http: https:",
                    str_starts_with($line, 'connect-src') => 'connect-src \'self\' ws: wss: http: https:',
                    str_starts_with($line, 'img-src') => "img-src 'self' data: blob: http: https:",
                    str_starts_with($line, 'font-src') => "font-src 'self' data: http: https:",
                    default => $line,
                },
                $directives,
            );
        }

        $policy = implode('; ', $directives);

        // Upgrading subresources would break the Vite dev server, which only
        // speaks HTTP, so it is reserved for real traffic.
        if (! $request->isSecure() && ! $isDevelopment) {
            $policy .= '; upgrade-insecure-requests';
        }

        return $policy;
    }
}
