<template>
    <div class="max-w-3xl mx-auto px-4 sm:px-6 py-16 space-y-8">

        <h1 class="text-2xl text-text">projects</h1>

        <div class="space-y-6">
            <div
                v-for="project in projects"
                :key="project.name"
                class="bg-surface border border-border p-4 sm:p-6"
            >
                <div class="flex items-start justify-between gap-4">
                    <h2 class="text-text text-sm font-medium">{{ project.name }}</h2>
                    <a
                        v-if="project.url"
                        :href="project.url"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="text-dim text-xs hover:text-accent transition-colors shrink-0"
                    >
                        github →
                    </a>
                    <span v-else class="text-dim text-xs shrink-0">private</span>
                </div>

                <p class="text-dim text-sm mt-2 leading-relaxed">{{ project.description }}</p>

                <p v-if="project.story" class="text-dim text-xs mt-2 leading-relaxed italic">
                    {{ project.story }}
                </p>

                <div class="flex flex-wrap gap-2 mt-3">
                    <span
                        v-for="tag in project.tags"
                        :key="tag"
                        class="text-dim text-xs bg-bg border border-border px-2 py-0.5 rounded"
                    >
                        {{ tag }}
                    </span>
                </div>
            </div>
        </div>

    </div>
</template>

<script>
export default {
    name: 'ProjectsPage',

    data() {
        return {
            projects: [
                {
                    name: 'sinfat.com',
                    description: 'This portfolio site. Laravel 12 API backend, Vue 3 SPA frontend, AI-powered blog playground with SSE streaming. Deployed on Oracle Cloud ARM VM with GitHub Actions CI/CD.',
                    story: null,
                    url: 'https://github.com/bernardjbs/sinfat-portfolio',
                    tags: ['Laravel 12', 'Vue 3', 'Pinia', 'Tailwind', 'Neuron AI', 'Oracle Cloud', 'GitHub Actions'],
                },
                {
                    name: 'pi-agent-toolkit',
                    description: 'TypeScript extensions for the pi coding agent. Session focus gate, bash guardrails, workflow observer with slash commands, blog drafting tools, progress tracking with session shutdown prompts, and a custom footer with git branch and context metering.',
                    story: null,
                    url: 'https://github.com/bernardjbs/pi-agent-toolkit',
                    tags: ['TypeScript', 'AI agents', 'CLI tooling', 'pi framework'],
                },
                {
                    name: 'Football Analytics Platform',
                    description: 'Sports analytics platform for Australian football — data ingestion, statistical models, and match prediction dashboards.',
                    story: 'Five iterations over two years. Started as a Laravel monolith, moved to separate API + SPA, tried microservices, came back to a simpler architecture. The real lesson was knowing when to stop rebuilding and start shipping.',
                    url: null,
                    tags: ['Laravel', 'Vue', 'Python', 'Data pipelines', 'MySQL'],
                },
                {
                    name: 'Time Capsule App',
                    description: 'A personal project for saving messages to your future self. Schedule delivery dates and receive them later via email.',
                    story: null,
                    url: null,
                    tags: ['Laravel', 'Vue', 'Queue workers', 'Email'],
                },
            ],
        }
    },
}
</script>
