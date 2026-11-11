# Session: PHP patch, package update, post-to-prod
**Date:** 2026-11-11

## What Was Done
- Audit: production ran PHP 8.3.30; PHP's advisories of 2026-10-21 (CVE-2026-109618, CVE-2026-109619,
  critical RCE) are fixed in 8.3.36. Upgraded on the server (`apt-get install --only-upgrade "php8.3*"`,
  configs kept, php8.3-fpm restarted); 8.3.36 confirmed for CLI and FPM; public pages and APIs 200
- PHP 8.3.31 to 8.3.36 change log read before upgrading: 29 entries, all security or crash fixes
- Packages: `composer update` within constraints, resolution pinned to PHP 8.3.36 (`config.platform`):
  78 packages, Laravel 12.53.0 to 12.72.0, Neuron AI 3.1.3 to 3.17.2; `composer audit` 54 to 0. Merged
  and pushed (`6bef05a`), not deployed
- Laravel's critical TokenGuard bypass (2026-10-23) does not apply: no `token` guard configured
- `post-to-prod`: `blog:manage export` writes a header with the post's fields; new `blog:manage import`
  (match by slug, draft on create, backup on update; refuses missing series, a part held by another
  post, a shrink over half without `--force`); `scripts/post-to-prod.sh` and `just post-to-prod <id>`
  pipe the export to the server's import over ssh stdin; publishing or unpublishing rebuilds the sitemap
  (path is `config('app.sitemap_path')`, so tests never write `public/sitemap.xml`)
- Draft #46, "My server was three weeks behind a critical PHP patch", standalone, local only

## Measured
- New tests: 9 import/export, 1 sitemap on publish; every guard mutation-proved (10 of 10 red)
- `post-to-prod` end to end on this Mac with a local shell as the "server": create into the test
  database and update of #46; content checksum identical to the source
- `php artisan test`: 128 passed, 3 skipped
- Neuron AI 3.17: real streamed reply via Ollama; all imported classes resolve (a fake class reports missing)

## Later the same day
- Bernard deployed, rebooted the server (back in about 80 s; site and SSH checked from outside), ran
  `just post-to-prod 46` and published it: part 15 of `building-sinfat`, live, in the feed
- Sitemap rebuild on publish failed on production: `public/sitemap.xml` was `ubuntu:ubuntu`, PHP-FPM runs
  as `www-data`. `scripts/deploy.sh` now writes it as `www-data` (`66adda9`); Bernard ran the new step
  by hand first: file `www-data`, part 15 listed
- Live AI check: the playground answered `{"error":"Generation failed"}`. Server log: "Connection
  refused" for `models.inference.ai.azure.com`; last completed AI request 2026-03-14. GitHub's docs
  (docs.github.com/en/github-models, via the research agent): GitHub Models fully retired 2026-07-30
- Playground switched off behind `PLAYGROUND_ENABLED` (default off): requests answer 503, the nav hides
  the link, the page says it is offline; checked on sinfat.test both ways. Branch `fix/playground-offline`
- Registered this repo with `/user-wrap-up` (`~/.claude/skills/user-wrap-up/references/workspaces/sinfat-portfolio.md`); the handover's
  outstanding items moved to backlog rows 5 to 8, server facts to `docs/deploy-guide.md`;
  `PORTFOLIO.md` ruled a closed record
- `php artisan test`: 134 passed, 3 skipped

## Close of session
- Merged and pushed to `main` (`adc2e92`), deployed by Bernard: the playground switch (sinfat.com
  measured: status `enabled: false`, page shell `playground=off`, generate 503) and every tab title
  using "·" instead of an em dash (backlog row 4, done)
- Docs: the handover is now a pointer with a `## Pinned` rule (every wrap-up checks every document
  the wrap-up entry lists); backlog rows 5 to 9; `docs/deploy-guide.md` (sitemap as `www-data`,
  `post-to-prod`, patching PHP, server facts); module 16 and 17 spec status corrected (they still said
  "awaiting merge" a day after both merged and deployed)
- Wrap-up entry gained a `spec-status` check: adding `specs/` to the stale-phrasing sweep alone did not
  catch "awaiting merge" (measured: all clear); the dedicated check flagged both lines, clean after the fix
- Branch `feat/factory-series-part-2` carries the spec fixes, the pin and this wrap; per Bernard, small
  fixes ride on the next work branch and merge with it
- `project-blog-writer`: one voice line, routine work (audits, upgrades, patches) reads matter-of-fact,
  from Bernard's edits to the PHP post
- Wrap applied the pinned rule: module 11 and 12 specs said "ready to build" (completed 2026-03-10),
  `docs/production-env.md` and the `project-portfolio-context` / `project-neuron-ai-patterns` skills
  described GitHub Models as live or pointed at the old handover; all corrected. Module 15 spec gained a
  status line; module 18's step 4 now uses `just post-to-prod`

## Traps and where they now live
- The deploy never patches PHP; production sat on 8.3.30 three weeks past a critical fix:
  `docs/deploy-guide.md`, "Patching PHP"
- `public/sitemap.xml` owned by the deploy user stopped the publish-time rebuild silently (the error is
  caught and logged): `docs/deploy-guide.md` step 9
- GitHub Models died with nothing noticing for months (last success 2026-03-14): backlog row 9 and the
  planned AI health check in the handover's next steps
- The wrap-up's stale-phrasing sweep does not know "awaiting merge": the entry's `spec-status` check
- Branch checkouts that swap skill files trip the skills hook as if edited (false alarm, nothing to
  undo): session record only

## Outstanding
- Backlog row 9: choose the provider that replaces GitHub Models (admin draft writer also broken)
- Local playground crash on Ollama: fixed (`ca8dc55`)
