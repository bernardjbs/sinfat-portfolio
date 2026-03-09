<template>
    <div class="max-w-3xl mx-auto px-4 sm:px-6 py-16 space-y-10">

        <section class="space-y-4">
            <h1 class="text-2xl text-text">contact</h1>
            <p class="text-dim text-sm leading-relaxed">
                Best way to reach me is via email or GitHub. I'm not on Twitter/X.
            </p>
        </section>

        <section class="space-y-4">
            <div
                v-for="link in links"
                :key="link.label"
                class="flex items-center gap-4 border-b border-border pb-4"
            >
                <span class="text-dim text-xs w-20 shrink-0">{{ link.label }}</span>
                <a
                    v-if="link.url"
                    :href="link.url"
                    :target="link.external ? '_blank' : undefined"
                    :rel="link.external ? 'noopener noreferrer' : undefined"
                    class="text-accent text-sm hover:underline"
                >
                    {{ link.display }}
                </a>
                <span v-else class="text-text text-sm">{{ link.display }}</span>
            </div>
        </section>

        <section class="space-y-4">
            <h2 class="text-lg text-text">availability</h2>
            <p class="text-dim text-sm leading-relaxed">
                Based in Perth, Australia. Open to local roles with work-from-home flexibility.
            </p>
        </section>

    </div>
</template>

<script>
export default {
    name: 'ContactPage',

    data() {
        return {
            links: [
                {
                    label: 'email',
                    display: 'bernard@sinfat.com',
                    url: 'mailto:bernard@sinfat.com',
                    external: false,
                },
                {
                    label: 'github',
                    display: 'bernardjbs',
                    url: 'https://github.com/bernardjbs',
                    external: true,
                },
                {
                    label: 'linkedin',
                    display: 'linkedin.com/in/bernardjbs',
                    url: 'https://www.linkedin.com/in/bernardjbs',
                    external: true,
                },
                {
                    label: 'location',
                    display: 'Perth, Australia',
                    url: null,
                    external: false,
                },
            ],
        }
    },
}
</script>
