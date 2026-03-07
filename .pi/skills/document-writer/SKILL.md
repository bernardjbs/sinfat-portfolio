---
name: document-writer
description: Writing and organising project documentation as small, well-named markdown files. Use when creating notes, summaries, decisions, specs, or any reference content that will be read in future sessions. Covers file sizing, naming conventions, and directory structure.
---

# Document Writer

## The Core Problem

A large markdown file is expensive. Reading `SUMMARY.md` at 63KB costs 63KB of context every time — even if you only needed 4KB of it. A file that cannot be found by name is useless — the agent has to read an index or guess.

**The two rules everything else follows from:**
1. One topic = one file
2. The filename IS the index

---

## File Size Target

| Size | Status |
|------|--------|
| Under 8KB | ✅ Ideal |
| 8–15KB | ⚠️ Acceptable if the topic is genuinely unified |
| Over 15KB | ❌ Split it |

When a file grows beyond 15KB, find the natural seam — a section break, a topic shift — and split there. Each half becomes its own file.

---

## Naming Conventions

The filename must answer the question: *"Is this the file I need?"* — without opening it.

**Format:** `kebab-case-specific-topic.md`

### Be Specific, Not Generic

```
❌ auth.md
✅ laravel-admin-session-auth.md

❌ redis.md
✅ redis-rate-limiting-guest-playground.md

❌ streaming.md
✅ sse-streaming-nginx-buffering-fix.md

❌ notes.md
✅ oracle-cloud-iptables-firewall-gotchas.md
```

### Include the Domain Signal

The first word or two should narrow the domain so the agent can rule files out without reading them:

```
laravel-*     → backend PHP patterns
vue-*         → frontend component patterns
neuron-ai-*   → AI agent / streaming patterns
infra-*       → server, deployment, networking
oracle-*      → Oracle Cloud specific
cloudflare-*  → DNS, proxy, SSL
db-*          → database schema, migrations
git-*         → git workflow, commits
seo-*         → sitemap, meta tags
blog-*        → blog feature specific
playground-*  → guest playground specific
```

### Ordered Sequences Use Number Prefixes

Only use numeric prefixes when order matters — tutorial steps, build modules, release notes:

```
module-01-infrastructure.md
module-02-database-schema.md
module-03-authentication.md
```

For reference content with no inherent order, skip the number — alphabetical sorting is fine.

### Session Notes Get Dates

Notes written during a session that capture what happened:

```
2026-03-07-module-1-redis-setup.md
2026-03-08-module-2-schema-decisions.md
```

---

## Directory Structure

```
.pi/
  SESSION.md              ← current task, module status, reference file pointers
  skills/                 ← Pi skill files (auto-loaded at startup)
  agents/                 ← subagent persona files

docs/                     ← project-level reference (decisions, context)
  decisions/              ← one file per architectural decision
    decision-spa-vs-hybrid.md
    decision-sse-vs-websockets.md
    decision-neuron-ai-vs-laravel-ai.md
  sessions/               ← session notes (dated)
    2026-03-07-module-1-redis-setup.md
    2026-03-08-module-2-schema-decisions.md
  summaries/              ← topic summaries for blog post drafting
    oracle-cloud-infra-journey.md
    redis-rate-limiting-patterns.md
    sse-streaming-laravel-vue.md
    llm-security-laravel-mapping.md

specs/                    ← implementation specs per module
  module-01-infrastructure.md
  module-02-database-schema.md
  ...
```

---

## When to Write What

### Decision file
Write one when a significant architectural choice is made. Capture: what was decided, what was ruled out, and why. Filename = the decision topic.

```markdown
# Decision — SSE over WebSockets

## Decision
Use SSE for all AI streaming. No WebSockets.

## Why
AI streaming is server → browser only. SSE is built for this.
WebSockets require Laravel Reverb — added complexity for no benefit.

## Ruled Out
WebSockets — only needed if browser must also send while server streams.
```

### Session note
Write one at the end of a session capturing what was done, what was learned, any gotchas. Use a date prefix. Keep it under 5KB.

### Summary file
Write one when a topic is well understood and needs to be referenced for blog posts or future sessions. One topic, complete treatment, no padding.

### Spec file
Already split by module in `specs/`. One file per module. Update the module file as decisions are made during implementation — it's a living doc, not a frozen plan.

---

## Updating SESSION.md

After creating or updating any reference file, update the pointer in `.pi/SESSION.md` if it's relevant to the current or next task. SESSION.md is the map — keep it accurate.

If the current module spec changes location or a new summary is written that the next session will need, add it to the Reference Files section:

```markdown
## Reference Files (read only when needed)
- Current module spec → `specs/module-02-database-schema.md`
- Schema decision context → `docs/decisions/decision-db-guest-usage-table.md`
```

---

## What Not to Do

- ❌ One giant file that keeps growing — split at 15KB
- ❌ Generic names (`notes.md`, `misc.md`, `temp.md`) — useless as an index
- ❌ Duplicating content across files — one source of truth, reference by path
- ❌ Writing docs no one will read — if it won't be referenced in a future session or blog post, skip it
- ❌ Burying the topic in the filename — domain signal first (`laravel-`, `vue-`, `infra-`)
