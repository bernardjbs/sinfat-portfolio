import { defineStore } from 'pinia'

export const useThemeStore = defineStore('theme', {
    state: () => ({
        isDark: JSON.parse(localStorage.getItem('theme-dark') ?? 'true'),
    }),

    actions: {
        toggle() {
            this.isDark = !this.isDark
            localStorage.setItem('theme-dark', JSON.stringify(this.isDark))
            this.applyToDocument()
        },

        applyToDocument() {
            if (this.isDark) {
                document.documentElement.classList.add('dark')
            } else {
                document.documentElement.classList.remove('dark')
            }
        },
    },
})
