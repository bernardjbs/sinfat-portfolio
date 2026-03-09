<template>
    <nav class="border-b border-border bg-bg">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 flex items-center justify-between h-14">
            <!-- Logo -->
            <router-link to="/" class="text-accent text-sm font-semibold tracking-tight">
                sinfat
            </router-link>

            <!-- Nav links + theme toggle -->
            <div class="flex items-center gap-6">
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
        </div>
    </nav>
</template>

<script>
import ThemeToggle from './ThemeToggle.vue'

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
}
</script>
