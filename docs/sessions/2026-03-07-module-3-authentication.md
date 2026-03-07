# Session Note — Module 3: Authentication
**Date:** 2026-03-07

## What Was Done
- Created `AdminAuthController` — show(), login(), logout(), me()
- Created `routes/web.php` — auth routes + SPA catch-all
- Created `routes/api.php` — admin group scaffold (empty, ready for Module 5+)
- Created `resources/views/app.blade.php` — SPA entry point
- Installed Vue 3, Vue Router, Pinia, lucide-vue-next, @vitejs/plugin-vue
- Configured Tailwind v4 colour tokens + Geist Mono in `app.css`
- Created `App.vue`, `router/index.js`, `stores/auth.js`
- Created `pages/Login.vue` — terminal aesthetic, session auth
- Created `pages/admin/Dashboard.vue` — placeholder, logout button
- Installed `laravel/sanctum` — required for stateful SPA session auth
- Published Sanctum config, ran `personal_access_tokens` migration
- Configured `$middleware->statefulApi()` in `bootstrap/app.php`
- Added `SANCTUM_STATEFUL_DOMAINS=sinfat.test` to `.env`

## Issues Hit
- **Vite 7 + @vitejs/plugin-vue** — needed `@vitejs/plugin-vue@^6` for Vite 7 support
- **CSRF token** — cookie approach works fine; meta tag approach broke JSON response
- **Auth persistence on refresh** — Pinia resets on page load; fixed with async `fetchUser()` in router guard
- **Sanctum not installed** — `statefulApi()` blew up; fixed by installing `laravel/sanctum`

## Files Changed
```
app/Http/Controllers/AdminAuthController.php
bootstrap/app.php
composer.json / composer.lock
config/sanctum.php
database/migrations/..._create_personal_access_tokens_table.php
resources/css/app.css
resources/js/App.vue
resources/js/app.js
resources/js/pages/Login.vue
resources/js/pages/admin/Dashboard.vue
resources/js/router/index.js
resources/js/stores/auth.js
resources/views/app.blade.php
routes/api.php
routes/web.php
tests/Feature/ExampleTest.php
vite.config.js
package.json / package-lock.json
```

## Conventions Established
- Tailwind v4 tokens defined in `app.css` @theme block (not tailwind.config.js)
- CSRF token read from XSRF-TOKEN cookie (not meta tag)
- Router guard calls `fetchUser()` on refresh to restore auth state from session
- `statefulApi()` + `SANCTUM_STATEFUL_DOMAINS` required for Sanctum SPA auth

## Outstanding
- Prod: add `SANCTUM_STATEFUL_DOMAINS=sinfat.com` to server `.env` after deploy
- Prod: run `php artisan migrate` (personal_access_tokens table) — handled by GitHub Actions

## Next Module
Module 4 — API Contract
