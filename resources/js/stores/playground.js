import { defineStore } from 'pinia'

export const usePlaygroundStore = defineStore('playground', {
    state: () => ({
        topic: '',
        content: '',
        streaming: false,
        loading: false,
        error: null,
        remaining: 3,
        limitReached: false,
        hasOwnKey: false,
        showKeyModal: false,
    }),

    getters: {
        canGenerate: (state) => {
            return !state.streaming && !state.loading && state.topic.trim().length > 0
        },
    },

    actions: {
        async generate() {
            if (!this.canGenerate) return

            this.content = ''
            this.error = null
            this.streaming = true

            try {
                const res = await fetch('/api/playground/generate', {
                    method: 'POST',
                    credentials: 'same-origin',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'text/event-stream',
                    },
                    body: JSON.stringify({ topic: this.topic }),
                })

                if (res.status === 429) {
                    const data = await res.json()
                    this.remaining = 0
                    this.limitReached = true
                    this.showKeyModal = true
                    this.error = data.message
                    this.streaming = false
                    return
                }

                if (!res.ok) {
                    const data = await res.json()
                    this.error = data.message || 'Generation failed'
                    this.streaming = false
                    return
                }

                // Update remaining from header
                const remainingHeader = res.headers.get('X-RateLimit-Remaining')
                if (remainingHeader !== null) {
                    this.remaining = parseInt(remainingHeader, 10)
                }

                // Read SSE stream
                const reader = res.body.getReader()
                const decoder = new TextDecoder()
                let buffer = ''

                while (true) {
                    const { done, value } = await reader.read()
                    if (done) break

                    buffer += decoder.decode(value, { stream: true })
                    const lines = buffer.split('\n')
                    buffer = lines.pop() // keep incomplete line in buffer

                    for (const line of lines) {
                        if (!line.startsWith('data: ')) continue
                        const payload = line.slice(6)

                        if (payload === '[DONE]') {
                            this.streaming = false
                            return
                        }

                        try {
                            const chunk = JSON.parse(payload)
                            if (chunk.text) {
                                this.content += chunk.text
                            }
                            if (chunk.error) {
                                this.error = chunk.error
                                this.streaming = false
                                return
                            }
                        } catch {
                            // ignore parse errors
                        }
                    }
                }

                this.streaming = false

            } catch (e) {
                this.error = e.message || 'Network error'
                this.streaming = false
            }
        },

        async submitKey(apiKey) {
            this.loading = true
            this.error = null

            try {
                const res = await fetch('/api/playground/key', {
                    method: 'POST',
                    credentials: 'same-origin',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ api_key: apiKey }),
                })

                if (!res.ok) {
                    const data = await res.json()
                    this.error = data.message || data.errors?.api_key?.[0] || 'Invalid API key'
                    return false
                }

                this.hasOwnKey = true
                this.limitReached = false
                this.showKeyModal = false
                return true

            } catch (e) {
                this.error = e.message || 'Network error'
                return false
            } finally {
                this.loading = false
            }
        },

        clearContent() {
            this.content = ''
            this.error = null
        },

        copyContent() {
            if (this.content) {
                navigator.clipboard.writeText(this.content)
            }
        },
    },
})
