<template>
  <div class="max-w-3xl mx-auto px-4 sm:px-6 py-16">
    <router-link :to="{ name: 'blog' }" class="text-dim text-xs hover:text-text transition-colors">← blog</router-link>

    <div v-if="seriesLoading" class="text-dim text-sm mt-5">loading...</div>

    <div v-else-if="seriesError === 'not_found'" class="text-dim text-sm mt-5">
      series not found.
      <router-link :to="{ name: 'blog-series-index' }" class="text-accent ml-2 hover:underline">
        ← all series
      </router-link>
    </div>

    <div v-else-if="seriesError" class="text-danger text-sm mt-5">{{ seriesError }}</div>

    <template v-else-if="currentSeries">
      <h1 class="flex items-center gap-3 text-2xl text-text mt-5 mb-3">
        <Layers :size="20" class="text-link shrink-0" aria-hidden="true" />
        {{ currentSeries.title }}
      </h1>
      <p v-if="currentSeries.pitch" class="font-sans text-body leading-relaxed mb-2">{{ currentSeries.pitch }}</p>
      <p class="text-dim text-xs mb-10">{{ countLabel }}</p>

      <ol>
        <li v-for="part in currentSeries.parts" :key="part.slug" class="border-b border-border">
          <router-link
            :to="{ name: 'blog-post', params: { slug: part.slug } }"
            class="group grid grid-cols-[2.5rem_1fr] gap-y-1 py-5"
          >
            <span class="text-dim text-sm pt-0.5">{{ String(part.part).padStart(2, '0') }}</span>
            <span class="text-text text-base group-hover:text-link transition-colors duration-150">{{ part.title }}</span>
            <span v-if="part.excerpt" class="col-start-2 font-sans text-body text-sm leading-relaxed">{{ part.excerpt }}</span>
            <span class="col-start-2 text-dim text-xs">{{ formatDate(part.published_at) }}</span>
          </router-link>
        </li>
      </ol>
    </template>
  </div>
</template>

<script>
import { mapState, mapActions } from 'pinia'
import { useBlogStore } from '../stores/blog.js'
import { Layers } from 'lucide-vue-next'

export default {
  name: 'BlogSeriesPage',

  components: { Layers },

  props: {
    slug: {
      type: String,
      required: true,
    },
  },

  computed: {
    ...mapState(useBlogStore, ['currentSeries', 'seriesLoading', 'seriesError']),

    countLabel() {
      const n = this.currentSeries.part_count
      const parts = `${n} ${n === 1 ? 'part' : 'parts'}`
      return this.currentSeries.status === 'complete' ? `${parts}, complete` : `Being written, ${parts} so far`
    },
  },

  methods: {
    ...mapActions(useBlogStore, ['fetchSeries']),

    formatDate(iso) {
      return new Date(iso).toLocaleDateString('en-AU', { year: 'numeric', month: 'short', day: 'numeric' })
    },
  },

  watch: {
    currentSeries(series) {
      if (series) {
        document.title = `${series.title} · sinfat.com`
        const metaDesc = document.querySelector('meta[name="description"]')
        if (metaDesc && series.pitch) {
          metaDesc.setAttribute('content', series.pitch)
        }
      }
    },
  },

  async mounted() {
    await this.fetchSeries(this.slug)
  },

  async beforeRouteUpdate(to) {
    await this.fetchSeries(to.params.slug)
  },
}
</script>
