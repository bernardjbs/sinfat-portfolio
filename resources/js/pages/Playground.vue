<template>
    <div class="max-w-3xl mx-auto px-4 sm:px-6 py-12">
        <!-- Header -->
        <div class="flex items-center justify-between mb-8">
            <h1 class="text-text text-2xl font-semibold">AI Blog Playground</h1>
            <span class="bg-surface border border-border text-dim text-xs px-2 py-0.5 rounded">
                <template v-if="hasOwnKey">using your key</template>
                <template v-else>{{ remaining }} free {{ remaining === 1 ? 'generation' : 'generations' }} left</template>
            </span>
        </div>

        <!-- Input -->
        <div class="mb-6">
            <label class="text-dim text-sm mb-2 block">What would you like to write about?</label>
            <div class="flex gap-3">
                <input
                    v-model="topic"
                    type="text"
                    placeholder="e.g. Building SSE streaming with Laravel and Vue 3"
                    class="flex-1 bg-bg border border-border text-text text-sm px-3 py-2 focus:border-accent focus:outline-none focus:ring-1 focus:ring-accent placeholder:text-dim font-mono"
                    :disabled="streaming"
                    @keyup.enter="generate"
                />
                <button
                    class="bg-accent text-white px-4 py-2 text-sm font-medium hover:bg-green-700 transition disabled:opacity-50"
                    :disabled="!canGenerate"
                    @click="generate"
                >
                    {{ streaming ? 'Generating...' : 'Generate' }}
                </button>
            </div>
        </div>

        <!-- Error -->
        <div v-if="error && !showKeyModal" class="text-red-400 text-sm mb-4">
            {{ error }}
        </div>

        <!-- Output -->
        <div v-if="content || streaming" class="mb-4">
            <div class="bg-bg border border-border p-4 font-mono text-sm text-text min-h-[200px] max-h-[500px] overflow-y-auto whitespace-pre-wrap">{{ content }}<span v-if="streaming" class="animate-pulse">▌</span></div>

            <!-- Actions -->
            <div class="flex gap-3 mt-3">
                <button
                    class="border border-border text-text px-3 py-1.5 text-xs hover:border-accent hover:text-accent transition"
                    :disabled="streaming || !content"
                    @click="copyContent"
                >
                    {{ copied ? 'Copied!' : 'Copy' }}
                </button>
                <button
                    class="border border-border text-text px-3 py-1.5 text-xs hover:border-accent hover:text-accent transition"
                    :disabled="streaming"
                    @click="clearContent"
                >
                    Clear
                </button>
            </div>
        </div>

        <!-- Empty state -->
        <div v-else class="bg-surface border border-border p-8 text-center">
            <p class="text-dim text-sm">Enter a topic above and click Generate to create AI-written content.</p>
        </div>

        <!-- API Key Modal -->
        <ApiKeyModal
            :visible="showKeyModal"
            @close="showKeyModal = false"
        />
    </div>
</template>

<script>
import { mapState, mapActions, mapWritableState } from 'pinia'
import { usePlaygroundStore } from '../stores/playground.js'
import ApiKeyModal from '../components/ApiKeyModal.vue'

export default {
    name: 'Playground',

    components: {
        ApiKeyModal,
    },

    data() {
        return {
            copied: false,
        }
    },

    computed: {
        ...mapState(usePlaygroundStore, ['content', 'streaming', 'error', 'remaining', 'hasOwnKey', 'canGenerate']),
        ...mapWritableState(usePlaygroundStore, ['topic', 'showKeyModal']),
    },

    methods: {
        ...mapActions(usePlaygroundStore, ['generate', 'clearContent']),

        async copyContent() {
            const store = usePlaygroundStore()
            store.copyContent()
            this.copied = true
            setTimeout(() => { this.copied = false }, 2000)
        },
    },
}
</script>
