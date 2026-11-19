# sinfat-portfolio

Bernard's portfolio and blog: a Laravel API with a Vue 3 SPA. Local `https://sinfat.test` (Valet),
prod `sinfat.com`.

## Start here

- `docs/handover.md`: where to resume (points at the latest session note, next steps, git state).
- Owed work lives in `docs/backlog.md`; check it at session start.
- Specs: `specs/module-NN-*.md`. Session notes: `docs/sessions/`.
- Deploy, server facts and PHP patching: `docs/deploy-guide.md`. Production settings: `docs/production-env.md`.
- Original plan (closed record, last changed 2026-03-07; never updated from here):
  `~/code/ai-learning/pi-vs-claude-code/docs/PORTFOLIO.md`. Decisions since live in `specs/`.

## Commands

- Tests: `php artisan test`.
- Deploy is manual (`just deploy`) and runs only on Bernard's word.
- A local post reaches production with `just post-to-prod <id> --publish` (live at once, with its date) or `--draft`;
  one is required. The factory research session may run it for a new post (`--publish` once Bernard has approved
  its static preview); updating an already-published post goes live at once, so that stays Bernard's call
  (`specs/module-19-series-post-path.md`).
- A notebook snapshot reaches production with no deploy: `php artisan notebook:manage import --file=<original>`, then
  `just notebook-to-prod <stamp>`, before the post that links it (`docs/deploy-guide.md`). The factory research session may send a new one;
  replacing one (`--force`) changes a live page, so that is Bernard's call.

## Project skills

- `project-portfolio-context`: architecture reference (stack, routes, tables, stores, deploy).
- `project-laravel-conventions`: PHP conventions, Controller → Service → Resource.
- `project-vue-conventions`: Vue 3 Options API, Pinia, fetch, SSE streaming.
- `project-terminal-aesthetic`: design system, colour tokens, Geist Mono.
- `project-neuron-ai-patterns`: Neuron AI agents, SSE, guest keys, rate limiting, LLM security.
- `project-test-writer`: PHPUnit feature test conventions.
- `project-security-review`: pre-commit security checklist.
- `project-git-conventions`: branches, Conventional Commits, deploy triggers.
- `project-blog-writer`: writing blog posts for sinfat.com, draft to publish.

Agents: `security-reviewer`, `pr-reviewer`.

## Done means

- `php artisan test` passes, with the count stated.
- Spec tasks and acceptance criteria ticked `[x]`.
- Session note written to `docs/sessions/`.
- `docs/handover.md` updated.
- Skills updated if a convention changed.

## Blog material

Blog raw material stays in the project it is about; this repo only publishes.
