## Module 17: Series
> 🟢 Opus

### Goal
Let a post belong to a series, show series on the blog, and move the 14 "Building sinfat.com" posts
into one series as parts 1 to 14 with the approved titles and excerpts. Slugs never change, so every
link and the RSS feed keep working. Design, rules and approved copy:
`~/code/software-factory-research/docs/blog-series-design.md` (ruled 2026-11-10). Roadmap steps 2.3 to 2.6.

Two commits for separate review: **2a** (data, API, command, data move; nothing visible changes
until the move runs) and **2b** (the UI).

### Decisions taken here (not in the design doc)
- Series API lives at `/api/series` and `/api/series/{slug}`, so it never collides with `/api/blog/{slug}`.
- A series shows publicly only once it has a published part; otherwise its page is a 404.
- Part count and "last updated" count published parts only.
- Order (one rule, used by the index and `/blog/series`): being written first, then complete; within
  each, by latest published part, newest first. "Most recently finished" is read as the date of the
  last part, so no extra column.
- `--series` without `--part` appends. `--part=N` where N is taken moves that part and every later
  part up by one, as the design accepts.
- On a series post the end-of-post series box replaces today's date-ordered previous/next; standalone
  posts keep today's nav unchanged.
- Over 15 parts, the collapsed list shows the 2 parts either side ("Parts 10 to 14") and links "All N
  parts" to the series page.
- The blog index series section shows on page 1 with no search; searching or paging shows posts only.
- New page titles use "·", like the server meta ("Series · sinfat.com").
- Data move matches posts by slug, not id or date: locally, parts 4 and 5 and parts 7 and 8 were
  published out of order, and three posts share one timestamp.

### Tasks

#### 2a: data, API, command
- [x] Migration: `series` table (`slug` unique, `title`, `pitch` nullable, `status` `writing|complete`
      default `writing`, timestamps)
- [x] Migration: `blog_posts.series_id` and `series_part` (unsigned, nullable), unique on
      (`series_id`, `series_part`); no foreign key constraint (house rule)
- [x] `Series` model (`posts()`, `publishedPosts()`), `BlogPost::series()`, factory states
- [x] `SeriesService`: ordered public list, one series with its parts, in-series previous/next
- [x] `blog:manage series create|update|complete` (`--slug`, `--title`, `--pitch`), and
      `--series=<slug> --part=<n>` on post create and update
- [x] API: posts in `/api/blog` and `/api/blog/{slug}` carry `series` (`slug`, `title`, `status`,
      `part`, `total`) or `null`; the show response adds the series parts list (part, title, slug)
- [x] API: previous/next on a series post stay within the series by part; standalone posts keep date order
- [x] API: `GET /api/series` (slug, title, pitch, status, part count, last updated) in the order above;
      `GET /api/series/{slug}` (series plus parts: number, title, slug, excerpt, date); 404 for an unknown
      or empty series
- [x] Sitemap: `/blog/series` and each public series page
- [x] Data move `php artisan blog:move-building-sinfat`: dry run by default, `--apply` writes in one
      transaction. Creates `building-sinfat` ("Building sinfat.com with AI", the approved pitch,
      status `complete`), sets the 14 posts to parts 1 to 14 with the approved titles and 6 excerpts.
      Refuses unless all 14 slugs exist and each current title and excerpt is either the old or the new
      value. Re-running is a no-op. Never touches slugs, content, published dates or status.
- [x] Feature tests, each guard mutation-proved

#### 2b: UI
- [x] Series mark: Lucide `Layers`, 15px in the `link` colour, 13px in the posts-list tag
- [x] `/blog`: approved intro line; "Series" heading with the mark; at most three series rows (name,
      part count, last updated, pitch); "All series (N)" link; then all posts, each series post tagged
      "Part N of <series>" with the mark
- [x] `/blog/series`: compact rows grouped "Being written" then "Complete"
- [x] `/blog/series/{slug}`: title and pitch, then every part in order (number, title, excerpt, date)
- [x] Post track: mark plus "Part 12 of 14" (complete) or "Part 3" (being written), then the series
      name linking to its page, above the contents list
- [x] End-of-post series box: "<series>, part N[ of M]", previous and next within the series, parts
      list collapsed, windowed past 15 parts
- [x] Router entries and client meta for both series pages
- [x] Server meta for `/blog/series` (description: the blog intro) and `/blog/series/{slug}` (title and
      pitch), registered ahead of `/blog/{slug}`; 404 shell for an unknown series slug; tests
- [x] `project-terminal-aesthetic` and `project-portfolio-context` record the mark, the series pages
      and the `series` table (through `/skills-builder`)

#### Review fixes (2026-11-10)
- [x] Post card meta is a sentence ("15 Mar 2026, 3 minute read. Part 14 of <series>."), no category pill or "AI drafted"
- [x] Card excerpts in Geist at the `body` colour
- [x] "All series (N)" only when there are more than three series

### Acceptance Criteria (fresh output in the report)
- [x] Slug list from `/api/blog` (both pages) byte-identical before and after the data move on the local
      data; local database dumped to the session scratchpad first
- [x] `/feed.xml` still has 14 items
- [x] Previous/next from part 1 and from part 14 never leave the series (part 1 has no previous, part 14 no next)
- [x] A standalone fixture post keeps date-ordered previous/next and shows no series UI
- [x] Ordering rule proved with a fixture of five series (feature test; the index shows three and
      "All series (5)" in the browser, fixture removed afterwards)
- [x] `scrollWidth` equals the viewport at 390 and 1150px on `/blog`, `/blog/series`, a series page
      and a post page
- [x] Post page contrast unchanged from module 16 (p, li, td 6.15 dark, 5.25 light)
- [x] `php artisan test`: baseline 80 passed and 3 skipped, plus the new tests, 0 failed
- [x] `git diff --stat main`
- [x] Report carries the exact production command for the data move

### Production (Bernard, after merge and `just deploy`)
Deploy runs the migrations. Then a dry run, read it, then apply and rebuild the sitemap (deploy
generated it before the series had parts):

```
ssh sinfat "cd /var/www/sinfat && php artisan blog:move-building-sinfat"
ssh sinfat "cd /var/www/sinfat && php artisan blog:move-building-sinfat --apply && php artisan sitemap:generate"
```

The dry run lists each change and writes nothing; locally it listed 35. If any title or excerpt has
drifted from the approved table it refuses and writes nothing.

### Dependencies
Module 16 (merged)

---

*Created: 2026-11-10*
*Status: ✅ Complete (on branch, awaiting merge)*
