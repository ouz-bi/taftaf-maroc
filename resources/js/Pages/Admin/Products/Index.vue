<script setup>
import { ref } from 'vue'
import { router, Link } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { PlusIcon, PencilIcon, TrashIcon, MagnifyingGlassIcon } from '@heroicons/vue/24/outline'

const props = defineProps({
    products: Object,
    categories: Array,
})

const searchQuery = ref('')
const showCreateModal = ref(false)

const search = () => {
    router.get('/admin/products', {
        search: searchQuery.value
    }, {
        preserveState: true,
        preserveScroll: true,
    })
}

const deleteProduct = (productId) => {
    if (confirm('Êtes-vous sûr de vouloir supprimer ce produit ?')) {
        router.delete(`/admin/products/${productId}`, {
            preserveScroll: true,
        })
    }
}

const toggleAvailability = (productId, currentStatus) => {
    router.patch(`/admin/products/${productId}/toggle`, {
        is_available: !currentStatus
    }, {
        preserveScroll: true,
    })
}
</script>

<template>
    <AdminLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h1 class="text-2xl font-bold text-gray-900">Gestion des Produits</h1>
                <Link
                    href="/admin/products/create"
                    class="btn-primary flex items-center space-x-2"
                >
                    <PlusIcon class="h-5 w-5" />
                    <span>Nouveau produit</span>
                </Link>
            </div>
        </template>

        <!-- Filters -->
        <div class="card mb-6">
            <div class="card-body">
                <div class="grid md:grid-cols-3 gap-4">
                    <!-- Search -->
                    <div class="md:col-span-2">
                        <div class="relative">
                            <input
                                v-model="searchQuery"
                                type="text"
                                placeholder="Rechercher un produit..."
                                class="input-field pl-10"
                            />
                            <MagnifyingGlassIcon class="absolute left-3 top-3 h-5 w-5 text-gray-400" />
                        </div>
                    </div>

                    <!-- Category Filter -->
                    <div>
                        <select class="input-field">
                            <option value="">Toutes les catégories</option>
                            <option
                                v-for="category in categories"
                                :key="category.id"
                                :value="category.id"
                            >
                                {{ category.name }}
                            </option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <!-- Products Grid -->
        <div class="grid md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            <div
                v-for="product in products.data"
                :key="product.id"
                class="card overflow-hidden"
            >
                <!-- Product Image -->
                <div class="relative aspect-square bg-gray-100">
                    <img
                        :src="product.image ? `/storage/${product.image}` : '/images/placeholder.png'"
                        :alt="product.name"
                        class="w-full h-full object-cover"
                    />
                    <!-- Status Badge -->
                    <div class="absolute top-2 right-2">
                        <button
                            @click="toggleAvailability(product.id, product.is_available)"
                            :class="[
                                'px-3 py-1 rounded-full text-xs font-bold',
                                product.is_available
                                    ? 'bg-green-500 text-white'
                                    : 'bg-red-500 text-white'
                            ]"
                        >
                            {{ product.is_available ? 'Actif' : 'Inactif' }}
                        </button>
                    </div>
                    <!-- Stock Badge -->
                    <div class="absolute bottom-2 left-2">
                        <span :class="[
                            'px-2 py-1 rounded text-xs font-bold',
                            product.stock > 10 ? 'bg-green-100 text-green-800' :
                            product.stock > 0 ? 'bg-orange-100 text-orange-800' :
                            'bg-red-100 text-red-800'
                        ]">
                            Stock: {{ product.stock }}
                        </span>
                    </div>
                </div>

                <!-- Product Info -->
                <div class="p-4">
                    <div class="mb-2">
                        <span class="text-xs text-gray-500">{{ product.category.name }}</span>
                    </div>
                    <h3 class="font-semibold text-gray-900 mb-2 line-clamp-2">
                        {{ product.name }}
                    </h3>

                    <!-- Price -->
                    <div class="flex items-center space-x-2 mb-3">
                        <span class="text-lg font-bold text-primary-600">
                            {{ product.promo_price || product.price }} DH
                        </span>
                        <span v-if="product.promo_price" class="text-sm text-gray-500 line-through">
                            {{ product.price }} DH
                        </span>
                    </div>

                    <!-- Badges -->
                    <div class="flex flex-wrap gap-1 mb-3">
                        <span v-if="product.is_featured" class="badge-primary text-xs">
                            ⭐ Featured
                        </span>
                        <span v-if="product.is_halal" class="badge-success text-xs">
                            ✓ Halal
                        </span>
                        <span v-if="product.is_made_in_morocco" class="badge-info text-xs">
                            🇲🇦 Maroc
                        </span>
                    </div>

                    <!-- Actions -->
                    <div class="flex space-x-2">
                        <Link
                            :href="`/admin/products/${product.id}/edit`"
                            class="flex-1 btn-secondary btn-sm flex items-center justify-center space-x-1"
                        >
                            <PencilIcon class="h-4 w-4" />
                            <span>Modifier</span>
                        </Link>
                        <button
                            @click="deleteProduct(product.id)"
                            class="btn-danger btn-sm"
                        >
                            <TrashIcon class="h-4 w-4" />
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pagination -->
        <div class="mt-8 flex justify-center">
            <nav class="flex space-x-2">
                <button
                    v-for="link in products.links"
                    :key="link.label"
                    @click="router.visit(link.url)"
                    :disabled="!link.url"
                    :class="[
                        'px-4 py-2 text-sm rounded',
                        link.active
                            ? 'bg-primary-600 text-white'
                            : 'bg-white border hover:bg-gray-50',
                        !link.url && 'opacity-50 cursor-not-allowed'
                    ]"
                    v-html="link.label"
                />
            </nav>
        </div>
    </AdminLayout>
</template>
