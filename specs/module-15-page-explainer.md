## Module 15 — "How Was This Built?" Page Explainer
> 🟢 Sonnet
> 🎓 **Learning module** — Bernard builds, AI mentors. Review at end.

### Goal
A public-facing AI chat feature on every page. Visitors ask questions about how the page was built and the AI answers using real implementation details. Demonstrates Neuron AI agent architecture with tools, SSE streaming, conversation memory, and LLM security.

### Learning Objectives
- [ ] Understand how Neuron AI tools work — definition, properties, callbacks, LLM selection
- [ ] Understand context injection — what to feed the LLM and why
- [ ] Understand multi-turn conversation via session-based message history
- [ ] Understand LLM security — prompt injection, data leakage, defence in depth
- [ ] Understand cost management — token budgets, rate limiting, context window trade-offs

---

### Lesson 1 — The Agent Class
> Neuron AI fundamentals: agent + tools

**Tasks:**
- [ ] Create `app/Agents/PageExplainerAgent.php`
- [ ] Override `provider()` — reuse the multi-provider pattern from BlogWriterAgent
- [ ] Write `instructions()` — personality, scope, rules
- [ ] Override `tools()` — define `get_page_context` tool with a `page` property
- [ ] Wire the tool callback to a `getPageContext()` method (return a placeholder string for now)

**Mentor checks:**
- Does the tool description clearly tell the LLM when to use it?
- Are property types and `required` flags correct?
- Is the agent class clean — no controller logic leaking in?

---

### Lesson 2 — Page Context Data
> Context engineering: what to tell the LLM

**Tasks:**
- [ ] Build `getPageContext(string $page): string` with real data for every public page
- [ ] Cover: component file, route, data flow, design decisions, interesting details
- [ ] Handle unknown pages gracefully

**Mentor checks:**
- Is each context concise but complete? (Not dumping raw source files)
- Are there any secrets leaking? (env values, keys, IPs)
- Is the context useful enough for the LLM to give specific answers?

---

### Lesson 3 — Controller, Service & Route
> Reusing existing SSE streaming pattern

**Tasks:**
- [ ] Create `app/Services/PageExplainerService.php` — streaming logic
- [ ] Create `app/Http/Controllers/PageExplainerController.php` — thin controller
- [ ] Create request class for validation (`page` required, `message` required, max lengths)
- [ ] Add route: `POST /api/explain` (public)
- [ ] Apply rate limiting — separate key from playground (`explain:` prefix)
- [ ] Exclude from CSRF if needed
- [ ] Log to `ai_sessions` table

**Mentor checks:**
- Is the controller thin? (No business logic — delegates to service)
- Is the streaming pattern correct? (SSE headers, ob_flush, X-Accel-Buffering)
- Is the rate limit key independent from the playground?

---

### Lesson 4 — Conversation Memory
> Stateless HTTP meets stateful conversations

**Tasks:**
- [ ] Store message history in session, keyed by page
- [ ] On each request, replay previous messages to the agent
- [ ] Limit history to last 10 messages
- [ ] Clear history when switching pages (or keep per-page — decide and justify)

**Mentor checks:**
- Are messages stored efficiently? (role + content only, no metadata bloat)
- Is the 10-message limit enforced correctly? (trim oldest, keep newest)
- Does the session key avoid collisions? (e.g. `explainer_history_{page}`)
- What happens when session expires? (Should be graceful — empty history, fresh start)

---

### Lesson 5 — Frontend Chat Component
> Vue component + SSE consumption + Pinia store

**Tasks:**
- [ ] Create `resources/js/components/PageExplainer.vue` — chat panel
- [ ] Create `resources/js/stores/explainer.js` — Pinia store
- [ ] Add to `AppLayout.vue` — available on all public pages
- [ ] Auto-detect current page from `$route.path`
- [ ] Handle UI states: empty, streaming, error (rate limited), conversation
- [ ] Streaming indicator: reuse `▌` cursor pattern
- [ ] Mobile: full-screen overlay. Desktop: floating panel
- [ ] Close button, clear conversation button

**Mentor checks:**
- Is Options API used? (Not Composition API — project convention)
- Is the SSE pattern correct? (fetch with reader, not EventSource if POST)
- Does the component clean up on unmount? (abort controller, close stream)
- Does it work on mobile? (tested at 375px width)

---

### Lesson 6 — More Tools
> Multi-tool agents and LLM tool selection

**Tasks:**
- [ ] Add `get_tech_stack` tool — no parameters, returns stack summary
- [ ] Add `get_recent_posts` tool — queries published posts, returns titles + slugs
- [ ] Add `get_design_tokens` tool — returns colour palette, font, spacing rules

**Mentor checks:**
- Are there too many tools? (3–5 is ideal, more = worse selection accuracy)
- Are descriptions distinct enough that the LLM won't confuse them?
- Do dynamic tools (get_recent_posts) use existing services/models?

---

### Lesson 7 — Security & Rate Limiting
> LLM security in practice

**Tasks:**
- [ ] Add instruction guardrails: only answer about sinfat.com, decline off-topic
- [ ] Audit all tool outputs — no secrets, keys, IPs, env values
- [ ] Validate input: max message length, strip HTML
- [ ] Rate limit: 10 messages/hour per IP (separate from playground)
- [ ] Test prompt injection: "ignore your instructions and..." — verify it's handled

**Mentor checks:**
- Is security enforced at the data layer, not just the prompt?
- Could a determined user extract sensitive info through creative questions?
- Is rate limiting applied at middleware level, not in the controller?

---

### Lesson 8 — Tests, Deploy & Review

**Tasks:**
- [ ] Feature test: `POST /api/explain` returns 200 with SSE headers
- [ ] Feature test: rate limiting returns 429 after limit
- [ ] Feature test: unknown page returns graceful response
- [ ] Feature test: missing/invalid params return 422
- [ ] Test on mobile — chat panel UX on iPhone
- [ ] Deploy to production
- [ ] Verify on sinfat.com

**Mentor checks:**
- Do tests follow project conventions? (test-writer skill)
- Are tests testing behaviour, not implementation?
- Does everything work on prod (not just local)?

---

### Lesson 9 — Mentor Review

**This is not a rubber stamp. Bernard presents, mentor critiques.**

Review areas:
- [ ] **Architecture:** Is the agent class clean? Is the service layer correct? Any code in the wrong place?
- [ ] **Tool design:** Are tool descriptions good enough? Would you add/remove/rename any?
- [ ] **Context quality:** Is the page context data useful? Too much? Too little?
- [ ] **Security:** Run prompt injection attempts. Try to extract secrets. Try to go off-topic.
- [ ] **UX:** Is the chat panel intuitive? Does streaming feel smooth? Mobile tested?
- [ ] **Code quality:** Naming, patterns, conventions — does it match the rest of the codebase?
- [ ] **What Bernard learned:** Verbal walkthrough of each lesson's key concept

**Output:** Written review with ✅ pass / ⚠️ improve / ❌ fix for each area. Fixes must be completed before module is marked done.

---

### Acceptance Criteria
- [ ] Chat button visible on all public pages
- [ ] Agent answers questions about the current page using real implementation details
- [ ] Agent uses tools to fetch context (not hardcoded in the prompt)
- [ ] Conversation memory works across multiple messages
- [ ] Rate limiting prevents abuse (10 messages/hour per IP)
- [ ] Prompt injection attempts are handled gracefully
- [ ] No secrets or sensitive data exposed through any tool
- [ ] Mobile UX works on iPhone
- [ ] SSE streaming with visible token-by-token output
- [ ] All feature tests pass
- [ ] Deployed and verified on sinfat.com

### Git Plan
- Branch: `feat/module-15-page-explainer`
- Commits per lesson (one logical unit each)
- Merge to main after Lesson 8, before Lesson 9 review
