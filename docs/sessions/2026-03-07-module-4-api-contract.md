# Session Note — Module 4: API Contract
**Date:** 2026-03-07

## What Was Done
- Created `BlogController` (public: index, show)
- Created `AdminBlogController` (admin: index, store, show, update, destroy, togglePublish)
- Created `AiController` (stub: generate — returns 501)
- Created `PlaygroundController` (stubs: generate — returns 501, setKey — stores in session)
- Created `BlogPostResource` — conditional field inclusion based on route name
- Created `StoreBlogPostRequest` — validation for create/update
- Created `GuestRateLimit` middleware — 3 requests/day per IP via Cache facade, bypasses if guest has session API key
- Registered all 12 API routes with named routes in `routes/api.php`
- Registered `guest-rate-limit` middleware alias in `bootstrap/app.php`

## Issues Hit
- **Session store not set** — `GuestRateLimit` called `$request->session()` on API routes where Sanctum hadn't attached session middleware (non-stateful requests like curl). Fixed with `$request->hasSession()` guard.
- **Cache vs Redis** — Redis not installed locally. Switched middleware to use `Cache` facade instead of `Redis` directly — works with any cache driver (database locally, redis on prod).

## Files Changed
```
app/Http/Controllers/BlogController.php
app/Http/Controllers/AdminBlogController.php
app/Http/Controllers/AiController.php
app/Http/Controllers/PlaygroundController.php
app/Http/Middleware/GuestRateLimit.php
app/Http/Requests/StoreBlogPostRequest.php
app/Http/Resources/BlogPostResource.php
bootstrap/app.php
routes/api.php
```

## Decisions Made
- **AdminBlogController has real logic, not stubs** — CRUD operations are straightforward enough that stubs would be more work to replace later
- **AiController and PlaygroundController are stubs** — return 501, implemented in Modules 6 and 7
- **Cache facade over Redis facade** — middleware works regardless of cache driver configured per environment

## Next Module
Module 5 — Blog (Admin + Public)
