<template>
  <div class="min-h-screen flex flex-col bg-slate-50 font-sans">
    <!-- Navbar (Different Layout: Centered Logo, Links on sides) -->
    <nav class="fixed top-0 left-0 right-0 z-50 bg-gradient-to-r from-gray-700 to-gray-400 shadow-md">
      <div class="max-w-7xl mx-auto px-4">
        <div class="flex items-center justify-between h-16">
          
          <!-- Nav Links Left -->
          <div class="hidden md:flex items-center gap-4">
            <NuxtLink to="/" class="text-white hover:text-gray-200 font-medium">Beranda</NuxtLink>
            <NuxtLink to="/rooms" class="text-white hover:text-gray-200 font-medium">Kamar</NuxtLink>
          </div>

          <!-- Logo Center -->
          <NuxtLink to="/" class="flex-1 flex justify-center text-center">
            <span class="text-2xl font-black text-white tracking-widest uppercase">Hotelqu Viul</span>
          </NuxtLink>

          <!-- Auth / Links Right -->
          <div class="flex items-center gap-3 justify-end flex-1">
            <div class="hidden md:block">
              <NuxtLink to="/cek-pesanan" class="text-white hover:text-gray-200 font-medium mr-4">Cek Pesanan</NuxtLink>
            </div>

            <template v-if="isLoggedIn">
              <div class="relative" ref="dropdownRef">
                <button @click="showDropdown = !showDropdown" class="flex items-center gap-2 px-3 py-1.5 text-gray-800 bg-white rounded font-bold hover:bg-gray-100">
                  {{ user?.nama_user }}
                  <svg class="w-4 h-4 transition-transform" :class="{ 'rotate-180': showDropdown }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                  </svg>
                </button>

                <div v-if="showDropdown" class="absolute right-0 mt-2 w-48 bg-white border border-gray-200 shadow-lg rounded">
                  <div class="p-3 border-b border-gray-100 bg-gray-50">
                    <span class="block text-xs font-semibold text-gray-800 uppercase">{{ user?.role }}</span>
                  </div>
                  <div class="py-1">
                    <NuxtLink v-if="isAdmin" to="/admin/dashboard" @click="showDropdown = false" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Panel Admin</NuxtLink>
                    <NuxtLink v-if="isResepsionis" to="/resepsionis/dashboard" @click="showDropdown = false" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Panel Resepsionis</NuxtLink>
                    <NuxtLink v-if="isTamu" to="/my-bookings" @click="showDropdown = false" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Pesanan Saya</NuxtLink>
                    <button @click="handleLogout" class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-red-50 font-bold">Logout</button>
                  </div>
                </div>
              </div>
            </template>

            <template v-else>
              <NuxtLink to="/login" class="text-white font-bold hover:underline">Masuk</NuxtLink>
              <span class="text-gray-300">|</span>
              <NuxtLink to="/register" class="text-white font-bold hover:underline">Daftar</NuxtLink>
            </template>

            <!-- Mobile menu button -->
            <button @click="showMobile = !showMobile" class="md:hidden p-2 text-white">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path v-if="!showMobile" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
              </svg>
            </button>
          </div>
        </div>
      </div>

      <!-- Mobile menu -->
      <div v-if="showMobile" class="md:hidden bg-gray-700 p-4 space-y-2">
        <NuxtLink to="/" @click="showMobile = false" class="block text-white font-medium pb-2 border-b border-gray-500">Beranda</NuxtLink>
        <NuxtLink to="/rooms" @click="showMobile = false" class="block text-white font-medium pb-2 border-b border-gray-500">Kamar</NuxtLink>
        <NuxtLink to="/cek-pesanan" @click="showMobile = false" class="block text-white font-medium pb-2 border-b border-gray-500">Cek Pesanan</NuxtLink>
      </div>
    </nav>

    <!-- Content -->
    <main class="flex-1 pt-16">
      <slot />
    </main>

    <!-- Footer (Different structure, simple centered) -->
    <footer class="bg-gradient-to-b from-gray-800 to-gray-500 text-white py-10 mt-auto">
      <div class="max-w-4xl mx-auto px-4 text-center">
        <h2 class="text-3xl font-black uppercase tracking-widest mb-4">Hotelqu Viul</h2>
        <div class="flex justify-center gap-6 mb-6 text-sm font-semibold">
          <NuxtLink to="/" class="hover:text-gray-300">Beranda</NuxtLink>
          <NuxtLink to="/rooms" class="hover:text-gray-300">Daftar Kamar</NuxtLink>
          <NuxtLink to="/cek-pesanan" class="hover:text-gray-300">Cek Reservasi</NuxtLink>
        </div>
        
        <div class="bg-gray-700 p-4 rounded inline-block mb-6">
          <p class="font-bold text-gray-200 text-sm mb-1">Hubungi Kami:</p>
          <p class="text-lg">Telp: +62 882-9316-5787 | Token: Yqf1zvaY3unx3bTtPjnN</p>
          <p class="text-sm mt-1 text-gray-300">Email: info@hotelquviul.com</p>
        </div>
        
        <p class="text-gray-400 text-xs uppercase tracking-wider">&copy; {{ new Date().getFullYear() }} Hotelqu Viul - Hak Cipta Dilindungi.</p>
      </div>
    </footer>
  </div>
</template>

<script setup lang="ts">
const { user, isLoggedIn, isAdmin, isResepsionis, isTamu, logout } = useAuth()
const showDropdown = ref(false)
const showMobile = ref(false)
const dropdownRef = ref<HTMLElement | null>(null)

const handleLogout = async () => {
  showDropdown.value = false
  await logout()
}

onMounted(() => {
  if (import.meta.client) {
    document.addEventListener('click', (e) => {
      if (dropdownRef.value && !dropdownRef.value.contains(e.target as Node)) {
        showDropdown.value = false
      }
    })
  }
})
</script>
