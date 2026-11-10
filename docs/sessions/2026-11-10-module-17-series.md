# Session: Module 17: Series
**Date:** 2026-11-10

## What Was Done

### 2a: data, API, command (`a5abeb6`)
- `series` table; `blog_posts.series_id` and `series_part`, nullable, unique as a pair, no foreign key
- `SeriesService`: public list (being written first, then complete, each by latest published part), one series with its parts, in-series previous/next
- `/api/series` and `/api/series/{slug}` (404 for unknown or empty); posts carry `series` (part and published total); a series post's previous/next step by part, standalone posts keep date order
- `blog:manage series create|update|complete`; `--series/--part` on post create and update (append by default, a taken part moves later parts up)
- Sitemap lists `/blog/series` and each public series
- `blog:move-building-sinfat`: dry run unless `--apply`, one transaction, matches by slug, refuses on any drift, no-op on re-run

### 2b: UI
- `/blog`: approved intro, "Series" heading with the Layers mark, at most three series, "All series (N)", "All posts"; posts tagged "Part N of <series>". The series section shows on page 1 with no search
- `/blog/series` grouped "Being written" / "Complete"; `/blog/series/{slug}` lists every part
- Post track: "Part 12 of 14" (complete) or "Part 3" (being written) and the series link; end-of-post series box replaces date previous/next, parts list collapsed, windowed past 15 parts
- Server meta for both series pages, registered ahead of `/blog/{slug}`; 404 shell for an unknown or empty series

### Convention change
- `project-terminal-aesthetic`: series mark, rows, track line and series box
- `project-portfolio-context`: routes, `series` table and columns, store state, `blog:manage` series actions

## Measured (sinfat.test, local data)
- Data move: dry run listed 35 changes and wrote nothing; `--apply` applied 35; re-run "Nothing to do: already applied."
- Slug list from `/api/blog` (both pages): byte-identical before the migrations, after the move, and after the fixtures were removed (sha1 `3c97bc26…`)
- `/feed.xml`: 14 items before and after
- Part 1: no previous, next part 2. Part 14: previous part 13, no next. Part 12: 11 and 13
- Locally the 14 posts are not in part order by date (parts 4/5 and 7/8 swapped, three share a timestamp), which is why the move matches by slug
- Five-series fixture: API order writing-new, writing-old, complete-new, building-sinfat, complete-old; index shows the first three and "All series (5)"
- 17-part fixture, part 9: "Part 9", "Parts 7 to 11", list starts at 7, "All 17 parts" links to the series page; part 1 "Parts 1 to 3", part 17 "Parts 15 to 17"
- Standalone fixture post: no series UI, date-ordered previous/next
- `scrollWidth` equals the viewport at 390 and 1150 on `/blog`, `/blog/series`, the series page and a post page (with and without fixtures)
- Post page contrast unchanged: p, li, td 6.15 dark, 5.25 light; inline code 6.81/4.77; links 7.45/5.08
- Local database dumped before any change: scratchpad `local-before-m17.sql`; fixtures removed afterwards (14 posts, 1 series)

## Tests
- 30 new tests (series API 6, blog API series 5, `blog:manage` series 7, data move 6, sitemap 1, series page meta 5)
- Total: 110 passed, 3 skipped (369 assertions); baseline was 80 passed, 3 skipped
- Mutation-proved: 21 of 22 server mutations red. The "free part" test first survived because it had no later parts to move; it now uses a gap and goes red. "Parts not ordered" survives, see below. 3 UI rules (three-series cap, 15-part window, "of N" only when complete) red in the browser after a rebuild, then restored and rebuilt

## Outstanding
- The explicit `orderBy('series_part')` on a series' parts cannot be proved by a test: MySQL reads through the `(series_id, series_part)` index and returns part order without it. The order stays explicit in code
- A post whose slug is `series` would be shadowed by `/blog/series`; no post has it
- Production data move: Bernard, after deploy (command in the spec)
