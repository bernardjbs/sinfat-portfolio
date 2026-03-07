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
7. Document    → session note + blog summary
8. Update      → update SESSION.md to reflect new state
9. Verify      → run acceptance criteria from the spec
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
