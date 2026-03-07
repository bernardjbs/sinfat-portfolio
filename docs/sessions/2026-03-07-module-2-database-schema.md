# Session Note — Module 2: Database Schema
**Date:** 2026-03-07

## What Was Done
- Created migrations for `blog_posts` and `ai_sessions`
- Created `BlogPost` and `AiSession` Eloquent models
- Created `AdminSeeder` — single admin user seeded from `.env`
- Ran migrations locally, merged and pushed (prod migrations via GitHub Actions)

## Decisions Made
- **`guest_usage` table dropped** — Redis owns rate limiting entirely; DB persistence unnecessary for a portfolio site
- **`category` column added to `blog_posts`** — nullable varchar(100), simple single-category approach over a full categories table
- **No foreign key constraints** — `unsignedBigInteger` for any foreign-style column, no `foreignId()->constrained()` anywhere in this project
- **`/projects` is static** — hardcoded in Vue, no DB table
- **`/contact` is links only** — GitHub, LinkedIn, email mailto — no form, no DB table

## Files Changed
```
app/Models/BlogPost.php
app/Models/AiSession.php
database/migrations/2026_03_07_082755_create_blog_posts_table.php
database/migrations/2026_03_07_082755_create_ai_sessions_table.php
database/seeders/AdminSeeder.php
```

## Skills Updated
- `laravel-conventions` — added Migration Conventions section (no foreignId, use unsignedBigInteger)
- `module-runner` — added model check to Orient step (prompt user if active model mismatches spec)
- `portfolio-context` — annotated /projects (static) and /contact (links only)

## Outstanding
- Prod admin seeder — SSH in and run `php artisan db:seed --class=AdminSeeder` after deploy
- Admin login not verified — deferred to Module 3 (Authentication)

## Next Module
Module 3 — Authentication
