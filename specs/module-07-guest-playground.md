## Module 7 — Guest Playground
> 🔴 Opus — rate limiting logic and security review

### Goal
Guests can generate blog content via AI. 3 free generations per day. After limit, prompted for their own Anthropic API key. Keys never stored — session only.

### Tasks
- [x] `GuestRateLimit` middleware
- [x] `PlaygroundController` with SSE endpoint
- [x] Guest key — session storage only
- [x] `Playground.vue` — chat interface with counter
- [x] API key modal — appears after limit reached
- [x] Log guest usage to `guest_usage` table

### Technical Detail

**GuestRateLimit middleware:**
```php
public function handle(Request $request, Closure $next) {
    $key = 'playground:' . $request->ip();

    if (RateLimiter::tooManyAttempts($key, 3)) {
        // Check if guest provided their own key
        if (!$request->session()->has('guest_api_key')) {
            return response()->json([
                'error'   => 'limit_reached',
                'message' => 'Free limit reached. Please provide your Anthropic API key.'
            ], 429);
        }
    }

    RateLimiter::hit($key, 86400); // 24 hour decay
    return $next($request);
}
```

**Guest API key — session only:**
```php
// Store guest key in session — never in DB, never in response
public function setKey(Request $request) {
    $request->validate(['api_key' => 'required|string|starts_with:sk-ant-']);
    $request->session()->put('guest_api_key', $request->api_key);
    return response()->json(['success' => true]);
}

// Use guest key if available, fall back to system key
$apiKey = $request->session()->get('guest_api_key', config('services.anthropic.key'));
```

**Playground.vue layout:**
```
Header: "AI Blog Playground" | counter badge "3 free generations remaining"
Input: text field "What would you like to write about?"
Output: streaming text area — tokens appear as they arrive
Actions: Generate | Copy | Clear
Modal: appears when limit reached — "Enter your Anthropic API key to continue"
```

### Acceptance Criteria
- [x] Guest generates content without auth
- [x] Counter decrements with each generation
- [x] After 3 uses, modal appears prompting for API key
- [x] Guest API key accepted and stored in session only
- [x] Guest API key used for subsequent generations
- [x] Key never appears in API response or frontend state
- [x] Rate limit resets after 24 hours
- [x] All generations logged to `ai_sessions` and `guest_usage`

### Dependencies
Modules 4, 6

---
