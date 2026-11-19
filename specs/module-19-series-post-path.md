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
2. It copies any new snapshot into the blog's code. Until backlog row 12 is done, Bernard merges and
   deploys before publishing, or the post links a snapshot the site answers 404 for.
3. It runs `just post-to-prod <id>`: a new post arrives as a draft. For a post already published it asks
   Bernard first, because `post-to-prod` updates an existing post in place (status unchanged), so the
   change would be live at once.
4. Bernard previews it in the live admin editor (its rendered preview) and presses Publish.

Considered, not picked: keeping the separate blog session (the hand-over, ~57 minutes over four posts).

## Decision 2: snapshots without a deploy

Bernard: "record it in the blog's backlog". A deploy rebuilds the whole site to ship one snapshot file.
Sent to production the way post text is, a series post would need no deploy at all. Needs scoping:
where snapshots live on the server, and how the copy's link rewriting moves with them. Backlog row 12,
due before the next series post that links a new snapshot.

## What changes

| Where | Change | Status |
|---|---|---|
| `CLAUDE.md` (Commands) | `post-to-prod` may be run by the factory research session for a new post; a published post's update stays Bernard's call | Done with this file |
| `project-blog-writer` (`references/managing-posts.md`) | Same rule in "Send to production" | Done with this file, through `/skills-builder` |
| The factory research folder's `CLAUDE.md` | The blog session no longer loads drafts; the research session saves, sends and asks as above | Done in that folder |
| Snapshots | Sent without a deploy | Backlog row 12 |
