# Session: Module 16: Reading Fixes and Honest URLs
**Date:** 2026-11-10

## What Was Done

### Per-post meta and real 404 (server)
- New `BlogPageController` on `GET /blog/{slug}`, registered ahead of the SPA catch-all
- A published post renders the app shell with its own `<title>`, meta description and `og:` tags (`og:type` `article`, `og:url` from `APP_URL`)
- A missing or draft slug renders the same shell with status 404, so the SPA keeps its "post not found" page and crawlers get the real status
- `app.blade.php` takes an optional `$meta`; every other route renders the default shell unchanged

### Reading (CSS)
- Geist (sans) loaded alongside Geist Mono in the one Google Fonts request; `--font-sans` token
- Post paragraphs, list items and blockquotes: Geist, 16px, `leading-relaxed`, new `body` colour token (dark `#8b949e`, light `#656d76`); table cells take `body` too
- Inline code stays Geist Mono and no longer shows literal backticks
- Post column `max-w-3xl` → `max-w-xl`

### Convention change
- `project-terminal-aesthetic` records the body-text exception and the `body` token, and its stale Tailwind v3 config section now shows the v4 `@theme` block

### Measured (sinfat.test, post 02)
- Paragraph contrast: 6.15:1 dark, 5.25:1 light (was 4.12:1 dark)
- Characters per line: 73.1 (average character width) and 62.5 (rendered line count); was about 100
- `/blog/does-not-exist`: 404 (was 200)

### Tests
- 6 new tests in `tests/Feature/BlogPageControllerTest.php`, each guard mutation-proved
- Total: 80 passed, 3 skipped (220 assertions)

## Files Changed
- `app/Http/Controllers/BlogPageController.php`: new
- `routes/web.php`: `/blog/{slug}` route
- `resources/views/app.blade.php`: optional per-page meta
- `resources/css/app.css`: Geist font, `--font-sans`, `body` token
- `resources/js/pages/BlogPost.vue`: body text classes, inline code, column width
- `tests/Feature/BlogPageControllerTest.php`: new
- `.claude/skills/project-terminal-aesthetic/SKILL.md`: convention change
- `specs/module-16-reading-fixes.md`: new spec
- `docs/handover.md`: module 16 row, test count, outstanding `BlogService` note

## Outstanding
- No post has a blockquote yet, so the blockquote styling is untested in the browser
- Locally `og:url` reads `http://localhost/...` because local `APP_URL` is `http://localhost`; production is `https://sinfat.com`
