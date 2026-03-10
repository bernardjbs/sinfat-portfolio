# Deploy Guide

How code gets from your machine to `sinfat.com`.

---

## Automatic Deploy (GitHub Actions)

Every push to `main` triggers the deploy workflow.

**Flow:**
```
git push origin main
  → GitHub Actions triggers `.github/workflows/deploy.yml`
  → SSH into Oracle VM
  → git pull, composer install, migrate, cache, sitemap, nginx reload
  → Live in ~60 seconds
```

**What the action does:**
1. `git checkout .gitignore` — prevents server-side `.gitignore` drift
2. `git pull origin main` — pull latest code
3. `composer install --no-dev --optimize-autoloader` — install production deps
4. `php artisan migrate --force` — run pending migrations
5. `php artisan config:cache` — cache config
6. `php artisan route:cache` — cache routes
7. `php artisan view:cache` — cache Blade views
8. `php artisan sitemap:generate` — regenerate sitemap.xml
9. `sudo systemctl reload nginx` — reload Nginx gracefully

**Secrets required** (set in GitHub repo → Settings → Secrets):
- `SERVER_HOST` — Oracle VM IP (`<server-ip>`)
- `SERVER_SSH_KEY` — contents of `~/.ssh/sinfat-portfolio.key`

**Monitor:** Check the Actions tab at `github.com/bernardjbs/sinfat-portfolio/actions`

---

## Manual Deploy Fallback

If GitHub Actions fails or you need to deploy manually:

```bash
# SSH into the server
ssh -i ~/.ssh/sinfat-portfolio.key ubuntu@<server-ip>

# Deploy
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

**When to use manual deploy:**
- GitHub Actions runner is down
- You need to deploy a hotfix immediately
- You need to run a one-off artisan command on the server

---

## Pre-Deploy Checklist

Before pushing to `main`:
1. `php artisan test` — all tests pass
2. `npm run build` — frontend builds cleanly (if JS changed)
3. No `dd()`, `console.log`, or debug code in the diff
4. Commit message follows Conventional Commits format

---

## Rollback

If a deploy breaks the site:

```bash
ssh -i ~/.ssh/sinfat-portfolio.key ubuntu@<server-ip>
cd /var/www/sinfat

# Find the last good commit
git log --oneline -10

# Reset to it
git checkout <commit-hash> .
composer install --no-dev --optimize-autoloader
php artisan config:cache
php artisan route:cache
php artisan view:cache
sudo systemctl reload nginx
```

Then fix the issue locally, push a fix to `main`, and let auto-deploy bring the server back to head.

---

## Server Details

| Item | Value |
|------|-------|
| Provider | Oracle Cloud (Arm A1, free tier) |
| OS | Ubuntu 22.04 |
| IP | `<server-ip>` |
| Web root | `/var/www/sinfat` |
| PHP | 8.3-FPM |
| DB | MySQL 8 |
| Cache | Redis |
| Web server | Nginx |
| SSL | Cloudflare (Full mode) |
| DNS | Cloudflare |
