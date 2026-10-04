<?php

namespace App\Core\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ForceCanonicalHost
{
    /**
     * Paths that must keep answering on the host they were sent to.
     *
     * 'up' is the health check the platform polls to decide whether this
     * instance is alive, and a redirect is a failure to it, so redirecting it
     * would have the platform restart a perfectly healthy instance forever.
     * '.well-known' is where the certificate authority validates a domain, and
     * it has to be served from the domain being issued, not the canonical one.
     *
     * @var list<string>
     */
    protected const EXEMPT_PATHS = ['up', 'up/*', '.well-known', '.well-known/*'];

    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $canonical = $this->canonicalUrl();

        if ($canonical === null || $this->isExempt($request)) {
            return $next($request);
        }

        if ($request->getHost() === $this->canonicalHost($canonical)) {
            return $next($request);
        }

        // The original path and query string are carried over, so a shared link
        // or a webhook pointed at the old host still lands on the same page
        // rather than on the home page.
        $target = $canonical.$request->getRequestUri();

        // A 301 is only defined for GET and HEAD, and a client that follows one
        // for a POST resubmits it as a GET and drops the body. Everything else
        // gets a 308, which keeps both the method and the payload.
        return redirect($target, $request->isMethodSafe() ? 301 : 308);
    }

    /**
     * The configured canonical origin, normalised to a scheme and host with no
     * trailing slash. Null when it is unset or unusable, which disables the
     * redirect rather than guessing at an address to send visitors to.
     */
    protected function canonicalUrl(): ?string
    {
        $configured = trim((string) config('app.canonical_url', ''));

        if ($configured === '') {
            return null;
        }

        $parts = parse_url($configured);

        if (! is_array($parts) || ($parts['host'] ?? '') === '') {
            return null;
        }

        $scheme = strtolower((string) ($parts['scheme'] ?? 'https'));

        return $scheme.'://'.strtolower($parts['host']).(isset($parts['port']) ? ':'.$parts['port'] : '');
    }

    protected function canonicalHost(string $canonicalUrl): string
    {
        return (string) parse_url($canonicalUrl, PHP_URL_HOST);
    }

    protected function isExempt(Request $request): bool
    {
        return $request->is(...self::EXEMPT_PATHS);
    }
}
