# Components, spacing and prose classes

Read before building or restyling a component. Live classes win: `resources/js/` components and
`resources/css/app.css`.

## Components: What They Should Look Like

### Navigation Bar
```
Dark background (bg-surface or bg-bg, with border-b border-border)
Logo/name on left: "bernard" or "sinfat" in text-accent
Links on right: text-dim, hover:text-text transition
Active link: text-accent (no underline, no bold)
Mobile (under sm): logo + theme toggle, then the links always visible in a second row
  (flex-wrap gap-x-4 gap-y-2 pb-3); no hamburger (ruled 2026-11-11)
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
AI drafted: no badge; inline "· AI drafted" in the meta line, text-dim text-xs
```

### Blog Post Cards
```
border-b border-border py-6
Title: text-text text-lg hover:text-accent transition (link)
Excerpt: font-sans text-body text-sm mt-1
Meta: text-dim text-xs sentence, "15 Mar 2026, 3 minute read." then, in a series, mark + "Part 14 of <series>."
```

### Series (module 17)
```
Mark: lucide-vue-next Layers, 15px text-link (13px in a post card tag); standalone posts get none
Row (SeriesRow.vue): name with mark, count right ("14 parts, complete" / "Being written, 3 parts so far"; on /blog/series, under a status heading, "N parts, last <date>"), pitch in font-sans text-body
Post track: "Part N of M" only when complete, "Part N" while being written, then the series link
After the post: series box replaces date previous/next; parts list collapsed, windowed to 2 either side past 15 (.post-series* in app.css)
```

### Code Blocks (in blog posts)
```
prose styling from @tailwindcss/typography — configured for dark mode
background: bg-surface
text: text-text with syntax highlighting classes
Callout blocks: not built (no rule in app.css, 2026-11-11)
```

### Streaming Text Output (Playground / Admin)
```
bg-bg border border-border p-4 font-mono text-sm text-text
Cursor blink: append "▌" while streaming is active
Smooth: new tokens append directly to content, no flicker
```

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

## Prose (Blog Post) Styling

Blog posts render markdown to HTML from Laravel. Style with `@tailwindcss/typography`. Body text (paragraphs, list items, blockquotes) is Geist sans in the `body` colour, sized by `.post-prose` in `app.css`; inline code is forced back to `font-mono` (it would inherit sans) and shows no backticks. Live classes: `resources/js/pages/BlogPost.vue`.

```vue
<div class="prose prose-invert max-w-none post-prose
            prose-headings:font-mono prose-headings:text-text
            prose-p:font-sans prose-p:text-body
            prose-li:font-sans prose-li:text-body
            prose-blockquote:font-sans prose-blockquote:text-body
            prose-a:text-link prose-a:no-underline
            hover:prose-a:underline
            prose-code:font-mono prose-code:bg-surface prose-code:text-link prose-code:rounded prose-code:px-1
            prose-code:before:content-none prose-code:after:content-none
            prose-pre:bg-surface prose-pre:border prose-pre:border-border
            prose-strong:text-text prose-hr:border-border
            prose-th:text-text prose-th:border-border prose-th:px-3 prose-th:py-1.5
            prose-td:text-body prose-td:border-border prose-td:px-3 prose-td:py-1.5
            prose-table:border-collapse">
  <div v-html="post.content" />
</div>
```
