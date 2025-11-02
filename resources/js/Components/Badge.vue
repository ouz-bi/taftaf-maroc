<script setup>
defineProps({
    type: {
        type: String,
        default: 'primary'
    },
    size: {
        type: String,
        default: 'md'
    }
})

const typeClasses = {
    primary: 'badge-primary',
    success: 'badge-success',
    warning: 'badge-warning',
    danger: 'badge-danger',
    info: 'badge-info'
}

const sizeClasses = {
    sm: 'text-xs px-2 py-0.5',
    md: 'text-sm px-2.5 py-0.5',
    lg: 'text-base px-3 py-1'
}
</script>

<template>
    <span :class="[typeClasses[type], sizeClasses[size]]">
        <slot />
    </span>
</template>
