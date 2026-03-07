<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

class GuestRateLimit
{
    private const MAX_REQUESTS = 3;
    private const TTL_SECONDS = 86400; // 24 hours

    public function handle(Request $request, Closure $next): Response
    {
        // Skip rate limiting if guest provided their own API key
        if ($request->hasSession() && $request->session()->has('guest_api_key')) {
            return $next($request);
        }

        $key = 'guest_usage:' . $request->ip();
        $count = (int) Cache::get($key, 0);

        if ($count >= self::MAX_REQUESTS) {
            return response()->json([
                'error'   => 'limit_reached',
                'message' => 'Free limit reached. Provide your Anthropic API key to continue.',
            ], 429);
        }

        Cache::put($key, $count + 1, self::TTL_SECONDS);

        return $next($request);
    }
}
