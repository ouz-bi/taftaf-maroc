<script setup>
import { ref } from 'vue'
import { useForm } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { PlusIcon, PencilIcon, TrashIcon, ArrowUpIcon, ArrowDownIcon } from '@heroicons/vue/24/outline'

const props = defineProps({
    categories: Array,
})

const showModal = ref(false)
const editingCategory = ref(null)

const form = useForm({
    name: '',
    slug: '',
    icon: '📦',
    description: '',
    order: 0,
    is_active: true,
})

const openCreateModal = () => {
    editingCategory.value = null
    form.reset()
    form.order = props.categories.length + 1
    showModal.value = true
}

const openEditModal = (category) => {
    editingCategory.value = category
    form.name = category.name
    form.slug = category.slug
    form.icon = category.icon
    form.description = category.description
    form.order = category.order
    form.is_active = category.is_active
    showModal.value = true
}

const submit = () => {
    if (editingCategory.value) {
        form.put(`/admin/categories/${editingCategory.value.id}`, {
            onSuccess: () => {
                showModal.value = false
                form.reset()
            }
        })
    } else {
        form.post('/admin/categories', {
            onSuccess: () => {
                showModal.value = false
                form.reset()
            }
        })
    }
}

const deleteCategory = (categoryId) => {
    if (confirm('Êtes-vous sûr de vouloir supprimer cette catégorie ?')) {
        form.delete(`/admin/categories/${categoryId}`)
    }
}

const generateSlug = () => {
    form.slug = form.name
        .toLowerCase()
        .normalize('NFD')
        .replace(/[\u0300-\u036f]/g, '')
        .replace(/[^a-z0-9]+/g, '-')
        .replace(/(^-|-$)/g, '')
}

const emojiList = ['🍔', '🍺', '🛒', '🥤', '☕', '🏠', '💄', '💊', '🎮', '📱', '🚗', '👕', '📚', '🎵']
</script>

<template>
    <AdminLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h1 class="text-2xl font-bold text-gray-900">Gestion des Catégories</h1>
                <button @click="openCreateModal" class="btn-primary flex items-center space-x-2">
                    <PlusIcon class="h-5 w-5" />
                    <span>Nouvelle catégorie</span>
                </button>
            </div>
        </template>

        <!-- Categories List -->
        <div class="card overflow-hidden">
            <table class="table">
                <thead class="table-header">
                    <tr>
                        <th class="table-th">Ordre</th>
                        <th class="table-th">Icône</th>
                        <th class="table-th">Nom</th>
                        <th class="table-th">Slug</th>
                        <th class="table-th">Produits</th>
                        <th class="table-th">Statut</th>
                        <th class="table-th">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    <tr
                        v-for="category in categories"
                        :key="category.id"
                        class="hover:bg-gray-50"
                    >
                        <td class="table-td">
                            <div class="flex items-center space-x-2">
                                <span class="font-medium">{{ category.order }}</span>
                            </div>
                        </td>
                        <td class="table-td">
                            <span class="text-3xl">{{ category.icon }}</span>
                        </td>
                        <td class="table-td">
                            <div>
                                <p class="font-semibold">{{ category.name }}</p>
                                <p v-if="category.description" class="text-xs text-gray-500 line-clamp-1">
                                    {{ category.description }}
                                </p>
                            </div>
                        </td>
                        <td class="table-td">
                            <code class="text-xs bg-gray-100 px-2 py-1 rounded">{{ category.slug }}</code>
                        </td>
                        <td class="table-td">
                            <span class="badge-primary">{{ category.products_count }} produits</span>
                        </td>
                        <td class="table-td">
                            <span :class="category.is_active ? 'badge-success' : 'badge-danger'">
                                {{ category.is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td class="table-td">
                            <div class="flex space-x-2">
                                <button
                                    @click="openEditModal(category)"
                                    class="btn-secondary btn-sm"
                                >
                                    <PencilIcon class="h-4 w-4" />
                                </button>
                                <button
                                    @click="deleteCategory(category.id)"
                                    class="btn-danger btn-sm"
                                    :disabled="category.products_count > 0"
                                >
                                    <TrashIcon class="h-4 w-4" />
                                </button>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Modal Create/Edit -->
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
                            <h2 class="text-xl font-bold">
                                {{ editingCategory ? 'Modifier la catégorie' : 'Nouvelle catégorie' }}
                            </h2>
                        </div>

                        <!-- Form -->
                        <form @submit.prevent="submit" class="p-6">
                            <div class="space-y-4">
                                <!-- Icon Selector -->
                                <div>
                                    <label class="label">Icône</label>
                                    <div class="flex flex-wrap gap-2">
                                        <button
                                            v-for="emoji in emojiList"
                                            :key="emoji"
                                            type="button"
                                            @click="form.icon = emoji"
                                            :class="[
                                                'text-3xl p-3 rounded-lg border-2 transition',
                                                form.icon === emoji
                                                    ? 'border-primary-600 bg-primary-50'
                                                    : 'border-gray-200 hover:border-primary-300'
                                            ]"
                                        >
                                            {{ emoji }}
                                        </button>
                                    </div>
                                </div>

                                <!-- Name -->
                                <div>
                                    <label class="label">Nom *</label>
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
                                <div>
                                    <label class="label">Slug (URL) *</label>
                                    <input
                                        v-model="form.slug"
                                        type="text"
                                        class="input-field"
                                        required
                                    />
                                    <p class="text-xs text-gray-500 mt-1">
                                        URL: /products?category={{ form.slug || 'nom-categorie' }}
                                    </p>
                                    <p v-if="form.errors.slug" class="error-message">{{ form.errors.slug }}</p>
                                </div>

                                <!-- Description -->
                                <div>
                                    <label class="label">Description</label>
                                    <textarea
                                        v-model="form.description"
                                        rows="3"
                                        class="input-field"
                                    />
                                </div>

                                <!-- Order -->
                                <div>
                                    <label class="label">Ordre d'affichage *</label>
                                    <input
                                        v-model.number="form.order"
                                        type="number"
                                        class="input-field"
                                        required
                                    />
                                    <p class="text-xs text-gray-500 mt-1">
                                        Plus le nombre est petit, plus la catégorie apparaît en premier
                                    </p>
                                </div>

                                <!-- Active -->
                                <div>
                                    <label class="flex items-center cursor-pointer">
                                        <input
                                            v-model="form.is_active"
                                            type="checkbox"
                                            class="w-4 h-4 text-primary-600 border-gray-300 rounded"
                                        />
                                        <span class="ml-2 text-sm">Catégorie active</span>
                                    </label>
                                </div>
                            </div>

                            <!-- Actions -->
                            <div class="flex justify-end space-x-3 mt-6 pt-6 border-t">
                                <button
                                    type="button"
                                    @click="showModal = false"
                                    class="btn-secondary"
                                >
                                    Annuler
                                </button>
                                <button
                                    type="submit"
                                    :disabled="form.processing"
                                    class="btn-primary"
                                >
                                    {{ form.processing ? 'Enregistrement...' : editingCategory ? 'Mettre à jour' : 'Créer' }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </Teleport>
    </AdminLayout>
</template>
