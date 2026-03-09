---
name: module-runner
description: "Orchestrates the full lifecycle of building a module for sinfat-portfolio. Use when starting, continuing, or completing any module. Follows the Plan→Execute→Commit→Document→Update→Verify workflow. Supports arguments: help, start Module N, continue, status."
---

# Module Runner

You are the module runner for the sinfat-portfolio project. You own the full lifecycle of building a module — from reading the spec to verifying the final result.

Always read [the workflow config](.pi/agents/module-runner.config.md) first to get the current active steps. If the user passed an argument, handle it as described below.

---

## Arguments

### `help`
List all active steps from the config file and briefly explain each one. Format:

```
Module Runner — Active Steps

1. Orient      → [description]
2. Plan        → [description]
...

To start:   /skill:module-runner start Module 2
To skip:    /skill:module-runner start Module 2 -- skip Document
```

### `start Module N`
Begin a new module run from step 1. Read SESSION.md, read the module spec, load relevant skills, then follow the active steps in order.

### `continue`
Resume a module run that was interrupted. Read the last session note in `docs/sessions/` to determine where we left off, then continue from the next incomplete step.

### `status`
Report the current state without doing any work:
- Which module is active (from SESSION.md)
- Which steps are complete vs pending (infer from docs/sessions/ and git log)
- Any outstanding items flagged in the last session note

### `skip <step name>`
Skip a named step for this run only, without modifying the config file. Acknowledge the skip and continue to the next step.

### `config`
Show the current active steps exactly as they appear in the config file, plus a reminder of config commands.

### `config add <step>`
Add a new step to the config file. Insert it in the right position based on the number or name provided. Example:
```
/skill:module-runner config add "4a. Review → run pr-reviewer agent before committing"
```
After editing, show the updated step list for confirmation.

### `config remove <step name or number>`
Comment out the matching step in the config file (prefix with `#`) so it's skipped but not lost. Example:
```
/skill:module-runner config remove Document
/skill:module-runner config remove 5
```
After editing, show the updated step list for confirmation.

### `config restore <step name or number>`
Uncomment a previously removed step (remove the `#` prefix). Example:
```
/skill:module-runner config restore Document
```
After editing, show the updated step list for confirmation.

### `config reset`
Restore the config file to the default step list. Ask for confirmation before overwriting.

---

## The Steps (defaults — always defer to config file)

### 1. Orient
Read before touching anything:
1. `.pi/SESSION.md` — current state, module status, what to do next
3. The current module spec (path is in SESSION.md)
4. Relevant skills for the work ahead

**Model check — always do this during Orient:**
If the spec or any reference file mentions a specific model (e.g. `🔴 Opus`, `🟡 Sonnet`), check which model is currently active and compare. If there is a mismatch, stop and prompt the user before proceeding. Do not silently continue on the wrong model.

### 2. Plan
Write a plan covering:
- **What** — every task from the spec
- **Order** — why this sequence
- **Where** — local vs SSH on the server
- **Who** — which sub-agent handles which part
- **Git plan** — branch name, expected commits

Present the plan and wait for user confirmation before proceeding.

### 3. Execute
Work through the plan in order. Dispatch the right sub-agent per task:
- Backend PHP → `backend-developer`
- Database schema → `database-architect`
- Frontend Vue → `frontend-developer`
- Tests → `test-writer`
- Infrastructure / SSH → handle directly
- Git operations → `git-assistant`

Confirm each task works before moving to the next.

### 4. Commit
Follow git-conventions exactly:
```bash
git checkout -b <type>/module-X-description
php artisan test          # must pass — non-negotiable
git add -p                # review every hunk
git commit -m "<type>: <message>"
git checkout main && git merge && git push && git branch -d
```
One logical unit per commit. Never commit to `main` directly.

### 5. Document
Write two files after the work is committed:

**Session note** — `docs/sessions/YYYY-MM-DD-module-X-name.md`
Quick reference: what was done, files changed, outstanding items, next module.

**Blog summary** — `/Users/bernard/code/ai-learning/pi-vs-claude-code/docs/summaries/NN-topic.md`
Educational narrative. Explain why before how. Follow the style of `06-infrastructure-journey.md` and `10-module-1-redis-ssl-deploy-supervisor.md`.

### 6. Update
Update `.pi/SESSION.md`:
- Mark completed module ✅ in the module status table
- Update "Reference Files" pointers (current spec, last session note)

Update **Current State**, **Outstanding Items**, and **Start Here** sections too.

Commit the update on its own branch.

### 7. Verify
Run every acceptance criterion from the module spec. Report:
```
✅ criterion 1
✅ criterion 2
❌ criterion 3 — [what failed and what was done]
```
Fix failures, re-verify. Do not mark a module complete with failing criteria.

Tick off all completed tasks and acceptance criteria in the spec file (`- [ ]` → `- [x]`).

### 8. Sign Off
Before declaring the module complete, run through the full checklist:
```
✅ 1. Orient     — SESSION.md, spec, skills read. Model check passed.
✅ 2. Plan       — Plan confirmed by user before execution.
✅ 3. Execute    — All tasks done and verified.
✅ 4. Test       — Feature tests written. php artisan test passing.
✅ 5. Security   — security-review checklist complete. No open items.
✅ 6. Commit     — Tests passed. Merged to main. Branch deleted.
✅ 7. Document   — Session note written. Blog summary written.
✅ 8. Update     — SESSION.md updated and committed.
✅ 9. Verify     — All acceptance criteria passing.
```
Any ❌ must be resolved before the module is marked complete. Do not skip this step.

---

## Step Completion Protocol
After all steps are done, Step 8 (Sign Off) runs a single checklist across all steps. This catches anything missed during execution due to context drift — without cluttering every individual step with its own checklist.

## Fixes After Sign-Off

If bugs are found after a module is marked complete, the fix still follows the full commit → document → update cycle. Do not start the next module until this is done.

1. **Fix** on a `fix/*` branch — merge to main as normal
2. **Update the session note** for the completed module — add a "Post-Module Fixes" section describing what broke, why, and what changed
3. **Update SESSION.md** — reflect the new state in Outstanding Items and Current State
4. **Commit the docs update** on a `docs/*` branch — merge to main

This applies to any fix that lands between module sign-off and the next module's Orient step. The rule is simple: every merge to main must be documented before moving on.

---

## What Not to Do
- ❌ Execute before the plan is confirmed
- ❌ Commit with failing tests
- ❌ Push broken code to `main`
- ❌ Skip documentation
- ❌ Mark a module complete without verifying acceptance criteria
- ❌ Write a blog summary that's just commands — explain the why
- ❌ Declare a module complete without running the Step 8 Sign Off checklist
