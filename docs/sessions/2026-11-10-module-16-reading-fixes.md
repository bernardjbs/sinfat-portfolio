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
- Post column `max-w-3xl` → `max-w-xl`, then replaced by the post page layout below

### Post page layout (folded in on Bernard's ruling)
- Grid: sticky 11rem left track with a contents list from the post's h2s, 38rem prose column, `pre` and `table` breaking out 12rem to the right
- Body 18px/1.6 Geist (17px under 600px), title 36px/1.15 mono (30px under 600px), h2 22px mono
- Meta is a sentence; the category is its only link (blog index until phase 2)
- New `link` token for prose links and inline code: dark `#3fb950` (7.45:1), light `#1a7f37` (5.08:1); the old accent measured 4.09:1 on dark
- Under 1100px: one column, contents hidden, code full width
- Right grid column is `minmax(10rem, 1fr)`: the prototype's `1fr` overflowed at 1150px (scrollWidth 1175, mutation-tested)

### Convention change
- `project-terminal-aesthetic` records the body-text exception and the `body` token, and its stale Tailwind v3 config section now shows the v4 `@theme` block

### Measured (sinfat.test, post 02)
- Paragraph contrast: 6.15:1 dark, 5.25:1 light (was 4.12:1 dark)
- Characters per line at the `max-w-xl` stage: 73.1 by average character width; was about 100. The 62.5 "rendered line count" figure recorded earlier was wrong: counting line boxes double-counts lines holding inline code
- Post page layout, post 12 at 1440 and 2000px: 73.6 characters a line by height ÷ line height (62.9 to 88.4 per paragraph); prose 608px
- Track right edge left of the first code block at 1440 (488 < 520) and 2000 (768 < 800)
- `scrollWidth` equals the viewport at 390 and at every width tested from 599 to 1200
- Contents link: heading lands 32px below the top edge
- Contrast on post 02: p, li, td 6.15:1 dark, 5.25:1 light; inline code 6.81 dark, 4.77 light; links 7.45 dark, 5.08 light
- `/blog/does-not-exist`: 404 (was 200)

### Tests
- 6 new tests in `tests/Feature/BlogPageControllerTest.php`, each guard mutation-proved
- Total: 80 passed, 3 skipped (220 assertions)

## Files Changed
- `app/Http/Controllers/BlogPageController.php`: new
- `routes/web.php`: `/blog/{slug}` route
- `resources/views/app.blade.php`: optional per-page meta
- `resources/css/app.css`: Geist font, `--font-sans`, `body` and `link` tokens, `.post-*` layout rules
- `resources/js/pages/BlogPost.vue`: body text classes, inline code, page grid, contents list, meta sentence
- `tests/Feature/BlogPageControllerTest.php`: new
- `.claude/skills/project-terminal-aesthetic/SKILL.md`: convention change
- `specs/module-16-reading-fixes.md`: new spec
- `docs/handover.md`: module 16 row, test count, outstanding `BlogService` note

## Outstanding
- No post has a blockquote yet, so the blockquote styling is untested in the browser
- Locally `og:url` reads `http://localhost/...` because local `APP_URL` is `http://localhost`; production is `https://sinfat.com`
