<template>
  <div>
    <h1 class="text-2xl font-bold text-gray-800 mb-6">
      Laporan Harian
    </h1>

    <!-- FILTER -->
    <div class="glass rounded-none p-4 mb-6 flex items-center gap-4">
      <input
        v-model="tanggal"
        type="date"
        @change="loadData"
        class="px-4 py-2.5 rounded-sm border text-gray-800 text-sm"
      >

      <button
        @click="loadData"
        class="px-5 py-2.5 rounded-sm bg-gradient-to-r from-gray-700 to-gray-400 text-white font-semibold text-sm"
      >
        Lihat
      </button>
    </div>

    <div v-if="report" class="space-y-8">

      <!-- CHECK IN -->
      <div class="glass rounded-none p-6 overflow-x-auto">
        <h2 class="text-lg font-bold text-gray-800 mb-4">
          Data Check-in
        </h2>

        <table class="w-full border border-gray-300 text-sm">
          <thead class="bg-gray-200">
            <tr>
              <th class="p-3 border">No</th>
              <th class="p-3 border">Kode Booking</th>
              <th class="p-3 border">Nama Tamu</th>
              <th class="p-3 border">Jumlah Kamar</th>
              <th class="p-3 border">Total Bayar</th>
              <th class="p-3 border">Metode</th>
            </tr>
          </thead>

          <tbody>
            <tr
              v-for="(b, i) in report.check_in.data"
              :key="b.id"
              class="hover:bg-gray-50"
            >
              <td class="p-3 border text-center">
                {{ i + 1 }}
              </td>

              <td class="p-3 border">
                {{ b.nomor_pemesanan }}
              </td>

              <td class="p-3 border">
                {{ b.nama_pemesan }}
              </td>

              <td class="p-3 border text-center">
                {{ b.jumlah_kamar }}
              </td>

              <td class="p-3 border">
                {{ formatRupiah(b.total_harga) }}
              </td>

              <td class="p-3 border capitalize">
                {{ b.metode_pembayaran }}
              </td>
            </tr>

            <tr v-if="report.check_in.total === 0">
              <td colspan="6" class="p-4 border text-center text-gray-500">
                Tidak ada data check-in
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- CHECK OUT -->
      <div class="glass rounded-none p-6 overflow-x-auto">
        <h2 class="text-lg font-bold text-gray-800 mb-4">
          Data Check-out
        </h2>

        <table class="w-full border border-gray-300 text-sm">
          <thead class="bg-gray-200">
            <tr>
              <th class="p-3 border">No</th>
              <th class="p-3 border">Kode Booking</th>
              <th class="p-3 border">Nama Tamu</th>
              <th class="p-3 border">Total</th>
              <th class="p-3 border">Denda</th>
              <th class="p-3 border">Jumlah Bayar</th>
              <th class="p-3 border">Kembalian</th>
              <th class="p-3 border">Metode</th>
            </tr>
          </thead>

          <tbody>
            <tr
              v-for="(b, i) in report.check_out.data"
              :key="b.id"
              class="hover:bg-gray-50"
            >
              <td class="p-3 border text-center">
                {{ i + 1 }}
              </td>

              <td class="p-3 border">
                {{ b.nomor_pemesanan }}
              </td>

              <td class="p-3 border">
                {{ b.nama_pemesan }}
              </td>

              <td class="p-3 border">
                {{ formatRupiah(b.total_harga + (b.denda || 0)) }}
              </td>

              <td class="p-3 border">
                {{ b.denda > 0 ? formatRupiah(b.denda) : '-' }}
              </td>

              <td class="p-3 border">
                {{
                  b.jumlah_bayar
                    ? formatRupiah(b.jumlah_bayar)
                    : '-'
                }}
              </td>

              <td class="p-3 border">
                {{
                  b.kembalian
                    ? formatRupiah(b.kembalian)
                    : '-'
                }}
              </td>

              <td class="p-3 border capitalize">
                {{ b.metode_pembayaran }}
              </td>
            </tr>

            <tr v-if="report.check_out.total === 0">
              <td colspan="8" class="p-4 border text-center text-gray-500">
                Tidak ada data check-out
              </td>
            </tr>
          </tbody>
        </table>
      </div>

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

const loadData = async () => {
  try {
    report.value = await apiFetch(
      `/resepsionis/laporan?tanggal=${tanggal.value}`
    )
  } catch (e) {
    console.error(e)
  }
}

onMounted(loadData)
</script>