*Resume with /user-next. Full state: docs/sessions/2026-11-19-notebook-snapshots-without-a-deploy.md; the 19 to 20 Nov work was done from the factory research session: `~/code/software-factory-research/docs/sessions/2026-11-20-blog-live-and-page-index.md`.*

## Pinned

- **Move this project to the shared documentation layout first** (ruled 21 Nov in the factory session). Trigger: this session's start, with no other session open here. Checklist: `~/.claude/skills/user-wrap-up/references/docs-layout.md`, "Moving a project in". Questions: ask in a factory session (`~/code/software-factory`). Remove this pin once moved. This repo is public: it gets a companion first, `~/code/sinfat-portfolio-companion/`, and its private records leave the repo (`~/.claude/reference/companion-folders.md`). Also bring over the four blog-site files kept in the factory's `docs/blog/` (port brief, post page design, site review, roadmap; layout record question 7).

- Every wrap-up checks and updates every document this project's wrap-up entry lists
  (`~/.claude/skills/user-wrap-up/references/workspaces/sinfat-portfolio.md`): the backlog, every spec's
  status line, the deploy guide, the production settings notes, `CLAUDE.md` and the project skills.
  After the last merge and push, the Git state below is rewritten to match. (Bernard, 2026-11-11)

## Next steps

0. Done 19 to 20 Nov (factory research session): row 14 (a draft keeps its publish date: `create --published-at`, the
   date in export and import, Publish keeps it, the admin shows it) and `just post-to-prod <id> --publish|--draft`
   (one required; `--publish` puts a new post or a draft live with its date) are built, reviewed and deployed
   (`bcf5363`). Record: `~/code/software-factory-research/docs/sessions/2026-11-20-blog-live-and-page-index.md`.
1. Bernard: check Oracle Cloud disk snapshots in the console (backlog row 15, due 2026-11-26).
2. Done 19 to 20 Nov: merged, pushed, deployed once each time; 11 snapshots serve; series parts 4 to 10 published
   (ids 20 to 26), each dated to its session. The merged branches are deleted.
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

- `main` at `372a738`, pushed; production deployed at `bcf5363` (the same code; later commits are docs). Plus the
  factory research session's wrap docs commit of 20 Nov (`c315e5f`), pushed.
- One-off production dump, untracked, on the Mac: `~/backups/sinfat-portfolio/2026-11-19-before-module-19.sql.gz`.

## Plans in flight

None. Each module's spec in `specs/` carries its own status. Nothing left running: no servers, watchers or
agents from this session.
