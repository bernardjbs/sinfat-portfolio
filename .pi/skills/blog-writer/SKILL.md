---
name: blog-writer
description: Generate professional blog posts for sinfat.com. Use when creating blog content from summary files or manual input. Covers tone, structure, category assignment, and the full draft-to-publish workflow.
---

# Blog Writer — sinfat.com

Use this skill when generating blog content for sinfat.com. Two modes: from a summary file, or from manual input.

---

## How to Invoke

### Mode 1: From a summary file

```
Write a blog post from /Users/bernard/code/ai-learning/pi-vs-claude-code/docs/summaries/22-module-11-deploy-pipeline-and-ai-provider.md
```

The skill will:
1. Read the summary file
2. Identify the best category
3. Generate a professional blog post
4. Create it as a draft via artisan tinker or the admin API

### Mode 2: Manual input

```
Write a blog post:
- Category: laravel
- Title: Building an SSE Streaming Pipeline in Laravel
- Content: [your rough notes, bullet points, or outline]
```

The skill will expand your notes into a polished post.

---

## Categories

Use one of these values for the `category` field:

| Category | Covers |
|----------|--------|
| `laravel` | Laravel patterns, PHP, backend architecture, Eloquent, services |
| `ai` | AI integration, Neuron AI, LLMs, streaming, prompt engineering |
| `vue` | Vue 3, frontend patterns, Pinia, Tailwind, SPA architecture |
| `devops` | Deploy pipelines, CI/CD, server setup, Nginx, Redis, Docker |
| `building-sinfat` | Meta posts about building this portfolio — decisions, journey, lessons |

If a post spans multiple topics, pick the **primary** one. A post about "SSE streaming with Neuron AI in Laravel" is `laravel` if the focus is the Laravel plumbing, or `ai` if the focus is the AI integration.

---

## Tone & Style

The blog reflects the terminal aesthetic — technical, opinionated, concise.

**Do:**
- Write in first person ("I built", "I discovered")
- Be specific — name the exact package, version, error message
- Show real code from the project, not generic examples
- Explain the *why* before the *how*
- Include gotchas and what didn't work
- Keep paragraphs short (2-4 sentences)
- Use markdown headings, code blocks, and bullet points

**Don't:**
- Start with "In this blog post, I will..." — just start
- Use filler phrases ("It's worth noting that...", "As we all know...")
- Write generic tutorials — this is a project journal
- Over-explain basics (audience is experienced developers)
- Use emoji in prose

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
    'category'     => 'laravel',
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

The summaries at `/Users/bernard/code/ai-learning/pi-vs-claude-code/docs/summaries/` cover the entire build journey. Here's a suggested mapping:

| Summary | Blog Title | Category |
|---------|-----------|----------|
| `06-infrastructure-journey.md` | Setting Up an Oracle Cloud Arm VM for Laravel | devops |
| `10-module-1-redis-ssl-deploy-supervisor.md` | Redis, SSL, and Auto-Deploy on a Free Oracle VM | devops |
| `14-security-test-skills.md` | Security Checklist for a Laravel API | laravel |
| `15-module-5-blog-frontend-markdown.md` | Building a Markdown Blog with Laravel and Vue | building-sinfat |
| `16-session-crash-recovery-and-process-refinement.md` | When Your AI Session Crashes Mid-Module | building-sinfat |
| `17-module-6-ai-streaming-neuron.md` | SSE Streaming with Neuron AI in Laravel | ai |
| `18-module-7-guest-playground.md` | Building a Guest AI Playground with Rate Limiting | ai |
| `19-module-8-spa-foundation.md` | Terminal Aesthetic — Designing a Developer Portfolio | building-sinfat |
| `20-module-9-static-pages.md` | Writing Static Pages That Don't Feel Static | building-sinfat |
| `21-module-10-sitemap-seo.md` | SEO for a Single-Page App with Laravel | laravel |
| `22-module-11-deploy-pipeline-and-ai-provider.md` | GitHub Models — Free AI for Your Side Project | ai |

---

## Quality Checklist

Before marking a post ready for review:

- [ ] Title is specific and descriptive (not clickbait)
- [ ] Excerpt is one sentence, works in RSS and blog listing
- [ ] Category is set to one of the five values above
- [ ] Code examples are from the actual project, not generic
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
