<template>
  <div class="py-12">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
      <h1 class="text-3xl font-bold text-gray-800 mb-2">Pemesanan <span class="text-gray-800">Kamar</span></h1>
      <p class="text-gray-700 mb-8">Lengkapi data pemesanan Anda</p>

      <div v-if="error" class="mb-6 px-4 py-3 bg-gray-600/10 border border-gray-600/20 text-red-400 text-sm">{{ error }}</div>

      <form @submit.prevent="handleBooking" class="space-y-6">
        <!-- Room Type -->
        <div class="border border-orange- bg-white p-6">
          <h2 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
            <svg class="w-5 h-5 text-gray-800" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
            Detail Kamar
          </h2>
          <div v-if="tipeKamar" class="flex items-center gap-4 p-4 bg-orange- border border-orange-">
            <div class="w-16 h-16 bg-gradient-to-r from-gray-700 to-gray-400/10 border border-gray-600/20 flex items-center justify-center">
              <svg class="w-8 h-8 text-gray-800" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
            </div>
            <div>
              <p class="text-gray-800 font-semibold">{{ tipeKamar.nama_tipe_kamar }}</p>
              <p class="text-gray-800 font-bold">{{ formatRupiah(tipeKamar.harga) }}/malam</p>
            </div>
          </div>
        </div>

        <!-- Dates -->
        <div class="border border-orange- bg-white p-6">
          <h2 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
            <svg class="w-5 h-5 text-gray-800" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
            Tanggal Menginap
          </h2>
          <div class="grid sm:grid-cols-2 gap-4">
            <div>
              <label class="block text-xs font-medium text-gray-700 mb-1.5 uppercase tracking-wider">Check-in</label>
              <input v-model="form.tgl_check_in" type="date" required :min="today" class="w-full px-4 py-3 bg-orange- border border-orange- text-gray-800 text-sm focus:outline-none focus:border-gray-600 transition-smooth">
            </div>
            <div>
              <label class="block text-xs font-medium text-gray-700 mb-1.5 uppercase tracking-wider">Check-out</label>
              <input v-model="form.tgl_check_out" type="date" required :min="checkInPlusOne" class="w-full px-4 py-3 bg-orange- border border-orange- text-gray-800 text-sm focus:outline-none focus:border-gray-600 transition-smooth">
            </div>
          </div>
          <div v-if="nights > 0" class="mt-3 text-sm text-gray-700">{{ nights }} malam</div>
        </div>

        <!-- Choose Rooms + guest names -->
        <div class="border border-orange- bg-white p-6">
          <h2 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
            <svg class="w-5 h-5 text-gray-800" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
            Pilih Kamar & Tamu
          </h2>
          <div class="mb-6">
            <label class="block text-xs font-medium text-gray-700 mb-2 uppercase tracking-wider">Pilih Kamar yang Tersedia</label>
            <div v-if="!tipeKamar?.kamars" class="text-sm text-gray-600">Memuat kamar...</div>
            <div v-else class="grid grid-cols-4 sm:grid-cols-6 gap-2">
              <div v-for="k in tipeKamar.kamars" :key="k.id">
                <label v-if="k.is_available" class="cursor-pointer relative block py-2 text-center border transition-smooth text-sm font-medium" :class="form.selected_rooms.includes(k.id) ? 'bg-gradient-to-r from-gray-700 to-gray-400/10 border-gray-600 text-gray-800' : 'bg-orange- border-orange- text-gray-600 hover:bg-orange-'">
                  <input type="checkbox" :value="k.id" v-model="form.selected_rooms" @change="updateGuestNames" class="hidden"> #{{ k.nomor_kamar }}
                </label>
                <div v-else class="relative py-2 text-center bg-gray-600/10 border border-gray-600/20 text-red-400 cursor-not-allowed text-sm font-medium group/booked">
                  #{{ k.nomor_kamar }}
                  <div class="absolute bottom-full left-1/2 -translate-x-1/2 mb-2 px-3 py-1.5 bg-gray-100 border border-gray-600/30 text-red-400 text-[11px] font-semibold whitespace-nowrap opacity-0 group-hover/booked:opacity-100 transition-all duration-200 pointer-events-none z-10">
                    Sudah di-booking
                    <div class="absolute top-full left-1/2 -translate-x-1/2 w-0 h-0 border-l-[5px] border-r-[5px] border-t-[5px] border-l-transparent border-r-transparent border-t-dark-800"></div>
                  </div>
                </div>
              </div>
            </div>
            <p v-if="form.selected_rooms.length === 0" class="mt-2 text-xs text-red-400">Silakan pilih minimal 1 kamar.</p>
          </div>
          <div class="space-y-3" v-if="form.selected_rooms.length > 0">
            <div v-for="(roomId, i) in form.selected_rooms" :key="roomId">
              <label class="block text-xs font-medium text-gray-700 mb-1.5">Nama Tamu - Kamar #{{ getNomorKamar(roomId) }}</label>
              <input v-model="form.nama_tamu[i]" type="text" required :placeholder="`Nama tamu kamar #${getNomorKamar(roomId)}`" class="w-full px-4 py-3 bg-orange- border border-orange- text-gray-800 placeholder-gray-600 text-sm focus:outline-none focus:border-gray-600 transition-smooth">
            </div>
          </div>
        </div>

        <!-- Payment -->
        <div class="border border-orange- bg-white p-6">
          <h2 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
            <svg class="w-5 h-5 text-gray-800" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
            Pembayaran
          </h2>
          <div class="p-4 bg-gradient-to-r from-gray-700 to-gray-400/5 border border-gray-600/10 mb-4">
            <div class="flex justify-between text-sm mb-2">
              <span class="text-gray-700">{{ tipeKamar?.nama_tipe_kamar }} × {{ form.selected_rooms.length }} kamar × {{ nights }} malam</span>
            </div>
            <div class="flex justify-between text-lg font-bold">
              <span class="text-gray-800">Total</span>
              <span class="text-gray-800">{{ formatRupiah(totalHarga) }}</span>
            </div>
          </div>
          <div v-if="totalHarga > 500000" class="px-4 py-3 bg-yellow-500/10 border border-yellow-500/20 text-yellow-400 text-sm mb-4">
            ⚠️ Total di atas Rp 500.000 — wajib transfer bank
          </div>
          <div>
            <label class="block text-xs font-medium text-gray-700 mb-2 uppercase tracking-wider">Metode Pembayaran</label>
            <div class="grid grid-cols-2 gap-3">
              <button type="button" @click="form.metode_pembayaran = 'transfer'" class="p-4 border text-center text-sm font-medium transition-smooth" :class="form.metode_pembayaran === 'transfer' ? 'border-gray-600 bg-gradient-to-r from-gray-700 to-gray-400/10 text-gray-800' : 'border-orange- text-gray-700 hover:border-white/20'">
                🏦 Transfer Bank
              </button>
              <button type="button" @click="form.metode_pembayaran = 'cash'" :disabled="totalHarga > 500000" class="p-4 border text-center text-sm font-medium transition-smooth disabled:opacity-30 disabled:cursor-not-allowed" :class="form.metode_pembayaran === 'cash' ? 'border-gray-600 bg-gradient-to-r from-gray-700 to-gray-400/10 text-gray-800' : 'border-orange- text-gray-700 hover:border-white/20'">
                💵 Bayar di Hotel
              </button>
            </div>
          </div>
        </div>

        <button type="submit" :disabled="isLoading" class="w-full py-4 bg-gradient-to-r from-gray-700 to-gray-400 text-dark-950 font-bold text-lg hover:bg-gradient-to-r from-gray-700 to-gray-400 disabled:opacity-50 transition-smooth flex items-center justify-center gap-2">
          <svg v-if="isLoading" class="animate-spin w-5 h-5" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
          {{ isLoading ? 'Memproses...' : 'Konfirmasi Pemesanan' }}
        </button>
      </form>
    </div>
  </div>
</template>

<script setup lang="ts">
definePageMeta({ middleware: 'auth' })

const route = useRoute()
const { apiFetch, formatRupiah } = useApi()
const { isLoggedIn } = useAuth()

const tipeKamar = ref<any>(null)
const error = ref('')
const isLoading = ref(false)

const today = computed(() => new Date().toISOString().split('T')[0])

const form = ref({
  tgl_check_in: typeof route.query.check_in === 'string' ? route.query.check_in : '',
  tgl_check_out: typeof route.query.check_out === 'string' ? route.query.check_out : '',
  selected_rooms: [] as number[],
  nama_tamu: [] as string[],
  metode_pembayaran: 'transfer',
})

const checkInPlusOne = computed(() => {
  if (!form.value.tgl_check_in) return today.value
  const d = new Date(form.value.tgl_check_in)
  d.setDate(d.getDate() + 1)
  return d.toISOString().split('T')[0]
})

const nights = computed(() => {
  if (!form.value.tgl_check_in || !form.value.tgl_check_out) return 0
  const diff = new Date(form.value.tgl_check_out).getTime() - new Date(form.value.tgl_check_in).getTime()
  return Math.max(0, Math.ceil(diff / (1000 * 60 * 60 * 24)))
})

const totalHarga = computed(() => (tipeKamar.value?.harga || 0) * form.value.selected_rooms.length * nights.value)

const getNomorKamar = (id: number) => {
  return tipeKamar.value?.kamars?.find((k: any) => k.id === id)?.nomor_kamar || ''
}

const updateGuestNames = () => {
  const needed = form.value.selected_rooms.length
  while (form.value.nama_tamu.length < needed) form.value.nama_tamu.push('')
  if (form.value.nama_tamu.length > needed) form.value.nama_tamu = form.value.nama_tamu.slice(0, needed)
}

watch([() => form.value.tgl_check_in, () => form.value.tgl_check_out], async () => {
  if (form.value.tgl_check_in && form.value.tgl_check_out && tipeKamar.value) {
    tipeKamar.value = await apiFetch(`/tipe-kamar/${route.query.tipe}?check_in=${form.value.tgl_check_in}&check_out=${form.value.tgl_check_out}`)
    form.value.selected_rooms = []
    updateGuestNames()
  }
})

const handleBooking = async () => {
  error.value = ''

  // Validate selections
  if (form.value.selected_rooms.length === 0) {
    error.value = 'Silakan pilih minimal 1 kamar.'
    return
  }
  if (!form.value.tgl_check_in || !form.value.tgl_check_out) {
    error.value = 'Silakan isi tanggal check-in dan check-out.'
    return
  }
  if (nights.value <= 0) {
    error.value = 'Tanggal check-out harus setelah check-in.'
    return
  }

  isLoading.value = true
  try {
    const data = await apiFetch('/pemesanan', {
      method: 'POST',
      body: JSON.stringify({
        id_tipe_kamar: route.query.tipe,
        ...form.value,
      }),
    })
    navigateTo(`/booking/confirmation?id=${data.data.id}&nomor=${data.data.nomor_pemesanan}`)
  } catch (e: any) {
    error.value = e?.data?.message || e?.response?._data?.message || 'Pemesanan gagal. Silakan coba lagi.'

    // Reload room data to get fresh availability (rooms may have been booked by someone else)
    try {
      let url = `/tipe-kamar/${route.query.tipe}`
      if (form.value.tgl_check_in && form.value.tgl_check_out) {
        url += `?check_in=${form.value.tgl_check_in}&check_out=${form.value.tgl_check_out}`
      }
      tipeKamar.value = await apiFetch(url)
      // Clear selected rooms that are no longer available
      form.value.selected_rooms = form.value.selected_rooms.filter(roomId => {
        const kamar = tipeKamar.value?.kamars?.find((k: any) => k.id === roomId)
        return kamar?.is_available
      })
      updateGuestNames()
    } catch {}
  } finally {
    isLoading.value = false
  }
}

onMounted(async () => {
  if (!route.query.tipe) return navigateTo('/rooms')
  try {
    let url = `/tipe-kamar/${route.query.tipe}`
    if (form.value.tgl_check_in && form.value.tgl_check_out) {
      url += `?check_in=${form.value.tgl_check_in}&check_out=${form.value.tgl_check_out}`
    }
    tipeKamar.value = await apiFetch(url)
  } catch {
    navigateTo('/rooms')
  }
})
</script>
