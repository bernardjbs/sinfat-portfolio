<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PlaygroundEnabled
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! config('services.ai.playground_enabled')) {
            return response()->json([
                'error'   => 'playground_offline',
                'message' => 'The playground is offline while the site moves to a new AI provider.',
            ], 503);
        }

        return $next($request);
    }
}
