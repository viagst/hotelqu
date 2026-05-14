<template>
  <div>
    <h1 class="text-2xl font-bold text-gray-800 mb-6">Kelola Pemesanan</h1>

    <!-- Filters -->
    <div class="border border-orange- bg-white p-4 mb-6 flex flex-wrap items-center gap-3">
      <select
        v-model="statusFilter"
        @change="loadData"
        class="px-4 py-2.5 border border-orange- text-gray-800 text-sm focus:border-gray-600 focus:outline-none"
      >
        <option value="">Semua Status</option>
        <option value="baru">Baru</option>
        <option value="check_in">Check In</option>
        <option value="check_out">Check Out</option>
      </select>

      <input
        v-model="searchName"
        @input="loadData"
        type="text"
        placeholder="Cari nama tamu..."
        class="flex-1 min-w-[200px] px-4 py-2.5 border border-orange- text-gray-800 placeholder-gray-600 text-sm focus:border-gray-600 focus:outline-none"
      >
    </div>

    <!-- TABLE -->
    <div class="border border-orange- bg-white overflow-hidden">
      <div class="overflow-x-auto">
        <table class="w-full text-sm">
          <thead>
            <tr class="border-b border-orange- bg-gray-100">
              <th class="text-left p-4 text-gray-700 font-medium">No. Pemesanan</th>
              <th class="text-left p-4 text-gray-700 font-medium">Tamu</th>
              <th class="text-left p-4 text-gray-700 font-medium">Kamar</th>
              <th class="text-left p-4 text-gray-700 font-medium">Check-in</th>
              <th class="text-left p-4 text-gray-700 font-medium">Check-out</th>
              <th class="text-right p-4 text-gray-700 font-medium">Total</th>
              <th class="text-left p-4 text-gray-700 font-medium">Status Bayar</th>
              <th class="text-center p-4 text-gray-700 font-medium">Status Kamar</th>
              <th class="text-center p-4 text-gray-700 font-medium">Aksi</th>
            </tr>
          </thead>

          <tbody>
            <tr
              v-for="p in pemesanans"
              :key="p.id"
              class="border-b border-orange- hover:bg-gray-50"
            >
              <td class="p-4 text-gray-800 font-mono text-xs">
                {{ p.nomor_pemesanan }}
              </td>

              <td class="p-4 text-gray-800">
                {{ p.nama_pemesan }}
              </td>

              <td class="p-4 text-gray-700 text-xs">
                <div v-for="d in p.detail_pemesanans" :key="d.id">
                  <span class="font-medium">{{ d.tipe_kamar?.nama_tipe_kamar }}</span>
                  <span v-if="d.kamar" class="text-gray-500"> #{{ d.kamar.nomor_kamar }}</span>
                </div>
              </td>

              <td class="p-4 text-gray-600">
                {{ formatDateShort(p.tgl_check_in) }}
              </td>

              <td class="p-4 text-gray-600">
                {{ formatDateShort(p.tgl_check_out) }}
              </td>

              <td class="p-4 text-right text-gray-800 font-medium">
                {{ formatRupiah(p.total_harga) }}
              </td>

              <!-- STATUS PEMBAYARAN -->
              <td class="p-4 text-left">
                <span
                  class="px-2 py-1 text-[10px] font-bold uppercase border"
                  :class="{
                    'bg-red-100 text-red-600 border-red-200':
                      p.status_pembayaran === 'belum_dibayar',

                    'bg-yellow-100 text-yellow-700 border-yellow-300':
                      p.status_pembayaran === 'menunggu_validasi',

                    'bg-green-100 text-green-700 border-green-300':
                      p.status_pembayaran === 'lunas',
                  }"
                >
                  {{ (p.status_pembayaran || 'belum_dibayar').replace('_', ' ') }}
                </span>
              </td>

              <!-- STATUS KAMAR -->
              <td class="p-4 text-center">
                <span
                  class="px-2 py-1 text-[10px] font-bold uppercase border"
                  :class="{
                    'bg-yellow-100 text-yellow-700 border-yellow-300':
                      p.status_pemesanan === 'baru',

                    'bg-blue-100 text-blue-700 border-blue-300':
                      p.status_pemesanan === 'check_in',

                    'bg-green-100 text-green-700 border-green-300':
                      p.status_pemesanan === 'check_out',
                  }"
                >
                  {{ p.status_pemesanan.replace('_', ' ') }}
                </span>
              </td>

              <!-- AKSI -->
              <td class="p-4 text-center whitespace-nowrap">

                <!-- VALIDASI BUKTI -->
                <button
                  v-if="p.bukti_transfer"
                  @click="openBuktiModal(p)"
                  class="px-3 py-1.5 bg-gray-200 text-gray-700 text-xs font-medium mr-1 border border-gray-300"
                >
                  {{
                    p.status_pembayaran === 'menunggu_validasi'
                      ? 'Validasi Bukti'
                      : 'Lihat Bukti'
                  }}
                </button>

                <!-- BUTTON BAYAR CASH -->
                <button
                  v-if="
                    p.metode_pembayaran === 'cash' &&
                    p.status_pembayaran !== 'lunas'
                  "
                  @click="openBayarModal(p)"
                  class="px-3 py-1.5 bg-green-500 text-white text-xs font-medium mr-1 border border-green-600 hover:bg-green-600"
                >
                  Bayar
                </button>

                <!-- CHECK IN -->
                <button
                  v-if="p.status_pemesanan === 'baru'"
                  @click="updateStatus(p.id, 'check_in')"
                  class="px-3 py-1.5 bg-blue-500 text-white text-xs font-medium mr-1 border border-blue-600 hover:bg-blue-600"
                >
                  Check In
                </button>

                <!-- CHECK OUT -->
                <button
                  v-if="p.status_pemesanan === 'check_in'"
                  @click="openCheckoutModal(p)"
                  class="px-3 py-1.5 bg-orange-500 text-white text-xs font-medium border border-orange-600 hover:bg-orange-600"
                >
                  Check Out
                </button>

                <!-- DENDA -->
                <button
                  v-if="p.denda > 0"
                  @click="openDendaModal(p)"
                  class="px-3 py-1.5 bg-yellow-400 text-yellow-900 text-xs font-medium ml-1 border border-yellow-500"
                >
                  Lihat Denda
                </button>

                <!-- INVOICE -->
                <button
                  @click="navigateTo(`/invoice/${p.id}`)"
                  class="px-3 py-1.5 bg-gray-700 text-white text-xs font-medium ml-1 border border-gray-800"
                >
                  Invoice
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- MODAL BAYAR CASH -->
    <Teleport to="body">
      <div
        v-if="showBayarModal"
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 p-4"
        @click.self="showBayarModal = false"
      >
        <div class="w-full max-w-md bg-white p-6 border border-gray-300">
          <h2 class="text-xl font-bold text-gray-800 mb-4">
            Pembayaran Cash
          </h2>

          <div class="space-y-3">
            <div>
              <p class="text-sm text-gray-600">Nama Tamu</p>
              <p class="font-semibold text-gray-800">
                {{ bayarItem?.nama_pemesan }}
              </p>
            </div>

            <div>
              <p class="text-sm text-gray-600">Total Tagihan</p>
              <p class="font-bold text-lg text-gray-800">
                {{ formatRupiah(totalTagihan) }}
              </p>
            </div>

            <div>
              <label class="block text-sm text-gray-700 mb-1">
                Jumlah Bayar
              </label>

              <input
                v-model="jumlahBayar"
                type="number"
                class="w-full px-4 py-2 border border-gray-300 focus:outline-none focus:border-gray-500"
                placeholder="Masukkan jumlah bayar"
              >
            </div>

            <div>
              <p class="text-sm text-gray-600">Kembalian</p>
              <p class="font-bold text-green-600 text-lg">
                {{ formatRupiah(kembalian) }}
              </p>
            </div>
          </div>

          <div class="flex gap-3 mt-6">
            <button
              @click="showBayarModal = false"
              class="flex-1 py-2 border border-gray-300 text-gray-700"
            >
              Batal
            </button>

            <button
              @click="submitBayarCash"
              class="flex-1 py-2 bg-green-500 hover:bg-green-600 text-white"
            >
              Simpan Pembayaran
            </button>
          </div>
        </div>
      </div>
    </Teleport>

    <!-- CHECKOUT MODAL -->
    <Teleport to="body">
      <div
        v-if="showCheckoutModal"
        class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/70"
        @click.self="showCheckoutModal = false"
      >
        <div class="w-full max-w-md bg-white border border-orange- p-8">
          <h2 class="text-xl font-bold text-gray-800 mb-4">
            Check Out
          </h2>

          <p class="text-gray-700 text-sm mb-4">
            {{ checkoutItem?.nama_pemesan }} -
            {{ checkoutItem?.nomor_pemesanan }}
          </p>

          <div class="p-4 border border-yellow-300 mb-4 bg-yellow-50">
            <p class="text-sm text-yellow-700 font-semibold mb-2">
              Perhitungan Denda
            </p>

            <p class="text-xs text-gray-700 mb-3">
              Denda: Rp 50.000 / jam keterlambatan
            </p>

            <div>
              <label class="block text-xs font-medium text-gray-700 mb-1">
                Waktu Check-out Aktual
              </label>

              <input
                v-model="actualCheckoutTime"
                type="datetime-local"
                class="w-full px-4 py-3 border border-gray-300 text-sm focus:outline-none"
              >
            </div>

            <div class="mt-3 p-3 border border-gray-300 bg-white">
              <p class="font-bold text-red-600">
                Denda: {{ formatRupiah(calculatedDenda) }}
              </p>

              <p class="text-xs text-gray-700 mt-1">
                {{ hoursLate }} jam keterlambatan
              </p>
            </div>
          </div>

          <div class="flex gap-3">
            <button
              @click="showCheckoutModal = false"
              class="flex-1 py-3 border border-gray-300 text-gray-700"
            >
              Batal
            </button>

            <button
              @click="processCheckout"
              class="flex-1 py-3 bg-green-500 text-white hover:bg-green-600"
            >
              Proses Check Out
            </button>
          </div>
        </div>
      </div>
    </Teleport>
  </div>
</template>

<script setup lang="ts">
definePageMeta({
  layout: 'panel',
  middleware: 'auth'
})

const { apiFetch, formatRupiah, formatDateShort } = useApi()

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

const showBayarModal = ref(false)
const bayarItem = ref<any>(null)
const jumlahBayar = ref(0)

const { confirm } = useConfirmDialog()

const totalTagihan = computed(() => {
  if (!bayarItem.value) return 0
  return bayarItem.value.total_harga + (bayarItem.value.denda || 0)
})

const kembalian = computed(() => {
  return Math.max(jumlahBayar.value - totalTagihan.value, 0)
})

const openBayarModal = (item: any) => {
  bayarItem.value = item
  jumlahBayar.value = totalTagihan.value
  showBayarModal.value = true
}

const submitBayarCash = async () => {
  if (!bayarItem.value) return

  if (jumlahBayar.value < totalTagihan.value) {
    alert('Jumlah bayar kurang')
    return
  }

  try {
    await apiFetch(
      `/resepsionis/pemesanan/${bayarItem.value.id}/validasi-pembayaran`,
      {
        method: 'PUT',
        body: JSON.stringify({
          status: 'lunas',
          jumlah_bayar: jumlahBayar.value,
          kembalian: kembalian.value
        })
      }
    )

    showBayarModal.value = false
    loadData()
  } catch (e: any) {
    alert(
      e.response?._data?.message ||
      'Gagal menyimpan pembayaran'
    )
  }
}

const openBuktiModal = (item: any) => {
  selectedBuktiItem.value = item
  showBuktiModal.value = true
}

const openDendaModal = (item: any) => {
  selectedDendaItem.value = item
  showDendaModal.value = true
}

const loadData = async () => {
  let url = '/resepsionis/pemesanan?'

  if (statusFilter.value) {
    url += `status=${statusFilter.value}&`
  }

  if (searchName.value) {
    url += `nama_tamu=${searchName.value}&`
  }

  try {
    const res = await apiFetch(url)
    pemesanans.value = res.data || res
  } catch (e) {
    console.error(e)
  }
}

const updateStatus = async (id: number, status: string) => {
  try {
    await apiFetch(`/resepsionis/pemesanan/${id}/status`, {
      method: 'PUT',
      body: JSON.stringify({
        status_pemesanan: status
      })
    })

    loadData()
  } catch (e: any) {
    alert(
      e.response?._data?.message ||
      'Gagal update status'
    )
  }
}

const openCheckoutModal = (item: any) => {
  checkoutItem.value = item

  const now = new Date()

  actualCheckoutTime.value = now
    .toISOString()
    .slice(0, 16)

  showCheckoutModal.value = true
}

const safeParseDatetimeLocal = (value: string) => {
  if (!value) return null

  const iso =
    value.length === 16
      ? `${value}:00`
      : value

  const d = new Date(iso)

  if (Number.isNaN(d.getTime())) return null

  return d
}

const hoursLate = computed(() => {
  if (!checkoutItem.value || !actualCheckoutTime.value) {
    return 0
  }

  const expected = safeParseDatetimeLocal(
    checkoutItem.value.tgl_check_out + 'T12:00'
  )

  const actual = safeParseDatetimeLocal(
    actualCheckoutTime.value
  )

  if (!expected || !actual) return 0

  if (actual.getTime() <= expected.getTime()) {
    return 0
  }

  return Math.ceil(
    (actual.getTime() - expected.getTime()) /
    (1000 * 60 * 60)
  )
})

const calculatedDenda = computed(() => {
  return hoursLate.value * 50000
})

const processCheckout = async () => {
  if (!checkoutItem.value) return

  try {
    await apiFetch(
      `/resepsionis/pemesanan/${checkoutItem.value.id}/status`,
      {
        method: 'PUT',
        body: JSON.stringify({
          status_pemesanan: 'check_out',
          actual_checkout_time: actualCheckoutTime.value,
        }),
      }
    )

    showCheckoutModal.value = false
    loadData()
  } catch (e: any) {
    alert(
      e.response?._data?.message ||
      e.message ||
      'Gagal proses checkout'
    )
  }
}

onMounted(loadData)
</script>