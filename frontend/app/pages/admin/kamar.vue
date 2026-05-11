<template>
 <div>
<div class="flex items-center justify-between mb-6">
<h1 class="text-2xl font-bold text-gray-800">Kamar</h1>
<button @click="openModal()" class="px-5 py-2.5 rounded-sm bg-gradient-to-r from-gray-700 to-gray-400 text-gray-800 font-semibold text-sm transition-smooth">+ Tambah</button>
</div>
<div class="glass rounded-none overflow-hidden">
<div class="overflow-x-auto">
<table class="w-full text-sm">
<thead><tr class="border-b border-orange-">
<th class="text-left p-4 text-gray-700 font-medium">No. Kamar</th>
<th class="text-left p-4 text-gray-700 font-medium">Tipe</th>
<th class="text-center p-4 text-gray-700 font-medium">Status</th>
<th class="text-center p-4 text-gray-700 font-medium">Aksi</th>
</tr></thead>
<tbody>
<tr v-for="k in kamars" :key="k.id" class="border-b border-orange- hover:bg-orange- transition-smooth">
<td class="p-4 text-gray-800 font-mono font-bold">#{{ k.nomor_kamar }}</td>
<td class="p-4 text-gray-300">{{ k.tipe_kamar?.nama_tipe_kamar }}</td>
<td class="p-4 text-center">
<span class="px-3 py-1 rounded text-xs font-bold uppercase tracking-wider" :class="{ 'bg-green-100 text-green-700': k.status_kamar === 'tersedia', 'bg-yellow-100 text-yellow-700': k.status_kamar === 'kotor', 'bg-red-100 text-gray-800': k.status_kamar === 'perbaikan', 'bg-orange- text-gray-800': k.status_kamar === 'bersih', 'bg-gray-200 text-gray-700': k.status_kamar === 'kosong' }">{{ k.status_kamar }}</span>
</td>
<td class="p-4 text-center">
<button @click="openModal(k)" class="px-3 py-1.5 rounded-sm bg-gradient-to-r from-gray-700 to-gray-400/10 text-gray-800 text-xs mr-1">Edit</button>
<button @click="hapus(k.id)" class="px-3 py-1.5 rounded-sm bg-gray-600/10 text-red-400 text-xs">Hapus</button>
</td>
</tr>
</tbody>
</table>
</div>
</div>
<Teleport to="body">
<div v-if="showModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm" @click.self="showModal = false">
<div class="w-full max-w-md glass rounded-none p-8 animate-scale-in">
<h2 class="text-xl font-bold text-gray-800 mb-6">{{ editItem ? 'Edit' : 'Tambah' }} Kamar</h2>
<form @submit.prevent="saveItem" class="space-y-4">
<div><label class="block text-xs font-medium text-gray-600 mb-1">Tipe Kamar</label>
<select v-model.number="form.id_tipe_kamar" required class="w-full px-4 py-3 rounded-sm bg-orange- border border-orange- text-gray-800 text-sm focus:ring-2 focus:ring-primary-500/50 focus:outline-none">
<option v-for="t in tipeKamars" :key="t.id" :value="t.id" class="bg-gray-100">{{ t.nama_tipe_kamar }}</option>
</select></div>
<div><label class="block text-xs font-medium text-gray-600 mb-1">Nomor Kamar</label>
<input v-model="form.nomor_kamar" required class="w-full px-4 py-3 rounded-sm bg-orange- border border-orange- text-gray-800 text-sm focus:ring-2 focus:ring-primary-500/50 focus:outline-none"></div>
<div><label class="block text-xs font-medium text-gray-600 mb-1">Status</label>
<select v-model="form.status_kamar" class="w-full px-4 py-3 rounded bg-orange- border border-orange- text-gray-800 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
<option value="tersedia" class="bg-white">Tersedia</option>
<option value="kotor" class="bg-white">Kotor</option>
<option value="perbaikan" class="bg-white">Perbaikan</option>
<option value="bersih" class="bg-white">Bersih</option>
<option value="kosong" class="bg-white">Kosong (Belum ada furniture)</option>
</select></div>
<div class="flex gap-3 pt-2">
<button type="button" @click="showModal = false" class="flex-1 py-3 rounded-sm bg-orange- text-gray-300 font-medium border border-orange- transition-smooth">Batal</button>
<button type="submit" class="flex-1 py-3 rounded-sm bg-gradient-to-r from-gray-700 to-gray-400 text-gray-800 font-semibold transition-smooth">Simpan</button>
</div>
</form>
</div>
</div>
</Teleport>
</div> 
</template>
 
<script setup lang="ts">
definePageMeta({ layout: 'panel', middleware: 'auth' })

const { apiFetch } = useApi()
const kamars = ref<any[]>([])
const tipeKamars = ref<any[]>([])
const showModal = ref(false)
const editItem = ref<any>(null)
const form = ref({ id_tipe_kamar: 0, nomor_kamar: '', status_kamar: 'tersedia' })

const loadData = async () => {
  kamars.value = await apiFetch('/admin/kamar')
  tipeKamars.value = await apiFetch('/tipe-kamar')
}

const openModal = (item?: any) => {
  editItem.value = item || null
  form.value = item
    ? { id_tipe_kamar: item.id_tipe_kamar, nomor_kamar: item.nomor_kamar, status_kamar: item.status_kamar }
    : { id_tipe_kamar: tipeKamars.value[0]?.id || 0, nomor_kamar: '', status_kamar: 'tersedia' }
  showModal.value = true
}

const saveItem = async () => {
  if (editItem.value) await apiFetch(`/admin/kamar/${editItem.value.id}`, { method: 'PUT', body: JSON.stringify(form.value) })
  else await apiFetch('/admin/kamar', { method: 'POST', body: JSON.stringify(form.value) })
  showModal.value = false
  loadData()
}

const { confirm } = useConfirmDialog()

const hapus = async (id: number) => {
  if (await confirm({ title: 'Hapus Kamar', message: 'Yakin ingin menghapus kamar ini secara permanen?', type: 'danger' })) {
    await apiFetch(`/admin/kamar/${id}`, { method: 'DELETE' })
    loadData()
  }
}

onMounted(loadData)
</script>
