<script setup>
defineProps({
    size: {
        type: String,
        default: 'md'
    }
})

const sizeClasses = {
    sm: 'h-4 w-4',
    md: 'h-6 w-6',
    lg: 'h-8 w-8',
    xl: 'h-12 w-12'
}
</script>

<template>
    <div class="flex items-center justify-center">
        <div :class="[sizeClasses[size], 'spinner']" />
    </div>
</template>
