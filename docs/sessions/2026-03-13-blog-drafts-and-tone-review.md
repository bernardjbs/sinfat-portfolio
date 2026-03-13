# Session: 2026-03-13 (evening) — Blog Drafts 11–14 & Tone Review

## What Was Done

### Blog Posts Drafted
- Posts 11–14 created in local DB as drafts
  - 11: SEO for a Single-Page App with Laravel
  - 12: From GitHub Actions to just deploy — Simplifying Production Deploys
  - 13: When Your AI Session Crashes Mid-Module
  - 14: The Repo Is the Portfolio — Why I Kept It Private Until It Was Done

### Blog Posts Published on Prod
- Posts 6–9 were already on prod (confirmed)
- Post 10 was missing from prod — created and published
- Sitemap regenerated on prod

### Tone Review — All 14 Posts
- Post 10: rewrote — was speaking about design like a consultant, now admits "I'm not a designer"
- Post 11: already rewritten earlier — "I don't know SEO" framing
- Post 7: softened authoritative "principle" statements
- Post 12: replaced polished "What I Learned" with honest closing, fixed Redis explanation for clarity, credited Bernard for finding GitHub Models
- Post 13: rewrote git-rule and documentation-checklist sections for readability, added Diablo analogy, fixed SESSION.md explanation ambiguity
- Post 14: rewrote from instructional tone to personal journey

### Blog Writer Skill Updated
- Added honest tone rules: admit uncertainty, say when AI carried you, sound like a person
- Added em dash rule: don't overuse, prefer periods/commas/colons
- Replaced tinker workflow with `blog:manage` command instructions

### Blog Manage Command
- Created `php artisan blog:manage` with create/update/export actions
- Safety checks: rejects content under 100 bytes, rejects if new content less than half original
- Added `--force` flag for non-interactive use
- Motivation: tinker heredoc wiped Post 10 content due to unassigned variable

### Heading Anchors
- Replaced `Str::markdown()` with CommonMark + HeadingPermalinkExtension in BlogPostResource
- Every blog heading now gets an `id` attribute for anchor linking
- Added scroll-to-hash in BlogPost.vue (waits for content to render, then scrolls)
- Post 10 links to Post 12's swap section via anchor

## Decisions Made
- Use `blog:manage` command instead of tinker for all post operations
- Em dashes are an AI tell — limit to one or two per post
- Posts should never sound authoritative on topics the author isn't expert in
- `--force` flag skips confirmation but keeps all safety validations

## Outstanding Items
- All 14 posts published on sinfat.com
- Em dash pass across all 14 posts (deferred)
