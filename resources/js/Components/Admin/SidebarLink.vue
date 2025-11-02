<!-- resources/js/Components/Admin/SidebarLink.vue -->
<template>
  <Link
    :href="href"
    :class="[
      'group flex items-center px-2 py-2 text-sm font-medium rounded-md',
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
    <span class="flex-1"><slot /></span>
  </Link>
</template>

<script setup>
import { Link } from '@inertiajs/vue3'
import {
  ChartBarIcon,
  ShoppingBagIcon,
  ClipboardDocumentListIcon,
  TruckIcon,
  UsersIcon,
  ChartPieIcon,
  Cog6ToothIcon
} from '@heroicons/vue/24/outline'

defineProps({
  href: {
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

const resolveIcon = (iconName) => {
  const icons = {
    ChartBarIcon,
    ShoppingBagIcon,
    ClipboardDocumentListIcon,
    TruckIcon,
    UsersIcon,
    ChartPieIcon,
    Cog6ToothIcon
  }
  return icons[iconName] || ChartBarIcon
}
</script>
