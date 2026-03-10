# Module Runner — Workflow Config

This file controls which steps the `module-runner` agent runs and in what order.
Edit this file to add, remove, or reorder steps. The agent reads it at the start of every run.

---

## Active Steps

```
1. Orient      → read SESSION.md, read module spec, load relevant skills
2. Plan        → write a plan and wait for user confirmation
3. Execute     → do the work, dispatch sub-agents as needed
4. Test        → write PHPUnit feature tests per test-writer skill; php artisan test must pass
5. Security    → run security-review skill checklist before any commit
6. Commit      → one logical unit at a time, following git-conventions
7. Document    → MUST complete ALL of these before moving on:
                  □ session note written to docs/sessions/
                  □ blog summary written to pi-playground/docs/summaries/
                  □ skills updated if new conventions or patterns discovered
                  □ outstanding items updated in SESSION.md and session note
                  □ spec file tasks and acceptance criteria ticked off
8. Update      → update SESSION.md: module status table, current state, tests count, what's next, reference files
9. Verify      → run acceptance criteria from the spec
10. Sign Off   → GATE CHECK — do not pass until every item is confirmed:
                  □ git status clean, all pushed, no stale branches
                  □ php artisan test passing (report exact count)
                  □ session note exists with correct filename
                  □ blog summary exists with correct filename
                  □ SESSION.md module table shows ✅ for this module
                  □ spec file has all tasks and criteria ticked [x]
                  □ outstanding items captured (not stale from previous module)
                  □ skills reflect any new patterns from this module
                  □ any ❌ above must be fixed before declaring complete
```

---

## How to Customise

**Remove a step** — delete or comment out the line:
```
# 5. Document    → removed: handled separately this module
```

**Add a step** — insert a new numbered line with a label and description:
```
4a. Review     → run pr-reviewer agent before committing
```

**Reorder steps** — change the numbers and re-sort:
```
1. Orient
2. Plan
3. Execute
4. Verify      ← moved up: verify incrementally as we build
5. Commit
6. Document
7. Update
```

**Skip for a single run** — tell the agent at dispatch time:
> "dispatch module-runner, start Module 2 — skip Document step this run"

The agent will acknowledge and skip that step for this run only, without modifying this file.
