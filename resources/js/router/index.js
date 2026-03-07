import { createRouter, createWebHistory } from 'vue-router'
import { useAuthStore } from '../stores/auth.js'

import Login from '../pages/Login.vue'
import Dashboard from '../pages/admin/Dashboard.vue'

const routes = [
    {
        path: '/login',
        name: 'login',
        component: Login,
    },
    {
        path: '/admin',
        name: 'admin',
        component: Dashboard,
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
