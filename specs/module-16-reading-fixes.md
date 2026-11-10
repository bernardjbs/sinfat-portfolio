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
- [x] Post body text in Geist, 16px, line-height `relaxed` (1.625): paragraphs, list items and blockquotes
- [x] Headings, meta, nav, inline code and code blocks stay Geist Mono
- [x] Record the exception in `project-terminal-aesthetic` (through `/skills-builder`)

#### Body text colour and width
- [x] Add a `body` colour token: dark `#8b949e`, light `#656d76`
- [x] Change `prose-p:text-dim` in `BlogPost.vue` to the new token, and the same for list items,
      blockquotes and table cells; the `dim` token is unchanged
- [x] Narrow the post column so body paragraphs run about 70 characters a line
- [x] Record the token in `project-terminal-aesthetic`

#### Inline code
- [x] Remove the literal backticks around inline code (`prose-code:before:content-none prose-code:after:content-none`)

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
- [x] Paragraphs average 60 to 75 characters a line, measured in the browser
- [x] Inline code shows no backticks (computed `::before` content is `none`)
- [x] `curl` of a post URL on `sinfat.test` has the post title in `<title>` and `og:title`
- [x] `curl` of a missing slug returns 404
- [x] `php artisan test`: 74 passed and 3 skipped, plus the new tests, 0 failed
- [x] No regressions on mobile layout

### Convention Change
`project-terminal-aesthetic` no longer says "monospace always". Blog post body text (paragraphs,
list items, blockquotes) is Geist sans, 16px, `leading-relaxed`, in the new `body` colour token;
table cells take the `body` colour only. Everything else stays Geist Mono. Ruled 2026-11-10 (ruling
2, then lists, tables and blockquotes as "prose body text").

### Dependencies
Module 14 (complete)

---

*Created: 2026-11-10*
*Status: ✅ Complete (on branch, awaiting merge)*
