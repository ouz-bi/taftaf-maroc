<template>
    <AdminLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h1 class="text-2xl font-bold text-gray-900">
                    Commande #{{ order.order_number }}
                </h1>
                <Link
                    :href="route('admin.orders.index')"
                    class="text-primary-600 hover:text-primary-700"
                    >Retour à la liste des commandes</Link
                >
            </div>
        </template>

        <div class="container-custom">
            <div class="mb-8">
                <Link
                    :href="route('admin.orders.index')"
                    class="text-primary-600 hover:text-primary-700 mb-4 inline-block"
                    >Retour aux commandes</Link
                >
                <div
                    class="flex flex-col md:flex-row md:items-center md:justify-between"
                >
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900">
                            Commande #{{ order.order_number }}
                        </h1>
                        <p class="text-gray-600 mt-1">
                            Passée le {{ formatDate(order.created_at) }}
                        </p>
                    </div>
                    <div class="mt-4 md:mt-0">
                        <span
                            :class="`status-${order.status} text-lg px-4 py-2`"
                        >
                            {{ order.status }}
                        </span>
                    </div>
                </div>
            </div>

            <div class="grid lg:grid-cols-3 gap-8">
                <div class="lg:col-span-2">
                    <div class="card">
                        <div class="card-header">
                            <h2 class="font-bold text-lg">
                                Informations de la commande
                            </h2>
                        </div>
                        <div class="card-body">
                            <p>
                                <strong>Nom du client :</strong>
                                {{ order.user.name }}
                            </p>
                            <p>
                                <strong>Email :</strong> {{ order.user.email }}
                            </p>
                            <p v-if="order.address">
                                <strong>Adresse :</strong>
                                {{ order.address?.address_line }},
                                {{ order.address?.district }},
                                {{ order.address?.city }}
                            </p>
                            <p v-if="order.address">
                                <strong>Téléphone :</strong>
                                {{ order.address.phone }}
                            </p>
                            <p><strong>Statut :</strong> {{ order.status }}</p>
                            <p>
                                <strong>Date de création :</strong>
                                {{ formatDate(order.created_at) }}
                            </p>
                        </div>
                    </div>
                </div>

                <div class="lg:col-span-1">
                    <div class="card">
                        <div class="card-header">
                            <h2 class="font-bold text-lg">
                                Suivi de la commande
                            </h2>
                        </div>
                        <div class="card-body">
                            <div class="space-y-3 mb-4 pb-4 border-b">
                                <div class="flex justify-between text-gray-700">
                                    <span>Sous-total</span>
                                    <span class="font-medium"
                                        >{{
                                            order.subtotal.toFixed(2)
                                        }}
                                        DH</span
                                    >
                                </div>
                                <div class="flex justify-between text-gray-700">
                                    <span>Livraison</span>
                                    <span class="font-medium"
                                        >{{
                                            order.delivery_fee.toFixed(2)
                                        }}
                                        DH</span
                                    >
                                </div>
                                <div
                                    v-if="order.discount > 0"
                                    class="flex justify-between text-green-600"
                                >
                                    <span>Remise</span>
                                    <span class="font-medium"
                                        >-{{
                                            order.discount.toFixed(2)
                                        }}
                                        DH</span
                                    >
                                </div>
                            </div>
                            <div class="flex justify-between items-baseline">
                                <span class="text-lg font-bold">Total</span>
                                <span
                                    class="text-2xl font-bold text-primary-600"
                                >
                                    {{ order.total.toFixed(2) }} DH
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h2 class="font-bold text-lg">Produits de la commande</h2>
                </div>
                <div class="card-body">
                    <div
                        v-for="item in order.items"
                        :key="item.id"
                        class="flex items-center mb-4"
                    >
                        <img
                            :src="item.product.image || '/images/placeholder.png'"
                            class="w-24 h-24 object-cover mr-4"
                            alt="Product Image"
                        />
                        <div class="flex-1">
                            <p>
                                <strong>{{ item.product.name }}</strong>
                            </p>
                            <p>{{ item.product.description }}</p>
                            <p>
                                <strong>Prix :</strong>
                                {{ item.product.price }} DH
                            </p>
                            <p>
                                <strong>Quantité :</strong> {{ item.quantity }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>

<script>
import AdminLayout from "@/Layouts/AdminLayout.vue";
import { Link } from "@inertiajs/vue3";

export default {
    components: {
        AdminLayout,
        Link,
    },
    props: {
        order: {
            type: Object,
            required: true,
        },
    },
    methods: {
        formatDate(date) {
            return new Date(date).toLocaleString("fr-FR", {
                day: "2-digit",
                month: "short",
                hour: "2-digit",
                minute: "2-digit",
            });
        },
    },
};
</script>

<style scoped>
.status-processing {
    background-color: #e2f2f9;
    color: #4a8df8;
    border-radius: 4px;
    padding: 4px 8px;
}

.status-pending {
    background-color: #fef2f2;
    color: #e53e3e;
    border-radius: 4px;
    padding: 4px 8px;
}

.status-shipped {
    background-color: #d1fcfb;
    color: #00b2a9;
    border-radius: 4px;
    padding: 4px 8px;
}

.status-delivered {
    background-color: #d8f4d8;
    color: #4caf00;
    border-radius: 4px;
    padding: 4px 8px;
}

.status-canceled {
    background-color: #fef2f2;
    color: #e53e3e;
    border-radius: 4px;
    padding: 4px 8px;
}
</style>
