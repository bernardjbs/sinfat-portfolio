<template>
  <div class="max-w-3xl mx-auto px-4 sm:px-6 py-16">

    <div v-if="loading" class="text-dim text-sm">loading...</div>

    <div v-else-if="error === 'not_found'" class="text-dim text-sm">
      post not found.
      <router-link :to="{ name: 'blog' }" class="text-accent ml-2 hover:underline">
        ← back to blog
      </router-link>
    </div>

    <div v-else-if="error" class="text-danger text-sm">{{ error }}</div>

    <article v-else-if="currentPost">
      <!-- Header -->
      <header class="mb-10">
        <router-link
          :to="{ name: 'blog' }"
          class="text-dim text-xs hover:text-text transition-colors"
        >
          ← blog
        </router-link>

        <h1 class="text-2xl text-text mt-4">{{ currentPost.title }}</h1>

        <div class="flex items-center gap-3 mt-3">
          <span class="text-dim text-xs">{{ formattedDate }}</span>
          <span v-if="currentPost.ai_generated" class="text-dim text-xs">· AI drafted</span>
        </div>
      </header>

      <!-- Content — server-rendered markdown from Laravel, safe to use v-html -->
      <div
        class="prose prose-invert prose-sm max-w-none
               prose-headings:font-mono prose-headings:text-text
               prose-p:text-dim prose-a:text-accent prose-a:no-underline
               hover:prose-a:underline
               prose-code:bg-surface prose-code:text-accent prose-code:rounded prose-code:px-1
               prose-pre:bg-surface prose-pre:border prose-pre:border-border
               prose-strong:text-text prose-hr:border-border"
        v-html="currentPost.content"
      />
    </article>

  </div>
</template>

<script>
import { mapState, mapActions } from 'pinia'
import { useBlogStore } from '../stores/blog.js'

export default {
  name: 'BlogPostPage',

  props: {
    slug: {
      type: String,
      required: true,
    },
  },

  computed: {
    ...mapState(useBlogStore, ['currentPost', 'loading', 'error']),

    formattedDate() {
      if (!this.currentPost?.published_at) return ''
      return new Date(this.currentPost.published_at).toLocaleDateString('en-AU', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
      })
    },
  },

  watch: {
    currentPost(post) {
      if (post) {
        document.title = `${post.title} — sinfat.com`
        const metaDesc = document.querySelector('meta[name="description"]')
        if (metaDesc && post.excerpt) {
          metaDesc.setAttribute('content', post.excerpt)
        }
        const ogTitle = document.querySelector('meta[property="og:title"]')
        if (ogTitle) {
          ogTitle.setAttribute('content', `${post.title} — sinfat.com`)
        }
        const ogDesc = document.querySelector('meta[property="og:description"]')
        if (ogDesc && post.excerpt) {
          ogDesc.setAttribute('content', post.excerpt)
        }
      }
    },
  },

  methods: {
    ...mapActions(useBlogStore, ['fetchPost']),
  },

  async mounted() {
    await this.fetchPost(this.slug)
  },

  async beforeRouteUpdate(to) {
    await this.fetchPost(to.params.slug)
  },
}
</script>
