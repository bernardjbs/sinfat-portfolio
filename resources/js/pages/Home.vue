<template>
    <div class="max-w-3xl mx-auto px-4 sm:px-6 py-16 space-y-16">

        <!-- Hero -->
        <section class="space-y-6">
            <p class="text-dim text-sm">hi, i'm</p>
            <h1 class="text-3xl text-text font-semibold">bernard</h1>
            <p class="text-base text-dim leading-relaxed max-w-xl">
                Full-stack developer (Laravel + Vue) — helping teams integrate AI responsibly.
            </p>
            <p class="text-sm text-dim leading-relaxed max-w-xl">
                Perth-based. Building web applications, exploring AI tooling,
                and writing about what I learn along the way.
            </p>
            <div class="flex gap-4 pt-2">
                <a
                    href="/cv.pdf"
                    target="_blank"
                    rel="noopener noreferrer"
                    class="bg-accent text-white px-4 py-2 text-sm font-medium hover:bg-green-700 transition"
                >
                    download cv
                </a>
                <router-link
                    to="/contact"
                    class="border border-border text-text px-4 py-2 text-sm hover:border-accent hover:text-accent transition"
                >
                    get in touch
                </router-link>
            </div>
        </section>

        <!-- Featured Projects -->
        <section class="space-y-6">
            <h2 class="text-lg text-text">featured projects</h2>
            <div class="space-y-4">
                <div
                    v-for="project in featuredProjects"
                    :key="project.name"
                    class="bg-surface border border-border p-4 sm:p-6 hover:border-accent transition-colors"
                >
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <h3 class="text-text text-sm font-medium">{{ project.name }}</h3>
                            <p class="text-dim text-sm mt-1">{{ project.description }}</p>
                        </div>
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
                    <div class="flex gap-2 mt-3">
                        <span
                            v-for="tag in project.tags"
                            :key="tag"
                            class="bg-bg text-dim text-xs px-2 py-0.5 rounded border border-border"
                        >
                            {{ tag }}
                        </span>
                    </div>
                </div>
            </div>
            <router-link
                to="/projects"
                class="text-accent text-sm hover:underline"
            >
                all projects →
            </router-link>
        </section>

        <!-- Latest Posts -->
        <section class="space-y-6">
            <h2 class="text-lg text-text">latest posts</h2>

            <div v-if="loading" class="text-dim text-sm">loading...</div>

            <div v-else-if="posts.length === 0" class="text-dim text-sm">
                no posts yet.
            </div>

            <div v-else>
                <div
                    v-for="post in latestPosts"
                    :key="post.id"
                    class="border-b border-border py-4"
                >
                    <router-link
                        :to="{ name: 'blog-post', params: { slug: post.slug } }"
                        class="group block"
                    >
                        <h3 class="text-text text-sm group-hover:text-accent transition-colors">
                            {{ post.title }}
                        </h3>
                        <p v-if="post.excerpt" class="text-dim text-xs mt-1">
                            {{ post.excerpt }}
                        </p>
                        <div class="flex items-center gap-2 mt-2">
                            <span class="text-dim text-xs">{{ formatDate(post.published_at) }}</span>
                            <span v-if="post.ai_generated" class="text-dim text-xs">· AI drafted</span>
                        </div>
                    </router-link>
                </div>
            </div>

            <router-link
                to="/blog"
                class="text-accent text-sm hover:underline"
            >
                all posts →
            </router-link>
        </section>

    </div>
</template>

<script>
import { mapState, mapActions } from 'pinia'
import { useBlogStore } from '../stores/blog.js'

export default {
    name: 'HomePage',

    computed: {
        ...mapState(useBlogStore, ['posts', 'loading']),

        latestPosts() {
            return this.posts.slice(0, 3)
        },

        featuredProjects() {
            return [
                {
                    name: 'sinfat.com',
                    description: 'This portfolio site — Laravel 12 API, Vue 3 SPA, AI blog playground, deployed on Oracle Cloud.',
                    url: 'https://github.com/bernardjbs/sinfat-portfolio',
                    tags: ['Laravel', 'Vue 3', 'Tailwind', 'Oracle Cloud'],
                },
                {
                    name: 'pi-agent-toolkit',
                    description: 'Custom coding agent extensions and skills built on the pi framework.',
                    url: 'https://github.com/bernardjbs/pi-agent-toolkit',
                    tags: ['TypeScript', 'AI agents', 'CLI'],
                },
                {
                    name: 'Football Analytics',
                    description: 'Sports analytics platform — 5 iterations from monolith to microservices. A story about learning by rebuilding.',
                    url: null,
                    tags: ['Laravel', 'Python', 'Data pipelines'],
                },
            ]
        },
    },

    methods: {
        ...mapActions(useBlogStore, ['fetchPosts']),

        formatDate(iso) {
            if (!iso) return ''
            return new Date(iso).toLocaleDateString('en-AU', {
                year: 'numeric',
                month: 'short',
                day: 'numeric',
            })
        },
    },

    async mounted() {
        if (this.posts.length === 0) {
            await this.fetchPosts()
        }
    },
}
</script>
