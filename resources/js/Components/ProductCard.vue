<script setup>
import { Link, router } from '@inertiajs/vue3'
import { ShoppingCartIcon } from '@heroicons/vue/24/outline'
import { ref } from 'vue'

const props = defineProps({
    product: Object
})

const adding = ref(false)

const addToCart = async () => {
    adding.value = true

    router.post(`/cart/add/${props.product.id}`, {
        quantity: 1
    }, {
        preserveScroll: true,
        onSuccess: () => {
            adding.value = false
        },
        onError: () => {
            adding.value = false
        }
    })
}
</script>

<template>
    <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-xl transition group">
        <!-- Image -->
        <Link :href="`/products/${product.slug}`" class="block relative">
            <img
                :src="product.image ? `/storage/${product.image}` : '/images/placeholder.png'"
                :alt="product.name"
                class="w-full h-48 object-cover group-hover:scale-105 transition duration-300"
            />

            <!-- Badges -->
            <div class="absolute top-2 left-2 space-y-1">
                <span v-if="product.is_halal" class="bg-green-500 text-white text-xs px-2 py-1 rounded block w-fit">
                    ✓ Halal
                </span>
                <span v-if="product.is_made_in_morocco" class="bg-morocco-red text-white text-xs px-2 py-1 rounded block w-fit">
                    🇲🇦 Made in Morocco
                </span>
            </div>

            <!-- Promo Badge -->
            <div v-if="product.promo_price" class="absolute top-2 right-2 bg-red-500 text-white px-2 py-1 rounded font-bold text-sm">
                -{{ Math.round((1 - product.promo_price / product.price) * 100) }}%
            </div>
        </Link>

        <!-- Content -->
        <div class="p-4">
            <Link :href="`/products/${product.slug}`">
                <h3 class="font-semibold text-gray-800 mb-1 hover:text-primary-600">
                    {{ product.name }}
                </h3>
            </Link>

            <p v-if="product.description" class="text-sm text-gray-600 mb-2 line-clamp-2">
                {{ product.description }}
            </p>

            <!-- Price -->
            <div class="flex items-center justify-between mb-3">
                <div>
                    <span v-if="product.promo_price" class="text-lg font-bold text-primary-600">
                        {{ product.promo_price }} DH
                    </span>
                    <span
                        :class="product.promo_price ? 'text-sm text-gray-500 line-through ml-2' : 'text-lg font-bold text-primary-600'"
                    >
                        {{ product.price }} DH
                    </span>
                </div>
                <span class="text-xs text-gray-500">/ {{ product.unit }}</span>
            </div>

            <!-- Actions -->
            <div class="flex space-x-2">
                <!-- <Link
                    :href="`/products/${product.slug}`"
                    class="flex-1 btn-secondary text-center text-sm"
                >
                    Détails
                </Link> -->
                <button
                    @click="addToCart"
                    :disabled="adding || !product.is_available"
                    class="flex-1 btn-primary text-sm flex items-center justify-center space-x-1"
                    :class="{ 'opacity-50 cursor-not-allowed': !product.is_available }"
                >
                    <ShoppingCartIcon class="h-4 w-4" />
                    <span>{{ adding ? 'Ajout...' : 'Ajouter' }}</span>
                </button>
            </div>

            <!-- Stock Warning -->
            <p v-if="!product.is_available" class="text-xs text-red-500 mt-2 text-center">
                Produit indisponible
            </p>
        </div>
    </div>
</template>
