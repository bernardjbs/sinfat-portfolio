# Pi Tutorial Progress

**Tutorial file:** `docs/TUTORIAL.md`
**Started:** 2026-03-02
**Last session:** 2026-03-06

---

## Status

| Module | Title | Status | Date | Notes |
|--------|-------|--------|------|-------|
| 1 | The Base Agent | ✅ Done | 2026-03-02 | |
| 2 | Pure Focus & Minimal | ✅ Done | 2026-03-03 | Built my-footer.ts with git branch in accent colour |
| 3 | Purpose Gate | ✅ Done | 2026-03-03 | Built my-gate.ts — void pattern for session_start, action:continue/handled, setStatus with theme |
| 4 | Tool Counter & Widgets | ✅ Done | 2026-03-03 | tool_result for counting, tool_call for interception, event.input not event.args |
| 5 | Damage Control | ✅ Done | 2026-03-03 | tool_call block pattern, YAML rules, ask vs auto-block, policy vs code separation |
| 6 | TillDone | ✅ Done | 2026-03-03 | registerTool object signature, label+details required, tool_call fires before execute in parallel blocks |
| 7 | Subagent Widget | ✅ Done | 2026-03-03 | createAgentSession, parallel execution, context isolation, dependency chaining via await |
| 8 | Agent Team | ✅ Done | 2026-03-03 | dispatch_agent tool, agent personas as .md files, teams.yaml roster, skills vs agents distinction |
| 9 | Agent Chain | ✅ Done | 2026-03-03 | $INPUT/$ORIGINAL substitution, sequential vs team, token cost awareness |
| 10 | Pi-Pi Meta Agent | ✅ Done | 2026-03-03 | orchestrator=main session, experts=research subagents, agents build extensions not agents |
| 11 | Theme Cycler | ✅ Done | 2026-03-03 | JSON color tokens, auto-discovered from .pi/themes/, same tokens used in theme.fg() calls |
| 12 | Stack & Ship | ✅ Done | 2026-03-03 | 3 RooFoot2 configs, extensions compose, tier 0-3 progression |

**Legend:** ⬜ Not started · 🔄 In progress · ✅ Done · ⏭️ Skipped

---

## Current Position

**Next up:** Tutorial complete ✅

**Resume prompt:**
> "Read /Users/bernard/code/sinfat-portfolio/.pi/SESSION.md. That's all you need to start."

---

## pi-agent-toolkit Status

| Extension | Status | Notes |
|-----------|--------|-------|
| `my-footer.ts` | ✅ Done | Custom footer, context meter, git branch, setStatus rendering |
| `my-gate.ts` | ✅ Done | Session focus gate, docblock added |
| `my-checkpoint.ts` | ✅ Done | Bash guardrail, docblock added |
| `session-observer.ts` | ✅ Done | Workflow analyst, /observer evaluate/fix/report/clear/history/help |
| `blog-writer.ts` | ✅ Done | /blog set/list/draft/drafts/help, editor preview |
| `progress-tracker.ts` | ✅ Done | session_shutdown prompt to update PROGRESS.md |
| `AppExtensionProvider.ts` | ✅ Done | Single entry point, boots all extensions |

**Conventions established:**
- Extension = plumbing (TypeScript hooks/tools/commands)
- Agent file = instructions (`.pi/agents/*.md`)
- Use branches: `feat/*`, `fix/*`, `refactor/*` → merge to main

**Next for toolkit:**
- Write first blog posts using `blog-writer.ts` (after portfolio is built)

---

## Portfolio Site Status

**Repo:** `bernardjbs/sinfat-portfolio` (private GitHub)
**Local:** `https://sinfat.test` (Valet)
**Production:** `https://sinfat.com` (Oracle Cloud Arm A1 VM)
**Spec:** `/Users/bernard/code/sinfat-portfolio/SPEC.md`
**Planning:** `/Users/bernard/code/ai-learning/pi-vs-claude-code/docs/PORTFOLIO.md`

### Infrastructure ✅
- Oracle Cloud Arm A1 VM (Ubuntu 22.04)
- Nginx + PHP 8.3-FPM + MySQL 8 + Composer + Node 22
- Laravel 12 installed at `/var/www/sinfat`
- Cloudflare DNS + proxy + SSL
- iptables open on ports 80 + 443
- SSH key: `~/.ssh/sinfat-portfolio.key`

### Infrastructure Still Needed
- [x] Redis on VM
- [x] Let's Encrypt SSL (Cloudflare Full mode)
- [x] GitHub Actions auto-deploy
- [ ] Supervisor for queue workers

### Tech Decisions
- **Architecture:** Full SPA (Vue Router + Laravel pure API)
- **AI runtime:** Neuron AI (`neuron-core/neuron-ai`)
- **Streaming:** SSE (Server-Sent Events)
- **Markdown editor:** md-editor-v3
- **Auth:** Custom (no Breeze/Jetstream)
- **Rate limiting:** Redis
- **Vue API:** Options API
- **Font:** Geist Mono
- **Icons:** Lucide (`lucide-vue-next`)
- **Dark mode:** Toggle, dark default
- **Contact:** Links only (no form)
- **Analytics:** None for now

### Build Modules (from SPEC.md)
| Module | Title | Status | Model |
|--------|-------|--------|-------|
| 1 | Infrastructure & Server Setup | ✅ | 🟢 Sonnet |
| 2 | Database Schema | ✅ | 🔴 Opus |
| 3 | Authentication | ✅ | 🟢 Sonnet |
| 4 | API Contract | ✅ | 🔴 Opus |
| 5 | Blog (Admin + Public) | ✅ | 🟢 Sonnet |
| 6 | AI Integration | ✅ | 🔴 Opus |
| 7 | Guest Playground | ✅ | 🔴 Opus |
| 8 | Frontend SPA Foundation | ✅ | 🟢 Sonnet |
| 9 | Static Pages | ✅ | 🟢 Sonnet |
| 10 | Sitemap + SEO | ✅ | 🟢 Sonnet |
| 11 | Deploy Pipeline Polish | ✅ | 🟢 Sonnet |

### AI Provider (2026-03-10)
- **Production:** GitHub Models (gpt-4o-mini) — free via fine-grained PAT
- **Local:** Ollama (llama3.2)
- Gemini attempted but free tier requires billing account
- Supported providers: `github`, `anthropic`, `gemini`, `ollama`

### Pi Skills (built 2026-03-07)
- [x] `portfolio-context/SKILL.md` — architecture, stack, routes, env, colour tokens
- [x] `laravel-conventions/SKILL.md` — Controller→Service→Resource, naming, patterns
- [x] `vue-conventions/SKILL.md` — Options API, Pinia, Tailwind rules, SSE pattern
- [x] `terminal-aesthetic/SKILL.md` — colour palette, Geist Mono, spacing, component shapes
- [x] `neuron-ai-patterns/SKILL.md` — agent classes, SSE streaming, guest key security
- [x] `git-conventions/SKILL.md` — branch names, commit format, workflow, deploy trigger
- [x] `document-writer/SKILL.md` — small file naming, splitting rules, directory structure

### Pi Agents (built 2026-03-07)
- [x] `backend-developer.md` — Laravel API, migrations, services, resources
- [x] `frontend-developer.md` — Vue Options API, Pinia, terminal aesthetic
- [x] `database-architect.md` — migrations, schema review, indexes
- [x] `security-reviewer.md` — auth, rate limiting, guest key, LLM security
- [x] `test-writer.md` — PHPUnit feature/unit tests, factory states, AI mocking
- [x] `git-assistant.md` — staged diff review, conventional commit messages
- [x] `pr-reviewer.md` — branch review before merge, structured report

### Session Housekeeping (2026-03-07)
- [x] SESSION.md moved to `sinfat-portfolio/.pi/SESSION.md` — lives with the project it serves
- [x] All docs moved to `pi-vs-claude-code/docs/` — root has only CLAUDE.md + README.md
- [x] SUMMARY.md split into `docs/summaries/` — 10 topic files, 3–14KB each
- [x] SPEC.md split into `specs/` — one file per module, 2–3KB each
- [x] All skill files have YAML frontmatter — Pi will now discover and load them
- [x] pi-vs-claude-code pushed to private repo: `bernardjbs/pi-playground`
- [x] sinfat-portfolio committed and pushed with full .pi/ scaffold

---

## Session Notes

### 2026-03-02
- Tutorial created from IndyDevDan's YouTube transcript
- All 12 modules planned and written to TUTORIAL.md
- Repo already cloned at `/Users/bernard/code/ai-learning/pi-vs-claude-code`
- RooFoot2 already has: `.pi/agents/`, `.pi/extensions/confirm-migrations.ts`, `.pi/prompts/`, `.pi/skills/flashscore-scraper`

**Module 1 completed:**
- `just pi` is not truly vanilla — `.pi/` folder auto-loads themes, agents, skills
- Fixed skill conflict: moved `.pi/skills/bowser.md` → `.pi/skills/bowser/SKILL.md`
- Installed `@mariozechner/pi-coding-agent` locally for editor type resolution
- Global npm install only provides the `pi` binary, not editor types
- Learned about `just` command runner (brew install just) — shortcuts for long pi -e commands
- Extensions load at startup only — cannot add to a running session

---

## Outstanding Items
- Email `bernard@sinfat.com` mailbox not configured
- Football Analytics project description is placeholder — real story TBD
- `og:image` not set — no images on the site yet
- Light mode could use further colour tuning
- Submit sitemap to Google Search Console
- Supervisor for queue workers not yet set up on prod
- GitHub Models PAT (`sinfat-ai`) expires — check expiry and rotate before it lapses

---

## Wins / Key Learnings
- All 11 modules complete — sinfat.com live and fully deployed
- GitHub Models provides free AI without billing — OpenAILike provider in Neuron AI
- Platform PHP pin in composer.json prevents server/local version drift
- Redis server ≠ PHP Redis extension — both required
- Never swallow exceptions silently — always log the real error

