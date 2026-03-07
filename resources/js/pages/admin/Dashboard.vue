<template>
    <div class="min-h-screen bg-bg text-text flex items-center justify-center">
        <div>
            <p class="text-dim text-sm">admin</p>
            <h1 class="text-text text-xl font-medium mt-1">dashboard</h1>
            <p class="text-dim text-xs mt-4">module 8 will build this out.</p>
            <button
                @click="handleLogout"
                class="mt-6 border border-border text-dim text-sm px-4 py-2 hover:border-accent hover:text-accent transition"
            >
                sign out
            </button>
        </div>
    </div>
</template>

<script>
import { useAuthStore } from '../../stores/auth.js'
import { mapActions } from 'pinia'

export default {
    name: 'Dashboard',

    methods: {
        ...mapActions(useAuthStore, ['logout']),

        async handleLogout() {
            await this.logout()
            this.$router.push('/login')
        },
    },
}
</script>
