<template>
    <div class="min-h-screen flex flex-col bg-bg text-text">
        <NavBar />
        <main class="flex-1">
            <router-view />
        </main>
        <AppFooter />
    </div>
</template>

<script>
import NavBar from '../components/NavBar.vue'
import AppFooter from '../components/Footer.vue'

export default {
    name: 'AppLayout',

    components: { NavBar, AppFooter },
}
</script>
