<template>
 <div>
<h1 class="text-2xl font-bold text-gray-800 mb-6">Laporan Pendapatan</h1>
<div class="glass rounded-none p-6 mb-8">
<div class="grid sm:grid-cols-3 gap-4 items-end">
<div><label class="block text-xs font-medium text-gray-600 mb-1.5">Dari Tanggal</label>
<input v-model="dari" type="date" class="w-full px-4 py-3 rounded-sm border border-gray-300 text-gray-800 text-sm focus:ring-2 focus:ring-primary-500/50 focus:outline-none"></div>
<div><label class="block text-xs font-medium text-gray-600 mb-1.5">Sampai Tanggal</label>
<input v-model="sampai" type="date" class="w-full px-4 py-3 rounded-sm border border-gray-300 text-gray-800 text-sm focus:ring-2 focus:ring-primary-500/50 focus:outline-none"></div>
<div class="flex gap-2">
<button @click="loadReport" class="flex-1 py-3 rounded-sm bg-gradient-to-r from-gray-700 to-gray-400 text-white font-semibold">Lihat Laporan</button>
<button @click="printLaporan" class="px-4 py-3 rounded-sm bg-gray-200 border border-gray-400 text-gray-700 font-semibold">🖨️</button>
</div>
</div>
<!-- Shortcut buttons -->
<div class="flex gap-2 mt-3 flex-wrap">
  <button @click="setHariIni" class="px-3 py-1.5 text-xs border border-gray-300 rounded-sm text-gray-600 hover:bg-gray-100">Hari Ini</button>
  <button @click="setBulanIni" class="px-3 py-1.5 text-xs border border-gray-300 rounded-sm text-gray-600 hover:bg-gray-100">Bulan Ini</button>
  <button @click="setBulanLalu" class="px-3 py-1.5 text-xs border border-gray-300 rounded-sm text-gray-600 hover:bg-gray-100">Bulan Lalu</button>
  <button @click="setTahunIni" class="px-3 py-1.5 text-xs border border-gray-300 rounded-sm text-gray-600 hover:bg-gray-100">Tahun Ini</button>
</div>
</div>
<div v-if="report" class="space-y-6">
<!-- PRINT HEADER -->
<div class="print-only" style="display:none; text-align:center; margin-bottom:20px; border-bottom:2px solid #000; padding-bottom:12px;">
  <h2 style="font-size:20px; font-weight:bold; margin:0;">HOTELQU VIUL (HVL)</h2>
  <p style="font-size:13px; margin:4px 0;">LAPORAN PENDAPATAN</p>
  <p style="font-size:13px; margin:0;">Periode: {{ dari }} s/d {{ sampai }}</p>
</div>
<!-- Ringkasan -->
<div class="grid sm:grid-cols-4 gap-4">
<div class="glass rounded-none p-5"><p class="text-xs text-gray-700 mb-1">Total Booking</p><p class="text-2xl font-bold text-gray-800">{{ report.ringkasan.total_booking }}</p></div>
<div class="glass rounded-none p-5"><p class="text-xs text-gray-700 mb-1">Total Pendapatan</p><p class="text-2xl font-bold text-green-600">{{ formatRupiah(report.ringkasan.total_pendapatan) }}</p></div>
<div class="glass rounded-none p-5"><p class="text-xs text-gray-700 mb-1">Total Denda</p><p class="text-2xl font-bold text-red-500">{{ formatRupiah(report.ringkasan.total_denda) }}</p></div>
<div class="glass rounded-none p-5"><p class="text-xs text-gray-700 mb-1">Total Keseluruhan</p><p class="text-2xl font-bold text-gray-800">{{ formatRupiah(report.ringkasan.total_keseluruhan) }}</p></div>
</div>

<!-- Tabel Detail -->
<div class="glass rounded-none overflow-hidden">
<div class="overflow-x-auto">
<table class="w-full text-sm border-collapse border border-gray-300">
<thead>
  <tr class="bg-gray-100">
    <th class="text-left p-3 border border-gray-300 text-gray-700">No</th>
    <th class="text-left p-3 border border-gray-300 text-gray-700">No. Pemesanan</th>
    <th class="text-left p-3 border border-gray-300 text-gray-700">Nama Tamu</th>
    <th class="text-left p-3 border border-gray-300 text-gray-700">Kamar</th>
    <th class="text-left p-3 border border-gray-300 text-gray-700">Tanggal Pesan</th>
    <th class="text-left p-3 border border-gray-300 text-gray-700">Check-In</th>
    <th class="text-left p-3 border border-gray-300 text-gray-700">Check-Out</th>
    <th class="text-right p-3 border border-gray-300 text-gray-700">Total Harga</th>
    <th class="text-right p-3 border border-gray-300 text-gray-700">Denda</th>
    <th class="text-right p-3 border border-gray-300 text-gray-700">Jml Bayar</th>
    <th class="text-right p-3 border border-gray-300 text-gray-700">Kembalian</th>
    <th class="text-center p-3 border border-gray-300 text-gray-700">Metode</th>
    <th class="text-center p-3 border border-gray-300 text-gray-700">Status</th>
  </tr>
</thead>
<tbody>
<tr v-for="(p, i) in report.data" :key="p.id" class="border-b border-gray-200 hover:bg-gray-50">
  <td class="p-3 border border-gray-300 text-center text-gray-600">{{ Number(i) + 1 }}</td>
  <td class="p-3 border border-gray-300 text-gray-800 font-mono text-xs">{{ p.nomor_pemesanan }}</td>
  <td class="p-3 border border-gray-300 text-gray-800">
    <div class="font-medium">{{ p.nama_pemesan }}</div>
    <div v-for="d in p.detail_pemesanans" :key="d.id" class="text-xs text-gray-500 mt-0.5">
      — {{ d.nama_tamu_spesifik }}
    </div>
  </td>
  <td class="p-3 border border-gray-300 text-gray-700">
    <div v-for="d in p.detail_pemesanans" :key="d.id" class="text-xs">
      <span class="font-medium">{{ d.tipe_kamar?.nama_tipe_kamar }}</span>
      <span v-if="d.kamar" class="text-gray-500"> #{{ d.kamar.nomor_kamar }}</span>
    </div>
  </td>
  <td class="p-3 border border-gray-300 text-gray-600 text-xs">{{ formatDateShort(p.tgl_pemesanan) }}</td>
  <td class="p-3 border border-gray-300 text-gray-600 text-xs">{{ formatDateShort(p.tgl_check_in) }}</td>
  <td class="p-3 border border-gray-300 text-gray-600 text-xs">{{ formatDateShort(p.tgl_check_out) }}</td>
  <td class="p-3 border border-gray-300 text-right text-gray-800 font-medium">{{ formatRupiah(p.total_harga) }}</td>
  <td class="p-3 border border-gray-300 text-right" :class="p.denda > 0 ? 'text-red-600 font-medium' : 'text-gray-400'">{{ p.denda > 0 ? formatRupiah(p.denda) : '-' }}</td>
  <td class="p-3 border border-gray-300 text-right text-gray-700">{{ p.jumlah_bayar ? formatRupiah(p.jumlah_bayar) : '-' }}</td>
  <td class="p-3 border border-gray-300 text-right" :class="p.kembalian > 0 ? 'text-green-600 font-medium' : 'text-gray-400'">{{ p.kembalian != null && p.kembalian >= 0 && p.jumlah_bayar ? formatRupiah(p.kembalian) : '-' }}</td>
  <td class="p-3 border border-gray-300 text-center text-xs capitalize text-gray-700">{{ p.metode_pembayaran === 'transfer' ? 'Transfer' : 'Cash' }}</td>
  <td class="p-3 border border-gray-300 text-center">
    <span class="px-2 py-1 rounded-none text-[10px] font-bold uppercase" :class="{
      'bg-yellow-500/20 text-yellow-600': p.status_pemesanan === 'baru',
      'bg-blue-500/20 text-blue-600': p.status_pemesanan === 'check_in',
      'bg-green-500/20 text-green-600': p.status_pemesanan === 'check_out'
    }">{{ p.status_pemesanan.replace('_',' ') }}</span>
  </td>
</tr>
<!-- TOTAL -->
<tr v-if="report.data.length > 0" class="bg-gray-100 font-bold">
  <td colspan="7" class="p-3 border border-gray-300 text-right">TOTAL:</td>
  <td class="p-3 border border-gray-300 text-right">{{ formatRupiah(report.ringkasan.total_pendapatan) }}</td>
  <td class="p-3 border border-gray-300 text-right text-red-600">{{ formatRupiah(report.ringkasan.total_denda) }}</td>
  <td class="p-3 border border-gray-300 text-right">{{ formatRupiah(totalBayarAdmin) }}</td>
  <td class="p-3 border border-gray-300 text-right text-green-600">{{ formatRupiah(totalKembalianAdmin) }}</td>
  <td colspan="2" class="p-3 border border-gray-300"></td>
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

const setHariIni = () => {
  const today = new Date().toISOString().split('T')[0]
  dari.value = today
  sampai.value = today
  loadReport()
}

const setBulanIni = () => {
  const now = new Date()
  dari.value = now.toISOString().slice(0, 7) + '-01'
  sampai.value = now.toISOString().split('T')[0]
  loadReport()
}

const setBulanLalu = () => {
  const now = new Date()
  const firstDayLastMonth = new Date(now.getFullYear(), now.getMonth() - 1, 1)
  const lastDayLastMonth = new Date(now.getFullYear(), now.getMonth(), 0)
  dari.value = firstDayLastMonth.toISOString().split('T')[0]
  sampai.value = lastDayLastMonth.toISOString().split('T')[0]
  loadReport()
}

const setTahunIni = () => {
  const year = new Date().getFullYear()
  dari.value = `${year}-01-01`
  sampai.value = new Date().toISOString().split('T')[0]
  loadReport()
}

const totalBayarAdmin = computed(() => {
  if (!report.value) return 0
  return report.value.data.reduce((sum: number, p: any) => sum + (p.jumlah_bayar || 0), 0)
})

const totalKembalianAdmin = computed(() => {
  if (!report.value) return 0
  return report.value.data.reduce((sum: number, p: any) => sum + (p.kembalian || 0), 0)
})

const loadReport = async () => {
  try {
    report.value = await apiFetch(`/admin/laporan?dari_tanggal=${dari.value}&sampai_tanggal=${sampai.value}`)
  } catch (e) {
    console.error(e)
  }
}

const printLaporan = () => {
  window.print()
}

onMounted(loadReport)
</script>

<style>
@media print {
  .no-print { display: none !important; }
  .print-only { display: block !important; }
  nav, header, aside, footer { display: none !important; }
  body { background: white !important; }
}
</style>
