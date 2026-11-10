<template>
  <div class="post-page">

    <div v-if="loading" class="text-dim text-sm">loading...</div>

    <div v-else-if="error === 'not_found'" class="text-dim text-sm">
      post not found.
      <router-link :to="{ name: 'blog' }" class="text-accent ml-2 hover:underline">
        ← back to blog
      </router-link>
    </div>

    <div v-else-if="error" class="text-danger text-sm">{{ error }}</div>

    <div v-else-if="currentPost" class="post-grid">
      <!-- Left track: series part, then contents built from the post's h2s -->
      <aside class="post-track">
        <div v-if="series" class="post-series">
          <span class="post-part">
            <Layers :size="15" class="text-link shrink-0" aria-hidden="true" />
            {{ partLabel }}
          </span>
          <router-link :to="{ name: 'blog-series', params: { slug: series.slug } }">{{ series.title }}</router-link>
        </div>
        <nav v-if="contents.length" class="post-toc" aria-label="Contents">
          <span>Contents</span>
          <a
            v-for="item in contents"
            :key="item.id"
            :href="`#${item.id}`"
            @click.prevent="goToHeading(item.id)"
          >{{ item.text }}</a>
        </nav>
      </aside>

      <header class="post-head">
        <router-link :to="{ name: 'blog' }" class="post-back">← blog</router-link>

        <h1 class="post-title">{{ currentPost.title }}</h1>

        <p class="post-meta">
          {{ metaLead }}
          <template v-if="currentPost.category">
            {{ metaCategoryPrefix }}
            <router-link :to="{ name: 'blog' }">{{ currentPost.category }}</router-link>.
          </template>
          <template v-else-if="currentPost.ai_generated">Drafted with AI.</template>
        </p>
      </header>

      <article class="post-body">
        <!-- Content — server-rendered markdown from Laravel, safe to use v-html -->
        <div
          ref="content"
          class="prose prose-invert max-w-none post-prose
                 prose-headings:font-mono prose-headings:text-text
                 prose-p:font-sans prose-p:text-body
                 prose-li:font-sans prose-li:text-body
                 prose-blockquote:font-sans prose-blockquote:text-body
                 prose-a:text-link prose-a:no-underline
                 hover:prose-a:underline
                 prose-code:font-mono prose-code:bg-surface prose-code:text-link prose-code:rounded prose-code:px-1
                 prose-code:before:content-none prose-code:after:content-none
                 prose-pre:bg-surface prose-pre:border prose-pre:border-border
                 prose-strong:text-text prose-hr:border-border
                 prose-th:text-text prose-th:border-border prose-th:px-3 prose-th:py-1.5
                 prose-td:text-body prose-td:border-border prose-td:px-3 prose-td:py-1.5
                 prose-table:border-collapse"
          v-html="currentPost.content"
        />

        <!-- Series posts step through the series; standalone posts by date -->
        <section v-if="series" class="post-series-box" aria-label="Series">
          <p class="post-series-head">{{ series.title }}, {{ partLabel.toLowerCase() }}</p>

          <div class="post-series-pn">
            <router-link
              v-if="previousPost"
              :to="{ name: 'blog-post', params: { slug: previousPost.slug } }"
            >
              <small>Previous, part {{ previousPost.part }}</small>
              <span>{{ previousPost.title }}</span>
            </router-link>
            <div v-else></div>

            <router-link
              v-if="nextPost"
              :to="{ name: 'blog-post', params: { slug: nextPost.slug } }"
              class="post-series-next"
            >
              <small>Next, part {{ nextPost.part }}</small>
              <span>{{ nextPost.title }}</span>
            </router-link>
          </div>

          <details v-if="seriesParts">
            <summary>{{ partsSummary }}</summary>
            <ol :start="partsWindow[0]?.part">
              <li
                v-for="part in partsWindow"
                :key="part.slug"
                :class="{ current: part.slug === currentPost.slug }"
              >
                <router-link :to="{ name: 'blog-post', params: { slug: part.slug } }">{{ part.title }}</router-link>
              </li>
            </ol>
            <router-link
              v-if="partsWindowed"
              :to="{ name: 'blog-series', params: { slug: series.slug } }"
              class="post-series-all"
            >All {{ seriesParts.length }} parts</router-link>
          </details>
        </section>

        <!-- Next/Previous navigation -->
        <nav v-else-if="previousPost || nextPost" class="flex items-stretch justify-between gap-4 mt-12 pt-6 border-t border-border">
          <router-link
            v-if="previousPost"
            :to="{ name: 'blog-post', params: { slug: previousPost.slug } }"
            class="group flex-1 min-w-0"
          >
            <span class="text-dim text-xs">← previous</span>
            <span class="block text-sm text-text group-hover:text-accent transition-colors truncate mt-0.5">
              {{ previousPost.title }}
            </span>
          </router-link>
          <div v-else class="flex-1"></div>

          <router-link
            v-if="nextPost"
            :to="{ name: 'blog-post', params: { slug: nextPost.slug } }"
            class="group flex-1 min-w-0 text-right"
          >
            <span class="text-dim text-xs">next →</span>
            <span class="block text-sm text-text group-hover:text-accent transition-colors truncate mt-0.5">
              {{ nextPost.title }}
            </span>
          </router-link>
          <div v-else class="flex-1"></div>
        </nav>

        <!-- Post footer -->
        <footer class="flex items-center justify-between mt-6 pt-6 border-t border-border">
          <button
            class="text-dim text-xs hover:text-accent transition-colors"
            @click="copyLink"
          >
            {{ linkCopied ? 'copied!' : 'copy link' }}
          </button>

          <a
            href="#"
            class="text-dim text-xs hover:text-accent transition-colors"
            @click.prevent="scrollToTop"
          >
            ↑ top
          </a>
        </footer>
      </article>
    </div>

  </div>
</template>

<script>
import { mapState, mapActions } from 'pinia'
import { useBlogStore } from '../stores/blog.js'
import hljs from 'highlight.js/lib/core'
import javascript from 'highlight.js/lib/languages/javascript'
import php from 'highlight.js/lib/languages/php'
import bash from 'highlight.js/lib/languages/bash'
import json from 'highlight.js/lib/languages/json'
import css from 'highlight.js/lib/languages/css'
import xml from 'highlight.js/lib/languages/xml'
import sql from 'highlight.js/lib/languages/sql'
import 'highlight.js/styles/github-dark.css'
import { Layers } from 'lucide-vue-next'

// Past this many parts, the end-of-post list shows only the parts either side of this one
const FULL_LIST_MAX = 15
const WINDOW_EACH_SIDE = 2

hljs.registerLanguage('javascript', javascript)
hljs.registerLanguage('js', javascript)
hljs.registerLanguage('php', php)
hljs.registerLanguage('bash', bash)
hljs.registerLanguage('sh', bash)
hljs.registerLanguage('json', json)
hljs.registerLanguage('css', css)
hljs.registerLanguage('html', xml)
hljs.registerLanguage('xml', xml)
hljs.registerLanguage('sql', sql)

export default {
  name: 'BlogPostPage',

  components: { Layers },

  props: {
    slug: {
      type: String,
      required: true,
    },
  },

  data() {
    return {
      linkCopied: false,
      contents: [],
    }
  },

  computed: {
    ...mapState(useBlogStore, ['currentPost', 'nextPost', 'previousPost', 'seriesParts', 'loading', 'error']),

    series() {
      return this.currentPost?.series ?? null
    },

    // "Part 12 of 14" once complete; "Part 3" while being written, which has no total yet
    partLabel() {
      const { part, total, status } = this.series
      return status === 'complete' ? `Part ${part} of ${total}` : `Part ${part}`
    },

    partsWindowed() {
      return (this.seriesParts?.length ?? 0) > FULL_LIST_MAX
    },

    partsWindow() {
      if (!this.partsWindowed) return this.seriesParts ?? []
      const i = this.seriesParts.findIndex(p => p.slug === this.currentPost.slug)
      return this.seriesParts.slice(Math.max(0, i - WINDOW_EACH_SIDE), i + WINDOW_EACH_SIDE + 1)
    },

    partsSummary() {
      if (!this.partsWindowed) return `All ${this.seriesParts.length} parts`
      const first = this.partsWindow[0].part
      const last = this.partsWindow[this.partsWindow.length - 1].part
      return `Parts ${first} to ${last}`
    },

    formattedDate() {
      if (!this.currentPost?.published_at) return ''
      return new Date(this.currentPost.published_at).toLocaleDateString('en-AU', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
      })
    },

    // "13 March 2026, 5 minute read."
    metaLead() {
      const time = this.currentPost?.reading_time ? `, ${this.currentPost.reading_time} minute read` : ''
      return `${this.formattedDate}${time}.`
    },

    metaCategoryPrefix() {
      return this.currentPost?.ai_generated ? 'Drafted with AI, in' : 'In'
    },
  },

  methods: {
    ...mapActions(useBlogStore, ['fetchPost']),

    highlightCode() {
      this.$nextTick(() => {
        const blocks = this.$el.querySelectorAll('pre code')
        blocks.forEach((block) => {
          hljs.highlightElement(block)
        })
      })
    },

    // Give each h2 the id of its permalink anchor, then list them in the track
    buildContents() {
      this.$nextTick(() => {
        const root = this.$refs.content
        if (!root) {
          this.contents = []
          return
        }
        this.contents = [...root.querySelectorAll('h2')].map((h2, i) => {
          const anchor = h2.querySelector('a.heading-permalink[id]')
          if (!h2.id) {
            h2.id = anchor ? anchor.id : `section-${i + 1}`
          }
          if (anchor) anchor.removeAttribute('id')
          return { id: h2.id, text: h2.textContent.trim() }
        })
      })
    },

    goToHeading(id) {
      const heading = this.$refs.content?.querySelector(`#${CSS.escape(id)}`)
      if (!heading) return
      heading.scrollIntoView({ behavior: 'smooth', block: 'start' })
      history.replaceState(history.state, '', `#${id}`)
    },

    copyLink() {
      navigator.clipboard.writeText(window.location.href)
      this.linkCopied = true
      setTimeout(() => { this.linkCopied = false }, 2000)
    },

    scrollToTop() {
      window.scrollTo({ top: 0, behavior: 'smooth' })
    },

    scrollToHash() {
      const hash = window.location.hash?.slice(1)
      if (!hash) return
      this.$nextTick(() => {
        const el = document.getElementById(hash)
        if (el) el.scrollIntoView({ behavior: 'smooth' })
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
        this.highlightCode()
        this.buildContents()
        this.scrollToHash()
      }
    },
  },

  async mounted() {
    await this.fetchPost(this.slug)
  },

  async beforeRouteUpdate(to) {
    await this.fetchPost(to.params.slug)
  },
}
</script>
