<template>
  <div class="w-full logo-wrap h-[60px] m-b-1">
    <transition name="sidebarLogoFade">
      <router-link v-if="collapse" key="collapse" class="h-full w-full flex items-center justify-center p-2" to="/">
        <img v-if="settingsStore.sidebarLogo" :src="logo" class="max-h-12 max-w-full" />
      </router-link>

      <router-link v-else key="expand" class="h-full w-full flex items-center p-3" to="/">
        <img v-if="settingsStore.sidebarLogo" :src="logo" class="max-h-12 max-w-full" />
      </router-link>
    </transition>
  </div>
</template>

<script setup>
import Logo from '@/assets/images/logo.png'
import { useSettingsStore } from '@/store/modules/settings'

const settingsStore = useSettingsStore()
console.log(settingsStore)
defineProps({
  collapse: {
    type: Boolean,
    required: true
  }
})

const logo = computed(() => {
  return settingsStore.logoUrl ? settingsStore.logoUrl : Logo
})
</script>

<style lang="scss" scoped>
.sidebarLogoFade-enter-active {
  transition: opacity 2s;
}

.sidebarLogoFade-leave-active,
.sidebarLogoFade-enter-from,
.sidebarLogoFade-leave-to {
  opacity: 0;
}
</style>
