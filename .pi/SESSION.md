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
| 10 | Sitemap + SEO | ✅ Complete |
| 11 | Deploy Pipeline Polish | ✅ Complete |
| 12 | Blog Polish | ✅ Complete |

**All 11 modules complete. 🎉**

---

## Current State
- **Backend:** All API routes implemented. All services, controllers, resources built.
- **Frontend:** Full SPA — Home, About, Blog, AI Chat, Playground, Admin. Per-route meta tags. Theme toggle.
- **AI:** Live on prod via GitHub Models (gpt-4o-mini). Free, no billing required.
- **Tests:** 65 passing + 3 live AI tests (auto-skip on ollama).
- **Infra:** sinfat.com live. GitHub Actions auto-deploy on push to main. Sitemap generated on deploy. Redis for session/cache/queue. PHP 8.3 + Nginx + MySQL 8.
- **Deploy:** Push to main → live in ~80 seconds. Manual fallback documented in `docs/deploy-guide.md`.

## Outstanding Items
- Email `bernard@sinfat.com` mailbox not configured
- Football Analytics description is placeholder — real story TBD
- `og:image` not set — no images on the site yet
- Light mode could use further colour tuning
- Submit sitemap to Google Search Console after launch
- Supervisor for queue workers not yet set up on prod

## What's Next
All modules complete. Write first blog posts using the blog-writer skill.
- Submit sitemap to Google Search Console
- Polish light mode colours
- Add og:image meta tags

---

## Reference Files (read only when needed)
- Deploy guide → `docs/deploy-guide.md`
- Production env reference → `docs/production-env.md`
- Last session note → `docs/sessions/2026-03-10-module-11-deploy-pipeline.md`
- Full history → `.pi/PROGRESS.md`
- Architecture decisions → `/Users/bernard/code/ai-learning/pi-vs-claude-code/docs/PORTFOLIO.md`
