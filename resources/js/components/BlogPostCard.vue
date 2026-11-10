<template>
  <div class="border-b border-border py-6">
    <router-link
      :to="{ name: 'blog-post', params: { slug: post.slug } }"
      class="group block"
    >
      <h2 class="text-text text-lg group-hover:text-accent transition-colors duration-150">
        {{ post.title }}
      </h2>

      <p v-if="post.excerpt" class="text-dim text-sm mt-1">
        {{ post.excerpt }}
      </p>

      <div class="flex flex-wrap items-center gap-x-3 gap-y-1 mt-2">
        <span class="text-dim text-xs">{{ formattedDate }}</span>
        <span v-if="post.reading_time" class="text-dim text-xs">· {{ post.reading_time }} min read</span>
        <span
          v-if="post.category"
          class="text-xs text-dim border border-border rounded px-1.5 py-0.5"
        >{{ post.category }}</span>
        <span v-if="post.ai_generated" class="text-dim text-xs">· AI drafted</span>
        <span v-if="post.series" class="inline-flex items-center gap-1 text-dim text-xs">
          <Layers :size="13" class="text-link shrink-0" aria-hidden="true" />
          Part {{ post.series.part }} of {{ post.series.title }}
        </span>
      </div>
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
