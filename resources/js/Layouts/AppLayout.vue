<script setup>
    import { ref, computed } from 'vue'
    import { Link, usePage } from '@inertiajs/vue3'
    import {
        ShoppingCartIcon,
        UserIcon,
        MagnifyingGlassIcon,
        Bars3Icon,
        XMarkIcon
    } from '@heroicons/vue/24/outline'

    // Récupérer les données partagées (auth, cart_count, flash)
    const page = usePage()
    const user = computed(() => page.props.auth.user)
    const cartCount = computed(() => page.props.cart_count)
    const flash = computed(() => page.props.flash)

    // État du menu mobile
    const mobileMenuOpen = ref(false)
</script>

<template>
    <div class="min-h-screen bg-gray-50">
        <!-- Flash Messages -->
        <div v-if="flash.success" class="bg-green-500 text-white px-4 py-3 text-center">
            {{ flash.success }}
        </div>
        <div v-if="flash.error" class="bg-red-500 text-white px-4 py-3 text-center">
            {{ flash.error }}
        </div>

        <!-- Header -->
        <header class="bg-white shadow-sm sticky top-0 z-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center h-16">
                    <!-- Logo -->
                    <Link href="/" class="flex items-center">
                        <span class="text-2xl font-bold text-primary-600">TafTaf</span>
                        <span class="text-sm text-gray-600 ml-2">Maroc 🇲🇦</span>
                    </Link>

                    <!-- Search Bar (Desktop) -->
                    <div class="hidden md:flex flex-1 max-w-lg mx-8">
                        <div class="relative w-full">
                            <input
                                type="text"
                                placeholder="Rechercher un produit..."
                                class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                            />
                            <MagnifyingGlassIcon class="absolute left-3 top-2.5 h-5 w-5 text-gray-400" />
                        </div>
                    </div>

                    <!-- Right Menu -->
                    <div class="flex items-center space-x-4">
                        <!-- Cart -->
                        <Link href="/cart" class="relative p-2 hover:bg-gray-100 rounded-lg">
                            <ShoppingCartIcon class="h-6 w-6 text-gray-700" />
                            <span
                                v-if="cartCount > 0"
                                class="absolute -top-1 -right-1 bg-primary-600 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center"
                            >
                                {{ cartCount }}
                            </span>
                        </Link>

                        <!-- User Menu -->
                        <div v-if="user" class="relative">
                            <div class="flex items-center space-x-2">
                                <!-- Bouton Admin (si admin) -->
                                <Link
                                    v-if="user.role === 'admin'"
                                    href="/admin/dashboard"
                                    class="btn-secondary btn-sm"
                                >
                                    📊 Admin
                                </Link>

                                <!-- Menu utilisateur -->
                                <div class="relative group">
                                    <button class="flex items-center space-x-2 p-2 hover:bg-gray-100 rounded-lg">
                                        <UserIcon class="h-6 w-6 text-gray-700" />
                                        <span class="hidden md:block text-sm font-medium">{{ user.name }}</span>
                                    </button>

                                    <!-- Dropdown -->
                                    <div class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg py-2 hidden group-hover:block z-50">
                                        <Link href="/profile" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                            Mon profil
                                        </Link>
                                        <Link href="/orders" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                            Mes commandes
                                        </Link>
                                        <Link href="/addresses" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                            Mes adresses
                                        </Link>
                                        <hr class="my-2">
                                        <Link href="/logout" method="post" as="button" class="block w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-gray-100">
                                            Déconnexion
                                        </Link>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div v-else>
                            <Link href="/login" class="btn-primary">
                                Connexion
                            </Link>
                        </div>

                        <!-- Mobile Menu Button -->
                        <button
                            @click="mobileMenuOpen = !mobileMenuOpen"
                            class="md:hidden p-2"
                        >
                            <Bars3Icon v-if="!mobileMenuOpen" class="h-6 w-6" />
                            <XMarkIcon v-else class="h-6 w-6" />
                        </button>
                    </div>
                </div>

                <!-- Mobile Search -->
                <div class="md:hidden pb-3">
                    <input
                        type="text"
                        placeholder="Rechercher..."
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg"
                    />
                </div>
            </div>

            <!-- Mobile Menu -->
            <div v-show="mobileMenuOpen" class="md:hidden border-t">
                <div class="px-4 py-3 space-y-2">
                    <Link href="/" class="block py-2 text-gray-700">Accueil</Link>
                    <Link href="/products" class="block py-2 text-gray-700">Produits</Link>
                    <Link href="/orders" class="block py-2 text-gray-700">Mes commandes</Link>
                </div>
            </div>
        </header>

        <!-- Main Content -->
        <main>
            <slot />
        </main>

        <!-- Footer -->
        <footer class="bg-gray-800 text-white mt-16">
            <div class="max-w-7xl mx-auto px-4 py-8">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <div>
                        <h3 class="font-bold text-lg mb-4">TafTaf Maroc</h3>
                        <p class="text-gray-400">Livraison ultra-rapide en 20 minutes</p>
                    </div>
                    <div>
                        <h3 class="font-bold text-lg mb-4">Contact</h3>
                        <p class="text-gray-400">Email: contact@taftaf.ma</p>
                        <p class="text-gray-400">Tel: +212 XXX XXX XXX</p>
                    </div>
                    <div>
                        <h3 class="font-bold text-lg mb-4">Suivez-nous</h3>
                        <p class="text-gray-400">Facebook | Instagram | Twitter</p>
                    </div>
                </div>
                <div class="border-t border-gray-700 mt-8 pt-4 text-center text-gray-400">
                    © 2025 TafTaf Maroc. Tous droits réservés.
                </div>
            </div>
        </footer>
    </div>
</template>
