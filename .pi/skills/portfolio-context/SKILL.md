---
name: portfolio-context
description: Architecture reference for the sinfat-portfolio project. Use when working on any part of sinfat.com — covers stack decisions, environments, API routes, Vue Router routes, database tables, Pinia stores, design tokens, and deploy workflow.
---

# Portfolio Context — sinfat.com

Use this skill when working on any part of the sinfat-portfolio project. It provides the authoritative reference for architectural decisions, environment details, routes, and key technical constraints.

---

## Project Identity

- **Domain:** sinfat.com
- **Purpose:** Personal portfolio for Bernard — full-stack developer (Laravel + Vue), targeting Perth local market
- **Primary goal:** Get hired. The site must tell the story of Laravel + Vue production experience PLUS genuine AI tooling depth.
- **Target visitor:** Tech lead or IT manager at a Perth company modernising with AI

---

## Environments

| Environment | URL | Notes |
|------------|-----|-------|
| Local dev | https://sinfat.test | Laravel Valet |
| Production | https://sinfat.com | Oracle Cloud Arm A1 VM |

**Production server:**
- Oracle Cloud Arm A1 (Ubuntu 22.04, 2 OCPU, 12GB RAM)
- Nginx + PHP 8.3-FPM + MySQL 8 + Redis
- Cloudflare DNS + proxy + SSL
- Laravel at `/var/www/sinfat`
- SSH: `ssh -i ~/.ssh/sinfat-portfolio.key ubuntu@<oracle-ip>`

**GitHub:** `bernardjbs/sinfat-portfolio` (private)

---

## Architecture — Full SPA

**Pattern:** Vue Router handles ALL routing. Laravel is a pure API.

```
Laravel (routes/api.php)  →  pure JSON API
Vue SPA (resources/js/)   →  all page rendering
```

There are NO Blade page templates — only `resources/views/app.blade.php` as the SPA entry point.

**Why SPA (not hybrid):**
- Primary goal is employer visits via direct URL, not Google organic
- SEO not critical short-term — SPA ships faster
- Admin panel and playground are inherently SPA experiences
- Can add prerendering later if needed

---

## Tech Stack

| Layer | Technology | Notes |
|-------|-----------|-------|
| Backend framework | Laravel 12 | |
| PHP | 8.3 | |
| Database | MySQL 8 | Local: user `sinfat`, db `sinfat_portfolio` |
| Cache/Rate limiting | Redis | In-memory, TTL-based |
| Frontend framework | Vue 3 | Options API throughout |
| Build tool | Vite | |
| CSS | Tailwind CSS | |
| Font | Geist Mono | Monospace — terminal aesthetic |
| Icons | lucide-vue-next | |
| Markdown editor | md-editor-v3 | Admin only |
| AI runtime | Neuron AI (`inspector-apm/neuron-ai`) | |
| AI streaming | SSE (Server-Sent Events) | Not WebSockets |
| AI provider | Anthropic Claude | `claude-sonnet-4-5` default |
| Auth | Custom (no Breeze/Jetstream) | Single admin user |
| Sitemap | spatie/laravel-sitemap | |
| Markdown rendering | spatie/laravel-markdown or league/commonmark | |

---

## Vue Router Routes

```
/               Home.vue
/about          About.vue
/projects       Projects.vue
/blog           Blog.vue
/blog/:slug     BlogPost.vue
/uses           Uses.vue
/contact        Contact.vue
/playground     Playground.vue
/login          Login.vue
/admin          AdminLayout.vue (requiresAuth: true)
  /admin/       Dashboard.vue
  /admin/blog   AdminBlog.vue
  /admin/blog/:id  AdminBlogEditor.vue
/:pathMatch(.*)*  NotFound.vue
```

---

## API Routes

**Public:**
```
GET  /api/blog          → paginated list of published posts
GET  /api/blog/{slug}   → single published post (markdown rendered to HTML)
```

**Admin (auth:sanctum):**
```
GET    /api/admin/blog           → all posts (draft + published)
POST   /api/admin/blog           → create post
GET    /api/admin/blog/{id}      → get single post for editing
PUT    /api/admin/blog/{id}      → update post
DELETE /api/admin/blog/{id}      → delete post
PATCH  /api/admin/blog/{id}/publish  → toggle publish status
GET    /api/admin/me             → current user
POST   /api/admin/ai/generate   → stream blog draft (SSE)
```

**Guest (rate limited):**
```
POST /api/playground/generate   → stream content (SSE, 3/day limit)
POST /api/playground/key        → store guest API key in session
```

**Auth:**
```
POST /login   → public
POST /logout  → auth
```

---

## Database Tables

| Table | Purpose |
|-------|---------|
| `users` | Single admin user — no registration |
| `blog_posts` | All blog posts with draft/published status |
| `guest_usage` | Persistence layer for guest playground usage |
| `ai_sessions` | Log of every AI generation (cost/usage monitoring) |

**Key `blog_posts` columns:** `title`, `slug` (unique), `excerpt`, `content` (markdown), `status` (draft/published), `published_at`, `ai_generated` (bool), `ai_model`

---

## Pinia Stores

```
stores/auth.js       → isAuthenticated, user, login(), logout()
stores/theme.js      → isDark, toggle()
stores/playground.js → usageCount, isStreaming, content, apiKey
stores/blog.js       → posts, currentPost, loading
```

---

## Design System

**Tailwind colour tokens (exact hex):**
```
bg:      #0d1117   ← near black — page background
surface: #161b22   ← slightly lighter — cards, panels
accent:  #238636   ← green — primary accent, links, highlights
text:    #e6edf3   ← off white — body text
dim:     #6e7681   ← muted — secondary text, labels, metadata
border:  #30363d   ← subtle borders, dividers
```

**Font:** Geist Mono — monospace, loaded from Google Fonts
**Icons:** lucide-vue-next
**Dark mode:** class-based, dark default, light toggle available

---

## Security Constraints

- Guest API keys stored in session ONLY — never persisted to DB, never sent in responses
- Rate limiting at middleware layer (GuestRateLimit), enforced at handler not at prompt
- Admin routes protected by `auth:sanctum` middleware
- Single admin user — no registration route
- Laravel Policies and Global Scopes apply normally to AI-initiated requests

---

## Projects to Showcase

| Project | Story |
|---------|-------|
| pi-agent-toolkit | GitHub link + what it demonstrates (Pi extensibility, agent patterns) |
| Football Analytics Platform | 5 architectural iterations — honest story, no live demo |
| Time-Capsule Messaging App | GitHub if public — "Zero-AI deep dive" |
| This portfolio site | The site itself is a demo |

**Employer anonymity:** Do NOT name the current employer (Nano Solutions). Use: *"Currently working at a Perth-based SaaS company as a Full-Stack Developer since 2022"*

---

## Deploy Workflow

```bash
# Development
cd /Users/bernard/code/sinfat-portfolio
# work at https://sinfat.test

# Deploy
git push origin main
# → GitHub Actions auto-deploys to VM

# Manual fallback
ssh -i ~/.ssh/sinfat-portfolio.key ubuntu@<oracle-ip>
cd /var/www/sinfat && git pull && composer install --no-dev --optimize-autoloader
php artisan migrate --force && php artisan config:cache && php artisan route:cache
php artisan sitemap:generate && sudo systemctl reload nginx
```
