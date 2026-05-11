interface User {
  id: number
  nama_user: string
  foto: string | null
  email: string
  no_hp: string | null
  role: 'admin' | 'resepsionis' | 'tamu'
}

export const useAuth = () => {
  const user = useState<User | null>('auth_user', () => null)
  const token = useState<string | null>('auth_token', () => null)
  const isLoggedIn = computed(() => !!user.value)
  const { apiFetch } = useApi()

  const initAuth = async () => {
    if (import.meta.client) {
      const savedToken = localStorage.getItem('auth_token')
      const savedUser = localStorage.getItem('auth_user')
      
      if (savedToken && savedUser) {
        token.value = savedToken
        user.value = JSON.parse(savedUser)
        
        try {
          const data = await apiFetch<User>('/me')
          user.value = data
          localStorage.setItem('auth_user', JSON.stringify(data))
        } catch {
          logout()
        }
      }
    }
  }

  const login = async (email: string, password: string) => {
    const data = await apiFetch<{ user: User; token: string }>('/login', {
      method: 'POST',
      body: JSON.stringify({ email, password }),
    })

    user.value = data.user
    token.value = data.token

    if (import.meta.client) {
      localStorage.setItem('auth_token', data.token)
      localStorage.setItem('auth_user', JSON.stringify(data.user))
    }

    return data
  }

  const register = async (payload: { nama_user: string; email: string; no_hp?: string; password: string; password_confirmation: string }) => {
    const data = await apiFetch<{ user: User; token: string }>('/register', {
      method: 'POST',
      body: JSON.stringify(payload),
    })

    user.value = data.user
    token.value = data.token

    if (import.meta.client) {
      localStorage.setItem('auth_token', data.token)
      localStorage.setItem('auth_user', JSON.stringify(data.user))
    }

    return data
  }

  const logout = async () => {
    try {
      await apiFetch('/logout', { method: 'POST' })
    } catch {}

    user.value = null
    token.value = null

    if (import.meta.client) {
      localStorage.removeItem('auth_token')
      localStorage.removeItem('auth_user')
    }

    navigateTo('/login')
  }

  const isAdmin = computed(() => user.value?.role === 'admin')
  const isResepsionis = computed(() => user.value?.role === 'resepsionis')
  const isTamu = computed(() => user.value?.role === 'tamu')

  return { user, token, isLoggedIn, isAdmin, isResepsionis, isTamu, initAuth, login, register, logout }
}
