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
| 7 | Guest Playground | ⬜ |
| 8 | Frontend SPA Foundation | ⬜ |
| 9 | Static Pages | ⬜ |
| 10 | Sitemap + SEO | ⬜ |
| 11 | Deploy Pipeline Polish | ⬜ |

---

## Current State
- **Backend:** All 12 API routes registered. BlogController + AdminBlogController fully implemented. AiController SSE streaming via Neuron AI + BlogWriterAgent. PlaygroundController is a 501 stub (Module 7).
- **Frontend:** Vue 3 SPA with auth working end-to-end. BlogEditor has AI generate panel with streaming. `/` redirects to `/blog`.
- **Tests:** 44 passing — blog, admin blog, auth, rate limiting, AI controller all covered.
- **Infra:** sinfat.com live. GitHub Actions auto-deploy on push. Prod `.env` set to `production` with debug off.

## Outstanding Items
- `ANTHROPIC_API_KEY` needs to be set in local `.env` and prod `.env` for AI generation to work
- Node.js 20.17.0 locally — Vite warns it needs 20.19+

## Start Here
Load spec at `specs/module-07-guest-playground.md` and run Module 7.

---

## Reference Files (read only when needed)
- Current module spec → `specs/module-07-guest-playground.md`
- Last session note → `docs/sessions/2026-03-08-module-6-ai-integration.md`
- Full history → `.pi/PROGRESS.md`
- Architecture decisions → `/Users/bernard/code/ai-learning/pi-vs-claude-code/docs/PORTFOLIO.md`
- Blog drafting → `/Users/bernard/code/ai-learning/pi-vs-claude-code/docs/summaries/`
