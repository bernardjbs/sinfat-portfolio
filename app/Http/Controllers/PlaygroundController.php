<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PlaygroundController extends Controller
{
    public function generate(Request $request): JsonResponse
    {
        $request->validate([
            'topic'   => ['required', 'string', 'max:500'],
            'api_key' => ['nullable', 'string'],
        ]);

        // Module 7 — will implement SSE streaming with guest rate limiting
        return response()->json(['message' => 'Playground not implemented yet'], 501);
    }

    public function setKey(Request $request): JsonResponse
    {
        $request->validate([
            'api_key' => ['required', 'string'],
        ]);

        // Store in session only — never persisted to DB
        $request->session()->put('guest_api_key', $request->input('api_key'));

        return response()->json(['message' => 'API key stored in session']);
    }
}
