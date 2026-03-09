<template>
    <div class="max-w-3xl mx-auto px-4 sm:px-6 py-16 text-center">
        <h1 class="text-2xl text-text mb-4">404</h1>
        <p class="text-dim text-sm mb-6">page not found.</p>
        <router-link
            to="/"
            class="text-accent text-sm hover:underline"
        >
            ← back to home
        </router-link>
    </div>
</template>

<script>
export default {
    name: 'NotFoundPage',
}
</script>
