<template>
 <div class="py-12">
<div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
<!-- Success icon -->
<div class="mb-8 animate-scale-in">
<div class="w-20 h-20 mx-auto rounded-none bg-green-500/10 border-2 border-green-500/30 flex items-center justify-center">
<svg class="w-10 h-10 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
</div>
</div>
<h1 class="text-3xl font-bold text-gray-800 mb-3 animate-fade-in">Pemesanan Berhasil! 🎉</h1>
<p class="text-gray-600 mb-8 animate-fade-in">Terima kasih telah memesan di 71 Hotel</p>
<div v-if="booking" class="glass rounded-none p-8 text-left mb-8 animate-slide-up">
<div class="flex items-center justify-between mb-6 pb-4 border-b border-orange-">
<div>
<p class="text-xs text-gray-700 uppercase tracking-wider">Nomor Pemesanan</p>
<p class="text-xl font-bold text-gray-800 font-mono">{{ booking.nomor_pemesanan }}</p>
</div>
<span class="px-3 py-1.5 rounded-none text-xs font-bold bg-green-500/20 text-green-400 border border-green-500/30 uppercase">{{ booking.status_pemesanan }}</span>
</div>
<div class="grid sm:grid-cols-2 gap-4 mb-6">
<div><p class="text-xs text-gray-700">Nama Pemesan</p><p class="text-gray-800 font-medium">{{ booking.nama_pemesan }}</p></div>
<div><p class="text-xs text-gray-700">Email</p><p class="text-gray-800 font-medium">{{ booking.email_pemesan }}</p></div>
<div><p class="text-xs text-gray-700">Check-in</p><p class="text-gray-800 font-medium">{{ formatDate(booking.tgl_check_in) }}</p></div>
<div><p class="text-xs text-gray-700">Check-out</p><p class="text-gray-800 font-medium">{{ formatDate(booking.tgl_check_out) }}</p></div>
<div><p class="text-xs text-gray-700">Jumlah Kamar</p><p class="text-gray-800 font-medium">{{ booking.jumlah_kamar }}</p></div>
<div><p class="text-xs text-gray-700">Jumlah Malam</p><p class="text-gray-800 font-medium">{{ booking.jumlah_malam }}</p></div>
</div>
<div class="p-4 rounded-sm bg-orange- border border-orange- mb-6">
<div class="flex justify-between items-center">
<span class="text-gray-600 font-bold">Total Pembayaran</span>
<span class="text-2xl font-black text-gray-800">{{ formatRupiah(booking.total_harga) }}</span>
</div>
<p class="text-sm text-gray-700 mt-1 font-semibold">Metode: {{ booking.metode_pembayaran === 'transfer' ? 'Transfer Bank' : 'Bayar di Hotel' }}</p>
</div>
<!-- Upload bukti transfer & QR Code -->
<div v-if="booking.metode_pembayaran === 'transfer' && !booking.bukti_transfer" class="mt-6 p-6 rounded bg-white border border-gray-200 shadow-sm text-center">
<h3 class="font-bold text-lg text-gray-800 mb-2">Selesaikan Pembayaran Anda</h3>
<p class="text-sm text-gray-600 mb-4">Silakan scan QRIS di bawah ini atau transfer ke rekening bank kami menggunakan nomor pesanan Anda.</p>

<div class="flex justify-center mb-6">
  <div class="p-4 bg-white border border-gray-300 rounded shadow-sm">
    <img :src="`https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=${booking.nomor_pemesanan}`" alt="QR Code Pembayaran" class="w-40 h-40 mx-auto" />
    <p class="text-xs font-bold text-center mt-2 text-gray-700">QRIS NO. {{ booking.nomor_pemesanan }}</p>
  </div>
</div>

<div class="border-t border-gray-200 pt-4">
  <p class="text-sm font-bold text-gray-800 mb-2">Atau Upload Bukti Transfer Bank</p>
  <input type="file" accept="image/*" @change="handleFileUpload" class="text-sm text-gray-600 block w-full mx-auto max-w-xs border border-gray-300 p-2 rounded">
  <button v-if="selectedFile" @click="uploadBukti" :disabled="uploading" class="mt-3 px-6 py-2 rounded bg-gradient-to-r from-gray-700 to-gray-400 text-white text-sm font-bold disabled:opacity-50 hover:bg-gradient-to-r from-gray-700 to-gray-400 shadow"> {{ uploading ? 'Mengupload...' : 'Upload Bukti Pembayaran' }} </button>
</div>
</div>
<div v-if="booking.bukti_transfer" class="mt-4 px-4 py-3 rounded bg-green-50 border border-green-200 text-green-700 text-sm font-bold text-center shadow-sm"> ✅ Bukti pembayaran telah berhasil diupload! Menunggu konfirmasi admin. </div>
</div>
<div class="flex flex-col sm:flex-row gap-3 justify-center">
<NuxtLink to="/" class="px-8 py-3 rounded-sm bg-orange- border border-orange- text-gray-300 font-medium hover:bg-orange- transition-smooth">Kembali ke Beranda</NuxtLink>
<button @click="printInvoice" class="px-8 py-3 rounded-sm bg-gradient-to-r from-gray-700 to-gray-400 text-gray-800 font-semibold transition-smooth">🖨️ Cetak Invoice</button>
</div>
</div>
</div> 
</template>
 
<script setup lang="ts">
const route = useRoute()
const { apiFetch, formatRupiah, formatDate } = useApi()
const booking = ref<any>(null)
const selectedFile = ref<any>(null)
const uploading = ref(false)

const handleFileUpload = (e: any) => {
  selectedFile.value = e.target.files[0]
}

const uploadBukti = async () => {
  if (!selectedFile.value || !booking.value) return
  uploading.value = true
  try {
    const formData = new FormData()
    formData.append('bukti_transfer', selectedFile.value)
    await apiFetch(`/pemesanan/upload-bukti/${booking.value.id}`, { method: 'POST', body: formData })
    booking.value.bukti_transfer = 'uploaded'
  } catch (e) {
    console.error(e)
  } finally {
    uploading.value = false
  }
}

const printInvoice = () => {
  if (booking.value) navigateTo(`/invoice/${booking.value.id}`)
}

onMounted(async () => {
  if (!route.query.id) return navigateTo('/')
  try {
    booking.value = await apiFetch(`/pemesanan/${route.query.id}/invoice`)
  } catch {
    navigateTo('/')
  }
})
</script>
