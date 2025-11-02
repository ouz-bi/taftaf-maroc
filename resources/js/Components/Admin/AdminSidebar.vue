<template>
  <div class="flex flex-col h-0 flex-1 bg-gray-800">
    <!-- Logo -->
    <div class="flex items-center justify-center h-16 flex-shrink-0 px-4 bg-gray-900">
      <Link href="/admin/dashboard" class="flex items-center space-x-2">
        <img src="/images/logo-white.png" alt="TafTaf Maroc" class="h-8 w-auto" />
        <span class="text-white font-bold text-lg">TafTaf Admin</span>
      </Link>
    </div>

    <!-- Navigation -->
    <div class="flex-1 flex flex-col overflow-y-auto">
      <nav class="flex-1 px-2 py-4 space-y-1">
        <!-- Dashboard -->
        <SidebarLink
          href="/admin/dashboard"
          :active="$page.url === '/admin/dashboard'"
          icon="ChartBarIcon"
        >
          Tableau de bord
        </SidebarLink>

        <!-- Gestion des produits -->
        <SidebarDropdown
          title="Produits"
          icon="ShoppingBagIcon"
          :active="$page.url.startsWith('/admin/products') || $page.url.startsWith('/admin/categories')"
        >
          <SidebarLink
            href="/admin/products"
            :active="$page.url === '/admin/products'"
          >
            Tous les produits
          </SidebarLink>
          <SidebarLink
            href="/admin/products/create"
            :active="$page.url === '/admin/products/create'"
          >
            Ajouter un produit
          </SidebarLink>
          <SidebarLink
            href="/admin/categories"
            :active="$page.url.startsWith('/admin/categories')"
          >
            Catégories
          </SidebarLink>
        </SidebarDropdown>

        <!-- Commandes -->
        <SidebarLink
          href="/admin/orders"
          :active="$page.url.startsWith('/admin/orders')"
          icon="ClipboardDocumentListIcon"
        >
          Commandes
          <span v-if="pendingOrders > 0" class="ml-auto bg-red-500 text-white text-xs rounded-full px-2 py-1">
            {{ pendingOrders }}
          </span>
        </SidebarLink>

        <!-- Livreurs & Livraison -->
        <SidebarDropdown
          title="Livraison"
          icon="TruckIcon"
          :active="$page.url.startsWith('/admin/riders') || $page.url.startsWith('/admin/delivery')"
        >
          <SidebarLink
            href="/admin/riders"
            :active="$page.url === '/admin/riders'"
          >
            Livreurs
          </SidebarLink>
          <SidebarLink
            href="/admin/delivery/zones"
            :active="$page.url.startsWith('/admin/delivery/zones')"
          >
            Zones de livraison
          </SidebarLink>
          <SidebarLink
            href="/admin/delivery/tracking"
            :active="$page.url.startsWith('/admin/delivery/tracking')"
          >
            Tracking en direct
          </SidebarLink>
        </SidebarDropdown>

        <!-- Clients -->
        <SidebarLink
          href="/admin/users"
          :active="$page.url.startsWith('/admin/users')"
          icon="UsersIcon"
        >
          Clients
        </SidebarLink>

        <!-- Analytics -->
        <SidebarDropdown
          title="Analytics"
          icon="ChartPieIcon"
          :active="$page.url.startsWith('/admin/stats')"
        >
          <SidebarLink
            href="/admin/stats/sales"
            :active="$page.url === '/admin/stats/sales'"
          >
            Ventes
          </SidebarLink>
          <SidebarLink
            href="/admin/stats/products"
            :active="$page.url === '/admin/stats/products'"
          >
            Produits
          </SidebarLink>
          <SidebarLink
            href="/admin/stats/customers"
            :active="$page.url === '/admin/stats/customers'"
          >
            Clients
          </SidebarLink>
        </SidebarDropdown>

        <!-- Paramètres -->
        <SidebarDropdown
          title="Paramètres"
          icon="Cog6ToothIcon"
          :active="$page.url.startsWith('/admin/settings')"
        >
          <SidebarLink
            href="/admin/settings/general"
            :active="$page.url === '/admin/settings/general'"
          >
            Général
          </SidebarLink>
          <SidebarLink
            href="/admin/settings/payment"
            :active="$page.url === '/admin/settings/payment'"
          >
            Paiements
          </SidebarLink>
          <SidebarLink
            href="/admin/settings/delivery"
            :active="$page.url === '/admin/settings/delivery'"
          >
            Livraison
          </SidebarLink>
        </SidebarDropdown>
      </nav>

      <!-- User Section -->
      <div class="flex-shrink-0 flex border-t border-gray-700 p-4">
        <div class="flex items-center">
          <div class="flex-shrink-0">
            <img
              class="h-10 w-10 rounded-full"
              :src="$page.props.auth.user.avatar || '/images/avatar-placeholder.png'"
              alt=""
            />
          </div>
          <div class="ml-3">
            <p class="text-base font-medium text-white">
              {{ $page.props.auth.user.name }}
            </p>
            <Link
              href="/admin/profile"
              class="text-sm font-medium text-gray-400 hover:text-white"
            >
              Voir le profil
            </Link>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { Link } from '@inertiajs/vue3'
import SidebarLink from '@/Components/Admin/SidebarLink.vue'
import SidebarDropdown from '@/Components/Admin/SidebarDropdown.vue'

defineProps({
  pendingOrders: {
    type: Number,
    default: 0
  }
})
</script>
