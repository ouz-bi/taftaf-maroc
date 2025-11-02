<script setup>
import { useForm } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

const props = defineProps({
    categories: Array,
    product: Object, // Pour l'édition
})

const form = useForm({
    category_id: props.product?.category_id || '',
    name: props.product?.name || '',
    slug: props.product?.slug || '',
    description: props.product?.description || '',
    price: props.product?.price || '',
    promo_price: props.product?.promo_price || '',
    stock: props.product?.stock || 0,
    unit: props.product?.unit || 'unité',
    is_available: props.product?.is_available ?? true,
    is_featured: props.product?.is_featured ?? false,
    is_halal: props.product?.is_halal ?? false,
    is_made_in_morocco: props.product?.is_made_in_morocco ?? false,
    image: null,
})

const submit = () => {
    if (props.product) {
        form.put(`/admin/products/${props.product.id}`)
    } else {
        form.post('/admin/products')
    }
}

const generateSlug = () => {
    form.slug = form.name
        .toLowerCase()
        .replace(/[^a-z0-9]+/g, '-')
        .replace(/(^-|-$)/g, '')
}
</script>

<template>
    <AdminLayout>
        <template #header>
            <h1 class="text-2xl font-bold text-gray-900">
                {{ product ? 'Modifier le produit' : 'Nouveau produit' }}
            </h1>
        </template>

        <form @submit.prevent="submit" class="max-w-4xl">
            <div class="card">
                <div class="card-body space-y-6">
                    <!-- Basic Info -->
                    <div class="grid md:grid-cols-2 gap-6">
                        <!-- Name -->
                        <div class="md:col-span-2">
                            <label class="label">Nom du produit *</label>
                            <input
                                v-model="form.name"
                                @input="generateSlug"
                                type="text"
                                class="input-field"
                                required
                            />
                            <p v-if="form.errors.name" class="error-message">{{ form.errors.name }}</p>
                        </div>

                        <!-- Slug -->
                        <div class="md:col-span-2">
                            <label class="label">Slug (URL)</label>
                            <input
                                v-model="form.slug"
                                type="text"
                                class="input-field"
                                required
                            />
                            <p class="text-xs text-gray-500 mt-1">
                                URL: /products/{{ form.slug || 'nom-du-produit' }}
                            </p>
                            <p v-if="form.errors.slug" class="error-message">{{ form.errors.slug }}</p>
                        </div>

                        <!-- Category -->
                        <div>
                            <label class="label">Catégorie *</label>
                            <select v-model="form.category_id" class="input-field" required>
                                <option value="">Sélectionner...</option>
                                <option
                                    v-for="category in categories"
                                    :key="category.id"
                                    :value="category.id"
                                >
                                    {{ category.icon }} {{ category.name }}
                                </option>
                            </select>
                            <p v-if="form.errors.category_id" class="error-message">{{ form.errors.category_id }}</p>
                        </div>

                        <!-- Unit -->
                        <div>
                            <label class="label">Unité</label>
                            <select v-model="form.unit" class="input-field">
                                <option value="unité">Unité</option>
                                <option value="kg">Kilogramme (kg)</option>
                                <option value="g">Gramme (g)</option>
                                <option value="L">Litre (L)</option>
                                <option value="ml">Millilitre (ml)</option>
                                <option value="pack">Pack</option>
                            </select>
                        </div>

                        <!-- Price -->
                        <div>
                            <label class="label">Prix (DH) *</label>
                            <input
                                v-model.number="form.price"
                                type="number"
                                step="0.01"
                                class="input-field"
                                required
                            />
                            <p v-if="form.errors.price" class="error-message">{{ form.errors.price }}</p>
                        </div>

                        <!-- Promo Price -->
                        <div>
                            <label class="label">Prix promo (DH)</label>
                            <input
                                v-model.number="form.promo_price"
                                type="number"
                                step="0.01"
                                class="input-field"
                                placeholder="Optionnel"
                            />
                            <p v-if="form.errors.promo_price" class="error-message">{{ form.errors.promo_price }}</p>
                        </div>

                        <!-- Stock -->
                        <div class="md:col-span-2">
                            <label class="label">Stock *</label>
                            <input
                                v-model.number="form.stock"
                                type="number"
                                class="input-field"
                                required
                            />
                            <p v-if="form.errors.stock" class="error-message">{{ form.errors.stock }}</p>
                        </div>

                        <!-- Description -->
                        <div class="md:col-span-2">
                            <label class="label">Description</label>
                            <textarea
                                v-model="form.description"
                                rows="4"
                                class="input-field"
                            />
                            <p v-if="form.errors.description" class="error-message">{{ form.errors.description }}</p>
                        </div>

                        <!-- Image Upload -->
                        <div class="md:col-span-2">
                            <label class="label">Image du produit</label>
                            <input
                                @input="form.image = $event.target.files[0]"
                                type="file"
                                accept="image/*"
                                class="input-field"
                            />
                            <p class="text-xs text-gray-500 mt-1">
                                Format: JPG, PNG. Taille max: 2MB
                            </p>
                        </div>
                    </div>

                    <!-- Checkboxes -->
                    <div class="border-t pt-6">
                        <h3 class="font-semibold mb-4">Options</h3>
                        <div class="space-y-3">
                            <label class="flex items-center cursor-pointer">
                                <input
                                    v-model="form.is_available"
                                    type="checkbox"
                                    class="w-4 h-4 text-primary-600 border-gray-300 rounded"
                                />
                                <span class="ml-2 text-sm">Produit disponible</span>
                            </label>

                            <label class="flex items-center cursor-pointer">
                                <input
                                    v-model="form.is_featured"
                                    type="checkbox"
                                    class="w-4 h-4 text-primary-600 border-gray-300 rounded"
                                />
                                <span class="ml-2 text-sm">Produit vedette (Featured)</span>
                            </label>

                            <label class="flex items-center cursor-pointer">
                                <input
                                    v-model="form.is_halal"
                                    type="checkbox"
                                    class="w-4 h-4 text-primary-600 border-gray-300 rounded"
                                />
                                <span class="ml-2 text-sm">✓ Certifié Halal</span>
                            </label>

                            <label class="flex items-center cursor-pointer">
                                <input
                                    v-model="form.is_made_in_morocco"
                                    type="checkbox"
                                    class="w-4 h-4 text-primary-600 border-gray-300 rounded"
                                />
                                <span class="ml-2 text-sm">🇲🇦 Fabriqué au Maroc</span>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="card-footer flex justify-between">
                    <Link
                        href="/admin/products"
                        class="btn-secondary"
                    >
                        Annuler
                    </Link>
                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="btn-primary"
                    >
                        {{ form.processing ? 'Enregistrement...' : product ? 'Mettre à jour' : 'Créer le produit' }}
                    </button>
                </div>
            </div>
        </form>
    </AdminLayout>
</template>
