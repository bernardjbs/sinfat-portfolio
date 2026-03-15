# Session: Module 14 — Blog Polish II
**Date:** 2026-03-14

## What Was Done

### Search & Sort (server-side)
- Added `?search=` query param to `BlogController@index` — filters by title or excerpt using `LIKE`
- Added `?sort=` query param — accepts `newest` (default) or `oldest`
- Pagination links preserve search/sort params via `appends()`
- Blog.vue: search input with 300ms debounce, sort toggle button
- blog.js store: passes search/sort state to API calls

### Next/Previous Post Navigation
- `BlogController@show` now returns `next` and `previous` post (slug + title) via `additional()`
- Queries use `published()` scope — drafts excluded from navigation
- Edge cases handled: first post has no previous, last post has no next
- BlogPost.vue: nav section above the footer with post titles and hover states

### Em Dash Reduction
- Audited all 14 published posts
- Reduced from 192 em dashes to 24 across all posts
- Punctuation changes only — no content was rewritten
- Remaining em dashes are justified: parenthetical pairs, code blocks, effective emphasis
- Updated via `php artisan blog:manage update`

### Tests
- 9 new tests: search by title, search by excerpt, empty search, sort oldest, sort default, next/previous, first post edge, last post edge, draft exclusion
- Total: 74 passed, 3 skipped (197 assertions)

## Files Changed
- `app/Http/Controllers/BlogController.php` — search, sort, next/previous
- `resources/js/pages/Blog.vue` — search input, sort toggle
- `resources/js/pages/BlogPost.vue` — next/previous nav
- `resources/js/stores/blog.js` — search/sort state, next/previous handling
- `tests/Feature/BlogControllerTest.php` — 9 new tests
- `specs/module-14-blog-polish-2.md` — new spec
- `.pi/SESSION.md` — module 14 added to status table
- Database: 13 posts updated (em dash reduction)

### Post-Module Fixes

**Table rendering** — Blog posts with markdown tables (posts 2 and 3) rendered tables in the admin preview (md-editor-v3) but not on the public blog. `CommonMarkCoreExtension` doesn't include GFM table support. Fix: registered `TableExtension` and added prose table styling (th/td borders, padding, colours).

**Slug consistency** — Posts 1–3 had slugs without number prefixes (created before the convention). Updated to `01-`, `02-`, `03-` prefixes to match posts 4–14.

### Production Deploy
- Code deployed via `just deploy`
- Slugs fixed for posts 1–3 on prod
- Em dash updates applied to all 13 posts on prod via SCP + `blog:manage update`
- Sitemap regenerated, cache cleared
- Table fix deployed in a follow-up commit

## Outstanding Items
- All outstanding items from SESSION.md unchanged
