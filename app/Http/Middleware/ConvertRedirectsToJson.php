<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ConvertRedirectsToJson
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if (! $request->expectsJson() || ! $response instanceof RedirectResponse) {
            return $response;
        }

        if (! str_starts_with($request->path(), 'admin/')) {
            return $response;
        }

        $session = $request->session();
        $hasError = $session->has('error');
        $message = $session->get('success') ?: $session->get('error');

        return new JsonResponse([
            'message' => $message ?: ($hasError ? 'অপারেশন সম্পন্ন হয়নি।' : 'অপারেশন সম্পন্ন হয়েছে।'),
            'type' => $hasError ? 'error' : 'success',
            'redirect' => $response->getTargetUrl(),
        ], $hasError ? 422 : 200);
    }
}
