# Session: Module 18: Factory Series
**Date:** 2026-11-10

Roadmap phase 3, step 3.4. Built while Bernard was away; deploy and publishing stay his.

## What Was Done

### Notebook snapshots (`f45b8c1`)
- Six snapshots copied from `software-factory-research/notebook/snapshots/` to `resources/notebook/<stamp>.html`; originals unchanged (6 of 6 checksums match)
- In the copies: 21 `file://` links unwrapped to their text, and 10 links between snapshots (`<stamp>.html`) pointed at `/notebook/<stamp>/`, which would otherwise break once each snapshot has its own URL
- A script proved nothing else changed: undoing both edits on each copy gives back the original exactly
- `NotebookController` serves them at `/notebook/<stamp>/`, ahead of the SPA catch-all; unknown or malformed stamps answer 404. A route instead of `public/notebook/<stamp>/index.html`, so it does not depend on how production nginx treats a folder URL (its config is not in the repo)
- Text read before publishing: no secrets, emails or client names; external links only to the workshop video

### Series and draft (local data only)
- Series `my-own-ai-software-factory`, "My own AI software factory", approved pitch, status `writing`
- Post #45 "Do I need a software factory product, or just a script?": draft, part 1, `ai_generated` true, `ai_model` `claude-opus-5-5`, category `development`, 1,021 words, written with `project-blog-writer` from `software-factory-research/docs/blog-post-01-brief.md`
- Admin list shows a post's series and part, so the draft reads "Part 1 of My own AI software factory"

## Measured
- `grep -c 'file://'` on all six copies: 0 (originals: 3, 3, 3, 4, 4, 4)
- `/notebook/<stamp>/` 200 for all six (feature test and curl); a link from the newest snapshot opens the oldest
- `/api/blog`, `/api/series` and `/feed.xml` byte-identical before and after the series and draft; 14 published posts; the draft, its series page and API answer 404
- `/blog` at 390 and 1150: only "Building sinfat.com with AI", no sideways scroll
- Draft: 1,021 words, 0 em dashes, 0 en dashes; only the three permitted figures; the four quotes verbatim from the journal (capitals only)

## Tests
- 6 new tests (notebook 5, admin listing series 1), each guard mutation-proved (6 of 6 red)
- Total: 116 passed, 3 skipped

## Outstanding
- The admin editor on screen was not checked: it needs Bernard's login. The admin API and a feature test prove the draft is listed as a draft, part 1
- Production steps in `specs/module-18-factory-series.md`
