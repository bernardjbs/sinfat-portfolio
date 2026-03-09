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
            },
            {
                path: 'blog',
                name: 'blog',
                component: () => import('../pages/Blog.vue'),
            },
            {
                path: 'blog/:slug',
                name: 'blog-post',
                component: () => import('../pages/BlogPost.vue'),
                props: true,
            },
            {
                path: 'projects',
                name: 'projects',
                component: () => import('../pages/Projects.vue'),
            },
            {
                path: 'about',
                name: 'about',
                component: () => import('../pages/About.vue'),
            },
            {
                path: 'uses',
                name: 'uses',
                component: () => import('../pages/Uses.vue'),
            },
            {
                path: 'contact',
                name: 'contact',
                component: () => import('../pages/Contact.vue'),
            },
            {
                path: 'playground',
                name: 'playground',
                component: () => import('../pages/Playground.vue'),
            },
        ],
    },

    // Login — no layout (standalone page)
    {
        path: '/login',
        name: 'login',
        component: () => import('../pages/Login.vue'),
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
            },
            {
                path: 'blog',
                name: 'admin-blog',
                component: () => import('../pages/admin/Blog.vue'),
            },
            {
                path: 'blog/new',
                name: 'admin-blog-new',
                component: () => import('../pages/admin/BlogEditor.vue'),
            },
            {
                path: 'blog/:id/edit',
                name: 'admin-blog-edit',
                component: () => import('../pages/admin/BlogEditor.vue'),
                props: true,
            },
        ],
    },

    // Catch-all 404
    {
        path: '/:pathMatch(.*)*',
        name: 'not-found',
        component: () => import('../pages/NotFound.vue'),
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

export default router
