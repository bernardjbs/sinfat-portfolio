## Module 12 — Blog Polish
> 🟢 Sonnet

### Goal
Make the blog feel professional and ready for real content. Add a blog writer skill for generating posts, and polish the reading experience with essential features.

### Tasks

#### Blog Writer Skill
- [x] Create `.pi/skills/blog-writer/SKILL.md` — generates professional blog posts
- [x] Two modes: from summary file or manual (category + title + content)
- [x] Output is a draft blog post created via admin API or artisan command
- [x] Posts follow the terminal aesthetic tone — technical, opinionated, concise

#### Reading Time
- [x] Calculate reading time from content (approx 200 words/min)
- [x] Display on `BlogPostCard` and `BlogPost` header
- [x] Return `reading_time` from the API resource

#### Category Pill
- [x] Display category as a small pill/label on cards and post header
- [x] Style with terminal aesthetic (border, mono font, muted colour)

#### Copy Link
- [x] Add a "copy link" button on the blog post page
- [x] Copies the full URL to clipboard
- [x] Brief visual feedback ("copied!")

#### Code Syntax Highlighting
- [x] Install highlight.js with a dark theme
- [x] Apply to all `<pre><code>` blocks in blog post content
- [x] Theme should match the terminal colour palette

#### Back to Top
- [x] Add a subtle "↑ top" link at the bottom of blog posts

#### RSS Feed
- [x] Add `/feed.xml` route returning RSS 2.0 XML
- [x] Include title, excerpt, link, published_at for published posts
- [x] Add `<link rel="alternate" type="application/rss+xml">` to the HTML head

### Acceptance Criteria
- [x] Blog writer skill can generate a draft post from a summary file
- [x] Blog writer skill can generate a draft post from manual input
- [x] Reading time displays on cards and post pages
- [x] Category pill renders on cards and post pages
- [x] Copy link works and shows feedback
- [x] Code blocks have syntax highlighting
- [x] Back to top link appears on post pages
- [x] `/feed.xml` returns valid RSS with published posts
- [x] All existing tests still pass
- [x] New tests written for RSS endpoint and reading time

### Dependencies
Module 11 (complete)

---

*Created: 2026-03-10*
*Status: Planning complete — ready to build*
