# Module 18: Blog Series: My own AI software factory

## Goal

Start the second series: a solo developer working out what a software factory means for one person,
then building it stage by stage. Publish the factory notebook's snapshots on sinfat.com so posts can
link the plan as it stood, create the series, and draft part 1. Roadmap phase 3, step 3.4
(`~/code/software-factory-research/docs/blog-roadmap.md`). Rulings 2026-11-10: publish the snapshots
with local file links stripped; positioning deferred, so the home and About pages are untouched.

## The Arc

The series follows the factory build as it happens, one post per milestone, each from that project's
blog journal (`~/code/software-factory-research/docs/blog-journal.md`). Status stays `writing`; there
is no fixed total. Candidate order, from the journal (not a commitment):

1. Do I need a software factory product, or just a script?
2. Merge is the last thing I'll automate, and it has to earn it
3. I'd already built the AI mock. I just didn't know its name
4. A plan that's alive: a notebook with snapshots instead of a document
5. Unit tests are a lie (for agents)

## Category

All posts: `development`

## Workflow per Post

1. The factory session writes a brief from its journal (`docs/blog-post-NN-brief.md` in that project)
2. This repo drafts it with `project-blog-writer` (manual input), saved locally as a draft in the
   series with `blog:manage create --series=my-own-ai-software-factory`
3. Bernard reads and edits it in the admin editor on `sinfat.test`
4. Export it (`blog:manage export --id=N`), copy the file to the server, and create it on production
   with the same command, which puts it in the series
5. Bernard publishes it in the production admin editor

Agents never publish.

## Snapshots

- Source: `~/code/software-factory-research/notebook/snapshots/<stamp>.html`, never edited
- Published copies: `resources/notebook/<stamp>.html`, served by a Laravel route at
  `/notebook/<stamp>/` (with or without the slash), registered ahead of the SPA catch-all. A route
  rather than `public/notebook/<stamp>/index.html`, so the result does not depend on how the
  production web server treats a folder URL, and so a feature test can prove it
- In the copies only: every `file://` link unwrapped to its text, and each link to another snapshot
  (`<stamp>.html`) pointed at `/notebook/<stamp>/`
- In the copies only, added 2026-11-10 after the part 1 fact-check: the trap line "One factory was shut
  down after 3 months" replaced with the corrected line from Dex Horthy's own essay, marked as a correction
- Six snapshots: `2026-11-09-2327` to `2026-11-10-1220`. A new snapshot is a new file plus a copy run

## The Posts

#### Post 1
**"Do I need a software factory product, or just a script?"**
- The three opening questions, what every factory has in common, a human still approves the merge,
  solo is real, quality is the goal, a control panel is not a quality system, the answer for me
- Brief: `~/code/software-factory-research/docs/blog-post-01-brief.md` (its hard rules bind)
- Links the notebook as it stood that night: `/notebook/2026-11-09-2327/`
- Saved locally as a draft, part 1 (post #45 locally), 1,021 words; body replaced after a fact-check
- Published on sinfat.com 2026-11-10, with the series and notebook snapshots live

## Tasks

- [x] Snapshots copied and transformed; originals unchanged (checksums before and after)
- [x] `NotebookController` and route; 404 for an unknown or malformed stamp; feature tests
- [x] Series `my-own-ai-software-factory` created locally ("My own AI software factory", approved
      pitch, `writing`)
- [x] Post 1 drafted from the brief and saved as a draft, part 1, `ai_generated` true
- [x] Admin list shows a post's series and part ("Part 1 of My own AI software factory"), so the draft can be found as part 1
- [x] Session note, handover

## Acceptance Criteria

- [x] `grep -c 'file://'` on every published copy is 0
- [x] Each `/notebook/<stamp>/` answers 200 on `sinfat.test` and renders (screenshot)
- [x] The draft shows in the admin editor as a draft, part 1 of the new series (admin API and a
      feature test; Bernard then published it from the production admin editor, 2026-11-10)
- [x] Public `/blog` and `/api/blog` unchanged: 14 published posts, and the new series hidden because
      it has no published part
- [x] Draft 800 to 1,100 words, no em dashes
- [x] `php artisan test`: baseline 110 passed and 3 skipped, plus the new tests, 0 failed
- [x] `git diff --stat main`
- [x] Report carries the exact production commands

## Production (run by Bernard 2026-11-10)

The deploy ships the snapshots: `/notebook/<stamp>/` is public from then on. Then:

```
ssh sinfat "cd /var/www/sinfat && php artisan blog:manage series create --slug=my-own-ai-software-factory --title='My own AI software factory' --pitch='A solo developer works out what a software factory means for one person, then builds it stage by stage.'"
```

When the draft is ready (after any edits in the local admin editor):

```
php artisan blog:manage export --id=45
scp storage/app/blog-exports/do-i-need-a-software-factory-product-or-just-a-script.md sinfat:/tmp/factory-part-1.md
ssh sinfat "cd /var/www/sinfat && php artisan blog:manage create --title='Do I need a software factory product, or just a script?' --excerpt='Three questions about AI software factories, what the published ones have in common, and why my answer was to build my own on what I already had.' --file=/tmp/factory-part-1.md --series=my-own-ai-software-factory --ai-model=claude-opus-5-5"
```

It is created as a draft, part 1. Publish it in the production admin editor, then rebuild the sitemap so
the post and the series page are listed:

```
ssh sinfat "cd /var/www/sinfat && php artisan sitemap:generate"
```

The series row appears on `/blog` once the post is published. A changed title or excerpt goes in the
create command, not the file.

### Dependencies
Module 17 (merged)

---

*Created: 2026-11-10*
*Status: ✅ Complete (merged `c1c75ea`; part 1 published 2026-11-10)*
