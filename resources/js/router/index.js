import { createRouter, createWebHistory } from 'vue-router'
import { useAuthStore } from '../stores/auth.js'

const routes = [
    // Public pages — wrapped in AppLayout
    {
        path: '/',
        component: () => import('../layouts/AppLayout.vue'),
        children: [
            {
                path: '',
                name: 'home',
                component: () => import('../pages/Home.vue'),
                meta: {
                    title: 'sinfat.com',
                    description: 'Bernard — full-stack developer (Laravel + Vue) based in Perth, Australia.',
                },
            },
            {
                path: 'blog',
                name: 'blog',
                component: () => import('../pages/Blog.vue'),
                meta: {
                    title: 'blog — sinfat.com',
                    description: 'Writing about Laravel, Vue, AI integration, and building things that work.',
                },
            },
            {
                path: 'blog/:slug',
                name: 'blog-post',
                component: () => import('../pages/BlogPost.vue'),
                props: true,
                meta: {
                    title: 'blog — sinfat.com',
                    description: '',
                },
            },
            {
                path: 'projects',
                name: 'projects',
                component: () => import('../pages/Projects.vue'),
                meta: {
                    title: 'projects — sinfat.com',
                    description: 'Things I\'ve built — portfolio site, coding agent toolkit, sports analytics, and more.',
                },
            },
            {
                path: 'about',
                name: 'about',
                component: () => import('../pages/About.vue'),
                meta: {
                    title: 'about — sinfat.com',
                    description: 'Full-stack developer at a Perth-based SaaS company. Laravel, Vue, AI tooling.',
                },
            },
            {
                path: 'uses',
                name: 'uses',
                component: () => import('../pages/Uses.vue'),
                meta: {
                    title: 'uses — sinfat.com',
                    description: 'Tools, hardware, and software I use daily for development.',
                },
            },
            {
                path: 'contact',
                name: 'contact',
                component: () => import('../pages/Contact.vue'),
                meta: {
                    title: 'contact — sinfat.com',
                    description: 'Get in touch — email, GitHub, LinkedIn. Based in Perth, Australia.',
                },
            },
            {
                path: 'playground',
                name: 'playground',
                component: () => import('../pages/Playground.vue'),
                meta: {
                    title: 'playground — sinfat.com',
                    description: 'AI blog post generator — try it live with 3 free generations.',
                },
            },
        ],
    },

    // Login — no layout (standalone page)
    {
        path: '/login',
        name: 'login',
        component: () => import('../pages/Login.vue'),
        meta: {
            title: 'login — sinfat.com',
            description: '',
        },
    },

    // Admin — wrapped in AdminLayout, requires auth
    {
        path: '/admin',
        component: () => import('../layouts/AdminLayout.vue'),
        meta: { requiresAuth: true },
        children: [
            {
                path: '',
                name: 'admin',
                component: () => import('../pages/admin/Dashboard.vue'),
                meta: { title: 'admin — sinfat.com', requiresAuth: true },
            },
            {
                path: 'blog',
                name: 'admin-blog',
                component: () => import('../pages/admin/Blog.vue'),
                meta: { title: 'blog posts — admin', requiresAuth: true },
            },
            {
                path: 'blog/new',
                name: 'admin-blog-new',
                component: () => import('../pages/admin/BlogEditor.vue'),
                meta: { title: 'new post — admin', requiresAuth: true },
            },
            {
                path: 'blog/:id/edit',
                name: 'admin-blog-edit',
                component: () => import('../pages/admin/BlogEditor.vue'),
                props: true,
                meta: { title: 'edit post — admin', requiresAuth: true },
            },
        ],
    },

    // Catch-all 404
    {
        path: '/:pathMatch(.*)*',
        name: 'not-found',
        component: () => import('../pages/NotFound.vue'),
        meta: {
            title: '404 — sinfat.com',
            description: '',
        },
    },
]

const router = createRouter({
    history: createWebHistory(),
    routes,
})

router.beforeEach(async (to, from, next) => {
    const auth = useAuthStore()

    if (to.meta.requiresAuth || to.matched.some(r => r.meta.requiresAuth)) {
        if (!auth.isAuthenticated) {
            await auth.fetchUser()
        }
        auth.isAuthenticated ? next() : next('/login')
    } else {
        next()
    }
})

// Update document title and meta description after each navigation
router.afterEach((to) => {
    const title = to.meta.title || 'sinfat.com'
    const description = to.meta.description || ''

    document.title = title

    let metaDesc = document.querySelector('meta[name="description"]')
    if (metaDesc && description) {
        metaDesc.setAttribute('content', description)
    }

    let ogTitle = document.querySelector('meta[property="og:title"]')
    if (ogTitle) {
        ogTitle.setAttribute('content', title)
    }

    let ogDesc = document.querySelector('meta[property="og:description"]')
    if (ogDesc && description) {
        ogDesc.setAttribute('content', description)
    }
})

export default router
