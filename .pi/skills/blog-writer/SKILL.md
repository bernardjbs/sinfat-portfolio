---
name: blog-writer
description: Generate professional blog posts for sinfat.com. Use when creating blog content from summary files or manual input. Covers tone, structure, category assignment, and the full draft-to-publish workflow.
---

# Blog Writer — sinfat.com

Use this skill when generating blog content for sinfat.com.

---

## The Narrative

Every blog post on sinfat.com tells the story of **building this portfolio with AI**. The overarching topic is the learning journey — what it's like to build a real project with AI as your development partner.

Posts aren't generic tutorials. They're chapters in a project journal. Each one covers a specific part of the build — infrastructure, authentication, streaming, deployment — but always through the lens of "I built this with AI, here's what actually happened."

---

## How to Invoke

### Mode 1: From a summary file

```
Write a blog post from /Users/bernard/code/ai-learning/pi-vs-claude-code/docs/summaries/22-module-11-deploy-pipeline-and-ai-provider.md
```

The skill will:
1. Read the summary file
2. Generate a professional blog post framed around the AI development journey
3. Create it as a draft via artisan tinker

### Mode 2: Manual input

```
Write a blog post:
- Title: Building an SSE Streaming Pipeline with AI Pair Programming
- Content: [your rough notes, bullet points, or outline]
```

The skill will expand your notes into a polished post.

---

## Category

All posts use the category **`development`**. This is a single-topic blog about building software with AI. If the blog grows to cover unrelated topics in the future, new categories can be added then.

---

## Tone & Style

The blog reflects the terminal aesthetic — technical, opinionated, concise.

**Do:**
- Write in first person ("I built", "I discovered")
- Be specific — name the exact package, version, error message
- Show real code from the project, not generic examples
- Explain the *why* before the *how*
- Include the AI collaboration angle — what did the AI get right, what needed correction
- Include gotchas and what didn't work
- Keep paragraphs short (2-4 sentences)
- Use markdown headings, code blocks, and bullet points

**Don't:**
- Start with "In this blog post, I will..." — just start
- Use filler phrases ("It's worth noting that...", "As we all know...")
- Write generic tutorials — this is a project journal
- Over-explain basics (audience is experienced developers)
- Use emoji in prose
- Make AI sound magical — be honest about where it helped and where it didn't

**Length:** 600–1200 words. Shorter is better if the point is made.

---

## Post Structure

```markdown
# [Title — specific, not clickbait]

[Opening paragraph — the problem or context, 2-3 sentences max]

## [First section heading]

[Content with code examples from the actual project]

## [Second section heading]

[More content]

## What I Learned

[Key takeaways — bullet points or short paragraphs]
```

Not every post needs "What I Learned" — use it when there are genuine surprises or gotchas.

---

## Creating the Draft

After generating the content, create the post as a draft using artisan tinker:

```bash
php artisan tinker
```

```php
App\Models\BlogPost::create([
    'title'        => 'Your Title Here',
    'excerpt'      => 'One sentence summary for the blog listing and RSS feed.',
    'content'      => $content, // the full markdown
    'category'     => 'development',
    'status'       => 'draft',
    'ai_generated' => true,
    'ai_model'     => 'claude-sonnet-4-5', // or whichever model generated it
]);
```

The slug is auto-generated from the title. To publish later:

```php
$post = App\Models\BlogPost::where('slug', 'your-slug')->first();
$post->update(['status' => 'published', 'published_at' => now()]);
```

Or use the admin UI at `/admin/blog` to review, edit, and publish.

---

## Mapping Summaries to Blog Posts

The summaries at `/Users/bernard/code/ai-learning/pi-vs-claude-code/docs/summaries/` cover the entire build journey. Here's a suggested mapping — all under `development`:

| Summary | Blog Title |
|---------|-----------|
| `06-infrastructure-journey.md` | Setting Up an Oracle Cloud Arm VM with AI |
| `10-module-1-redis-ssl-deploy-supervisor.md` | Redis, SSL, and Auto-Deploy — AI Gets Its Hands Dirty |
| `14-security-test-skills.md` | Building a Security Checklist with AI |
| `15-module-5-blog-frontend-markdown.md` | Building a Markdown Blog with My AI Pair Programmer |
| `16-session-crash-recovery-and-process-refinement.md` | When Your AI Session Crashes Mid-Module |
| `17-module-6-ai-streaming-neuron.md` | SSE Streaming with Neuron AI — The Build Story |
| `18-module-7-guest-playground.md` | Building a Guest AI Playground with Rate Limiting |
| `19-module-8-spa-foundation.md` | Terminal Aesthetic — Designing a Portfolio with AI |
| `20-module-9-static-pages.md` | Writing Static Pages That Don't Feel Static |
| `21-module-10-sitemap-seo.md` | SEO for a Single-Page App — What AI Missed |
| `22-module-11-deploy-pipeline-and-ai-provider.md` | GitHub Models — Free AI for Your Side Project |

---

## Quality Checklist

Before marking a post ready for review:

- [ ] Title is specific and descriptive (not clickbait)
- [ ] Excerpt is one sentence, works in RSS and blog listing
- [ ] Category is set to `development`
- [ ] Code examples are from the actual project, not generic
- [ ] The AI collaboration angle is present — not forced, but honest
- [ ] No filler paragraphs — every section earns its place
- [ ] Markdown renders cleanly (headings, code blocks, lists)
- [ ] Length is 600–1200 words
- [ ] `ai_generated` is set to `true` with the model name

---

## Workflow

1. **Generate** — use this skill to create the draft
2. **Review** — read it in the admin UI at `/admin/blog`
3. **Edit** — tweak the markdown if needed
4. **Publish** — toggle publish in admin or via tinker
5. **Verify** — check the live post at `sinfat.com/blog/[slug]`
