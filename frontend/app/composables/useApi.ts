export const useApi = () => {
  const config = useRuntimeConfig()
  const apiBase = config.public.apiBase as string
  const storageUrl = config.public.storageUrl as string

  const getToken = () => {
    if (import.meta.client) {
      return localStorage.getItem('auth_token')
    }
    return null
  }

  const apiFetch = async <T = any>(url: string, options: any = {}): Promise<T> => {
    const token = getToken()
    const headers: any = {
      'Accept': 'application/json',
      ...options.headers,
    }

    if (token) {
      headers['Authorization'] = `Bearer ${token}`
    }

    if (!(options.body instanceof FormData)) {
      headers['Content-Type'] = 'application/json'
    }

    const response = await $fetch<T>(`${apiBase}${url}`, {
      ...options,
      headers,
    })

    return response
  }

  const getImageUrl = (path: string | null) => {
    if (!path) return '/placeholder-room.jpg'
    if (path.startsWith('http')) return path
    return `${storageUrl}/${path}`
  }

  const formatRupiah = (amount: number) => {
    return new Intl.NumberFormat('id-ID', {
      style: 'currency',
      currency: 'IDR',
      minimumFractionDigits: 0,
    }).format(amount)
  }

  const formatDate = (date: string) => {
    return new Date(date).toLocaleDateString('id-ID', {
      weekday: 'long',
      year: 'numeric',
      month: 'long',
      day: 'numeric',
    })
  }

  const formatDateShort = (date: string) => {
    return new Date(date).toLocaleDateString('id-ID', {
      day: '2-digit',
      month: 'short',
      year: 'numeric',
    })
  }

  return { apiFetch, getImageUrl, formatRupiah, formatDate, formatDateShort, apiBase, storageUrl }
}
