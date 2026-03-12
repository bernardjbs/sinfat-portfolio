<template>
  <div class="flex flex-col h-screen">

    <!-- Top bar -->
    <div class="flex items-center gap-4 px-4 sm:px-6 py-3 border-b border-border bg-surface shrink-0">

      <router-link
        :to="{ name: 'admin-blog' }"
        class="text-dim text-xs hover:text-text transition-colors shrink-0"
      >
        ← posts
      </router-link>

      <!-- Title input -->
      <input
        v-model="title"
        type="text"
        placeholder="post title"
        class="flex-1 bg-transparent text-text text-sm placeholder:text-dim
               border-none outline-none focus:outline-none"
      />

      <!-- Status badge -->
      <span
        v-if="isEditing"
        class="shrink-0 text-xs px-2 py-0.5 rounded border"
        :class="currentStatus === 'published'
          ? 'bg-green-900/30 text-accent border-green-800'
          : 'bg-surface text-dim border-border'"
      >
        {{ currentStatus }}
      </span>

      <!-- Save -->
      <button
        class="shrink-0 border border-border text-text px-3 py-1.5 text-xs
               hover:border-accent hover:text-accent transition-colors"
        :disabled="saving"
        @click="save(currentStatus)"
      >
        {{ saving === currentStatus ? 'saving...' : 'save' }}
      </button>

      <!-- Publish / Unpublish -->
      <button
        v-if="currentStatus !== 'published'"
        class="shrink-0 bg-accent text-white px-3 py-1.5 text-xs
               font-medium hover:bg-green-700 transition-colors"
        :disabled="saving"
        @click="save('published')"
      >
        {{ saving === 'published' ? 'publishing...' : 'publish' }}
      </button>

      <button
        v-else
        class="shrink-0 border border-border text-dim px-3 py-1.5 text-xs
               hover:border-accent hover:text-accent transition-colors"
        :disabled="toggling"
        @click="handleTogglePublish"
      >
        {{ toggling ? 'unpublishing...' : 'unpublish' }}
      </button>

    </div>

    <!-- AI Generate Panel -->
    <div class="border-b border-border bg-surface shrink-0">
      <button
        class="w-full flex items-center gap-2 px-4 sm:px-6 py-2 text-xs text-dim
               hover:text-text transition-colors"
        @click="aiPanelOpen = !aiPanelOpen"
      >
        <span>{{ aiPanelOpen ? '▾' : '▸' }}</span>
        <span>ai generate</span>
        <span v-if="aiStreaming" class="text-accent">· streaming...</span>
      </button>

      <div v-if="aiPanelOpen" class="px-4 sm:px-6 pb-3 space-y-2">
        <div class="flex gap-2">
          <input
            v-model="aiTopic"
            type="text"
            placeholder="topic — e.g. 'SSE streaming in Laravel with Neuron AI'"
            class="flex-1 bg-bg border border-border text-text text-xs px-3 py-1.5
                   placeholder:text-dim outline-none focus:border-accent transition-colors"
            :disabled="aiStreaming"
            @keydown.enter="startAiGenerate"
          />
          <button
            class="shrink-0 text-xs px-3 py-1.5 transition-colors"
            :class="aiStreaming
              ? 'border border-red-800 text-red-400 hover:border-red-600'
              : 'bg-accent text-white hover:bg-green-700'"
            :disabled="!aiTopic.trim() && !aiStreaming"
            @click="aiStreaming ? stopAiGenerate() : startAiGenerate()"
          >
            {{ aiStreaming ? 'stop' : 'generate' }}
          </button>
        </div>
        <input
          v-model="aiContext"
          type="text"
          placeholder="additional context (optional)"
          class="w-full bg-bg border border-border text-text text-xs px-3 py-1.5
                 placeholder:text-dim outline-none focus:border-accent transition-colors"
          :disabled="aiStreaming"
        />
        <p v-if="aiError" class="text-danger text-xs">{{ aiError }}</p>
      </div>
    </div>

    <!-- Error banner -->
    <div v-if="error" class="px-4 py-2 bg-danger/10 text-danger text-xs border-b border-danger/30">
      {{ error }}
    </div>

    <!-- Loading state -->
    <div v-if="loading" class="flex-1 flex items-center justify-center">
      <span class="text-dim text-sm">loading...</span>
    </div>

    <!-- Editor body — split pane -->
    <div v-else class="flex-1 flex min-h-0">

      <!-- Left: markdown textarea -->
      <div class="flex-1 flex flex-col border-r border-border min-w-0">
        <div class="px-4 py-1.5 border-b border-border">
          <span class="text-dim text-xs">markdown</span>
        </div>
        <textarea
          v-model="content"
          class="flex-1 bg-bg text-text text-sm font-mono p-4
                 resize-none outline-none focus:outline-none
                 placeholder:text-dim"
          placeholder="write markdown here..."
          spellcheck="false"
          @keydown.ctrl.s.prevent="save(currentStatus)"
          @keydown.meta.s.prevent="save(currentStatus)"
        />
      </div>

      <!-- Right: rendered preview -->
      <div class="flex-1 flex flex-col min-w-0">
        <div class="px-4 py-1.5 border-b border-border">
          <span class="text-dim text-xs">preview</span>
        </div>
        <div
          class="flex-1 overflow-y-auto p-4
                 prose prose-invert prose-sm max-w-none
                 prose-headings:font-mono prose-headings:text-text
                 prose-p:text-dim prose-a:text-accent prose-a:no-underline
                 hover:prose-a:underline
                 prose-code:bg-surface prose-code:text-accent prose-code:rounded prose-code:px-1
                 prose-pre:bg-surface prose-pre:border prose-pre:border-border
                 prose-strong:text-text prose-hr:border-border"
          v-html="renderedContent"
        />
      </div>

    </div>

    <!-- Bottom metadata bar -->
    <div class="flex items-center gap-6 px-4 sm:px-6 py-2 border-t border-border bg-surface shrink-0">
      <input
        v-model="excerpt"
        type="text"
        placeholder="excerpt (optional)"
        class="flex-1 bg-transparent text-dim text-xs placeholder:text-dim
               border-none outline-none focus:outline-none"
      />
      <input
        v-model="category"
        type="text"
        placeholder="category"
        class="shrink-0 w-32 bg-transparent text-dim text-xs placeholder:text-dim
               border-none outline-none focus:outline-none text-right"
      />
      <div v-if="isEditing && adminCurrentPost" class="flex items-center gap-4 shrink-0">
        <span class="text-dim text-xs">
          created {{ formattedDate(adminCurrentPost.created_at) }}
        </span>
        <span v-if="adminCurrentPost.ai_model" class="text-dim text-xs">
          · {{ adminCurrentPost.ai_model }}
        </span>
      </div>
    </div>

  </div>
</template>

<script>
import { marked } from 'marked'
import { mapState, mapActions } from 'pinia'
import { useBlogStore } from '../../stores/blog.js'

export default {
  name: 'AdminBlogEditor',

  props: {
    id: {
      type: [String, Number],
      default: null,
    },
  },

  data() {
    return {
      title: '',
      content: '',
      excerpt: '',
      category: 'development',
      currentStatus: 'draft',
      saving: null,
      toggling: false,
      error: null,
      // AI generate
      aiPanelOpen: false,
      aiTopic: '',
      aiContext: '',
      aiStreaming: false,
      aiError: null,
      aiAbortController: null,
    }
  },

  computed: {
    ...mapState(useBlogStore, ['adminCurrentPost', 'loading']),

    isEditing() {
      return !!this.id
    },

    renderedContent() {
      if (!this.content) return '<p class="text-dim">nothing to preview yet.</p>'
      return marked(this.content)
    },
  },

  methods: {
    ...mapActions(useBlogStore, ['fetchAdminPost', 'createPost', 'updatePost', 'togglePublish']),

    formattedDate(iso) {
      if (!iso) return ''
      return new Date(iso).toLocaleDateString('en-AU', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
      })
    },

    async save(status) {
      if (!this.title.trim()) {
        this.error = 'Title is required.'
        return
      }

      this.error = null
      this.saving = status

      const payload = {
        title:    this.title.trim(),
        content:  this.content,
        excerpt:  this.excerpt.trim() || null,
        category: this.category.trim() || null,
        status,
      }

      try {
        if (this.isEditing) {
          await this.updatePost(this.id, payload)
        } else {
          const created = await this.createPost(payload)
          this.$router.replace({ name: 'admin-blog-edit', params: { id: created.id } })
        }
        this.currentStatus = status
      } catch (e) {
        this.error = e?.message ?? 'Save failed.'
      } finally {
        this.saving = null
      }
    },

    async handleTogglePublish() {
      this.toggling = true
      try {
        const updated = await this.togglePublish(this.id)
        this.currentStatus = updated.status
      } catch (e) {
        this.error = e?.message ?? 'Failed to change publish status.'
      } finally {
        this.toggling = false
      }
    },

    populateFromPost(post) {
      this.title         = post.title ?? ''
      this.content       = post.raw_content ?? ''
      this.excerpt       = post.excerpt ?? ''
      this.category      = post.category ?? 'development'
      this.currentStatus = post.status ?? 'draft'
    },

    // AI generate methods

    async startAiGenerate() {
      if (!this.aiTopic.trim() || this.aiStreaming) return

      this.aiStreaming = true
      this.aiError = null
      this.aiAbortController = new AbortController()

      // Append to existing content or start fresh
      if (this.content.trim()) {
        this.content += '\n\n'
      }

      try {
        // Get CSRF cookie first
        await fetch('/sanctum/csrf-cookie', { credentials: 'same-origin' })

        const csrfToken = decodeURIComponent(
          document.cookie.match(/XSRF-TOKEN=([^;]+)/)?.[1] ?? ''
        )

        const response = await fetch('/api/admin/ai/generate', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'Accept': 'text/event-stream',
            'X-XSRF-TOKEN': csrfToken,
          },
          credentials: 'same-origin',
          signal: this.aiAbortController.signal,
          body: JSON.stringify({
            topic: this.aiTopic.trim(),
            context: this.aiContext.trim() || null,
          }),
        })

        if (!response.ok) {
          const err = await response.json().catch(() => ({}))
          throw new Error(err.message || `Request failed (${response.status})`)
        }

        const reader = response.body.getReader()
        const decoder = new TextDecoder()
        let buffer = ''

        while (true) {
          const { done, value } = await reader.read()
          if (done) break

          buffer += decoder.decode(value, { stream: true })

          const lines = buffer.split('\n')
          // Keep the last incomplete line in the buffer
          buffer = lines.pop() || ''

          for (const line of lines) {
            if (!line.startsWith('data: ')) continue

            const data = line.slice(6)
            if (data === '[DONE]') {
              this.aiStreaming = false
              return
            }

            try {
              const parsed = JSON.parse(data)
              if (parsed.text) {
                this.content += parsed.text
              }
              if (parsed.error) {
                this.aiError = parsed.error
                this.aiStreaming = false
                return
              }
            } catch {
              // ignore parse errors
            }
          }
        }

        this.aiStreaming = false

      } catch (e) {
        if (e.name === 'AbortError') {
          // User cancelled — not an error
        } else {
          this.aiError = e.message || 'AI generation failed'
        }
        this.aiStreaming = false
      }
    },

    stopAiGenerate() {
      if (this.aiAbortController) {
        this.aiAbortController.abort()
        this.aiAbortController = null
      }
      this.aiStreaming = false
    },
  },

  async mounted() {
    if (this.isEditing) {
      await this.fetchAdminPost(this.id)
      if (this.adminCurrentPost) {
        this.populateFromPost(this.adminCurrentPost)
      }
    }
  },
}
</script>
