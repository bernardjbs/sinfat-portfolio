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

      <div class="flex items-center gap-3 mt-2">
        <span class="text-dim text-xs">{{ formattedDate }}</span>
        <span v-if="post.ai_generated" class="text-dim text-xs">· AI drafted</span>
      </div>
    </router-link>
  </div>
</template>

<script>
export default {
  name: 'BlogPostCard',

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
