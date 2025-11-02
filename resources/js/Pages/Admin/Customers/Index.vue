<script setup>
import { ref } from 'vue'
import { router, Link } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { MagnifyingGlassIcon, EyeIcon, EnvelopeIcon, PhoneIcon } from '@heroicons/vue/24/outline'

const props = defineProps({
    customers: Object,
})

const searchQuery = ref('')

const search = () => {
    router.get('/admin/customers', {
        search: searchQuery.value
    }, {
        preserveState: true,
        preserveScroll: true,
    })
}

const formatDate = (date) => {
    return new Date(date).toLocaleDateString('fr-FR', {
        day: '2-digit',
        month: 'short',
        year: 'numeric'
    })
}

const formatMoney = (amount) => {
    return new Intl.NumberFormat('fr-FR', {
        style: 'currency',
        currency: 'MAD',
        minimumFractionDigits: 2,
    }).format(amount || 0);
};

</script>

<template>
    <AdminLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h1 class="text-2xl font-bold text-gray-900">Gestion des Clients</h1>
                <span class="badge-primary">{{ customers.total }} clients</span>
            </div>
        </template>

        <!-- Search -->
        <div class="card mb-6">
            <div class="card-body">
                <div class="relative max-w-md">
                    <input
                        v-model="searchQuery"
                        @input="search"
                        type="text"
                        placeholder="Rechercher un client..."
                        class="input-field pl-10"
                    />
                    <MagnifyingGlassIcon class="absolute left-3 top-3 h-5 w-5 text-gray-400" />
                </div>
            </div>
        </div>

        <!-- Customers Table -->
        <div class="card overflow-hidden">
            <div class="overflow-x-auto">
                <table class="table">
                    <thead class="table-header">
                        <tr>
                            <th class="table-th">Client</th>
                            <th class="table-th">Contact</th>
                            <th class="table-th">Inscrit le</th>
                            <th class="table-th">Commandes</th>
                            <th class="table-th">Total dépensé</th>
                            <th class="table-th">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        <tr
                            v-for="customer in customers.data"
                            :key="customer.id"
                            class="hover:bg-gray-50"
                        >
                            <td class="table-td">
                                <div class="flex items-center space-x-3">
                                    <div class="w-10 h-10 bg-primary-100 rounded-full flex items-center justify-center">
                                        <span class="font-bold text-primary-600">
                                            {{ customer.name[0].toUpperCase() }}
                                        </span>
                                    </div>
                                    <div>
                                        <p class="font-semibold">{{ customer.name }}</p>
                                        <p class="text-xs text-gray-500">ID: {{ customer.id }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="table-td">
                                <div class="space-y-1">
                                    <div class="flex items-center text-sm text-gray-600">
                                        <EnvelopeIcon class="h-4 w-4 mr-2" />
                                        {{ customer.email }}
                                    </div>
                                    <div v-if="customer.phone" class="flex items-center text-sm text-gray-600">
                                        <PhoneIcon class="h-4 w-4 mr-2" />
                                        {{ customer.phone }}
                                    </div>
                                </div>
                            </td>
                            <td class="table-td">
                                {{ formatDate(customer.created_at) }}
                            </td>
                            <td class="table-td">
                                <span class="font-semibold">{{ customer.orders_count }}</span>
                                <span class="text-sm text-gray-500"> commandes</span>
                            </td>
                            <td class="table-td">
                                <span class="font-bold text-primary-600">
                                    {{ formatMoney(customer.total_spent) }}
                                </span>
                            </td>
                            <td class="table-td">
                                <Link
                                    :href="`/admin/customers/${customer.id}`"
                                    class="btn-secondary btn-sm flex items-center space-x-1"
                                >
                                    <EyeIcon class="h-4 w-4" />
                                    <span>Voir</span>
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
                        Affichage {{ customers.from }} - {{ customers.to }} sur {{ customers.total }}
                    </div>
                    <div class="flex space-x-2">
                        <button
                            v-for="link in customers.links"
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
