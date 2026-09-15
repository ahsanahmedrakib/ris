<?php

namespace App\Core\Http\Middleware;

use App\Models\Visit;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TrackVisitor
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if (! $request->isMethod('GET')) {
            return $response;
        }

        $ip = $request->ip();

        if (! $ip) {
            return $response;
        }

        $alreadyVisitedToday = Visit::query()
            ->where('ip_address', $ip)
            ->whereDate('visited_at', today())
            ->exists();

        if (! $alreadyVisitedToday) {
            Visit::create([
                'visitor_id' => $ip,
                'ip_address' => $ip,
                'user_agent' => $request->userAgent(),
                'url' => $request->fullUrl(),
                'visited_at' => now(),
            ]);
        }

        return $response;
    }
}
