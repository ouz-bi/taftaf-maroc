<script setup>
import { computed } from 'vue'
import { Link } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import {
    CheckCircleIcon,
    TruckIcon,
    ClockIcon,
    XCircleIcon,
    MapPinIcon,
    PhoneIcon,
    CreditCardIcon
} from '@heroicons/vue/24/outline'

const props = defineProps({
    order: Object,
})

const statusConfig = {
    pending: {
        label: 'En attente',
        color: 'yellow',
        icon: ClockIcon,
    },
    confirmed: {
        label: 'Confirmée',
        color: 'blue',
        icon: CheckCircleIcon,
    },
    preparing: {
        label: 'En préparation',
        color: 'purple',
        icon: ClockIcon,
    },
    ready: {
        label: 'Prête',
        color: 'indigo',
        icon: CheckCircleIcon,
    },
    picked_up: {
        label: 'Récupérée',
        color: 'orange',
        icon: TruckIcon,
    },
    on_the_way: {
        label: 'En route',
        color: 'orange',
        icon: TruckIcon,
    },
    delivered: {
        label: 'Livrée',
        color: 'green',
        icon: CheckCircleIcon,
    },
    cancelled: {
        label: 'Annulée',
        color: 'red',
        icon: XCircleIcon,
    },
}

const currentStatus = computed(() => statusConfig[props.order.status])

const paymentMethodLabel = {
    cash: 'Espèces à la livraison',
    card: 'Carte bancaire',
    cmi: 'CMI (Maroc)',
    cashplus: 'CashPlus',
}

const canCancel = computed(() => {
    return ['pending', 'confirmed'].includes(props.order.status)
})

const formatDate = (date) => {
    return new Date(date).toLocaleString('fr-FR', {
        day: '2-digit',
        month: 'long',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    })
}
</script>

<template>
    <AppLayout>
        <div class="bg-gray-50 min-h-screen py-8">
            <div class="container-custom">
                <!-- Header -->
                <div class="mb-8">
                    <Link href="/orders" class="text-primary-600 hover:text-primary-700 mb-4 inline-block">
                        ← Retour aux commandes
                    </Link>
                    <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                        <div>
                            <h1 class="text-3xl font-bold text-gray-900">
                                Commande #{{ order.order_number }}
                            </h1>
                            <p class="text-gray-600 mt-1">
                                Passée le {{ formatDate(order.created_at) }}
                            </p>
                        </div>
                        <div class="mt-4 md:mt-0">
                            <span :class="`status-${order.status} text-lg px-4 py-2`">
                                {{ currentStatus.label }}
                            </span>
                        </div>
                    </div>
                </div>

                <div class="grid lg:grid-cols-3 gap-8">
                    <!-- Main Content -->
                    <div class="lg:col-span-2 space-y-6">
                        <!-- Order Timeline -->
                        <div class="card">
                            <div class="card-header">
                                <h2 class="font-bold text-lg">Suivi de commande</h2>
                            </div>
                            <div class="card-body">
                                <Link
                                    :href="`/orders/${order.id}/track`"
                                    class="btn-primary w-full mb-4"
                                >
                                    <TruckIcon class="h-5 w-5 mr-2" />
                                    Suivre ma commande en temps réel
                                </Link>

                                <!-- Timeline Steps -->
                                <div class="relative">
                                    <div class="absolute left-4 top-0 bottom-0 w-0.5 bg-gray-200" />

                                    <!-- Step 1: Pending -->
                                    <div class="relative flex items-start mb-6">
                                        <div :class="[
                                            'flex-shrink-0 h-8 w-8 rounded-full flex items-center justify-center z-10',
                                            ['pending', 'confirmed', 'preparing', 'ready', 'picked_up', 'on_the_way', 'delivered'].includes(order.status)
                                                ? 'bg-primary-600 text-white'
                                                : 'bg-gray-300 text-gray-600'
                                        ]">
                                            <CheckCircleIcon class="h-5 w-5" />
                                        </div>
                                        <div class="ml-4 flex-1">
                                            <p class="font-semibold">Commande reçue</p>
                                            <p class="text-sm text-gray-600">{{ formatDate(order.created_at) }}</p>
                                        </div>
                                    </div>

                                    <!-- Step 2: Confirmed -->
                                    <div class="relative flex items-start mb-6">
                                        <div :class="[
                                            'flex-shrink-0 h-8 w-8 rounded-full flex items-center justify-center z-10',
                                            ['confirmed', 'preparing', 'ready', 'picked_up', 'on_the_way', 'delivered'].includes(order.status)
                                                ? 'bg-primary-600 text-white'
                                                : 'bg-gray-300 text-gray-600'
                                        ]">
                                            <CheckCircleIcon class="h-5 w-5" />
                                        </div>
                                        <div class="ml-4 flex-1">
                                            <p class="font-semibold">Commande confirmée</p>
                                            <p v-if="order.confirmed_at" class="text-sm text-gray-600">
                                                {{ formatDate(order.confirmed_at) }}
                                            </p>
                                        </div>
                                    </div>

                                    <!-- Step 3: Preparing -->
                                    <div class="relative flex items-start mb-6">
                                        <div :class="[
                                            'flex-shrink-0 h-8 w-8 rounded-full flex items-center justify-center z-10',
                                            ['preparing', 'ready', 'picked_up', 'on_the_way', 'delivered'].includes(order.status)
                                                ? 'bg-primary-600 text-white'
                                                : 'bg-gray-300 text-gray-600'
                                        ]">
                                            <ClockIcon class="h-5 w-5" />
                                        </div>
                                        <div class="ml-4 flex-1">
                                            <p class="font-semibold">En préparation</p>
                                            <p class="text-sm text-gray-600">Votre commande est en cours de préparation</p>
                                        </div>
                                    </div>

                                    <!-- Step 4: On the way -->
                                    <div class="relative flex items-start mb-6">
                                        <div :class="[
                                            'flex-shrink-0 h-8 w-8 rounded-full flex items-center justify-center z-10',
                                            ['on_the_way', 'delivered'].includes(order.status)
                                                ? 'bg-primary-600 text-white'
                                                : 'bg-gray-300 text-gray-600'
                                        ]">
                                            <TruckIcon class="h-5 w-5" />
                                        </div>
                                        <div class="ml-4 flex-1">
                                            <p class="font-semibold">En route</p>
                                            <p v-if="order.rider" class="text-sm text-gray-600">
                                                Livrée par {{ order.rider.user.name }}
                                            </p>
                                        </div>
                                    </div>

                                    <!-- Step 5: Delivered -->
                                    <div class="relative flex items-start">
                                        <div :class="[
                                            'flex-shrink-0 h-8 w-8 rounded-full flex items-center justify-center z-10',
                                            order.status === 'delivered'
                                                ? 'bg-green-600 text-white'
                                                : 'bg-gray-300 text-gray-600'
                                        ]">
                                            <CheckCircleIcon class="h-5 w-5" />
                                        </div>
                                        <div class="ml-4 flex-1">
                                            <p class="font-semibold">Livrée</p>
                                            <p v-if="order.delivered_at" class="text-sm text-gray-600">
                                                {{ formatDate(order.delivered_at) }}
                                            </p>
                                            <p v-if="order.delivery_time" class="text-sm text-green-600 font-medium">
                                                ⚡ Livrée en {{ order.delivery_time }} minutes
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Order Items -->
                        <div class="card">
                            <div class="card-header">
                                <h2 class="font-bold text-lg">Produits commandés</h2>
                            </div>
                            <div class="card-body">
                                <div class="space-y-4">
                                    <div
                                        v-for="item in order.items"
                                        :key="item.id"
                                        class="flex items-center space-x-4 pb-4 border-b last:border-b-0"
                                    >
                                        <img
                                            :src="item.product.image || '/images/placeholder.jpg'"
                                            :alt="item.product_name"
                                            class="w-20 h-20 object-cover rounded-lg"
                                        />
                                        <div class="flex-1">
                                            <Link
                                                :href="`/products/${item.product.slug}`"
                                                class="font-semibold hover:text-primary-600"
                                            >
                                                {{ item.product_name }}
                                            </Link>
                                            <p class="text-sm text-gray-600">
                                                Quantité : {{ item.quantity }}
                                            </p>
                                            <p class="text-sm text-gray-600">
                                                {{ item.price }} DH × {{ item.quantity }}
                                            </p>
                                        </div>
                                        <div class="text-right">
                                            <p class="font-bold text-lg">
                                                {{ item.subtotal.toFixed(2) }} DH
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Cancel Order -->
                        <div v-if="canCancel" class="card border-red-200">
                            <div class="card-body">
                                <h3 class="font-semibold text-red-600 mb-2">Annuler la commande</h3>
                                <p class="text-sm text-gray-600 mb-4">
                                    Vous pouvez annuler votre commande tant qu'elle n'a pas été préparée
                                </p>
                                <button class="btn-danger">
                                    Annuler cette commande
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Sidebar -->
                    <div class="lg:col-span-1 space-y-6">
                        <!-- Order Summary -->
                        <div class="card">
                            <div class="card-header">
                                <h2 class="font-bold">Récapitulatif</h2>
                            </div>
                            <div class="card-body">
                                <div class="space-y-3 mb-4 pb-4 border-b">
                                    <div class="flex justify-between text-gray-700">
                                        <span>Sous-total</span>
                                        <span class="font-medium">{{ order.subtotal.toFixed(2) }} DH</span>
                                    </div>
                                    <div class="flex justify-between text-gray-700">
                                        <span>Livraison</span>
                                        <span class="font-medium">{{ order.delivery_fee.toFixed(2) }} DH</span>
                                    </div>
                                    <div v-if="order.discount > 0" class="flex justify-between text-green-600">
                                        <span>Remise</span>
                                        <span class="font-medium">-{{ order.discount.toFixed(2) }} DH</span>
                                    </div>
                                </div>
                                <div class="flex justify-between items-baseline">
                                    <span class="text-lg font-bold">Total</span>
                                    <span class="text-2xl font-bold text-primary-600">
                                        {{ order.total.toFixed(2) }} DH
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Delivery Address -->
                        <div class="card">
                            <div class="card-header">
                                <h2 class="font-bold flex items-center">
                                    <MapPinIcon class="h-5 w-5 mr-2 text-primary-600" />
                                    Adresse de livraison
                                </h2>
                            </div>
                            <div class="card-body">
                                <p class="font-semibold mb-1">{{ order.address.label }}</p>
                                <p class="text-sm text-gray-700 mb-1">
                                    {{ order.address.full_name }}
                                </p>
                                <p class="text-sm text-gray-600">
                                    {{ order.address.address_line }}
                                </p>
                                <p class="text-sm text-gray-600">
                                    {{ order.address.district }}, {{ order.address.city }}
                                </p>
                                <div class="flex items-center mt-3 text-sm text-gray-600">
                                    <PhoneIcon class="h-4 w-4 mr-2" />
                                    {{ order.address.phone }}
                                </div>
                                <p v-if="order.address.instructions" class="mt-3 text-sm bg-blue-50 p-3 rounded">
                                    📝 {{ order.address.instructions }}
                                </p>
                            </div>
                        </div>

                        <!-- Payment Method -->
                        <div class="card">
                            <div class="card-header">
                                <h2 class="font-bold flex items-center">
                                    <CreditCardIcon class="h-5 w-5 mr-2 text-primary-600" />
                                    Paiement
                                </h2>
                            </div>
                            <div class="card-body">
                                <p class="font-semibold mb-1">
                                    {{ paymentMethodLabel[order.payment_method] }}
                                </p>
                                <span :class="[
                                    'badge mt-2',
                                    order.payment_status === 'paid' ? 'badge-success' :
                                    order.payment_status === 'failed' ? 'badge-danger' :
                                    'badge-warning'
                                ]">
                                    {{ order.payment_status === 'paid' ? 'Payé' :
                                       order.payment_status === 'failed' ? 'Échoué' :
                                       'En attente' }}
                                </span>
                            </div>
                        </div>

                        <!-- Support -->
                        <div class="card bg-primary-50 border-primary-200">
                            <div class="card-body">
                                <h3 class="font-bold mb-2">Besoin d'aide ?</h3>
                                <p class="text-sm text-gray-700 mb-4">
                                    Notre équipe est là pour vous aider
                                </p>
                                <Link href="/contact" class="btn-primary w-full text-center block">
                                    Contacter le support
                                </Link>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
