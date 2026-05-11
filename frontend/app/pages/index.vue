<template>
  <div>
    <!-- Hero Section (Distinct Layout with Image) -->
    <section class="relative bg-gradient-to-r from-gray-700 to-gray-400 py-24 text-center text-white border-b-8 border-gray-600">
      <img src="/images/hero-bg.png" alt="Hotelqu Viul" class="absolute inset-0 w-full h-full object-cover opacity-30 mix-blend-overlay">
      <div class="relative z-10 max-w-4xl mx-auto px-4">
        <h1 class="text-4xl md:text-6xl font-black mb-4 uppercase tracking-widest drop-shadow-md">Hotelqu Viul</h1>
        <p class="text-xl mb-8 text-blue-100 font-medium">Kenyamanan Sederhana dengan Harga Terbaik.</p>
      </div>
    </section>

    <!-- Search Form (Moved Below Hero) -->
    <section class="bg-gradient-to-r from-gray-700 to-gray-400 py-6 border-b border-gray-600">
      <div class="max-w-6xl mx-auto px-4">
        <div class="bg-white p-4 rounded shadow-md flex flex-col md:flex-row gap-4 items-end">
          <div class="flex-1 w-full">
            <label class="block text-sm font-bold mb-1 text-gray-800">Tanggal Check-in</label>
            <input v-model="checkIn" type="date" :min="today" class="w-full border-2 border-orange- rounded p-2 focus:outline-none focus:border-gray-600">
          </div>
          <div class="flex-1 w-full">
            <label class="block text-sm font-bold mb-1 text-gray-800">Tanggal Check-out</label>
            <input v-model="checkOut" type="date" :min="checkInPlusOne" class="w-full border-2 border-orange- rounded p-2 focus:outline-none focus:border-gray-600">
          </div>
          <button @click="searchRooms" class="w-full md:w-auto bg-gradient-to-r from-gray-700 to-gray-400 text-white font-bold px-8 py-3 rounded hover:bg-gradient-to-r from-gray-700 to-gray-400 shadow">
            Cari Kamar
          </button>
        </div>
      </div>
    </section>

    <!-- Room Types Section -->
    <section class="py-16 bg-slate-50">
      <div class="max-w-6xl mx-auto px-4">
        <div class="text-center mb-10 border-b-2 border-orange- pb-4 inline-block w-full">
          <h2 class="text-3xl font-black text-gray-800 uppercase tracking-widest">Tipe Kamar Kami</h2>
        </div>

        <div v-if="loading" class="text-center text-gray-800 font-bold p-10">
          Sedang mengambil data kamar...
        </div>

        <!-- Generic Cards -->
        <div v-else class="grid sm:grid-cols-2 lg:grid-cols-3 gap-8">
          <div v-for="room in roomTypes" :key="room.id" class="bg-white border-2 border-orange- rounded-lg shadow-md overflow-hidden flex flex-col hover:border-gray-600 transition-colors">
            <div class="h-56 bg-gray-200 relative overflow-hidden">
              <img v-if="room.foto" :src="getImageUrl(room.foto)" :alt="room.nama_tipe_kamar" class="w-full h-full object-cover" loading="lazy">
              <div v-else class="w-full h-full flex items-center justify-center bg-gray-300">
                <span class="text-gray-500 text-sm">No Image</span>
              </div>
              <div class="absolute top-0 right-0 bg-gradient-to-r from-gray-700 to-gray-400 text-white font-bold px-3 py-1 text-xs">
                {{ room.stok > 0 ? `Sisa: ${room.stok}` : 'Penuh' }}
              </div>
            </div>
            <div class="p-5 flex-1 flex flex-col">
              <h3 class="text-xl font-bold text-gray-800 mb-2 border-b border-gray-100 pb-2">{{ room.nama_tipe_kamar }}</h3>
              <p class="text-sm text-gray-600 mb-4 flex-1 line-clamp-3">{{ room.deskripsi || '-' }}</p>
              
              <div class="mt-4 bg-orange- p-3 rounded text-center">
                <p class="text-sm font-semibold text-gray-800">Harga Mulai</p>
                <p class="text-2xl font-black text-gray-800">{{ formatRupiah(room.harga) }}</p>
              </div>
              <NuxtLink :to="`/rooms/${room.id}`" class="mt-4 block w-full text-center bg-gradient-to-r from-gray-700 to-gray-400 text-white py-3 rounded font-bold hover:bg-gradient-to-r from-gray-700 to-gray-400 uppercase tracking-wider text-sm shadow">Lihat Detail Kamar</NuxtLink>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Features Section (Simplified Layout) -->
    <section class="py-16 bg-gradient-to-r from-gray-700 to-gray-400 text-white border-t-8 border-gray-600">
      <div class="max-w-6xl mx-auto px-4 text-center">
        <h2 class="text-2xl font-black mb-10 uppercase tracking-widest text-blue-200">Keunggulan Hotelqu Viul</h2>
        <div class="grid grid-cols-2 md:grid-cols-3 gap-6">
          <div v-for="(feature, i) in features" :key="i" class="p-4 border border-gray-600 bg-gradient-to-r from-gray-700 to-gray-400 rounded">
            <div class="text-3xl mb-2">{{ feature.icon }}</div>
            <h3 class="text-md font-bold text-white mb-1">{{ feature.title }}</h3>
            <p class="text-xs text-blue-200">{{ feature.desc }}</p>
          </div>
        </div>
      </div>
    </section>
  </div>
</template>

<script setup lang="ts">
const { apiFetch, formatRupiah, getImageUrl } = useApi()
const roomTypes = ref<any[]>([])
const loading = ref(true)
const checkIn = ref('')
const checkOut = ref('')

const today = computed(() => new Date().toISOString().split('T')[0])
const checkInPlusOne = computed(() => {
  if (!checkIn.value) return today.value
  const d = new Date(checkIn.value)
  d.setDate(d.getDate() + 1)
  return d.toISOString().split('T')[0]
})

const features = [
  { icon: '🛏️', title: 'Kamar Bersih', desc: 'Selalu dibersihkan dengan standar tinggi.' },
  { icon: '📶', title: 'WiFi Cepat', desc: 'Koneksi stabil untuk semua tamu.' },
  { icon: '🛡️', title: 'Aman 24 Jam', desc: 'Keamanan ekstra untuk kenyamanan Anda.' },
  { icon: '🧹', title: 'Staf Ramah', desc: 'Kami siap membantu kebutuhan Anda.' },
  { icon: '🅿️', title: 'Parkir Luas', desc: 'Tersedia tempat parkir memadai.' },
  { icon: '🎯', title: 'Pusat Kota', desc: 'Lokasi dekat dengan berbagai akses.' },
]

const searchRooms = () => {
  if (checkIn.value && checkOut.value) {
    navigateTo(`/rooms?check_in=${checkIn.value}&check_out=${checkOut.value}`)
  } else {
    navigateTo('/rooms')
  }
}

onMounted(async () => {
  try {
    roomTypes.value = await apiFetch('/tipe-kamar')
  } catch (e) {
    console.error('Failed to load room types:', e)
  } finally {
    loading.value = false
  }
})
</script>
