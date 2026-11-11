---
name: project-blog-writer
description: Generate professional blog posts for sinfat.com. Use when creating blog content from summary files, a post brief or manual input. Covers the narrative, tone, structure, series placement, category and the full draft-to-publish workflow.
---

# Blog Writer: sinfat.com

Use this skill when generating blog content for sinfat.com.

If `--help` appears anywhere in the user's invocation, read and display [help.md](help.md) and stop.

---

## The Narrative

Every post on sinfat.com is a first-person account of **building real software with AI**: what it was like, what the AI got right, what it got wrong. The software varies. Each project the blog covers is a series (for example the portfolio build, or the software factory), and each post is one chapter of that project's journal.

Posts aren't generic tutorials. Each covers one specific part of the work, always through the lens of "I built this with AI, here's what actually happened."

**Source material stays in the project it is about.** This repo only publishes. Read the sources the input names, nothing else, and keep to what they support.

---

## How to Invoke

### Mode 1: From a summary file

```
Write a blog post from /Users/bernard/code/ai-learning/pi-vs-claude-code/docs/summaries/22-module-11-deploy-pipeline-and-ai-provider.md
```

Read the summary, write the post framed around the AI development journey, create it as a draft.

### Mode 2: From a post brief

```
Write a blog post from /Users/bernard/code/software-factory-research/docs/blog-post-01-brief.md
```

A brief, written by the project's own session, names the series and part, the sources, the arc, the quotes and its hard rules. **The brief's hard rules override this skill's defaults** (length, figures, off-limits topics). Quotes it marks as Bernard's are verbatim; fix only obvious typos.

### Mode 3: Manual input

```
Write a blog post:
- Title: Building an SSE Streaming Pipeline with AI Pair Programming
- Content: [your rough notes, bullet points, or outline]
```

Expand the notes into a finished post.

---

## Series and Category

- **Series:** every post goes into the series its input names, via `--series=<slug>`. A series' arc, candidate order and workflow live in its spec (`grep -l <slug> specs/*.md`). No series named? Ask before creating one.
- **Category:** every post is **`development`**. New categories only if the blog grows beyond building software with AI.

---

## Tone & Style

The blog reflects the terminal aesthetic: technical, opinionated, concise. But above all, **honest**. The author is a developer learning in public, not a conference speaker or thought leader. If he doesn't know something, he says so. If the AI did most of the thinking, he admits it. The voice is someone figuring things out, not someone who already has.

**Do:**
- Write in first person ("I built", "I discovered")
- Be specific: name the exact package, version, error message
- Show real code from the project, not generic examples
- Explain the *why* before the *how*
- Include the AI collaboration angle: what did the AI get right, what needed correction
- Include gotchas and what didn't work
- Keep paragraphs short (2 to 4 sentences)
- Use markdown headings, code blocks, and bullet points
- **Admit uncertainty:** "I don't know if this is the right approach", "I'm not sure how much this matters"
- **Routine work reads as routine:** an audit, an upgrade or a patch is written matter-of-fact and competent. Keep "admit uncertainty" for what was genuinely unknown, never for normal maintenance
- **Say when the AI carried you:** if you followed the AI's lead without fully understanding, say that
- **Sound like a person talking, not a blog post being written:** conversational, not polished
- UK English

**Don't:**
- Start with "In this blog post, I will...". Just start
- Use filler phrases ("It's worth noting that...", "As we all know...")
- **Sound authoritative on topics you're not expert in.** Don't explain SEO like an SEO consultant or design like a designer. Explain it like a backend dev who had to figure it out
- **Wrap up with neat lessons** unless the lesson was genuinely surprising. Skip "What I Learned" if it would just restate the post
- **Use polished summary sentences** like "SEO for a single-page app isn't hard" or "The consistency is what creates the aesthetic". They sound like an AI wrapping up an essay
- **Use em dashes (—) at all.** None, in the post, title or excerpt. They are a major AI tell. Use a full stop, comma, colon or brackets; prefer short sentences. Check before saving: `grep -c '—' <file>` must print `0`
- Write generic tutorials: this is a project journal
- Over-explain basics (audience is experienced developers)
- Use emoji in prose
- Make AI sound magical: be honest about where it helped and where it didn't

**Length:** 600 to 1,200 words unless the brief says otherwise. Shorter is better if the point is made.

---

## Post Structure

```markdown
# [Title: specific, not clickbait]

[Opening paragraph: the problem or context, 2 to 3 sentences max]

## [First section heading]

[Content with code examples from the actual project]

## [Second section heading]

[More content]

## What I Learned

[Key takeaways: bullet points or short paragraphs]
```

Not every post needs "What I Learned"; use it only for genuine surprises or gotchas.

---

## Gates

- **Drafts only.** Agents create and update drafts; Bernard publishes in the admin editor. Never publish, never touch production.
- **All post writes go through `php artisan blog:manage`**, never tinker: heredoc escaping and variable scoping cause data loss. Commands, flags and safety checks: [references/managing-posts.md](references/managing-posts.md).
- **Export before every update**, as the backup.

---

## Quality Checklist

Before marking a post ready for review:

- [ ] Title is specific and descriptive (not clickbait)
- [ ] Excerpt is one sentence, works in RSS and blog listing
- [ ] Category is `development`; series and part as the input names
- [ ] Every claim and figure is in the named sources
- [ ] Code examples are from the actual project, not generic
- [ ] The AI collaboration angle is present: not forced, but honest
- [ ] No filler paragraphs; every section earns its place
- [ ] `grep -c '—'` on the file prints `0`
- [ ] Markdown renders cleanly (headings, code blocks, lists)
- [ ] Length within the input's range
- [ ] `ai_generated` true, with `--ai-model` set to the model that drafted it

---

## Workflow

1. **Generate:** use this skill to create the draft in its series
2. **Review:** Bernard reads it in the admin UI at `/admin/blog`
3. **Edit:** tweak the markdown if needed
4. **Publish:** Bernard toggles publish in admin (production steps for a series post are in its spec)
5. **Verify:** check the live post at `sinfat.com/blog/[slug]`

Past summary-to-post mapping (HISTORICAL): [references/summary-mapping.md](references/summary-mapping.md).
