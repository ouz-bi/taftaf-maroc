<script setup>
import { computed } from 'vue'
import { Link, usePage } from '@inertiajs/vue3'
import { ShoppingCartIcon } from '@heroicons/vue/24/outline'

const page = usePage()
const cartCount = computed(() => page.props.cart_count || 0)
</script>

<template>
    <Link href="/cart" class="relative p-2 hover:bg-gray-100 rounded-lg transition">
        <ShoppingCartIcon class="h-6 w-6 text-gray-700" />
        <span
            v-if="cartCount > 0"
            class="cart-badge animate-pulse"
        >
            {{ cartCount > 99 ? '99+' : cartCount }}
        </span>
    </Link>
</template>
