<template>
    <div class="min-h-screen flex items-center justify-center bg-bg px-4">
        <div class="w-full max-w-sm">

            <div class="mb-8">
                <p class="text-dim text-sm">sinfat.com</p>
                <h1 class="text-text text-xl font-medium mt-1">admin login</h1>
            </div>

            <form @submit.prevent="submit" class="space-y-4">
                <div>
                    <label class="block text-dim text-xs mb-1">email</label>
                    <input
                        v-model="form.email"
                        type="email"
                        autocomplete="email"
                        required
                        class="w-full bg-bg border border-border text-text text-sm px-3 py-2
                               focus:border-accent focus:outline-none focus:ring-1 focus:ring-accent
                               placeholder:text-dim"
                        placeholder="you@example.com"
                    />
                </div>

                <div>
                    <label class="block text-dim text-xs mb-1">password</label>
                    <input
                        v-model="form.password"
                        type="password"
                        autocomplete="current-password"
                        required
                        class="w-full bg-bg border border-border text-text text-sm px-3 py-2
                               focus:border-accent focus:outline-none focus:ring-1 focus:ring-accent
                               placeholder:text-dim"
                        placeholder="••••••••"
                    />
                </div>

                <p v-if="error" class="text-danger text-xs">{{ error }}</p>

                <button
                    type="submit"
                    :disabled="loading"
                    class="w-full bg-accent text-white text-sm font-medium px-4 py-2
                           hover:bg-green-700 transition disabled:opacity-50 disabled:cursor-not-allowed"
                >
                    {{ loading ? 'signing in...' : 'sign in' }}
                </button>
            </form>

        </div>
    </div>
</template>

<script>
import { useAuthStore } from '../stores/auth.js'
import { mapActions } from 'pinia'

export default {
    name: 'Login',

    data() {
        return {
            form: {
                email: '',
                password: '',
            },
            loading: false,
            error: null,
        }
    },

    methods: {
        ...mapActions(useAuthStore, ['login']),

        async submit() {
            this.loading = true
            this.error = null

            try {
                const redirect = await this.login(this.form.email, this.form.password)
                this.$router.push(redirect || '/admin')
            } catch (e) {
                this.error = e.message
            } finally {
                this.loading = false
            }
        },
    },
}
</script>
