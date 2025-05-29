<template>
  <router-link
    :to="to"
    class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium transition-colors duration-150"
    :class="active
      ? 'border-indigo-400 text-gray-900 dark:text-gray-100 focus:border-indigo-700'
      : 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300 focus:border-gray-300'"
  >
    <slot />
  </router-link>
</template>

<script setup>
defineProps({ to: String, active: Boolean })
</script>
