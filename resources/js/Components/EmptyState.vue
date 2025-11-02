<script setup>
defineProps({
    icon: String,
    title: String,
    description: String,
    actionText: String,
    actionLink: String
})
</script>

<template>
    <div class="text-center py-16">
        <div class="text-6xl mb-4">{{ icon }}</div>
        <h3 class="text-xl font-semibold text-gray-900 mb-2">
            {{ title }}
        </h3>
        <p class="text-gray-600 mb-6">
            {{ description }}
        </p>
        <Link
            v-if="actionText && actionLink"
            :href="actionLink"
            class="btn-primary inline-block"
        >
            {{ actionText }}
        </Link>
    </div>
</template>
