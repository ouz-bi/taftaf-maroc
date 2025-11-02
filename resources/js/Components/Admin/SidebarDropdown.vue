<template>
  <div>
    <button
      @click="open = !open"
      :class="[
        'group w-full flex items-center px-2 py-2 text-sm font-medium rounded-md',
        active
          ? 'bg-gray-900 text-white'
          : 'text-gray-300 hover:bg-gray-700 hover:text-white'
      ]"
    >
      <component
        :is="resolveIcon(icon)"
        :class="[
          'mr-3 flex-shrink-0 h-6 w-6',
          active ? 'text-white' : 'text-gray-400 group-hover:text-white'
        ]"
      />
      <span class="flex-1 text-left"><slot name="title" /></span>
      <ChevronDownIcon
        :class="[
          'ml-1 h-5 w-5 transform transition-transform',
          open ? 'rotate-180' : 'rotate-0'
        ]"
      />
    </button>

    <div v-show="open" class="mt-1 space-y-1 pl-11">
      <slot />
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { ChevronDownIcon } from '@heroicons/vue/24/outline'
import {
  ShoppingBagIcon,
  TruckIcon,
  ChartPieIcon,
  Cog6ToothIcon
} from '@heroicons/vue/24/outline'

defineProps({
  title: {
    type: String,
    required: true
  },
  active: {
    type: Boolean,
    default: false
  },
  icon: {
    type: String,
    required: true
  }
})

const open = ref(false)

const resolveIcon = (iconName) => {
  const icons = {
    ShoppingBagIcon,
    TruckIcon,
    ChartPieIcon,
    Cog6ToothIcon
  }
  return icons[iconName] || ShoppingBagIcon
}
</script>
