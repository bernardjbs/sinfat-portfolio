## Module 14 — Blog Polish II
> 🟢 Sonnet

### Goal
Improve blog navigation, discoverability, and content quality. Add sorting/filtering, post-to-post navigation, and clean up em dash overuse across all published posts.

### Tasks

#### Em Dash Reduction
- [x] Audit all 14 published posts for excessive em dash usage
- [x] Replace em dashes with commas, periods, colons, or semicolons where appropriate
- [x] Punctuation changes only — do not rewrite, rephrase, or modify any content
- [x] Use `php artisan blog:manage` for all post updates (not tinker)
- [x] Keep em dashes only where they genuinely add punch or parenthetical clarity

#### Blog Sorting & Search
- [x] Add a search input to the blog index page (filter by title/excerpt)
- [x] Add sort options: newest first (default), oldest first
- [x] Filter and sort client-side from the already-loaded post list
- [x] Maintain terminal aesthetic — minimal, no bloated UI

#### Next/Previous Post Navigation
- [x] Add next/previous navigation buttons at the bottom of each blog post
- [x] Order by `published_at` (same as blog index)
- [x] Show post title in the nav link
- [x] Handle edge cases: first post has no "previous", last post has no "next"
- [x] API: return `next` and `previous` slugs from the show endpoint, or resolve client-side from the post list

### Acceptance Criteria
- [x] All 14 posts reviewed — em dashes reduced to tasteful minimum
- [x] Blog index has a working search input that filters posts by title/excerpt
- [x] Blog index has sort toggle (newest/oldest)
- [x] Blog post page shows next/previous navigation with post titles
- [x] First and last posts handle missing prev/next gracefully
- [x] All existing tests still pass
- [x] No regressions on mobile layout

### Dependencies
Module 12 (complete)

---

*Created: 2026-03-14*
*Status: ✅ Complete*
