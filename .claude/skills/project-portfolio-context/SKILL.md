---
name: project-portfolio-context
description: Architecture reference for the sinfat-portfolio project. Use when working on any part of sinfat.com — covers stack decisions, environments, API routes, Vue Router routes, database tables, Pinia stores, design tokens, and deploy workflow.
---

# Portfolio Context — sinfat.com

Use this skill when working on any part of the sinfat-portfolio project. It is the reference for architectural decisions, environment details, routes, and key technical constraints.

If `--help` appears anywhere in the user's invocation, read and display [help.md](help.md) and stop.

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
- Oracle Cloud Arm A1, Ubuntu 22.04 (HISTORICAL, unverified: "2 OCPU, 12GB RAM"; `docs/deploy-guide.md` records 956MB RAM + 2GB swap)
- Nginx + PHP 8.3-FPM + MySQL 8 + Redis
- Cloudflare DNS + proxy + SSL
- Laravel at `/var/www/sinfat`
- SSH: `ssh sinfat` (host alias in `~/.ssh/config`), or `just ssh`

**GitHub:** `bernardjbs/sinfat-portfolio` (private)

---

## Architecture — Full SPA

**Pattern:** Vue Router handles ALL routing. Laravel is a pure API.

```
Laravel (routes/api.php)  →  pure JSON API
Vue SPA (resources/js/)   →  all page rendering
```

There are NO Blade page templates — only `resources/views/app.blade.php` as the SPA entry point (plus the unused `welcome.blade.php` scaffold). Laravel serves that shell with server-filled meta for blog URLs, plus `/feed.xml` and the static `/notebook/{stamp}` snapshots.

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
| Markdown editor | `<textarea>` with a `marked` preview | Admin only |
| AI runtime | Neuron AI (`neuron-core/neuron-ai`) | |
| AI streaming | SSE (Server-Sent Events) | Not WebSockets |
| AI provider | Switchable: `AI_PROVIDER` (anthropic, gemini, github, ollama) | Read `config/services.php` for models; prod per `docs/production-env.md`. GitHub Models (`github`) retired 2026-07-30: the playground is off (`PLAYGROUND_ENABLED`) until a replacement is chosen (`docs/backlog.md`) |
| Auth | Custom (no Breeze/Jetstream) | Single admin user |
| Sitemap | spatie/laravel-sitemap | |
| Markdown rendering | league/commonmark | Configured in `BlogPostResource` |

---

## Routes, Tables and Stores

Vue and Laravel routes (including server meta, `/feed.xml` and the `/notebook/{stamp}` snapshots), API routes, tables, series data and Pinia stores: [references/routes-and-data.md](references/routes-and-data.md). Read it before adding or changing a route, table or store.

**Design system:** colours, fonts and components are in `project-terminal-aesthetic`; values live in `resources/css/app.css`.

---

## Security Constraints

- Guest API keys stored in session ONLY — never persisted to DB, never sent in responses
- Rate limiting at middleware layer (GuestRateLimit), enforced at handler not at prompt
- Admin routes protected by `auth:sanctum` middleware
- Single admin user — no registration route
- No Policies or Global Scopes exist yet (2026-11-11); any added must apply to AI-initiated requests too

---

## Projects to Showcase

| Project | Story |
|---------|-------|
| pi-agent-toolkit | GitHub link + what it demonstrates (Pi extensibility, agent patterns) |
| Football Analytics Platform | 5 architectural iterations — honest story, no live demo |
| Time-Capsule Messaging App | GitHub if public — "Zero-AI deep dive" |
| This portfolio site | The site itself is a demo |

**Employer anonymity:** Do NOT name the current employer anywhere on the site or in posts. Use: *"Currently working at a Perth-based SaaS company as a Full-Stack Developer since 2022"*

---

## Deploy Workflow

```bash
# Development
cd /Users/bernard/code/sinfat-portfolio
# work at https://sinfat.test

# Deploy
git push origin main
just deploy
# → SSHes into server, runs scripts/deploy.sh
# → pull, composer, npm install, vite build, migrate, cache, sitemap, nginx reload

# Send local post <id> to production as a draft (Bernard runs it)
just post-to-prod <id>

# SSH into server directly
just ssh
```
