<template>
 <div>
<div class="flex items-center justify-between mb-6">
<h1 class="text-2xl font-bold text-gray-800">Users</h1>
<button @click="openModal()" class="px-5 py-2.5 rounded-sm bg-gradient-to-r from-gray-700 to-gray-400 text-gray-800 font-semibold text-sm transition-smooth">+ Tambah</button>
</div>
<div class="glass rounded-none overflow-hidden">
<div class="overflow-x-auto">
<table class="w-full text-sm">
<thead><tr class="border-b border-orange-">
<th class="text-left p-4 text-gray-700 font-medium">Nama</th>
<th class="text-left p-4 text-gray-700 font-medium">Email</th>
<th class="text-center p-4 text-gray-700 font-medium">Role</th>
<th class="text-center p-4 text-gray-700 font-medium">Aksi</th>
</tr></thead>
<tbody>
<tr v-for="u in users" :key="u.id" class="border-b border-orange- hover:bg-orange- transition-smooth">
<td class="p-4 text-gray-800 font-medium">{{ u.nama_user }}</td>
<td class="p-4 text-gray-600">{{ u.email }}</td>
<td class="p-4 text-center">
<span class="px-2.5 py-1 rounded-none text-xs font-bold uppercase" :class="{ 'bg-gradient-to-r from-gray-700 to-gray-400/20 text-gray-800': u.role === 'admin', 'bg-gradient-to-r from-gray-700 to-gray-400/20 text-gray-800': u.role === 'resepsionis', 'bg-green-500/20 text-green-400': u.role === 'tamu', }">{{ u.role }}</span>
</td>
<td class="p-4 text-center">
<button @click="openModal(u)" class="px-3 py-1.5 rounded-sm bg-gradient-to-r from-gray-700 to-gray-400/10 text-gray-800 text-xs mr-1">Edit</button>
<button @click="hapus(u.id)" class="px-3 py-1.5 rounded-sm bg-gray-600/10 text-red-400 text-xs">Hapus</button>
</td>
</tr>
</tbody>
</table>
</div>
</div>
<Teleport to="body">
<div v-if="showModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm" @click.self="showModal = false">
<div class="w-full max-w-md glass rounded-none p-8 animate-scale-in">
<h2 class="text-xl font-bold text-gray-800 mb-6">{{ editItem ? 'Edit' : 'Tambah' }} User</h2>
<form @submit.prevent="saveItem" class="space-y-4">
<div><label class="block text-xs font-medium text-gray-600 mb-1">Nama</label>
<input v-model="form.nama_user" required class="w-full px-4 py-3 rounded-sm bg-orange- border border-orange- text-gray-800 text-sm focus:ring-2 focus:ring-primary-500/50 focus:outline-none"></div>
<div><label class="block text-xs font-medium text-gray-600 mb-1">Email</label>
<input v-model="form.email" type="email" required class="w-full px-4 py-3 rounded-sm bg-orange- border border-orange- text-gray-800 text-sm focus:ring-2 focus:ring-primary-500/50 focus:outline-none"></div>
<div><label class="block text-xs font-medium text-gray-600 mb-1">No HP</label>
<input v-model="form.no_hp" class="w-full px-4 py-3 rounded-sm bg-orange- border border-orange- text-gray-800 text-sm focus:ring-2 focus:ring-primary-500/50 focus:outline-none"></div>
<div><label class="block text-xs font-medium text-gray-600 mb-1">Role</label>
<select v-model="form.role" class="w-full px-4 py-3 rounded-sm bg-orange- border border-orange- text-gray-800 text-sm focus:ring-2 focus:ring-primary-500/50 focus:outline-none">
<option value="admin" class="bg-gray-100">Admin</option>
<option value="resepsionis" class="bg-gray-100">Resepsionis</option>
<option value="tamu" class="bg-gray-100">Tamu</option>
</select></div>
<div><label class="block text-xs font-medium text-gray-600 mb-1">Password {{ editItem ? '(kosongkan jika tidak diubah)' : '' }}</label>
<input v-model="form.password" type="password" :required="!editItem" class="w-full px-4 py-3 rounded-sm bg-orange- border border-orange- text-gray-800 text-sm focus:ring-2 focus:ring-primary-500/50 focus:outline-none"></div>
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
const users = ref<any[]>([])
const showModal = ref(false)
const editItem = ref<any>(null)
const form = ref({ nama_user: '', email: '', no_hp: '', role: 'tamu', password: '' })

const loadData = async () => {
  users.value = await apiFetch('/admin/users')
}

const openModal = (item?: any) => {
  editItem.value = item || null
  form.value = item
    ? { nama_user: item.nama_user, email: item.email, no_hp: item.no_hp || '', role: item.role, password: '' }
    : { nama_user: '', email: '', no_hp: '', role: 'tamu', password: '' }
  showModal.value = true
}

const saveItem = async () => {
  const data: any = { ...form.value }
  if (!data.password) delete data.password
  if (editItem.value) await apiFetch(`/admin/users/${editItem.value.id}`, { method: 'PUT', body: JSON.stringify(data) })
  else await apiFetch('/admin/users', { method: 'POST', body: JSON.stringify(data) })
  showModal.value = false
  loadData()
}

const { confirm } = useConfirmDialog()

const hapus = async (id: number) => {
  if (await confirm({ title: 'Hapus User', message: 'Yakin ingin menghapus user ini?', type: 'danger' })) {
    await apiFetch(`/admin/users/${id}`, { method: 'DELETE' })
    loadData()
  }
}

onMounted(loadData)
</script>
