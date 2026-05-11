<template>
  <div>
    <NuxtLayout>
      <NuxtPage />
    </NuxtLayout>
    <GlobalConfirm />
  </div>
</template>

<script setup lang="ts">
const { initAuth } = useAuth()
onMounted(() => {
  initAuth()
})
</script>
