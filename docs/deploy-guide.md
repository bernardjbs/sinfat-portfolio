# Deploy Guide

How code gets from your machine to `sinfat.com`.

---

## Deploy

```bash
just deploy
```

That's it. This SSHes into the server, pulls the latest code (`git pull origin main`), then runs
`scripts/deploy.sh`, which:

1. `composer install --no-dev --optimize-autoloader`: install PHP deps
2. `npm install --prefer-offline`: install frontend deps (reuses existing node_modules)
3. `npm run build`: compile Vue/Tailwind assets with Vite
4. `php artisan migrate --force`: run pending migrations
5. `php artisan config:cache`: cache config
6. `php artisan route:cache`: cache routes
7. `php artisan view:cache`: cache Blade views
8. `sudo rm -f public/sitemap.xml`: remove any sitemap file. The sitemap is a route built from the database on each
   request, so it is never stale; nginx would serve a leftover file in its place
9. `sudo systemctl reload nginx`: reload Nginx gracefully

The pull happens in `just deploy`, before the script starts, because bash keeps reading the copy it started:
when the script pulled itself, a deploy that changed it ran the old one (2026-11-12, backlog row 10).

---

## Posting to Production

```bash
just post-to-prod <id> --publish   # or --draft; one is required
```

Sends local post `<id>` to production: a new slug is created, an existing one is backed up and updated.
`--publish` puts a new post or a draft live at once, keeping its date; `--draft` leaves it for the production
admin's Publish button. A post already published stays published either way. The sitemap lists a published
post at once (it is built on each request). Details: `.claude/skills/project-blog-writer/references/managing-posts.md`.

## Notebook Snapshots to Production

```bash
php artisan notebook:manage import --file=~/code/software-factory-research/blueprint/snapshots/<stamp>.html
just notebook-to-prod <stamp>
```

Snapshots live in the database (`notebook_snapshots`), so a new one needs no deploy. The import makes the
published copy locally (local file links unwrapped, links to snapshots stored here pointed at
`/notebook/<stamp>/`, links to others dropped) and refuses scripts and local paths. The send pipes those
bytes to production, which refuses a snapshot linking one it lacks, and replacing a stamp without `--force`.
Send a snapshot before the post that links it: `post-to-prod` refuses a post linking a missing snapshot.
The seven copies in `resources/notebook/` were loaded by a migration and stay as the record of their hand
correction (`specs/module-19-series-post-path.md`, Decision 2).

---

## Patching PHP

The deploy updates the app and its packages, never PHP itself. PHP comes from the ondrej/php PPA
(`deb.sury.org`), which Ubuntu's automatic security updates do not cover by default, so check after
every PHP security release:

```bash
ssh sinfat 'php -v | head -1; apt-cache policy php8.3-fpm | head -4'
ssh -t sinfat 'sudo apt-get update && sudo apt-get install --only-upgrade "php8.3*" && sudo systemctl restart php8.3-fpm'
```

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
git pull origin main
bash scripts/deploy.sh
```

The script refuses to run unless the server is at `origin/main`, so a forgotten pull stops it instead of
rebuilding the old code. It runs the migrations straight after `composer install`, before the frontend build,
because the pulled code is live from the pull onwards.

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
| Provider | Oracle Cloud (Arm A1, free tier), 956MB RAM + 2GB swap |
| OS | Ubuntu 22.04 |
| Web root | `/var/www/sinfat` |
| PHP | 8.3-FPM from the ondrej/php PPA; 8.3.36 as of 2026-11-11 |
| Node | 22, for Vite builds |
| DB | MySQL 8 |
| Cache | Redis (cache, sessions, queue) |
| Web server | Nginx |
| SSL | Cloudflare (Full mode) |
| DNS | Cloudflare |
