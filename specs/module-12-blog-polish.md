## Module 12 — Blog Polish
> 🟢 Sonnet

### Goal
Make the blog feel professional and ready for real content. Add a blog writer skill for generating posts, and polish the reading experience with essential features.

### Tasks

#### Blog Writer Skill
- [ ] Create `.pi/skills/blog-writer/SKILL.md` — generates professional blog posts
- [ ] Two modes: from summary file or manual (category + title + content)
- [ ] Output is a draft blog post created via admin API or artisan command
- [ ] Posts follow the terminal aesthetic tone — technical, opinionated, concise

#### Reading Time
- [ ] Calculate reading time from content (approx 200 words/min)
- [ ] Display on `BlogPostCard` and `BlogPost` header
- [ ] Return `reading_time` from the API resource

#### Category Pill
- [ ] Display category as a small pill/label on cards and post header
- [ ] Style with terminal aesthetic (border, mono font, muted colour)

#### Copy Link
- [ ] Add a "copy link" button on the blog post page
- [ ] Copies the full URL to clipboard
- [ ] Brief visual feedback ("copied!")

#### Code Syntax Highlighting
- [ ] Install highlight.js with a dark theme
- [ ] Apply to all `<pre><code>` blocks in blog post content
- [ ] Theme should match the terminal colour palette

#### Back to Top
- [ ] Add a subtle "↑ top" link at the bottom of blog posts

#### RSS Feed
- [ ] Add `/feed.xml` route returning RSS 2.0 XML
- [ ] Include title, excerpt, link, published_at for published posts
- [ ] Add `<link rel="alternate" type="application/rss+xml">` to the HTML head

### Acceptance Criteria
- [ ] Blog writer skill can generate a draft post from a summary file
- [ ] Blog writer skill can generate a draft post from manual input
- [ ] Reading time displays on cards and post pages
- [ ] Category pill renders on cards and post pages
- [ ] Copy link works and shows feedback
- [ ] Code blocks have syntax highlighting
- [ ] Back to top link appears on post pages
- [ ] `/feed.xml` returns valid RSS with published posts
- [ ] All existing tests still pass
- [ ] New tests written for RSS endpoint and reading time

### Dependencies
Module 11 (complete)

---

*Created: 2026-03-10*
*Status: Planning complete — ready to build*
