<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAiGenerateRequest;
use App\Services\AiService;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AiController extends Controller
{
    public function __construct(private AiService $aiService) {}

    public function generate(StoreAiGenerateRequest $request): StreamedResponse
    {
        $validated = $request->validated();

        return response()->stream(function () use ($validated, $request) {
            $this->aiService->streamBlogGeneration(
                topic: $validated['topic'],
                context: $validated['context'] ?? null,
                identifier: $request->user()->email,
                type: 'admin',
            );
        }, 200, [
            'Content-Type'      => 'text/event-stream',
            'Cache-Control'     => 'no-cache',
            'X-Accel-Buffering' => 'no',
            'Connection'        => 'keep-alive',
        ]);
    }
}
