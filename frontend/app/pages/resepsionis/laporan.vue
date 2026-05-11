<template>
 <div>
<h1 class="text-2xl font-bold text-gray-800 mb-6">Laporan Harian</h1>
<div class="glass rounded-none p-4 mb-6 flex items-center gap-4">
<input v-model="tanggal" type="date" @change="loadData" class="px-4 py-2.5 rounded-sm bg-orange- border border-orange- text-gray-800 text-sm focus:ring-2 focus:ring-primary-500/50 focus:outline-none">
<button @click="loadData" class="px-5 py-2.5 rounded-sm bg-gradient-to-r from-gray-700 to-gray-400 text-gray-800 font-semibold text-sm transition-smooth">Lihat</button>
</div>
<div v-if="report" class="grid lg:grid-cols-2 gap-6">
<div class="glass rounded-none p-6">
<h2 class="text-lg font-bold text-gray-800 mb-4">Check-in ({{ report.check_in.total }})</h2>
<div v-for="b in report.check_in.data" :key="b.id" class="p-4 rounded-sm bg-orange- mb-3 border border-orange-">
<p class="text-gray-800 font-medium">{{ b.nama_pemesan }}</p>
<p class="text-xs text-gray-600">{{ b.nomor_pemesanan }} · {{ b.jumlah_kamar }} kamar · {{ formatRupiah(b.total_harga) }}</p>
</div>
<p v-if="report.check_in.total === 0" class="text-gray-700 text-sm">Tidak ada</p>
</div>
<div class="glass rounded-none p-6">
<h2 class="text-lg font-bold text-gray-800 mb-4">Check-out ({{ report.check_out.total }})</h2>
<div v-for="b in report.check_out.data" :key="b.id" class="p-4 rounded-sm bg-orange- mb-3 border border-orange-">
<p class="text-gray-800 font-medium">{{ b.nama_pemesan }}</p>
<p class="text-xs text-gray-600">{{ b.nomor_pemesanan }} · Denda: {{ b.denda > 0 ? formatRupiah(b.denda) : '-' }}</p>
</div>
<p v-if="report.check_out.total === 0" class="text-gray-700 text-sm">Tidak ada</p>
</div>
</div>
</div> 
</template>
 
<script setup lang="ts">
definePageMeta({ layout: 'panel', middleware: 'auth' })

const { apiFetch, formatRupiah } = useApi()
const tanggal = ref(new Date().toISOString().split('T')[0])
const report = ref<any>(null)

const loadData = async () => {
  try {
    report.value = await apiFetch(`/resepsionis/laporan?tanggal=${tanggal.value}`)
  } catch (e) {
    console.error(e)
  }
}

onMounted(loadData)
</script>
