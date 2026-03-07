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

      <!-- Save draft -->
      <button
        class="shrink-0 border border-border text-text px-3 py-1.5 text-xs
               hover:border-accent hover:text-accent transition-colors"
        :disabled="saving"
        @click="save('draft')"
      >
        {{ saving === 'draft' ? 'saving...' : 'save draft' }}
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

    <!-- Error banner -->
    <div v-if="error" class="px-4 py-2 bg-danger/10 text-danger text-xs border-b border-danger/30">
      {{ error }}
    </div>

    <!-- Loading state (edit mode) -->
    <div v-if="loading" class="flex-1 flex items-center justify-center">
      <span class="text-dim text-sm">loading...</span>
    </div>

    <!-- Editor body -->
    <div v-else class="flex-1 flex flex-col min-h-0">
      <MdEditor
        v-model="content"
        theme="dark"
        class="flex-1"
        @save="save('draft')"
      />

      <!-- Bottom metadata bar -->
      <div class="flex items-center gap-6 px-4 sm:px-6 py-2 border-t border-border bg-surface shrink-0">

        <input
          v-model="excerpt"
          type="text"
          placeholder="excerpt (optional)"
          class="flex-1 bg-transparent text-dim text-xs placeholder:text-dim
                 border-none outline-none focus:outline-none"
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

  </div>
</template>

<script>
import { MdEditor } from 'md-editor-v3'
import 'md-editor-v3/lib/style.css'
import { mapState, mapActions } from 'pinia'
import { useBlogStore } from '../../stores/blog.js'

export default {
  name: 'AdminBlogEditor',

  components: { MdEditor },

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
      currentStatus: 'draft',
      saving: null,   // 'draft' | 'published' | null
      toggling: false,
      error: null,
    }
  },

  computed: {
    ...mapState(useBlogStore, ['adminCurrentPost', 'loading']),

    isEditing() {
      return !!this.id
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
        title:   this.title.trim(),
        content: this.content,
        excerpt: this.excerpt.trim() || null,
        status,
      }

      try {
        if (this.isEditing) {
          await this.updatePost(this.id, payload)
        } else {
          const created = await this.createPost(payload)
          // Redirect to edit view so subsequent saves use PUT
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
      this.currentStatus = post.status ?? 'draft'
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
