---
name: git-conventions
description: Git workflow for sinfat-portfolio. Use when creating branches, writing commit messages, or deciding when to commit. Covers branch naming (feat/fix/refactor/chore), Conventional Commits format, per-module workflow, and deploy trigger rules.
---

# Git Conventions — sinfat-portfolio

Use this skill when creating branches, writing commit messages, or deciding when to commit. This defines the git workflow for the sinfat-portfolio project.

---

## Branch Naming

```
feat/*      → new features           feat/blog-post-model
fix/*       → bug fixes              fix/sse-nginx-buffering
refactor/*  → no behaviour change    refactor/extract-ai-service
chore/*     → config, deps, tooling  chore/install-neuron-ai
docs/*      → documentation only     docs/update-readme
test/*      → tests only             test/blog-controller-feature
```

**Examples by module:**
```
feat/module-1-redis-setup
feat/module-2-database-schema
feat/module-3-auth
feat/module-4-api-routes
feat/module-5-blog-crud
feat/module-6-ai-streaming
feat/module-7-guest-playground
feat/module-8-spa-foundation
feat/module-9-static-pages
feat/module-10-sitemap-seo
feat/module-11-deploy-pipeline
```

---

## Commit Format — Conventional Commits

```
<type>: <short description in present tense>

[optional body — why, not what]
```

**Types:**
- `feat:` — new feature or functionality
- `fix:` — bug fix
- `refactor:` — code change that doesn't fix a bug or add a feature
- `chore:` — build tools, dependencies, config
- `docs:` — documentation only
- `test:` — adding or updating tests
- `style:` — formatting, whitespace (no logic change)

**Good commit messages:**
```
feat: add BlogPost model with published scope and slug generation
fix: add X-Accel-Buffering header to stop Nginx buffering SSE
refactor: extract rate limiting logic into GuestUsageService
chore: install inspector-apm/neuron-ai
test: add feature test for public blog listing endpoint
feat: add GuestRateLimit middleware with 24-hour Redis decay
feat: add streaming chat interface to AdminBlogEditor
fix: handle EventSource onerror to prevent infinite reconnects
chore: configure Tailwind with terminal colour palette
feat: add PlaygroundController with guest API key session storage
```

**Bad commit messages (don't do this):**
```
❌ update files
❌ WIP
❌ fixes
❌ blog stuff
❌ working on module 5
```

---

## When to Commit

**One logical unit of work per commit.** A commit should leave the codebase in a working state.

**Commit after:**
- A migration is created and runs cleanly
- A model is created with its relationships and scopes
- A controller + service pair is complete (both together, one commit)
- A Vue component is built and renders correctly
- A test suite passes for a feature

**Do NOT commit:**
- Partially built features with broken tests
- Debug `dd()` or `console.log` statements
- Commented-out code
- Merge conflicts unresolved

**Before every commit:**
```bash
php artisan test              # all tests pass
php artisan route:list        # routes look correct (if changed)
# review: php artisan tinker  # spot-check if needed
```

---

## Workflow per Module

```
1. git checkout main && git pull
2. git checkout -b feat/module-X-description
3. Build the feature (dispatch agents as needed)
4. php artisan test  ← must pass
5. git add -p  ← stage carefully, review each hunk
6. git commit -m "feat: ..."
7. (repeat 3–6 for additional logical units in the module)
8. git checkout main && git merge feat/module-X-description
9. git push origin main  ← triggers GitHub Actions deploy
10. git branch -d feat/module-X-description
```

---

## Rules

1. **NEVER commit directly to `main`** — always use a feature branch. No exceptions. Not for docs, not for chores, not for one-line fixes. Every commit goes through a branch → merge → push flow.
2. **One logical unit per commit** — not one commit per module (modules are too big)
3. **Tests pass before every commit** — `php artisan test` is non-negotiable
4. **Stage with `git add -p`** — review every hunk before committing, catch debug code
5. **Merge to main before pushing** — squash if the feature branch has noisy WIP commits
6. **Push to main triggers deploy** — GitHub Actions auto-deploys to Oracle VM

---

## Squashing WIP Commits Before Merge

If the feature branch has messy WIP commits, squash before merging:

```bash
# From the feature branch
git rebase -i main

# In the editor: mark WIP commits as 'squash' or 's'
# Write the final clean commit message

# Then merge
git checkout main
git merge feat/module-X
```

---

## GitHub Actions Deploy Trigger

Pushing to `main` automatically:
1. SSH into Oracle VM
2. `git checkout .gitignore` (prevent server divergence)
3. `git pull origin main`
4. `composer install --no-dev --optimize-autoloader`
5. `php artisan migrate --force`
6. `php artisan config:cache && route:cache && view:cache`
7. `sudo systemctl reload nginx`

**Implication:** Only push to `main` when the code is production-ready.

---

## Git Ignore Notes

The repo is private. Still, never commit:
- `.env` (it's in `.gitignore` by default — verify)
- `storage/logs/*`
- `node_modules/`
- `.pi/agent-sessions/` (ephemeral session files)
