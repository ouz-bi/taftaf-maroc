<script setup>
import { ref, computed } from 'vue'
import { Link, usePage } from '@inertiajs/vue3'
import {
    HomeIcon,
    ShoppingBagIcon,
    CubeIcon,
    TruckIcon,
    UserGroupIcon,
    ChartBarIcon,
    Cog6ToothIcon,
    Bars3Icon,
    XMarkIcon,
    ArrowRightOnRectangleIcon,
    BuildingStorefrontIcon
} from '@heroicons/vue/24/outline'

const page = usePage()
const user = computed(() => page.props.auth.user)
const sidebarOpen = ref(false)

const navigation = [
    { name: 'Tableau de bord', href: '/admin/dashboard', icon: HomeIcon },
    { name: 'Commandes', href: '/admin/orders', icon: ShoppingBagIcon },
    { name: 'Produits', href: '/admin/products', icon: CubeIcon },
    { name: 'Catégories', href: '/admin/categories', icon: ChartBarIcon },
    { name: 'Clients', href: '/admin/customers', icon: UserGroupIcon },
    { name: 'Livreurs', href: '/admin/riders', icon: TruckIcon },
    { name: 'Paramètres', href: '/admin/settings', icon: Cog6ToothIcon },
]
</script>

<template>
    <div class="min-h-screen bg-gray-50">
        <!-- Mobile sidebar overlay -->
        <div
            v-if="sidebarOpen"
            @click="sidebarOpen = false"
            class="fixed inset-0 bg-black bg-opacity-50 z-40 lg:hidden"
        />

        <!-- Sidebar -->
        <aside
            :class="[
                'fixed inset-y-0 left-0 w-64 bg-white text-gray-800 transform transition-transform duration-300 z-50 shadow-lg border-r border-gray-200',
                sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'
            ]"
        >
            <div class="flex flex-col h-full">
                <!-- Logo -->
                <div class="flex items-center justify-between h-20 px-6 bg-white border-b border-gray-200">
                    <Link href="/" class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-gradient-to-r from-blue-600 to-blue-700 rounded-xl flex items-center justify-center shadow-md">
                            <BuildingStorefrontIcon class="h-6 w-6 text-white" />
                        </div>
                        <div class="flex flex-col">
                            <span class="text-xl font-bold text-gray-900">TafTaf</span>
                            <span class="text-xs bg-blue-100 text-blue-700 px-2 py-0.5 rounded-full font-medium">ADMIN</span>
                        </div>
                    </Link>
                    <button
                        @click="sidebarOpen = false"
                        class="lg:hidden text-gray-500 hover:text-gray-700 transition-colors"
                    >
                        <XMarkIcon class="h-6 w-6" />
                    </button>
                </div>

                <!-- Navigation -->
                <nav class="flex-1 px-4 py-6 space-y-1 overflow-y-auto">
                    <Link
                        v-for="item in navigation"
                        :key="item.name"
                        :href="item.href"
                        :class="[
                            'flex items-center space-x-3 px-4 py-3 rounded-xl transition-all duration-200 group',
                            $page.url.startsWith(item.href)
                                ? 'bg-blue-50 text-blue-700 border border-blue-200 shadow-sm'
                                : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900 hover:border hover:border-gray-200'
                        ]"
                    >
                        <component
                            :is="item.icon"
                            :class="[
                                'h-5 w-5 transition-transform duration-200',
                                $page.url.startsWith(item.href)
                                    ? 'text-blue-600'
                                    : 'text-gray-400 group-hover:text-gray-600'
                            ]"
                        />
                        <span class="font-medium">{{ item.name }}</span>
                        <div
                            v-if="$page.url.startsWith(item.href)"
                            class="w-2 h-2 bg-blue-600 rounded-full ml-auto"
                        ></div>
                    </Link>
                </nav>

                <!-- User Info -->
                <div class="border-t border-gray-200 p-4 bg-gray-50">
                    <div class="flex items-center space-x-3 mb-3">
                        <div class="w-12 h-12 bg-gradient-to-r from-blue-600 to-blue-700 rounded-xl flex items-center justify-center shadow-md">
                            <span class="font-bold text-white text-lg">{{ user?.name[0] }}</span>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="font-semibold text-gray-900 truncate">{{ user?.name }}</p>
                            <p class="text-xs text-gray-500 truncate">{{ user?.email }}</p>
                        </div>
                    </div>

                    <div class="space-y-2">
                        <Link
                            href="/"
                            class="flex items-center space-x-2 text-gray-500 hover:text-gray-700 text-sm transition-colors p-2 rounded-lg hover:bg-white border border-transparent hover:border-gray-200"
                        >
                            <HomeIcon class="h-4 w-4" />
                            <span>Voir le site</span>
                        </Link>
                        <Link
                            href="/logout"
                            method="post"
                            as="button"
                            class="flex items-center space-x-2 text-gray-500 hover:text-red-600 text-sm transition-colors p-2 rounded-lg hover:bg-white border border-transparent hover:border-gray-200 w-full text-left"
                        >
                            <ArrowRightOnRectangleIcon class="h-4 w-4" />
                            <span>Déconnexion</span>
                        </Link>
                    </div>
                </div>
            </div>
        </aside>

        <!-- Main Content -->
        <div class="lg:ml-64">
            <!-- Top Bar -->
            <header class="bg-white shadow-sm sticky top-0 z-30 border-b border-gray-200">
                <div class="flex items-center justify-between h-16 px-6">
                    <button
                        @click="sidebarOpen = true"
                        class="lg:hidden text-gray-600 hover:text-gray-900 transition-colors p-2 rounded-lg hover:bg-gray-100"
                    >
                        <Bars3Icon class="h-6 w-6" />
                    </button>

                    <div class="flex-1 flex items-center justify-between">
                        <div class="flex-1 max-w-2xl">
                            <slot name="header" />
                        </div>

                        <!-- Notifications et profil -->
                        <div class="flex items-center space-x-4">
                            <!-- Indicateur en ligne -->
                            <div class="flex items-center space-x-2 text-sm text-gray-500">
                                <div class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></div>
                                <span>En ligne</span>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Page Content -->
            <main class="p-6">
                <div class="max-w-7xl mx-auto">
                    <slot />
                </div>
            </main>
        </div>
    </div>
</template>

<style scoped>
/* Scrollbar personnalisée pour la sidebar */
nav::-webkit-scrollbar {
    width: 4px;
}

nav::-webkit-scrollbar-track {
    background: rgba(0, 0, 0, 0.05);
    border-radius: 10px;
}

nav::-webkit-scrollbar-thumb {
    background: rgba(0, 0, 0, 0.2);
    border-radius: 10px;
}

nav::-webkit-scrollbar-thumb:hover {
    background: rgba(0, 0, 0, 0.3);
}
</style>
