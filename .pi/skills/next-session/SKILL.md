---
name: next-session
description: End-of-session closing routine for sinfat-portfolio. Use before ending any work session. Verifies everything is committed, documented, and captured so the next session can wake up and know exactly where to start.
---

# Next Session — Closing Routine

You are putting the project to sleep. The next session reads `.pi/SESSION.md` and immediately knows what to do. Your job is to make sure that file is accurate.

---

## When to Run This

At the end of any work session — after a module is complete, after a debugging session, after decisions were made, or any time work is stopping.

---

## The Closing Checklist

Work through each item in order. Report ✅ or ❌ for each.

### 1. Git — Clean State
```bash
git status
git log --oneline -5
```
- All work committed and pushed to `origin/main`?
- No stale feature branches?

### 2. Tests — Passing
```bash
php artisan test
```
- All tests passing?
- If any failing, note why in SESSION.md outstanding items.

### 3. Session Note — Written
- Does a session note exist for today's work in `docs/sessions/`?
- Does it cover: what was done, decisions made, outstanding items?

### 4. SESSION.md — Updated
Update `.pi/SESSION.md`:
- Module status table — ✅ for complete, ⬜ for pending
- **Current State** — one sentence per layer (backend, frontend, infra)
- **Outstanding Items** — anything left undone
- **Start Here** — the exact next action for the next session
- **Reference Files** — point to correct spec and session note

### 5. Outstanding Items — Captured
- Any prod tasks pending? Known issues? Deferred decisions?
- All captured in SESSION.md?

### 6. Blog Summary — Written
- Write a summary of the session's journey at `/Users/bernard/code/ai-learning/pi-vs-claude-code/docs/summaries/`
- Follow the numbering and style of existing files (e.g. `15-module-5-blog-frontend-markdown.md`)
- Explain the *why* before the *how* — this is educational narrative, not a changelog

### 7. Skills — Updated
- Any new conventions or gotchas discovered?
- Captured in the relevant skill file?

---

## Closing Report Format

```
Session closed — [date]

1. Git          ✅/❌ — [one line]
2. Tests        ✅/❌ — [pass count or failure note]
3. Session note ✅/❌ — [filename]
4. SESSION.md   ✅/❌ — [next task]
5. Outstanding  ✅/❌ — [count or "none"]
6. Blog summary ✅/❌ — [filename or "not needed this session"]
7. Skills       ✅/❌ — [what changed or "no changes"]

Next session starts at: [exact instruction]
```

Any ❌ must be resolved before the session is considered closed.

---

## What NOT to Do
- ❌ Don't close with uncommitted work
- ❌ Don't leave SESSION.md pointing at the wrong module
- ❌ Don't leave outstanding prod tasks undocumented
