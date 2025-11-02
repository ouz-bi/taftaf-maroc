<script setup>
import { router } from '@inertiajs/vue3'

defineProps({
    links: Array
})
</script>

<template>
    <nav class="flex items-center justify-center space-x-2">
        <button
            v-for="link in links"
            :key="link.label"
            @click="link.url && router.visit(link.url)"
            :disabled="!link.url"
            :class="[
                'px-4 py-2 text-sm font-medium rounded-lg transition',
                link.active
                    ? 'bg-primary-600 text-white'
                    : 'bg-white text-gray-700 hover:bg-gray-100 border border-gray-300',
                !link.url && 'opacity-50 cursor-not-allowed'
            ]"
            v-html="link.label"
        />
    </nav>
</template>
