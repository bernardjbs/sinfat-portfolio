<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreGuestKeyRequest;
use App\Http\Requests\StorePlaygroundGenerateRequest;
use App\Models\AiSession;
use App\Models\GuestUsage;
use App\Services\GuestUsageService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PlaygroundController extends Controller
{
    private const MAX_ATTEMPTS = 3;

    public function __construct(private GuestUsageService $guestUsageService) {}

    public function status(Request $request): JsonResponse
    {
        $key = 'playground:' . $request->ip();
        $remaining = RateLimiter::remaining($key, self::MAX_ATTEMPTS);
        $hasOwnKey = $request->session()->has('guest_api_key');

        return response()->json([
            'remaining'   => $remaining,
            'has_own_key' => $hasOwnKey,
        ]);
    }

    public function generate(StorePlaygroundGenerateRequest $request): StreamedResponse
    {
        $validated = $request->validated();

        $provider = config('services.ai.provider');
        $defaultKey = config("services.{$provider}.key");
        $apiKey = $request->session()->get('guest_api_key', $defaultKey);
        $usedOwnKey = $request->session()->has('guest_api_key');
        $ip = $request->ip();
        $model = config('services.' . config('services.ai.provider') . '.model');

        // Log before streaming — ensures records exist even if stream fails
        $guestUsage = GuestUsage::create([
            'ip_address'   => $ip,
            'topic'        => $validated['topic'],
            'model'        => $model,
            'used_own_key' => $usedOwnKey,
            'status'       => 'streaming',
        ]);

        $aiSession = AiSession::create([
            'identifier' => $ip,
            'type'       => 'guest',
            'topic'      => $validated['topic'],
            'model'      => $model,
            'status'     => 'streaming',
        ]);

        return response()->stream(function () use ($validated, $apiKey, $guestUsage, $aiSession) {
            $this->guestUsageService->streamGeneration(
                topic: $validated['topic'],
                apiKey: $apiKey,
                guestUsage: $guestUsage,
                aiSession: $aiSession,
            );
        }, 200, [
            'Content-Type'      => 'text/event-stream',
            'Cache-Control'     => 'no-cache',
            'X-Accel-Buffering' => 'no',
            'Connection'        => 'keep-alive',
        ]);
    }

    public function setKey(StoreGuestKeyRequest $request): JsonResponse
    {
        $request->session()->put('guest_api_key', $request->input('api_key'));

        return response()->json(['success' => true]);
    }
}
