<template>
    <nav class="border-b border-border bg-bg relative">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 flex items-center justify-between h-14">
            <!-- Logo -->
            <router-link to="/" class="text-accent text-sm font-semibold tracking-tight">
                sinfat
            </router-link>

            <!-- Desktop nav links + theme toggle -->
            <div class="hidden sm:flex items-center gap-6">
                <router-link
                    v-for="link in links"
                    :key="link.to"
                    :to="link.to"
                    class="text-dim text-sm hover:text-text transition-colors"
                    active-class="!text-accent"
                >
                    {{ link.label }}
                </router-link>

                <ThemeToggle />
            </div>

            <!-- Mobile: theme toggle + hamburger -->
            <div class="flex sm:hidden items-center gap-3">
                <ThemeToggle />
                <button
                    class="text-dim hover:text-text transition-colors p-1"
                    title="Menu"
                    @click="mobileOpen = !mobileOpen"
                >
                    <X v-if="mobileOpen" :size="18" />
                    <Menu v-else :size="18" />
                </button>
            </div>
        </div>

        <!-- Mobile dropdown -->
        <div
            v-if="mobileOpen"
            class="sm:hidden border-t border-border bg-bg"
        >
            <div class="max-w-4xl mx-auto px-4 py-3 flex flex-col gap-3">
                <router-link
                    v-for="link in links"
                    :key="link.to"
                    :to="link.to"
                    class="text-dim text-sm hover:text-text transition-colors"
                    active-class="!text-accent"
                    @click="mobileOpen = false"
                >
                    {{ link.label }}
                </router-link>
            </div>
        </div>
    </nav>
</template>

<script>
import ThemeToggle from './ThemeToggle.vue'
import { Menu, X } from 'lucide-vue-next'

export default {
    name: 'NavBar',

    components: { ThemeToggle, Menu, X },

    data() {
        return {
            mobileOpen: false,
            links: [
                { to: '/blog', label: 'blog' },
                { to: '/projects', label: 'projects' },
                { to: '/about', label: 'about' },
                { to: '/uses', label: 'uses' },
                { to: '/playground', label: 'playground' },
            ],
        }
    },

    watch: {
        $route() {
            this.mobileOpen = false
        },
    },
}
</script>
