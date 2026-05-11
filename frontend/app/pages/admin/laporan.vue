<template>
 <div>
<h1 class="text-2xl font-bold text-gray-800 mb-6">Laporan Pendapatan</h1>
<div class="glass rounded-none p-6 mb-8">
<div class="grid sm:grid-cols-3 gap-4 items-end">
<div><label class="block text-xs font-medium text-gray-600 mb-1.5">Dari Tanggal</label>
<input v-model="dari" type="date" class="w-full px-4 py-3 rounded-sm bg-orange- border border-orange- text-gray-800 text-sm focus:ring-2 focus:ring-primary-500/50 focus:outline-none"></div>
<div><label class="block text-xs font-medium text-gray-600 mb-1.5">Sampai Tanggal</label>
<input v-model="sampai" type="date" class="w-full px-4 py-3 rounded-sm bg-orange- border border-orange- text-gray-800 text-sm focus:ring-2 focus:ring-primary-500/50 focus:outline-none"></div>
<button @click="loadReport" class="py-3 rounded-sm bg-gradient-to-r from-gray-700 to-gray-400 text-gray-800 font-semibold transition-smooth">Lihat Laporan</button>
</div>
</div>
<div v-if="report" class="space-y-6">
<div class="grid sm:grid-cols-3 gap-4">
<div class="glass rounded-none p-5"><p class="text-xs text-gray-700 mb-1">Total Pendapatan</p><p class="text-2xl font-bold text-green-400">{{ formatRupiah(report.ringkasan.total_pendapatan) }}</p></div>
<div class="glass rounded-none p-5"><p class="text-xs text-gray-700 mb-1">Total Denda</p><p class="text-2xl font-bold text-yellow-400">{{ formatRupiah(report.ringkasan.total_denda) }}</p></div>
<div class="glass rounded-none p-5"><p class="text-xs text-gray-700 mb-1">Total Keseluruhan</p><p class="text-2xl font-bold text-gray-800">{{ formatRupiah(report.ringkasan.total_keseluruhan) }}</p></div>
</div>
<div class="glass rounded-none overflow-hidden">
<div class="overflow-x-auto">
<table class="w-full text-sm">
<thead><tr class="border-b border-orange-">
<th class="text-left p-4 text-gray-700">No. Pemesanan</th>
<th class="text-left p-4 text-gray-700">Tamu</th>
<th class="text-left p-4 text-gray-700">Tanggal</th>
<th class="text-right p-4 text-gray-700">Total</th>
<th class="text-right p-4 text-gray-700">Denda</th>
<th class="text-center p-4 text-gray-700">Status</th>
</tr></thead>
<tbody>
<tr v-for="p in report.data" :key="p.id" class="border-b border-orange-">
<td class="p-4 text-gray-800 font-mono text-xs">{{ p.nomor_pemesanan }}</td>
<td class="p-4 text-gray-800">{{ p.nama_pemesan }}</td>
<td class="p-4 text-gray-600">{{ formatDateShort(p.tgl_pemesanan) }}</td>
<td class="p-4 text-right text-gray-800 font-medium">{{ formatRupiah(p.total_harga) }}</td>
<td class="p-4 text-right text-yellow-400">{{ p.denda > 0 ? formatRupiah(p.denda) : '-' }}</td>
<td class="p-4 text-center"><span class="px-2 py-1 rounded-none text-[10px] font-bold uppercase" :class="{ 'bg-yellow-500/20 text-yellow-400': p.status_pemesanan === 'baru', 'bg-gradient-to-r from-gray-700 to-gray-400/20 text-gray-800': p.status_pemesanan === 'check_in', 'bg-green-500/20 text-green-400': p.status_pemesanan === 'check_out' }">{{ p.status_pemesanan.replace('_',' ') }}</span></td>
</tr>
</tbody>
</table>
</div>
</div>
</div>
</div> 
</template>
 
<script setup lang="ts">
definePageMeta({ layout: 'panel', middleware: 'auth' })

const { apiFetch, formatRupiah, formatDateShort } = useApi()
const dari = ref(new Date().toISOString().slice(0, 7) + '-01')
const sampai = ref(new Date().toISOString().split('T')[0])
const report = ref<any>(null)

const loadReport = async () => {
  try {
    report.value = await apiFetch(`/admin/laporan?dari_tanggal=${dari.value}&sampai_tanggal=${sampai.value}`)
  } catch (e) {
    console.error(e)
  }
}

onMounted(loadReport)
</script>
