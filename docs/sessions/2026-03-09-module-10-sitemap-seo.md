# Module 10 — Sitemap + SEO

**Date:** 2026-03-09
**Status:** ✅ Complete

## What Was Done

Made the site discoverable by search engines — sitemap, robots.txt, and per-page meta tags.

### Backend
- **spatie/laravel-sitemap** installed
- **`GenerateSitemap` artisan command** — `php artisan sitemap:generate` creates `public/sitemap.xml` with all static pages + published blog posts (with priorities and change frequencies)
- **`robots.txt`** updated — allows all crawlers, blocks `/admin` and `/login`, points to sitemap
- **Deploy pipeline** — `php artisan sitemap:generate` added to GitHub Actions workflow so sitemap regenerates on every deploy

### Frontend
- **Blade template** — default `<meta description>` and Open Graph tags (`og:title`, `og:description`, `og:type`, `og:url`)
- **Router meta** — every route has `meta.title` and `meta.description`
- **`afterEach` hook** — updates `document.title`, `meta[name="description"]`, and OG tags on every navigation
- **BlogPost.vue** — watcher updates title and description from loaded post data (so blog posts get their own title/excerpt in search results)

### Files Changed
- `app/Console/Commands/GenerateSitemap.php` (new)
- `public/robots.txt` (updated)
- `.gitignore` (added `/public/sitemap.xml`)
- `.github/workflows/deploy.yml` (added sitemap step)
- `resources/views/app.blade.php` (added meta + OG tags)
- `resources/js/router/index.js` (added meta per route + afterEach hook)
- `resources/js/pages/BlogPost.vue` (added watcher for dynamic title)

### Key Decisions
- `sitemap.xml` is gitignored — generated per environment (local uses `localhost`, prod uses `sinfat.com`)
- Blog post titles set dynamically via Vue watcher, not route meta (because the title depends on fetched data)
- Google Search Console submission deferred until after launch review

## Outstanding Items
- Submit sitemap to Google Search Console after launch
- `og:image` not set — no images on the site yet

## Tests
- 58 passing (no new tests — command is simple, meta tags are frontend-only)

## Next Module
Module 11 — Deploy Pipeline Polish
