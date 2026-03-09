<template>
    <RouterView />
</template>

<script>
import { useThemeStore } from './stores/theme.js'

export default {
    name: 'App',

    mounted() {
        const theme = useThemeStore()
        theme.applyToDocument()
    },
}
</script>
