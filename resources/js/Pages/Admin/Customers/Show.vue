<script setup>
import { Link } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import {
    ArrowLeftIcon,
    ShoppingBagIcon,
    MapPinIcon,
    CurrencyDollarIcon
} from '@heroicons/vue/24/outline'

const props = defineProps({
    customer: Object,
    stats: Object,
})

const formatDate = (date) => {
    return new Date(date).toLocaleString('fr-FR', {
        day: '2-digit',
        month: 'short',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    })
}
</script>

<template>
    <AdminLayout>
        <template #header>
            <div class="flex items-center space-x-4">
                <Link href="/admin/customers" class="text-gray-600 hover:text-gray-900">
                    <ArrowLeftIcon class="h-6 w-6" />
                </Link>
                <h1 class="text-2xl font-bold text-gray-900">{{ customer.name }}</h1>
            </div>
        </template>

        <div class="grid lg:grid-cols-3 gap-6">
            <!-- Info Client -->
            <div class="lg:col-span-1">
                <div class="card">
                    <div class="card-header">
                        <h2 class="font-bold">Informations</h2>
                    </div>
                    <div class="card-body space-y-4">
                        <div class="flex items-center justify-center">
                            <div class="w-24 h-24 bg-primary-100 rounded-full flex items-center justify-center">
                                <span class="text-4xl font-bold text-primary-600">
                                    {{ customer.name[0].toUpperCase() }}
                                </span>
                            </div>
                        </div>

                        <div class="text-center">
                            <p class="font-semibold text-lg">{{ customer.name }}</p>
                            <p class="text-sm text-gray-600">{{ customer.email }}</p>
                        </div>

                        <div class="border-t pt-4 space-y-2">
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">Client depuis</span>
                                <span class="font-medium">{{ formatDate(customer.created_at) }}</span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">Dernière commande</span>
                                <span class="font-medium">
                                    {{ customer.orders[0] ? formatDate(customer.orders[0].created_at) : 'Aucune' }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Stats -->
                <div class="card mt-6">
                    <div class="card-header">
                        <h2 class="font-bold">Statistiques</h2>
                    </div>
                    <div class="card-body space-y-4">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-3">
                                <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                                    <ShoppingBagIcon class="h-5 w-5 text-blue-600" />
                                </div>
                                <div>
                                    <p class="text-2xl font-bold">{{ stats.total_orders }}</p>
                                    <p class="text-xs text-gray-600">Commandes</p>
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-3">
                                <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                                    <CurrencyDollarIcon class="h-5 w-5 text-green-600" />
                                </div>
                                <div>
                                    <p class="text-2xl font-bold">{{ stats.total_spent?.toFixed(2) }} DH</p>
                                    <p class="text-xs text-gray-600">Total dépensé</p>
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-3">
                                <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center">
                                    <span class="text-purple-600 font-bold">€</span>
                                </div>
                                <div>
                                    <p class="text-2xl font-bold">{{ stats.average_order?.toFixed(2) }} DH</p>
                                    <p class="text-xs text-gray-600">Panier moyen</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Adresses -->
                <div class="card mt-6">
                    <div class="card-header">
                        <h2 class="font-bold flex items-center">
                            <MapPinIcon class="h-5 w-5 mr-2" />
                            Adresses
                        </h2>
                    </div>
                    <div class="card-body">
                        <div v-if="customer.addresses.length > 0" class="space-y-3">
                            <div
                                v-for="address in customer.addresses"
                                :key="address.id"
                                class="p-3 bg-gray-50 rounded-lg"
                            >
                                <p class="font-semibold text-sm">{{ address.label }}</p>
                                <p class="text-xs text-gray-600 mt-1">{{ address.address_line }}</p>
                                <p class="text-xs text-gray-600">{{ address.city }}</p>
                            </div>
                        </div>
                        <p v-else class="text-sm text-gray-500 text-center py-4">
                            Aucune adresse enregistrée
                        </p>
                    </div>
                </div>
            </div>

            <!-- Historique Commandes -->
            <div class="lg:col-span-2">
                <div class="card">
                    <div class="card-header">
                        <h2 class="font-bold">Historique des commandes</h2>
                    </div>
                    <div class="divide-y">
                        <div
                            v-for="order in customer.orders"
                            :key="order.id"
                            class="p-4 hover:bg-gray-50"
                        >
                            <div class="flex items-center justify-between mb-2">
                                <div>
                                    <Link
                                        :href="`/admin/orders/${order.id}`"
                                        class="font-semibold text-primary-600 hover:text-primary-700"
                                    >
                                        #{{ order.order_number }}
                                    </Link>
                                    <p class="text-sm text-gray-600">{{ formatDate(order.created_at) }}</p>
                                </div>
                                <div class="text-right">
                                    <p class="font-bold text-lg">{{ order.total.toFixed(2) }} DH</p>
                                    <span :class="`status-${order.status} text-xs`">
                                        {{ order.status }}
                                    </span>
                                </div>
                            </div>
                            <div class="text-sm text-gray-600">
                                {{ order.items?.length || 0 }} produit(s) • {{ order.payment_method }}
                            </div>
                        </div>

                        <div v-if="customer.orders.length === 0" class="p-8 text-center text-gray-500">
                            Aucune commande pour le moment
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
