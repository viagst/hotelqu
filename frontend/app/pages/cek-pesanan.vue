<template>
 <div class="py-12">
<div class="max-w-lg mx-auto px-4 sm:px-6 lg:px-8">
<div class="text-center mb-8">
<h1 class="text-3xl font-bold text-gray-800 mb-2">Cek <span class="gradient-text">Pesanan</span></h1>
<p class="text-gray-600">Masukkan nomor pemesanan dan email untuk melihat detail pesanan Anda</p>
</div>
<form @submit.prevent="cekPesanan" class="glass rounded-none p-8 space-y-5 mb-8">
<div v-if="error" class="px-4 py-3 rounded-sm bg-gray-600/10 border border-gray-600/20 text-red-400 text-sm">{{ error }}</div>
<div>
<label class="block text-sm font-medium text-gray-300 mb-1.5">Nomor Pemesanan (ID)</label>
<div class="relative">
<div class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-700">
<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
</div>
<input v-model="form.nomor_pemesanan" type="text" required placeholder="BO-202604020001" class="w-full pl-10 pr-4 py-3 rounded-sm bg-orange- border border-orange- text-gray-800 placeholder-gray-500 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500/50 transition-smooth">
</div>
</div>
<div>
<label class="block text-sm font-medium text-gray-300 mb-1.5">Alamat Email</label>
<div class="relative">
<div class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-700">
<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
</div>
<input v-model="form.email" type="email" required placeholder="nama@email.com" class="w-full pl-10 pr-4 py-3 rounded-sm bg-orange- border border-orange- text-gray-800 placeholder-gray-500 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500/50 transition-smooth">
</div>
</div>
<button type="submit" :disabled="isLoading" class="w-full py-3.5 rounded-sm bg-gradient-to-r from-gray-700 to-gray-400 text-gray-800 font-semibold hover:bg-gradient-to-r from-gray-700 to-gray-400 border border-gray-600 disabled:opacity-50 transition-smooth"> {{ isLoading ? 'Mencari...' : 'Retrieve and Print' }} </button>
</form>
<!-- Result -->
<div v-if="booking" class="glass rounded-none p-8 animate-slide-up">
<div class="flex items-center justify-between mb-6 pb-4 border-b border-orange-">
<div>
<p class="text-xs text-gray-700">Nomor Pemesanan</p>
<p class="text-xl font-bold text-gray-800 font-mono">{{ booking.nomor_pemesanan }}</p>
</div>
<span class="px-3 py-1.5 rounded-none text-xs font-bold uppercase" :class="{ 'bg-yellow-500/20 text-yellow-400': booking.status_pemesanan === 'baru', 'bg-gradient-to-r from-gray-700 to-gray-400/20 text-gray-800': booking.status_pemesanan === 'check_in', 'bg-green-500/20 text-green-400': booking.status_pemesanan === 'check_out', }">{{ booking.status_pemesanan.replace('_', ' ') }}</span>
</div>
<div class="grid grid-cols-2 gap-4 mb-6">
<div><p class="text-xs text-gray-700">Nama</p><p class="text-gray-800 font-medium text-sm">{{ booking.nama_pemesan }}</p></div>
<div><p class="text-xs text-gray-700">Email</p><p class="text-gray-800 font-medium text-sm">{{ booking.email_pemesan }}</p></div>
<div><p class="text-xs text-gray-700">Check-in</p><p class="text-gray-800 font-medium text-sm">{{ formatDateShort(booking.tgl_check_in) }}</p></div>
<div><p class="text-xs text-gray-700">Check-out</p><p class="text-gray-800 font-medium text-sm">{{ formatDateShort(booking.tgl_check_out) }}</p></div>
<div><p class="text-xs text-gray-700">Kamar</p><p class="text-gray-800 font-medium text-sm">{{ booking.jumlah_kamar }} kamar × {{ booking.jumlah_malam }} malam</p></div>
<div><p class="text-xs text-gray-700">Pembayaran</p><p class="text-gray-800 font-medium text-sm">{{ booking.metode_pembayaran === 'transfer' ? 'Transfer' : 'Cash' }}</p></div>
</div>
<div class="p-4 rounded-sm bg-gradient-to-r from-gray-700 to-gray-400/5 border border-gray-600/10 mb-6">
<div class="flex justify-between"><span class="text-gray-600">Total</span><span class="text-xl font-bold text-gray-800">{{ formatRupiah(booking.total_harga) }}</span></div>
<div v-if="booking.denda > 0" class="flex justify-between mt-2"><span class="text-yellow-400 text-sm">Denda</span><span class="text-yellow-400 font-semibold">{{ formatRupiah(booking.denda) }}</span></div>
</div>
<button @click="navigateTo(`/invoice/${booking.id}`)" class="w-full py-3 rounded-sm bg-gradient-to-r from-gray-700 to-gray-400 text-gray-800 font-semibold transition-smooth">🖨️ Print Receipt</button>
</div>
</div>
</div> 
</template>
 
<script setup lang="ts">
const { apiFetch, formatRupiah, formatDateShort } = useApi()
const form = ref({ nomor_pemesanan: '', email: '' })
const booking = ref<any>(null)
const error = ref('')
const isLoading = ref(false)

const cekPesanan = async () => {
  error.value = ''
  booking.value = null
  isLoading.value = true
  try {
    booking.value = await apiFetch('/cek-pesanan', { method: 'POST', body: JSON.stringify(form.value) })
  } catch (e: any) {
    error.value = 'Pesanan tidak ditemukan. Periksa nomor pemesanan dan email.'
  } finally {
    isLoading.value = false
  }
}
</script>
