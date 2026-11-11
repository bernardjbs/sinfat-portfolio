<template>
    <nav class="border-b border-border bg-bg">
        <div class="max-w-4xl mx-auto px-4 sm:px-6">
            <div class="flex items-center justify-between h-14">
                <!-- Logo -->
                <router-link to="/" class="text-accent text-sm font-semibold tracking-tight">
                    sinfat
                </router-link>

                <!-- Desktop nav links + theme toggle -->
                <div class="hidden sm:flex items-center gap-6">
                    <router-link
                        v-for="link in visibleLinks"
                        :key="link.to"
                        :to="link.to"
                        class="text-dim text-sm hover:text-text transition-colors"
                        active-class="!text-accent"
                    >
                        {{ link.label }}
                    </router-link>

                    <ThemeToggle />
                </div>

                <!-- Mobile: theme toggle only; the links sit in the row below -->
                <div class="flex sm:hidden items-center">
                    <ThemeToggle />
                </div>
            </div>

            <!-- Mobile links: always visible, wrapping on the narrowest phones -->
            <div class="flex sm:hidden flex-wrap gap-x-4 gap-y-2 pb-3">
                <router-link
                    v-for="link in visibleLinks"
                    :key="link.to"
                    :to="link.to"
                    class="text-dim text-sm hover:text-text transition-colors"
                    active-class="!text-accent"
                >
                    {{ link.label }}
                </router-link>
            </div>
        </div>
    </nav>
</template>

<script>
import { mapState } from 'pinia'
import ThemeToggle from './ThemeToggle.vue'
import { usePlaygroundStore } from '../stores/playground.js'

export default {
    name: 'NavBar',

    components: { ThemeToggle },

    data() {
        return {
            links: [
                { to: '/blog', label: 'blog' },
                { to: '/projects', label: 'projects' },
                { to: '/about', label: 'about' },
                { to: '/uses', label: 'uses' },
                { to: '/playground', label: 'playground' },
            ],
        }
    },

    computed: {
        ...mapState(usePlaygroundStore, ['enabled']),

        visibleLinks() {
            return this.links.filter(link => link.to !== '/playground' || this.enabled)
        },
    },
}
</script>
