import { createRouter, createWebHistory } from 'vue-router'
import { useAuthStore } from '../stores/auth.js'

import Login from '../pages/Login.vue'
import Playground from '../pages/Playground.vue'
import Dashboard from '../pages/admin/Dashboard.vue'
import Blog from '../pages/Blog.vue'
import BlogPost from '../pages/BlogPost.vue'
import AdminBlog from '../pages/admin/Blog.vue'
import AdminBlogEditor from '../pages/admin/BlogEditor.vue'

const routes = [
    {
        path: '/',
        redirect: '/blog',
    },
    {
        path: '/login',
        name: 'login',
        component: Login,
    },
    // Public blog
    {
        path: '/blog',
        name: 'blog',
        component: Blog,
    },
    {
        path: '/blog/:slug',
        name: 'blog-post',
        component: BlogPost,
        props: true,
    },
    // Playground
    {
        path: '/playground',
        name: 'playground',
        component: Playground,
    },
    // Admin
    {
        path: '/admin',
        name: 'admin',
        component: Dashboard,
        meta: { requiresAuth: true },
    },
    {
        path: '/admin/blog',
        name: 'admin-blog',
        component: AdminBlog,
        meta: { requiresAuth: true },
    },
    {
        path: '/admin/blog/new',
        name: 'admin-blog-new',
        component: AdminBlogEditor,
        meta: { requiresAuth: true },
    },
    {
        path: '/admin/blog/:id/edit',
        name: 'admin-blog-edit',
        component: AdminBlogEditor,
        props: true,
        meta: { requiresAuth: true },
    },
    // additional routes added in Module 8
]

const router = createRouter({
    history: createWebHistory(),
    routes,
})

router.beforeEach(async (to, from, next) => {
    const auth = useAuthStore()

    if (to.meta.requiresAuth) {
        if (!auth.isAuthenticated) {
            await auth.fetchUser()
        }
        auth.isAuthenticated ? next() : next('/login')
    } else {
        next()
    }
})

export default router
