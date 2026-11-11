# Backlog

Owed work. Check at session start.

| # | Item | Status | Due | Trigger |
|---|------|--------|-----|---------|
| 1 | help.md for the 6 remaining project skills via /skills-builder (blog-writer, portfolio-context and terminal-aesthetic got theirs 2026-11-11) | OPEN | 2026-12-08 | The first time a skill is edited for content |
| 2 | Split project-portfolio-context (1,097 words) and project-terminal-aesthetic (1,212) via /skills-builder review --fix | DONE 2026-11-11 | 2026-12-08 | Next content edit to either skill after module 17 merges |
| 3 | project-blog-writer: widen The Narrative beyond this portfolio's build and change the em dash rule to none; project-portfolio-context: add the /notebook route and the admin series label. One /skills-builder pass, together with row 2's split. | DONE 2026-11-11 | 2026-11-24 | Before drafting factory series part 2 |
| 4 | Post page tab title uses an em dash ("Title — sinfat.com", `resources/js/pages/BlogPost.vue` `document.title` and `og:title`); change to "·" to match the server title and the no-em-dash style | DONE 2026-11-11 (all page titles) | 2026-12-08 | The next deploy for any other reason |
| 5 | No `BlogService`: `BlogController` and `BlogPageController` query the `BlogPost` scope directly, while `project-laravel-conventions` describes Controller → Service. Add the service, or correct the skill (noted 2026-11-10, module 16) | OPEN | 2026-12-08 | Next change to either controller |
| 6 | Football Analytics project description is a placeholder; write the real story (5 architectural iterations) | OPEN | 2026-12-08 | Next edit to the Projects page |
| 7 | Supervisor for queue workers on production. Parked: no queued jobs exist, and nothing is dispatched to the queue | OPEN | 2027-02-01 | The first queued job is added |
| 8 | Neuron AI blog tools: let `BlogWriterAgent` read skills, query posts and read project files, with tool filtering by role, permissions enforced at the handler, scoped data access | OPEN | 2027-02-01 | Next work on the admin draft writer |
| 9 | Choose the AI provider that replaces GitHub Models (retired 2026-07-30; playground switched off 2026-11-11, admin draft writer broken): check current prices and free tiers for Anthropic, Gemini and Azure AI Foundry, put the choice to Bernard, set it in production, then `PLAYGROUND_ENABLED=true` | OPEN | 2026-11-25 | Next session |
