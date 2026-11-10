<template>
  <router-link
    :to="{ name: 'blog-series', params: { slug: series.slug } }"
    class="group grid grid-cols-1 sm:grid-cols-[1fr_auto] gap-x-6 gap-y-1 py-5 border-b border-border"
  >
    <span class="flex items-center gap-2 text-text text-base group-hover:text-link transition-colors duration-150">
      <Layers :size="15" class="text-link shrink-0" aria-hidden="true" />
      {{ series.title }}
    </span>
    <span class="text-dim text-xs sm:row-start-1 sm:col-start-2 sm:self-center sm:text-right">{{ countLabel }}</span>
    <span v-if="series.pitch" class="font-sans text-body text-sm leading-relaxed">{{ series.pitch }}</span>
  </router-link>
</template>

<script>
import { Layers } from 'lucide-vue-next'

export default {
  name: 'SeriesRow',

  components: { Layers },

  props: {
    series: {
      type: Object,
      required: true,
    },
    // On /blog/series the group heading carries the status, so the row shows the last update instead
    showUpdated: {
      type: Boolean,
      default: false,
    },
  },

  computed: {
    parts() {
      return `${this.series.part_count} ${this.series.part_count === 1 ? 'part' : 'parts'}`
    },

    countLabel() {
      if (this.showUpdated) {
        const last = new Date(this.series.last_updated).toLocaleDateString('en-AU', {
          year: 'numeric',
          month: 'short',
          day: 'numeric',
        })
        return `${this.parts}, last ${last}`
      }
      return this.series.status === 'complete'
        ? `${this.parts}, complete`
        : `Being written, ${this.parts} so far`
    },
  },
}
</script>
