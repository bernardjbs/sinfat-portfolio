# Module 11 — Deploy Pipeline Polish

**Date:** 2026-03-10
**Status:** ✅ Complete

---

## What Was Done

### Fixes
1. **Downgraded `spatie/laravel-sitemap` from `^8.0` to `^7.4`** — v8 requires PHP 8.4, server runs 8.3.
2. **Pinned `composer config platform.php 8.3.30`** — ensures `composer.lock` always resolves packages compatible with the server's PHP version. This was the root cause of `composer install --no-dev` failing silently on prod.
3. **Installed `php8.3-redis` on prod** — Redis server was running but the PHP extension was missing. Switching session/cache to Redis caused a 500 until the extension was installed.
4. **Fixed prod `.env`** — `APP_URL` was `http://localhost` (now `https://sinfat.com`), session/cache/queue drivers switched from `database` to `redis`.

### Documentation
- `docs/production-env.md` — all required `.env` keys with explanations
- `docs/deploy-guide.md` — auto-deploy flow, manual fallback, rollback, pre-deploy checklist

### Verification
- Push to main → GitHub Actions → site updated within 80 seconds ✅
- Sitemap generated on deploy ✅
- Config/route/view caching working ✅
- Redis connected for session, cache, queue ✅

---

## Files Changed
- `composer.json` — sitemap version + platform PHP pin
- `composer.lock` — regenerated for PHP 8.3 compat
- `docs/production-env.md` — new
- `docs/deploy-guide.md` — new
- `docs/sessions/2026-03-10-module-11-deploy-pipeline.md` — this file

## Server Changes (not in repo)
- Installed `php8.3-redis` extension
- Updated `/var/www/sinfat/.env` (APP_URL, CACHE_DRIVER, SESSION_DRIVER, QUEUE_CONNECTION)
- Ran `php artisan config:cache`

---

## Outstanding Items
- Prod `.env` still needs `AI_PROVIDER` + `ANTHROPIC_API_KEY` before AI features work on sinfat.com
- Email `bernard@sinfat.com` mailbox not configured
- Football Analytics project description is placeholder
- `og:image` not set (no images on site yet)
- Light mode colour tuning could be improved
- Submit sitemap to Google Search Console

---

## Lessons Learned
- Always pin `platform.php` in `composer.json` when local PHP > server PHP
- Redis server ≠ PHP Redis extension — both are required
- `composer install --no-dev` fails silently in GitHub Actions SSH if lock file has incompatible packages
