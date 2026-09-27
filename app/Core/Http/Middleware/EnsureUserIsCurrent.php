<?php

namespace App\Core\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\JWTGuard;

/**
 * Rejects requests whose credential is no longer current.
 *
 * Two conditions are enforced on every authenticated request:
 *
 *  1. The account must still be active. `AuthController` checks this at login
 *     time, but a user deactivated afterwards would keep browsing on an
 *     existing session or token until it expired.
 *  2. The `token_version` the credential was issued with must still match the
 *     user row. Incrementing it (see `User::revokeTokens()`) after a password
 *     change, role change or deactivation invalidates every outstanding JWT and
 *     web session at once, which signature expiry alone cannot do.
 */
class EnsureUserIsCurrent
{
    public function handle(Request $request, Closure $next): Response
    {
        /** @var User|null $user */
        $user = $request->user();

        if ($user === null) {
            return $next($request);
        }

        if (! $user->is_active) {
            return $this->deny($request, 'আপনার অ্যাকাউন্ট নিষ্ক্রিয় করা হয়েছে।');
        }

        $issuedVersion = $this->issuedTokenVersion($request);

        if ($issuedVersion !== null && $issuedVersion !== (int) $user->token_version) {
            return $this->deny($request, 'সেশনটির মেয়াদ শেষ হয়েছে। আবার লগইন করুন।');
        }

        return $next($request);
    }

    /**
     * The token version the current credential was issued with, or null when
     * the request carries no version (older sessions, which stay valid until
     * they are replaced by a fresh login).
     */
    private function issuedTokenVersion(Request $request): ?int
    {
        if ($request->hasSession()) {
            $fromSession = $request->session()->get('token_version');

            if (is_numeric($fromSession)) {
                return (int) $fromSession;
            }
        }

        // Only the API guard carries a version claim, and asking a JWT guard
        // for a token it cannot find raises a JWTException on web requests.
        if (! $request->is('api/*')) {
            return null;
        }

        // `Auth::guard()` is typed as Guard|StatefulGuard, but only the JWT
        // guard can report its payload, so the guard is narrowed before use.
        $guard = Auth::guard('api');

        if (! $guard instanceof JWTGuard) {
            return null;
        }

        try {
            $version = $guard->payload()->get('token_version');
        } catch (JWTException) {
            return null;
        }

        return is_numeric($version) ? (int) $version : null;
    }

    private function deny(Request $request, string $message): Response
    {
        try {
            Auth::guard('api')->logout();
        } catch (JWTException) {
            // No API token on this request; the web guard below is enough.
        }

        Auth::guard('web')->logout();

        if ($request->hasSession()) {
            $request->session()->invalidate();
            $request->session()->regenerateToken();
        }

        if ($request->expectsJson()) {
            return response()->json(['message' => $message], 403);
        }

        return redirect()->route('login')->withErrors(['email' => $message]);
    }
}
