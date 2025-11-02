<script setup>
import { ref, watch } from 'vue'
import { router } from '@inertiajs/vue3'
import { MagnifyingGlassIcon } from '@heroicons/vue/24/outline'

const searchQuery = ref('')
const isSearching = ref(false)
const suggestions = ref([])

// Debounce search
let searchTimeout = null

watch(searchQuery, (newValue) => {
    clearTimeout(searchTimeout)

    if (newValue.length >= 2) {
        isSearching.value = true
        searchTimeout = setTimeout(() => {
            // Ici, faire un appel API pour obtenir les suggestions
            // Pour l'instant, on simule
            isSearching.value = false
        }, 300)
    } else {
        suggestions.value = []
    }
})

const search = () => {
    if (searchQuery.value) {
        router.visit(`/products?search=${searchQuery.value}`)
    }
}
</script>

<template>
    <div class="relative">
        <form @submit.prevent="search" class="relative">
            <input
                v-model="searchQuery"
                type="text"
                placeholder="Rechercher un produit..."
                class="input-field pl-10 pr-4"
            />
            <MagnifyingGlassIcon class="absolute left-3 top-3 h-5 w-5 text-gray-400" />

            <!-- Loading Spinner -->
            <div v-if="isSearching" class="absolute right-3 top-3">
                <LoadingSpinner size="sm" />
            </div>
        </form>

        <!-- Suggestions Dropdown -->
        <div
            v-if="suggestions.length > 0"
            class="absolute top-full left-0 right-0 mt-2 bg-white rounded-lg shadow-lg border max-h-64 overflow-y-auto z-50"
        >
            <div
                v-for="suggestion in suggestions"
                :key="suggestion.id"
                class="p-3 hover:bg-gray-50 cursor-pointer border-b last:border-b-0"
            >
                <p class="font-medium">{{ suggestion.name }}</p>
                <p class="text-sm text-gray-600">{{ suggestion.category.name }}</p>
            </div>
        </div>
    </div>
</template>
