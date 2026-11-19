# Session: notebook snapshots without a deploy, the deploy's pull, a database backup
**Date:** 2026-11-19 (AWST). Ad-hoc, no plan; module 19 spec, Decision 2 (`specs/module-19-series-post-path.md`).

## What was done
- **Backlog row 12, snapshots without a deploy** (`3c49c73`): snapshots moved from `resources/notebook/` files to a
  `notebook_snapshots` table (stamp, html), served at the same `/notebook/<stamp>/` by `NotebookController` through
  `NotebookService`. A data migration loads the 7 committed copies as they are; the files stay in git as the record.
  - `php artisan notebook:manage import --file=<original>` does the link rewrite module 18 did by hand (`file://`
    unwrapped, links to stored snapshots pointed at `/notebook/<stamp>/`, others dropped) and refuses a title not
    naming the stamp, any `<script`, `file://` or `/Users/`, a leftover `<stamp>.html` link, a link to a missing
    snapshot, and an existing stamp without `--force`. `export` writes the page alone to stdout.
  - `just notebook-to-prod <stamp>` (`scripts/notebook-to-prod.sh`) pipes the local export over ssh to a `--prepared`
    import, so production holds the same bytes.
  - `blog:manage import` (so `post-to-prod`) refuses a post linking a snapshot missing there.
  - Snapshot pages send `Content-Security-Policy: script-src 'none'; object-src 'none'; base-uri 'none'`.
- **Backlog row 10, the deploy's pull** (same commit): `just deploy` runs `git pull origin main` then
  `bash scripts/deploy.sh`; the script no longer pulls, so a changed script runs its new copy.
- **Docs** (`0ccad4a`): deploy guide (steps, "Notebook Snapshots to Production"), `CLAUDE.md` Commands, module 18
  and 19 specs, backlog rows 10 and 12 DONE; skills through `/skills-builder`: `project-portfolio-context` (route,
  snapshot paragraph, tables, deploy), `project-blog-writer` `managing-posts.md` (missing-snapshot refusal),
  `project-git-conventions` (two deploy lines).
- **Blog voice** (`3f025d3`, `1f3c638`, through `/skills-builder`): two Don't lines in `project-blog-writer`: cut
  details that say nothing important; say what was found, not who did each step.
- **Factory research folder** `CLAUDE.md` send step (its commit `b99549d`, ruling 4): import, then
  `just notebook-to-prod`, before the post; fails until this branch is deployed.
- **Database backup** (row 13): one-off production dump `~/backups/sinfat-portfolio/2026-11-19-before-module-19.sql.gz`
  (71449 bytes, on the Mac, untracked), proven by a restore. Rows 13 updated and 15 added (`docs/backlog.md`).
- Spec 19's "What changes" table: stale rows marked done, a duplicate row dropped (wrap commit).

## Rulings (Bernard)
- Decision 2 scope: "1 yes, 2 keep, 3 one branch, 4 you edit, 5 record": database, the 7 files kept as the record,
  one branch carrying row 10, I edit the factory folder's `CLAUDE.md`, backups recorded (row 13).
- The four skill edits applied and committed; the two voice lines in his approved wording.
- "lets do 1": measure the server's backups. Then "1 yes, 2 record, 3 I'll check, record 3 with a trigger": one-off
  copy taken; regular backups recorded (row 13); the Oracle Cloud console check is his (row 15).
- The research session's publish-date row renumbered 13 to 14, at its request.

## Decisions (mine, reported)
- Files on the server rejected: the ssh user writes and the web server reads, the sitemap's ownership trap.
- Export errors go to stderr, and the send script checks the export before piping, so an error never travels as a page.
- Pint not run as a gate: 30+ existing files already fail it (no action required: not a project gate).

## Measured
- `php artisan test`: 153 passed, 3 skipped (554 assertions), re-run at wrap.
- Golden check: `prepare` reproduces the hand copies; part 3's `2026-11-11-2343` identical, the first six differ only
  on the hand-corrected "shut down after 3 months" line.
- 10 mutations, each turned its test red; each restore grep-checked.
- End-to-end send against the test database (`SINFAT_REMOTE="env DB_DATABASE=sinfat_portfolio_test sh -c"`):
  checksums match, a repeat refused without `--force`, an unknown stamp refused before sending, 0 bytes on stdout
  on a failed export; the post guard refused a missing snapshot.
- Server backups (read-only ssh): none. No crontab for ubuntu or root, no timer, no backup folder; the system's own
  jobs listed as the control. MySQL data 210M, disk 24% used. Oracle volume backups unmeasured (console only).
- Restore of the dump: 19 posts, 2 series, 1 user, as production.

## Production
- Nothing changed on production this session. Reads only: the backup ssh look and the `mysqldump`.

## Traps and where they now live
- A failed export's message went down the pipe and production refused it for a misleading reason: fixed in code
  (stderr, the script's pre-check) and `NotebookManageTest`.
- The local `sinfat` database user cannot create a database, so a restore check goes into `sinfat_portfolio_test`
  and `php artisan test` resets it: backlog row 13.
- zsh does not word-split an unquoted `$var`: already in `~/.claude/rules/verify-before-consequential-done.md`.
- The research session's commit `ae5b33d` swept my uncommitted backlog edits (shared tree): it agreed to commit named
  files only and not while this repo is being worked on; the rule is in `~/.claude/rules/multi-agent-orchestration.md`.

## After the wrap: the branch reviewed and fixed (from the factory research session)
- Reviewed before merge by this repo's `pr-reviewer` (read-only, production and network off limits): approve after
  fixes, nothing critical. Tests at review: 153 passed, 3 skipped; 15 mutations each turned a test red.
- All six findings fixed on Bernard's word ("fix all", "fix here"), test first, in `c8cde50` and `a1fb95a`:
  1. the snapshot page header also blocks loading, framing and form submission, and an import refuses an
     `http-equiv` meta tag (none of the 39 existing snapshots, here or in the research folder, hold one);
  2. `notebook-to-prod.sh` passes nothing but `--force` to the server's shell;
  3. `deploy.sh` stops unless the server is at `origin/main` (proved behind: exit 1, level: exit 0, in a throwaway
     repo); the deploy guide's manual steps pull first;
  4. migrations run straight after `composer install`, so the live code never queries a missing table during the build;
  5. the missing-snapshot check also catches `/notebook/<stamp>` without the trailing slash;
  6. the send script's first tests (`NotebookToProdScriptTest`, a recording stub for the server), each proved by
     breaking the script.
- Tests after: 159 passed, 3 skipped, exit 0.
- Production backup confirmed before the deploy: the dump above is complete ("Dump completed"), 14 tables, taken 13:27.

## Added later the same day: row 14 built (factory research session)

- A draft can carry its publish date: `blog:manage create --published-at=<ISO 8601>` (strict: "tomorrow" is refused),
  `published_at` in the export header, import takes it only for a new post or a draft, and the admin Publish button
  keeps a date already set (it used to overwrite it with now()).
- Tests first, each break proved red (10 mutations). `pr-reviewer`: approve after fixes; fixed two missing tests and the
  loose date reading. Bernard's rulings on the other three: refuse a future date (a typo would go live dated ahead;
  "a"), a locally published post's date travels so both sites match ("a"), the admin list and editor show the date
  ("a"). Tests after: 172 passed, 3 skipped, exit 0.

## Still open
- Bernard: Oracle Cloud disk snapshots (row 15, due 2026-11-26, before the deploy); then merge, push and deploy
  `feat/module-19-notebook-snapshots`. After the deploy, check `/notebook/<stamp>/` serves each of the 7 snapshots and
  the deploy ran once (row 10's proof).
- Row 13 regular backups (needs scoping, due 2026-12-03); row 9 AI provider (due 2026-11-25); row 14 publish date
  (2026-12-08); row 1 skill help files (2026-12-08); module 15.
- Blog sources relied on: `~/code/software-factory-research/notebook/snapshots/` (read, not edited) and its
  `CLAUDE.md` (edited on Bernard's ruling 4).
