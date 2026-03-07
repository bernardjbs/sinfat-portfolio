# Session State

## How to Start
```bash
cd /Users/bernard/code/sinfat-portfolio
pi
```
Read `.pi/MORNING_BRIEF.md` first — it tells you where you are and what to do next.
Then use this file for reference pointers and module status only.

---

## Key Facts
- Local: `https://sinfat.test` (Valet)
- Prod: `sinfat.com` — Oracle Cloud Arm A1 VM
- SSH: `ssh -i ~/.ssh/sinfat-portfolio.key ubuntu@<oracle-ip>`
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
| 6 | AI Integration | ⬜ |
| 7 | Guest Playground | ⬜ |
| 8 | Frontend SPA Foundation | ⬜ |
| 9 | Static Pages | ⬜ |
| 10 | Sitemap + SEO | ⬜ |
| 11 | Deploy Pipeline Polish | ⬜ |

---

## Reference Files (read only when needed)
- Current module spec → `specs/module-06-ai-integration.md`
- Last session note → `docs/sessions/2026-03-07-module-5-blog.md`
- Full history → `.pi/PROGRESS.md`
- Architecture decisions → `/Users/bernard/code/ai-learning/pi-vs-claude-code/docs/PORTFOLIO.md`
- Blog drafting → `/Users/bernard/code/ai-learning/pi-vs-claude-code/docs/summaries/`
  - `02-side-discussions-pi-vs-cc-llm-security.md` → Pi vs CC + LLM security posts
  - `06-infrastructure-journey.md` → Oracle Cloud / infra post
  - `07-redis-and-sse.md` → Redis + SSE posts
  - `08-agentic-workflow-planning.md` → agentic dev workflow post
  - `09-skills-and-agents.md` → skills and agents post
  - `10-module-1-redis-ssl-deploy-supervisor.md` → Redis, SSL, GitHub Actions, Supervisor post
