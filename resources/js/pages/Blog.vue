<template>
  <div class="max-w-3xl mx-auto px-4 sm:px-6 py-16">
    <h1 class="text-2xl text-text mb-8">blog</h1>

    <div v-if="loading" class="text-dim text-sm">loading...</div>

    <div v-else-if="error" class="text-danger text-sm">{{ error }}</div>

    <div v-else-if="posts.length === 0" class="text-dim text-sm">
      no posts yet.
    </div>

    <div v-else>
      <BlogPostCard
        v-for="post in posts"
        :key="post.id"
        :post="post"
      />

      <!-- Pagination -->
      <div v-if="meta && meta.last_page > 1" class="flex items-center gap-4 mt-10">
        <button
          v-if="meta.current_page > 1"
          class="text-dim text-sm hover:text-text transition-colors"
          @click="goToPage(meta.current_page - 1)"
        >
          ← prev
        </button>

        <span class="text-dim text-xs">
          page {{ meta.current_page }} of {{ meta.last_page }}
        </span>

        <button
          v-if="meta.current_page < meta.last_page"
          class="text-dim text-sm hover:text-text transition-colors"
          @click="goToPage(meta.current_page + 1)"
        >
          next →
        </button>
      </div>
    </div>
  </div>
</template>

<script>
import { mapState, mapActions } from 'pinia'
import { useBlogStore } from '../stores/blog.js'
import BlogPostCard from '../components/BlogPostCard.vue'

export default {
  name: 'BlogPage',

  components: { BlogPostCard },

  computed: {
    ...mapState(useBlogStore, ['posts', 'meta', 'loading', 'error']),
  },

  methods: {
    ...mapActions(useBlogStore, ['fetchPosts']),

    async goToPage(page) {
      await this.fetchPosts(page)
      window.scrollTo(0, 0)
    },
  },

  async mounted() {
    await this.fetchPosts()
  },
}
</script>
