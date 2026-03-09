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
| 9 | Static Pages | ✅ Complete |
| 10 | Sitemap + SEO | ⬜ |
| 11 | Deploy Pipeline Polish | ⬜ |

---

## Current State
- **Backend:** All API routes implemented. No backend changes since Module 7.
- **Frontend:** All public pages built with content: Home (hero + projects + blog), About (skills grid + education), Projects (4 cards with stories), Contact (links + availability), Uses (tool categories). Blog + Playground unchanged. AppLayout + AdminLayout in place. Theme toggle working.
- **Tests:** 58 passing.
- **Infra:** sinfat.com live. GitHub Actions auto-deploy on push. Modules 8+9 pushed and deployed.

## Outstanding Items
- Prod `.env` needs `AI_PROVIDER` + API key set (anthropic or gemini) before AI works on sinfat.com
- Local uses Ollama (llama3.2) — `brew services start ollama` to run
- Node.js 20.17.0 locally — Vite warns it needs 20.19+
- `public/cv.pdf` needs to be added for CV download links
- Email `bernard@sinfat.com` mailbox not yet configured
- Football Analytics description is placeholder — real story TBD
- Light mode works but could use further colour tuning

## Start Here
Load spec at `specs/module-10-sitemap-seo.md` and run Module 10.

---

## Reference Files (read only when needed)
- Current module spec → `specs/module-10-sitemap-seo.md`
- Last session note → `docs/sessions/2026-03-09-module-9-static-pages.md`
- Full history → `.pi/PROGRESS.md`
- Architecture decisions → `/Users/bernard/code/ai-learning/pi-vs-claude-code/docs/PORTFOLIO.md`
- Blog drafting → `/Users/bernard/code/ai-learning/pi-vs-claude-code/docs/summaries/`
