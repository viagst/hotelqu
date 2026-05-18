<template>
  <div style="font-family: 'Courier New', Courier, monospace; background-color: #e0e0e0; padding: 20px; min-height: 100vh;">
    <div style="max-width: 600px; margin: 0 auto; background-color: white; border: 2px solid black; padding: 20px;">
      <h1 style="text-align: center; text-decoration: underline; margin-bottom: 5px; font-size: 24px;">INVOICE</h1>
      <h2 style="text-align: center; margin-top: 0; font-size: 20px;">HOTELQU VIUL (HVL)</h2>
      <p style="text-align: center; font-size: 12px; border-bottom: 2px dashed black; padding-bottom: 15px;">
        Telp: +62 882-9316-5787 | Email: info@hotelquviul.com
      </p>
      
      <p style="margin-top: 15px;"><b>Order ID:</b> {{ booking?.nomor_pemesanan }}</p>
      <p><b>Tanggal Pesan:</b> {{ formatDateShort(booking?.tgl_pemesanan) }}</p>
      <p><b>Status:</b> {{ statusLabel }}</p>
      
      <hr style="border-top: 1px dashed black; margin: 20px 0;">
      
      <h3 style="margin-bottom: 5px; font-size: 16px;">Data Tamu:</h3>
      <table style="width: 100%; border: none; font-size: 14px;">
        <tbody>
          <tr><td style="width: 150px;">Nama Pemesan</td><td>: {{ booking?.nama_pemesan }}</td></tr>
          <tr><td>Email</td><td>: {{ booking?.email_pemesan }}</td></tr>
          <tr><td>Check In</td><td>: {{ formatDateShort(booking?.tgl_check_in) }}</td></tr>
          <tr><td>Check Out</td><td>: {{ formatDateShort(booking?.tgl_check_out) }}</td></tr>
          <tr><td>Lama Menginap</td><td>: {{ booking?.jumlah_malam }} Malam</td></tr>
        </tbody>
      </table>

      <!-- Daftar Tamu per Kamar -->
      <div v-if="booking?.detail_pemesanans?.length" style="margin-top: 10px; font-size: 13px;">
        <b>Tamu Menginap:</b>
        <ul style="margin: 4px 0 0 18px; padding: 0;">
          <li v-for="(d, i) in booking.detail_pemesanans" :key="i">
            {{ d.nama_tamu_spesifik || booking.nama_pemesan }}
            <span v-if="d.tipe_kamar" style="color:#555;"> — {{ d.tipe_kamar.nama_tipe_kamar }}</span>
            <span v-if="d.kamar" style="color:#555;"> #{{ d.kamar.nomor_kamar }}</span>
          </li>
        </ul>
      </div>
      
      <br>
      
      <!-- Detail Kamar -->
      <table style="width: 100%; border-collapse: collapse; font-size: 14px;" border="1">
        <thead>
          <tr style="background-color: #f0f0f0;">
            <th style="padding: 8px; text-align: left;">Tipe Kamar</th>
            <th style="padding: 8px; text-align: left;">No. Kamar</th>
            <th style="padding: 8px; text-align: left;">Tamu</th>
            <th style="padding: 8px; text-align: right;">Harga/Malam</th>
            <th style="padding: 8px; text-align: right;">Total</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="(detail, index) in booking?.detail_pemesanans" :key="detail.id">
            <td style="padding: 8px;">
              <b>{{ detail.tipe_kamar?.nama_tipe_kamar }}</b>
            </td>
            <td style="padding: 8px;">
              {{ detail.kamar?.nomor_kamar ? '#' + detail.kamar.nomor_kamar : '-' }}
            </td>
            <td style="padding: 8px;">{{ detail.nama_tamu_spesifik || booking?.nama_pemesan || '-' }}</td>
            <td style="padding: 8px; text-align: right;">{{ formatRupiahPlain(detail.harga_kamar_instance || 0) }}</td>
            <td style="padding: 8px; text-align: right;">{{ formatRupiahPlain((detail.harga_kamar_instance || 0) * booking.jumlah_malam) }}</td>
          </tr>
        </tbody>
      </table>
      
      <!-- Rincian Pembayaran -->
      <div style="text-align: right; margin-top: 20px; font-size: 14px;">
        <table style="width: 100%; border: none; font-size: 14px;">
          <tbody>
            <tr>
              <td style="text-align: left; color: #555;">Subtotal Kamar:</td>
              <td style="text-align: right; padding-left: 20px;">{{ formatRupiahPlain(booking?.total_harga) }}</td>
            </tr>
            <tr v-if="booking?.denda > 0">
              <td style="text-align: left; color: #c00;">Denda Keterlambatan:</td>
              <td style="text-align: right; padding-left: 20px; color: #c00;">+ {{ formatRupiahPlain(booking?.denda) }}</td>
            </tr>
            <tr v-if="booking?.denda > 0">
              <td colspan="2" style="text-align: left; font-size: 11px; color: #555; padding-top: 2px;">
                ↳ Denda keterlambatan check-out (Rp 50.000/jam)
              </td>
            </tr>
            <tr>
              <td colspan="2"><hr style="border: none; border-top: 1px solid #000; margin: 8px 0;"></td>
            </tr>
            <tr>
              <td style="text-align: left; font-weight: bold; font-size: 16px;">TOTAL BAYAR:</td>
              <td style="text-align: right; font-weight: bold; font-size: 16px; padding-left: 20px;">
                {{ formatRupiahPlain((booking?.total_harga || 0) + (booking?.denda || 0)) }}
              </td>
            </tr>
            <tr v-if="booking?.jumlah_bayar > 0">
              <td style="text-align: left; color: #555; padding-top: 6px;">Jumlah Dibayar:</td>
              <td style="text-align: right; padding-left: 20px; color: #555; padding-top: 6px;">{{ formatRupiahPlain(booking?.jumlah_bayar) }}</td>
            </tr>
            <tr v-if="booking?.kembalian >= 0 && booking?.jumlah_bayar > 0">
              <td style="text-align: left; color: #006600; font-weight: bold; padding-top: 4px;">Kembalian:</td>
              <td style="text-align: right; padding-left: 20px; color: #006600; font-weight: bold; padding-top: 4px;">{{ formatRupiahPlain(booking?.kembalian) }}</td>
            </tr>
          </tbody>
        </table>

        <p style="margin-top: 10px;">Metode Pembayaran: {{ booking?.metode_pembayaran === 'transfer' ? 'Transfer Bank' : 'Bayar di Hotel (Cash)' }}</p>
      </div>

      <!-- Info Rekening Bank (untuk transfer) -->
      <div v-if="booking?.metode_pembayaran === 'transfer'" style="margin-top: 20px; padding: 12px; border: 1px solid #ccc; background: #f9f9f9; font-size: 13px;">
        <p style="font-weight: bold; margin-bottom: 8px; font-size: 14px;">📌 Info Rekening Pembayaran:</p>
        <table style="width: 100%; border: none; font-size: 13px;">
          <tbody>
            <tr>
              <td style="width: 80px; padding: 3px 0;"><b>BCA</b></td>
              <td>: 1234567890 a/n HOTELQU VIUL</td>
            </tr>
            <tr>
              <td style="padding: 3px 0;"><b>Mandiri</b></td>
              <td>: 0987654321 a/n HOTELQU VIUL</td>
            </tr>
          </tbody>
        </table>
        <p style="font-size: 11px; color: #555; margin-top: 6px;">
          * Harap transfer sesuai total tagihan dengan mencantumkan nomor pemesanan sebagai berita acara.
        </p>
      </div>

      <!-- Barcode untuk referensi pembayaran -->
      <div v-if="booking?.denda > 0 && booking?.metode_pembayaran === 'transfer'" style="margin-top: 10px; text-align: right;">
        <p style="font-size: 12px; margin-bottom: 6px;"><b>REF BAYAR DENDA:</b> {{ booking?.nomor_pemesanan }}</p>
        <svg width="260" height="70" viewBox="0 0 260 70" xmlns="http://www.w3.org/2000/svg">
          <rect x="0" y="0" width="260" height="70" fill="white" stroke="black"/>
          <g transform="translate(10,10)">
            <template v-for="(ch, i) in String(booking?.nomor_pemesanan || '').slice(0, 24)" :key="i">
              <rect :x="i*10" y="0" :width="(Number(ch) || ch.charCodeAt(0)) % 7 + 2" height="50" fill="black"/>
            </template>
          </g>
          <text x="130" y="64" text-anchor="middle" font-size="10">{{ booking?.nomor_pemesanan }}</text>
        </svg>
      </div>
      
      <hr style="border-top: 1px dashed black; margin: 20px 0;">
      <p style="text-align: center; font-size: 12px; margin-bottom: 0;">TERIMA KASIH TELAH MENGINAP DI HOTELQU VIUL.</p>
      
      <div style="text-align: center; margin-top: 30px;" class="no-print">
        <button @click="$router.back()" style="padding: 8px 15px; margin-right: 10px; cursor: pointer; border: 1px solid black; background-color: #ccc; font-weight: bold;">KEMBALI</button>
        <button @click="printPage()" style="padding: 8px 15px; cursor: pointer; border: 1px solid black; background-color: #333; color: white; font-weight: bold;">PRINT INVOICE</button>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue'

definePageMeta({ layout: false })

const route = useRoute()
const { apiFetch, formatDateShort } = useApi()
const booking = ref<any>(null)

const statusLabel = computed(() => {
  if (!booking.value) return ''
  if (booking.value.status_pemesanan === 'check_out') return 'LUNAS / SELESAI'
  if (booking.value.metode_pembayaran === 'cash') return 'BAYAR DI TEMPAT'
  if (booking.value.bukti_transfer) return 'MENUNGGU VALIDASI / SUDAH DIBAYAR'
  return 'BELUM DIBAYAR'
})

const formatRupiahPlain = (n: number) => {
  if (!n && n !== 0) return 'Rp 0'
  return `Rp ${new Intl.NumberFormat('id-ID').format(n)}`
}

const printPage = () => { window.print() }

onMounted(async () => {
  try {
    booking.value = await apiFetch(`/pemesanan/${route.params.id}/invoice`)
  } catch {
    navigateTo('/')
  }
})
</script>

<style scoped>
@media print {
  .no-print { display: none !important; }
  body, div { background-color: white !important; }
}
</style>
