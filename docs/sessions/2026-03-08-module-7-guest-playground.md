# Module 7 — Guest Playground

**Date:** 2026-03-08
**Status:** ✅ Complete

## What Was Done

Built the guest AI playground — unauthenticated users can generate blog content via SSE streaming with a 3-per-day free limit. After the limit, they're prompted to enter their own Anthropic API key (stored in session only, never persisted).

## Files Changed / Created

### Backend
- `app/Agents/GuestBlogWriterAgent.php` — Neuron AI agent that accepts a custom API key
- `app/Http/Controllers/PlaygroundController.php` — SSE `generate()`, `setKey()`, and `status()` endpoints
- `app/Http/Middleware/GuestRateLimit.php` — switched from Cache to RateLimiter facade, added `X-RateLimit-Remaining` header
- `app/Http/Requests/StoreGuestKeyRequest.php` — validates `sk-ant-` prefix, min:20
- `app/Http/Requests/StorePlaygroundGenerateRequest.php` — topic validation
- `app/Models/GuestUsage.php` — tracks guest generations
- `app/Services/GuestUsageService.php` — streaming logic with status tracking
- `database/migrations/2026_03_08_123451_create_guest_usage_table.php`
- `routes/api.php` — session middleware on playground routes, status endpoint outside rate limit
- `bootstrap/app.php` — CSRF exclusion for `api/playground/*`

### Frontend
- `resources/js/pages/Playground.vue` — full playground UI with streaming output, counter, copy/clear
- `resources/js/components/ApiKeyModal.vue` — modal for entering API key after limit reached
- `resources/js/stores/playground.js` — Pinia store managing streaming, rate limit, key state
- `resources/js/router/index.js` — added `/playground` route

### Tests
- `tests/Feature/GuestRateLimitTest.php` — 8 tests (rate limiting, bypass with key, validation)
- `tests/Feature/PlaygroundControllerTest.php` — 10 tests (key validation, streaming, logging, security)

## Post-Build Fixes

1. **CSRF token rotation breaking second request** — Sanctum's `EnsureFrontendRequestsAreStateful` includes `VerifyCsrfToken` for stateful domains. After the first SSE stream, the token rotated and the second request got a 419 rendered as HTML. Fixed by excluding `api/playground/*` from CSRF verification — these are public unauthenticated endpoints.
2. **Counter resetting on page refresh** — Added `GET /api/playground/status` endpoint returning server-side remaining count from RateLimiter. Status route placed outside rate-limit middleware so it's always accessible. Frontend fetches on mount.
3. **Copy button not working** — `navigator.clipboard.writeText` requires a user gesture context. Routing through an async Pinia action broke the gesture chain. Moved clipboard write directly into the component click handler.

## Key Decisions

1. **Session middleware without CSRF** — playground routes use `EncryptCookies + AddQueuedCookies + StartSession` directly, with CSRF excluded in `bootstrap/app.php`
2. **Logging before streaming** — `guest_usage` and `ai_sessions` records are created in the controller before the stream callback, ensuring they exist even if streaming fails
3. **RateLimiter over Cache** — switched from raw Cache to Laravel's `RateLimiter` facade for cleaner API and built-in decay handling
4. **Status endpoint outside rate limit** — `GET /api/playground/status` is always accessible so the frontend can show the real counter on page load

## Test Count
58 passing (up from 44)

## Outstanding Items
- Prod needs `AI_PROVIDER` + API key in `.env` for playground to work on sinfat.com
- Local testing requires `brew services start ollama`

## Next Module
Module 8 — Frontend SPA Foundation
