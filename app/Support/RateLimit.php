<?php

namespace App\Support;

use Illuminate\Http\Request;

/**
 * Builds rate limiter cache keys.
 *
 * Keys are hashed so that raw email addresses and IP addresses never end up as
 * cache keys, and so keys stay a fixed length regardless of input size.
 */
class RateLimit
{
    /**
     * Key for a limit scoped to the client IP address.
     */
    public static function byIp(Request $request, string $prefix = ''): string
    {
        return self::key($prefix, $request->ip());
    }

    /**
     * Key for a limit scoped to the authenticated user, falling back to the IP.
     */
    public static function byUser(Request $request, string $prefix = ''): string
    {
        return self::key($prefix, $request->user()?->getAuthIdentifier() ?? $request->ip());
    }

    /**
     * Key for a limit scoped to a submitted field plus the client IP.
     *
     * Pairing the field with the IP means one attacker working through many
     * email addresses is still throttled by the per-IP limit, while a shared
     * school network cannot exhaust the per-account limit for everyone.
     */
    public static function byField(Request $request, string $field, string $prefix = ''): string
    {
        $value = $request->input($field);

        if (is_array($value)) {
            $value = implode(',', array_filter($value, 'is_string'));
        }

        $normalized = mb_strtolower(trim((string) $value));

        return self::key($prefix, $normalized.'|'.$request->ip());
    }

    /**
     * Hash the given parts into a single cache key.
     *
     * @param  array<int, string|int|null>  $parts
     */
    protected static function key(string $prefix, string|int|null ...$parts): string
    {
        $fingerprint = implode('|', array_map(
            static fn (string|int|null $part): string => (string) $part,
            $parts,
        ));

        return $prefix === '' ? sha1($fingerprint) : $prefix.':'.sha1($fingerprint);
    }
}
