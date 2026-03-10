# Module 12 — Blog Polish

**Date:** 2026-03-10
**Status:** ✅ Complete

---

## What Was Done

### Backend
- **Reading time** — calculated from word count (200 wpm, min 1 min), added to `BlogPostResource`
- **RSS feed** — `/feed.xml` endpoint returning RSS 2.0 XML with published posts, category, excerpt

### Frontend
- **Category pill** — small bordered label on blog cards and post header
- **Reading time** — "X min read" on cards and post header
- **Syntax highlighting** — highlight.js with github-dark theme, registered languages: JS, PHP, Bash, JSON, CSS, HTML, XML, SQL
- **Copy link** — button on post footer, copies URL with "copied!" feedback
- **Back to top** — "↑ top" link at post footer, smooth scroll

### Skill
- **Blog writer skill** — `.pi/skills/blog-writer/SKILL.md` with two modes (from summary file or manual input), category mapping, tone guide, quality checklist

### Tests
- 5 new RSS feed tests (valid XML, published only, excludes drafts, includes category, empty state)
- 2 new reading time tests (correct calculation, minimum 1 min)
- **Total: 65 passing + 3 skipped (live AI)**

---

## Files Changed
- `app/Http/Resources/BlogPostResource.php` — reading_time field
- `app/Http/Controllers/FeedController.php` — new RSS controller
- `routes/web.php` — `/feed.xml` route
- `resources/views/app.blade.php` — RSS `<link>` tag
- `resources/js/components/BlogPostCard.vue` — category pill, reading time
- `resources/js/pages/BlogPost.vue` — category pill, reading time, highlight.js, copy link, back to top
- `package.json` — highlight.js dependency
- `.pi/skills/blog-writer/SKILL.md` — new skill
- `tests/Feature/FeedControllerTest.php` — new
- `tests/Feature/BlogControllerTest.php` — 2 new tests
