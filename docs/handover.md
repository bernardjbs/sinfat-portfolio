*Resume with /user-next. Full state: docs/sessions/2026-11-19-notebook-snapshots-without-a-deploy.md.*

## Pinned

- Every wrap-up checks and updates every document this project's wrap-up entry lists
  (`~/.claude/skills/user-wrap-up/references/workspaces/sinfat-portfolio.md`): the backlog, every spec's
  status line, the deploy guide, the production settings notes, `CLAUDE.md` and the project skills.
  After the last merge and push, the Git state below is rewritten to match. (Bernard, 2026-11-11)

## Next steps

1. Bernard: check Oracle Cloud disk snapshots in the console (backlog row 15, due 2026-11-26), before the deploy.
2. Bernard: merge `feat/module-19-notebook-snapshots` to `main`, push (`ALLOW_GIT_PUSH=1`), `just deploy`. Then check
   each of the 7 snapshots serves at `/notebook/<stamp>/` (the data migration loads them) and that one `just deploy` finished
   through the nginx reload with no second run (backlog row 10's proof). Until then the factory session's snapshot send fails.
3. The first step for the next session (steps 1 and 2 are Bernard's, in deploy order): backlog row 9, due
   2026-11-25 (trigger fired): price the GitHub Models replacement via the `doc-researcher` agent, put
   the choice to Bernard, then `PLAYGROUND_ENABLED=true`. The admin draft writer is broken until then.
4. Backlog row 13, due 2026-12-03: scope regular database backups once row 15 is answered.
5. Backlog row 1, due 2026-12-08: help.md for the project skills lacking one, via `/skills-builder`. Row 14 (a draft
   carries its publish date) due 2026-12-08, before the first catch-up post is published.
6. Then: module 15, Page Explainer (`specs/module-15-page-explainer.md`).

Waiting on Bernard: row 15, every merge, push and deploy, and the provider choice (row 9).

Owed work: `docs/backlog.md`. Deploy, server and PHP patching: `docs/deploy-guide.md`.

## Git state

- `main` at `19b1474`, pushed and deployed.
- Branch `feat/module-19-notebook-snapshots`, not pushed: rows 10 and 12 (`3c49c73`), their docs (`0ccad4a`), two
  blog-voice lines, and this wrap. Carries the earlier `temp-branch` wrap commits.
- One-off production dump, untracked, on the Mac: `~/backups/sinfat-portfolio/2026-11-19-before-module-19.sql.gz`.

## Plans in flight

None. Each module's spec in `specs/` carries its own status. Nothing left running: no servers, watchers or
agents from this session.
