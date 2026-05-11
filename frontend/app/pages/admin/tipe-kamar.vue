<template>
  <div>
    <div class="flex items-center justify-between mb-6">
      <h1 class="text-2xl font-bold text-gray-800">Tipe Kamar</h1>
      <button
        @click="openModal()"
        class="px-5 py-2.5 rounded-sm bg-gradient-to-r from-gray-700 to-gray-400 text-gray-800 font-semibold text-sm hover:bg-gradient-to-r from-gray-700 to-gray-400 border border-gray-600 transition-smooth focus:outline-none focus:ring-2 focus:ring-gray-600/30"
      >
        + Tambah
      </button>
    </div>

    <div class="glass rounded-none overflow-hidden">
      <div class="overflow-x-auto">
        <table class="w-full text-sm">
          <thead>
            <tr class="border-b border-orange-">
              <th class="text-left p-4 text-gray-700 font-medium">Nama</th>
              <th class="text-right p-4 text-gray-700 font-medium">Harga</th>
              <th class="text-center p-4 text-gray-700 font-medium">Stok</th>
              <th class="text-center p-4 text-gray-700 font-medium">Tersisa</th>
              <th class="text-left p-4 text-gray-700 font-medium">Fasilitas</th>
              <th class="text-center p-4 text-gray-700 font-medium">Aksi</th>
            </tr>
          </thead>

          <tbody>
            <tr
              v-for="t in tipeKamars"
              :key="t.id"
              class="border-b border-orange- hover:bg-orange- transition-smooth"
            >
              <td class="p-4 text-gray-800 font-medium">{{ t.nama_tipe_kamar }}</td>
              <td class="p-4 text-right text-gray-800 font-semibold">{{ formatRupiah(t.harga) }}</td>
              <td class="p-4 text-center text-gray-800">{{ t.stok }}</td>
              <td class="p-4 text-center text-gray-800">{{ t.available ?? t.stok }}</td>

              <td class="p-4">
                <div class="flex flex-wrap gap-1">
                  <span
                    v-for="(f, i) in (t.fasilitas_tipe || []).slice(0, 10)"
                    :key="i"
                    class="px-2 py-0.5 rounded bg-orange- text-xs text-gray-600"
                  >
                    {{ f }}
                  </span>
                </div>
              </td>

              <td class="p-4 text-center">
                <div class="flex items-center justify-center gap-2">
                  <button
                    @click="openModal(t)"
                    class="px-3 py-1.5 rounded-sm bg-gradient-to-r from-gray-700 to-gray-400/10 text-gray-800 text-xs font-medium hover:bg-gradient-to-r from-gray-700 to-gray-400/20"
                  >
                    Edit
                  </button>
                  <button
                    @click="hapus(t.id)"
                    class="px-3 py-1.5 rounded-sm bg-gray-600/10 text-red-400 text-xs font-medium hover:bg-gray-600/20"
                  >
                    Hapus
                  </button>
                </div>
              </td>
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
        @click.self="showModal = false"
      >
        <div class="w-full max-w-lg glass rounded-none p-8 animate-scale-in">
          <h2 class="text-xl font-bold text-gray-800 mb-6">
            {{ editItem ? 'Edit' : 'Tambah' }} Tipe Kamar
          </h2>

          <form @submit.prevent="saveItem" class="space-y-4">
            <div>
              <label class="block text-xs font-medium text-gray-600 mb-1">Nama Tipe</label>
              <input
                v-model="form.nama_tipe_kamar"
                required
                class="w-full px-4 py-3 rounded-sm bg-orange- border border-orange- text-gray-800 text-sm focus:ring-2 focus:ring-primary-500/50 focus:outline-none transition-smooth"
              />
            </div>

            <div class="grid grid-cols-2 gap-4">
              <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Harga/Malam</label>
                <input
                  v-model.number="form.harga"
                  type="number"
                  required
                  class="w-full px-4 py-3 rounded-sm bg-orange- border border-orange- text-gray-800 text-sm focus:ring-2 focus:ring-primary-500/50 focus:outline-none transition-smooth"
                />
              </div>

              <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Stok</label>
                <input
                  v-model.number="form.stok"
                  type="number"
                  required
                  class="w-full px-4 py-3 rounded-sm bg-orange- border border-orange- text-gray-800 text-sm focus:ring-2 focus:ring-primary-500/50 focus:outline-none transition-smooth"
                />
              </div>
            </div>

            <div>
              <label class="block text-xs font-medium text-gray-600 mb-1">Deskripsi</label>
              <textarea
                v-model="form.deskripsi"
                rows="3"
                class="w-full px-4 py-3 rounded-sm bg-orange- border border-orange- text-gray-800 text-sm focus:ring-2 focus:ring-primary-500/50 focus:outline-none transition-smooth resize-none"
              />
            </div>

            <div>
              <label class="block text-xs font-medium text-gray-600 mb-1">Foto Kamar (Opsional)</label>
              <input
                type="file"
                @change="handleFile"
                accept="image/*"
                class="w-full px-4 py-2 rounded-sm bg-orange- border border-orange- text-gray-800 text-sm focus:ring-2 focus:ring-primary-500/50 focus:outline-none transition-smooth file:mr-4 file:py-2 file:px-4 file:rounded-sm file:border-0 file:text-xs file:font-semibold file:bg-gradient-to-r from-gray-700 to-gray-400 file:text-gray-800 hover:file:bg-gradient-to-r from-gray-700 to-gray-400"
              />

              <img
                v-if="fotoPreview || editItem?.foto"
                :src="fotoPreview || getImageUrl(editItem?.foto)"
                alt="Preview Foto"
                class="mt-2 h-24 w-32 object-cover rounded-sm border border-orange-"
              />
            </div>

            <div>
              <label class="block text-xs font-medium text-gray-600 mb-1">Fasilitas</label>
              <div class="space-y-2">
                <div class="flex flex-wrap gap-2">
                  <span
                    v-for="(f, i) in fasilitas"
                    :key="i"
                    class="inline-flex items-center gap-1 px-3 py-1 bg-orange- text-xs text-gray-600 rounded border"
                  >
                    {{ f }}
                    <button
                      @click="removeFasilitas(i)"
                      class="text-red-500 hover:text-red-700"
                      type="button"
                    >
                      ×
                    </button>
                  </span>
                </div>

                <div class="flex gap-2">
                  <input
                    v-model="newFasilitas"
                    @keyup.enter="addFasilitas"
                    placeholder="Tambah fasilitas..."
                    class="flex-1 px-3 py-2 rounded-sm bg-orange- border border-orange- text-gray-800 text-sm focus:ring-2 focus:ring-primary-500/50 focus:outline-none"
                  />
                  <button
                    @click="addFasilitas"
                    type="button"
                    class="px-4 py-2 bg-orange- text-gray-600 border border-orange- rounded hover:bg-orange- transition-colors"
                  >
                    Tambah
                  </button>
                </div>
              </div>
            </div>

            <div class="flex gap-3 pt-2">
              <button
                type="button"
                @click="showModal = false"
                class="flex-1 py-3 rounded-sm bg-orange- text-gray-300 font-medium border border-orange- hover:bg-orange- transition-smooth"
              >
                Batal
              </button>
              <button
                type="submit"
                class="flex-1 py-3 rounded-sm bg-gradient-to-r from-gray-700 to-gray-400 text-gray-800 font-semibold transition-smooth"
              >
                Simpan
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
const form = ref({ nama_tipe_kamar: '', harga: 0, stok: 0, deskripsi: '' })
const fasilitas = ref<string[]>([])
const newFasilitas = ref('')
const fotoFile = ref<File | null>(null)
const fotoPreview = ref<string>('')

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

const addFasilitas = () => {
  if (newFasilitas.value.trim() && !fasilitas.value.includes(newFasilitas.value.trim())) {
    fasilitas.value.push(newFasilitas.value.trim())
    newFasilitas.value = ''
  }
}

const removeFasilitas = (index: number) => {
  fasilitas.value.splice(index, 1)
}

const loadData = async () => {
  tipeKamars.value = await apiFetch('/admin/tipe-kamar')
}

const openModal = async (item?: any) => {
  if (item) {
    const freshItem = await apiFetch(`/admin/tipe-kamar/${item.id}`)
    editItem.value = freshItem
    form.value = { nama_tipe_kamar: freshItem.nama_tipe_kamar, harga: freshItem.harga, stok: freshItem.stok, deskripsi: freshItem.deskripsi || '' }
    fasilitas.value = Array.isArray(freshItem.fasilitas_tipe) ? [...freshItem.fasilitas_tipe] : []
  } else {
    editItem.value = null
    form.value = { nama_tipe_kamar: '', harga: 0, stok: 0, deskripsi: '' }
    fasilitas.value = []
  }
  newFasilitas.value = ''
  fotoFile.value = null
  fotoPreview.value = ''
  showModal.value = true
}

const saveItem = async () => {
  if (!form.value.nama_tipe_kamar || form.value.harga < 0 || form.value.stok < 0) {
    alert('Harap isi semua field dengan benar!')
    return
  }

  const formData = new FormData()
  formData.append('nama_tipe_kamar', form.value.nama_tipe_kamar)
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
    showModal.value = false
    await loadData()
  } catch (error) {
    console.error('Failed to save item', error)
    alert('Gagal menyimpan data: ' + (error as any).message)
  }
}

const { confirm } = useConfirmDialog()

const hapus = async (id: number) => {
  if (await confirm({ title: 'Hapus Tipe Kamar', message: 'Yakin ingin menghapus kamar beserta semua sub-kamarnya?', type: 'danger' })) {
    await apiFetch(`/admin/tipe-kamar/${id}`, { method: 'DELETE' })
    loadData()
  }
}

onMounted(loadData)
</script>