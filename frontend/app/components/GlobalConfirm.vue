<template>
  <Teleport to="body">
    <div v-if="isShowing" class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-black/70 backdrop-blur-sm" @click.self="cancel">
      <div class="w-full max-w-sm bg-white border border-orange- p-6 animate-scale-in">
        <h3 class="text-lg font-bold mb-2" :class="options.type === 'danger' ? 'text-red-400' : 'text-gray-800'">{{ options.title }}</h3>
        <p class="text-gray-700 text-sm mb-6">{{ options.message }}</p>
        <div class="flex gap-3">
          <button @click="cancel" class="flex-1 py-2.5 bg-orange- text-gray-600 text-sm font-medium hover:bg-orange- border border-orange- transition-smooth">{{ options.cancelText }}</button>
          <button @click="accept" class="flex-1 py-2.5 text-sm font-bold transition-smooth" :class="options.type === 'danger' ? 'bg-gray-600 text-gray-800 hover:bg-gray-600' : 'bg-gradient-to-r from-gray-700 to-gray-400 text-dark-950 hover:bg-gradient-to-r from-gray-700 to-gray-400'">{{ options.confirmText }}</button>
        </div>
      </div>
    </div>
  </Teleport>
</template>

<script setup lang="ts">
const { isShowing, options, accept, cancel } = useConfirmDialog()
</script>
