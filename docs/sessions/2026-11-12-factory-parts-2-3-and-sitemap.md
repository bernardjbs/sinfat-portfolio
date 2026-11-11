# Session: factory parts 2 and 3, a standalone post, the sitemap as a route
**Date:** 2026-11-11 to 2026-11-12 (AWST). Ad-hoc, no plan.

## What was done
- **Part 2** (post #47, "Why merge is the last thing I'll automate"): drafted from
  `~/code/software-factory-research/docs/blog-post-02-brief.md` (`ec59457`), then the body and excerpt replaced
  from that project's `blog-post-02-draft.md` after an export backup (`2c66379`). Merged `856c9d0`
- **Editor preview:** notebook links open in a new tab, as the post page already did server side (`4f9483b`)
- **Part 3** (#48, "Before deciding anything, I compared my factory with two others", part 3) and a
  **standalone post** (#49, "I can't trust everything I download: a scanner for hidden text", no series, so no
  spec) loaded from `blog-post-03-draft.md` and `blog-post-04-draft.md`: title, excerpt and body below the
  front matter, `ai_model` `claude-opus-5-5` (read from the factory session's log). Nothing to export first:
  neither existed
- **Snapshot `2026-11-11-2343`** published for part 3 (`3af8bf5`, merged `09979fd`): 4 `file://` links unwrapped,
  7 links to published snapshots pointed at `/notebook/<stamp>/`, 7 links to unpublished snapshots
  (`2026-11-10-2015` to `2026-11-11-2313`) dropped so none 404s; a replay check proved nothing else changed;
  original checksum unchanged; text read for emails, paths, client names and secrets (only the generic mentions
  already in published snapshot `2026-11-10-1220`)
- **Sitemap as a route** (`77b3b4d`, merged `19b1474`): the sitemap URL is built from the database on every
  request (`SitemapController`, `SitemapService`), like the RSS feed. Removed: the `sitemap:generate` command, its
  config path and test override, the rebuild in the publish toggle. The deploy now deletes any
  sitemap file left in `public/`, which nginx would serve in the route's place; a test refuses a leftover file. Deploy
  guide, module 10 and 18 specs and three skill lines (through `/skills-builder`) updated
- **Backlog row 10** recorded: the deploy script pulls itself (see Traps)

## Rulings (Bernard)
- Part 2's "15 takeaways" and "three tasks" stay: the brief's Figures rule now allows the project's own counts
- Draft text from the factory project is loaded as it is; this repo checks format and rendering only
- Merge and push on his word, with the push hook's escape hatch (`ALLOW_GIT_PUSH=1`); branches deleted after merge
- Sitemap: a durable fix, not another rebuild call ("this is the second time it happens"); built per request
- The three skill wording edits approved; the Warp quote ("it's just overkill for an individual", 21:38)
  confirmed by ear before part 3 went live
- Deploy self-update trap: fix on the next branch, recorded with a trigger (row 10)
- Wrap commit rides the next work branch, `feat/replace-github-models`

## Decisions (mine, reported)
- Part 2's notebook links stay relative: on production they resolve to `https://sinfat.com/notebook/<stamp>/`
  and still work on sinfat.test
- Links to unpublished snapshots dropped rather than unwrapped, which would leave a bare "Snapshot" word
- Sitemap rejected alternatives: rebuild on every save (misses paths; `post-to-prod`'s import runs as the deploy
  user over ssh, the ownership trap again); a timed rebuild (stale between runs, no scheduler on the server)

## Production (run by Bernard, measured from outside)
- Deploys and `post-to-prod` 47, 48, 49; published #47 (2026-11-11 22:01), #48 and #49 (2026-11-12 00:21)
- Each live post byte-identical to the local render; notebook links open in a new tab on the post page and in
  the deployed editor bundle; snapshot `2026-11-11-2343` byte-identical to the repo copy, its 7 links 200
- First deploy of the sitemap fix failed (the old script's `sitemap:generate`, his pasted output); the second
  finished: the sitemap URL served by the route (no `last-modified`/`etag`), 29 URLs, 19 post links = 19 published
- Playground still off (`enabled: false`)

## Measured
- `php artisan test`: 134 passed, 3 skipped before the sitemap change; 138 passed, 3 skipped (510 assertions) after
- Sitemap tests red before the route (5 of 5); mutations (posts query empty, series skipped, sitemap built once
  and reused) each turned the matching tests red; the leftover-file test red with the file, green without
- Editor preview rule: 2 of 2 notebook links new-tab on #47's content; broken on purpose, 0 of 2
- Both render paths (server CommonMark, editor `marked`) agree on every element for #47, #48, #49

## Traps and where they now live
- The editor's publish button saved without rebuilding the sitemap (second stale-sitemap incident): removed by
  design; the leftover-file test and deploy step 9 in `docs/deploy-guide.md`
- A file in `public/` shadows a route of the same path (nginx serves it first): the route comment, the test,
  deploy step 9
- `just deploy` runs the old `scripts/deploy.sh` when the deploy changes it: `docs/deploy-guide.md` trap note,
  backlog row 10
- A snapshot that links unpublished snapshots: module 18 spec line; `NotebookControllerTest` refuses dead links
- The skills hook fires on a checkout or merge that swaps skill files, and on restoring a file from git (false
  alarm, nothing to undo): session record only, as before
- Process slip: I edited a skill reference without `/skills-builder`; the hook caught it, I restored it from
  git, then applied it through `/skills-builder` with Bernard's approval

## Relied on (factory project, read only, never edited)
- In `~/code/software-factory-research`: the part 2, 3 and 4 briefs and drafts
  (`~/code/software-factory-research/docs/blog-post-02-brief.md` and its siblings),
  `~/code/software-factory-research/docs/blog-journal.md`,
  `~/code/software-factory-research/notebook/data.js`, `~/code/software-factory-research/notebook/takeaways.js`,
  `~/code/software-factory-research/notebook/snapshots/2026-11-11-2343.html`

## Outstanding
- Backlog row 10 (deploy pulls before starting the script), due 2026-11-26: on `feat/replace-github-models`
- Backlog row 9 (replace GitHub Models), due 2026-11-25, trigger fired
- Backlog row 1 (help.md for project skills), due 2026-12-08, trigger fired: `project-git-conventions` has none
- Not mine: `~/.claude/skills/user-playwright-verify/references/gotchas.md` changed during a command at
  2026-11-11 22:05, likely another session; left as it is
