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
        <h1 class="text-2xl font-bold text-gray-800">Buat Akun Baru</h1>
        <p class="text-gray-700 mt-2">Daftar untuk mulai memesan kamar</p>
      </div>

      <form @submit.prevent="handleRegister" class="border border-orange- bg-white p-8 space-y-5">
        <div v-if="error" class="px-4 py-3 bg-gray-600/10 border border-gray-600/20 text-red-400 text-sm">{{ error }}</div>

        <div>
          <label class="block text-sm font-medium text-gray-600 mb-1.5">Nama Lengkap</label>
          <input v-model="form.nama_user" type="text" required placeholder="Nama lengkap"
            class="w-full px-4 py-3 bg-orange- border border-orange- text-gray-800 placeholder-gray-600 text-sm focus:outline-none focus:border-gray-600 transition-smooth">
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-600 mb-1.5">Email</label>
          <input v-model="form.email" type="email" required placeholder="nama@email.com"
            class="w-full px-4 py-3 bg-orange- border border-orange- text-gray-800 placeholder-gray-600 text-sm focus:outline-none focus:border-gray-600 transition-smooth">
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-600 mb-1.5">No. Telepon</label>
          <input v-model="form.no_telp" type="tel" required placeholder="08xxxxxxxxxx"
            class="w-full px-4 py-3 bg-orange- border border-orange- text-gray-800 placeholder-gray-600 text-sm focus:outline-none focus:border-gray-600 transition-smooth">
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-600 mb-1.5">Password</label>
          <input v-model="form.password" type="password" required placeholder="Minimal 6 karakter"
            class="w-full px-4 py-3 bg-orange- border border-orange- text-gray-800 placeholder-gray-600 text-sm focus:outline-none focus:border-gray-600 transition-smooth">
        </div>

        <button type="submit" :disabled="isLoading" class="w-full py-3.5 bg-gradient-to-r from-gray-700 to-gray-400 text-dark-950 font-bold hover:bg-gradient-to-r from-gray-700 to-gray-400 disabled:opacity-50 transition-smooth flex items-center justify-center gap-2">
          <svg v-if="isLoading" class="animate-spin w-5 h-5" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
          {{ isLoading ? 'Mendaftar...' : 'Daftar' }}
        </button>
      </form>

      <p class="text-center mt-6 text-sm text-gray-700">
        Sudah punya akun?
        <NuxtLink to="/login" class="text-gray-800 hover:text-gray-800 font-medium transition-smooth">Masuk</NuxtLink>
      </p>
    </div>
  </div>
</template>

<script setup lang="ts">
definePageMeta({ layout: 'default' })
const { apiFetch } = useApi()
const { login } = useAuth()
const form = ref({ nama_user: '', email: '', no_telp: '', password: '' })
const error = ref('')
const isLoading = ref(false)

const handleRegister = async () => {
  error.value = ''
  isLoading.value = true
  try {
    await apiFetch('/register', { method: 'POST', body: JSON.stringify(form.value) })
    await login(form.value.email, form.value.password)
    navigateTo('/')
  } catch (e: any) {
    error.value = e?.data?.message || 'Pendaftaran gagal'
  } finally {
    isLoading.value = false
  }
}
</script>
