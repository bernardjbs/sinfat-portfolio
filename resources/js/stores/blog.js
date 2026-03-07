import { defineStore } from 'pinia'

export const useBlogStore = defineStore('blog', {
    state: () => ({
        // Public
        posts: [],
        currentPost: null,
        meta: null,
        // Admin
        adminPosts: [],
        adminMeta: null,
        adminCurrentPost: null,
        // Shared
        loading: false,
        error: null,
    }),

    actions: {
        // ─── Public ────────────────────────────────────────────────────────

        async fetchPosts(page = 1) {
            this.loading = true
            this.error = null
            try {
                const res = await fetch(`/api/blog?page=${page}`)
                const json = await res.json()
                this.posts = json.data
                this.meta = json.meta
            } catch (e) {
                this.error = e.message
            } finally {
                this.loading = false
            }
        },

        async fetchPost(slug) {
            this.loading = true
            this.error = null
            this.currentPost = null
            try {
                const res = await fetch(`/api/blog/${slug}`)
                if (res.status === 404) {
                    this.error = 'not_found'
                    return
                }
                const json = await res.json()
                this.currentPost = json.data
            } catch (e) {
                this.error = e.message
            } finally {
                this.loading = false
            }
        },

        // ─── Admin ─────────────────────────────────────────────────────────

        async fetchAdminPosts(page = 1) {
            this.loading = true
            this.error = null
            try {
                const res = await fetch(`/api/admin/blog?page=${page}`, {
                    headers: { 'X-XSRF-TOKEN': getCsrfToken() },
                })
                const json = await res.json()
                this.adminPosts = json.data
                this.adminMeta = json.meta
            } catch (e) {
                this.error = e.message
            } finally {
                this.loading = false
            }
        },

        async fetchAdminPost(id) {
            this.loading = true
            this.error = null
            this.adminCurrentPost = null
            try {
                const res = await fetch(`/api/admin/blog/${id}`, {
                    headers: { 'X-XSRF-TOKEN': getCsrfToken() },
                })
                const json = await res.json()
                this.adminCurrentPost = json.data
            } catch (e) {
                this.error = e.message
            } finally {
                this.loading = false
            }
        },

        async createPost(payload) {
            const res = await fetch('/api/admin/blog', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-XSRF-TOKEN': getCsrfToken(),
                },
                body: JSON.stringify(payload),
            })
            const json = await res.json()
            if (!res.ok) throw json
            return json.data
        },

        async updatePost(id, payload) {
            const res = await fetch(`/api/admin/blog/${id}`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-XSRF-TOKEN': getCsrfToken(),
                },
                body: JSON.stringify(payload),
            })
            const json = await res.json()
            if (!res.ok) throw json
            return json.data
        },

        async deletePost(id) {
            const res = await fetch(`/api/admin/blog/${id}`, {
                method: 'DELETE',
                headers: { 'X-XSRF-TOKEN': getCsrfToken() },
            })
            if (!res.ok) throw new Error('Delete failed')
            this.adminPosts = this.adminPosts.filter(p => p.id !== id)
        },

        async togglePublish(id) {
            const res = await fetch(`/api/admin/blog/${id}/publish`, {
                method: 'PATCH',
                headers: { 'X-XSRF-TOKEN': getCsrfToken() },
            })
            const json = await res.json()
            if (!res.ok) throw json
            const updated = json.data
            const idx = this.adminPosts.findIndex(p => p.id === id)
            if (idx !== -1) this.adminPosts[idx] = updated
            return updated
        },
    },
})

function getCsrfToken() {
    return decodeURIComponent(document.cookie.match(/XSRF-TOKEN=([^;]+)/)?.[1] ?? '')
}
