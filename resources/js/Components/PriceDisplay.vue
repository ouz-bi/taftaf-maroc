<script setup>
const props = defineProps({
    price: Number,
    promoPrice: Number,
    size: {
        type: String,
        default: 'md'
    }
})

const sizeClasses = {
    sm: 'text-sm',
    md: 'text-lg',
    lg: 'text-2xl',
    xl: 'text-3xl'
}

const currentPrice = props.promoPrice || props.price
const hasPromo = props.promoPrice && props.promoPrice < props.price
</script>

<template>
    <div class="flex items-center space-x-2">
        <span :class="[sizeClasses[size], 'font-bold text-primary-600']">
            {{ currentPrice.toFixed(2) }} DH
        </span>
        <span
            v-if="hasPromo"
            :class="[size === 'sm' ? 'text-xs' : 'text-sm', 'text-gray-500 line-through']"
        >
            {{ price.toFixed(2) }} DH
        </span>
    </div>
</template>
