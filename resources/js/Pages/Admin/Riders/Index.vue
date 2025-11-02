<!-- resources/js/Pages/Admin/Riders/Index.vue -->
<script setup>
import { ref } from 'vue'
import { useForm } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { PlusIcon, TruckIcon, StarIcon } from '@heroicons/vue/24/outline'

const props = defineProps({
    riders: Array,
})

const showModal = ref(false)

const form = useForm({
    name: '',
    email: '',
    phone: '',
    vehicle_type: 'Moto',
    vehicle_plate: '',
    license_number: '',
})

const submit = () => {
    form.post('/admin/riders', {
        onSuccess: () => {
            showModal.value = false
            form.reset()
        }
    })
}
</script>

<template>
    <AdminLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h1 class="text-2xl font-bold text-gray-900">Gestion des Livreurs</h1>
                <button @click="showModal = true" class="btn-primary flex items-center space-x-2">
                    <PlusIcon class="h-5 w-5" />
                    <span>Nouveau livreur</span>
                </button>
            </div>
        </template>

        <!-- Riders Grid -->
        <div class="grid md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            <div
                v-for="rider in riders"
                :key="rider.id"
                class="card"
            >
                <div class="card-body">
                    <!-- Avatar -->
                    <div class="flex items-center space-x-4 mb-4">
                        <div class="w-16 h-16 bg-orange-100 rounded-full flex items-center justify-center">
                            <span class="text-2xl font-bold text-orange-600">
                                {{ rider.user.name[0].toUpperCase() }}
                            </span>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="font-semibold truncate">{{ rider.user.name }}</p>
                            <p class="text-sm text-gray-600 truncate">{{ rider.user.email }}</p>
                        </div>
                    </div>

                    <!-- Stats -->
                    <div class="space-y-2 mb-4">
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-gray-600">Véhicule</span>
                            <span class="font-medium flex items-center">
                                <TruckIcon class="h-4 w-4 mr-1" />
                                {{ rider.vehicle_type }}
                            </span>
                        </div>
                        <div v-if="rider.vehicle_plate" class="flex items-center justify-between text-sm">
                            <span class="text-gray-600">Plaque</span>
                            <code class="text-xs bg-gray-100 px-2 py-1 rounded">{{ rider.vehicle_plate }}</code>
                        </div>
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-gray-600">Livraisons</span>
                            <span class="font-medium">{{ rider.orders_count || 0 }}</span>
                        </div>
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-gray-600">Note</span>
                            <span class="font-medium flex items-center">
                                <StarIcon class="h-4 w-4 text-yellow-500 mr-1 fill-current" />
                                {{ rider.rating }}
                            </span>
                        </div>
                    </div>

                    <!-- Status -->
                    <div class="flex items-center justify-between pt-4 border-t">
                        <span :class="[
                            'text-sm font-medium',
                            rider.is_available ? 'text-green-600' : 'text-gray-500'
                        ]">
                            {{ rider.is_available ? '🟢 Disponible' : '⚫ Indisponible' }}
                        </span>
                        <span :class="[
                            'text-xs px-2 py-1 rounded',
                            rider.is_verified ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800'
                        ]">
                            {{ rider.is_verified ? '✓ Vérifié' : 'En attente' }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Empty State -->
            <div v-if="riders.length === 0" class="col-span-full">
                <div class="text-center py-16">
                    <TruckIcon class="h-16 w-16 text-gray-300 mx-auto mb-4" />
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">
                        Aucun livreur
                    </h3>
                    <p class="text-gray-600 mb-6">
                        Commencez par ajouter votre premier livreur
                    </p>
                    <button @click="showModal = true" class="btn-primary">
                        Ajouter un livreur
                    </button>
                </div>
            </div>
        </div>

        <!-- Modal Add Rider -->
        <Teleport to="body">
            <div
                v-if="showModal"
                class="fixed inset-0 z-50 overflow-y-auto"
            >
                <div
                    @click="showModal = false"
                    class="fixed inset-0 bg-black bg-opacity-50"
                />

                <div class="flex min-h-full items-center justify-center p-4">
                    <div class="relative bg-white rounded-lg shadow-xl max-w-2xl w-full">
                        <!-- Header -->
                        <div class="border-b p-6">
                            <h2 class="text-xl font-bold">Ajouter un livreur</h2>
                        </div>

                        <!-- Form -->
                        <form @submit.prevent="submit" class="p-6">
                            <div class="grid md:grid-cols-2 gap-4">
                                <!-- Name -->
                                <div class="md:col-span-2">
                                    <label class="label">Nom complet *</label>
                                    <input
                                        v-model="form.name"
                                        type="text"
                                        class="input-field"
                                        placeholder="Mohammed Alaoui"
                                        required
                                    />
                                    <p v-if="form.errors.name" class="error-message">{{ form.errors.name }}</p>
                                </div>

                                <!-- Email -->
                                <div>
                                    <label class="label">Email *</label>
                                    <input
                                        v-model="form.email"
                                        type="email"
                                        class="input-field"
                                        placeholder="mohammed@example.com"
                                        required
                                    />
                                    <p v-if="form.errors.email" class="error-message">{{ form.errors.email }}</p>
                                </div>

                                <!-- Phone -->
                                <div>
                                    <label class="label">Téléphone *</label>
                                    <input
                                        v-model="form.phone"
                                        type="tel"
                                        placeholder="+212 6XX XXX XXX"
                                        class="input-field"
                                        required
                                    />
                                    <p v-if="form.errors.phone" class="error-message">{{ form.errors.phone }}</p>
                                </div>

                                <!-- Vehicle Type -->
                                <div>
                                    <label class="label">Type de véhicule *</label>
                                    <select v-model="form.vehicle_type" class="input-field" required>
                                        <option value="Moto">🏍️ Moto</option>
                                        <option value="Vélo">🚴 Vélo</option>
                                        <option value="Voiture">🚗 Voiture</option>
                                    </select>
                                    <p v-if="form.errors.vehicle_type" class="error-message">{{ form.errors.vehicle_type }}</p>
                                </div>

                                <!-- Vehicle Plate -->
                                <div>
                                    <label class="label">Plaque d'immatriculation</label>
                                    <input
                                        v-model="form.vehicle_plate"
                                        type="text"
                                        placeholder="12345-أ-12"
                                        class="input-field"
                                    />
                                    <p class="text-xs text-gray-500 mt-1">
                                        Format marocain : 12345-أ-12
                                    </p>
                                    <p v-if="form.errors.vehicle_plate" class="error-message">{{ form.errors.vehicle_plate }}</p>
                                </div>

                                <!-- License Number -->
                                <div class="md:col-span-2">
                                    <label class="label">Numéro de permis de conduire</label>
                                    <input
                                        v-model="form.license_number"
                                        type="text"
                                        placeholder="A123456"
                                        class="input-field"
                                    />
                                    <p v-if="form.errors.license_number" class="error-message">{{ form.errors.license_number }}</p>
                                </div>

                                <!-- Info -->
                                <div class="md:col-span-2 bg-blue-50 border border-blue-200 rounded-lg p-4">
                                    <p class="text-sm text-blue-800">
                                        <strong>ℹ️ Information importante :</strong>
                                    </p>
                                    <ul class="text-sm text-blue-700 mt-2 space-y-1 list-disc list-inside">
                                        <li>Un compte utilisateur sera automatiquement créé</li>
                                        <li>Mot de passe par défaut : <code class="bg-blue-100 px-2 py-1 rounded font-mono">password</code></li>
                                        <li>Le livreur pourra se connecter avec son email</li>
                                        <li>Il devra changer son mot de passe lors de la première connexion</li>
                                    </ul>
                                </div>
                            </div>

                            <!-- Actions -->
                            <div class="flex justify-end space-x-3 mt-6 pt-6 border-t">
                                <button
                                    type="button"
                                    @click="showModal = false; form.reset()"
                                    class="btn-secondary"
                                >
                                    Annuler
                                </button>
                                <button
                                    type="submit"
                                    :disabled="form.processing"
                                    class="btn-primary"
                                >
                                    {{ form.processing ? 'Création en cours...' : 'Créer le livreur' }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </Teleport>
    </AdminLayout>
</template>
