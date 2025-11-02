<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue'
import { useForm } from '@inertiajs/vue3'

const form = useForm({
    email: '',
    password: '',
    remember: false,
})

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    })
}
</script>

<template>
    <GuestLayout>
        <div class="min-h-screen flex items-center justify-center bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
            <div class="max-w-md w-full space-y-8">
                <!-- Logo -->
                <div class="text-center">
                    <h2 class="text-4xl font-bold text-primary-600">TafTaf</h2>
                    <p class="mt-2 text-sm text-gray-600">Maroc 🇲🇦</p>
                    <h1 class="mt-6 text-3xl font-bold text-gray-900">
                        Connexion
                    </h1>
                    <p class="mt-2 text-sm text-gray-600">
                        Ou
                        <Link href="/register" class="text-primary-600 hover:text-primary-700 font-medium">
                            créez un compte
                        </Link>
                    </p>
                </div>

                <!-- Form -->
                <form @submit.prevent="submit" class="mt-8 space-y-6">
                    <!-- Email -->
                    <div>
                        <label for="email" class="label">Adresse email</label>
                        <input
                            id="email"
                            v-model="form.email"
                            type="email"
                            required
                            class="input-field"
                            placeholder="vous@exemple.com"
                        />
                        <p v-if="form.errors.email" class="error-message">
                            {{ form.errors.email }}
                        </p>
                    </div>

                    <!-- Password -->
                    <div>
                        <label for="password" class="label">Mot de passe</label>
                        <input
                            id="password"
                            v-model="form.password"
                            type="password"
                            required
                            class="input-field"
                            placeholder="••••••••"
                        />
                        <p v-if="form.errors.password" class="error-message">
                            {{ form.errors.password }}
                        </p>
                    </div>

                    <!-- Remember & Forgot -->
                    <div class="flex items-center justify-between">
                        <label class="flex items-center">
                            <input
                                v-model="form.remember"
                                type="checkbox"
                                class="w-4 h-4 text-primary-600 border-gray-300 rounded"
                            />
                            <span class="ml-2 text-sm text-gray-600">Se souvenir de moi</span>
                        </label>

                        <Link href="/forgot-password" class="text-sm text-primary-600 hover:text-primary-700">
                            Mot de passe oublié ?
                        </Link>
                    </div>

                    <!-- Submit Button -->
                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="btn-primary w-full btn-lg"
                    >
                        {{ form.processing ? 'Connexion...' : 'Se connecter' }}
                    </button>

                    <!-- Divider -->
                    <div class="relative">
                        <div class="absolute inset-0 flex items-center">
                            <div class="w-full border-t border-gray-300"></div>
                        </div>
                        <div class="relative flex justify-center text-sm">
                            <span class="px-2 bg-gray-50 text-gray-500">Comptes de test</span>
                        </div>
                    </div>

                    <!-- Test Accounts Info -->
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 text-sm">
                        <p class="font-semibold text-blue-900 mb-2">Comptes de démonstration :</p>
                        <ul class="space-y-1 text-blue-800">
                            <li>👤 Client : client@taftaf.ma</li>
                            <li>👨‍💼 Admin : admin@taftaf.ma</li>
                            <li>🛵 Livreur : livreur@taftaf.ma</li>
                            <li>🔑 Mot de passe : password</li>
                        </ul>
                    </div>
                </form>
            </div>
        </div>
    </GuestLayout>
</template>
