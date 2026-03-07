import { defineStore } from 'pinia'

export const useAuthStore = defineStore('auth', {
    state: () => ({
        isAuthenticated: false,
        user: null,
    }),

    actions: {
        async login(email, password) {
            const res = await fetch('/login', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-XSRF-TOKEN': getCsrfToken(),
                },
                body: JSON.stringify({ email, password }),
            })

            const json = await res.json()

            if (!res.ok) {
                throw new Error(json.message || 'Login failed')
            }

            this.isAuthenticated = true
            return json.redirect
        },

        async logout() {
            await fetch('/logout', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-XSRF-TOKEN': getCsrfToken(),
                },
            })

            this.isAuthenticated = false
            this.user = null
        },

        async fetchUser() {
            const res = await fetch('/api/admin/me')
            if (res.ok) {
                const json = await res.json()
                this.user = json.user
                this.isAuthenticated = true
            } else {
                this.isAuthenticated = false
                this.user = null
            }
        },
    },
})

function getCsrfToken() {
    return decodeURIComponent(document.cookie.match(/XSRF-TOKEN=([^;]+)/)?.[1] ?? '')
}
