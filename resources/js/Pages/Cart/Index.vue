<script setup>
import { ref, computed } from 'vue'
import { router, Link } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import { TrashIcon, MinusIcon, PlusIcon, ShoppingBagIcon } from '@heroicons/vue/24/outline'

const props = defineProps({
    cartItems: Array,
    total: Number,
    checkoutUrl: String,
})

const updating = ref({})

const updateQuantity = (cartItem, newQuantity) => {
    if (newQuantity < 1 || newQuantity > cartItem.product.stock) return

    updating.value[cartItem.id] = true

    router.put(`/cart/${cartItem.id}`, {
        quantity: newQuantity
    }, {
        preserveScroll: true,
        onFinish: () => {
            updating.value[cartItem.id] = false
        }
    })
}

const removeItem = (cartItemId) => {
    if (confirm('Êtes-vous sûr de vouloir retirer ce produit ?')) {
        router.delete(`/cart/${cartItemId}`, {
            preserveScroll: true,
        })
    }
}

const subtotal = computed(() => {
    return props.cartItems.reduce((sum, item) => {
        const price = item.product.promo_price || item.product.price
        return sum + (price * item.quantity)
    }, 0)
})

const deliveryFee = 15.00 // À calculer selon la zone
const total = computed(() => subtotal.value + deliveryFee)
</script>

<template>
    <AppLayout>
        <div class="bg-gray-50 min-h-screen py-8">
            <div class="container-custom">
                <h1 class="text-3xl font-bold text-gray-900 mb-8">Mon Panier</h1>

                <div v-if="cartItems.length > 0" class="grid lg:grid-cols-3 gap-8">
                    <!-- Cart Items -->
                    <div class="lg:col-span-2 space-y-4">
                        <div
                            v-for="item in cartItems"
                            :key="item.id"
                            class="card p-4 md:p-6"
                        >
                            <div class="flex gap-4">
                                <!-- Product Image -->
                                <Link
                                    :href="`/products/${item.product.slug}`"
                                    class="flex-shrink-0"
                                >
                                    <img
                                        :src="item.product.image || '/images/placeholder.png'"
                                        :alt="item.product.name"
                                        class="w-24 h-24 object-cover rounded-lg"
                                    />
                                </Link>

                                <!-- Product Info -->
                                <div class="flex-1 min-w-0">
                                    <Link
                                        :href="`/products/${item.product.slug}`"
                                        class="font-semibold text-gray-900 hover:text-primary-600 block mb-1"
                                    >
                                        {{ item.product.name }}
                                    </Link>

                                    <p class="text-sm text-gray-600 mb-2">
                                        {{ item.product.category?.name }}
                                    </p>

                                    <!-- Badges -->
                                    <div class="flex flex-wrap gap-1 mb-3">
                                        <span v-if="item.product.is_halal" class="badge-success text-xs">
                                            Halal
                                        </span>
                                        <span v-if="item.product.is_made_in_morocco" class="badge-info text-xs">
                                            🇲🇦 Made in Morocco
                                        </span>
                                    </div>

                                    <!-- Price -->
                                    <div class="flex items-center space-x-2 mb-3">
                                        <span class="text-lg font-bold text-primary-600">
                                            {{ item.product.promo_price || item.product.price }} DH
                                        </span>
                                        <span v-if="item.product.promo_price" class="text-sm text-gray-500 line-through">
                                            {{ item.product.price }} DH
                                        </span>
                                    </div>

                                    <!-- Quantity Controls -->
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center space-x-2">
                                            <button
                                                @click="updateQuantity(item, item.quantity - 1)"
                                                :disabled="item.quantity <= 1 || updating[item.id]"
                                                class="btn-secondary btn-sm disabled:opacity-50"
                                            >
                                                <MinusIcon class="h-4 w-4" />
                                            </button>
                                            <span class="w-12 text-center font-semibold">
                                                {{ item.quantity }}
                                            </span>
                                            <button
                                                @click="updateQuantity(item, item.quantity + 1)"
                                                :disabled="item.quantity >= item.product.stock || updating[item.id]"
                                                class="btn-secondary btn-sm disabled:opacity-50"
                                            >
                                                <PlusIcon class="h-4 w-4" />
                                            </button>
                                        </div>

                                        <!-- Subtotal & Remove -->
                                        <div class="flex items-center space-x-4">
                                            <span class="font-bold text-gray-900">
                                                {{ ((item.product.promo_price || item.product.price) * item.quantity).toFixed(2) }} DH
                                            </span>
                                            <button
                                                @click="removeItem(item.id)"
                                                class="text-red-600 hover:text-red-700 p-2 hover:bg-red-50 rounded"
                                            >
                                                <TrashIcon class="h-5 w-5" />
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Order Summary -->
                    <div class="lg:col-span-1">
                        <div class="card sticky top-20">
                            <div class="p-6">
                                <h2 class="text-xl font-bold mb-6">Récapitulatif</h2>

                                <div class="space-y-3 mb-6 pb-6 border-b">
                                    <div class="flex justify-between text-gray-700">
                                        <span>Sous-total</span>
                                        <span class="font-medium">{{ subtotal.toFixed(2) }} DH</span>
                                    </div>
                                    <div class="flex justify-between text-gray-700">
                                        <span>Frais de livraison</span>
                                        <span class="font-medium">{{ deliveryFee.toFixed(2) }} DH</span>
                                    </div>
                                </div>

                                <div class="flex justify-between items-baseline mb-6">
                                    <span class="text-lg font-bold">Total</span>
                                    <span class="text-2xl font-bold text-primary-600">
                                        {{ total.toFixed(2) }} DH
                                    </span>
                                </div>

                                <Link :href="checkoutUrl || '/guest/checkout'" class="btn-primary w-full text-center block mb-3">
                                    Passer la commande
                                </Link>

                                <Link href="/products" class="btn-outline w-full text-center block">
                                    Continuer mes achats
                                </Link>

                                <!-- Promo Code -->
                                <div class="mt-6 pt-6 border-t">
                                    <label class="label">Code promo</label>
                                    <div class="flex space-x-2">
                                        <input
                                            type="text"
                                            placeholder="Entrez votre code"
                                            class="input-field flex-1"
                                        />
                                        <button class="btn-secondary">
                                            Appliquer
                                        </button>
                                    </div>
                                </div>

                                <!-- Info -->
                                <div class="mt-6 bg-blue-50 p-4 rounded-lg">
                                    <p class="text-sm text-blue-800">
                                        🚀 <strong>Livraison en 20 minutes</strong> pour toutes les commandes dans votre zone
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Empty Cart -->
                <div v-else class="text-center py-16">
                    <ShoppingBagIcon class="h-24 w-24 text-gray-300 mx-auto mb-6" />
                    <h3 class="text-2xl font-bold text-gray-900 mb-2">
                        Votre panier est vide
                    </h3>
                    <p class="text-gray-600 mb-8">
                        Ajoutez des produits pour commencer vos achats
                    </p>
                    <Link href="/products" class="btn-primary inline-block">
                        Découvrir nos produits
                    </Link>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
