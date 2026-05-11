import { ref } from 'vue'

const isShowing = ref(false)
const options = ref({
    title: 'Konfirmasi',
    message: 'Apakah Anda yakin melakukan aksi ini?',
    confirmText: 'Ya',
    cancelText: 'Batal',
    type: 'warning'
})
let resolvePromise: any = null

export const useConfirmDialog = () => {
    const confirm = (opts: Partial<typeof options.value> = {}) => {
        options.value = { 
            title: 'Konfirmasi',
            message: 'Apakah Anda yakin melakukan aksi ini?',
            confirmText: 'Ya',
            cancelText: 'Batal',
            type: 'warning',
            ...opts 
        }
        isShowing.value = true
        return new Promise<boolean>((resolve) => {
            resolvePromise = resolve
        })
    }

    const accept = () => {
        isShowing.value = false
        if (resolvePromise) resolvePromise(true)
    }

    const cancel = () => {
        isShowing.value = false
        if (resolvePromise) resolvePromise(false)
    }

    return { isShowing, options, confirm, accept, cancel }
}
