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

## Outstanding
- Deploy, then a live GitHub Models check; server reboot (blocked for the agent, Bernard's)
- Local playground crashes on Ollama (null API key); pre-existing, local only; fix-or-record put to Bernard
