<template>
  <div class="max-w-3xl mx-auto px-4 sm:px-6 py-16">
    <h1 class="text-2xl text-text mb-8">blog</h1>

    <!-- Search & Sort -->
    <div class="flex flex-col sm:flex-row sm:items-center gap-3 mb-8">
      <input
        v-model="searchInput"
        type="text"
        placeholder="search posts..."
        class="bg-bg border border-border text-text text-sm px-3 py-1.5 rounded
               focus:border-accent focus:outline-none focus:ring-1 focus:ring-accent
               placeholder:text-dim font-mono flex-1"
        @input="onSearchInput"
      />
      <button
        class="text-dim text-xs hover:text-text transition-colors whitespace-nowrap"
        @click="toggleSort"
      >
        {{ sort === 'newest' ? '↓ newest first' : '↑ oldest first' }}
      </button>
    </div>

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
import { mapState, mapActions, mapWritableState } from 'pinia'
import { useBlogStore } from '../stores/blog.js'
import BlogPostCard from '../components/BlogPostCard.vue'

export default {
  name: 'BlogPage',

  components: { BlogPostCard },

  data() {
    return {
      searchInput: '',
      searchTimeout: null,
    }
  },

  computed: {
    ...mapState(useBlogStore, ['posts', 'meta', 'loading', 'error', 'sort']),
    ...mapWritableState(useBlogStore, ['search']),
  },

  methods: {
    ...mapActions(useBlogStore, ['fetchPosts']),

    onSearchInput() {
      clearTimeout(this.searchTimeout)
      this.searchTimeout = setTimeout(() => {
        this.search = this.searchInput
        this.fetchPosts(1)
      }, 300)
    },

    async toggleSort() {
      const store = useBlogStore()
      store.sort = store.sort === 'newest' ? 'oldest' : 'newest'
      await this.fetchPosts(1)
    },

    async goToPage(page) {
      await this.fetchPosts(page)
      window.scrollTo(0, 0)
    },
  },

  mounted() {
    const store = useBlogStore()
    this.searchInput = store.search
    this.fetchPosts()
  },
}
</script>
