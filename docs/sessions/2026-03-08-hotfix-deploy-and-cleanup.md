# Session: 2026-03-08 — Hotfix Deploy & Cleanup

## What Happened
Previous session crashed without running the closing routine. Site was down (Vite manifest missing on prod).

## What Was Fixed
- Server `.gitignore` had `/public/build` as active rule — build assets weren't pulled. Fixed on server.
- Server was 5 commits behind — pulled to latest.
- Added `/` → `/blog` redirect in Vue Router (homepage was blank).
- Fixed DatabaseSeeder to use `ADMIN_EMAIL` / `ADMIN_PASSWORD` from `.env` instead of hardcoded `test@example.com`.
- Set prod `APP_ENV=production`, `APP_DEBUG=false`.
- Created admin user on prod matching `.env` credentials.

## Process Improvements
- Merged `MORNING_BRIEF.md` into `SESSION.md` — single source of truth.
- Updated `next-session` and `module-runner` skills to reference SESSION.md only.
- Fixed deploy workflow: removed `sitemap:generate` (Module 10 not built), added `git checkout .gitignore` to prevent server divergence.

## Commits
- `7f97fdf` — fix: add root route redirect to /blog
- `8486c14` — fix: use ADMIN_EMAIL and ADMIN_PASSWORD env values in database seeder
- `985416a` — chore: merge MORNING_BRIEF into SESSION.md; fix deploy workflow
