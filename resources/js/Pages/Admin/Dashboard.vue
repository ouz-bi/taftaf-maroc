<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue'
import {
    ShoppingBagIcon,
    CurrencyDollarIcon,
    UserGroupIcon,
    TruckIcon,
    ArrowTrendingUpIcon,
    ArrowTrendingDownIcon
} from '@heroicons/vue/24/outline'

const props = defineProps({
    stats: Object,
    recentOrders: Array,
    topProducts: Array,
})
</script>

<template>
    <AdminLayout>
        <template #header>
            <h1 class="text-2xl font-bold text-gray-900">Dashboard</h1>
        </template>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <!-- Total Orders -->
            <div class="card">
                <div class="card-body">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-600 mb-1">Commandes (aujourd'hui)</p>
                            <p class="text-3xl font-bold text-gray-900">{{ stats.today_orders }}</p>
                            <p class="text-sm mt-2 flex items-center">
                                <ArrowTrendingUpIcon class="h-4 w-4 text-green-600 mr-1" />
                                <span class="text-green-600 font-medium">+12%</span>
                                <span class="text-gray-600 ml-1">vs hier</span>
                            </p>
                        </div>
                        <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                            <ShoppingBagIcon class="h-6 w-6 text-blue-600" />
                        </div>
                    </div>
                </div>
            </div>

            <!-- Revenue -->
            <div class="card">
                <div class="card-body">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-600 mb-1">Revenu (aujourd'hui)</p>
                            <p class="text-3xl font-bold text-gray-900">{{ stats.today_revenue }} DH</p>
                            <p class="text-sm mt-2 flex items-center">
                                <ArrowTrendingUpIcon class="h-4 w-4 text-green-600 mr-1" />
                                <span class="text-green-600 font-medium">+8%</span>
                                <span class="text-gray-600 ml-1">vs hier</span>
                            </p>
                        </div>
                        <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                            <CurrencyDollarIcon class="h-6 w-6 text-green-600" />
                        </div>
                    </div>
                </div>
            </div>

            <!-- Customers -->
            <div class="card">
                <div class="card-body">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-600 mb-1">Nouveaux clients</p>
                            <p class="text-3xl font-bold text-gray-900">{{ stats.new_customers }}</p>
                            <p class="text-sm mt-2 flex items-center">
                                <ArrowTrendingUpIcon class="h-4 w-4 text-green-600 mr-1" />
                                <span class="text-green-600 font-medium">+15%</span>
                                <span class="text-gray-600 ml-1">ce mois</span>
                            </p>
                        </div>
                        <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                            <UserGroupIcon class="h-6 w-6 text-purple-600" />
                        </div>
                    </div>
                </div>
            </div>

            <!-- Active Deliveries -->
            <div class="card">
                <div class="card-body">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-600 mb-1">Livraisons en cours</p>
                            <p class="text-3xl font-bold text-gray-900">{{ stats.active_deliveries }}</p>
                            <p class="text-sm mt-2">
                                <span class="text-gray-600">{{ stats.available_riders }} livreurs actifs</span>
                            </p>
                        </div>
                        <div class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center">
                            <TruckIcon class="h-6 w-6 text-orange-600" />
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid lg:grid-cols-2 gap-8">
            <!-- Recent Orders -->
            <div class="card">
                <div class="card-header">
                    <div class="flex items-center justify-between">
                        <h2 class="font-bold text-lg">Dernières commandes</h2>
                        <Link href="/admin/orders" class="text-primary-600 text-sm hover:text-primary-700">
                            Voir tout →
                        </Link>
                    </div>
                </div>
                <div class="divide-y">
                    <div
                        v-for="order in recentOrders"
                        :key="order.id"
                        class="p-4 hover:bg-gray-50 transition"
                    >
                        <div class="flex items-center justify-between mb-2">
                            <div>
                                <p class="font-semibold">#{{ order.order_number }}</p>
                                <p class="text-sm text-gray-600">{{ order.user.name }}</p>
                            </div>
                            <span :class="`status-${order.status}`">
                                {{ order.status }}
                            </span>
                        </div>
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-gray-600">{{ order.items_count }} produits</span>
                            <span class="font-bold text-primary-600">{{ order.total }} DH</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Top Products -->
            <div class="card">
                <div class="card-header">
                    <h2 class="font-bold text-lg">Produits populaires</h2>
                </div>
                <div class="p-4">
                    <div class="space-y-4">
                        <div
                            v-for="(product, index) in topProducts"
                            :key="product.id"
                            class="flex items-center space-x-4"
                        >
                            <div class="flex-shrink-0 w-8 h-8 bg-primary-100 rounded-full flex items-center justify-center">
                                <span class="text-primary-600 font-bold text-sm">{{ index + 1 }}</span>
                            </div>
                            <img
                                :src="product.image || '/images/placeholder.png'"
                                class="w-12 h-12 object-cover rounded"
                            />
                            <div class="flex-1 min-w-0">
                                <p class="font-medium truncate">{{ product.name }}</p>
                                <p class="text-sm text-gray-600">{{ product.sales_count }} ventes</p>
                            </div>
                            <div class="text-right">
                                <p class="font-bold text-gray-900">{{ product.revenue }} DH</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
