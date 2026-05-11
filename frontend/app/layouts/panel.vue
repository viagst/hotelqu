<template>
  <div class="min-h-screen flex bg-gray-200" style="font-family: Arial, Helvetica, sans-serif;">
    <!-- Sidebar Left -->
    <aside class="w-64 bg-gradient-to-b from-gray-800 to-gray-500 text-white border-r-4 border-gray-800 flex-col hidden lg:flex fixed inset-y-0 left-0 z-40">
      <div class="p-4 border-b-4 border-gray-800 bg-gray-700 text-center">
        <NuxtLink to="/" class="text-xl font-black text-white hover:text-yellow-300 uppercase tracking-widest block">
          HOTELQU VIUL<br>(HVL)
        </NuxtLink>
        <div class="mt-2 text-xs text-yellow-300 font-bold uppercase border-t border-gray-500 pt-2">
          {{ panelTitle }}
        </div>
      </div>

      <nav class="flex-1 overflow-y-auto py-2">
        <NuxtLink v-for="item in menuItems" :key="item.to" :to="item.to"
          class="block px-4 py-3 text-sm font-bold text-white hover:bg-gray-900 border-b border-gray-700"
          :class="route.path === item.to ? 'bg-gray-900 text-yellow-300 border-l-4 border-yellow-300' : ''">
          &gt;&gt; {{ item.label }}
        </NuxtLink>
      </nav>

      <div class="p-4 border-t-4 border-gray-800 bg-gray-700 text-center">
        <p class="text-xs mb-3 text-gray-200">Login User:<br><b class="text-white text-sm">{{ user?.nama_user }}</b></p>
        <button @click="handleLogout" class="w-full bg-gray-600 hover:bg-gray-700 text-white px-3 py-2 text-sm font-bold uppercase border border-gray-800" style="box-shadow: 2px 2px 0px #000;">LOGOUT</button>
      </div>
    </aside>

    <!-- Mobile Header -->
    <div class="lg:hidden fixed top-0 w-full bg-gradient-to-b from-gray-800 to-gray-500 text-white border-b-4 border-gray-800 p-4 z-50 flex justify-between items-center">
       <span class="font-bold text-sm tracking-wider">HVL | {{ panelTitle }}</span>
       <button @click="handleLogout" class="bg-gray-600 px-3 py-1 text-xs font-bold border border-gray-800">LOGOUT</button>
    </div>

    <!-- Content Right Side -->
    <main class="flex-1 lg:ml-64 pt-16 lg:pt-0 min-h-screen flex flex-col">
      <div class="flex-1 p-6">
        <div class="bg-white border border-gray-400 p-6" style="box-shadow: 5px 5px 0px #999;">
          <slot />
        </div>
      </div>
      
      <footer class="text-center py-4 text-xs text-gray-700 font-bold border-t border-gray-400 bg-gray-300">
        &copy; {{ new Date().getFullYear() }} Sistem Informasi Manajemen Hotelqu Viul.<br>
        All Rights Reserved.
      </footer>
    </main>
  </div>
</template>

<script setup lang="ts">
const route = useRoute()
const { user, isAdmin, isResepsionis, logout } = useAuth()

const panelTitle = computed(() => isAdmin.value ? 'ADMINISTRATOR' : 'RESEPSIONIS')

const adminMenu = [
  { to: '/admin/dashboard', label: 'Dashboard' },
  { to: '/admin/tipe-kamar', label: 'Data Tipe Kamar' },
  { to: '/admin/kamar', label: 'Data Kamar' },
  { to: '/admin/users', label: 'Manajemen User' },
  { to: '/admin/laporan', label: 'Laporan' },
  { to: '/admin/profil', label: 'Profil Saya' },
]

const resepsionisMenu = [
  { to: '/resepsionis/dashboard', label: 'Dashboard' },
  { to: '/resepsionis/pemesanan', label: 'Kelola Pemesanan' },
  { to: '/resepsionis/laporan', label: 'Laporan' },
  { to: '/resepsionis/profil', label: 'Profil Saya' },
]

const menuItems = computed(() => isAdmin.value ? adminMenu : resepsionisMenu)
const handleLogout = () => logout()
</script>


