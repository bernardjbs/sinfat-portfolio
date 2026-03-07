---
name: vue-conventions
description: Vue 3 coding conventions for sinfat-portfolio. Use when writing any frontend code — components, stores, routing, or API calls. Options API only, no Composition API, Pinia for shared state, fetch over Axios, SSE streaming pattern.
---

# Vue Conventions — sinfat-portfolio

Use this skill when building any Vue 3 frontend code for this project. All components use Options API. This is a full SPA with Vue Router and Pinia.

---

## Non-Negotiable Rules

1. **Options API only** — no Composition API, no `<script setup>`, no `ref()`, no `reactive()`
2. **Pinia for shared state** — no Vuex, no prop-drilling across routes
3. **Tailwind utility classes** — no custom CSS unless absolutely unavoidable
4. **lucide-vue-next** for all icons
5. **No TypeScript in Vue files** — plain `.vue` with `<script>` (not `<script lang="ts">`)

---

## Component File Structure

```
resources/js/
  app.js                    ← entry point, mounts Vue
  router/
    index.js                ← all routes defined here
  stores/
    auth.js
    theme.js
    playground.js
    blog.js
  layouts/
    AppLayout.vue           ← public pages (nav + footer)
    AdminLayout.vue         ← admin pages (sidebar + admin nav)
  pages/
    Home.vue
    About.vue
    Projects.vue
    Blog.vue
    BlogPost.vue
    Uses.vue
    Contact.vue
    Playground.vue
    Login.vue
    NotFound.vue
    admin/
      Dashboard.vue
      Blog.vue              ← list of all posts
      BlogEditor.vue        ← md-editor-v3 + controls
  components/
    BlogPostCard.vue        ← reusable post preview card
    StreamingOutput.vue     ← SSE token-by-token display
    ApiKeyModal.vue         ← guest key input modal
    ThemeToggle.vue         ← dark/light switch
    NavBar.vue
    Footer.vue
```

---

## Options API Template

Every component follows this shape:

```vue
<template>
  <!-- template here -->
</template>

<script>
import { mapState, mapActions } from 'pinia'
import { useBlogStore } from '../stores/blog.js'

export default {
  name: 'ComponentName',

  components: {
    // registered child components
  },

  props: {
    // explicit props with types and defaults
    slug: {
      type: String,
      required: true,
    },
  },

  data() {
    return {
      // local reactive state
      loading: false,
      error: null,
    }
  },

  computed: {
    ...mapState(useBlogStore, ['posts', 'currentPost']),
    // local computed properties
  },

  methods: {
    ...mapActions(useBlogStore, ['fetchPost']),
    // local methods
    async loadData() {
      this.loading = true
      try {
        await this.fetchPost(this.slug)
      } catch (e) {
        this.error = e.message
      } finally {
        this.loading = false
      }
    },
  },

  async mounted() {
    await this.loadData()
  },
}
</script>
```

---

## Pinia Store Pattern

```javascript
// stores/blog.js
import { defineStore } from 'pinia'

export const useBlogStore = defineStore('blog', {
  state: () => ({
    posts: [],
    currentPost: null,
    loading: false,
    meta: null,
  }),

  getters: {
    publishedPosts: (state) => state.posts.filter(p => p.status === 'published'),
  },

  actions: {
    async fetchPosts(page = 1) {
      this.loading = true
      try {
        const res = await fetch(`/api/blog?page=${page}`)
        const json = await res.json()
        this.posts = json.data
        this.meta = json.meta
      } finally {
        this.loading = false
      }
    },

    async fetchPost(slug) {
      const res = await fetch(`/api/blog/${slug}`)
      const json = await res.json()
      this.currentPost = json.data
    },
  },
})
```

---

## API Calls

Use `fetch` directly — no Axios. All API calls go through Pinia actions (not in component methods unless truly local).

```javascript
// With auth header (admin)
const res = await fetch('/api/admin/blog', {
  headers: {
    'Content-Type': 'application/json',
    'X-XSRF-TOKEN': getCsrfToken(),  // for non-GET requests
  },
})

// Helper to get CSRF token from cookie
function getCsrfToken() {
  return decodeURIComponent(document.cookie.match(/XSRF-TOKEN=([^;]+)/)?.[1] ?? '')
}
```

---

## SSE Streaming Pattern

Used for playground and admin AI generation:

```javascript
// In a Pinia action or component method
startStream(topic) {
  this.content = ''
  this.streaming = true

  const source = new EventSource('/api/playground/generate')

  source.onmessage = (event) => {
    if (event.data === '[DONE]') {
      this.streaming = false
      source.close()
      return
    }
    try {
      const chunk = JSON.parse(event.data)
      this.content += chunk.text
    } catch {
      // ignore parse errors
    }
  }

  source.onerror = () => {
    this.streaming = false
    source.close()
  }
},
```

---

## Tailwind Usage Rules

1. Use semantic colour tokens from config — `bg-bg`, `bg-surface`, `text-text`, `text-dim`, `text-accent`, `border-border`
2. Dark mode via `dark:` prefix when needed (most components are dark by default)
3. No arbitrary values like `text-[#abc123]` — add to tailwind config if a new token is needed
4. Responsive: mobile-first, use `sm:`, `md:`, `lg:` breakpoints

```vue
<!-- ✅ Correct -->
<div class="bg-bg text-text border border-border rounded p-4">
  <span class="text-accent">green highlight</span>
  <span class="text-dim">muted label</span>
</div>

<!-- ❌ Wrong — don't inline arbitrary hex -->
<div style="background: #0d1117">
```

---

## Vue Router — Guard Pattern

```javascript
// router/index.js
import { createRouter, createWebHistory } from 'vue-router'
import { useAuthStore } from '../stores/auth.js'

const router = createRouter({
  history: createWebHistory(),
  routes: [ /* ... */ ],
})

router.beforeEach((to, from, next) => {
  const auth = useAuthStore()
  if (to.meta.requiresAuth && !auth.isAuthenticated) {
    next('/login')
  } else {
    next()
  }
})
```

---

## Component Communication

| Scenario | How |
|----------|-----|
| Parent → child | Props |
| Child → parent | `$emit` |
| Sibling ↔ sibling | Pinia store |
| Page ↔ layout | Pinia store |
| Deep hierarchy | Pinia store |

Avoid prop-drilling beyond one level. Put it in a store.

---

## md-editor-v3 (Admin Only)

The markdown editor only appears in `AdminBlogEditor.vue`:

```vue
<template>
  <MdEditor v-model="content" theme="dark" @save="saveDraft" />
</template>

<script>
import { MdEditor } from 'md-editor-v3'
import 'md-editor-v3/lib/style.css'

export default {
  components: { MdEditor },
  data() {
    return { content: '' }
  },
  methods: {
    saveDraft() {
      // call admin blog store action
    },
  },
}
</script>
```

---

## What NOT to Do

- ❌ No `<script setup>` or Composition API
- ❌ No TypeScript in `.vue` files
- ❌ No Axios — use `fetch`
- ❌ No inline styles or arbitrary Tailwind values
- ❌ No business logic in templates — put it in `methods` or Pinia
- ❌ No direct `document.querySelector` in components — use `$refs`
- ❌ No `v-html` without sanitisation (blog content is safe — rendered server-side by Laravel)
