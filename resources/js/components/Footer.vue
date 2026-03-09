<template>
    <footer class="border-t border-border bg-bg">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 py-8 flex items-center justify-between">
            <span class="text-dim text-xs">© {{ year }} sinfat.com</span>
            <div class="flex items-center gap-4">
                <a
                    href="https://github.com/bernardjbs"
                    target="_blank"
                    rel="noopener noreferrer"
                    class="text-dim text-xs hover:text-text transition-colors"
                >
                    github
                </a>
                <router-link
                    to="/contact"
                    class="text-dim text-xs hover:text-text transition-colors"
                >
                    contact
                </router-link>
            </div>
        </div>
    </footer>
</template>

<script>
export default {
    name: 'AppFooter',

    computed: {
        year() {
            return new Date().getFullYear()
        },
    },
}
</script>
