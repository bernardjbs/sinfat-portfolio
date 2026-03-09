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
| 8 | Frontend SPA Foundation | ✅ Complete |
| 9 | Static Pages | ⬜ |
| 10 | Sitemap + SEO | ⬜ |
| 11 | Deploy Pipeline Polish | ⬜ |

---

## Current State
- **Backend:** All API routes implemented. No changes in Module 8 (frontend-only module).
- **Frontend:** Full SPA foundation in place. AppLayout (NavBar + Footer) wraps all public pages. AdminLayout (sidebar) wraps admin pages. Theme toggle (dark/light) with localStorage persistence. Lazy-loaded routes with code-splitting. Stub pages for Home, About, Projects, Uses, Contact, NotFound.
- **Tests:** 58 passing — no new PHP tests needed (frontend-only module).
- **Infra:** sinfat.com live. GitHub Actions auto-deploy on push. Module 8 pushed and deployed.

## Outstanding Items
- Prod `.env` needs `AI_PROVIDER` + API key set (anthropic or gemini) before AI works on sinfat.com
- Local uses Ollama (llama3.2) — `brew services start ollama` to run
- Node.js 20.17.0 locally — Vite warns it needs 20.19+
- Light mode colour palette needs refinement (dark tokens on body don't invert)
- md-editor-v3 removed from plan — caused Vite build issues on server

## Start Here
Load spec at `specs/module-09-static-pages.md` and run Module 9.

---

## Reference Files (read only when needed)
- Current module spec → `specs/module-09-static-pages.md`
- Last session note → `docs/sessions/2026-03-09-module-8-frontend-spa-foundation.md`
- Full history → `.pi/PROGRESS.md`
- Architecture decisions → `/Users/bernard/code/ai-learning/pi-vs-claude-code/docs/PORTFOLIO.md`
- Blog drafting → `/Users/bernard/code/ai-learning/pi-vs-claude-code/docs/summaries/`
