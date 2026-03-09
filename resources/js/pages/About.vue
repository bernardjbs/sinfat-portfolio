<template>
    <div class="max-w-3xl mx-auto px-4 sm:px-6 py-16 space-y-12">

        <!-- Header -->
        <section class="space-y-4">
            <h1 class="text-2xl text-text">about</h1>
            <p class="text-dim text-base leading-relaxed">
                I'm a full-stack developer working at a Perth-based SaaS company since 2022.
                I build and maintain web applications with Laravel and Vue, and I've been
                increasingly focused on integrating AI tooling into development workflows.
            </p>
            <p class="text-dim text-sm leading-relaxed">
                Before that, I studied Computer Science at Edith Cowan University and completed
                a certificate program at the University of Western Australia. I like solving
                real problems with practical tools — not chasing trends.
            </p>
        </section>

        <!-- Skills -->
        <section class="space-y-6">
            <h2 class="text-lg text-text">skills</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <div
                    v-for="group in skillGroups"
                    :key="group.category"
                    class="bg-surface border border-border p-4"
                >
                    <h3 class="text-accent text-xs font-medium uppercase tracking-wide mb-3">
                        {{ group.category }}
                    </h3>
                    <div class="flex flex-wrap gap-2">
                        <span
                            v-for="skill in group.items"
                            :key="skill"
                            class="text-dim text-xs bg-bg border border-border px-2 py-0.5 rounded"
                        >
                            {{ skill }}
                        </span>
                    </div>
                </div>
            </div>
        </section>

        <!-- Education -->
        <section class="space-y-4">
            <h2 class="text-lg text-text">education</h2>
            <div class="space-y-3">
                <div
                    v-for="edu in education"
                    :key="edu.title"
                    class="border-b border-border pb-3"
                >
                    <p class="text-text text-sm">{{ edu.title }}</p>
                    <p class="text-dim text-xs mt-1">{{ edu.institution }}</p>
                </div>
            </div>
        </section>

        <!-- Philosophy -->
        <section class="space-y-4">
            <h2 class="text-lg text-text">how i work</h2>
            <p class="text-dim text-sm leading-relaxed">
                I prefer simple, boring infrastructure over clever abstractions. I write tests
                before shipping. I document decisions so the next person (or future me) doesn't
                have to guess. I think AI is a tool — useful when applied carefully, harmful
                when applied blindly.
            </p>
        </section>

        <!-- CV Download -->
        <section>
            <a
                href="/cv.pdf"
                target="_blank"
                rel="noopener noreferrer"
                class="inline-block bg-accent text-white px-4 py-2 text-sm font-medium hover:bg-green-700 transition"
            >
                download cv
            </a>
        </section>

    </div>
</template>

<script>
export default {
    name: 'AboutPage',

    data() {
        return {
            skillGroups: [
                {
                    category: 'Backend',
                    items: ['Laravel', 'PHP', 'MySQL', 'Redis', 'REST APIs', 'Queue workers'],
                },
                {
                    category: 'Frontend',
                    items: ['Vue 3', 'Tailwind CSS', 'Pinia', 'Vue Router', 'Vite'],
                },
                {
                    category: 'AI & Tooling',
                    items: ['Neuron AI', 'Ollama', 'Anthropic API', 'SSE streaming', 'pi framework'],
                },
                {
                    category: 'Infrastructure',
                    items: ['Oracle Cloud', 'Nginx', 'GitHub Actions', 'Supervisor', 'SSL/TLS'],
                },
            ],

            education: [
                {
                    title: 'Bachelor of Science (Computer Science)',
                    institution: 'Edith Cowan University',
                },
                {
                    title: 'Certificate in Software Development',
                    institution: 'University of Western Australia',
                },
            ],
        }
    },
}
</script>
