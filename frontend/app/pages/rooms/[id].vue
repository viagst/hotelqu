<template>
  <div class="py-12 bg-slate-50 min-h-screen">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
      <NuxtLink to="/rooms" class="inline-flex items-center gap-2 text-gray-800 hover:text-gray-800 mb-6 font-medium">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        Kembali ke Daftar Kamar
      </NuxtLink>

      <div v-if="room" class="bg-white border border-gray-200 rounded-lg shadow-sm overflow-hidden">
        <div class="h-64 sm:h-80 relative">
          <img :src="getImageUrl(room.foto)" :alt="room.nama_tipe_kamar" class="w-full h-full object-cover">
          <div class="absolute top-4 right-4">
            <span class="px-3 py-1 text-sm font-bold rounded shadow" :class="room.available > 0 ? 'bg-green-100 text-green-800 border border-green-200' : 'bg-red-100 text-gray-800 border border-red-200'">
              {{ room.available > 0 ? `Tersedia: ${room.available} kamar` : 'Penuh' }}
              <span v-if="room.available > 0" class="font-normal text-xs ml-1">{{ route.query.check_in ? '(Sesuai Tanggal)' : '(Hari Ini)' }}</span>
            </span>
          </div>
        </div>

        <div class="p-6 md:p-8">
          <div class="border-b border-gray-200 pb-6 mb-6">
            <h1 class="text-3xl font-bold text-gray-800 mb-2">{{ room.nama_tipe_kamar }}</h1>
            <p class="text-2xl font-bold text-gray-800">{{ formatRupiah(room.harga) }}<span class="text-sm text-gray-500 font-normal">/malam</span></p>
          </div>

          <h2 class="text-xl font-bold text-gray-800 mb-3">Deskripsi</h2>
          <p class="text-gray-600 leading-relaxed mb-8">{{ room.deskripsi }}</p>

          <h2 class="text-xl font-bold text-gray-800 mb-4">Fasilitas</h2>
          <div class="grid grid-cols-2 sm:grid-cols-3 gap-3 mb-8">
            <div v-for="(f, i) in (room.fasilitas_tipe || [])" :key="i" class="flex items-center gap-2 px-3 py-2 bg-orange- border border-orange- rounded">
              <svg class="w-4 h-4 text-gray-800 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
              <span class="text-sm text-gray-700">{{ f }}</span>
            </div>
          </div>

          <h2 class="text-xl font-bold text-gray-800 mb-4">Daftar Kamar</h2>
          <div class="grid grid-cols-3 sm:grid-cols-5 gap-3 mb-8">
            <div
              v-for="k in room.kamars"
              :key="k.id"
              class="px-4 py-2 text-center text-sm font-semibold rounded border"
              :class="getRoomClass(k)"
            >
              #{{ k.nomor_kamar }}
            </div>
          </div>

          <div class="text-center sm:text-left mt-8">
            <NuxtLink v-if="room.available > 0" :to="bookUrl" class="inline-flex items-center justify-center gap-2 px-8 py-3 bg-gradient-to-r from-gray-700 to-gray-400 text-white font-bold rounded shadow hover:bg-gradient-to-r from-gray-700 to-gray-400 w-full sm:w-auto">
              Booking Sekarang
            </NuxtLink>
            <span v-else class="inline-block px-8 py-3 bg-gray-300 text-gray-600 font-bold rounded cursor-not-allowed w-full sm:w-auto text-center">
              Semua Kamar Penuh
            </span>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
const route = useRoute()
const { apiFetch, formatRupiah, getImageUrl } = useApi()
const room = ref<any>(null)

const getRoomClass = (k: any) => {
  // Booked room (not available for selected dates)
  if (!k.is_available) {
    return 'bg-red-50 text-gray-700 border-red-200'
  }
  // Room status based styling
  if (k.status_kamar === 'kotor') {
    return 'bg-yellow-50 text-yellow-700 border-yellow-200'
  }
  if (k.status_kamar === 'perbaikan') {
    return 'bg-gray-100 text-orange-700 border-gray-400'
  }
  if (k.status_kamar === 'kosong') {
    return 'bg-gray-100 text-gray-700 border-gray-300'
  }
  // Available (tersedia or bersih)
  return 'bg-green-50 text-green-700 border-green-200'
}

const bookUrl = computed(() => {
  let url = `/booking?tipe=${room.value?.id}`
  if (route.query.check_in) url += `&check_in=${route.query.check_in}`
  if (route.query.check_out) url += `&check_out=${route.query.check_out}`
  return url
})

onMounted(async () => {
  try {
    let url = `/tipe-kamar/${route.params.id}`
    if (route.query.check_in && route.query.check_out) {
      url += `?check_in=${route.query.check_in}&check_out=${route.query.check_out}`
    }
    room.value = await apiFetch(url)
  } catch (e) { navigateTo('/rooms') }
})
</script>
