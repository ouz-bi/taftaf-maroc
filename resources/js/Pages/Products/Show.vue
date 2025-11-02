<script setup>
import { ref } from 'vue'
import { router } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import ProductCard from '@/Components/ProductCard.vue'
import {
    MinusIcon,
    PlusIcon,
    ShoppingCartIcon,
    CheckCircleIcon,
    XCircleIcon,
    TruckIcon,
    ShieldCheckIcon
} from '@heroicons/vue/24/outline'
import { HeartIcon } from '@heroicons/vue/24/solid'

const props = defineProps({
    product: Object,
    relatedProducts: Array,
})

const quantity = ref(1)
const selectedImage = ref(props.product.image)
const adding = ref(false)

const incrementQuantity = () => {
    if (quantity.value < props.product.stock) {
        quantity.value++
    }
}

const decrementQuantity = () => {
    if (quantity.value > 1) {
        quantity.value--
    }
}

const addToCart = () => {
    adding.value = true

    router.post(`/cart/add/${props.product.id}`, {
        quantity: quantity.value
    }, {
        preserveScroll: true,
        onSuccess: () => {
            adding.value = false
            quantity.value = 1
        },
        onError: () => {
            adding.value = false
        }
    })
}

const currentPrice = props.product.promo_price || props.product.price
const hasPromo = props.product.promo_price && props.product.promo_price < props.product.price
const discountPercentage = hasPromo
    ? Math.round((1 - props.product.promo_price / props.product.price) * 100)
    : 0
</script>

<template>
    <AppLayout>
        <div class="container-custom py-8">
            <!-- Breadcrumb -->
            <nav class="flex items-center space-x-2 text-sm text-gray-600 mb-6">
                <a href="/" class="hover:text-primary-600">Accueil</a>
                <span>/</span>
                <a href="/products" class="hover:text-primary-600">Produits</a>
                <span>/</span>
                <span class="text-gray-900">{{ product.name }}</span>
            </nav>

            <!-- Product Detail -->
            <div class="grid lg:grid-cols-2 gap-8 mb-16">
                <!-- Images -->
                <div>
                    <div class="card overflow-hidden mb-4">
                        <div class="relative aspect-square bg-gray-100">
                            <img
                                :src="selectedImage || '/images/placeholder.png'"
                                :alt="product.name"
                                class="w-full h-full object-cover"
                            />

                            <!-- Badges -->
                            <div class="absolute top-4 left-4 space-y-2">
                                <span v-if="hasPromo" class="badge-promo text-base px-3 py-1.5">
                                    -{{ discountPercentage }}%
                                </span>
                                <span v-if="product.is_halal" class="badge-halal block">
                                    ✓ Halal
                                </span>
                                <span v-if="product.is_made_in_morocco" class="block bg-morocco-red text-white px-3 py-1.5 text-sm font-bold rounded shadow-sm">
                                    🇲🇦 Made in Morocco
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Thumbnail Images -->
                    <div v-if="product.images && product.images.length > 0" class="grid grid-cols-4 gap-2">
                        <button
                            @click="selectedImage = product.image"
                            :class="[
                                'card overflow-hidden aspect-square',
                                selectedImage === product.image && 'ring-2 ring-primary-600'
                            ]"
                        >
                            <img :src="product.image" class="w-full h-full object-cover" />
                        </button>
                        <button
                            v-for="(image, index) in product.images"
                            :key="index"
                            @click="selectedImage = image"
                            :class="[
                                'card overflow-hidden aspect-square',
                                selectedImage === image && 'ring-2 ring-primary-600'
                            ]"
                        >
                            <img :src="image" class="w-full h-full object-cover" />
                        </button>
                    </div>
                </div>

                <!-- Product Info -->
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 mb-2">
                        {{ product.name }}
                    </h1>

                    <p v-if="product.category" class="text-gray-600 mb-4">
                        {{ product.category.name }}
                    </p>

                    <!-- Price -->
                    <div class="mb-6">
                        <div class="flex items-baseline space-x-3">
                            <span class="text-4xl font-bold text-primary-600">
                                {{ currentPrice }} DH
                            </span>
                            <span v-if="hasPromo" class="text-2xl text-gray-500 line-through">
                                {{ product.price }} DH
                            </span>
                        </div>
                        <p class="text-sm text-gray-600 mt-1">
                            Prix par {{ product.unit }}
                        </p>
                    </div>

                    <!-- Stock Status -->
                    <div class="flex items-center space-x-2 mb-6">
                        <CheckCircleIcon v-if="product.is_available && product.stock > 0" class="h-5 w-5 text-green-600" />
                        <XCircleIcon v-else class="h-5 w-5 text-red-600" />
                        <span :class="product.is_available && product.stock > 0 ? 'text-green-600' : 'text-red-600'" class="font-medium">
                            {{ product.is_available && product.stock > 0 ? 'En stock' : 'Rupture de stock' }}
                        </span>
                        <span v-if="product.stock > 0" class="text-gray-600">
                            ({{ product.stock }} disponibles)
                        </span>
                    </div>

                    <!-- Description -->
                    <div v-if="product.description" class="mb-6 pb-6 border-b">
                        <h3 class="font-semibold text-lg mb-2">Description</h3>
                        <p class="text-gray-700 leading-relaxed">
                            {{ product.description }}
                        </p>
                    </div>

                    <!-- Quantity Selector -->
                    <div class="mb-6">
                        <label class="label">Quantité</label>
                        <div class="flex items-center space-x-3">
                            <button
                                @click="decrementQuantity"
                                :disabled="quantity <= 1"
                                class="btn-secondary btn-sm disabled:opacity-50"
                            >
                                <MinusIcon class="h-4 w-4" />
                            </button>
                            <input
                                v-model.number="quantity"
                                type="number"
                                min="1"
                                :max="product.stock"
                                class="w-20 text-center input-field"
                            />
                            <button
                                @click="incrementQuantity"
                                :disabled="quantity >= product.stock"
                                class="btn-secondary btn-sm disabled:opacity-50"
                            >
                                <PlusIcon class="h-4 w-4" />
                            </button>
                        </div>
                    </div>

                    <!-- Add to Cart -->
                    <div class="flex space-x-3 mb-8">
                        <button
                            @click="addToCart"
                            :disabled="!product.is_available || product.stock <= 0 || adding"
                            class="flex-1 btn-primary btn-lg flex items-center justify-center space-x-2"
                        >
                            <ShoppingCartIcon class="h-6 w-6" />
                            <span>{{ adding ? 'Ajout en cours...' : 'Ajouter au panier' }}</span>
                        </button>
                        <button class="btn-outline px-6">
                            <HeartIcon class="h-6 w-6" />
                        </button>
                    </div>

                    <!-- Features -->
                    <div class="grid grid-cols-2 gap-4 bg-gray-50 p-6 rounded-lg">
                        <div class="flex items-start space-x-3">
                            <TruckIcon class="h-6 w-6 text-primary-600 flex-shrink-0" />
                            <div>
                                <p class="font-semibold text-sm">Livraison rapide</p>
                                <p class="text-xs text-gray-600">En 20 minutes</p>
                            </div>
                        </div>
                        <div class="flex items-start space-x-3">
                            <ShieldCheckIcon class="h-6 w-6 text-primary-600 flex-shrink-0" />
                            <div>
                                <p class="font-semibold text-sm">Qualité garantie</p>
                                <p class="text-xs text-gray-600">Produits frais</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Related Products -->
            <div v-if="relatedProducts.length > 0">
                <h2 class="text-2xl font-bold mb-6">Produits similaires</h2>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                    <ProductCard
                        v-for="relatedProduct in relatedProducts"
                        :key="relatedProduct.id"
                        :product="relatedProduct"
                    />
                </div>
            </div>
        </div>
    </AppLayout>
</template>
