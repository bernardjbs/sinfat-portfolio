# Session Note — Module 5: Blog (Admin + Public)
*2026-03-07*

## What Was Done

Full blog system implemented — public-facing pages and admin management UI.

### Backend
- `BlogPostResource` updated: public show returns `content` as rendered HTML via `Str::markdown()`. Admin show returns `raw_content` (raw markdown for the editor). Sensitive fields (`status`, `ai_model`) still hidden from public responses.
- `league/commonmark` already installed as Laravel dependency — no new PHP packages needed.

### Frontend
- `stores/blog.js` — Pinia store with public actions (`fetchPosts`, `fetchPost`) and admin actions (`fetchAdminPosts`, `fetchAdminPost`, `createPost`, `updatePost`, `deletePost`, `togglePublish`). All admin mutations include XSRF-TOKEN.
- `components/BlogPostCard.vue` — reusable card: title (hover:text-accent), excerpt, date, AI drafted badge.
- `pages/Blog.vue` — public paginated listing. Pagination controls, empty state, error state.
- `pages/BlogPost.vue` — single post. Prose typography classes for rendered HTML. `v-html` used safely on server-rendered content. 404 handled.
- `pages/admin/Blog.vue` — admin post list with published/draft badges, edit link, publish toggle, delete (with confirm).
- `pages/admin/BlogEditor.vue` — `md-editor-v3` full-height editor. Top bar: title input, status badge, save draft, publish/unpublish. Bottom bar: excerpt input, created date, AI model.
- `router/index.js` — 4 new routes: `/blog`, `/blog/:slug`, `/admin/blog`, `/admin/blog/new`, `/admin/blog/:id/edit`.

### Packages installed
- `md-editor-v3@6.4.0` — markdown editor for admin
- `@tailwindcss/typography@0.5.19` — prose styling for blog posts (wired via `@plugin` in app.css)

### Workflow tooling
- `security-review` and `test-writer` skills created
- Module-runner workflow extended from 7 to 9 steps (Test + Security added before Commit)

### Tests
- Added `test_single_post_content_is_rendered_as_html` — confirms `<h2>` tag present
- Added `test_single_post_does_not_expose_raw_content_field` — public show has no `raw_content`
- Added `test_admin_single_post_returns_raw_content_not_rendered` — admin show has `raw_content`, no `content`
- 36 tests, 95 assertions, all passing

## Files Changed

```
app/Http/Resources/BlogPostResource.php
resources/js/stores/blog.js                  ← new
resources/js/components/BlogPostCard.vue     ← new
resources/js/pages/Blog.vue                  ← new
resources/js/pages/BlogPost.vue              ← new
resources/js/pages/admin/Blog.vue            ← new
resources/js/pages/admin/BlogEditor.vue      ← new
resources/js/router/index.js
resources/css/app.css
package.json / package-lock.json
tests/Feature/BlogControllerTest.php
tests/Feature/AdminBlogControllerTest.php
.pi/skills/security-review/SKILL.md         ← new
.pi/skills/test-writer/SKILL.md             ← new
.pi/agents/module-runner.config.md
.pi/skills/module-runner/SKILL.md
```

## Decisions Made

- `raw_content` field name (not `content`) in admin show — avoids editor loading rendered HTML into a markdown editor
- `md-editor-v3` chosen over plain textarea — built-in preview, dark mode, syntax highlighting
- Typography plugin wired via `@plugin` directive (Tailwind v4 CSS-based config, not tailwind.config.js)
- `beforeRouteUpdate` hook in BlogPost.vue — handles browser back/forward between posts without remounting

## Outstanding Items

- Build chunk size warning: `app.js` is 1MB+ due to `md-editor-v3`. Not a problem now; code-splitting is a Module 11 concern.
- `PROGRESS.md` in root still untracked — safe to ignore or delete.
- Admin editor has no autosave — intentional for now, `Ctrl+S` triggers save draft via `@save`.

## Next Module

Module 6 — AI Integration. `AiController` is currently a 501 stub. Needs Neuron AI agent, SSE streaming endpoint, admin UI for AI-assisted post drafting.
Spec at `specs/module-06-ai-integration.md`.
