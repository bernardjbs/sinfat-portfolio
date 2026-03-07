## Module 9 — Static Pages
> 🟢 Sonnet

### Goal
All public-facing pages built with content and terminal aesthetic.

### Pages

**Home.vue**
```
Hero: name + one-liner headline
      "Full-stack developer (Laravel + Vue) — helping teams integrate AI responsibly."
CTA: Download CV button + Contact link
Brief: 3-line intro
Featured: 2-3 project cards
Latest: 2-3 blog post previews
```

**About.vue**
```
Professional summary (no employer name — "Perth-based SaaS company since 2022")
Education: ECU CS degree + UWA certificate
Skills: grouped by category (Backend, Frontend, AI, Testing)
Philosophy: short paragraph on how you work
CV download button
```

**Projects.vue**
```
pi-agent-toolkit    → GitHub link + description + tech tags
Football Analytics  → description + honest story (5 iterations) — no live link
Time-Capsule App    → description + GitHub if public
This site           → GitHub link + tech used
```

**Uses.vue**
```
TBD — scan /code directory when ready to build
```

**Contact.vue**
```
GitHub link
LinkedIn link
Email link (mailto)
Location: Perth, Australia
Availability: Open to local roles with WFH flexibility
```

**Blog.vue**
```
Post list — title, excerpt, date, ai_generated badge
Pagination
```

**BlogPost.vue**
```
Title + date + ai_generated badge
Rendered markdown with prose styling
Back to blog link
```

### Acceptance Criteria
- [ ] All pages render correctly at their routes
- [ ] Terminal aesthetic consistent across all pages
- [ ] CV downloads from About + Home
- [ ] All external links open in new tab
- [ ] Mobile responsive

### Dependencies
Module 8

---

