# Module 8 — Frontend SPA Foundation

**Date:** 2026-03-09
**Status:** ✅ Complete

## What Was Done

Built the SPA foundation layer — layouts, navigation, theme toggle, stub pages, and refactored routing with code-splitting.

### New Files Created
- `stores/theme.js` — dark/light toggle with localStorage persistence
- `components/ThemeToggle.vue` — sun/moon icon toggle using lucide-vue-next
- `components/NavBar.vue` — site name + nav links + theme toggle
- `components/Footer.vue` — copyright + external links
- `layouts/AppLayout.vue` — wraps public pages (NavBar + router-view + Footer)
- `layouts/AdminLayout.vue` — sidebar navigation + router-view + sign out
- `pages/Home.vue` — hero with intro and CTA buttons
- `pages/About.vue` — stub (Module 9)
- `pages/Projects.vue` — stub (Module 9)
- `pages/Uses.vue` — stub (Module 9)
- `pages/Contact.vue` — stub (Module 9)
- `pages/NotFound.vue` — 404 catch-all

### Modified Files
- `App.vue` — applies theme from store on mount
- `router/index.js` — full refactor: lazy imports, AppLayout/AdminLayout nesting, all routes from spec, catch-all 404
- `pages/admin/Dashboard.vue` — simplified (logout now in AdminLayout)

### Key Decisions
- **md-editor-v3 skipped** — caused Vite build issues on server SSH. BlogEditor uses plain textarea + `marked` for preview instead.
- **Dark is default** — light mode toggle works via localStorage but dark-first design. Light palette refinement deferred.
- **Code-splitting** — lazy imports produce per-page chunks. Main bundle dropped from 203KB to 136KB.
- **Admin sidebar** — chose sidebar over top nav for admin to visually separate admin from public experience.

## Outstanding Items
- Light mode colour palette needs tuning (dark tokens applied to body don't invert)
- Static page content to be filled in Module 9
- Prod deploy triggered by push — GitHub Actions will handle

## Tests
- 58 passing (no new PHP tests needed — purely frontend module)
- Vite build successful with code-splitting

## Next Module
Module 9 — Static Pages
