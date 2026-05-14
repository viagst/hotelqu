<template>
  <div>
    <div class="flex items-center justify-between mb-6">
      <h1 class="text-2xl font-bold text-gray-800">Tipe Kamar</h1>
      <button
        @click="openModal()"
        class="px-5 py-2.5 rounded-sm bg-gradient-to-r from-gray-700 to-gray-500 text-white font-semibold text-sm border border-gray-600 hover:opacity-90 transition-opacity focus:outline-none focus:ring-2 focus:ring-gray-600/30"
      >
        + Tambah Tipe Kamar
      </button>
    </div>

    <div class="glass rounded-none overflow-hidden">
      <div class="overflow-x-auto">
        <table class="w-full text-sm">
          <thead>
            <tr class="bg-gray-100 border-b border-gray-300">
              <th class="text-left p-4 text-gray-700 font-medium">Nama Tipe</th>
              <th class="text-right p-4 text-gray-700 font-medium">Harga/Malam</th>
              <th class="text-center p-4 text-gray-700 font-medium">Total Stok</th>
              <th class="text-center p-4 text-gray-700 font-medium">Tersedia</th>
              <th class="text-left p-4 text-gray-700 font-medium">Fasilitas</th>
              <th class="text-center p-4 text-gray-700 font-medium">Aksi</th>
            </tr>
          </thead>

          <tbody>
            <tr
              v-for="t in tipeKamars"
              :key="t.id"
              class="border-b border-gray-200 hover:bg-gray-50 transition-colors"
            >
              <td class="p-4 text-gray-800 font-medium">{{ t.nama_tipe_kamar }}</td>
              <td class="p-4 text-right text-gray-800 font-semibold">{{ formatRupiah(t.harga) }}</td>
              <td class="p-4 text-center text-gray-700">{{ t.stok }}</td>

              <!-- Tersedia: tampilkan angka jelas, warna merah bila habis -->
              <td class="p-4 text-center">
                <span
                  class="font-semibold"
                  :class="tersedia(t) === 0 ? 'text-red-500' : tersedia(t) <= 2 ? 'text-yellow-600' : 'text-green-600'"
                >
                  {{ tersedia(t) }}
                </span>
                <span class="text-gray-400 text-xs"> / {{ t.stok }}</span>
              </td>

              <td class="p-4">
                <div class="flex flex-wrap gap-1">
                  <span
                    v-for="(f, i) in (t.fasilitas_tipe || []).slice(0, 6)"
                    :key="i"
                    class="px-2 py-0.5 rounded bg-gray-100 border border-gray-300 text-xs text-gray-600"
                  >
                    {{ f }}
                  </span>
                  <span
                    v-if="(t.fasilitas_tipe || []).length > 6"
                    class="px-2 py-0.5 text-xs text-gray-400"
                  >
                    +{{ (t.fasilitas_tipe || []).length - 6 }} lainnya
                  </span>
                </div>
              </td>

              <td class="p-4 text-center">
                <div class="flex items-center justify-center gap-2">
                  <button
                    @click="openModal(t)"
                    class="px-3 py-1.5 rounded-sm bg-gray-700 text-white text-xs font-medium hover:bg-gray-600 transition-colors"
                  >
                    Edit
                  </button>
                  <button
                    @click="hapus(t.id)"
                    class="px-3 py-1.5 rounded-sm bg-red-50 border border-red-300 text-red-500 text-xs font-medium hover:bg-red-100 transition-colors"
                  >
                    Hapus
                  </button>
                </div>
              </td>
            </tr>

            <tr v-if="tipeKamars.length === 0">
              <td colspan="6" class="p-8 text-center text-gray-400 text-sm">Belum ada tipe kamar.</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Modal -->
    <Teleport to="body">
      <div
        v-if="showModal"
        class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm"
        @click.self="closeModal"
      >
        <div class="w-full max-w-lg glass rounded-none p-8 animate-scale-in max-h-[90vh] overflow-y-auto">
          <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-bold text-gray-800">
              {{ editItem ? 'Edit Tipe Kamar' : 'Tambah Tipe Kamar' }}
            </h2>
            <button @click="closeModal" class="text-gray-400 hover:text-gray-700 text-2xl leading-none">&times;</button>
          </div>

          <form @submit.prevent="saveItem" class="space-y-4">
            <!-- Nama -->
            <div>
              <label class="block text-xs font-medium text-gray-600 mb-1.5">Nama Tipe <span class="text-red-500">*</span></label>
              <input
                v-model="form.nama_tipe_kamar"
                required
                placeholder="Contoh: Deluxe Room"
                class="w-full px-4 py-3 rounded-sm bg-white border border-gray-300 text-gray-800 text-sm focus:ring-2 focus:ring-gray-400/50 focus:outline-none"
              />
            </div>

            <!-- Harga & Stok -->
            <div class="grid grid-cols-2 gap-4">
              <div>
                <label class="block text-xs font-medium text-gray-600 mb-1.5">Harga/Malam <span class="text-red-500">*</span></label>
                <input
                  v-model.number="form.harga"
                  type="number"
                  min="0"
                  required
                  class="w-full px-4 py-3 rounded-sm bg-white border border-gray-300 text-gray-800 text-sm focus:ring-2 focus:ring-gray-400/50 focus:outline-none"
                />
              </div>
              <div>
                <label class="block text-xs font-medium text-gray-600 mb-1.5">Stok <span class="text-red-500">*</span></label>
                <input
                  v-model.number="form.stok"
                  type="number"
                  min="0"
                  required
                  class="w-full px-4 py-3 rounded-sm bg-white border border-gray-300 text-gray-800 text-sm focus:ring-2 focus:ring-gray-400/50 focus:outline-none"
                />
              </div>
            </div>

            <!-- Deskripsi -->
            <div>
              <label class="block text-xs font-medium text-gray-600 mb-1.5">Deskripsi</label>
              <textarea
                v-model="form.deskripsi"
                rows="3"
                placeholder="Deskripsi singkat tipe kamar..."
                class="w-full px-4 py-3 rounded-sm bg-white border border-gray-300 text-gray-800 text-sm focus:ring-2 focus:ring-gray-400/50 focus:outline-none resize-none"
              />
            </div>

            <!-- Foto -->
            <div>
              <label class="block text-xs font-medium text-gray-600 mb-1.5">Foto Kamar <span class="text-gray-400">(opsional)</span></label>
              <input
                type="file"
                @change="handleFile"
                accept="image/*"
                class="w-full px-4 py-2 rounded-sm bg-white border border-gray-300 text-gray-800 text-sm focus:outline-none file:mr-3 file:py-1.5 file:px-3 file:rounded-sm file:border-0 file:text-xs file:font-semibold file:bg-gray-700 file:text-white hover:file:bg-gray-600"
              />
              <div v-if="fotoPreview || editItem?.foto" class="mt-2">
                <img
                  :src="fotoPreview || getImageUrl(editItem?.foto)"
                  alt="Preview"
                  class="h-24 w-32 object-cover rounded-sm border border-gray-300"
                />
                <button
                  v-if="fotoPreview"
                  @click="clearFoto"
                  type="button"
                  class="mt-1 text-xs text-red-400 hover:text-red-600"
                >Hapus preview</button>
              </div>
            </div>

            <!-- Fasilitas -->
            <div>
              <label class="block text-xs font-medium text-gray-600 mb-1.5">Fasilitas</label>
              <div class="space-y-2">
                <div v-if="fasilitas.length > 0" class="flex flex-wrap gap-1.5 p-2 bg-gray-50 border border-gray-200 rounded-sm min-h-8">
                  <span
                    v-for="(f, i) in fasilitas"
                    :key="i"
                    class="inline-flex items-center gap-1 px-2.5 py-0.5 bg-white border border-gray-300 text-xs text-gray-700 rounded"
                  >
                    {{ f }}
                    <button
                      @click="removeFasilitas(i)"
                      class="text-red-400 hover:text-red-600 ml-0.5 font-bold"
                      type="button"
                    >×</button>
                  </span>
                </div>
                <p v-else class="text-xs text-gray-400 italic">Belum ada fasilitas ditambahkan.</p>

                <div class="flex gap-2">
                  <input
                    v-model="newFasilitas"
                    @keyup.enter.prevent="addFasilitas"
                    placeholder="Ketik lalu Enter atau klik Tambah..."
                    class="flex-1 px-3 py-2 rounded-sm bg-white border border-gray-300 text-gray-800 text-sm focus:ring-2 focus:ring-gray-400/50 focus:outline-none"
                  />
                  <button
                    @click="addFasilitas"
                    type="button"
                    class="px-4 py-2 bg-gray-600 text-white text-sm border border-gray-700 rounded-sm hover:bg-gray-500 transition-colors"
                  >
                    Tambah
                  </button>
                </div>
              </div>
            </div>

            <!-- Tombol -->
            <div class="flex gap-3 pt-2 border-t border-gray-200">
              <button
                type="button"
                @click="closeModal"
                class="flex-1 py-3 rounded-sm bg-gray-100 text-gray-700 font-medium border border-gray-300 hover:bg-gray-200 transition-colors"
              >
                Batal
              </button>
              <button
                type="submit"
                class="flex-1 py-3 rounded-sm bg-gradient-to-r from-gray-700 to-gray-500 text-white font-semibold hover:opacity-90 transition-opacity"
              >
                {{ editItem ? 'Simpan Perubahan' : 'Tambah Tipe Kamar' }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </Teleport>
  </div>
</template>

<script setup lang="ts">
definePageMeta({ layout: 'panel', middleware: 'auth' })

const { apiFetch, formatRupiah, getImageUrl } = useApi()
const tipeKamars = ref<any[]>([])
const showModal = ref(false)
const editItem = ref<any>(null)

const defaultForm = () => ({ nama_tipe_kamar: '', harga: 0, stok: 1, deskripsi: '' })
const form = ref(defaultForm())
const fasilitas = ref<string[]>([])
const newFasilitas = ref('')
const fotoFile = ref<File | null>(null)
const fotoPreview = ref<string>('')

// Hitung kamar tersedia dengan aman
const tersedia = (t: any): number => {
  if (typeof t.available === 'number') return t.available
  if (typeof t.kamars_count === 'number') return t.kamars_count
  return t.stok ?? 0
}

const handleFile = (e: Event) => {
  const target = e.target as HTMLInputElement
  if (target.files && target.files.length > 0) {
    fotoFile.value = target.files[0] || null
    if (fotoFile.value) {
      const reader = new FileReader()
      reader.onload = (event) => {
        fotoPreview.value = event.target?.result as string
      }
      reader.readAsDataURL(fotoFile.value)
    }
  }
}

const clearFoto = () => {
  fotoFile.value = null
  fotoPreview.value = ''
}

const addFasilitas = () => {
  const val = newFasilitas.value.trim()
  if (val && !fasilitas.value.includes(val)) {
    fasilitas.value.push(val)
    newFasilitas.value = ''
  }
}

const removeFasilitas = (index: number) => {
  fasilitas.value.splice(index, 1)
}

const loadData = async () => {
  tipeKamars.value = await apiFetch('/admin/tipe-kamar')
}

const closeModal = () => {
  showModal.value = false
}

const openModal = async (item?: any) => {
  // Reset semua state dulu (konsisten untuk Tambah maupun Edit)
  editItem.value = null
  form.value = defaultForm()
  fasilitas.value = []
  newFasilitas.value = ''
  fotoFile.value = null
  fotoPreview.value = ''

  if (item) {
    // Mode Edit: fetch data terbaru lalu isi form
    try {
      const freshItem = await apiFetch(`/admin/tipe-kamar/${item.id}`)
      editItem.value = freshItem
      form.value = {
        nama_tipe_kamar: freshItem.nama_tipe_kamar ?? '',
        harga: freshItem.harga ?? 0,
        stok: freshItem.stok ?? 1,
        deskripsi: freshItem.deskripsi ?? '',
      }
      fasilitas.value = Array.isArray(freshItem.fasilitas_tipe) ? [...freshItem.fasilitas_tipe] : []
    } catch (e) {
      alert('Gagal memuat data tipe kamar.')
      return
    }
  }

  showModal.value = true
}

const saveItem = async () => {
  if (!form.value.nama_tipe_kamar.trim()) {
    alert('Nama tipe kamar wajib diisi!')
    return
  }
  if (form.value.harga < 0 || form.value.stok < 0) {
    alert('Harga dan stok tidak boleh negatif!')
    return
  }

  const formData = new FormData()
  formData.append('nama_tipe_kamar', form.value.nama_tipe_kamar.trim())
  formData.append('harga', form.value.harga.toString())
  formData.append('stok', form.value.stok.toString())
  formData.append('deskripsi', form.value.deskripsi)

  const validFasilitas = fasilitas.value.filter(f => f.trim())
  validFasilitas.forEach((f, i) => formData.append(`fasilitas_tipe[${i}]`, f))

  if (fotoFile.value) {
    formData.append('foto', fotoFile.value)
  }

  try {
    if (editItem.value) {
      await apiFetch(`/admin/tipe-kamar/${editItem.value.id}`, { method: 'PUT', body: formData })
    } else {
      await apiFetch('/admin/tipe-kamar', { method: 'POST', body: formData })
    }
    closeModal()
    await loadData()
  } catch (error) {
    console.error('Failed to save item', error)
    alert('Gagal menyimpan data. Cek koneksi atau coba lagi.')
  }
}

const { confirm } = useConfirmDialog()

const hapus = async (id: number) => {
  if (await confirm({ title: 'Hapus Tipe Kamar', message: 'Yakin ingin menghapus tipe kamar ini beserta semua sub-kamarnya? Tindakan ini tidak bisa dibatalkan.', type: 'danger' })) {
    try {
      await apiFetch(`/admin/tipe-kamar/${id}`, { method: 'DELETE' })
      await loadData()
    } catch {
      alert('Gagal menghapus. Mungkin masih ada pemesanan aktif.')
    }
  }
}

onMounted(loadData)
</script>