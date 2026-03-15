## Module 14 — Blog Polish II
> 🟢 Sonnet

### Goal
Improve blog navigation, discoverability, and content quality. Add sorting/filtering, post-to-post navigation, and clean up em dash overuse across all published posts.

### Tasks

#### Em Dash Reduction
- [ ] Audit all 14 published posts for excessive em dash usage
- [ ] Replace em dashes with commas, periods, colons, or semicolons where appropriate
- [ ] Punctuation changes only — do not rewrite, rephrase, or modify any content
- [ ] Use `php artisan blog:manage` for all post updates (not tinker)
- [ ] Keep em dashes only where they genuinely add punch or parenthetical clarity

#### Blog Sorting & Search
- [ ] Add a search input to the blog index page (filter by title/excerpt)
- [ ] Add sort options: newest first (default), oldest first
- [ ] Filter and sort client-side from the already-loaded post list
- [ ] Maintain terminal aesthetic — minimal, no bloated UI

#### Next/Previous Post Navigation
- [ ] Add next/previous navigation buttons at the bottom of each blog post
- [ ] Order by `published_at` (same as blog index)
- [ ] Show post title in the nav link
- [ ] Handle edge cases: first post has no "previous", last post has no "next"
- [ ] API: return `next` and `previous` slugs from the show endpoint, or resolve client-side from the post list

### Acceptance Criteria
- [ ] All 14 posts reviewed — em dashes reduced to tasteful minimum
- [ ] Blog index has a working search input that filters posts by title/excerpt
- [ ] Blog index has sort toggle (newest/oldest)
- [ ] Blog post page shows next/previous navigation with post titles
- [ ] First and last posts handle missing prev/next gracefully
- [ ] All existing tests still pass
- [ ] No regressions on mobile layout

### Dependencies
Module 12 (complete)

---

*Created: 2026-03-14*
*Status: Planning — ready to build*
