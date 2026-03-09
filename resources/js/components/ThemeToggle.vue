<template>
    <button
        class="text-dim hover:text-text transition-colors p-1"
        :title="isDark ? 'Switch to light mode' : 'Switch to dark mode'"
        @click="toggle"
    >
        <Sun v-if="isDark" :size="16" />
        <Moon v-else :size="16" />
    </button>
</template>

<script>
import { mapState, mapActions } from 'pinia'
import { useThemeStore } from '../stores/theme.js'
import { Sun, Moon } from 'lucide-vue-next'

export default {
    name: 'ThemeToggle',

    components: { Sun, Moon },

    computed: {
        ...mapState(useThemeStore, ['isDark']),
    },

    methods: {
        ...mapActions(useThemeStore, ['toggle']),
    },
}
</script>
