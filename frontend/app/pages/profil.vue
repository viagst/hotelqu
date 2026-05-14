<template>
  <div class="min-h-screen bg-slate-50 pt-20 pb-12 px-4">
    <div class="max-w-2xl mx-auto">

      <!-- Header -->
      <div class="mb-8">
        <h1 class="text-2xl font-bold text-gray-800">Profil Saya</h1>
        <p class="text-sm text-gray-500 mt-1">Kelola informasi akun dan keamanan akun Anda</p>
      </div>

      <!-- Alert -->
      <div v-if="success" class="mb-4 px-4 py-3 rounded bg-green-50 border border-green-300 text-green-700 text-sm flex items-center gap-2">
        <span>✓</span> {{ success }}
      </div>
      <div v-if="error" class="mb-4 px-4 py-3 rounded bg-red-50 border border-red-300 text-red-600 text-sm flex items-center gap-2">
        <span>✕</span> {{ error }}
      </div>

      <!-- Data Diri -->
      <div class="bg-white border border-gray-200 rounded p-6 mb-5 shadow-sm">
        <h2 class="text-base font-semibold text-gray-800 mb-4 pb-2 border-b border-gray-100">Data Diri</h2>

        <div class="space-y-4">
          <div>
            <label class="block text-xs font-medium text-gray-600 mb-1.5">Nama Lengkap <span class="text-red-500">*</span></label>
            <input
              v-model="form.nama_user"
              type="text"
              placeholder="Nama lengkap Anda"
              class="w-full px-4 py-3 rounded bg-white border border-gray-300 text-gray-800 text-sm focus:ring-2 focus:ring-gray-400/40 focus:outline-none"
            />
          </div>

          <div>
            <label class="block text-xs font-medium text-gray-600 mb-1.5">Email <span class="text-red-500">*</span></label>
            <input
              v-model="form.email"
              type="email"
              placeholder="email@contoh.com"
              class="w-full px-4 py-3 rounded bg-white border border-gray-300 text-gray-800 text-sm focus:ring-2 focus:ring-gray-400/40 focus:outline-none"
            />
            <p class="text-xs text-gray-400 mt-1">Email digunakan untuk login. Pastikan email belum dipakai akun lain.</p>
          </div>

          <div>
            <label class="block text-xs font-medium text-gray-600 mb-1.5">No. HP / WhatsApp</label>
            <input
              v-model="form.no_hp"
              type="tel"
              placeholder="08xxxxxxxxxxxx"
              class="w-full px-4 py-3 rounded bg-white border border-gray-300 text-gray-800 text-sm focus:ring-2 focus:ring-gray-400/40 focus:outline-none"
            />
          </div>
        </div>

        <button
          @click="updateDataDiri"
          :disabled="loadingDiri"
          class="mt-5 w-full py-3 rounded bg-gradient-to-r from-gray-700 to-gray-500 text-white font-semibold text-sm hover:opacity-90 transition-opacity disabled:opacity-50"
        >
          {{ loadingDiri ? 'Menyimpan...' : 'Simpan Data Diri' }}
        </button>
      </div>

      <!-- Ubah Password -->
      <div class="bg-white border border-gray-200 rounded p-6 shadow-sm">
        <h2 class="text-base font-semibold text-gray-800 mb-1 pb-2 border-b border-gray-100">Ubah Password</h2>
        <p class="text-xs text-gray-400 mb-4">Kosongkan jika tidak ingin mengubah password.</p>

        <div class="space-y-4">
          <div>
            <label class="block text-xs font-medium text-gray-600 mb-1.5">Password Lama</label>
            <input
              v-model="form.current_password"
              :type="showCurrentPwd ? 'text' : 'password'"
              placeholder="Password saat ini"
              class="w-full px-4 py-3 rounded bg-white border border-gray-300 text-gray-800 text-sm focus:ring-2 focus:ring-gray-400/40 focus:outline-none"
            />
          </div>

          <div>
            <label class="block text-xs font-medium text-gray-600 mb-1.5">Password Baru</label>
            <input
              v-model="form.password"
              :type="showNewPwd ? 'text' : 'password'"
              placeholder="Minimal 6 karakter"
              class="w-full px-4 py-3 rounded bg-white border border-gray-300 text-gray-800 text-sm focus:ring-2 focus:ring-gray-400/40 focus:outline-none"
            />
          </div>

          <div>
            <label class="block text-xs font-medium text-gray-600 mb-1.5">Konfirmasi Password Baru</label>
            <input
              v-model="form.password_confirmation"
              :type="showNewPwd ? 'text' : 'password'"
              placeholder="Ulangi password baru"
              class="w-full px-4 py-3 rounded bg-white border border-gray-300 text-gray-800 text-sm focus:ring-2 focus:ring-gray-400/40 focus:outline-none"
            />
            <div v-if="form.password && form.password_confirmation" class="mt-1">
              <span v-if="form.password === form.password_confirmation" class="text-xs text-green-600">✓ Password cocok</span>
              <span v-else class="text-xs text-red-500">✕ Password tidak cocok</span>
            </div>
          </div>

          <label class="flex items-center gap-2 text-xs text-gray-500 cursor-pointer select-none">
            <input type="checkbox" v-model="showNewPwd" class="rounded"> Tampilkan password
          </label>
        </div>

        <button
          @click="updatePassword"
          :disabled="loadingPwd || !canUpdatePassword"
          class="mt-5 w-full py-3 rounded bg-gray-700 text-white font-semibold text-sm hover:bg-gray-600 transition-colors disabled:opacity-40"
        >
          {{ loadingPwd ? 'Menyimpan...' : 'Ubah Password' }}
        </button>
      </div>

      <!-- Info akun -->
      <div class="mt-4 text-center">
        <NuxtLink to="/my-bookings" class="text-sm text-gray-500 hover:text-gray-700 underline">
          ← Kembali ke Pesanan Saya
        </NuxtLink>
      </div>

    </div>
  </div>
</template>

<script setup lang="ts">
definePageMeta({ middleware: 'auth' })

const { apiFetch } = useApi()
const { user, refreshUser } = useAuth()

const success = ref('')
const error = ref('')
const loadingDiri = ref(false)
const loadingPwd = ref(false)
const showCurrentPwd = ref(false)
const showNewPwd = ref(false)

const form = ref({
  nama_user: '',
  email: '',
  no_hp: '',
  current_password: '',
  password: '',
  password_confirmation: '',
})

const canUpdatePassword = computed(() =>
  form.value.current_password &&
  form.value.password &&
  form.value.password_confirmation &&
  form.value.password === form.value.password_confirmation &&
  form.value.password.length >= 6
)

onMounted(async () => {
  try {
    const data = await apiFetch('/profil')
    form.value.nama_user = data.nama_user ?? ''
    form.value.email = data.email ?? ''
    form.value.no_hp = data.no_hp ?? ''
  } catch {
    error.value = 'Gagal memuat data profil.'
  }
})

const updateDataDiri = async () => {
  if (!form.value.nama_user.trim()) { error.value = 'Nama tidak boleh kosong.'; return }
  if (!form.value.email.trim()) { error.value = 'Email tidak boleh kosong.'; return }

  success.value = ''
  error.value = ''
  loadingDiri.value = true
  try {
    await apiFetch('/profil', {
      method: 'POST',
      body: JSON.stringify({
        nama_user: form.value.nama_user.trim(),
        email: form.value.email.trim(),
        no_hp: form.value.no_hp.trim(),
      }),
    })
    success.value = 'Data diri berhasil disimpan.'
    if (refreshUser) await refreshUser()
  } catch (e: any) {
    const msg = e?.data?.message || e?.data?.errors?.email?.[0] || 'Gagal menyimpan. Email mungkin sudah digunakan akun lain.'
    error.value = msg
  } finally {
    loadingDiri.value = false
  }
}

const updatePassword = async () => {
  success.value = ''
  error.value = ''
  loadingPwd.value = true
  try {
    await apiFetch('/profil', {
      method: 'POST',
      body: JSON.stringify({
        nama_user: form.value.nama_user,
        email: form.value.email,
        no_hp: form.value.no_hp,
        current_password: form.value.current_password,
        password: form.value.password,
        password_confirmation: form.value.password_confirmation,
      }),
    })
    success.value = 'Password berhasil diubah.'
    form.value.current_password = ''
    form.value.password = ''
    form.value.password_confirmation = ''
  } catch (e: any) {
    error.value = e?.data?.message || 'Gagal ubah password. Pastikan password lama benar.'
  } finally {
    loadingPwd.value = false
  }
}
</script>
