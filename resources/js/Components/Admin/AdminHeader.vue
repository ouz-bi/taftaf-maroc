<template>
  <header class="bg-white shadow-sm border-b border-gray-200">
    <div class="flex justify-between items-center px-4 sm:px-6 md:px-8 h-16">
      <!-- Left Section -->
      <div class="flex items-center">
        <!-- Mobile menu button -->
        <button
          type="button"
          class="md:hidden bg-white p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-indigo-500"
          @click="$emit('toggle-sidebar')"
        >
          <Bars3Icon class="h-6 w-6" />
        </button>

        <!-- Breadcrumb -->
        <nav class="hidden md:flex ml-4" aria-label="Breadcrumb">
          <ol class="flex items-center space-x-4">
            <li>
              <div class="flex">
                <Link
                  href="/admin/dashboard"
                  class="text-sm font-medium text-gray-500 hover:text-gray-700"
                >
                  Admin
                </Link>
              </div>
            </li>
            <li v-for="(item, index) in breadcrumbs" :key="index">
              <div class="flex items-center">
                <ChevronRightIcon class="flex-shrink-0 h-5 w-5 text-gray-400" />
                <Link
                  :href="item.href"
                  class="ml-4 text-sm font-medium text-gray-500 hover:text-gray-700"
                  :class="{ 'text-gray-700': index === breadcrumbs.length - 1 }"
                >
                  {{ item.name }}
                </Link>
              </div>
            </li>
          </ol>
        </nav>
      </div>

      <!-- Right Section -->
      <div class="flex items-center space-x-4">
        <!-- Notifications -->
        <div class="relative">
          <button
            @click="notificationsOpen = !notificationsOpen"
            class="bg-white p-1 rounded-full text-gray-400 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
          >
            <BellIcon class="h-6 w-6" />
            <span
              v-if="unreadNotifications > 0"
              class="absolute top-0 right-0 block h-2 w-2 rounded-full bg-red-400 ring-2 ring-white"
            ></span>
          </button>

          <!-- Notifications Dropdown -->
          <div
            v-if="notificationsOpen"
            class="origin-top-right absolute right-0 mt-2 w-80 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 focus:outline-none z-50"
          >
            <div class="py-1">
              <div class="px-4 py-2 border-b border-gray-100">
                <h3 class="text-sm font-semibold text-gray-900">Notifications</h3>
              </div>
              <div class="max-h-96 overflow-y-auto">
                <div
                  v-for="notification in notifications"
                  :key="notification.id"
                  class="px-4 py-3 hover:bg-gray-50 border-b border-gray-100 last:border-b-0"
                >
                  <div class="flex items-start">
                    <div
                      class="flex-shrink-0 pt-0.5"
                      :class="{
                        'text-green-500': notification.type === 'success',
                        'text-blue-500': notification.type === 'info',
                        'text-yellow-500': notification.type === 'warning',
                        'text-red-500': notification.type === 'error'
                      }"
                    >
                      <component
                        :is="getNotificationIcon(notification.type)"
                        class="h-5 w-5"
                      />
                    </div>
                    <div class="ml-3 flex-1">
                      <p class="text-sm font-medium text-gray-900">
                        {{ notification.title }}
                      </p>
                      <p class="text-sm text-gray-500">
                        {{ notification.message }}
                      </p>
                      <p class="text-xs text-gray-400 mt-1">
                        {{ formatTime(notification.created_at) }}
                      </p>
                    </div>
                  </div>
                </div>

                <div v-if="notifications.length === 0" class="px-4 py-8 text-center">
                  <BellSlashIcon class="mx-auto h-8 w-8 text-gray-400" />
                  <p class="mt-2 text-sm text-gray-500">Aucune notification</p>
                </div>
              </div>
              <div class="px-4 py-2 border-t border-gray-100">
                <Link
                  href="/admin/notifications"
                  class="block text-center text-sm font-medium text-indigo-600 hover:text-indigo-500"
                >
                  Voir toutes les notifications
                </Link>
              </div>
            </div>
          </div>
        </div>

        <!-- Search -->
        <div class="relative">
          <button
            @click="searchOpen = !searchOpen"
            class="bg-white p-1 rounded-full text-gray-400 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
          >
            <MagnifyingGlassIcon class="h-6 w-6" />
          </button>

          <!-- Search Dropdown -->
          <div
            v-if="searchOpen"
            class="origin-top-right absolute right-0 mt-2 w-96 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 focus:outline-none z-50"
          >
            <div class="p-2">
              <input
                type="text"
                v-model="searchQuery"
                placeholder="Rechercher produits, commandes, clients..."
                class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                @keyup.enter="performSearch"
              />
            </div>
          </div>
        </div>

        <!-- User Menu -->
        <div class="relative">
          <button
            @click="userMenuOpen = !userMenuOpen"
            class="flex items-center text-sm rounded-full focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
          >
            <span class="sr-only">Ouvrir le menu utilisateur</span>
            <img
              class="h-8 w-8 rounded-full"
              :src="$page.props.auth.user.avatar || '/images/avatar-placeholder.png'"
              alt=""
            />
            <span class="hidden md:block ml-2 text-sm font-medium text-gray-700">
              {{ $page.props.auth.user.name }}
            </span>
            <ChevronDownIcon class="hidden md:block ml-1 h-5 w-5 text-gray-400" />
          </button>

          <!-- User Dropdown -->
          <div
            v-if="userMenuOpen"
            class="origin-top-right absolute right-0 mt-2 w-48 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 focus:outline-none z-50"
          >
            <div class="py-1">
              <Link
                href="/admin/profile"
                class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
              >
                Mon profil
              </Link>
              <Link
                href="/"
                class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
              >
                Voir le site
              </Link>
              <div class="border-t border-gray-100"></div>
              <form @submit.prevent="logout">
                <button
                  type="submit"
                  class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                >
                  Se déconnecter
                </button>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </header>
</template>

<script setup>
import { ref, computed } from 'vue'
import { Link, router } from '@inertiajs/vue3'
import {
  Bars3Icon,
  BellIcon,
  BellSlashIcon,
  MagnifyingGlassIcon,
  ChevronDownIcon,
  ChevronRightIcon,
  CheckCircleIcon,
  InformationCircleIcon,
  ExclamationTriangleIcon,
  XCircleIcon
} from '@heroicons/vue/24/outline'

defineEmits(['toggle-sidebar'])

// States
const notificationsOpen = ref(false)
const searchOpen = ref(false)
const userMenuOpen = ref(false)
const searchQuery = ref('')

// Données de démonstration
const notifications = ref([
  {
    id: 1,
    type: 'success',
    title: 'Nouvelle commande',
    message: 'Commande #1234 passée par Ahmed Hassan',
    created_at: new Date(Date.now() - 5 * 60 * 1000) // 5 minutes ago
  },
  {
    id: 2,
    type: 'warning',
    title: 'Stock faible',
    message: 'Produit "Couscous" bientôt en rupture',
    created_at: new Date(Date.now() - 2 * 60 * 60 * 1000) // 2 hours ago
  }
])

const unreadNotifications = computed(() => notifications.value.length)

// Breadcrumbs dynamiques
const breadcrumbs = computed(() => {
  const paths = $page.url.split('/').filter(path => path)
  const crumbs = []

  let currentPath = ''
  paths.forEach((path, index) => {
    currentPath += `/${path}`
    crumbs.push({
      name: formatBreadcrumbName(path),
      href: currentPath
    })
  })

  return crumbs
})

// Méthodes
const getNotificationIcon = (type) => {
  const icons = {
    success: CheckCircleIcon,
    info: InformationCircleIcon,
    warning: ExclamationTriangleIcon,
    error: XCircleIcon
  }
  return icons[type] || InformationCircleIcon
}

const formatTime = (date) => {
  const now = new Date()
  const diff = now - new Date(date)
  const minutes = Math.floor(diff / 60000)
  const hours = Math.floor(diff / 3600000)
  const days = Math.floor(diff / 86400000)

  if (days > 0) return `Il y a ${days} jour${days > 1 ? 's' : ''}`
  if (hours > 0) return `Il y a ${hours} heure${hours > 1 ? 's' : ''}`
  if (minutes > 0) return `Il y a ${minutes} minute${minutes > 1 ? 's' : ''}`
  return 'À l\'instant'
}

const formatBreadcrumbName = (path) => {
  const names = {
    admin: 'Admin',
    dashboard: 'Tableau de bord',
    products: 'Produits',
    orders: 'Commandes',
    users: 'Clients',
    categories: 'Catégories',
    riders: 'Livreurs',
    settings: 'Paramètres',
    create: 'Créer',
    edit: 'Modifier'
  }
  return names[path] || path.charAt(0).toUpperCase() + path.slice(1)
}

const performSearch = () => {
  if (searchQuery.value.trim()) {
    router.get('/admin/search', { q: searchQuery.value })
    searchOpen.value = false
    searchQuery.value = ''
  }
}

const logout = () => {
  router.post(route('logout'))
}
</script>
