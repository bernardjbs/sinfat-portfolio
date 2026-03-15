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

## Outstanding Items
- Em dash changes are in the local database — need to run on production after deploy
- All other outstanding items from SESSION.md unchanged

## Next
- Deploy to production
- Run em dash updates on prod (export files, then update via artisan)
- Step 8–10: Update SESSION.md, verify acceptance criteria, sign off
