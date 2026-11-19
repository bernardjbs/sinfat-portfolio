# Module 19: A Factory-Series Post's Path to Production

> Decided 2026-11-19 in the factory research, from a measurement of posts 1 to 4. This file records
> what changes in how a series post reaches sinfat.com, and why. Owed code: backlog row 12.

## Contents

- [Why](#why)
- [How a series post travelled before](#how-a-series-post-travelled-before)
- [Decision 1: one session, draft straight to production](#decision-1-one-session-draft-straight-to-production)
- [Decision 2: snapshots without a deploy](#decision-2-snapshots-without-a-deploy)
- [What changes](#what-changes)

## Why

Bernard, 2026-11-19: the blog was taking too long, with too much ceremony between steps. Each turn of
factory-series posts 1 to 4 (published 10 and 11 Nov) was timed until his next message, Claude's work
and his reading included, idle gaps capped at 5 minutes, and sorted by what he asked (the sort is a
judgement; the minutes are measured):

| Where | Minutes, 4 posts | Share |
|---|---|---|
| Loading, publishing, deploying, checking the live site | ~108 | 35% |
| His corrections to the text | ~81 | 26% |
| Handing work between two sessions | ~57 | 18% |
| Plan changes the writing sparked | ~40 | 13% |
| Checking claims against sources | ~23 | 7% |
| Writing the drafts | ~5 | 2% |

The writing is fast. About half the time went on moving a post from one session to another and onto
the server. The corrections are where the quality comes from and fell to about a minute by parts 3 and 4.

## How a series post travelled before

1. The factory research session took a notebook snapshot and wrote the draft that links it.
2. Bernard pasted a line into a blog session, which loaded the draft locally and checked the format.
3. The blog session copied the snapshot into `resources/notebook/` (links rewritten, module 18).
4. The branch was merged and pushed, then `just deploy` shipped the snapshot.
5. Bernard ran `just post-to-prod <id>`: the text arrived as a draft post.
6. Bernard previewed and pressed Publish.

The text never needed a deploy; a new snapshot did, because snapshots are files in the code. Part 3's
merge (`09979fd`) shipped one file: the snapshot.

## Decision 1: one session, draft straight to production

Bernard: "yes, this version".

1. The factory research session writes the draft and saves it to the local blog, with no local preview.
   `post-to-prod` sends from the local database, so the local save stays.
2. It imports any new snapshot into the local blog and sends it with `just notebook-to-prod <stamp>`
   (Decision 2; before backlog row 12 it was copied into the code and needed a merge and deploy).
3. It runs `just post-to-prod <id>`: a new post arrives as a draft. For a post already published it asks
   Bernard first, because `post-to-prod` updates an existing post in place (status unchanged), so the
   change would be live at once.
4. Bernard previews it in the live admin editor (its rendered preview) and presses Publish.

Considered, not picked: keeping the separate blog session (the hand-over, ~57 minutes over four posts).

## Decision 2: snapshots without a deploy

Bernard: "record it in the blog's backlog". A deploy rebuilds the whole site to ship one snapshot file.
Sent to production the way post text is, a series post would need no deploy at all. Backlog row 12.

Scoped and ruled 2026-11-19 (Bernard: "1 yes, 2 keep, 3 one branch, 4 you edit, 5 record"):

1. **Snapshots live in the database** (`notebook_snapshots`: stamp, html), served at the same
   `/notebook/<stamp>/`. Files on the server were rejected: the ssh user writes them and the web server
   reads them, the ownership trap the sitemap hit.
2. **The link rewriting is a command**, `php artisan notebook:manage import --file=<source>`: `file://`
   links unwrapped to their text, links to snapshots in that database pointed at `/notebook/<stamp>/`,
   links to any other snapshot dropped. It refuses a result holding `file://`, `/Users/` or `<script`.
3. **`just notebook-to-prod <stamp>`** sends the local copy over ssh stdin, so production holds the same
   bytes. Production refuses a snapshot that links one it lacks, and replacing a stamp without `--force`
   (a live page would change).
4. **`post-to-prod` refuses a post linking a snapshot production lacks**, the 404 this decision is about.
5. **The seven published copies** are loaded by a data migration on the deploy, so no link goes dark,
   and stay in `resources/notebook/` as the record of the hand correction (ruling 2: "keep"). New
   snapshots are not committed; their source is the factory folder and the command redoes the copy.
6. **Snapshot pages forbid scripts** (a Content-Security-Policy header). None of the seven has one;
   imported pages skip code review, so a bad import cannot run code on sinfat.com.
7. Rides one branch with backlog row 10, so its deploy runs the new script first time (ruling 3).
8. The factory research folder's `CLAUDE.md` send step is edited here, once the command exists (ruling 4).

Production database backups: none on record, backlog row 13 (ruling 5: "record").

## What changes

| Where | Change | Status |
|---|---|---|
| `CLAUDE.md` (Commands) | `post-to-prod` may be run by the factory research session for a new post; a published post's update stays Bernard's call | Done with this file |
| `project-blog-writer` (`references/managing-posts.md`) | Same rule in "Send to production" | Done with this file, through `/skills-builder` |
| The factory research folder's `CLAUDE.md` | The blog session no longer loads drafts; the research session saves, sends and asks as above | Done in that folder |
| Snapshots | Sent without a deploy (Decision 2) | Done on branch `feat/module-19-notebook-snapshots`; live once it deploys |
| `docs/deploy-guide.md`, `CLAUDE.md` (Commands) | `notebook:manage import` and `just notebook-to-prod`; the deploy pulls before the script (backlog row 10) | Done |
| `project-portfolio-context`, `project-blog-writer`, `project-git-conventions` | Snapshots in the database, sent before the post; the deploy's pull | Through `/skills-builder` |
| The factory research folder's `CLAUDE.md` | "copy any new snapshot into the blog's code" becomes import, then `notebook-to-prod` | Ruling 4 |
