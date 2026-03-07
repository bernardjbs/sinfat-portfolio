---
name: terminal-aesthetic
description: Design system for sinfat.com. Use when making any UI decisions — colours, typography, spacing, or component design. Defines the exact Tailwind colour tokens, Geist Mono font rules, component shapes, and what the terminal aesthetic must not include.
---

# Terminal Aesthetic — sinfat.com Design System

Use this skill when making any UI decisions — layout, colour, typography, spacing, component design. The goal is a minimal, monospace, terminal-feel developer portfolio. Professional and quietly confident. Not flashy.

---

## The Aesthetic in One Sentence

> Feels like a developer's environment — dark, monospace, green accents — rendered as a web page, not a marketing site.

---

## Colour Palette

These are the ONLY colours used. They match the Tailwind config tokens exactly.

| Token | Hex | Use |
|-------|-----|-----|
| `bg` | `#0d1117` | Page background — near black (GitHub dark) |
| `surface` | `#161b22` | Cards, panels, modal backgrounds, code blocks |
| `accent` | `#238636` | Primary accent — links, highlights, active states, focus rings, CTA buttons |
| `text` | `#e6edf3` | Body text — off white |
| `dim` | `#6e7681` | Secondary text — labels, metadata, dates, timestamps, muted descriptions |
| `border` | `#30363d` | Borders, dividers, input outlines |

**Error state:** `#f85149` (GitHub dark red) — validation errors, danger actions. Can be added to config as `danger`.
**Warning:** `#d29922` (GitHub dark yellow) — sparingly, if needed.

**Do not introduce new colours** without updating `tailwind.config.js` and this document.

---

## Typography

**Font:** Geist Mono — loaded from Google Fonts
- Monospace throughout — headings, body, UI labels, everything
- No serif or sans-serif fonts on this site
- Weights: 400 (regular), 500 (medium), 600 (semibold)

```css
/* resources/css/app.css */
@import url('https://fonts.googleapis.com/css2?family=Geist+Mono:wght@400;500;600&display=swap');

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

1. **Max width:** `max-w-3xl` or `max-w-4xl` for content — never full bleed text
2. **Centering:** `mx-auto px-4 sm:px-6`
3. **Vertical rhythm:** consistent `space-y-8` between sections, `space-y-4` within
4. **No hero images** — text-first, no stock photos, no abstract gradients
5. **No rounded corners on main containers** — use `rounded` (4px) for small elements only (badges, buttons)
6. **Borders not shadows** — `border border-border` instead of `shadow-*`

---

## Components — What They Should Look Like

### Navigation Bar
```
Dark background (bg-surface or bg-bg, with border-b border-border)
Logo/name on left: "bernard" or "sinfat" in text-accent
Links on right: text-dim, hover:text-text transition
Active link: text-accent (no underline, no bold)
No hamburger menus on mobile — use a simple stacked layout
```

### Buttons
```
Primary CTA: bg-accent text-white px-4 py-2 text-sm font-medium
             hover:bg-green-700 transition
Secondary:   border border-border text-text px-4 py-2 text-sm
             hover:border-accent hover:text-accent transition
Danger:      border border-red-800 text-red-400 — for delete actions
```

### Cards
```
bg-surface border border-border p-4 sm:p-6
No box shadows
Hover state: border-color transitions to accent (subtle)
```

### Inputs and Textareas
```
bg-bg border border-border text-text
focus:border-accent focus:outline-none focus:ring-1 focus:ring-accent
placeholder:text-dim
font-mono (inherited, but be explicit if overriding)
```

### Badges
```
Draft:     bg-surface text-dim border border-border text-xs px-2 py-0.5 rounded
Published: bg-green-900/30 text-accent border border-green-800 text-xs px-2 py-0.5 rounded
AI badge:  bg-surface text-dim text-xs px-2 py-0.5 rounded (small label: "AI drafted")
```

### Blog Post Cards
```
border-b border-border py-6
Title: text-text text-lg hover:text-accent transition (link)
Excerpt: text-dim text-sm mt-1
Meta row: text-dim text-xs — date · "AI drafted" if applicable
```

### Code Blocks (in blog posts)
```
prose styling from @tailwindcss/typography — configured for dark mode
background: bg-surface
text: text-text with syntax highlighting classes
border-l-2 border-accent for callout blocks
```

### Streaming Text Output (Playground / Admin)
```
bg-bg border border-border p-4 font-mono text-sm text-text
Cursor blink: append "▌" while streaming is active
Smooth: new tokens append directly to content, no flicker
```

---

## Dark Mode

- Dark is the default — `<html class="dark">` set on load
- Light mode available via toggle — stored in localStorage
- Toggle: sun/moon icon from lucide-vue-next, top-right nav
- Light palette: invert bg/surface/text tokens in `tailwind.config.js`
- Most development effort is dark-first

---

## What the Aesthetic Is NOT

- ❌ No hero images, stock photos, abstract gradients
- ❌ No bright colours (no orange/purple/pink accents)
- ❌ No rounded-full pill shapes on containers
- ❌ No animations beyond subtle opacity/color transitions
- ❌ No loading spinners — use skeleton text or simple "loading..." text
- ❌ No emoji in UI (exception: code examples or blog content)
- ❌ No card shadows — borders only
- ❌ No gradients on backgrounds
- ❌ No sans-serif or serif fonts — monospace always

---

## Spacing Reference (Tailwind)

```
4px  → p-1  / gap-1  ← tight elements, icon padding
8px  → p-2  / gap-2  ← badge padding, small gaps
12px → p-3  / gap-3  ← list item padding
16px → p-4  / gap-4  ← standard padding
24px → p-6  / gap-6  ← card padding, section spacing
32px → p-8  / gap-8  ← large section gaps
48px → p-12          ← hero section top padding
64px → py-16         ← top-level page sections
```

---

## Tailwind Config Reference

```javascript
// tailwind.config.js
export default {
  darkMode: 'class',
  content: ['./resources/js/**/*.{vue,js}', './resources/views/**/*.blade.php'],
  theme: {
    extend: {
      fontFamily: {
        mono: ['Geist Mono', 'monospace'],
      },
      colors: {
        bg:      '#0d1117',
        surface: '#161b22',
        accent:  '#238636',
        text:    '#e6edf3',
        dim:     '#6e7681',
        border:  '#30363d',
      },
    },
  },
  plugins: [require('@tailwindcss/typography')],
}
```

---

## Prose (Blog Post) Styling

Blog posts render markdown to HTML from Laravel. Style with `@tailwindcss/typography`:

```vue
<div class="prose prose-invert prose-sm max-w-none
            prose-headings:font-mono prose-headings:text-text
            prose-p:text-dim prose-a:text-accent prose-a:no-underline
            hover:prose-a:underline
            prose-code:bg-surface prose-code:text-accent prose-code:rounded
            prose-pre:bg-surface prose-pre:border prose-pre:border-border">
  <div v-html="post.content" />
</div>
```
