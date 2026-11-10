## Module 16: Reading Fixes and Honest URLs
> 🟢 Opus

### Goal
Make long posts comfortable to read and make every post URL tell the truth: a post's own title and
description when shared, and a real 404 when the post does not exist. Source: blog review findings
2, 3, 4 and 8 (`~/code/software-factory-research/docs/blog-review.md`). Ruling 2 (2026-11-10): body
paragraphs in Geist sans, everything else stays Geist Mono.

### Tasks

#### Body font (ruling 2)
- [x] Load Geist (400, 500) alongside Geist Mono in the existing Google Fonts request
- [x] Add a `--font-sans` theme token for Geist
- [x] Post body text in Geist: paragraphs, list items and blockquotes (16px first, then 18px/1.6 with the post page layout below)
- [x] Headings, meta, nav, inline code and code blocks stay Geist Mono
- [x] Record the exception in `project-terminal-aesthetic` (through `/skills-builder`)

#### Body text colour and width
- [x] Add a `body` colour token: dark `#8b949e`, light `#656d76`
- [x] Change `prose-p:text-dim` in `BlogPost.vue` to the new token, and the same for list items,
      blockquotes and table cells; the `dim` token is unchanged
- [x] Narrow the post column so body paragraphs run about 70 characters a line (first `max-w-xl`, then the 38rem prose column below)
- [x] Record the token in `project-terminal-aesthetic`

#### Inline code
- [x] Remove the literal backticks around inline code (`prose-code:before:content-none prose-code:after:content-none`)

#### Post page layout (folded in 2026-11-10, design: `~/code/software-factory-research/docs/blog-post-page-design.md`)
- [x] Page grid: 38rem prose column with a sticky 11rem left track; left aligned
- [x] Contents list in the track, built from the post's h2s; each h2 takes its permalink anchor's id, with `scroll-margin-top`
- [x] `pre` and `table` break out 12rem to the right of the prose, never left
- [x] Type: body 18px/1.6 Geist, title 36px/1.15 mono, h2 22px mono, meta and track 13px
- [x] Meta as a sentence ("13 March 2026, 5 minute read. Drafted with AI, in development."), category the only link (to the blog index until phase 2)
- [x] New `link` token for prose links and inline code: dark `#3fb950`, light `#1a7f37`
- [x] Under 1100px: one column, track above the title, contents hidden, code full width
- [x] Under 600px: title 30px, body 17px
- [x] Right grid column `minmax(10rem, 1fr)` so the breakout never overflows between 1100 and 1200px

#### Per-post meta and real 404 (server)
- [x] Route `GET /blog/{slug}` ahead of the SPA catch-all
- [x] Published post: render the app shell with the post's title, excerpt and URL in `<title>`,
      meta description, `og:title`, `og:description`, `og:url`, and `og:type` `article`
- [x] No excerpt: fall back to the site description
- [x] Missing or draft slug: the same app shell with status 404 (the SPA shows "post not found")
- [x] Every other route keeps the existing shell, unchanged
- [x] Feature tests: 200 with the title in the HTML; 404 for a missing slug; 404 for a draft, with
      the draft's title absent from the HTML; HTML in a title is escaped; another route keeps the
      default meta

### Acceptance Criteria
- [x] Computed `font-family` of a post paragraph is Geist; a heading and inline code are Geist Mono
- [x] Paragraph text contrast is at least 4.5:1 in both themes, measured in the browser on `sinfat.test`
- [x] Paragraphs average 60 to 80 characters a line at 1440 and 2000px, measured in the browser (height ÷ line height)
- [x] Track right edge is left of the first code block's left edge when scrolled to it
- [x] `scrollWidth` equals the viewport at 390px and from 599 to 1200px
- [x] A contents link scrolls its heading into view below the top edge
- [x] Link and inline code contrast at least 4.5:1 in both themes
- [x] Inline code shows no backticks (computed `::before` content is `none`)
- [x] `curl` of a post URL on `sinfat.test` has the post title in `<title>` and `og:title`
- [x] `curl` of a missing slug returns 404
- [x] `php artisan test`: 74 passed and 3 skipped, plus the new tests, 0 failed
- [x] No regressions on mobile layout

### Convention Change
`project-terminal-aesthetic` no longer says "monospace always". Blog post body text (paragraphs,
list items, blockquotes) is Geist sans, 18px/1.6, in the new `body` colour token;
table cells take the `body` colour only. Everything else stays Geist Mono. Ruled 2026-11-10 (ruling
2, then lists, tables and blockquotes as "prose body text"). Post pages use the 38rem prose column
with a left contents track, and prose links and inline code use the new `link` token.

### Dependencies
Module 14 (complete)

---

*Created: 2026-11-10*
*Status: ✅ Complete (on branch, awaiting merge)*
