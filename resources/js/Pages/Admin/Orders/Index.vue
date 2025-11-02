<script setup>
import { ref } from 'vue'
import { router, Link } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { MagnifyingGlassIcon, FunnelIcon, EyeIcon } from '@heroicons/vue/24/outline'

const props = defineProps({
    orders: Object,
    filters: Object,
})

const searchQuery = ref(props.filters.search || '')
const statusFilter = ref(props.filters.status || '')

const applyFilters = () => {
    router.get('/admin/orders', {
        search: searchQuery.value,
        status: statusFilter.value,
    }, {
        preserveState: true,
        preserveScroll: true,
    })
}

const updateOrderStatus = (orderId, newStatus) => {
    if (confirm('Confirmer le changement de statut ?')) {
        router.patch(`/admin/orders/${orderId}/status`, {
            status: newStatus
        }, {
            preserveScroll: true,
        })
    }
}

const formatDate = (date) => {
    return new Date(date).toLocaleString('fr-FR', {
        day: '2-digit',
        month: 'short',
        hour: '2-digit',
        minute: '2-digit'
    })
}
</script>

<template>
    <AdminLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h1 class="text-2xl font-bold text-gray-900">Gestion des Commandes</h1>
                <span class="badge-primary">{{ orders.total }} commandes</span>
            </div>
        </template>

        <!-- Filters -->
        <div class="card mb-6">
            <div class="card-body">
                <div class="grid md:grid-cols-3 gap-4">
                    <!-- Search -->
                    <div>
                        <label class="label">Rechercher</label>
                        <div class="relative">
                            <input
                                v-model="searchQuery"
                                @input="applyFilters"
                                type="text"
                                placeholder="N° commande, client..."
                                class="input-field pl-10"
                            />
                            <MagnifyingGlassIcon class="absolute left-3 top-3 h-5 w-5 text-gray-400" />
                        </div>
                    </div>

                    <!-- Status Filter -->
                    <div>
                        <label class="label">Statut</label>
                        <select
                            v-model="statusFilter"
                            @change="applyFilters"
                            class="input-field"
                        >
                            <option value="">Tous les statuts</option>
                            <option value="pending">En attente</option>
                            <option value="confirmed">Confirmée</option>
                            <option value="preparing">En préparation</option>
                            <option value="ready">Prête</option>
                            <option value="on_the_way">En route</option>
                            <option value="delivered">Livrée</option>
                            <option value="cancelled">Annulée</option>
                        </select>
                    </div>

                    <!-- Date Filter -->
                    <div>
                        <label class="label">Date</label>
                        <select class="input-field">
                            <option value="">Toutes les dates</option>
                            <option value="today">Aujourd'hui</option>
                            <option value="yesterday">Hier</option>
                            <option value="week">Cette semaine</option>
                            <option value="month">Ce mois</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <!-- Orders Table -->
        <div class="card overflow-hidden">
            <div class="overflow-x-auto">
                <table class="table">
                    <thead class="table-header">
                        <tr>
                            <th class="table-th">N° Commande</th>
                            <th class="table-th">Client</th>
                            <th class="table-th">Date</th>
                            <th class="table-th">Montant</th>
                            <th class="table-th">Paiement</th>
                            <th class="table-th">Statut</th>
                            <th class="table-th">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        <tr
                            v-for="order in orders.data"
                            :key="order.id"
                            class="hover:bg-gray-50"
                        >
                            <td class="table-td">
                                <Link
                                    :href="`/admin/orders/${order.id}`"
                                    class="font-medium text-primary-600 hover:text-primary-700"
                                >
                                    #{{ order.order_number }}
                                </Link>
                            </td>
                            <td class="table-td">
                                <div>
                                    <p class="font-medium">{{ order.user.name }}</p>
                                    <p class="text-xs text-gray-500">{{ order.user.email }}</p>
                                </div>
                            </td>
                            <td class="table-td">
                                {{ formatDate(order.created_at) }}
                            </td>
                            <td class="table-td">
                                <span class="font-bold">{{ order.total.toFixed(2) }} DH</span>
                            </td>
                            <td class="table-td">
                                <div>
                                    <span class="text-sm text-gray-600">{{ order.payment_method }}</span>
                                    <br>
                                    <span :class="`badge text-xs ${
                                        order.payment_status === 'paid' ? 'badge-success' :
                                        order.payment_status === 'failed' ? 'badge-danger' :
                                        'badge-warning'
                                    }`">
                                        {{ order.payment_status }}
                                    </span>
                                </div>
                            </td>
                            <td class="table-td">
                                <select
                                    :value="order.status"
                                    @change="updateOrderStatus(order.id, $event.target.value)"
                                    :class="`status-${order.status} text-xs px-2 py-1 rounded border-0`"
                                >
                                    <option value="pending">En attente</option>
                                    <option value="confirmed">Confirmée</option>
                                    <option value="preparing">En préparation</option>
                                    <option value="ready">Prête</option>
                                    <option value="on_the_way">En route</option>
                                    <option value="delivered">Livrée</option>
                                    <option value="cancelled">Annulée</option>
                                </select>
                            </td>
                            <td class="table-td">
                                <Link
                                    :href="`/admin/orders/${order.id}`"
                                    class="btn-secondary btn-sm"
                                >
                                    <EyeIcon class="h-4 w-4" />
                                </Link>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="border-t p-4">
                <nav class="flex items-center justify-between">
                    <div class="text-sm text-gray-600">
                        Affichage {{ orders.from }} - {{ orders.to }} sur {{ orders.total }}
                    </div>
                    <div class="flex space-x-2">
                        <button
                            v-for="link in orders.links"
                            :key="link.label"
                            @click="router.visit(link.url)"
                            :disabled="!link.url"
                            :class="[
                                'px-3 py-1 text-sm rounded',
                                link.active
                                    ? 'bg-primary-600 text-white'
                                    : 'bg-white border hover:bg-gray-50',
                                !link.url && 'opacity-50 cursor-not-allowed'
                            ]"
                            v-html="link.label"
                        />
                    </div>
                </nav>
            </div>
        </div>
    </AdminLayout>
</template>
