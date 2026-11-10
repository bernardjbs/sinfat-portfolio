<template>
  <div class="max-w-3xl mx-auto px-4 sm:px-6 py-16">
    <router-link :to="{ name: 'blog' }" class="text-dim text-xs hover:text-text transition-colors">← blog</router-link>

    <h1 class="text-2xl text-text mt-5 mb-3">All series</h1>
    <p class="font-sans text-body leading-relaxed mb-10">
      Longer stories told in parts. Each one is read in order from part 1.
    </p>

    <div v-if="seriesLoading" class="text-dim text-sm">loading...</div>

    <div v-else-if="seriesError" class="text-danger text-sm">{{ seriesError }}</div>

    <div v-else-if="seriesList.length === 0" class="text-dim text-sm">no series yet.</div>

    <template v-else>
      <section v-for="group in groups" :key="group.label" class="mb-12" :aria-label="group.label">
        <h2 class="text-dim text-sm mb-1">{{ group.label }}</h2>
        <SeriesRow v-for="series in group.series" :key="series.slug" :series="series" show-updated />
      </section>
    </template>
  </div>
</template>

<script>
import { mapState, mapActions } from 'pinia'
import { useBlogStore } from '../stores/blog.js'
import SeriesRow from '../components/SeriesRow.vue'

export default {
  name: 'BlogSeriesIndexPage',

  components: { SeriesRow },

  computed: {
    ...mapState(useBlogStore, ['seriesList', 'seriesLoading', 'seriesError']),

    // The API already orders each group by latest part
    groups() {
      return [
        { label: 'Being written', series: this.seriesList.filter(s => s.status === 'writing') },
        { label: 'Complete', series: this.seriesList.filter(s => s.status === 'complete') },
      ].filter(group => group.series.length > 0)
    },
  },

  methods: {
    ...mapActions(useBlogStore, ['fetchSeriesList']),
  },

  mounted() {
    this.fetchSeriesList()
  },
}
</script>
