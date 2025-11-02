<script setup>
    import AppLayout from '@/Layouts/AppLayout.vue'
    import { Link } from '@inertiajs/vue3'

    // Props reçues du contrôleur Laravel
    const props = defineProps({
        categories: Array,
        featuredProducts: Array,
        promoProducts: Array
    })
</script>

<template>
    <AppLayout>
        <!-- Hero Section -->
        <section class="bg-gradient-to-r from-primary-600 to-primary-700 text-white py-8 md:py-12">
            <div class="max-w-7xl mx-auto px-4">
                <div class="text-center max-w-3xl mx-auto">
                    <h1 class="text-2xl md:text-4xl font-bold mb-3">
                        Livraison en 20 minutes ⚡
                    </h1>
                    <p class="text-base md:text-lg mb-6">
                        Vos produits préférés, livrés ultra rapidement au Maroc
                    </p>
                    <div class="flex items-center justify-center space-x-2 text-sm">
                        <span>🚀 Livraison rapide</span>
                        <span>•</span>
                        <span>✓ Produits frais</span>
                        <span>•</span>
                        <span>💳 Paiement facile</span>
                    </div>
                </div>
            </div>
        </section>

        <!-- Categories -->
        <!-- <section class="max-w-7xl mx-auto px-4 py-12">
            <h2 class="text-3xl font-bold mb-8">Catégories</h2>
            <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4">
                <Link
                    v-for="category in categories"
                    :key="category.id"
                    :href="`/products?category=${category.slug}`"
                    class="bg-white rounded-lg p-6 text-center hover:shadow-lg transition transform hover:-translate-y-1"
                >
                    <div class="text-4xl mb-2">{{ category.icon }}</div>
                    <h3 class="font-semibold">{{ category.name }}</h3>
                </Link>
            </div>
        </section> -->
        <section class="max-w-7xl mx-auto px-4 py-8 md:py-12">
            <h2 class="text-2xl md:text-3xl font-bold mb-6">Choisissez une catégorie</h2>
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4">
                <Link
                    v-for="category in categories"
                    :key="category.id"
                    :href="`/products?category=${category.slug}`"
                    class="group"
                >
                    <div class="bg-white rounded-xl overflow-hidden shadow-sm hover:shadow-md transition-all duration-300 transform hover:-translate-y-1">
                        <!-- Image de catégorie -->
                        <div class="aspect-square bg-gradient-to-br from-primary-50 to-primary-100 flex items-center justify-center p-6">
                            <span class="text-5xl md:text-6xl group-hover:scale-110 transition-transform duration-300">
                                {{ category.icon }}
                            </span>
                        </div>
                        <!-- Nom de catégorie -->
                        <div class="p-3 text-center">
                            <h3 class="font-semibold text-gray-800 text-sm md:text-base group-hover:text-primary-600 transition-colors">
                                {{ category.name }}
                            </h3>
                        </div>
                    </div>
                </Link>
            </div>
        </section>

        <!-- Produits en Promo -->
        <section v-if="promoProducts.length" class="bg-yellow-50 py-12">
            <div class="max-w-7xl mx-auto px-4">
                <h2 class="text-3xl font-bold mb-8">🔥 Promotions</h2>
                <div class="grid grid-cols-2 md:grid-cols-5 gap-6">
                    <div
                        v-for="product in promoProducts"
                        :key="product.id"
                        class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-xl transition"
                    >
                        <div class="relative">
                            <img
                                :src="product.image || '/images/placeholder.png'"
                                :alt="product.name"
                                class="w-full h-48 object-cover"
                            />
                            <div class="absolute top-2 right-2 bg-red-500 text-white px-2 py-1 rounded text-sm font-bold">
                                -{{ Math.round((1 - product.promo_price / product.price) * 100) }}%
                            </div>
                        </div>
                        <div class="p-4">
                            <h3 class="font-semibold mb-2">{{ product.name }}</h3>
                            <div class="flex items-center space-x-2">
                                <span class="text-lg font-bold text-primary-600">
                                    {{ product.promo_price }} DH
                                </span>
                                <span class="text-sm text-gray-500 line-through">
                                    {{ product.price }} DH
                                </span>
                            </div>
                            <Link
                                :href="`/products/${product.slug}`"
                                class="mt-3 btn-primary w-full text-center block"
                            >
                                Ajouter
                            </Link>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Produits Populaires -->
        <section class="max-w-7xl mx-auto px-4 py-12">
            <h2 class="text-3xl font-bold mb-8">Produits Populaires</h2>
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-6">
                <div
                    v-for="product in featuredProducts"
                    :key="product.id"
                    class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-xl transition"
                >
                    <img
                        :src="product.image || '/images/placeholder.png'"
                        :alt="product.name"
                        class="w-full h-48 object-cover"
                    />
                    <div class="p-4">
                        <h3 class="font-semibold mb-2">{{ product.name }}</h3>
                        <div class="text-lg font-bold text-primary-600 mb-3">
                            {{ product.price }} DH
                        </div>
                        <Link
                            :href="`/products/${product.slug}`"
                            class="btn-primary w-full text-center block"
                        >
                            Voir détails
                        </Link>
                    </div>
                </div>
            </div>
        </section>

        <!-- Comment ça marche -->
        <section class="bg-gray-100 py-16">
            <div class="max-w-7xl mx-auto px-4">
                <h2 class="text-3xl font-bold text-center mb-12">Comment ça marche ?</h2>
                <div class="grid md:grid-cols-3 gap-8">
                    <div class="text-center">
                        <div class="bg-primary-600 text-white w-16 h-16 rounded-full flex items-center justify-center text-2xl font-bold mx-auto mb-4">
                            1
                        </div>
                        <h3 class="font-bold text-xl mb-2">Commandez</h3>
                        <p class="text-gray-600">Choisissez vos produits préférés</p>
                    </div>
                    <div class="text-center">
                        <div class="bg-primary-600 text-white w-16 h-16 rounded-full flex items-center justify-center text-2xl font-bold mx-auto mb-4">
                            2
                        </div>
                        <h3 class="font-bold text-xl mb-2">On prépare</h3>
                        <p class="text-gray-600">Votre commande est préparée rapidement</p>
                    </div>
                    <div class="text-center">
                        <div class="bg-primary-600 text-white w-16 h-16 rounded-full flex items-center justify-center text-2xl font-bold mx-auto mb-4">
                            3
                        </div>
                        <h3 class="font-bold text-xl mb-2">Livraison 20min</h3>
                        <p class="text-gray-600">Recevez chez vous en un éclair</p>
                    </div>
                </div>
            </div>
        </section>
    </AppLayout>
</template>
