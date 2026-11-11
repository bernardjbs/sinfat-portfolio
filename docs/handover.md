*Resume with /user-next. Full state: docs/sessions/2026-11-12-factory-parts-2-3-and-sitemap.md.*

## Pinned

- Every wrap-up checks and updates every document this project's wrap-up entry lists
  (`~/.claude/skills/user-wrap-up/references/workspaces/sinfat-portfolio.md`): the backlog, every spec's
  status line, the deploy guide, the production settings notes, `CLAUDE.md` and the project skills.
  After the last merge and push, the Git state below is rewritten to match. (Bernard, 2026-11-11)

## Next steps

1. Backlog row 10, due 2026-11-26, on branch `feat/replace-github-models` (named for row 9, the branch's main
   work; row 10 is a small fix riding it, per Bernard's small-fixes rule): edit `justfile` so `deploy` pulls
   the code, then starts `scripts/deploy.sh`, and take `git pull` out of the script. It goes first because
   its trigger is this branch; the deploy after it is the first that will not need running twice when the
   script changes.
2. Backlog row 9, due 2026-11-25 (trigger fired): check prices and free tiers for the GitHub Models
   replacement via the research agent, put the choice to Bernard, then `PLAYGROUND_ENABLED=true`. The admin
   draft writer is broken until then.
3. Backlog row 1, due 2026-12-08 (trigger fired): help.md for the project skills that lack one, starting with
   `project-git-conventions`, via `/skills-builder`.
4. Then: module 15, Page Explainer (`specs/module-15-page-explainer.md`); Laravel mail (SMTP via Resend) with an
   AI health check that alerts when the provider stops answering.

Waiting on Bernard: the provider choice (row 9), every merge and push, and every deploy.

Owed work: `docs/backlog.md`. Deploy, server and PHP patching: `docs/deploy-guide.md`.

## Git state

- `main` at `19b1474`, pushed and deployed (sitemap built per request; parts 2 and 3 and the standalone
  post live).
- Branch `feat/replace-github-models`, not pushed: this wrap and backlog row 10's record. Merges with row 10's fix.

## Plans in flight

None. Each module's spec in `specs/` carries its own status. Nothing left running: no servers, watchers or
agents from this session.
