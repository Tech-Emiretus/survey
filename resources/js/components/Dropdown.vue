<template>
  <div class="relative" @keydown.esc="open = false">
    <div @click="toggle" class="cursor-pointer">
      <slot name="trigger" />
    </div>
    <transition name="fade">
      <div v-if="open" class="absolute right-0 mt-2 w-48 rounded-md shadow-lg z-50 bg-white dark:bg-gray-800 ring-1 ring-black ring-opacity-5 focus:outline-none">
        <slot name="content" />
      </div>
    </transition>
  </div>
</template>

<script setup>
import { ref } from 'vue'
const open = ref(false)
function toggle() { open.value = !open.value }
</script>

<style scoped>
.fade-enter-active, .fade-leave-active { transition: opacity 0.15s; }
.fade-enter-from, .fade-leave-to { opacity: 0; }
</style>
