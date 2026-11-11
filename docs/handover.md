*Resume with /user-next. Full state: docs/sessions/2026-11-11-php-patch-and-post-to-prod.md.*

## Pinned

- Every wrap-up checks and updates every document this project's wrap-up entry lists
  (`~/.claude/skills/user-wrap-up/references/workspaces/sinfat-portfolio.md`): the backlog, every spec's
  status line, the deploy guide, the production settings notes, `CLAUDE.md` and the project skills.
  After the last merge and push, the Git state below is rewritten to match. (Bernard, 2026-11-11)

## Next steps

1. `just deploy` (Bernard): takes the broken playground off sinfat.com and ships the tab title fix.
2. Backlog row 9, due 2026-11-25: choose the AI provider that replaces GitHub Models, then switch the
   playground back on (`PLAYGROUND_ENABLED=true`).
3. Next session: part 2 of the factory series, on `feat/factory-series-part-2` (brief from `~/code/software-factory-research`).
4. Then: module 15, Page Explainer (`specs/module-15-page-explainer.md`); Laravel mail (SMTP via
   Resend) with an AI health check that alerts when the provider stops answering.

Owed work: `docs/backlog.md`. Deploy, server and PHP patching: `docs/deploy-guide.md`.

## Git state

- `main` at `adc2e92`, pushed, not yet deployed.
- Branch `feat/factory-series-part-2`, not merged: carries the module 16 and 17 status fixes and this pin. Next session continues on it with part 2 of the factory series, then merges and pushes everything together.

## Plans in flight

None. Each module's spec in `specs/` carries its own status.
