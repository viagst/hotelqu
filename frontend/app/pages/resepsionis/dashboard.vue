<template>
 <div>
<h1 class="text-2xl font-bold text-gray-800 mb-6">Dashboard Resepsionis</h1>
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
<div v-for="(stat, i) in stats" :key="i" class="glass rounded-none p-5">
<p class="text-xs text-gray-700 mb-1">{{ stat.label }}</p>
<p class="text-2xl font-bold text-gray-800">{{ stat.value }}</p>
</div>
</div>
<div class="grid lg:grid-cols-2 gap-6">
<div class="glass rounded-none p-6">
<h2 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
<span class="w-2 h-2 rounded-none bg-green-400"></span> Check-in Hari Ini </h2>
<div v-for="b in recentBookings.filter(b => b.status_pemesanan === 'baru')" :key="b.id" class="p-4 rounded-sm bg-orange- mb-3 border border-orange-">
<div class="flex justify-between items-center">
<div>
<p class="text-gray-800 font-medium">{{ b.nama_pemesan }}</p>
<p class="text-xs text-gray-600 font-mono">{{ b.nomor_pemesanan }}</p>
</div>
<span class="px-2 py-1 rounded-none text-[10px] font-bold bg-yellow-500/20 text-yellow-400">BARU</span>
</div>
</div>
<p v-if="recentBookings.filter(b => b.status_pemesanan === 'baru').length === 0" class="text-gray-700 text-sm">Tidak ada check-in hari ini</p>
</div>
<div class="glass rounded-none p-6">
<h2 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
<span class="w-2 h-2 rounded-none bg-gradient-to-r from-gray-700 to-gray-400"></span> Sedang Menginap </h2>
<div v-for="b in recentBookings.filter(b => b.status_pemesanan === 'check_in')" :key="b.id" class="p-4 rounded-sm bg-orange- mb-3 border border-orange-">
<div class="flex justify-between items-center">
<div>
<p class="text-gray-800 font-medium">{{ b.nama_pemesan }}</p>
<p class="text-xs text-gray-600">Check-out: {{ formatDateShort(b.tgl_check_out) }}</p>
</div>
<span class="px-2 py-1 rounded-none text-[10px] font-bold bg-gradient-to-r from-gray-700 to-gray-400/20 text-gray-800">CHECK IN</span>
</div>
</div>
<p v-if="recentBookings.filter(b => b.status_pemesanan === 'check_in').length === 0" class="text-gray-700 text-sm">Tidak ada tamu menginap</p>
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
    { label: 'Check-in Hari Ini', value: s.check_ins_today || 0 },
    { label: 'Check-out Hari Ini', value: s.check_outs_today || 0 },
    { label: 'Tamu Aktif', value: s.active_bookings || 0 },
    { label: 'Total Booking', value: s.total_bookings || 0 },
  ]
})

onMounted(async () => {
  try {
    dashboardData.value = await apiFetch('/resepsionis/dashboard')
    recentBookings.value = dashboardData.value.recent_bookings || []
  } catch (e) {
    console.error(e)
  }
})
</script>
