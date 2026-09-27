<?php

namespace App\Support;

use Illuminate\Support\Facades\Config;
use Symfony\Component\HttpFoundation\Cookie;

/**
 * The browser copy of the API token.
 *
 * HttpOnly keeps the token away from injected scripts, Secure keeps it off
 * plaintext connections, and SameSite=Lax blocks it on cross-site form posts.
 */
class JwtCookie
{
    /**
     * Minutes the browser should keep the token for. The token itself expires
     * sooner, so this is only an upper bound on the cookie's lifetime.
     */
    public const LIFETIME_MINUTES = 1440;

    public static function make(string $token): Cookie
    {
        return cookie(
            name: 'jwt_token',
            value: $token,
            minutes: self::LIFETIME_MINUTES,
            path: '/',
            domain: null,
            secure: self::secure(),
            httpOnly: true,
            raw: false,
            sameSite: 'lax',
        );
    }

    /**
     * An expired replacement, so browsers drop any copy they are still holding.
     */
    public static function forget(): Cookie
    {
        return cookie(
            name: 'jwt_token',
            value: null,
            minutes: -2628000,
            path: '/',
            domain: null,
            secure: self::secure(),
            httpOnly: true,
            raw: false,
            sameSite: 'lax',
        );
    }

    /**
     * Mark the cookie Secure whenever the app is served over HTTPS, including
     * behind a proxy that terminates TLS.
     */
    protected static function secure(): bool
    {
        return (bool) Config::get('session.secure') || app()->environment('production');
    }
}
