<template>
  <div class="max-w-3xl mx-auto px-4 sm:px-6 py-16">
    <h1 class="text-2xl text-text mb-3">blog</h1>
    <p class="font-sans text-body leading-relaxed mb-10">
      Notes from building software with AI coding agents: what worked, what broke, and where the AI got it wrong.
    </p>

    <!-- Series: at most three, in the API's order -->
    <section v-if="showSeries" class="mb-12" aria-labelledby="series-heading">
      <h2 id="series-heading" class="flex items-center gap-2 text-dim text-sm mb-1">
        <Layers :size="15" class="text-link" aria-hidden="true" />
        Series
      </h2>
      <SeriesRow v-for="series in featuredSeries" :key="series.slug" :series="series" />
      <router-link
        v-if="seriesList.length > featuredSeries.length"
        :to="{ name: 'blog-series-index' }"
        class="inline-block mt-4 text-link text-sm hover:underline"
      >All series ({{ seriesList.length }})</router-link>
    </section>

    <h2 class="text-dim text-sm mb-3">All posts</h2>

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
import SeriesRow from '../components/SeriesRow.vue'
import { Layers } from 'lucide-vue-next'

export default {
  name: 'BlogPage',

  components: { BlogPostCard, SeriesRow, Layers },

  data() {
    return {
      searchInput: '',
      searchTimeout: null,
    }
  },

  computed: {
    ...mapState(useBlogStore, ['posts', 'meta', 'loading', 'error', 'sort', 'seriesList']),
    ...mapWritableState(useBlogStore, ['search']),

    // Searching or paging shows posts only
    showSeries() {
      return this.seriesList.length > 0 && !this.search && (this.meta?.current_page ?? 1) === 1
    },

    featuredSeries() {
      return this.seriesList.slice(0, 3)
    },
  },

  methods: {
    ...mapActions(useBlogStore, ['fetchPosts', 'fetchSeriesList']),

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
    this.fetchSeriesList()
  },
}
</script>
