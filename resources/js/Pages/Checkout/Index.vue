<script setup>
import { ref, computed } from 'vue'
import { router, useForm } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import {
    MapPinIcon,
    PlusIcon,
    CreditCardIcon,
    BanknotesIcon,
    CheckCircleIcon
} from '@heroicons/vue/24/outline'

const props = defineProps({
    cartItems: Array,
    addresses: Array,
    subtotal: Number,
})

const showAddressForm = ref(false)
const deliveryFee = 15.00

const form = useForm({
    address_id: props.addresses.find(a => a.is_default)?.id || null,
    payment_method: 'cash',
    notes: '',
})

const addressForm = useForm({
    label: 'Maison',
    full_name: '',
    phone: '',
    address_line: '',
    city: 'Casablanca',
    district: '',
    postal_code: '',
    latitude: 33.5731, // Default Casablanca
    longitude: -7.5898,
    instructions: '',
    is_default: false,
})

const total = computed(() => props.subtotal + deliveryFee)

const submitOrder = () => {
    form.post('/orders', {
        preserveScroll: true,
        onSuccess: () => {
            // Redirection automatique vers la page de commande
        },
    })
}

const addAddress = () => {
    addressForm.post('/addresses', {
        preserveScroll: true,
        onSuccess: () => {
            showAddressForm.value = false
            addressForm.reset()
        },
    })
}

const cities = [
    'Casablanca', 'Rabat', 'Marrakech', 'Fès', 'Tanger',
    'Agadir', 'Meknès', 'Oujda', 'Kenitra', 'Tétouan'
]
</script>

<template>
    <AppLayout>
        <div class="bg-gray-50 min-h-screen py-8">
            <div class="container-custom">
                <h1 class="text-3xl font-bold text-gray-900 mb-8">Finaliser la commande</h1>

                <form @submit.prevent="submitOrder" class="grid lg:grid-cols-3 gap-8">
                    <!-- Main Form -->
                    <div class="lg:col-span-2 space-y-6">
                        <!-- Delivery Address -->
                        <div class="card">
                            <div class="card-header">
                                <div class="flex items-center justify-between">
                                    <h2 class="text-lg font-bold flex items-center">
                                        <MapPinIcon class="h-5 w-5 mr-2 text-primary-600" />
                                        Adresse de livraison
                                    </h2>
                                    <button
                                        type="button"
                                        @click="showAddressForm = !showAddressForm"
                                        class="btn-secondary btn-sm flex items-center space-x-1"
                                    >
                                        <PlusIcon class="h-4 w-4" />
                                        <span>Nouvelle adresse</span>
                                    </button>
                                </div>
                            </div>

                            <div class="card-body">
                                <!-- Existing Addresses -->
                                <div v-if="addresses.length > 0" class="space-y-3 mb-6">
                                    <label
                                        v-for="address in addresses"
                                        :key="address.id"
                                        :class="[
                                            'block p-4 border-2 rounded-lg cursor-pointer transition',
                                            form.address_id === address.id
                                                ? 'border-primary-600 bg-primary-50'
                                                : 'border-gray-200 hover:border-primary-300'
                                        ]"
                                    >
                                        <input
                                            v-model="form.address_id"
                                            type="radio"
                                            :value="address.id"
                                            class="sr-only"
                                        />
                                        <div class="flex items-start">
                                            <CheckCircleIcon
                                                :class="[
                                                    'h-6 w-6 mr-3 flex-shrink-0',
                                                    form.address_id === address.id
                                                        ? 'text-primary-600'
                                                        : 'text-gray-300'
                                                ]"
                                            />
                                            <div class="flex-1">
                                                <div class="flex items-center space-x-2 mb-1">
                                                    <span class="font-semibold">{{ address.label }}</span>
                                                    <span v-if="address.is_default" class="badge-primary text-xs">
                                                        Par défaut
                                                    </span>
                                                </div>
                                                <p class="text-sm text-gray-700 mb-1">
                                                    {{ address.full_name }} - {{ address.phone }}
                                                </p>
                                                <p class="text-sm text-gray-600">
                                                    {{ address.address_line }}, {{ address.district }}, {{ address.city }}
                                                </p>
                                                <p v-if="address.instructions" class="text-sm text-gray-500 mt-1">
                                                    📝 {{ address.instructions }}
                                                </p>
                                            </div>
                                        </div>
                                    </label>
                                </div>

                                <!-- No Address -->
                                <div v-else class="text-center py-8 text-gray-600">
                                    <MapPinIcon class="h-12 w-12 text-gray-300 mx-auto mb-3" />
                                    <p class="mb-4">Aucune adresse enregistrée</p>
                                    <button
                                        type="button"
                                        @click="showAddressForm = true"
                                        class="btn-primary"
                                    >
                                        Ajouter une adresse
                                    </button>
                                </div>

                                <!-- Add Address Form -->
                                <div v-if="showAddressForm" class="mt-6 p-6 bg-gray-50 rounded-lg">
                                    <h3 class="font-bold mb-4">Nouvelle adresse</h3>
                                    <div class="grid md:grid-cols-2 gap-4">
                                        <div>
                                            <label class="label">Type d'adresse</label>
                                            <select v-model="addressForm.label" class="input-field">
                                                <option value="Maison">🏠 Maison</option>
                                                <option value="Bureau">🏢 Bureau</option>
                                                <option value="Autre">📍 Autre</option>
                                            </select>
                                        </div>

                                        <div>
                                            <label class="label">Nom complet *</label>
                                            <input
                                                v-model="addressForm.full_name"
                                                type="text"
                                                class="input-field"
                                                required
                                            />
                                        </div>

                                        <div>
                                            <label class="label">Téléphone *</label>
                                            <input
                                                v-model="addressForm.phone"
                                                type="tel"
                                                placeholder="+212 6XX XXX XXX"
                                                class="input-field"
                                                required
                                            />
                                        </div>

                                        <div>
                                            <label class="label">Ville *</label>
                                            <select v-model="addressForm.city" class="input-field">
                                                <option v-for="city in cities" :key="city" :value="city">
                                                    {{ city }}
                                                </option>
                                            </select>
                                        </div>

                                        <div class="md:col-span-2">
                                            <label class="label">Adresse complète *</label>
                                            <input
                                                v-model="addressForm.address_line"
                                                type="text"
                                                placeholder="Numéro, rue, immeuble..."
                                                class="input-field"
                                                required
                                            />
                                        </div>

                                        <div>
                                            <label class="label">Quartier</label>
                                            <input
                                                v-model="addressForm.district"
                                                type="text"
                                                placeholder="Maarif, Ain Diab..."
                                                class="input-field"
                                            />
                                        </div>

                                        <div>
                                            <label class="label">Code postal</label>
                                            <input
                                                v-model="addressForm.postal_code"
                                                type="text"
                                                class="input-field"
                                            />
                                        </div>

                                        <div class="md:col-span-2">
                                            <label class="label">Instructions de livraison</label>
                                            <textarea
                                                v-model="addressForm.instructions"
                                                rows="2"
                                                placeholder="Sonnez au 2ème étage, bâtiment B..."
                                                class="input-field"
                                            />
                                        </div>

                                        <div class="md:col-span-2">
                                            <label class="flex items-center cursor-pointer">
                                                <input
                                                    v-model="addressForm.is_default"
                                                    type="checkbox"
                                                    class="w-4 h-4 text-primary-600 border-gray-300 rounded"
                                                />
                                                <span class="ml-2 text-sm text-gray-700">
                                                    Définir comme adresse par défaut
                                                </span>
                                            </label>
                                        </div>

                                        <div class="md:col-span-2 flex space-x-3">
                                            <button
                                                type="button"
                                                @click="addAddress"
                                                class="btn-primary"
                                                :disabled="addressForm.processing"
                                            >
                                                {{ addressForm.processing ? 'Enregistrement...' : 'Enregistrer' }}
                                            </button>
                                            <button
                                                type="button"
                                                @click="showAddressForm = false; addressForm.reset()"
                                                class="btn-secondary"
                                            >
                                                Annuler
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <div v-if="form.errors.address_id" class="error-message mt-2">
                                    {{ form.errors.address_id }}
                                </div>
                            </div>
                        </div>

                        <!-- Payment Method -->
                        <div class="card">
                            <div class="card-header">
                                <h2 class="text-lg font-bold flex items-center">
                                    <CreditCardIcon class="h-5 w-5 mr-2 text-primary-600" />
                                    Mode de paiement
                                </h2>
                            </div>

                            <div class="card-body">
                                <div class="space-y-3">
                                    <!-- Cash Payment -->
                                    <label
                                        :class="[
                                            'block p-4 border-2 rounded-lg cursor-pointer transition',
                                            form.payment_method === 'cash'
                                                ? 'border-primary-600 bg-primary-50'
                                                : 'border-gray-200 hover:border-primary-300'
                                        ]"
                                    >
                                        <input
                                            v-model="form.payment_method"
                                            type="radio"
                                            value="cash"
                                            class="sr-only"
                                        />
                                        <div class="flex items-center">
                                            <BanknotesIcon class="h-8 w-8 text-green-600 mr-4" />
                                            <div class="flex-1">
                                                <p class="font-semibold">Paiement à la livraison</p>
                                                <p class="text-sm text-gray-600">Payez en espèces au livreur</p>
                                            </div>
                                            <CheckCircleIcon
                                                :class="[
                                                    'h-6 w-6',
                                                    form.payment_method === 'cash'
                                                        ? 'text-primary-600'
                                                        : 'text-gray-300'
                                                ]"
                                            />
                                        </div>
                                    </label>

                                    <!-- Card Payment -->
                                    <!-- <label
                                        :class="[
                                            'block p-4 border-2 rounded-lg cursor-pointer transition',
                                            form.payment_method === 'card'
                                                ? 'border-primary-600 bg-primary-50'
                                                : 'border-gray-200 hover:border-primary-300'
                                        ]"
                                    >
                                        <input
                                            v-model="form.payment_method"
                                            type="radio"
                                            value="card"
                                            class="sr-only"
                                        />
                                        <div class="flex items-center">
                                            <CreditCardIcon class="h-8 w-8 text-blue-600 mr-4" />
                                            <div class="flex-1">
                                                <p class="font-semibold">Carte bancaire</p>
                                                <p class="text-sm text-gray-600">Paiement sécurisé en ligne</p>
                                            </div>
                                            <CheckCircleIcon
                                                :class="[
                                                    'h-6 w-6',
                                                    form.payment_method === 'card'
                                                        ? 'text-primary-600'
                                                        : 'text-gray-300'
                                                ]"
                                            />
                                        </div>
                                    </label> -->

                                    <!-- CMI Payment -->
                                    <!-- <label
                                        :class="[
                                            'block p-4 border-2 rounded-lg cursor-pointer transition',
                                            form.payment_method === 'cmi'
                                                ? 'border-primary-600 bg-primary-50'
                                                : 'border-gray-200 hover:border-primary-300'
                                        ]"
                                    >
                                        <input
                                            v-model="form.payment_method"
                                            type="radio"
                                            value="cmi"
                                            class="sr-only"
                                        />
                                        <div class="flex items-center">
                                            <div class="h-8 w-12 bg-gradient-to-r from-blue-600 to-blue-800 rounded mr-4 flex items-center justify-center text-white text-xs font-bold">
                                                CMI
                                            </div>
                                            <div class="flex-1">
                                                <p class="font-semibold">CMI (Maroc)</p>
                                                <p class="text-sm text-gray-600">Paiement par carte marocaine</p>
                                            </div>
                                            <CheckCircleIcon
                                                :class="[
                                                    'h-6 w-6',
                                                    form.payment_method === 'cmi'
                                                        ? 'text-primary-600'
                                                        : 'text-gray-300'
                                                ]"
                                            />
                                        </div>
                                    </label> -->
                                </div>
                            </div>
                        </div>

                        <!-- Order Notes -->
                        <div class="card">
                            <div class="card-body">
                                <label class="label">Notes de commande (optionnel)</label>
                                <textarea
                                    v-model="form.notes"
                                    rows="3"
                                    placeholder="Ajoutez des instructions spéciales pour votre commande..."
                                    class="input-field"
                                />
                            </div>
                        </div>
                    </div>

                    <!-- Order Summary Sidebar -->
                    <div class="lg:col-span-1">
                        <div class="card sticky top-20">
                            <div class="card-header">
                                <h2 class="font-bold">Récapitulatif ({{ cartItems.length }} articles)</h2>
                            </div>

                            <div class="card-body">
                                <!-- Cart Items -->
                                <div class="space-y-3 mb-4 max-h-64 overflow-y-auto">
                                    <div
                                        v-for="item in cartItems"
                                        :key="item.id"
                                        class="flex items-center space-x-3 pb-3 border-b"
                                    >
                                        <img
                                            :src="item.product.image || '/images/placeholder.png'"
                                            class="w-16 h-16 object-cover rounded"
                                        />
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm font-medium text-gray-900 truncate">
                                                {{ item.product.name }}
                                            </p>
                                            <p class="text-sm text-gray-600">
                                                {{ item.quantity }} x {{ item.product.promo_price || item.product.price }} DH
                                            </p>
                                        </div>
                                        <span class="text-sm font-semibold">
                                            {{ ((item.product.promo_price || item.product.price) * item.quantity).toFixed(2) }} DH
                                        </span>
                                    </div>
                                </div>

                                <!-- Pricing -->
                                <div class="space-y-2 py-4 border-t">
                                    <div class="flex justify-between text-gray-700">
                                        <span>Sous-total</span>
                                        <span class="font-medium">{{ subtotal.toFixed(2) }} DH</span>
                                    </div>
                                    <div class="flex justify-between text-gray-700">
                                        <span>Livraison</span>
                                        <span class="font-medium">{{ deliveryFee.toFixed(2) }} DH</span>
                                    </div>
                                </div>

                                <div class="flex justify-between items-baseline pt-4 border-t mb-6">
                                    <span class="text-lg font-bold">Total</span>
                                    <span class="text-2xl font-bold text-primary-600">
                                        {{ total.toFixed(2) }} DH
                                    </span>
                                </div>

                                <!-- Submit Button -->
                                <button
                                    type="submit"
                                    :disabled="form.processing || !form.address_id"
                                    class="btn-primary w-full btn-lg"
                                >
                                    {{ form.processing ? 'Traitement...' : 'Confirmer la commande' }}
                                </button>

                                <!-- Security Badge -->
                                <div class="mt-4 p-3 bg-green-50 rounded-lg text-center">
                                    <p class="text-xs text-green-800">
                                        🔒 Paiement 100% sécurisé
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </AppLayout>
</template>
