<template>
  <div class="border-b border-border py-6">
    <router-link
      :to="{ name: 'blog-post', params: { slug: post.slug } }"
      class="group block"
    >
      <h2 class="text-text text-lg group-hover:text-accent transition-colors duration-150">
        {{ post.title }}
      </h2>

      <p v-if="post.excerpt" class="font-sans text-body text-sm leading-relaxed mt-1">
        {{ post.excerpt }}
      </p>

      <!-- "15 Mar 2026, 3 minute read. Part 14 of <series>." -->
      <p class="text-dim text-xs mt-2">
        {{ metaLead }}
        <template v-if="post.series">
          <Layers :size="13" class="inline align-text-bottom text-link" aria-hidden="true" />
          Part {{ post.series.part }} of {{ post.series.title }}.
        </template>
      </p>
    </router-link>
  </div>
</template>

<script>
import { Layers } from 'lucide-vue-next'

export default {
  name: 'BlogPostCard',

  components: { Layers },

  props: {
    post: {
      type: Object,
      required: true,
    },
  },

  computed: {
    metaLead() {
      const time = this.post.reading_time ? `, ${this.post.reading_time} minute read` : ''
      return `${this.formattedDate}${time}.`
    },

    formattedDate() {
      if (!this.post.published_at) return ''
      return new Date(this.post.published_at).toLocaleDateString('en-AU', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
      })
    },
  },
}
</script>
