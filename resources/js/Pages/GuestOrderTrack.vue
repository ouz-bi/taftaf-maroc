<script setup>
import { ref, onMounted, onUnmounted } from 'vue'
import AppLayout from '@/Layouts/AppLayout.vue'
import { TruckIcon, MapPinIcon, PhoneIcon, ClockIcon } from '@heroicons/vue/24/outline'

const props = defineProps({
    order: Object,
})

// Simulation de position du livreur (à remplacer par vraies données)
const riderPosition = ref({
    lat: 33.5731,
    lng: -7.5898
})

// Timer pour le temps estimé
const estimatedTime = ref(15) // minutes

// Refresh automatique (à remplacer par WebSocket/Pusher)
let intervalId = null

onMounted(() => {
    // Simuler le rafraîchissement toutes les 10 secondes
    intervalId = setInterval(() => {
        // Ici vous feriez un appel API pour obtenir la position réelle
        console.log('Refreshing rider position...')
    }, 10000)
})

onUnmounted(() => {
    if (intervalId) {
        clearInterval(intervalId)
    }
})
</script>

<template>
    <AppLayout>
        <div class="bg-gray-50 min-h-screen">
            <!-- Map Placeholder -->
            <div class="relative h-96 bg-gray-200">
                <!-- Remplacer par Google Maps ou Leaflet -->
                <div class="absolute inset-0 flex items-center justify-center">
                    <div class="text-center">
                        <TruckIcon class="h-16 w-16 text-gray-400 mx-auto mb-4" />
                        <p class="text-gray-600">Carte interactive du suivi</p>
                        <p class="text-sm text-gray-500">Intégration Google Maps à venir</p>
                    </div>
                </div>

                <!-- Estimated Time Badge -->
                <div class="absolute top-4 left-1/2 transform -translate-x-1/2 bg-white rounded-full shadow-lg px-6 py-3">
                    <div class="flex items-center space-x-2">
                        <ClockIcon class="h-5 w-5 text-primary-600" />
                        <span class="font-bold text-lg">{{ estimatedTime }} min</span>
                    </div>
                    <p class="text-xs text-gray-600 text-center">Temps estimé</p>
                </div>
            </div>

            <!-- Order Info -->
            <div class="container-custom py-6">
                <div class="grid md:grid-cols-2 gap-6">
                    <!-- Rider Info -->
                    <div v-if="order.rider" class="card">
                        <div class="card-body">
                            <h3 class="font-bold text-lg mb-4 flex items-center">
                                <TruckIcon class="h-6 w-6 mr-2 text-primary-600" />
                                Votre livreur
                            </h3>
                            <div class="flex items-center space-x-4">
                                <div class="w-16 h-16 bg-gray-300 rounded-full flex items-center justify-center text-2xl">
                                    👤
                                </div>
                                <div class="flex-1">
                                    <p class="font-semibold text-lg">{{ order.rider.user.name }}</p>
                                    <p class="text-sm text-gray-600">{{ order.rider.vehicle_type }}</p>
                                    <div class="flex items-center mt-1">
                                        <span class="text-yellow-500">⭐</span>
                                        <span class="text-sm font-medium ml-1">{{ order.rider.rating }}</span>
                                        <span class="text-xs text-gray-500 ml-1">
                                            ({{ order.rider.total_deliveries }} livraisons)
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <button class="btn-outline w-full mt-4 flex items-center justify-center space-x-2">
                                <PhoneIcon class="h-5 w-5" />
                                <span>Appeler le livreur</span>
                            </button>
                        </div>
                    </div>

                    <!-- Delivery Address -->
                    <div class="card">
                        <div class="card-body">
                            <h3 class="font-bold text-lg mb-4 flex items-center">
                                <MapPinIcon class="h-6 w-6 mr-2 text-primary-600" />
                                Point de livraison
                            </h3>
                            <div class="space-y-2">
                                <p class="font-semibold">{{ order.delivery_address }}</p>
                                <p class="text-sm text-gray-700">{{ order.customer_name }}</p>
                                <p class="text-sm text-gray-600">{{ order.delivery_address }}</p>
                                <div class="flex items-center text-sm text-gray-600 pt-2">
                                    <PhoneIcon class="h-4 w-4 mr-2" />
                                    {{ order.customer_phone }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Order Details -->
                    <div class="card md:col-span-2">
                        <div class="card-body">
                            <h3 class="font-bold text-lg mb-4">Détails de la commande #{{ order.order_number }}</h3>
                            <div class="grid md:grid-cols-2 gap-6">
                                <div>
                                    <div class="space-y-3">
                                        <div
                                            v-for="item in order.items"
                                            :key="item.id"
                                            class="flex items-center space-x-3"
                                        >
                                            <img
                                                :src="item.product.image ? `/storage/${item.product.image}` : '/images/placeholder.png'"
                                                class="w-12 h-12 object-cover rounded"
                                            />
                                            <div class="flex-1">
                                                <p class="font-medium text-sm">{{ item.product_name }}</p>
                                                <p class="text-xs text-gray-600">Qté: {{ item.quantity }}</p>
                                            </div>
                                            <p class="font-semibold text-sm">{{ item.subtotal.toFixed(2) }} DH</p>
                                        </div>
                                    </div>
                                </div>
                                <div>
                                    <div class="bg-gray-50 p-4 rounded-lg">
                                        <div class="space-y-2 text-sm">
                                            <div class="flex justify-between">
                                                <span>Sous-total</span>
                                                <span class="font-medium">{{ order.subtotal.toFixed(2) }} DH</span>
                                            </div>
                                            <div class="flex justify-between">
                                                <span>Livraison</span>
                                                <span class="font-medium">{{ order.delivery_fee.toFixed(2) }} DH</span>
                                            </div>
                                            <div class="flex justify-between pt-2 border-t font-bold">
                                                <span>Total</span>
                                                <span class="text-primary-600">{{ order.total.toFixed(2) }} DH</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Status Updates (simulé) -->
                <div class="card mt-6">
                    <div class="card-header">
                        <h3 class="font-bold">Historique</h3>
                    </div>
                    <div class="card-body">
                        <div class="space-y-4">
                            <div class="flex items-start space-x-3">
                                <div class="flex-shrink-0 w-2 h-2 bg-primary-600 rounded-full mt-2" />
                                <div>
                                    <p class="font-medium">Livreur en route vers votre adresse</p>
                                    <p class="text-sm text-gray-600">Il y a 5 minutes</p>
                                </div>
                            </div>
                            <div class="flex items-start space-x-3">
                                <div class="flex-shrink-0 w-2 h-2 bg-gray-300 rounded-full mt-2" />
                                <div>
                                    <p class="font-medium">Commande récupérée par le livreur</p>
                                    <p class="text-sm text-gray-600">Il y a 10 minutes</p>
                                </div>
                            </div>
                            <div class="flex items-start space-x-3">
                                <div class="flex-shrink-0 w-2 h-2 bg-gray-300 rounded-full mt-2" />
                                <div>
                                    <p class="font-medium">Commande prête</p>
                                    <p class="text-sm text-gray-600">Il y a 15 minutes</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
