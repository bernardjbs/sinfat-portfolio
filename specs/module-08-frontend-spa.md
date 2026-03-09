## Module 8 — Frontend SPA Foundation
> 🟢 Sonnet

### Goal
Vue SPA scaffolded with routing, Pinia stores, layout components, and design system in place.

### Tasks
- [x] Install and configure Vue Router
- [x] Install and configure Pinia
- [x] Install Lucide Vue: `npm install lucide-vue-next`
- [x] ~~Install md-editor-v3~~ — skipped, Vite build issue on server. Using textarea + marked instead.
- [x] Configure Geist Mono font
- [x] Configure Tailwind with terminal palette + typography plugin
- [x] Build `AppLayout.vue` — nav, footer, dark/light toggle
- [x] Build `AdminLayout.vue` — sidebar, admin nav
- [x] Set up all routes in `router/index.js`
- [x] Set up Pinia stores

### Technical Detail

**Tailwind config:**
```js
// tailwind.config.js
export default {
    darkMode: 'class',
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
            }
        }
    },
    plugins: [require('@tailwindcss/typography')],
}
```

**Geist Mono font (`resources/css/app.css`):**
```css
@import url('https://fonts.googleapis.com/css2?family=Geist+Mono:wght@400;500;600&display=swap');
```

**Pinia stores:**
```
stores/
    auth.js        → isAuthenticated, user, login(), logout()
    theme.js       → isDark, toggle()
    playground.js  → usageCount, isStreaming, content, apiKey
    blog.js        → posts, currentPost, loading
```

**Vue Router routes:**
```js
const routes = [
    { path: '/',              component: () => import('./pages/Home.vue') },
    { path: '/about',         component: () => import('./pages/About.vue') },
    { path: '/projects',      component: () => import('./pages/Projects.vue') },
    { path: '/blog',          component: () => import('./pages/Blog.vue') },
    { path: '/blog/:slug',    component: () => import('./pages/BlogPost.vue') },
    { path: '/uses',          component: () => import('./pages/Uses.vue') },
    { path: '/contact',       component: () => import('./pages/Contact.vue') },
    { path: '/playground',    component: () => import('./pages/Playground.vue') },
    { path: '/login',         component: () => import('./pages/Login.vue') },
    {
        path: '/admin',
        component: () => import('./layouts/AdminLayout.vue'),
        meta: { requiresAuth: true },
        children: [
            { path: '',       component: () => import('./pages/admin/Dashboard.vue') },
            { path: 'blog',   component: () => import('./pages/admin/Blog.vue') },
            { path: 'blog/:id', component: () => import('./pages/admin/BlogEditor.vue') },
        ]
    },
    { path: '/:pathMatch(.*)*', component: () => import('./pages/NotFound.vue') },
]
```

**Auth guard:**
```js
router.beforeEach((to, from, next) => {
    const auth = useAuthStore()
    if (to.meta.requiresAuth && !auth.isAuthenticated) {
        next('/login')
    } else {
        next()
    }
})
```

### Acceptance Criteria
- [x] `https://sinfat.test` loads Vue SPA
- [x] All routes navigate without page reload
- [x] Dark/light toggle works and persists in localStorage
- [x] Unauthenticated visit to `/admin` redirects to `/login`
- [x] Geist Mono font rendering in browser
- [x] Tailwind palette applied — bg colour matches `#0d1117`
- [x] Pinia stores initialised

### Dependencies
Module 3 (auth guard needs auth store)

---

