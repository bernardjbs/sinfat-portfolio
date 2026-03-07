## Module 8 — Frontend SPA Foundation
> 🟢 Sonnet

### Goal
Vue SPA scaffolded with routing, Pinia stores, layout components, and design system in place.

### Tasks
- [ ] Install and configure Vue Router
- [ ] Install and configure Pinia
- [ ] Install Lucide Vue: `npm install lucide-vue-next`
- [ ] Install md-editor-v3: `npm install md-editor-v3`
- [ ] Configure Geist Mono font
- [ ] Configure Tailwind with terminal palette + typography plugin
- [ ] Build `AppLayout.vue` — nav, footer, dark/light toggle
- [ ] Build `AdminLayout.vue` — sidebar, admin nav
- [ ] Set up all routes in `router/index.js`
- [ ] Set up Pinia stores

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
- [ ] `https://sinfat.test` loads Vue SPA
- [ ] All routes navigate without page reload
- [ ] Dark/light toggle works and persists in localStorage
- [ ] Unauthenticated visit to `/admin` redirects to `/login`
- [ ] Geist Mono font rendering in browser
- [ ] Tailwind palette applied — bg colour matches `#0d1117`
- [ ] Pinia stores initialised

### Dependencies
Module 3 (auth guard needs auth store)

---

