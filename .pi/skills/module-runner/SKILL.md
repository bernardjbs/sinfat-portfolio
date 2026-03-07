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
1. `.pi/SESSION.md` — current task, module status, reference files
2. The current module spec (path is in SESSION.md)
3. Relevant skills for the work ahead

**Model check — always do this during Orient:**
If the spec or any reference file mentions a specific model (e.g. `🔴 Opus`, `🟡 Sonnet`), check which model is currently active and compare. If there is a mismatch, stop and prompt the user before proceeding. Do not silently continue on the wrong model.

Do not proceed to Plan until all three are read.

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

Before moving to Step 6, explicitly confirm both outputs with a checklist:
```
Step 5 complete:
✅ Session note → docs/sessions/YYYY-MM-DD-module-X-name.md
✅ Blog summary → .../summaries/NN-topic.md
```
Do not proceed to Step 6 until both are checked off.

### 6. Update
Update `.pi/SESSION.md`:
- Mark completed module ✅
- Set "Next Task" to the next module
- Update "Reference Files" pointers
- Remove resolved warnings

Commit the update on its own branch.

### 7. Verify
Run every acceptance criterion from the module spec. Report:
```
✅ criterion 1
✅ criterion 2
❌ criterion 3 — [what failed and what was done]
```
Fix failures, re-verify. Do not mark a module complete with failing criteria.

---

## Step Completion Protocol
Before moving from any step to the next, explicitly verify every output that step requires. For steps with multiple outputs, list each one with ✅ or ❌. Do not proceed until all are ✅.

This is non-negotiable — context drift during long executions makes partial completion easy to miss.

## What Not to Do
- ❌ Execute before the plan is confirmed
- ❌ Commit with failing tests
- ❌ Push broken code to `main`
- ❌ Skip documentation
- ❌ Mark a module complete without verifying acceptance criteria
- ❌ Write a blog summary that's just commands — explain the why
- ❌ Move to the next step without explicitly confirming all outputs of the current step
