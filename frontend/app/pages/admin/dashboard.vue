<template>
 <div>
<h1 class="text-2xl font-bold text-gray-800 mb-6">Dashboard</h1>
<!-- Stats -->
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
<div v-for="(stat, i) in stats" :key="i" class="glass rounded-none p-5 hover:border-gray-600/20 transition-smooth">
<div class="flex items-center justify-between mb-3">
<div class="w-10 h-10 rounded-sm flex items-center justify-center" :class="stat.bg">
<span v-html="stat.icon"></span>
</div>
<span class="text-xs font-medium text-gray-700 uppercase">{{ stat.label }}</span>
</div>
<p class="text-2xl font-bold text-gray-800">{{ stat.value }}</p>
<p v-if="stat.sub" class="text-xs text-gray-700 mt-1">{{ stat.sub }}</p>
</div>
</div>
<!-- Recent Bookings -->
<div class="glass rounded-none p-6">
<h2 class="text-lg font-bold text-gray-800 mb-4">Pemesanan Terbaru</h2>
<div class="overflow-x-auto">
<table class="w-full text-sm">
<thead>
<tr class="border-b border-orange-">
<th class="text-left py-3 text-gray-700 font-medium">No. Pemesanan</th>
<th class="text-left py-3 text-gray-700 font-medium">Tamu</th>
<th class="text-left py-3 text-gray-700 font-medium">Tipe Kamar</th>
<th class="text-left py-3 text-gray-700 font-medium">Check-in</th>
<th class="text-right py-3 text-gray-700 font-medium">Total</th>
<th class="text-center py-3 text-gray-700 font-medium">Status</th>
</tr>
</thead>
<tbody>
<tr v-for="b in recentBookings" :key="b.id" class="border-b border-orange- hover:bg-orange- transition-smooth">
<td class="py-3 font-mono text-gray-800 text-xs">{{ b.nomor_pemesanan }}</td>
<td class="py-3 text-gray-800">{{ b.nama_pemesan }}</td>
<td class="py-3 text-gray-600">{{ b.detail_pemesanans?.[0]?.tipe_kamar?.nama_tipe_kamar || '-' }}</td>
<td class="py-3 text-gray-600">{{ formatDateShort(b.tgl_check_in) }}</td>
<td class="py-3 text-right text-gray-800 font-medium">{{ formatRupiah(b.total_harga) }}</td>
<td class="py-3 text-center">
<span class="px-2 py-1 rounded-none text-[10px] font-bold uppercase" :class="{ 'bg-yellow-500/20 text-yellow-400': b.status_pemesanan === 'baru', 'bg-gradient-to-r from-gray-700 to-gray-400/20 text-gray-800': b.status_pemesanan === 'check_in', 'bg-green-500/20 text-green-400': b.status_pemesanan === 'check_out', }">{{ b.status_pemesanan.replace('_', ' ') }}</span>
</td>
</tr>
</tbody>
</table>
</div>
</div>
</div> 
</template>
 
<script setup lang="ts">
definePageMeta({ layout: 'panel', middleware: 'auth' })

const { apiFetch, formatRupiah, formatDateShort } = useApi()
const dashboardData = ref<any>({})
const recentBookings = ref<any[]>([])

const stats = computed(() => {
  const s = dashboardData.value?.stats || {}
  return [
    { label: 'Total Booking', value: s.total_bookings || 0, bg: 'bg-gradient-to-r from-gray-700 to-gray-400/10', icon: '<svg class="w-5 h-5 text-gray-800" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>' },
    { label: 'Check-in Hari Ini', value: s.check_ins_today || 0, bg: 'bg-gradient-to-r from-gray-700 to-gray-400/10', icon: '<svg class="w-5 h-5 text-gray-800" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/></svg>' },
    { label: 'Pendapatan Bulan Ini', value: formatRupiah(s.revenue_this_month || 0), bg: 'bg-green-500/10', icon: '<svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>', sub: 'Revenue' },
    { label: 'Tamu Aktif', value: s.active_bookings || 0, bg: 'bg-gold-500/10', icon: '<svg class="w-5 h-5 text-gold-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>' },
  ]
})

onMounted(async () => {
  try {
    dashboardData.value = await apiFetch('/admin/dashboard')
    recentBookings.value = dashboardData.value.recent_bookings || []
  } catch (e) {
    console.error(e)
  }
})
</script>
