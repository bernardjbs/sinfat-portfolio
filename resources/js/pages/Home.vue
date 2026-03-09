<template>
    <div class="max-w-3xl mx-auto px-4 sm:px-6 py-16">
        <div class="space-y-6">
            <p class="text-dim text-sm">hi, i'm</p>
            <h1 class="text-3xl text-text font-semibold">bernard</h1>
            <p class="text-dim text-base leading-relaxed max-w-xl">
                full-stack developer building things with Laravel, Vue, and whatever else gets the job done.
            </p>

            <div class="flex gap-4 pt-4">
                <router-link
                    to="/blog"
                    class="bg-accent text-white px-4 py-2 text-sm font-medium hover:bg-green-700 transition"
                >
                    read the blog
                </router-link>
                <router-link
                    to="/projects"
                    class="border border-border text-text px-4 py-2 text-sm hover:border-accent hover:text-accent transition"
                >
                    see projects
                </router-link>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: 'HomePage',
}
</script>
