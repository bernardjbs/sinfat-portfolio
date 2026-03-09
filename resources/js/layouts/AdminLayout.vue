<template>
    <div class="min-h-screen flex bg-bg text-text">
        <!-- Sidebar -->
        <aside class="w-48 shrink-0 border-r border-border bg-surface flex flex-col">
            <!-- Logo -->
            <div class="px-4 py-4 border-b border-border">
                <router-link to="/admin" class="text-accent text-sm font-semibold">
                    sinfat
                </router-link>
                <span class="text-dim text-xs ml-1">admin</span>
            </div>

            <!-- Nav links -->
            <nav class="flex-1 px-2 py-4 space-y-1">
                <router-link
                    v-for="link in links"
                    :key="link.to"
                    :to="link.to"
                    class="block px-3 py-2 text-sm text-dim hover:text-text hover:bg-bg/50 transition-colors rounded"
                    active-class="!text-accent !bg-bg/50"
                >
                    {{ link.label }}
                </router-link>
            </nav>

            <!-- Footer actions -->
            <div class="px-2 py-4 border-t border-border space-y-1">
                <router-link
                    to="/"
                    class="block px-3 py-2 text-xs text-dim hover:text-text transition-colors"
                >
                    ← view site
                </router-link>
                <button
                    class="w-full text-left px-3 py-2 text-xs text-dim hover:text-text transition-colors"
                    @click="handleLogout"
                >
                    sign out
                </button>
            </div>
        </aside>

        <!-- Main content -->
        <main class="flex-1 min-w-0">
            <router-view />
        </main>
    </div>
</template>

<script>
import { mapActions } from 'pinia'
import { useAuthStore } from '../stores/auth.js'

export default {
    name: 'AdminLayout',

    data() {
        return {
            links: [
                { to: '/admin', label: 'dashboard' },
                { to: '/admin/blog', label: 'blog posts' },
            ],
        }
    },

    methods: {
        ...mapActions(useAuthStore, ['logout']),

        async handleLogout() {
            await this.logout()
            this.$router.push('/login')
        },
    },
}
</script>
