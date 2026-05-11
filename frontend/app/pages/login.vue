<template>
  <div class="min-h-screen flex items-center justify-center py-12 px-4">
    <div class="w-full max-w-md">
      <div class="text-center mb-8">
        <NuxtLink to="/" class="inline-flex items-center gap-3 mb-6">
          <div class="w-12 h-12 bg-gradient-to-r from-gray-700 to-gray-400 flex items-center justify-center">
            <span class="text-dark-950 font-bold text-xl">HVL</span>
          </div>
          <span class="text-2xl font-bold text-gray-800">Hotel</span>
        </NuxtLink>
        <h1 class="text-2xl font-bold text-gray-800">Selamat Datang Kembali</h1>
        <p class="text-gray-700 mt-2">Masuk ke akun Anda untuk melanjutkan</p>
      </div>

      <form @submit.prevent="handleLogin" class="border border-orange- bg-white p-8 space-y-5">
        <div v-if="error" class="px-4 py-3 bg-gray-600/10 border border-gray-600/20 text-red-400 text-sm">{{ error }}</div>

        <div>
          <label class="block text-sm font-medium text-gray-600 mb-1.5">Email</label>
          <input v-model="form.email" type="email" required placeholder="nama@email.com"
            class="w-full px-4 py-3 bg-orange- border border-orange- text-gray-800 placeholder-gray-600 text-sm focus:outline-none focus:border-gray-600 transition-smooth">
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-600 mb-1.5">Password</label>
          <div class="relative">
            <input v-model="form.password" :type="showPass ? 'text' : 'password'" required placeholder="••••••••"
              class="w-full px-4 py-3 bg-orange- border border-orange- text-gray-800 placeholder-gray-600 text-sm focus:outline-none focus:border-gray-600 transition-smooth pr-12">
            <button type="button" @click="showPass = !showPass" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-600 hover:text-gray-600">
              <svg v-if="!showPass" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
              <svg v-else class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/></svg>
            </button>
          </div>
        </div>

        <div class="flex items-center justify-end">
          <NuxtLink to="/forgot-password" class="text-sm text-gray-800 hover:text-gray-800 transition-smooth">Lupa password?</NuxtLink>
        </div>

        <button type="submit" :disabled="isLoading" class="w-full py-3.5 bg-gradient-to-r from-gray-700 to-gray-400 text-dark-950 font-bold hover:bg-gradient-to-r from-gray-700 to-gray-400 disabled:opacity-50 transition-smooth flex items-center justify-center gap-2">
          <svg v-if="isLoading" class="animate-spin w-5 h-5" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
          {{ isLoading ? 'Masuk...' : 'Masuk' }}
        </button>
      </form>

      <p class="text-center mt-6 text-sm text-gray-700">
        Belum punya akun?
        <NuxtLink to="/register" class="text-gray-800 hover:text-gray-800 font-medium transition-smooth">Daftar sekarang</NuxtLink>
      </p>
    </div>
  </div>
</template>

<script setup lang="ts">
definePageMeta({ layout: 'default' })
const { login } = useAuth()
const form = ref({ email: '', password: '' })
const error = ref('')
const isLoading = ref(false)
const showPass = ref(false)

const handleLogin = async () => {
  error.value = ''
  isLoading.value = true
  try {
    const data = await login(form.value.email, form.value.password)
    if (data.user.role === 'admin') navigateTo('/admin/dashboard')
    else if (data.user.role === 'resepsionis') navigateTo('/resepsionis/dashboard')
    else navigateTo('/')
  } catch (e: any) {
    error.value = e?.data?.message || 'Email atau password salah'
  } finally {
    isLoading.value = false
  }
}
</script>
