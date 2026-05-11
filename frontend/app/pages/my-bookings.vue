<template>
 <div class="py-12">
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
<h1 class="text-3xl font-bold text-gray-800 mb-6">Pesanan <span class="gradient-text">Saya</span></h1>
<div v-if="loading" class="space-y-4">
<div v-for="i in 3" :key="i" class="glass rounded-none p-6 animate-pulse"><div class="h-6 bg-orange- rounded w-1/4 mb-3"></div><div class="h-4 bg-orange- rounded w-1/2"></div></div>
</div>
<div v-else-if="bookings.length === 0" class="text-center py-16 glass rounded-none">
<svg class="w-16 h-16 text-gray-600 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
<p class="text-gray-600 mb-4">Belum ada pesanan</p>
<NuxtLink to="/rooms" class="px-6 py-3 rounded-sm bg-gradient-to-r from-gray-700 to-gray-400 text-gray-800 font-semibold">Booking Sekarang</NuxtLink>
</div>
<div v-else class="space-y-4">
<div v-for="b in bookings" :key="b.id" class="glass rounded-none p-6 hover:border-gray-600/20 transition-smooth">
<div class="flex items-center justify-between mb-4">
<div>
<p class="text-xs text-gray-700">Nomor Pemesanan</p>
<p class="text-lg font-bold text-gray-800 font-mono">{{ b.nomor_pemesanan }}</p>
</div>
<div class="flex gap-2 text-right">
<span class="px-3 py-1 rounded-none text-xs font-bold uppercase" :class="{ 'bg-gray-600/20 text-red-400': b.status_pembayaran === 'belum_dibayar', 'bg-yellow-500/20 text-yellow-400': b.status_pembayaran === 'menunggu_validasi', 'bg-green-500/20 text-green-400': b.status_pembayaran === 'lunas', }">{{ b.status_pembayaran ? b.status_pembayaran.replace('_', ' ') : 'Belum Dibayar' }}</span>
<span class="px-3 py-1 rounded-none text-xs font-bold uppercase" :class="{ 'bg-yellow-500/20 text-yellow-400': b.status_pemesanan === 'baru', 'bg-gradient-to-r from-gray-700 to-gray-400/20 text-gray-800': b.status_pemesanan === 'check_in', 'bg-gray-500/20 text-gray-600': b.status_pemesanan === 'check_out', }">{{ b.status_pemesanan.replace('_', ' ') }}</span>
</div>
</div>
<div class="grid grid-cols-2 sm:grid-cols-4 gap-3 text-sm mb-4">
<div><p class="text-xs text-gray-700">Tipe</p><p class="text-gray-800">{{ b.detail_pemesanans?.[0]?.tipe_kamar?.nama_tipe_kamar || '-' }}</p></div>
<div><p class="text-xs text-gray-700">Check-in</p><p class="text-gray-800">{{ formatDateShort(b.tgl_check_in) }}</p></div>
<div><p class="text-xs text-gray-700">Check-out</p><p class="text-gray-800">{{ formatDateShort(b.tgl_check_out) }}</p></div>
<div><p class="text-xs text-gray-700">Total</p><p class="text-gray-800 font-bold">{{ formatRupiah(b.total_harga) }}</p></div>
</div>
<div v-if="b.denda > 0" class="mb-4 p-4 rounded-sm bg-yellow-50 border border-yellow-300">
  <p class="text-sm font-semibold text-yellow-700">Denda keterlambatan: {{ formatRupiah(b.denda) }}</p>
  <p class="text-xs text-yellow-600">Jumlah denda ini sudah tercantum di invoice. Silakan selesaikan pembayaran saat check-out.</p>
</div>
<div v-if="b.detail_pemesanans?.length" class="mb-4">
<p class="text-xs text-gray-700 mb-2">Detail Kamar & Tamu</p>
<div class="flex flex-wrap gap-2">
<span v-for="d in b.detail_pemesanans" :key="d.id" class="px-3 py-1.5 bg-orange- border border-orange- rounded-sm text-xs font-medium text-gray-300">
<span class="text-gray-800">#{{ d.kamar?.nomor_kamar || '-' }}</span> — {{ d.nama_tamu_spesifik || '-' }} </span>
</div>
</div>
<div v-if="b.metode_pembayaran === 'transfer' && (!b.status_pembayaran || b.status_pembayaran === 'belum_dibayar')" class="mt-4 p-4 rounded-sm bg-gray-1000/10 border border-gray-2000/20">
<p class="text-sm text-gray-400 font-semibold mb-2">Upload Bukti Transfer Pembayaran</p>
<div class="flex items-center gap-3">
<input type="file" @change="(e: Event) => { const input = e.target as HTMLInputElement; if (input?.files?.[0]) selectedFiles[b.id] = input.files[0] }" class="block w-full text-sm text-gray-600 file:mr-4 file:py-2 file:px-4 file:rounded-none file:border-0 file:text-sm file:font-semibold file:bg-gradient-to-r from-gray-700 to-gray-400/20 file:text-gray-800 hover:file:bg-gradient-to-r from-gray-700 to-gray-400/30">
<button @click="uploadBukti(b.id)" class="px-4 py-2 rounded-sm bg-gradient-to-r from-gray-700 to-gray-400 text-gray-800 text-sm font-semibold whitespace-nowrap" :disabled="uploading[b.id]"> {{ uploading[b.id] ? 'Mengunggah...' : 'Kirim Bukti' }} </button>
</div>
</div>
<div class="flex gap-3 mt-4">
<NuxtLink :to="`/invoice/${b.id}`" class="px-4 py-2 rounded-sm bg-gradient-to-r from-gray-700 to-gray-400/10 text-gray-800 text-sm font-medium hover:bg-gradient-to-r from-gray-700 to-gray-400/20 transition-smooth">Lihat Invoice</NuxtLink>
</div>
</div>
</div>
</div>
</div> 
</template>
 
<script setup lang="ts">
definePageMeta({ middleware: 'auth' })

const { formatRupiah, formatDateShort } = useApi()
const config = useRuntimeConfig()

const bookings = ref<any>([])
const loading = ref(true)
const selectedFiles = ref<Record<number, File>>({})
const uploading = ref<Record<number, boolean>>({})

const loadBookings = async () => {
  try {
    const token = localStorage.getItem('auth_token')
    const res = await $fetch(`${config.public.apiBase}/pemesanan/my-bookings`, {
      headers: { Authorization: `Bearer ${token}`, Accept: 'application/json' }
    })
    bookings.value = res
  } catch (e) {
    console.error(e)
  } finally {
    loading.value = false
  }
}

const uploadBukti = async (id: number) => {
  const file = selectedFiles.value[id]
  if (!file) return alert('Pilih file terlebih dahulu')

  uploading.value[id] = true
  const formData = new FormData()
  formData.append('bukti_transfer', file)

  try {
    const token = localStorage.getItem('auth_token')
    await $fetch(`${config.public.apiBase}/pemesanan/upload-bukti/${id}`, {
      method: 'POST',
      headers: { Authorization: `Bearer ${token}`, Accept: 'application/json' },
      body: formData
    })
    alert('Bukti transfer berhasil diunggah! Menunggu validasi resepsionis.')
    await loadBookings()
  } catch (e: any) {
    alert(e.response?._data?.message || 'Gagal mengunggah bukti. Pastikan format gambar dan ukuran maksimal 2MB.')
  } finally {
    uploading.value[id] = false
  }
}

onMounted(loadBookings)
</script>
