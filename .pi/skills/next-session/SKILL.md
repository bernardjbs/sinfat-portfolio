---
name: next-session
description: End-of-session closing routine for sinfat-portfolio. Use before ending any work session. Verifies everything is committed, documented, and captured so the next session can wake up and know exactly who they are, where they are, and what to do next. Produces a morning brief.
---

# Next Session — Closing Routine

You are putting the project to sleep. The next session that opens this project is a fresh mind with no memory of what happened today. Your job is to make sure that mind can wake up, read one file, and immediately know:

- What was built
- What decisions were made
- What is left to do
- Exactly where to start

---

## When to Run This

Run this skill at the end of any work session — after a module is complete, after a debugging session, after decisions were made, or any time work is stopping for the day.

---

## The Closing Checklist

Work through each item in order. Report ✅ or ❌ for each.

### 1. Git — Clean State
```bash
git status
git log --oneline -5
```
- All work committed? No uncommitted changes?
- All commits pushed to `origin/main`?
- No stale feature branches left over?

### 2. Tests — Passing
```bash
php artisan test
```
- All tests passing before closing?
- If any are failing, note why and what the plan is.

### 3. Session Note — Written and Current
- Does a session note exist for today's work in `docs/sessions/`?
- Does it cover: what was done, files changed, decisions made, outstanding items, next module?
- If anything happened after the session note was written (debugging, test fixes, etc.) — update it.

### 4. Blog Summary — Written
- Does a blog summary exist for each module completed this session?
- Located at `/Users/bernard/code/ai-learning/pi-vs-claude-code/docs/summaries/`?
- Does it explain the *why* before the *how*?

### 5. SESSION.md — Accurate
Check `.pi/SESSION.md`:
- "Where We Are" reflects the actual current state?
- "Next Task" is set to the correct next module?
- Module status table is up to date (✅ for complete, ⬜ for pending)?
- Reference Files point to the right spec and session note?
- Any resolved warnings removed?

### 6. Outstanding Items — Captured
- Any prod tasks still pending (SSH commands to run, env vars to set)?
- Any deferred decisions or known issues?
- Are they documented in the session note and/or SESSION.md?

### 7. Skills — Updated
- Were any new conventions established this session?
- Were any gotchas discovered that future sessions should know?
- Are they captured in the relevant skill file (`laravel-conventions`, `vue-conventions`, etc.)?

### 8. Morning Brief — Written
Write or update `.pi/MORNING_BRIEF.md` — the single file the next session reads first.

---

## The Morning Brief

The morning brief is the most important output of this skill. It is a short, dense snapshot of the project state written for a reader with no memory of today.

**Location:** `.pi/MORNING_BRIEF.md`
**Rule:** Overwrite it every session. It always reflects the current state — not history.

### Format

```markdown
# Morning Brief — [Date]

## What Was Built Today
[2–4 sentences. What modules or features were completed. What's now working that wasn't before.]

## Decisions Made
[Bullet list of any significant decisions — architectural, design, tooling. Just enough context to understand why.]

## Current State
[One sentence per layer: backend, frontend, infra. What exists and works right now.]

## Outstanding Items
[Anything left undone that must be handled — prod tasks, known issues, deferred decisions.]

## Start Here Tomorrow
[The exact next action. Be specific: module number, which skill to load, which spec to read.]
```

### Rules for the Brief
- **Short** — under 30 lines. If it's longer, cut it.
- **Dense** — no padding, no repetition. Every sentence earns its place.
- **Actionable** — the last section must tell the next session exactly what to do first.
- **Current** — always reflects today's state, not last week's.

---

## Closing Report Format

When the skill finishes, output:

```
Session closed — [date]

1. Git          ✅/❌ — [one line summary]
2. Tests        ✅/❌ — [pass count or failure note]
3. Session note ✅/❌ — [filename]
4. Blog summary ✅/❌ — [filename or "not needed this session"]
5. SESSION.md   ✅/❌ — [next task set to X]
6. Outstanding  ✅/❌ — [count of open items or "none"]
7. Skills       ✅/❌ — [what was updated or "no changes"]
8. Morning brief✅/❌ — .pi/MORNING_BRIEF.md written

Next session starts at: [exact instruction]
```

Any ❌ must be resolved before the session is considered closed.

---

## What NOT to Do
- ❌ Don't close the session with uncommitted work
- ❌ Don't write a morning brief that's just a list of files changed — explain state and intent
- ❌ Don't leave outstanding prod tasks undocumented
- ❌ Don't make the morning brief so long that reading it costs more context than it saves
