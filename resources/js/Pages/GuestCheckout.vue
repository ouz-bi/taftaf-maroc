<script setup>
import { ref, computed, onMounted } from 'vue'
import { useForm, Link } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import { MapPinIcon, PhoneIcon, UserIcon, CreditCardIcon, BanknotesIcon } from '@heroicons/vue/24/outline'

const props = defineProps({
    cartItems: Array,
    subtotal: Number,
})

// Google Maps
const map = ref(null)
const marker = ref(null)
const mapContainer = ref(null)
const searchBox = ref(null)

// Formulaire simplifié
const form = useForm({
    // Informations client
    name: '',
    phone: '',

    // Adresse avec coordonnées
    address: '',
    latitude: 33.5731, // Default: Casablanca
    longitude: -7.5898,

    // Paiement
    payment_method: 'cash',

    // Notes
    notes: '',
})

const deliveryFee = 15.00
const total = computed(() => props.subtotal + deliveryFee)

// Initialiser Google Maps
onMounted(() => {
    loadGoogleMaps()
})

const loadGoogleMaps = () => {
    // Vérifier si Google Maps est déjà chargé
    if (window.google && window.google.maps) {
        initMap()
        return
    }

    // Charger le script Google Maps
    const script = document.createElement('script')
    script.src = `https://maps.googleapis.com/maps/api/js?key=${import.meta.env.VITE_GOOGLE_MAPS_API_KEY}&libraries=places&language=fr`
    script.async = true
    script.defer = true
    script.onload = initMap
    document.head.appendChild(script)
}

const initMap = () => {
    // Créer la carte centrée sur Casablanca
    map.value = new google.maps.Map(mapContainer.value, {
        center: { lat: form.latitude, lng: form.longitude },
        zoom: 13,
        mapTypeControl: false,
        streetViewControl: false,
        fullscreenControl: false,
    })

    // Créer le marqueur
    marker.value = new google.maps.Marker({
        map: map.value,
        position: { lat: form.latitude, lng: form.longitude },
        draggable: true,
        animation: google.maps.Animation.DROP,
        title: 'Déplacez-moi sur votre adresse exacte'
    })

    // Écouter le déplacement du marqueur
    marker.value.addListener('dragend', (event) => {
        form.latitude = event.latLng.lat()
        form.longitude = event.latLng.lng()
        reverseGeocode(event.latLng)
    })

    // Clic sur la carte pour déplacer le marqueur
    map.value.addListener('click', (event) => {
        marker.value.setPosition(event.latLng)
        form.latitude = event.latLng.lat()
        form.longitude = event.latLng.lng()
        reverseGeocode(event.latLng)
    })

    // Recherche d'adresse
    const input = searchBox.value
    const autocomplete = new google.maps.places.Autocomplete(input, {
        componentRestrictions: { country: 'ma' }, // Limiter au Maroc
        fields: ['geometry', 'formatted_address']
    })

    autocomplete.addListener('place_changed', () => {
        const place = autocomplete.getPlace()
        if (place.geometry) {
            const location = place.geometry.location
            map.value.setCenter(location)
            marker.value.setPosition(location)
            form.latitude = location.lat()
            form.longitude = location.lng()
            form.address = place.formatted_address
        }
    })

    // Géolocalisation
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition((position) => {
            const pos = {
                lat: position.coords.latitude,
                lng: position.coords.longitude
            }
            map.value.setCenter(pos)
            marker.value.setPosition(pos)
            form.latitude = pos.lat
            form.longitude = pos.lng
            reverseGeocode(pos)
        })
    }
}

// Obtenir l'adresse à partir des coordonnées
const reverseGeocode = (latLng) => {
    const geocoder = new google.maps.Geocoder()
    geocoder.geocode({ location: latLng }, (results, status) => {
        if (status === 'OK' && results[0]) {
            form.address = results[0].formatted_address
        }
    })
}

const submit = () => {
    if (!form.name || !form.phone || !form.address) {
        alert('Veuillez remplir tous les champs obligatoires')
        return
    }

    form.post('/guest/checkout', {
        preserveScroll: true,
    })
}
</script>

<template>
    <AppLayout>
        <div class="bg-gray-50 min-h-screen py-8">
            <div class="container-custom">
                <!-- Header -->
                <div class="text-center mb-8">
                    <h1 class="text-3xl font-bold text-gray-900 mb-2">
                        Finalisez votre commande
                    </h1>
                    <p class="text-gray-600">
                        Pas besoin de créer un compte, c'est simple et rapide ! ⚡
                    </p>
                </div>

                <form @submit.prevent="submit" class="grid lg:grid-cols-3 gap-8">
                    <!-- Formulaire principal -->
                    <div class="lg:col-span-2 space-y-6">
                        <!-- Étape 1 : Vos informations -->
                        <div class="card">
                            <div class="card-header">
                                <div class="flex items-center justify-between">
                                    <h2 class="text-lg font-bold flex items-center">
                                        <div class="w-8 h-8 bg-primary-600 text-white rounded-full flex items-center justify-center mr-3 text-sm font-bold">
                                            1
                                        </div>
                                        Vos informations
                                    </h2>
                                </div>
                            </div>

                            <div class="card-body">
                                <div class="grid md:grid-cols-2 gap-4">
                                    <!-- Nom -->
                                    <div>
                                        <label class="label">Nom complet *</label>
                                        <div class="relative">
                                            <input
                                                v-model="form.name"
                                                type="text"
                                                class="input-field pl-10"
                                                placeholder="Mohammed Alaoui"
                                                required
                                            />
                                            <UserIcon class="absolute left-3 top-3 h-5 w-5 text-gray-400" />
                                        </div>
                                        <p v-if="form.errors.name" class="error-message">{{ form.errors.name }}</p>
                                    </div>

                                    <!-- Téléphone -->
                                    <div>
                                        <label class="label">Téléphone *</label>
                                        <div class="relative">
                                            <input
                                                v-model="form.phone"
                                                type="tel"
                                                class="input-field pl-10"
                                                placeholder="+212 6XX XXX XXX"
                                                required
                                            />
                                            <PhoneIcon class="absolute left-3 top-3 h-5 w-5 text-gray-400" />
                                        </div>
                                        <p v-if="form.errors.phone" class="error-message">{{ form.errors.phone }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Étape 2 : Votre adresse avec Google Maps -->
                        <div class="card">
                            <div class="card-header">
                                <h2 class="text-lg font-bold flex items-center">
                                    <div class="w-8 h-8 bg-primary-600 text-white rounded-full flex items-center justify-center mr-3 text-sm font-bold">
                                        2
                                    </div>
                                    Votre adresse de livraison
                                </h2>
                            </div>

                            <div class="card-body space-y-4">
                                <!-- Recherche d'adresse -->
                                <div>
                                    <label class="label">Rechercher votre adresse</label>
                                    <div class="relative">
                                        <input
                                            v-model="form.address"
                                            type="text"
                                            class="input-field pl-10"
                                            placeholder="Tapez votre adresse (rue, quartier, ville)..."
                                        />
                                        <MapPinIcon class="absolute left-3 top-3 h-5 w-5 text-gray-400" />
                                    </div>
                                    <p class="text-xs text-gray-500 mt-1">
                                        Ou cliquez directement sur la carte pour placer le marqueur
                                    </p>
                                </div>

                                <!-- Google Map -->
                                <div class="rounded-lg overflow-hidden border-2 border-gray-300">
                                    <div ref="mapContainer" class="w-full h-96"></div>
                                </div>

                                <!-- Adresse sélectionnée -->
                                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                                    <p class="text-sm font-medium text-blue-900 mb-1">
                                        📍 Adresse sélectionnée :
                                    </p>
                                    <p class="text-sm text-blue-800">
                                        {{ form.address || 'Placez le marqueur sur votre adresse exacte' }}
                                    </p>
                                    <p class="text-xs text-blue-600 mt-2">
                                        Coordonnées : {{ form.latitude.toFixed(6) }}, {{ form.longitude.toFixed(6) }}
                                    </p>
                                </div>

                                <!-- Instructions de livraison -->
                                <div>
                                    <label class="label">Instructions de livraison (optionnel)</label>
                                    <textarea
                                        v-model="form.notes"
                                        rows="2"
                                        class="input-field"
                                        placeholder="Ex: Sonnez au 2ème étage, bâtiment B..."
                                    />
                                </div>

                                <p v-if="form.errors.address" class="error-message">{{ form.errors.address }}</p>
                            </div>
                        </div>

                        <!-- Étape 3 : Paiement -->
                        <div class="card">
                            <div class="card-header">
                                <h2 class="text-lg font-bold flex items-center">
                                    <div class="w-8 h-8 bg-primary-600 text-white rounded-full flex items-center justify-center mr-3 text-sm font-bold">
                                        3
                                    </div>
                                    Mode de paiement
                                </h2>
                            </div>

                            <div class="card-body">
                                <div class="space-y-3">
                                    <!-- Paiement à la livraison -->
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
                                                <p class="font-semibold">💵 Paiement à la livraison</p>
                                                <p class="text-sm text-gray-600">Payez en espèces au livreur</p>
                                            </div>
                                            <div v-if="form.payment_method === 'cash'" class="text-primary-600 font-bold">
                                                ✓
                                            </div>
                                        </div>
                                    </label>

                                    <!-- Paiement en ligne -->
                                    <label
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
                                                <p class="font-semibold">💳 Carte bancaire</p>
                                                <p class="text-sm text-gray-600">Paiement sécurisé en ligne</p>
                                            </div>
                                            <div v-if="form.payment_method === 'card'" class="text-primary-600 font-bold">
                                                ✓
                                            </div>
                                        </div>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Récapitulatif sticky -->
                    <div class="lg:col-span-1">
                        <div class="card sticky top-20">
                            <div class="card-header">
                                <h2 class="font-bold">Récapitulatif ({{ cartItems.length }} articles)</h2>
                            </div>

                            <div class="card-body">
                                <!-- Produits -->
                                <div class="space-y-3 mb-4 max-h-64 overflow-y-auto">
                                    <div
                                        v-for="item in cartItems"
                                        :key="item.id"
                                        class="flex items-center space-x-3 pb-3 border-b"
                                    >
                                        <img
                                            :src="item.product.image ? `/storage/${item.product.image}` : '/images/placeholder.png'"
                                            class="w-12 h-12 object-cover rounded"
                                        />
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm font-medium truncate">{{ item.product.name }}</p>
                                            <p class="text-xs text-gray-600">
                                                {{ item.quantity }} × {{ item.product.price }} DH
                                            </p>
                                        </div>
                                        <span class="text-sm font-semibold">
                                            {{ (item.product.price * item.quantity).toFixed(2) }} DH
                                        </span>
                                    </div>
                                </div>

                                <!-- Total -->
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

                                <!-- Bouton Commander -->
                                <button
                                    type="submit"
                                    :disabled="form.processing || !form.address"
                                    class="btn-primary w-full btn-lg"
                                >
                                    {{ form.processing ? 'Traitement...' : '🚀 Commander maintenant' }}
                                </button>

                                <!-- Info livraison -->
                                <div class="mt-4 p-3 bg-green-50 rounded-lg text-center">
                                    <p class="text-xs text-green-800">
                                        ⚡ Livraison en 20 minutes !
                                    </p>
                                </div>

                                <!-- Sécurité -->
                                <div class="mt-4 text-center">
                                    <p class="text-xs text-gray-500">
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
