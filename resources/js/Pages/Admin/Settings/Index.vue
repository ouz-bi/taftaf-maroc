<script setup>
import { useForm } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import {
    Cog6ToothIcon,
    TruckIcon,
    CurrencyDollarIcon,
    ClockIcon,
    BellIcon
} from '@heroicons/vue/24/outline'

const props = defineProps({
    settings: Object,
})

// Formulaire général
const generalForm = useForm({
    site_name: props.settings?.site_name || 'TafTaf Maroc',
    site_email: props.settings?.site_email || 'contact@taftaf.ma',
    site_phone: props.settings?.site_phone || '+212 XXX XXX XXX',
    delivery_time: props.settings?.delivery_time || 20,
})

// Formulaire livraison
const deliveryForm = useForm({
    base_delivery_fee: props.settings?.base_delivery_fee || 15.00,
    free_delivery_threshold: props.settings?.free_delivery_threshold || 200.00,
    max_delivery_distance: props.settings?.max_delivery_distance || 10,
})

// Formulaire notifications
const notificationForm = useForm({
    enable_sms: props.settings?.enable_sms ?? true,
    enable_email: props.settings?.enable_email ?? true,
    enable_push: props.settings?.enable_push ?? false,
})

const saveGeneral = () => {
    generalForm.post('/admin/settings/general', {
        preserveScroll: true,
    })
}

const saveDelivery = () => {
    deliveryForm.post('/admin/settings/delivery', {
        preserveScroll: true,
    })
}

const saveNotifications = () => {
    notificationForm.post('/admin/settings/notifications', {
        preserveScroll: true,
    })
}
</script>

<template>
    <AdminLayout>
        <template #header>
            <div class="flex items-center space-x-3">
                <Cog6ToothIcon class="h-8 w-8 text-primary-600" />
                <h1 class="text-2xl font-bold text-gray-900">Paramètres</h1>
            </div>
        </template>

        <div class="space-y-6">
            <!-- Informations Générales -->
            <div class="card">
                <div class="card-header">
                    <h2 class="text-lg font-bold">Informations Générales</h2>
                </div>
                <form @submit.prevent="saveGeneral" class="card-body">
                    <div class="grid md:grid-cols-2 gap-6">
                        <!-- Nom du site -->
                        <div>
                            <label class="label">Nom du site</label>
                            <input
                                v-model="generalForm.site_name"
                                type="text"
                                class="input-field"
                                required
                            />
                            <p v-if="generalForm.errors.site_name" class="error-message">
                                {{ generalForm.errors.site_name }}
                            </p>
                        </div>

                        <!-- Email -->
                        <div>
                            <label class="label">Email de contact</label>
                            <input
                                v-model="generalForm.site_email"
                                type="email"
                                class="input-field"
                                required
                            />
                            <p v-if="generalForm.errors.site_email" class="error-message">
                                {{ generalForm.errors.site_email }}
                            </p>
                        </div>

                        <!-- Téléphone -->
                        <div>
                            <label class="label">Téléphone</label>
                            <input
                                v-model="generalForm.site_phone"
                                type="tel"
                                class="input-field"
                                required
                            />
                            <p v-if="generalForm.errors.site_phone" class="error-message">
                                {{ generalForm.errors.site_phone }}
                            </p>
                        </div>

                        <!-- Temps de livraison -->
                        <div>
                            <label class="label">Temps de livraison estimé (minutes)</label>
                            <div class="relative">
                                <input
                                    v-model.number="generalForm.delivery_time"
                                    type="number"
                                    min="10"
                                    max="60"
                                    class="input-field pl-10"
                                    required
                                />
                                <ClockIcon class="absolute left-3 top-3 h-5 w-5 text-gray-400" />
                            </div>
                            <p v-if="generalForm.errors.delivery_time" class="error-message">
                                {{ generalForm.errors.delivery_time }}
                            </p>
                        </div>
                    </div>

                    <div class="flex justify-end mt-6 pt-6 border-t">
                        <button
                            type="submit"
                            :disabled="generalForm.processing"
                            class="btn-primary"
                        >
                            {{ generalForm.processing ? 'Enregistrement...' : 'Enregistrer' }}
                        </button>
                    </div>
                </form>
            </div>

            <!-- Paramètres de Livraison -->
            <div class="card">
                <div class="card-header">
                    <h2 class="text-lg font-bold flex items-center">
                        <TruckIcon class="h-6 w-6 mr-2 text-primary-600" />
                        Paramètres de Livraison
                    </h2>
                </div>
                <form @submit.prevent="saveDelivery" class="card-body">
                    <div class="grid md:grid-cols-3 gap-6">
                        <!-- Frais de base -->
                        <div>
                            <label class="label">Frais de livraison de base (DH)</label>
                            <div class="relative">
                                <input
                                    v-model.number="deliveryForm.base_delivery_fee"
                                    type="number"
                                    step="0.01"
                                    min="0"
                                    class="input-field pl-10"
                                    required
                                />
                                <CurrencyDollarIcon class="absolute left-3 top-3 h-5 w-5 text-gray-400" />
                            </div>
                            <p class="text-xs text-gray-500 mt-1">
                                Frais standard pour toutes les livraisons
                            </p>
                        </div>

                        <!-- Livraison gratuite -->
                        <div>
                            <label class="label">Livraison gratuite à partir de (DH)</label>
                            <div class="relative">
                                <input
                                    v-model.number="deliveryForm.free_delivery_threshold"
                                    type="number"
                                    step="0.01"
                                    min="0"
                                    class="input-field pl-10"
                                    required
                                />
                                <CurrencyDollarIcon class="absolute left-3 top-3 h-5 w-5 text-gray-400" />
                            </div>
                            <p class="text-xs text-gray-500 mt-1">
                                Montant minimum pour livraison offerte
                            </p>
                        </div>

                        <!-- Distance max -->
                        <div>
                            <label class="label">Distance maximum (km)</label>
                            <input
                                v-model.number="deliveryForm.max_delivery_distance"
                                type="number"
                                min="1"
                                max="50"
                                class="input-field"
                                required
                            />
                            <p class="text-xs text-gray-500 mt-1">
                                Rayon de livraison maximum
                            </p>
                        </div>
                    </div>

                    <!-- Info tarification -->
                    <div class="mt-6 bg-blue-50 border border-blue-200 rounded-lg p-4">
                        <h4 class="font-semibold text-blue-900 mb-2">Tarification actuelle :</h4>
                        <ul class="text-sm text-blue-800 space-y-1">
                            <li>• 0-3 km : {{ deliveryForm.base_delivery_fee }} DH</li>
                            <li>• 3-7 km : {{ (deliveryForm.base_delivery_fee * 1.33).toFixed(2) }} DH</li>
                            <li>• 7-{{ deliveryForm.max_delivery_distance }} km : {{ (deliveryForm.base_delivery_fee * 2).toFixed(2) }} DH</li>
                            <li>• Livraison gratuite à partir de {{ deliveryForm.free_delivery_threshold }} DH</li>
                        </ul>
                    </div>

                    <div class="flex justify-end mt-6 pt-6 border-t">
                        <button
                            type="submit"
                            :disabled="deliveryForm.processing"
                            class="btn-primary"
                        >
                            {{ deliveryForm.processing ? 'Enregistrement...' : 'Enregistrer' }}
                        </button>
                    </div>
                </form>
            </div>

            <!-- Notifications -->
            <div class="card">
                <div class="card-header">
                    <h2 class="text-lg font-bold flex items-center">
                        <BellIcon class="h-6 w-6 mr-2 text-primary-600" />
                        Notifications
                    </h2>
                </div>
                <form @submit.prevent="saveNotifications" class="card-body">
                    <div class="space-y-4">
                        <!-- SMS -->
                        <label class="flex items-center justify-between p-4 border-2 rounded-lg cursor-pointer hover:border-primary-300">
                            <div>
                                <p class="font-semibold">Notifications SMS</p>
                                <p class="text-sm text-gray-600">Envoyer des SMS aux clients (confirmation, suivi)</p>
                            </div>
                            <input
                                v-model="notificationForm.enable_sms"
                                type="checkbox"
                                class="w-5 h-5 text-primary-600 border-gray-300 rounded"
                            />
                        </label>

                        <!-- Email -->
                        <label class="flex items-center justify-between p-4 border-2 rounded-lg cursor-pointer hover:border-primary-300">
                            <div>
                                <p class="font-semibold">Notifications Email</p>
                                <p class="text-sm text-gray-600">Envoyer des emails de confirmation</p>
                            </div>
                            <input
                                v-model="notificationForm.enable_email"
                                type="checkbox"
                                class="w-5 h-5 text-primary-600 border-gray-300 rounded"
                            />
                        </label>

                        <!-- Push -->
                        <label class="flex items-center justify-between p-4 border-2 rounded-lg cursor-pointer hover:border-primary-300">
                            <div>
                                <p class="font-semibold">Notifications Push</p>
                                <p class="text-sm text-gray-600">Notifications push pour l'application mobile</p>
                            </div>
                            <input
                                v-model="notificationForm.enable_push"
                                type="checkbox"
                                class="w-5 h-5 text-primary-600 border-gray-300 rounded"
                            />
                        </label>
                    </div>

                    <div class="flex justify-end mt-6 pt-6 border-t">
                        <button
                            type="submit"
                            :disabled="notificationForm.processing"
                            class="btn-primary"
                        >
                            {{ notificationForm.processing ? 'Enregistrement...' : 'Enregistrer' }}
                        </button>
                    </div>
                </form>
            </div>

            <!-- Actions Dangereuses -->
            <div class="card border-red-200">
                <div class="card-header bg-red-50">
                    <h2 class="text-lg font-bold text-red-900">Zone Dangereuse</h2>
                </div>
                <div class="card-body space-y-4">
                    <div class="flex items-center justify-between p-4 border-2 border-red-200 rounded-lg">
                        <div>
                            <p class="font-semibold text-red-900">Vider le cache</p>
                            <p class="text-sm text-gray-600">Supprimer tous les fichiers en cache de l'application</p>
                        </div>
                        <button
                            @click="clearCache"
                            class="btn-danger"
                        >
                            Vider le cache
                        </button>
                    </div>

                    <div class="flex items-center justify-between p-4 border-2 border-red-200 rounded-lg">
                        <div>
                            <p class="font-semibold text-red-900">Réinitialiser les paramètres</p>
                            <p class="text-sm text-gray-600">Restaurer tous les paramètres par défaut</p>
                        </div>
                        <button
                            @click="resetSettings"
                            class="btn-danger"
                        >
                            Réinitialiser
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>

<script>
export default {
    methods: {
        clearCache() {
            if (confirm('Êtes-vous sûr de vouloir vider le cache ?')) {
                this.$inertia.post('/admin/settings/clear-cache', {}, {
                    preserveScroll: true,
                })
            }
        },
        resetSettings() {
            if (confirm('Êtes-vous sûr de vouloir réinitialiser tous les paramètres ? Cette action est irréversible.')) {
                this.$inertia.post('/admin/settings/reset', {}, {
                    preserveScroll: true,
                })
            }
        }
    }
}
</script>
