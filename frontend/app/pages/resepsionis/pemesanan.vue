<template>
  <div>
    <h1 class="text-2xl font-bold text-gray-800 mb-6">Kelola Pemesanan</h1>

    <!-- Filters -->
    <div class="border border-orange- bg-white p-4 mb-6 flex flex-wrap items-center gap-3">
      <select v-model="statusFilter" @change="loadData" class="px-4 py-2.5 bg-orange- border border-orange- text-gray-800 text-sm focus:border-gray-600 focus:outline-none">
        <option value="" class="bg-white">Semua Status</option>
        <option value="baru" class="bg-white">Baru</option>
        <option value="check_in" class="bg-white">Check In</option>
        <option value="check_out" class="bg-white">Check Out</option>
      </select>
      <input v-model="searchName" @input="loadData" type="text" placeholder="Cari nama tamu..." class="flex-1 min-w-[200px] px-4 py-2.5 bg-orange- border border-orange- text-gray-800 placeholder-gray-600 text-sm focus:border-gray-600 focus:outline-none">
    </div>

    <div class="border border-orange- bg-white overflow-hidden">
      <div class="overflow-x-auto">
        <table class="w-full text-sm">
          <thead>
            <tr class="border-b border-orange-">
              <th class="text-left p-4 text-gray-700 font-medium">No. Pemesanan</th>
              <th class="text-left p-4 text-gray-700 font-medium">Tamu</th>
              <th class="text-left p-4 text-gray-700 font-medium">Check-in</th>
              <th class="text-left p-4 text-gray-700 font-medium">Check-out</th>
              <th class="text-right p-4 text-gray-700 font-medium">Total</th>
              <th class="text-left p-4 text-gray-700 font-medium">Status Bayar</th>
              <th class="text-center p-4 text-gray-700 font-medium">Status Kamar</th>
              <th class="text-center p-4 text-gray-700 font-medium">Aksi</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="p in pemesanans" :key="p.id" class="border-b border-orange- hover:bg-orange- transition-smooth">
              <td class="p-4 text-gray-800 font-mono text-xs">{{ p.nomor_pemesanan }}</td>
              <td class="p-4 text-gray-800">{{ p.nama_pemesan }}</td>
              <td class="p-4 text-gray-600">{{ formatDateShort(p.tgl_check_in) }}</td>
              <td class="p-4 text-gray-600">{{ formatDateShort(p.tgl_check_out) }}</td>
              <td class="p-4 text-right text-gray-800 font-medium">{{ formatRupiah(p.total_harga) }}</td>
              <td class="p-4 text-left">
                <span class="px-2 py-1 text-[10px] font-bold uppercase border" :class="{
                  'bg-gray-600/10 text-red-400 border-gray-600/20': p.status_pembayaran === 'belum_dibayar',
                  'bg-yellow-500/10 text-yellow-400 border-yellow-500/20': p.status_pembayaran === 'menunggu_validasi',
                  'bg-green-500/10 text-green-400 border-green-500/20': p.status_pembayaran === 'lunas',
                }">{{ (p.status_pembayaran || 'belum_dibayar').replace('_', ' ') }}</span>
              </td>
              <td class="p-4 text-center">
                <span class="px-2 py-1 text-[10px] font-bold uppercase border" :class="{
                  'bg-yellow-500/10 text-yellow-400 border-yellow-500/20': p.status_pemesanan === 'baru',
                  'bg-gradient-to-r from-gray-700 to-gray-400/10 text-gray-800 border-gray-600/20': p.status_pemesanan === 'check_in',
                  'bg-green-500/10 text-green-400 border-green-500/20': p.status_pemesanan === 'check_out',
                }">{{ p.status_pemesanan.replace('_', ' ') }}</span>
              </td>
              <td class="p-4 text-center">
                <button v-if="p.bukti_transfer" @click="openBuktiModal(p)" class="px-3 py-1.5 bg-gray-1000/10 text-gray-400 text-xs font-medium mr-1 inline-block border border-gray-2000/20">
                  {{ p.status_pembayaran === 'menunggu_validasi' ? 'Validasi Bukti' : 'Lihat Bukti' }}
                </button>
                <button v-if="p.metode_pembayaran === 'cash' && p.status_pembayaran !== 'lunas'" @click="markCashPaymentDone(p.id)" class="px-3 py-1.5 bg-green-500/10 text-green-400 text-xs font-medium mr-1 inline-block border border-green-500/20">Tandai Lunas</button>
                <button v-if="p.status_pemesanan === 'baru'" @click="updateStatus(p.id, 'check_in')" class="px-3 py-1.5 bg-gradient-to-r from-gray-700 to-gray-400/10 text-gray-800 text-xs font-medium mr-1 inline-block border border-gray-600/20">Check In</button>
                <button v-if="p.status_pemesanan === 'check_in'" @click="openCheckoutModal(p)" class="px-3 py-1.5 bg-green-500/10 text-green-400 text-xs font-medium inline-block border border-green-500/20">Check Out</button>
                <button v-if="p.denda > 0" @click="openDendaModal(p)" class="px-3 py-1.5 bg-yellow-500/10 text-yellow-500 text-xs font-medium ml-1 inline-block border border-yellow-500/20">Lihat Denda</button>
                <button @click="navigateTo(`/invoice/${p.id}`)" class="px-3 py-1.5 bg-gradient-to-r from-gray-700 to-gray-400/10 text-gray-800 text-xs font-medium ml-1 inline-block border border-gray-600/20">Invoice</button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Checkout Modal with Late Fee -->
    <Teleport to="body">
      <div v-if="showCheckoutModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/70 backdrop-blur-sm" @click.self="showCheckoutModal = false">
        <div class="w-full max-w-md bg-white border border-orange- p-8 animate-scale-in">
          <h2 class="text-xl font-bold text-gray-800 mb-4">Check Out</h2>
          <p class="text-gray-700 text-sm mb-4">{{ checkoutItem?.nama_pemesan }} - {{ checkoutItem?.nomor_pemesanan }}</p>
          <div class="p-4 bg-yellow-500/5 border border-yellow-500/10 mb-4">
            <p class="text-sm text-yellow-400 font-semibold mb-2">⏰ Perhitungan Denda (Check-out 12:00)</p>
            <p class="text-xs text-gray-700 mb-3">Denda: Rp 50.000 / jam keterlambatan</p>
            <div>
              <label class="block text-xs font-medium text-gray-700 mb-1">Waktu Check-out Aktual</label>
              <input v-model="actualCheckoutTime" type="datetime-local" class="w-full px-4 py-3 bg-orange- border border-orange- text-gray-800 text-sm focus:border-gray-600 focus:outline-none">
            </div>
            <div class="mt-3 p-3 rounded-sm" :class="calculatedDenda > 0 ? 'bg-red-500/10 border border-red-500/20' : 'bg-green-500/10 border border-green-500/20'">
              <p :class="calculatedDenda > 0 ? 'text-red-500 font-bold' : 'text-green-600 font-bold'">Denda: {{ formatRupiah(calculatedDenda) }}</p>
              <p v-if="hoursLate > 0" class="text-xs text-gray-700 mt-1">{{ hoursLate }} jam keterlambatan</p>
              <p v-else class="text-xs text-gray-700 mt-1">Tidak ada keterlambatan</p>
            </div>
          </div>
          <div class="flex gap-3">
            <button @click="showCheckoutModal = false" class="flex-1 py-3 bg-orange- text-gray-600 font-medium border border-orange-">Batal</button>
            <button @click="processCheckout" class="flex-1 py-3 bg-green-500 text-gray-800 font-semibold hover:bg-green-600 transition-smooth">Proses Check Out</button>
          </div>
        </div>
      </div>
    </Teleport>

    <!-- Bukti Pembayaran Modal -->
    <Teleport to="body">
      <div v-if="showBuktiModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/70 backdrop-blur-sm" @click.self="showBuktiModal = false">
        <div class="w-full max-w-lg bg-white border border-orange- p-6 animate-scale-in">
          <div class="flex justify-between items-center mb-4">
            <h2 class="text-xl font-bold text-gray-800">Bukti Pembayaran</h2>
            <button @click="showBuktiModal = false" class="text-gray-700 hover:text-gray-800 text-2xl leading-none">&times;</button>
          </div>
          <p class="text-gray-700 text-sm mb-4">{{ selectedBuktiItem?.nama_pemesan }} - {{ selectedBuktiItem?.nomor_pemesanan }}</p>
          <div class="mb-6 overflow-hidden bg-slate-50 border border-orange- flex items-center justify-center min-h-[300px]">
            <img v-if="selectedBuktiItem?.bukti_transfer" :src="config.public.storageUrl + '/' + selectedBuktiItem.bukti_transfer" alt="Bukti Transfer" class="max-w-full max-h-[60vh] object-contain">
            <p v-else class="text-gray-600 text-sm">Tidak ada bukti pembayaran</p>
          </div>
          <div v-if="selectedBuktiItem?.status_pembayaran === 'menunggu_validasi'" class="flex gap-3">
            <button @click="updatePaymentStatus(selectedBuktiItem.id, 'belum_dibayar')" class="flex-1 py-3 bg-gray-600/10 text-gray-700 font-semibold border border-gray-600/20 hover:bg-gray-600/20 transition-smooth">Tidak Valid</button>
            <button @click="updatePaymentStatus(selectedBuktiItem.id, 'lunas')" class="flex-1 py-3 bg-green-500 text-gray-800 font-semibold hover:bg-green-600 transition-smooth">Valid & Lunas</button>
          </div>
          <div v-else class="flex justify-end gap-3">
            <button @click="showBuktiModal = false" class="w-full py-3 bg-orange- text-gray-800 font-semibold border border-orange- hover:bg-orange- transition-smooth">Tutup</button>
          </div>
        </div>
      </div>
    </Teleport>

    <!-- Denda Modal -->
    <Teleport to="body">
      <div v-if="showDendaModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/70 backdrop-blur-sm" @click.self="showDendaModal = false">
        <div class="w-full max-w-md bg-white border border-orange- p-6 animate-scale-in">
          <div class="flex justify-between items-center mb-4">
            <h2 class="text-xl font-bold text-gray-800">Detail Denda</h2>
            <button @click="showDendaModal = false" class="text-gray-700 hover:text-gray-800 text-2xl leading-none">&times;</button>
          </div>
          <p class="text-sm text-gray-700 mb-4">Nomor Pemesanan: <span class="font-medium">{{ selectedDendaItem?.nomor_pemesanan }}</span></p>
          <div class="p-4 rounded-sm bg-yellow-50 border border-yellow-300">
            <p class="text-sm text-yellow-700 mb-2">Jumlah denda keterlambatan</p>
            <p class="text-3xl font-bold text-yellow-800">{{ formatRupiah(selectedDendaItem?.denda || 0) }}</p>
            <p class="text-xs text-yellow-600 mt-2">Denda ini dihitung otomatis saat proses check-out.</p>
          </div>
          <div class="mt-6">
            <button @click="showDendaModal = false" class="w-full py-3 bg-orange- text-gray-800 font-semibold border border-orange- hover:bg-orange- transition-smooth">Tutup</button>
          </div>
        </div>
      </div>
    </Teleport>
  </div>
</template>

<script setup lang="ts">
definePageMeta({ layout: 'panel', middleware: 'auth' })

const { apiFetch, formatRupiah, formatDateShort } = useApi()
const config = useRuntimeConfig()

const pemesanans = ref<any[]>([])
const statusFilter = ref('')
const searchName = ref('')
const showCheckoutModal = ref(false)
const checkoutItem = ref<any>(null)
const actualCheckoutTime = ref('')
const showBuktiModal = ref(false)
const selectedBuktiItem = ref<any>(null)
const showDendaModal = ref(false)
const selectedDendaItem = ref<any>(null)

const openBuktiModal = (item: any) => {
  selectedBuktiItem.value = item
  showBuktiModal.value = true
}

const openDendaModal = (item: any) => {
  selectedDendaItem.value = item
  showDendaModal.value = true
}

const { confirm } = useConfirmDialog()

const updatePaymentStatus = async (id: number, status: string) => {
  const isLunas = status === 'lunas'
  const confirmed = await confirm({
    title: isLunas ? 'Validasi Pembayaran' : 'Tolak Pembayaran',
    message: isLunas ? 'Apakah Anda yakin bukti transfer ini Valid dan tamu sudah Lunas?' : 'Apakah Anda yakin bukti transfer TIDAK valid dan ingin ditolak/dihapus?',
    confirmText: isLunas ? 'Ya, Valid & Lunas' : 'Ya, Tolak',
    type: isLunas ? 'primary' : 'danger'
  })
  if (!confirmed) return
  try {
    await apiFetch(`/resepsionis/pemesanan/${id}/validasi-pembayaran`, { method: 'PUT', body: JSON.stringify({ status }) })
    showBuktiModal.value = false
    loadData()
  } catch (e: any) {
    alert(e.response?._data?.message || 'Gagal update status pembayaran')
  }
}

const markCashPaymentDone = async (id: number) => {
  const confirmed = await confirm({
    title: 'Tandai Pembayaran Cash Selesai',
    message: 'Apakah pembayaran cash sudah diterima dari tamu?',
    confirmText: 'Ya, Sudah Dibayar',
    type: 'primary'
  })
  if (!confirmed) return
  try {
    await apiFetch(`/resepsionis/pemesanan/${id}/validasi-pembayaran`, { method: 'PUT', body: JSON.stringify({ status: 'lunas' }) })
    loadData()
  } catch (e: any) {
    alert(e.response?._data?.message || 'Gagal update status pembayaran')
  }
}

const hoursLate = computed(() => {
  if (!checkoutItem.value || !actualCheckoutTime.value) return 0

  // backend assumes expected checkout time is 12:00 server-side
  const expected = safeParseDatetimeLocal(checkoutItem.value.tgl_check_out + 'T12:00')
  const actual = safeParseDatetimeLocal(actualCheckoutTime.value)

  if (!expected || Number.isNaN(expected.getTime()) || !actual) return 0
  if (actual.getTime() <= expected.getTime()) return 0

  return Math.ceil((actual.getTime() - expected.getTime()) / (1000 * 60 * 60))
})

const calculatedDenda = computed(() => hoursLate.value * 50000)



const loadData = async () => {
  let url = '/resepsionis/pemesanan?'
  if (statusFilter.value) url += `status=${statusFilter.value}&`
  if (searchName.value) url += `nama_tamu=${searchName.value}&`
  try {
    const res = await apiFetch(url)
    pemesanans.value = res.data || res
  } catch (e) {
    console.error(e)
  }
}

const updateStatus = async (id: number, status: string) => {
  const isCheckIn = status === 'check_in'
  const confirmed = await confirm({
    title: isCheckIn ? 'Check In Tamu' : 'Konfirmasi Status',
    message: isCheckIn ? 'Apakah tamu sudah tiba dan kunci sudah diserahkan?' : `Ubah status pemesanan menjadi ${status}?`,
    confirmText: isCheckIn ? 'Ya, Check In' : 'Ya, Ubah',
  })
  if (!confirmed) return
  try {
    await apiFetch(`/resepsionis/pemesanan/${id}/status`, { method: 'PUT', body: JSON.stringify({ status_pemesanan: status }) })
    loadData()
  } catch (e: any) {
    alert(e.response?._data?.message || e.message || 'Gagal mengubah status pemesanan')
  }
}

const openCheckoutModal = (item: any) => {
  checkoutItem.value = item
  const now = new Date()

  // datetime-local needs: YYYY-MM-DDTHH:mm
  actualCheckoutTime.value = now.toISOString().slice(0, 16)
  showCheckoutModal.value = true
}

const safeParseDatetimeLocal = (value: string) => {
  // value format: YYYY-MM-DDTHH:mm (from datetime-local)
  // Convert to ISO by appending seconds so browser parsing is consistent.
  if (!value) return null
  const iso = value.length === 16 ? `${value}:00` : value
  const d = new Date(iso)
  if (Number.isNaN(d.getTime())) return null
  return d
}


const processCheckout = async () => {
  if (!checkoutItem.value) return
  try {
    await apiFetch(`/resepsionis/pemesanan/${checkoutItem.value.id}/status`, {
      method: 'PUT',
      body: JSON.stringify({
        status_pemesanan: 'check_out',
        actual_checkout_time: actualCheckoutTime.value,
      }),
    })
    showCheckoutModal.value = false
    loadData()
  } catch (e: any) {
    alert(e.response?._data?.message || e.message || 'Gagal memproses check out')
  }
}

onMounted(loadData)
</script>
