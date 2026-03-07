<template>
  <div class="max-w-4xl mx-auto px-4 sm:px-6 py-12">

    <!-- Header -->
    <div class="flex items-center justify-between mb-8">
      <h1 class="text-xl text-text">blog posts</h1>
      <router-link
        :to="{ name: 'admin-blog-new' }"
        class="bg-accent text-white px-4 py-2 text-sm font-medium hover:bg-green-700 transition-colors"
      >
        + new post
      </router-link>
    </div>

    <div v-if="loading" class="text-dim text-sm">loading...</div>

    <div v-else-if="error" class="text-danger text-sm">{{ error }}</div>

    <div v-else-if="adminPosts.length === 0" class="text-dim text-sm">
      no posts yet.
    </div>

    <div v-else>
      <!-- Post rows -->
      <div
        v-for="post in adminPosts"
        :key="post.id"
        class="border-b border-border py-4 flex items-center justify-between gap-4"
      >
        <!-- Left: title + meta -->
        <div class="min-w-0">
          <div class="flex items-center gap-3">
            <span class="text-text text-sm truncate">{{ post.title }}</span>
            <!-- Status badge -->
            <span
              v-if="post.status === 'published'"
              class="shrink-0 bg-green-900/30 text-accent border border-green-800 text-xs px-2 py-0.5 rounded"
            >
              published
            </span>
            <span
              v-else
              class="shrink-0 bg-surface text-dim border border-border text-xs px-2 py-0.5 rounded"
            >
              draft
            </span>
          </div>
          <div class="text-dim text-xs mt-1">
            {{ formattedDate(post.created_at) }}
            <span v-if="post.ai_generated"> · AI drafted</span>
          </div>
        </div>

        <!-- Right: actions -->
        <div class="flex items-center gap-3 shrink-0">
          <router-link
            :to="{ name: 'admin-blog-edit', params: { id: post.id } }"
            class="text-dim text-xs hover:text-text transition-colors"
          >
            edit
          </router-link>

          <button
            class="text-dim text-xs hover:text-accent transition-colors"
            :disabled="toggling === post.id"
            @click="handleTogglePublish(post)"
          >
            {{ post.status === 'published' ? 'unpublish' : 'publish' }}
          </button>

          <button
            class="text-dim text-xs hover:text-danger transition-colors"
            :disabled="deleting === post.id"
            @click="handleDelete(post)"
          >
            delete
          </button>
        </div>
      </div>

      <!-- Pagination -->
      <div v-if="adminMeta && adminMeta.last_page > 1" class="flex items-center gap-4 mt-8">
        <button
          v-if="adminMeta.current_page > 1"
          class="text-dim text-sm hover:text-text transition-colors"
          @click="goToPage(adminMeta.current_page - 1)"
        >
          ← prev
        </button>
        <span class="text-dim text-xs">
          page {{ adminMeta.current_page }} of {{ adminMeta.last_page }}
        </span>
        <button
          v-if="adminMeta.current_page < adminMeta.last_page"
          class="text-dim text-sm hover:text-text transition-colors"
          @click="goToPage(adminMeta.current_page + 1)"
        >
          next →
        </button>
      </div>
    </div>

  </div>
</template>

<script>
import { mapState, mapActions } from 'pinia'
import { useBlogStore } from '../../stores/blog.js'

export default {
  name: 'AdminBlogPage',

  data() {
    return {
      toggling: null,
      deleting: null,
    }
  },

  computed: {
    ...mapState(useBlogStore, ['adminPosts', 'adminMeta', 'loading', 'error']),
  },

  methods: {
    ...mapActions(useBlogStore, ['fetchAdminPosts', 'togglePublish', 'deletePost']),

    formattedDate(iso) {
      if (!iso) return ''
      return new Date(iso).toLocaleDateString('en-AU', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
      })
    },

    async goToPage(page) {
      await this.fetchAdminPosts(page)
      window.scrollTo(0, 0)
    },

    async handleTogglePublish(post) {
      this.toggling = post.id
      try {
        await this.togglePublish(post.id)
      } finally {
        this.toggling = null
      }
    },

    async handleDelete(post) {
      if (!confirm(`Delete "${post.title}"?`)) return
      this.deleting = post.id
      try {
        await this.deletePost(post.id)
      } finally {
        this.deleting = null
      }
    },
  },

  async mounted() {
    await this.fetchAdminPosts()
  },
}
</script>
