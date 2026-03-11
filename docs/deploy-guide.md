# Deploy Guide

How code gets from your machine to `sinfat.com`.

---

## Deploy

```bash
just deploy
```

That's it. This SSHes into the server and runs `scripts/deploy.sh`, which:

1. `git pull origin main` — pull latest code
2. `composer install --no-dev --optimize-autoloader` — install PHP deps
3. `npm install --prefer-offline` — install frontend deps (reuses existing node_modules)
4. `npm run build` — compile Vue/Tailwind assets with Vite
5. `php artisan migrate --force` — run pending migrations
6. `php artisan config:cache` — cache config
7. `php artisan route:cache` — cache routes
8. `php artisan view:cache` — cache Blade views
9. `php artisan sitemap:generate` — regenerate sitemap.xml
10. `sudo systemctl reload nginx` — reload Nginx gracefully

---

## SSH Config

The deploy command uses an SSH alias. Add this to `~/.ssh/config`:

```
Host sinfat
    HostName <server-ip>
    User ubuntu
    IdentityFile ~/.ssh/sinfat-portfolio.key
```

Then `ssh sinfat` or `just ssh` connects directly.

---

## Manual Deploy

If you need to run steps individually:

```bash
just ssh
cd /var/www/sinfat
bash scripts/deploy.sh
```

Or run individual commands as needed.

---

## Pre-Deploy Checklist

Before pushing to `main`:
1. `php artisan test` — all tests pass
2. No `dd()`, `console.log`, or debug code in the diff
3. Commit message follows Conventional Commits format

---

## Rollback

If a deploy breaks the site:

```bash
just ssh
cd /var/www/sinfat

# Find the last good commit
git log --oneline -10

# Reset to it
git checkout <commit-hash> .
composer install --no-dev --optimize-autoloader
npm install --prefer-offline
npm run build
php artisan config:cache
php artisan route:cache
php artisan view:cache
sudo systemctl reload nginx
```

Then fix the issue locally, push a fix to `main`, and `just deploy`.

---

## Server Details

| Item | Value |
|------|-------|
| Provider | Oracle Cloud (Arm A1, free tier) |
| OS | Ubuntu 22.04 |
| Web root | `/var/www/sinfat` |
| PHP | 8.3-FPM |
| DB | MySQL 8 |
| Cache | Redis |
| Web server | Nginx |
| SSL | Cloudflare (Full mode) |
| DNS | Cloudflare |
