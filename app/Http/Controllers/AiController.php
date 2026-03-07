<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AiController extends Controller
{
    public function generate(Request $request): JsonResponse
    {
        $request->validate([
            'topic'   => ['required', 'string', 'max:500'],
            'context' => ['nullable', 'string', 'max:2000'],
        ]);

        // Module 6 — will implement SSE streaming via Neuron AI
        return response()->json(['message' => 'AI generation not implemented yet'], 501);
    }
}
