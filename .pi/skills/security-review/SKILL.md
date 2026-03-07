---
name: security-review
description: Security checklist for sinfat-portfolio. Use before committing any module. Covers auth guards, mass assignment, input validation, XSS, CSRF, draft post leakage, rate limiting, and API response exposure.
---

# Security Review — sinfat-portfolio

Run this checklist before committing any module that touches API routes, controllers, or Vue components. Work through every section relevant to what was built. Flag anything that fails — fix it before committing.

---

## 1. Auth Guards

**Backend — every admin route must be behind `auth:sanctum`:**
```bash
grep -n 'auth:sanctum' routes/api.php
```
Check: does every `/api/admin/*` route sit inside the `auth:sanctum` middleware group? No admin endpoint should be reachable unauthenticated.

**Frontend — every admin page must have `meta: { requiresAuth: true }`:**
```javascript
// router/index.js
{ path: '/admin/...', meta: { requiresAuth: true } }
```
Check: the `beforeEach` guard calls `auth.fetchUser()` if not authenticated, then redirects to `/login`. Confirm no admin route is missing the meta flag.

**Test to confirm:** hit an admin API endpoint without a session token → expect `401`. If not tested, write it.

---

## 2. Mass Assignment Protection

Every model must declare `$fillable` explicitly. No `$guarded = []`.

```php
// ✅ Correct
protected $fillable = ['title', 'slug', 'content', 'status', 'published_at', 'ai_generated', 'ai_model'];

// ❌ Wrong
protected $guarded = [];
```

Check: does `BlogPost::$fillable` list only the columns that should be user-settable? `id`, `created_at`, `updated_at` must NOT be in `$fillable`.

---

## 3. Input Validation

Every POST/PUT/PATCH endpoint must use a `FormRequest`. No inline `$request->validate()` in controllers.

```php
// ✅ Correct — type-hinted FormRequest
public function store(StoreBlogPostRequest $request): JsonResponse

// ❌ Wrong — inline validation
public function store(Request $request): JsonResponse {
    $request->validate([...]);
}
```

Check each `FormRequest`:
- Required fields are marked `required`
- String fields have `max:` limits
- `status` field uses `in:draft,published` rule
- Boolean fields use `boolean` rule
- Nullable fields are marked `nullable`, not omitted

---

## 4. XSS

**Markdown rendering (backend):** markdown is rendered to HTML by `Str::markdown()` on the server. Laravel's CommonMark implementation escapes raw HTML by default — confirm this is the case and no `html_input: 'allow'` option is passed.

**`v-html` in Vue (frontend):** blog post content uses `v-html` to display server-rendered HTML. This is acceptable ONLY because:
- Content is rendered server-side by Laravel (trusted source)
- The admin is the only person who can create content (authenticated)
- Public users cannot inject content

Document this in any component that uses `v-html`:
```vue
<!-- Content is server-rendered markdown from Laravel — safe to use v-html -->
<div v-html="post.content" />
```

**Never use `v-html` on user-supplied strings** that haven't been server-rendered (e.g. search inputs, URL params, form fields displayed back to the user).

---

## 5. CSRF Protection

All non-GET API requests from Vue must include the XSRF-TOKEN header.

```javascript
// In Pinia store actions (admin mutations)
const res = await fetch('/api/admin/blog', {
  method: 'POST',
  headers: {
    'Content-Type': 'application/json',
    'X-XSRF-TOKEN': getCsrfToken(),
  },
  body: JSON.stringify(payload),
})

function getCsrfToken() {
  return decodeURIComponent(document.cookie.match(/XSRF-TOKEN=([^;]+)/)?.[1] ?? '')
}
```

Check: every `fetch` call that does POST, PUT, PATCH, DELETE includes this header. GET requests do not need it.

Sanctum handles CSRF validation automatically for stateful SPA requests — but only if the header is present.

---

## 6. Draft Post Leakage

Draft posts must never appear on public routes.

**Backend:** `BlogController::index` and `show` must use the `published()` scope:
```php
BlogPost::published()->...
```

The `published` scope requires BOTH `status = 'published'` AND `published_at IS NOT NULL`. Confirm both conditions are enforced in `BlogPost::scopePublished()`.

**Test to confirm:** create a draft post, hit `/api/blog` and `/api/blog/{slug}` → expect draft to be absent / 404. If not tested, write it.

---

## 7. API Response Exposure

Sensitive fields must not leak to public endpoints. Check `BlogPostResource`:

| Field | Public listing | Public show | Admin listing | Admin show |
|-------|---------------|-------------|---------------|------------|
| `status` | ❌ hidden | ❌ hidden | ✅ visible | ✅ visible |
| `ai_model` | ❌ hidden | ❌ hidden | ✅ visible | ✅ visible |
| `content` | ❌ hidden | ✅ visible (rendered) | ❌ hidden | ✅ visible (raw) |

Use `$this->when(condition, value)` — never include sensitive fields unconditionally.

---

## 8. Rate Limiting (Playground Only)

The `guest-rate-limit` middleware applies to `/api/playground/*` routes only. Confirm it is NOT applied to public blog routes (no rate limiting needed there) and is NOT missing from playground routes.

```php
// ✅ Correct placement
Route::middleware('guest-rate-limit')->prefix('playground')->group(...)
```

---

## 9. API Key Handling (Playground / Module 7)

Guest API keys must be stored in session only — never in the database, never in localStorage.

```php
// ✅ Store in session
session(['guest_api_key' => $key]);

// ❌ Never persist to DB
GuestUsage::create(['api_key' => $key]);

// ❌ Never expose in API response
return response()->json(['key' => $key]);
```

---

## Pre-Commit Sign-Off

Before committing, confirm for this module:

```
[ ] Every admin API route is behind auth:sanctum
[ ] Every admin Vue route has meta: { requiresAuth: true }
[ ] All models use explicit $fillable (no $guarded = [])
[ ] All POST/PUT/PATCH use FormRequest classes
[ ] Markdown rendering uses default CommonMark (no html_input: allow)
[ ] v-html is only used on server-rendered content
[ ] All non-GET fetch calls include X-XSRF-TOKEN header
[ ] Draft posts cannot be accessed via public routes (tested)
[ ] Sensitive fields hidden from public API responses (tested)
```

Any unchecked item = do not commit.
