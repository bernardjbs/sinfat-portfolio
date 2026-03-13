# Session: 2026-03-13 — Blog Series & Deploy Rework

## What Was Done

### Deploy Pipeline Rework
- Gitignored `public/build/` and removed tracked build files from git
- Installed Node 22 on prod server
- Discovered VM only has 956MB RAM (not 12GB as documented) — added 2GB swap
- Replaced GitHub Actions with `scripts/deploy.sh` + `just deploy`
- Set up SSH config (`Host sinfat` in `~/.ssh/config`)
- Updated all living documents to reflect new deploy workflow

### Blog Infrastructure
- Changed blog category system from five categories to single `development` category
- Added category field to admin blog editor
- Updated blog-writer skill around AI journey narrative
- Fixed bug: `published_at` not set when publishing via editor save button
- Fixed UX: save button now preserves publish status (no unpublish/republish dance)

### Blog Series (Module 13)
- Created spec at `specs/module-13-blog-series.md` — 14 posts planned
- Drafted Posts 1–10 in local database
- Posts 1–5 published on sinfat.com
- Each post reviewed for tone, accuracy, and junior-developer accessibility
- Key edits: IndyDevDan credit, SQLite/MySQL explanation, honest deploy description, CSRF explanation for juniors

### Other
- Changed contact email to bernardjbs@yahoo.com
- Added living documents check (step 8) to next-session skill

## Decisions Made
- Blog topic is the AI development journey (not generic tutorials)
- Single category `development` instead of five categories
- `just deploy` instead of GitHub Actions (simpler, no timeout issues)
- Posts written chronologically from the perspective of when things happened
- Target audience includes junior developers — explain concepts, don't assume knowledge

## Outstanding Items
- Posts 11–14 still to draft
- Posts 6–10 not yet published on sinfat.com
- Neuron AI blog tools feature (give agent tools to read skills/DB)
- Email, og:image, light mode, Search Console, Supervisor (carried forward)
