---
name: project-terminal-aesthetic
description: Design system for sinfat.com. Use when making any UI decisions — colours, typography, spacing, or component design. Defines the exact Tailwind colour tokens, Geist Mono font rules, component shapes, and what the terminal aesthetic must not include.
---

# Terminal Aesthetic — sinfat.com Design System

Use this skill when making any UI decisions — layout, colour, typography, spacing, component design. The goal is a minimal, monospace, terminal-feel developer portfolio. Professional and quietly confident. Not flashy.

If `--help` appears anywhere in the user's invocation, read and display [help.md](help.md) and stop.

**Building or restyling a component** (nav, buttons, cards, inputs, badges, post cards, series, code blocks, streaming output), the spacing scale, or the blog prose classes: read [references/components.md](references/components.md).

---

## The Aesthetic in One Sentence

> Feels like a developer's environment — dark, monospace, green accents — rendered as a web page, not a marketing site.

---

## Colour Palette

These tokens are the palette; the hex values are the dark theme's, copied 2026-11-11. Outside them, components use only Tailwind `white`, `black/60` (overlays) and the green and red shades in [references/components.md](references/components.md).

| Token | Hex | Use |
|-------|-----|-----|
| `bg` | `#0d1117` | Page background — near black (GitHub dark) |
| `surface` | `#161b22` | Cards, panels, modal backgrounds, code blocks |
| `accent` | `#238636` | Primary accent — links, highlights, active states, focus rings, CTA buttons |
| `text` | `#e6edf3` | Body text — off white |
| `dim` | `#6e7681` | Secondary text — labels, metadata, dates, timestamps, muted descriptions |
| `body` | `#8b949e` (light `#656d76`) | Blog post body text: paragraphs, list items, blockquotes, table cells. At least 4.5:1 in both themes |
| `link` | `#3fb950` (light `#1a7f37`) | Prose links, inline code, the series mark and series links; nav and buttons keep `accent` |
| `border` | `#30363d` | Borders, dividers, input outlines |

**Error state:** token `danger` (`#f85149` dark, `#cf222e` light) — validation errors, danger actions.
**Warning:** `#d29922` (GitHub dark yellow) — sparingly, if needed.

**Do not introduce new colours** without updating `resources/css/app.css` and this document.

---

## Typography

**Font:** Geist Mono, loaded from Google Fonts
- Monospace throughout: headings, UI labels, meta, nav, inline code, code blocks
- **One exception:** blog post body text (paragraphs, list items, blockquotes) is Geist sans,
  18px/1.6, 17px under 600px (ruled 2026-11-10, module 16); post title 36px/1.15 mono, h2 22px
- No serif fonts
- Weights: Geist Mono 400, 500, 600; Geist 400, 500

```css
/* resources/css/app.css */
@import url('https://fonts.googleapis.com/css2?family=Geist:wght@400;500&family=Geist+Mono:wght@400;500;600&display=swap');

body {
  font-family: 'Geist Mono', monospace;
}
```

**Type scale (Tailwind):**
```
text-xs   → metadata, timestamps, badges
text-sm   → secondary content, nav items, labels
text-base → body text, descriptions
text-lg   → section headings, card titles
text-xl   → page section titles
text-2xl  → page hero headings
text-3xl  → homepage headline only
```

---

## Layout Principles

1. **Max width:** `max-w-3xl` or `max-w-4xl` for content — never full bleed text; post pages: 38rem prose with a left 11rem contents track, code and tables break out right by 12rem; one column under 1100px (`.post-*` rules in `resources/css/app.css`)
2. **Centering:** `mx-auto px-4 sm:px-6`
3. **Vertical rhythm:** consistent `space-y-8` between sections, `space-y-4` within
4. **No hero images** — text-first, no stock photos, no abstract gradients
5. **No rounded corners on main containers** — use `rounded` (4px) for small elements only (badges, buttons)
6. **Borders not shadows** — `border border-border` instead of `shadow-*`

---

## Dark Mode

- Dark is the default — `<html class="dark">` set on load
- Light mode available via toggle — stored in localStorage
- Toggle: sun/moon icon from lucide-vue-next, top-right nav
- Light palette: the `@theme` values in `resources/css/app.css` are light; `.dark` overrides them
- Most development effort is dark-first

---

## What the Aesthetic Is NOT

- ❌ No hero images, stock photos, abstract gradients
- ❌ No bright colours (no orange/purple/pink accents)
- ❌ No rounded-full pill shapes on containers
- ❌ No animations beyond subtle opacity/colour transitions
- ❌ No loading spinners — use skeleton text or simple "loading..." text
- ❌ No emoji in UI (exception: code examples or blog content)
- ❌ No card shadows — borders only
- ❌ No gradients on backgrounds
- ❌ No serif fonts; sans only for blog post body text

---

## Tailwind Config Reference

Tailwind v4: there is no `tailwind.config.js`. Fonts and colour tokens live in `resources/css/app.css`: light values in `@theme`, dark overrides under `.dark`. Read that file for the current values; it wins over the dated copy in the palette table.
