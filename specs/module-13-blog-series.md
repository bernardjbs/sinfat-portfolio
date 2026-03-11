# Module 13 — Blog Series: Building sinfat.com with AI

## Goal

Write and publish 14 blog posts that tell the story of building sinfat.com with AI as a development partner. Posts are ordered as a narrative journey — not the chaotic build order — so a reader starting at Post 1 gets the full story in sequence.

## The Arc

1. **The Setup** (Posts 1–3) — learning AI tools, making decisions, planning the build
2. **The Build** (Posts 4–11) — shipping features module by module with AI
3. **The Reflection** (Posts 12–14) — deploy, failures, and going public

## Category

All posts: `development`

## Workflow per Post

1. Draft content in conversation (Claude has full project context)
2. Save as draft via `php artisan tinker` locally
3. Copy markdown into prod admin editor at `sinfat.com/admin/blog`
4. Review preview, tweak if needed
5. Publish

---

## The Posts

### Act 1: The Setup

#### Post 1 ✅ DRAFTED
**"Building sinfat.com — A Developer Portfolio Built With AI"**
- Why the project exists, the stack, how AI was involved, what's coming
- Sources: 05, 06, 08
- Words: 703

#### Post 2
**"Learning Pi — What 12 Modules Taught Me About AI Coding Agents"**
- The pi tutorial journey — from vanilla agent to agent teams and meta-agents
- What "open and extensible" actually means vs Claude Code
- The toolkit that came out of it (5 extensions, 3-layer architecture)
- Sources: 01, 02, 03, 04, 05

#### Post 3
**"I Spent a Full Day Planning Before Writing a Single Line of Code"**
- Skills and agents — teaching AI about your specific project, not generic frameworks
- The module runner workflow — plan → execute → test → security → commit → document
- Why setup time compounds
- Sources: 08, 09, 14

---

### Act 2: The Build

#### Post 4
**"Setting Up an Oracle Cloud Arm VM for Laravel — Five Roadblocks and How AI Helped"**
- Oracle Cloud free tier, Cloudflare, Nginx, PHP-FPM, MySQL
- The five roadblocks (VCN, Composer, SQLite default, 522, iptables double firewall)
- What AI got right immediately vs what took conversation
- Sources: 06, 10

#### Post 5
**"Database Schema Decisions That Saved Me Later"**
- Why we dropped the guest_usage table
- No foreign keys — deliberate choice for a solo project
- Category as a simple string, not a pivot table
- The admin seeder that broke on prod
- Sources: 11

#### Post 6
**"Session Auth in a Vue SPA — Sanctum, CSRF, and the Refresh Problem"**
- Building the entire Vue foundation just to get login working
- The refresh problem (Pinia resets, server is source of truth)
- Sanctum's statefulApi() — why API routes can't see sessions without it
- The CSRF cookie approach
- Sources: 12

#### Post 7
**"Define the API Before You Build It"**
- 12 endpoints locked in before business logic
- Stub what's complex, implement what's trivial
- One resource class, multiple shapes (conditional fields per route)
- The GuestRateLimit middleware — Cache facade over Redis facade
- Sources: 13

#### Post 8
**"SSE Streaming with Neuron AI in Laravel — The Nginx Buffering Trap"**
- Why SSE over WebSockets for LLM streaming
- Neuron AI agent pattern (provider → instructions → stream)
- The POST problem with EventSource (fetch + ReadableStream instead)
- X-Accel-Buffering: no — the one header that makes or breaks it
- Multi-provider setup (swap LLMs with one env var)
- Sources: 07, 17

#### Post 9
**"Building a Guest AI Playground — Rate Limiting, Session Keys, and Three Bugs"**
- Session-only API key storage (never persisted, never in responses)
- RateLimiter facade vs raw cache
- Three bugs: CSRF rotation, counter reset on refresh, clipboard API gesture context
- Testing that a key is NOT stored
- Sources: 18

#### Post 10
**"Terminal Aesthetic — Designing a Developer Portfolio That Doesn't Look Like Every Other One"**
- Geist Mono, dark-first, green accents
- Tailwind v4 CSS-based config (no tailwind.config.js)
- Layout architecture with Vue Router nested routes
- Code splitting — 203KB → 136KB main chunk
- The md-editor-v3 decision (choosing to not add the dependency)
- Sources: 19, 20

#### Post 11
**"SEO for a Single-Page App with Laravel"**
- Sitemap generation with spatie/laravel-sitemap
- Robots.txt for admin/login exclusion
- Per-route meta tags in a Vue SPA (afterEach hook + watcher)
- What's still missing (og:image, Search Console, SSR)
- Sources: 21

---

### Act 3: The Reflection

#### Post 12
**"From GitHub Actions to `just deploy` — Simplifying Production Deploys"**
- GitHub Models — free AI via PAT (the Gemini dead end)
- PHP version trap (platform pin in composer.json)
- Redis server ≠ PHP Redis extension
- Silent exception swallowing — always log the real error
- Why we ditched GitHub Actions for a shell script
- The 956MB RAM surprise and the swap fix
- Sources: 22, session 2026-03-11

#### Post 13
**"When Your AI Session Crashes Mid-Module"**
- Session crash recovery — what happens when context is lost
- Two sources of truth problem (merged into one SESSION.md)
- The deploy disaster (.gitignore divergence on prod)
- Workflow gaps are invisible until they aren't
- Sources: 16, 24

#### Post 14
**"The Repo Is the Portfolio — Why I Kept It Private Until It Was Done"**
- The pre-launch checklist (secrets, server IP, README, favicon, license)
- Building in public vs shipping in private
- The repo as a deliverable, not a backing store
- Sources: 25

---

## Acceptance Criteria

- [ ] All 14 posts drafted and saved to local DB
- [ ] All 14 posts published on sinfat.com
- [ ] Each post 600–1200 words
- [ ] Each post has excerpt, category (`development`), ai_generated flag
- [ ] RSS feed includes all published posts
- [ ] Sitemap regenerated after publishing
