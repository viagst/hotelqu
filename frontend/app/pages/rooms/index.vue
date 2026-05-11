<template>
  <div class="py-12 bg-slate-50 min-h-screen">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
      <!-- Header -->
      <div class="mb-8 text-center md:text-left">
        <h1 class="text-3xl font-bold text-gray-800 mb-2">Tipe Kamar</h1>
        <p class="text-gray-600">Temukan kamar yang sempurna untuk Anda</p>
      </div>

      <!-- Search Filter -->
      <div class="bg-white p-6 mb-8 border border-orange- shadow-sm rounded-lg">
        <div class="grid sm:grid-cols-3 gap-4 items-end">
          <div>
            <label class="block text-sm font-semibold mb-1 text-gray-600">Check-in</label>
            <input v-model="checkIn" type="date" :min="today" class="w-full border border-gray-300 rounded p-2 focus:outline-blue-500">
          </div>
          <div>
            <label class="block text-sm font-semibold mb-1 text-gray-600">Check-out</label>
            <input v-model="checkOut" type="date" :min="checkInPlusOne" class="w-full border border-gray-300 rounded p-2 focus:outline-blue-500">
          </div>
          <button @click="checkAvail" class="w-full bg-gradient-to-r from-gray-700 to-gray-400 text-white font-bold py-2 rounded hover:bg-gradient-to-r from-gray-700 to-gray-400 h-10">
            Cek Ketersediaan
          </button>
        </div>
      </div>

      <!-- Room List -->
      <div v-if="loading" class="text-center text-gray-800 font-bold">
        Memuat data kamar...
      </div>

      <div v-else class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
        <div v-for="room in rooms" :key="room.id" class="bg-white border border-gray-200 rounded-lg shadow-sm overflow-hidden flex flex-col">
          <div class="relative h-48 bg-gray-200">
            <img :src="getImageUrl(room.foto)" :alt="room.nama_tipe_kamar" class="w-full h-full object-cover">
            <div class="absolute top-2 right-2">
              <span v-if="hasAvailability" class="px-2 py-1 text-xs font-bold rounded" :class="room.is_available ? 'bg-green-100 text-green-800' : 'bg-red-100 text-gray-800'">
                {{ room.is_available ? `Tersedia: ${room.available} kamar` : 'Penuh' }}
              </span>
              <span v-else class="px-2 py-1 text-xs font-bold bg-orange- text-gray-800 rounded">
                Stok: {{ room.stok }}
              </span>
            </div>
          </div>
          
          <div class="p-5 flex-1 flex flex-col">
            <h3 class="text-xl font-bold text-gray-800 mb-2">{{ room.nama_tipe_kamar }}</h3>
            <p class="text-sm text-gray-600 mb-4 line-clamp-2">{{ room.deskripsi }}</p>
            
            <div class="flex flex-wrap gap-1 mb-4">
              <span v-for="(f, i) in (room.fasilitas_tipe || [])" :key="i" class="px-2 py-1 bg-gray-100 text-xs text-gray-600 rounded border border-gray-200">{{ f }}</span>
            </div>
            
            <div class="mt-auto">
              <p class="text-2xl font-bold text-gray-800 mb-4">{{ formatRupiah(room.harga) }}<span class="text-sm font-normal text-gray-500">/malam</span></p>
              <div class="flex gap-2">
                <NuxtLink :to="detailUrl(room.id)" class="flex-1 text-center py-2 bg-gray-100 text-gray-800 font-semibold rounded hover:bg-gray-200 border border-gray-300">Detail</NuxtLink>
                <NuxtLink v-if="!hasAvailability || room.is_available" :to="bookUrl(room.id)" class="flex-1 text-center py-2 bg-gradient-to-r from-gray-700 to-gray-400 text-white font-semibold rounded hover:bg-gradient-to-r from-gray-700 to-gray-400">Booking</NuxtLink>
                <span v-else class="flex-1 text-center py-2 bg-gray-300 text-gray-600 font-semibold rounded cursor-not-allowed">Penuh</span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
const route = useRoute()
const { apiFetch, formatRupiah, getImageUrl } = useApi()

const rooms = ref<any[]>([])
const loading = ref(true)
const checkIn = ref(typeof route.query.check_in === 'string' ? route.query.check_in : '')
const checkOut = ref(typeof route.query.check_out === 'string' ? route.query.check_out : '')
const hasAvailability = ref(false)

const today = computed(() => new Date().toISOString().split('T')[0])
const checkInPlusOne = computed(() => {
  if (!checkIn.value) return today.value
  const d = new Date(checkIn.value)
  d.setDate(d.getDate() + 1)
  return d.toISOString().split('T')[0]
})

const detailUrl = (id: number) => {
  let url = `/rooms/${id}`
  if (checkIn.value && checkOut.value) url += `?check_in=${checkIn.value}&check_out=${checkOut.value}`
  return url
}
const bookUrl = (id: number) => {
  let url = `/booking?tipe=${id}`
  if (checkIn.value) url += `&check_in=${checkIn.value}`
  if (checkOut.value) url += `&check_out=${checkOut.value}`
  return url
}

const loadRooms = async () => {
  loading.value = true
  try {
    let url = '/tipe-kamar'
    if (checkIn.value && checkOut.value) {
      url += `?check_in=${checkIn.value}&check_out=${checkOut.value}`
      hasAvailability.value = true
    }
    rooms.value = await apiFetch(url)
  } catch (e) {
    console.error(e)
  } finally {
    loading.value = false
  }
}

const checkAvail = () => {
  if (checkIn.value && checkOut.value) loadRooms()
}

onMounted(() => {
  if (checkIn.value && checkOut.value) hasAvailability.value = true
  loadRooms()
})
</script>
