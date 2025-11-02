<script setup>
import { ref, computed } from 'vue'
import { router } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import ProductCard from '@/Components/ProductCard.vue'
import { MagnifyingGlassIcon, FunnelIcon, XMarkIcon } from '@heroicons/vue/24/outline'

const props = defineProps({
    products: Object,
    categories: Array,
    filters: Object,
})

const searchQuery = ref(props.filters.search || '')
const selectedCategory = ref(props.filters.category || '')
const showFilters = ref(false)
const sortBy = ref(props.filters.sort || 'name')
const showHalalOnly = ref(props.filters.halal || false)
const showMadeInMorocco = ref(props.filters.made_in_morocco || false)

const applyFilters = () => {
    router.get('/products', {
        search: searchQuery.value,
        category: selectedCategory.value,
        sort: sortBy.value,
        halal: showHalalOnly.value ? 1 : 0,
        made_in_morocco: showMadeInMorocco.value ? 1 : 0,
    }, {
        preserveState: true,
        preserveScroll: true,
    })
}

const clearFilters = () => {
    searchQuery.value = ''
    selectedCategory.value = ''
    sortBy.value = 'name'
    showHalalOnly.value = false
    showMadeInMorocco.value = false
    applyFilters()
}

const hasActiveFilters = computed(() => {
    return searchQuery.value || selectedCategory.value || showHalalOnly.value || showMadeInMorocco.value
})
</script>

<template>
    <AppLayout>
        <div class="bg-white shadow-sm border-b">
            <div class="container-custom py-6">
                <h1 class="text-3xl font-bold text-gray-900 mb-2">Nos Produits</h1>
                <p class="text-gray-600">{{ products.total }} produits disponibles</p>
            </div>
        </div>

        <div class="container-custom py-8">
            <div class="flex flex-col lg:flex-row gap-8">
                <!-- Sidebar Filters (Desktop) -->
                <aside class="hidden lg:block w-64 flex-shrink-0">
                    <div class="card sticky top-20">
                        <div class="p-6">
                            <div class="flex items-center justify-between mb-4">
                                <h3 class="font-bold text-lg">Filtres</h3>
                                <button
                                    v-if="hasActiveFilters"
                                    @click="clearFilters"
                                    class="text-sm text-primary-600 hover:text-primary-700"
                                >
                                    Réinitialiser
                                </button>
                            </div>

                            <!-- Search -->
                            <div class="mb-6">
                                <label class="label">Rechercher</label>
                                <div class="relative">
                                    <input
                                        v-model="searchQuery"
                                        @input="applyFilters"
                                        type="text"
                                        placeholder="Nom du produit..."
                                        class="input-field pl-10"
                                    />
                                    <MagnifyingGlassIcon class="absolute left-3 top-3 h-5 w-5 text-gray-400" />
                                </div>
                            </div>

                            <!-- Categories -->
                            <div class="mb-6">
                                <label class="label">Catégorie</label>
                                <select
                                    v-model="selectedCategory"
                                    @change="applyFilters"
                                    class="input-field"
                                >
                                    <option value="">Toutes les catégories</option>
                                    <option
                                        v-for="category in categories"
                                        :key="category.id"
                                        :value="category.slug"
                                    >
                                        {{ category.icon }} {{ category.name }}
                                    </option>
                                </select>
                            </div>

                            <!-- Sort -->
                            <div class="mb-6">
                                <label class="label">Trier par</label>
                                <select
                                    v-model="sortBy"
                                    @change="applyFilters"
                                    class="input-field"
                                >
                                    <option value="name">Nom (A-Z)</option>
                                    <option value="price_asc">Prix croissant</option>
                                    <option value="price_desc">Prix décroissant</option>
                                    <option value="newest">Nouveautés</option>
                                </select>
                            </div>

                            <!-- Special Filters -->
                            <div class="space-y-3 border-t pt-4">
                                <label class="flex items-center cursor-pointer">
                                    <input
                                        v-model="showHalalOnly"
                                        @change="applyFilters"
                                        type="checkbox"
                                        class="w-4 h-4 text-primary-600 border-gray-300 rounded focus:ring-primary-500"
                                    />
                                    <span class="ml-2 text-sm text-gray-700">✓ Produits Halal uniquement</span>
                                </label>

                                <label class="flex items-center cursor-pointer">
                                    <input
                                        v-model="showMadeInMorocco"
                                        @change="applyFilters"
                                        type="checkbox"
                                        class="w-4 h-4 text-primary-600 border-gray-300 rounded focus:ring-primary-500"
                                    />
                                    <span class="ml-2 text-sm text-gray-700">🇲🇦 Made in Morocco</span>
                                </label>
                            </div>
                        </div>
                    </div>
                </aside>

                <!-- Main Content -->
                <div class="flex-1">
                    <!-- Mobile Filters Button -->
                    <div class="lg:hidden mb-4">
                        <button
                            @click="showFilters = true"
                            class="btn-secondary w-full flex items-center justify-center space-x-2"
                        >
                            <FunnelIcon class="h-5 w-5" />
                            <span>Filtres</span>
                            <span v-if="hasActiveFilters" class="badge-primary ml-2">
                                Actifs
                            </span>
                        </button>
                    </div>

                    <!-- Products Grid -->
                    <div v-if="products.data.length > 0" class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-4 gap-4 md:gap-6">
                        <ProductCard
                            v-for="product in products.data"
                            :key="product.id"
                            :product="product"
                        />
                    </div>

                    <!-- Empty State -->
                    <div v-else class="text-center py-16">
                        <div class="text-6xl mb-4">🔍</div>
                        <h3 class="text-xl font-semibold text-gray-900 mb-2">
                            Aucun produit trouvé
                        </h3>
                        <p class="text-gray-600 mb-6">
                            Essayez de modifier vos filtres
                        </p>
                        <button @click="clearFilters" class="btn-primary">
                            Réinitialiser les filtres
                        </button>
                    </div>

                    <!-- Pagination -->
                    <div v-if="products.data.length > 0" class="mt-8 flex justify-center">
                        <nav class="flex items-center space-x-2">
                            <button
                                v-for="link in products.links"
                                :key="link.label"
                                @click="router.visit(link.url)"
                                :disabled="!link.url"
                                :class="[
                                    'px-4 py-2 text-sm font-medium rounded-lg transition',
                                    link.active
                                        ? 'bg-primary-600 text-white'
                                        : 'bg-white text-gray-700 hover:bg-gray-100 border border-gray-300',
                                    !link.url && 'opacity-50 cursor-not-allowed'
                                ]"
                                v-html="link.label"
                            />
                        </nav>
                    </div>
                </div>
            </div>
        </div>

        <!-- Mobile Filters Modal -->
        <Teleport to="body">
            <div
                v-if="showFilters"
                class="fixed inset-0 z-50 lg:hidden"
            >
                <div
                    @click="showFilters = false"
                    class="fixed inset-0 bg-black bg-opacity-50"
                />
                <div class="fixed inset-y-0 right-0 w-full max-w-sm bg-white shadow-xl">
                    <div class="flex items-center justify-between p-4 border-b">
                        <h3 class="font-bold text-lg">Filtres</h3>
                        <button @click="showFilters = false" class="p-2 hover:bg-gray-100 rounded-lg">
                            <XMarkIcon class="h-6 w-6" />
                        </button>
                    </div>

                    <div class="p-6 overflow-y-auto h-full pb-24">
                        <!-- Same filters as desktop -->
                        <div class="mb-6">
                            <label class="label">Rechercher</label>
                            <input
                                v-model="searchQuery"
                                type="text"
                                placeholder="Nom du produit..."
                                class="input-field"
                            />
                        </div>

                        <div class="mb-6">
                            <label class="label">Catégorie</label>
                            <select v-model="selectedCategory" class="input-field">
                                <option value="">Toutes les catégories</option>
                                <option
                                    v-for="category in categories"
                                    :key="category.id"
                                    :value="category.slug"
                                >
                                    {{ category.icon }} {{ category.name }}
                                </option>
                            </select>
                        </div>

                        <div class="mb-6">
                            <label class="label">Trier par</label>
                            <select v-model="sortBy" class="input-field">
                                <option value="name">Nom (A-Z)</option>
                                <option value="price_asc">Prix croissant</option>
                                <option value="price_desc">Prix décroissant</option>
                                <option value="newest">Nouveautés</option>
                            </select>
                        </div>

                        <div class="space-y-3 border-t pt-4">
                            <label class="flex items-center cursor-pointer">
                                <input
                                    v-model="showHalalOnly"
                                    type="checkbox"
                                    class="w-4 h-4 text-primary-600 border-gray-300 rounded"
                                />
                                <span class="ml-2 text-sm text-gray-700">✓ Produits Halal</span>
                            </label>

                            <label class="flex items-center cursor-pointer">
                                <input
                                    v-model="showMadeInMorocco"
                                    type="checkbox"
                                    class="w-4 h-4 text-primary-600 border-gray-300 rounded"
                                />
                                <span class="ml-2 text-sm text-gray-700">🇲🇦 Made in Morocco</span>
                            </label>
                        </div>
                    </div>

                    <div class="absolute bottom-0 left-0 right-0 p-4 bg-white border-t flex space-x-3">
                        <button @click="clearFilters" class="flex-1 btn-secondary">
                            Réinitialiser
                        </button>
                        <button
                            @click="applyFilters(); showFilters = false"
                            class="flex-1 btn-primary"
                        >
                            Appliquer
                        </button>
                    </div>
                </div>
            </div>
        </Teleport>
    </AppLayout>
</template>
