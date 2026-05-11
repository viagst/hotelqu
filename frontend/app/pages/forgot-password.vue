<template>
 <div class="min-h-screen flex items-center justify-center py-12 px-4">
<div class="w-full max-w-md">
<div class="text-center mb-8">
<NuxtLink to="/" class="inline-flex items-center gap-3 mb-6">
<div class="w-12 h-12 rounded-sm bg-gradient-to-r from-gray-700 to-gray-400 flex items-center justify-center ">
<span class="text-gray-800 font-bold text-xl">HVL</span>
</div>
<span class="text-2xl font-bold text-gray-800">Hotel</span>
</NuxtLink>
<h1 class="text-2xl font-bold text-gray-800">Reset Password</h1>
<p class="text-gray-600 mt-2">Kami akan mengirim OTP ke WhatsApp Anda</p>
</div>
<div class="glass rounded-none p-8">
<div v-if="error" class="mb-5 px-4 py-3 rounded-sm bg-gray-600/10 border border-gray-600/20 text-red-400 text-sm">{{ error }}</div>
<div v-if="success" class="mb-5 px-4 py-3 rounded-sm bg-green-500/10 border border-green-500/20 text-green-400 text-sm">{{ success }}</div>
<!-- Step 1: Enter Email -->
<form v-if="step === 1" @submit.prevent="sendOtp" class="space-y-5">
<div>
<label class="block text-sm font-medium text-gray-300 mb-1.5">Email</label>
<input v-model="form.email" type="email" required placeholder="nama@email.com" class="w-full px-4 py-3 rounded-sm bg-orange- border border-orange- text-gray-800 placeholder-gray-500 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500/50 transition-smooth">
</div>
<button type="submit" :disabled="isLoading" class="w-full py-3.5 rounded-sm bg-gradient-to-r from-gray-700 to-gray-400 text-gray-800 font-semibold disabled:opacity-50 transition-smooth"> {{ isLoading ? 'Mengirim...' : 'Kirim OTP' }} </button>
</form>
<!-- Step 2: Verify OTP -->
<form v-if="step === 2" @submit.prevent="verifyOtp" class="space-y-5">
<p class="text-sm text-gray-600">OTP telah dikirim ke {{ phoneHint }}</p>
<div>
<label class="block text-sm font-medium text-gray-300 mb-1.5">Kode OTP</label>
<input v-model="form.otp" type="text" required maxlength="6" placeholder="000000" class="w-full px-4 py-3 rounded-sm bg-orange- border border-orange- text-gray-800 placeholder-gray-500 text-sm text-center tracking-[0.5em] text-lg font-mono focus:outline-none focus:ring-2 focus:ring-primary-500/50 transition-smooth">
</div>
<button type="submit" :disabled="isLoading" class="w-full py-3.5 rounded-sm bg-gradient-to-r from-gray-700 to-gray-400 text-gray-800 font-semibold disabled:opacity-50 transition-smooth"> {{ isLoading ? 'Memverifikasi...' : 'Verifikasi OTP' }} </button>
</form>
<!-- Step 3: New Password -->
<form v-if="step === 3" @submit.prevent="resetPassword" class="space-y-5">
<div>
<label class="block text-sm font-medium text-gray-300 mb-1.5">Password Baru</label>
<input v-model="form.password" type="password" required minlength="6" placeholder="Minimal 6 karakter" class="w-full px-4 py-3 rounded-sm bg-orange- border border-orange- text-gray-800 placeholder-gray-500 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500/50 transition-smooth">
</div>
<div>
<label class="block text-sm font-medium text-gray-300 mb-1.5">Konfirmasi Password</label>
<input v-model="form.password_confirmation" type="password" required placeholder="Ulangi password" class="w-full px-4 py-3 rounded-sm bg-orange- border border-orange- text-gray-800 placeholder-gray-500 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500/50 transition-smooth">
</div>
<button type="submit" :disabled="isLoading" class="w-full py-3.5 rounded-sm bg-gradient-to-r from-gray-700 to-gray-400 text-gray-800 font-semibold disabled:opacity-50 transition-smooth"> {{ isLoading ? 'Menyimpan...' : 'Simpan Password Baru' }} </button>
</form>
<!-- Steps indicator -->
<div class="flex items-center justify-center gap-2 mt-6">
<div v-for="s in 3" :key="s" class="w-8 h-1 rounded-none transition-smooth" :class="s <= step ? 'bg-gradient-to-r from-gray-700 to-gray-400' : 'bg-orange-'"></div>
</div>
</div>
<p class="text-center mt-6 text-sm text-gray-600"> Ingat password? <NuxtLink to="/login" class="text-gray-800 hover:text-gray-800 font-medium transition-smooth">Masuk</NuxtLink>
</p>
</div>
</div> 
</template>
 
<script setup lang="ts">
definePageMeta({ layout: 'default' })

const { apiFetch } = useApi()
const step = ref(1)
const form = ref({ email: '', otp: '', password: '', password_confirmation: '' })
const phoneHint = ref('')
const error = ref('')
const success = ref('')
const isLoading = ref(false)

const sendOtp = async () => {
  error.value = ''
  success.value = ''
  isLoading.value = true
  try {
    const data = await apiFetch('/forgot-password/send-otp', { method: 'POST', body: JSON.stringify({ email: form.value.email }) })
    phoneHint.value = data.phone_hint
    success.value = data.message
    step.value = 2
  } catch (e: any) {
    error.value = e?.data?.message || 'Gagal mengirim OTP'
  } finally {
    isLoading.value = false
  }
}

const verifyOtp = async () => {
  error.value = ''
  success.value = ''
  isLoading.value = true
  try {
    const data = await apiFetch('/forgot-password/verify-otp', { method: 'POST', body: JSON.stringify({ email: form.value.email, otp: form.value.otp }) })
    success.value = data.message
    step.value = 3
  } catch (e: any) {
    error.value = e?.data?.message || 'OTP tidak valid'
  } finally {
    isLoading.value = false
  }
}

const resetPassword = async () => {
  error.value = ''
  success.value = ''
  isLoading.value = true
  try {
    await apiFetch('/forgot-password/reset', { method: 'POST', body: JSON.stringify({ email: form.value.email, otp: form.value.otp, password: form.value.password, password_confirmation: form.value.password_confirmation }) })
    navigateTo('/login')
  } catch (e: any) {
    error.value = e?.data?.message || 'Gagal reset password'
  } finally {
    isLoading.value = false
  }
}
</script>
