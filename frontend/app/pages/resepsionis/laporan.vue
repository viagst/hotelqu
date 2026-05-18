<template>
  <div>
    <h1 class="text-2xl font-bold text-gray-800 mb-6">
      Laporan Harian
    </h1>

    <!-- FILTER -->
    <div class="glass rounded-none p-4 mb-6 flex flex-wrap items-center gap-4">
      <div>
        <label class="block text-xs font-medium text-gray-600 mb-1">Tanggal</label>
        <input
          v-model="tanggal"
          type="date"
          @change="loadData"
          class="px-4 py-2.5 rounded-sm border text-gray-800 text-sm"
        >
      </div>

      <!-- Bulan ini shortcut -->
      <div class="flex gap-2 items-end">
        <button
          @click="setBulanIni"
          class="px-4 py-2.5 rounded-sm border border-gray-400 text-gray-700 text-sm font-medium hover:bg-gray-100"
        >
          Hari Ini
        </button>
        <button
          @click="loadData"
          class="px-5 py-2.5 rounded-sm bg-gradient-to-r from-gray-700 to-gray-400 text-white font-semibold text-sm"
        >
          Lihat
        </button>
        <button
          @click="printLaporan"
          class="px-5 py-2.5 rounded-sm bg-gray-200 border border-gray-400 text-gray-700 font-semibold text-sm"
        >
          🖨️ Print
        </button>
      </div>
    </div>

    <div v-if="report" class="space-y-8">

      <!-- RINGKASAN HARIAN -->
      <div class="grid sm:grid-cols-3 gap-4 no-print">
        <div class="glass rounded-none p-5">
          <p class="text-xs text-gray-600 mb-1">Total Check-In Hari Ini</p>
          <p class="text-2xl font-bold text-blue-600">{{ report.check_in.total }}</p>
        </div>
        <div class="glass rounded-none p-5">
          <p class="text-xs text-gray-600 mb-1">Total Check-Out Hari Ini</p>
          <p class="text-2xl font-bold text-green-600">{{ report.check_out.total }}</p>
        </div>
        <div class="glass rounded-none p-5">
          <p class="text-xs text-gray-600 mb-1">Total Pendapatan Hari Ini</p>
          <p class="text-xl font-bold text-gray-800">{{ formatRupiah(pendapatanHariIni) }}</p>
        </div>
      </div>

      <!-- PRINT HEADER -->
      <div class="print-only" style="display: none; text-align: center; margin-bottom: 20px; border-bottom: 2px solid #000; padding-bottom: 12px;">
        <h2 style="font-size: 20px; font-weight: bold; margin: 0;">HOTELQU VIUL (HVL)</h2>
        <p style="font-size: 13px; margin: 4px 0;">LAPORAN HARIAN RESEPSIONIS</p>
        <p style="font-size: 13px; margin: 0;">Tanggal: {{ tanggal }}</p>
      </div>

      <!-- CHECK IN -->
      <div class="glass rounded-none p-6 overflow-x-auto">
        <h2 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
          <span class="w-3 h-3 rounded-full bg-blue-500 inline-block"></span>
          Data Check-in — {{ report.check_in.total }} tamu
        </h2>

        <table class="w-full border border-gray-300 text-sm border-collapse">
          <thead class="bg-gray-200">
            <tr>
              <th class="p-3 border border-gray-300 text-left">No</th>
              <th class="p-3 border border-gray-300 text-left">Kode Booking</th>
              <th class="p-3 border border-gray-300 text-left">Nama Tamu</th>
              <th class="p-3 border border-gray-300 text-left">Kamar</th>
              <th class="p-3 border border-gray-300 text-center">Jml Kamar</th>
              <th class="p-3 border border-gray-300 text-right">Total Bayar</th>
              <th class="p-3 border border-gray-300 text-center">Metode</th>
            </tr>
          </thead>

          <tbody>
            <tr
              v-for="(b, i) in report.check_in.data"
              :key="b.id"
              class="hover:bg-gray-50"
            >
              <td class="p-3 border border-gray-300 text-center">{{ Number(i) + 1 }}</td>

              <td class="p-3 border border-gray-300 font-mono text-xs">
                {{ b.nomor_pemesanan }}
              </td>

              <td class="p-3 border border-gray-300">
                <div class="font-medium">{{ b.nama_pemesan }}</div>
                <!-- nama tamu per kamar -->
                <div
                  v-for="d in b.detail_pemesanans"
                  :key="d.id"
                  class="text-xs text-gray-500 mt-0.5"
                >
                  — {{ d.nama_tamu_spesifik }}
                  <span v-if="d.kamar"> (Kamar #{{ d.kamar.nomor_kamar }})</span>
                </div>
              </td>

              <td class="p-3 border border-gray-300 text-sm">
                <div v-for="d in b.detail_pemesanans" :key="d.id" class="text-xs">
                  <span class="font-medium">{{ d.tipe_kamar?.nama_tipe_kamar }}</span>
                  <span v-if="d.kamar" class="text-gray-500"> #{{ d.kamar.nomor_kamar }}</span>
                </div>
              </td>

              <td class="p-3 border border-gray-300 text-center">
                {{ b.jumlah_kamar }}
              </td>

              <td class="p-3 border border-gray-300 text-right">
                {{ formatRupiah(b.total_harga) }}
              </td>

              <td class="p-3 border border-gray-300 text-center capitalize">
                {{ b.metode_pembayaran === 'transfer' ? 'Transfer' : 'Cash' }}
              </td>
            </tr>

            <tr v-if="report.check_in.total === 0">
              <td colspan="7" class="p-4 border border-gray-300 text-center text-gray-500">
                Tidak ada data check-in hari ini
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- CHECK OUT -->
      <div class="glass rounded-none p-6 overflow-x-auto">
        <h2 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
          <span class="w-3 h-3 rounded-full bg-green-500 inline-block"></span>
          Data Check-out — {{ report.check_out.total }} tamu
        </h2>

        <table class="w-full border border-gray-300 text-sm border-collapse">
          <thead class="bg-gray-200">
            <tr>
              <th class="p-3 border border-gray-300 text-left">No</th>
              <th class="p-3 border border-gray-300 text-left">Kode Booking</th>
              <th class="p-3 border border-gray-300 text-left">Nama Tamu</th>
              <th class="p-3 border border-gray-300 text-left">Kamar</th>
              <th class="p-3 border border-gray-300 text-right">Total</th>
              <th class="p-3 border border-gray-300 text-right">Denda</th>
              <th class="p-3 border border-gray-300 text-right">Jumlah Bayar</th>
              <th class="p-3 border border-gray-300 text-right">Kembalian</th>
              <th class="p-3 border border-gray-300 text-center">Metode</th>
            </tr>
          </thead>

          <tbody>
            <tr
              v-for="(b, i) in report.check_out.data"
              :key="b.id"
              class="hover:bg-gray-50"
            >
              <td class="p-3 border border-gray-300 text-center">{{ Number(i) + 1 }}</td>

              <td class="p-3 border border-gray-300 font-mono text-xs">
                {{ b.nomor_pemesanan }}
              </td>

              <td class="p-3 border border-gray-300">
                <div class="font-medium">{{ b.nama_pemesan }}</div>
                <div
                  v-for="d in b.detail_pemesanans"
                  :key="d.id"
                  class="text-xs text-gray-500 mt-0.5"
                >
                  — {{ d.nama_tamu_spesifik }}
                  <span v-if="d.kamar"> (Kamar #{{ d.kamar.nomor_kamar }})</span>
                </div>
              </td>

              <td class="p-3 border border-gray-300 text-sm">
                <div v-for="d in b.detail_pemesanans" :key="d.id" class="text-xs">
                  <span class="font-medium">{{ d.tipe_kamar?.nama_tipe_kamar }}</span>
                  <span v-if="d.kamar" class="text-gray-500"> #{{ d.kamar.nomor_kamar }}</span>
                </div>
              </td>

              <td class="p-3 border border-gray-300 text-right">
                {{ formatRupiah(b.total_harga) }}
              </td>

              <td class="p-3 border border-gray-300 text-right" :class="b.denda > 0 ? 'text-red-600 font-medium' : ''">
                {{ b.denda > 0 ? formatRupiah(b.denda) : '-' }}
              </td>

              <td class="p-3 border border-gray-300 text-right">
                {{
                  b.jumlah_bayar
                    ? formatRupiah(b.jumlah_bayar)
                    : '-'
                }}
              </td>

              <td class="p-3 border border-gray-300 text-right" :class="b.kembalian > 0 ? 'text-green-600 font-medium' : ''">
                {{
                  b.kembalian != null && b.kembalian >= 0 && b.jumlah_bayar
                    ? formatRupiah(b.kembalian)
                    : '-'
                }}
              </td>

              <td class="p-3 border border-gray-300 text-center capitalize">
                {{ b.metode_pembayaran === 'transfer' ? 'Transfer' : 'Cash' }}
              </td>
            </tr>

            <tr v-if="report.check_out.total === 0">
              <td colspan="9" class="p-4 border border-gray-300 text-center text-gray-500">
                Tidak ada data check-out hari ini
              </td>
            </tr>

            <!-- TOTAL BARIS -->
            <tr v-if="report.check_out.total > 0" class="bg-gray-100 font-bold">
              <td colspan="4" class="p-3 border border-gray-300 text-right">TOTAL:</td>
              <td class="p-3 border border-gray-300 text-right">{{ formatRupiah(totalCheckout) }}</td>
              <td class="p-3 border border-gray-300 text-right text-red-600">{{ formatRupiah(totalDenda) }}</td>
              <td class="p-3 border border-gray-300 text-right">{{ formatRupiah(totalBayar) }}</td>
              <td class="p-3 border border-gray-300 text-right text-green-600">{{ formatRupiah(totalKembalian) }}</td>
              <td class="p-3 border border-gray-300"></td>
            </tr>
          </tbody>
        </table>
      </div>

    </div>

    <div v-else class="text-center py-12 text-gray-500">
      Pilih tanggal dan klik "Lihat" untuk menampilkan laporan.
    </div>
  </div>
</template>

<script setup lang="ts">
definePageMeta({
  layout: 'panel',
  middleware: 'auth'
})

const { apiFetch, formatRupiah } = useApi()

const tanggal = ref(
  new Date().toISOString().split('T')[0]
)

const report = ref<any>(null)

const setBulanIni = () => {
  tanggal.value = new Date().toISOString().split('T')[0]
  loadData()
}

const pendapatanHariIni = computed(() => {
  if (!report.value) return 0
  const fromCheckout = report.value.check_out.data.reduce((sum: number, b: any) => sum + (b.jumlah_bayar || 0), 0)
  return fromCheckout
})

const totalCheckout = computed(() => {
  if (!report.value) return 0
  return report.value.check_out.data.reduce((sum: number, b: any) => sum + (b.total_harga || 0), 0)
})

const totalDenda = computed(() => {
  if (!report.value) return 0
  return report.value.check_out.data.reduce((sum: number, b: any) => sum + (b.denda || 0), 0)
})

const totalBayar = computed(() => {
  if (!report.value) return 0
  return report.value.check_out.data.reduce((sum: number, b: any) => sum + (b.jumlah_bayar || 0), 0)
})

const totalKembalian = computed(() => {
  if (!report.value) return 0
  return report.value.check_out.data.reduce((sum: number, b: any) => sum + (b.kembalian || 0), 0)
})

const loadData = async () => {
  try {
    report.value = await apiFetch(
      `/resepsionis/laporan?tanggal=${tanggal.value}`
    )
  } catch (e) {
    console.error(e)
  }
}

const printLaporan = () => {
  window.print()
}

onMounted(loadData)
</script>

<style>
@media print {
  .no-print { display: none !important; }
  .print-only { display: block !important; }
  nav, header, aside, footer { display: none !important; }
  body { background: white !important; }
}
</style>