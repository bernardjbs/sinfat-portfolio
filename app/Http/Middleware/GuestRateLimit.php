<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Symfony\Component\HttpFoundation\Response;

class GuestRateLimit
{
    private const MAX_ATTEMPTS = 3;
    private const DECAY_SECONDS = 86400; // 24 hours

    public function handle(Request $request, Closure $next): Response
    {
        $key = 'playground:' . $request->ip();

        if (RateLimiter::tooManyAttempts($key, self::MAX_ATTEMPTS)) {
            // Allow through if guest has provided their own key
            if ($request->session()->has('guest_api_key')) {
                return $next($request);
            }

            return response()->json([
                'error'   => 'limit_reached',
                'message' => 'You have used all 3 free generations. Please provide your Anthropic API key to continue.',
                'remaining' => 0,
            ], 429);
        }

        // Only hit the limiter on the generate endpoint, not on setKey
        if ($request->routeIs('api.playground.generate')) {
            RateLimiter::hit($key, self::DECAY_SECONDS);
        }

        $response = $next($request);

        $remaining = RateLimiter::remaining($key, self::MAX_ATTEMPTS);
        $response->headers->set('X-RateLimit-Remaining', $remaining);

        return $response;
    }
}
