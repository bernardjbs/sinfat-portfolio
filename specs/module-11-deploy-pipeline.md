## Module 11 — Deploy Pipeline Polish
> 🟢 Sonnet

### Goal
Deployment is automated, reliable, and zero-downtime where possible.

### Tasks
- [x] Verify GitHub Actions deploy works end to end
- [x] Add `.env.production` documentation (what keys are required)
- [x] Add `php artisan sitemap:generate` to deploy script
- [x] Test full deploy: push → action triggers → site updates
- [x] Document manual deploy fallback

### Required Production `.env` Keys
```
APP_NAME=sinfat
APP_ENV=production
APP_DEBUG=false
APP_URL=https://sinfat.com
APP_KEY=

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=sinfat_portfolio
DB_USERNAME=sinfat
DB_PASSWORD=

CACHE_DRIVER=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis

REDIS_HOST=127.0.0.1
REDIS_PORT=6379

AI_PROVIDER=github
GITHUB_MODELS_KEY=github_pat_...
GITHUB_MODELS_MODEL=gpt-4o-mini

ADMIN_EMAIL=
ADMIN_PASSWORD=
```

### Manual Deploy Fallback
```bash
ssh -i ~/.ssh/sinfat-portfolio.key ubuntu@<oracle-ip>
cd /var/www/sinfat
git pull origin main
composer install --no-dev --optimize-autoloader
php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan sitemap:generate
sudo systemctl reload nginx
```

### Acceptance Criteria
- [x] Push to main → GitHub Action → live site updated within 2 minutes
- [x] All `.env` keys documented
- [x] Manual deploy fallback documented and tested
- [x] `https://sinfat.com` serving latest code after deploy

### Dependencies
All modules

---

## Build Order Summary

```
Module 1  → Infrastructure (do first — foundation)
Module 2  → Database Schema 🔴
Module 3  → Authentication
Module 4  → API Contract 🔴
Module 8  → Frontend SPA Foundation (parallel with 3/4)
Module 5  → Blog Backend + Frontend
Module 6  → AI Integration 🔴
Module 7  → Guest Playground 🔴
Module 9  → Static Pages
Module 10 → Sitemap + SEO
Module 11 → Deploy Pipeline Polish
```

🔴 = Use Opus for these modules

---

## Pi Workflow Reference

**Skills to load:** `portfolio-context` · `laravel-conventions` · `vue-conventions` · `terminal-aesthetic` · `neuron-ai-patterns` · `git-conventions`

**Agents available:** `backend-developer` · `frontend-developer` · `database-architect` · `security-reviewer` · `test-writer` · `git-assistant` · `pr-reviewer`

**Per-feature pattern:**
```
git checkout -b feat/module-X-description
→ dispatch relevant agent(s)
→ review + test
→ dispatch git-assistant → commit
→ dispatch pr-reviewer → merge
→ push to main → auto-deploy
```

---

*Created: 2026-03-06*
*Status: Planning complete — ready to build*
