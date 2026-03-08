# Session State

Read this file first at the start of every session. It tells you where you are and what to do next.

---

## Key Facts
- Local: `https://sinfat.test` (Valet)
- Prod: `sinfat.com` — Oracle Cloud Arm A1 VM
- SSH: `ssh -i ~/.ssh/sinfat-portfolio.key ubuntu@140.238.202.198`
- Repo: `bernardjbs/sinfat-portfolio` (private), code at `/Users/bernard/code/sinfat-portfolio`
- GitHub Actions secrets set ✅ (`SERVER_HOST`, `SERVER_SSH_KEY`)

---

## Module Status
| # | Module | Status |
|---|--------|--------|
| 1 | Infrastructure & Server Setup | ✅ Complete |
| 2 | Database Schema | ✅ Complete |
| 3 | Authentication | ✅ Complete |
| 4 | API Contract | ✅ Complete |
| 5 | Blog (Admin + Public) | ✅ Complete |
| 6 | AI Integration | ✅ Complete |
| 7 | Guest Playground | ✅ Complete |
| 8 | Frontend SPA Foundation | ⬜ |
| 9 | Static Pages | ⬜ |
| 10 | Sitemap + SEO | ⬜ |
| 11 | Deploy Pipeline Polish | ⬜ |

---

## Current State
- **Backend:** All API routes implemented. PlaygroundController with SSE streaming, GuestRateLimit middleware (3/day via RateLimiter), guest API key session storage. GuestUsageService handles streaming + logging.
- **Frontend:** Vue 3 SPA with Playground.vue (streaming output, counter badge, ApiKeyModal). Routes: `/blog`, `/playground`, `/admin/*`.
- **Tests:** 58 passing — blog, admin blog, auth, rate limiting, AI controller, playground all covered.
- **Infra:** sinfat.com live. GitHub Actions auto-deploy on push. Prod `.env` set to `production` with debug off.

## Outstanding Items
- Prod `.env` needs `AI_PROVIDER` + API key set (anthropic or gemini) before AI works on sinfat.com
- Local uses Ollama (llama3.2) — `brew services start ollama` to run
- Node.js 20.17.0 locally — Vite warns it needs 20.19+

## Start Here
Load spec at `specs/module-08-frontend-spa-foundation.md` and run Module 8.

---

## Reference Files (read only when needed)
- Current module spec → `specs/module-08-frontend-spa-foundation.md`
- Last session note → `docs/sessions/2026-03-08-module-7-guest-playground.md`
- Full history → `.pi/PROGRESS.md`
- Architecture decisions → `/Users/bernard/code/ai-learning/pi-vs-claude-code/docs/PORTFOLIO.md`
- Blog drafting → `/Users/bernard/code/ai-learning/pi-vs-claude-code/docs/summaries/`
