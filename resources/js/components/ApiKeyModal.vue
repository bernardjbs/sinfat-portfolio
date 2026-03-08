<template>
    <div v-if="visible" class="fixed inset-0 z-50 flex items-center justify-center bg-black/60">
        <div class="bg-surface border border-border p-6 w-full max-w-md mx-4">
            <h2 class="text-text text-lg font-medium mb-2">Free limit reached</h2>
            <p class="text-dim text-sm mb-4">
                You've used all 3 free generations. Enter your Anthropic API key to continue.
                Your key is stored in your session only — never saved.
            </p>

            <div v-if="error" class="text-red-400 text-xs mb-3">{{ error }}</div>

            <input
                v-model="apiKey"
                type="password"
                placeholder="sk-ant-..."
                class="w-full bg-bg border border-border text-text text-sm px-3 py-2 mb-4 focus:border-accent focus:outline-none focus:ring-1 focus:ring-accent placeholder:text-dim font-mono"
                @keyup.enter="submit"
            />

            <div class="flex gap-3 justify-end">
                <button
                    class="border border-border text-text px-4 py-2 text-sm hover:border-accent hover:text-accent transition"
                    @click="close"
                >
                    Cancel
                </button>
                <button
                    class="bg-accent text-white px-4 py-2 text-sm font-medium hover:bg-green-700 transition disabled:opacity-50"
                    :disabled="!apiKey || submitting"
                    @click="submit"
                >
                    {{ submitting ? 'Verifying...' : 'Submit Key' }}
                </button>
            </div>
        </div>
    </div>
</template>

<script>
import { mapState, mapActions } from 'pinia'
import { usePlaygroundStore } from '../stores/playground.js'

export default {
    name: 'ApiKeyModal',

    props: {
        visible: {
            type: Boolean,
            default: false,
        },
    },

    data() {
        return {
            apiKey: '',
            submitting: false,
        }
    },

    computed: {
        ...mapState(usePlaygroundStore, ['error']),
    },

    methods: {
        ...mapActions(usePlaygroundStore, ['submitKey']),

        async submit() {
            if (!this.apiKey) return
            this.submitting = true
            const success = await this.submitKey(this.apiKey)
            this.submitting = false
            if (success) {
                this.apiKey = ''
            }
        },

        close() {
            this.apiKey = ''
            this.$emit('close')
        },
    },
}
</script>
