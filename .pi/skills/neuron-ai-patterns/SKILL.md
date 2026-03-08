---
name: neuron-ai-patterns
description: Neuron AI implementation patterns for sinfat-portfolio. Use when building any AI feature — agent class structure, SSE streaming with Nginx buffering fix, guest API key session-only storage, rate limiting middleware, and LLM security rules.
---

# Neuron AI Patterns — sinfat-portfolio

Use this skill when building any AI-related code in this project. Covers Neuron AI agent setup, SSE streaming, guest API key handling, and the security model for AI features.

---

## Package

```bash
composer require neuron-core/neuron-ai
```
> Note: `inspector-apm/neuron-ai` is abandoned. Use `neuron-core/neuron-ai` instead.

**Config in `.env`:**
```
ANTHROPIC_API_KEY=sk-ant-...
ANTHROPIC_MODEL=claude-sonnet-4-5
```

**Config in `config/services.php`:**
```php
'anthropic' => [
    'key'   => env('ANTHROPIC_API_KEY'),
    'model' => env('ANTHROPIC_MODEL', 'claude-sonnet-4-5'),
],
```

---

## Agent Definition Pattern

All Neuron AI agents live in `app/Agents/`. One class per agent persona.

```php
<?php

namespace App\Agents;

use NeuronAI\Agent;
use NeuronAI\Providers\Anthropic;

class BlogWriterAgent extends Agent
{
    protected function provider(): Anthropic
    {
        return new Anthropic(
            apiKey: config('services.anthropic.key'),
            model: config('services.anthropic.model'),
        );
    }

    protected function instructions(): string
    {
        return <<<PROMPT
        You are an expert technical blog writer specialising in Laravel, Vue 3, and AI development.
        Write clear, practical, developer-focused content with real code examples.
        Use markdown formatting with appropriate headings, code blocks, and bullet points.
        Be opinionated and specific — avoid generic advice.
        Target audience: experienced developers who want depth, not hand-holding.
        Length: 600–1200 words unless instructed otherwise.
        PROMPT;
    }
}
```

**Guest variant** — uses the guest's API key if provided:

```php
class GuestBlogWriterAgent extends Agent
{
    public function __construct(private string $apiKey) {}

    protected function provider(): Anthropic
    {
        return new Anthropic(
            apiKey: $this->apiKey,  // guest's key — never stored
            model: config('services.anthropic.model'),
        );
    }

    protected function instructions(): string
    {
        return "You are a creative writing assistant. Help the user write clear, engaging content on any topic they choose. Use markdown formatting.";
    }
}
```

---

## SSE Streaming Pattern (Laravel Controller)

Both admin and guest endpoints use the same SSE pattern. The key difference is which agent and API key is used.

```php
public function generate(Request $request): StreamedResponse
{
    $validated = $request->validate([
        'topic'   => ['required', 'string', 'max:500'],
        'context' => ['nullable', 'string', 'max:1000'],
    ]);

    return response()->stream(function () use ($validated) {
        $agent = new BlogWriterAgent();
        $prompt = "Write a blog post about: {$validated['topic']}";

        if (!empty($validated['context'])) {
            $prompt .= "\n\nAdditional context: {$validated['context']}";
        }

        try {
            $agent->stream($prompt)
                ->each(function ($chunk) {
                    $text = $chunk->delta->text ?? '';
                    if ($text !== '') {
                        echo "data: " . json_encode(['text' => $text]) . "\n\n";
                        ob_flush();
                        flush();
                    }
                });

            // Signal completion
            echo "data: [DONE]\n\n";
            ob_flush();
            flush();

        } catch (\Exception $e) {
            echo "data: " . json_encode(['error' => 'Generation failed']) . "\n\n";
            ob_flush();
            flush();
        }

    }, 200, [
        'Content-Type'      => 'text/event-stream',
        'Cache-Control'     => 'no-cache',
        'X-Accel-Buffering' => 'no',  // CRITICAL: disables Nginx buffering
        'Connection'        => 'keep-alive',
    ]);
}
```

**`X-Accel-Buffering: no` is not optional** — without it, Nginx buffers the entire response and the user sees nothing until generation is complete. This kills the streaming effect.

---

## Guest API Key Pattern

The guest playground allows users to bring their own Anthropic API key after the free limit is reached. This key must NEVER be stored in the database or sent to the frontend.

**Store key (session only):**
```php
// PlaygroundController@setKey
public function setKey(Request $request): JsonResponse
{
    $request->validate([
        'api_key' => ['required', 'string', 'starts_with:sk-ant-', 'min:20'],
    ]);

    // Store in server-side session — never echoed back
    $request->session()->put('guest_api_key', $request->input('api_key'));

    return response()->json(['success' => true]);
    // Key is NOT in the response — Vue never sees the key value
}
```

**Use key in generation:**
```php
// PlaygroundController@generate
public function generate(Request $request): StreamedResponse
{
    $validated = $request->validate([
        'topic' => ['required', 'string', 'max:500'],
    ]);

    // Use guest key if available, fall back to system key
    $apiKey = $request->session()->get('guest_api_key', config('services.anthropic.key'));
    $agent = new GuestBlogWriterAgent($apiKey);

    // ... same SSE streaming pattern
}
```

**Security rules:**
- Guest key stored in session only — `$request->session()->put('guest_api_key', ...)`
- Never written to `guest_usage` table or `ai_sessions` table
- Never included in any JSON response
- Session expires normally — key gone when session ends
- Validate format before storing: must start with `sk-ant-`

---

## Rate Limiting (GuestRateLimit Middleware)

```php
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Symfony\Component\HttpFoundation\Response;

class GuestRateLimit
{
    public function handle(Request $request, Closure $next): Response
    {
        $key = 'playground:' . $request->ip();

        if (RateLimiter::tooManyAttempts($key, maxAttempts: 3)) {
            // Allow through if guest has provided their own key
            if ($request->session()->has('guest_api_key')) {
                return $next($request);
            }

            return response()->json([
                'error'   => 'limit_reached',
                'message' => 'You have used all 3 free generations. Please provide your Anthropic API key to continue.',
            ], 429);
        }

        RateLimiter::hit($key, decaySeconds: 86400); // 24-hour window
        return $next($request);
    }
}
```

---

## Session Logging (AiSession Model)

Every generation attempt — admin or guest — is logged for cost monitoring:

```php
// In AiController / PlaygroundController, before streaming:
$session = AiSession::create([
    'identifier' => $request->ip(),
    'type'       => 'guest',  // or 'admin'
    'topic'      => $validated['topic'],
    'model'      => config('services.anthropic.model'),
    'status'     => 'streaming',
]);

// After streaming completes (approximate — exact token count from Neuron AI if available):
$session->update(['status' => 'completed', 'tokens_used' => $tokenCount ?? null]);
```

---

## Security Principles for AI Features

These map directly to the LLM security patterns documented in SUMMARY.md:

1. **System prompt is not a security boundary** — it shapes behaviour, it doesn't enforce access
2. **Enforce at the handler** — rate limiting in middleware, not in the prompt
3. **Keys never touch the frontend** — guest key stored server-side only, never in response
4. **Validate before use** — guest key format validated before session storage
5. **Log everything** — `ai_sessions` table captures all generation attempts for monitoring

---

## What NOT to Do

- ❌ Do NOT store guest API keys in the database
- ❌ Do NOT echo the guest API key in any JSON response
- ❌ Do NOT pass `api_key` as a request parameter that gets forwarded to Anthropic directly from the client (always server-side)
- ❌ Do NOT skip `X-Accel-Buffering: no` on Nginx — streaming won't work
- ❌ Do NOT forget to call `ob_flush()` and `flush()` after every `echo` in a stream
- ❌ Do NOT use WebSockets — SSE is sufficient for one-way AI streaming
