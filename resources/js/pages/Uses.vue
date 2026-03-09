<template>
    <div class="max-w-3xl mx-auto px-4 sm:px-6 py-16 space-y-10">

        <section class="space-y-4">
            <h1 class="text-2xl text-text">uses</h1>
            <p class="text-dim text-sm leading-relaxed">
                Tools, hardware, and software I use daily.
            </p>
        </section>

        <section
            v-for="group in categories"
            :key="group.category"
            class="space-y-4"
        >
            <h2 class="text-lg text-text">{{ group.category }}</h2>
            <div class="space-y-3">
                <div
                    v-for="item in group.items"
                    :key="item.name"
                    class="border-b border-border pb-3"
                >
                    <p class="text-text text-sm">{{ item.name }}</p>
                    <p class="text-dim text-xs mt-1">{{ item.description }}</p>
                </div>
            </div>
        </section>

    </div>
</template>

<script>
export default {
    name: 'UsesPage',

    data() {
        return {
            categories: [
                {
                    category: 'editor & terminal',
                    items: [
                        { name: 'VS Code', description: 'Primary editor. Vim keybindings, minimal extensions.' },
                        { name: 'iTerm2', description: 'Terminal emulator with tmux-style splits.' },
                        { name: 'pi', description: 'AI coding agent for scaffolding and module workflows.' },
                    ],
                },
                {
                    category: 'development',
                    items: [
                        { name: 'Laravel 12', description: 'PHP framework for API backends.' },
                        { name: 'Vue 3', description: 'Frontend SPA framework. Options API, Pinia stores.' },
                        { name: 'Tailwind CSS', description: 'Utility-first CSS. No custom stylesheets.' },
                        { name: 'Vite', description: 'Build tool. Fast HMR, code-splitting.' },
                        { name: 'MySQL', description: 'Primary database. Simple, reliable.' },
                        { name: 'Redis', description: 'Caching, rate limiting, session store.' },
                    ],
                },
                {
                    category: 'infrastructure',
                    items: [
                        { name: 'Oracle Cloud (ARM)', description: 'Free-tier A1 VM. Nginx, Supervisor, Let\'s Encrypt.' },
                        { name: 'GitHub Actions', description: 'CI/CD — auto-deploy on push to main.' },
                        { name: 'Laravel Valet', description: 'Local development. Zero-config HTTPS.' },
                    ],
                },
                {
                    category: 'ai tools',
                    items: [
                        { name: 'Claude (Anthropic)', description: 'Primary LLM for coding assistance and content generation.' },
                        { name: 'Ollama', description: 'Local LLM runner for development and testing.' },
                        { name: 'Neuron AI', description: 'PHP library for LLM integration with SSE streaming.' },
                    ],
                },
            ],
        }
    },
}
</script>
