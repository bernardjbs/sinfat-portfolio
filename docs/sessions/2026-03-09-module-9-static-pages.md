# Module 9 — Static Pages

**Date:** 2026-03-09
**Status:** ✅ Complete

## What Was Done

Built all public-facing pages with real content following the terminal aesthetic.

### Pages Built
- **Home.vue** — Hero with headline + intro, CV download + contact CTA, featured project cards (3), latest blog post previews (3, fetched from API)
- **About.vue** — Professional summary, skills grid (4 categories), education, philosophy paragraph, CV download
- **Projects.vue** — 4 project cards: sinfat.com, pi-agent-toolkit, Football Analytics (with story), Time Capsule
- **Contact.vue** — Email, GitHub, LinkedIn links + location + availability statement
- **Uses.vue** — 4 categories: editor/terminal, development, infrastructure, AI tools

### Pages Unchanged
- **Blog.vue** — Already complete from Module 5
- **BlogPost.vue** — Already complete from Module 5
- **NotFound.vue** — Already complete from Module 8

### Key Decisions
- CV download links point to `/cv.pdf` — file needs to be added to `public/` directory
- Uses page filled with real content rather than left as TBD
- Football Analytics project includes honest "5 iterations" story as italic text
- Contact email set to `bernard@sinfat.com` — needs mailbox configured
- Location field rendered as plain text, not a link

## Outstanding Items
- `public/cv.pdf` needs to be added for download links to work
- Email `bernard@sinfat.com` mailbox not yet configured
- Football Analytics description is placeholder — real story TBD

## Post-Module Fixes (same session)

Three bugs found during manual testing and fixed in `fix/module-8-9-issues`:

1. **Light mode toggle stuck on dark** — CSS had hardcoded dark colours in `body` and `@theme` only defined dark tokens. Fixed: light colours as `@theme` defaults, dark overrides in `.dark` class selector. Toggle now works.
2. **`/admin` blank page when logged out** — `fetchUser()` in auth store had no `Accept: application/json` header and no try/catch. Laravel likely returned an HTML redirect instead of JSON 401, crashing the guard silently. Fixed: added header + try/catch.
3. **Home featured projects missing "private" label** — Football Analytics had no URL but no fallback label. Fixed: added `v-else` span showing "private".

## Tests
- 58 passing (no new PHP tests — frontend content only)
- Vite build successful

## Next Module
Module 10 — Sitemap + SEO
