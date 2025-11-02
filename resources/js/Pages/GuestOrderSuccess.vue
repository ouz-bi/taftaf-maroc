<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import { Link } from '@inertiajs/vue3'
import { CheckCircleIcon } from '@heroicons/vue/24/outline'

const props = defineProps({
    order: Object,
})
</script>

<template>
    <AppLayout>
        <div class="min-h-screen bg-gray-50 py-16">
            <div class="max-w-2xl mx-auto px-4">
                <div class="text-center mb-8">
                    <CheckCircleIcon class="h-24 w-24 text-green-600 mx-auto mb-4" />
                    <h1 class="text-3xl font-bold text-gray-900 mb-2">
                        Commande confirmée ! 🎉
                    </h1>
                    <p class="text-gray-600">
                        Votre commande #{{ order.order_number }} a été enregistrée
                    </p>
                </div>

                <div class="card mb-6">
                    <div class="card-body">
                        <h2 class="font-bold text-lg mb-4">Détails de livraison</h2>
                        <div class="space-y-2 text-sm">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Adresse</span>
                                <span class="font-medium">{{ order.delivery_address }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Montant total</span>
                                <span class="font-bold text-lg text-primary-600">{{ order.total }} DH</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-green-50 border border-green-200 rounded-lg p-6 mb-6">
                    <h3 class="font-bold text-green-900 mb-2">⚡ Livraison en cours</h3>
                    <p class="text-green-800 mb-4">
                        Votre commande sera livrée dans environ 20 minutes !
                    </p>
                    <Link
                        :href="`/guest/track/${order.order_number}`"
                        class="btn-primary inline-block"
                    >
                        📍 Suivre ma commande en temps réel
                    </Link>
                </div>

                <div class="text-center space-y-3">
                    <Link href="/products" class="btn-secondary inline-block">
                        Continuer mes achats
                    </Link>
                    <p class="text-sm text-gray-600">
                        Numéro de suivi : <strong>{{ order.order_number }}</strong>
                    </p>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
