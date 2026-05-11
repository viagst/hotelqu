<template>
 <div>
<h1 class="text-2xl font-bold text-gray-800 mb-6">Profil Saya</h1>
<form @submit.prevent="updateProfile" class="max-w-lg space-y-5">
<div v-if="success" class="px-4 py-3 rounded-sm bg-green-500/10 border border-green-500/20 text-green-400 text-sm">{{ success }}</div>
<div v-if="error" class="px-4 py-3 rounded-sm bg-gray-600/10 border border-gray-600/20 text-red-400 text-sm">{{ error }}</div>
<div class="glass rounded-none p-6 space-y-4">
<div><label class="block text-xs font-medium text-gray-600 mb-1">Nama</label>
<input v-model="form.nama_user" class="w-full px-4 py-3 rounded-sm bg-orange- border border-orange- text-gray-800 text-sm focus:ring-2 focus:ring-primary-500/50 focus:outline-none transition-smooth"></div>
<div><label class="block text-xs font-medium text-gray-600 mb-1">Email</label>
<input v-model="form.email" type="email" class="w-full px-4 py-3 rounded-sm bg-orange- border border-orange- text-gray-800 text-sm focus:ring-2 focus:ring-primary-500/50 focus:outline-none transition-smooth"></div>
<div><label class="block text-xs font-medium text-gray-600 mb-1">No HP</label>
<input v-model="form.no_hp" class="w-full px-4 py-3 rounded-sm bg-orange- border border-orange- text-gray-800 text-sm focus:ring-2 focus:ring-primary-500/50 focus:outline-none transition-smooth"></div>
</div>
<div class="glass rounded-none p-6 space-y-4">
<h3 class="text-gray-800 font-semibold">Ubah Password</h3>
<div><label class="block text-xs font-medium text-gray-600 mb-1">Password Lama</label>
<input v-model="form.current_password" type="password" class="w-full px-4 py-3 rounded-sm bg-orange- border border-orange- text-gray-800 text-sm focus:ring-2 focus:ring-primary-500/50 focus:outline-none transition-smooth"></div>
<div><label class="block text-xs font-medium text-gray-600 mb-1">Password Baru</label>
<input v-model="form.password" type="password" class="w-full px-4 py-3 rounded-sm bg-orange- border border-orange- text-gray-800 text-sm focus:ring-2 focus:ring-primary-500/50 focus:outline-none transition-smooth"></div>
<div><label class="block text-xs font-medium text-gray-600 mb-1">Konfirmasi Password</label>
<input v-model="form.password_confirmation" type="password" class="w-full px-4 py-3 rounded-sm bg-orange- border border-orange- text-gray-800 text-sm focus:ring-2 focus:ring-primary-500/50 focus:outline-none transition-smooth"></div>
</div>
<button type="submit" class="w-full py-3.5 rounded-sm bg-gradient-to-r from-gray-700 to-gray-400 text-gray-800 font-semibold transition-smooth">Simpan Perubahan</button>
</form>
</div> 
</template>
 
<script setup lang="ts">
definePageMeta({ layout: 'panel', middleware: 'auth' })

const { apiFetch } = useApi()
const { user } = useAuth()
const success = ref('')
const error = ref('')
const form = ref({ nama_user: '', email: '', no_hp: '', current_password: '', password: '', password_confirmation: '' })

onMounted(() => {
  if (user.value) {
    form.value.nama_user = user.value.nama_user
    form.value.email = user.value.email
    form.value.no_hp = user.value.no_hp || ''
  }
})

const updateProfile = async () => {
  success.value = ''
  error.value = ''
  try {
    const data: any = { ...form.value }
    if (!data.password) { delete data.password; delete data.password_confirmation; delete data.current_password }
    await apiFetch('/profil', { method: 'POST', body: JSON.stringify(data) })
    success.value = 'Profil berhasil diupdate'
  } catch (e: any) {
    error.value = e?.data?.message || 'Gagal update profil'
  }
}
</script>
