# Session State

## Where We Are
Building sinfat-portfolio — Laravel 12 API + Vue 3 SPA at sinfat.com.
Pi skills + agents scaffolded. Ready to write code.

## Next Task
**Module 1 — Infrastructure** (complete the server setup)
- [ ] Install Redis on VM
- [ ] GitHub Actions auto-deploy (`.github/workflows/deploy.yml`)
- [ ] Let's Encrypt SSL (`sudo certbot --nginx -d sinfat.com -d www.sinfat.com`)
- [ ] Supervisor for queue workers

Then: **Module 2 — Database Schema** → use Opus, dispatch `database-architect` agent.

## Key Facts
- Local: `https://sinfat.test` (Valet)
- Prod: `sinfat.com` — Oracle Cloud Arm A1 VM
- SSH: `ssh -i ~/.ssh/sinfat-portfolio.key ubuntu@<oracle-ip>`
- Repo: `bernardjbs/sinfat-portfolio` (private), code at `/Users/bernard/code/sinfat-portfolio`
- Pi skills + agents: `/Users/bernard/code/sinfat-portfolio/.pi/`
- Spec: `/Users/bernard/code/sinfat-portfolio/SPEC.md` (read only the current module)

## Module Status
| # | Module | Status |
|---|--------|--------|
| 1 | Infrastructure & Server Setup | 🔄 In progress (server live, Redis/SSL/GH Actions/Supervisor pending) |
| 2 | Database Schema | ⬜ |
| 3 | Authentication | ⬜ |
| 4 | API Contract | ⬜ |
| 5 | Blog (Admin + Public) | ⬜ |
| 6 | AI Integration | ⬜ |
| 7 | Guest Playground | ⬜ |
| 8 | Frontend SPA Foundation | ⬜ |
| 9 | Static Pages | ⬜ |
| 10 | Sitemap + SEO | ⬜ |
| 11 | Deploy Pipeline Polish | ⬜ |

## Reference Files (read only when needed)
- **Current module spec** → `/Users/bernard/code/sinfat-portfolio/specs/module-01-infrastructure.md`
- Architecture decisions → `/Users/bernard/code/ai-learning/pi-vs-claude-code/docs/PORTFOLIO.md`
- Full history → `/Users/bernard/code/ai-learning/pi-vs-claude-code/docs/PROGRESS.md`
- Blog drafting → `/Users/bernard/code/ai-learning/pi-vs-claude-code/docs/summaries/` (read only the relevant file, 3–14KB each)
  - `02-side-discussions-pi-vs-cc-llm-security.md` → Pi vs CC + LLM security posts
  - `06-infrastructure-journey.md` → Oracle Cloud / infra post
  - `07-redis-and-sse.md` → Redis + SSE posts
  - `08-agentic-workflow-planning.md` → agentic dev workflow post
  - `09-skills-and-agents.md` → skills and agents post
