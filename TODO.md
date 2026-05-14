# TODO
- [x] Perbaiki frontend `resepsionis/pemesanan.vue`: pastikan `actualCheckoutTime` selalu diparse aman (tidak menghasilkan NaN).
- [x] Perbaiki computed `hoursLate` supaya aman terhadap invalid date (return 0 bukan NaN).
- [x] Hapus pengiriman field `denda` dari request `processCheckout` (biar backend menghitung sendiri).
- [x] Verifikasi UI: modal "Check Out" menampilkan jam keterlambatan dan denda angka normal (harusnya muncul bila terlambat).
- [x] Aturan denda (denda > 500 wajib transfer) diterapkan saat check-out.
- [x] Barcode invoice untuk denda sudah ditambahkan (sederhana); verifikasi visual.

- [x] Rapihin UI/typo/layout di frontend `admin/tipe-kamar.vue` (terutama tabel & modal)
- [x] Rapihin konsistensi reset form state pada buka modal (Tambah vs Edit)
- [x] Rapihin tampilan kolom "Tersida" — sekarang tampil angka dengan warna (hijau/kuning/merah)

## Backend
- [ ] Jalankan `composer install` di folder backend (vendor/ belum ada → artisan tidak bisa jalan)
