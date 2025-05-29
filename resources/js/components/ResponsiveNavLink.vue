<template>
  <component
    :is="as || 'router-link'"
    :to="to"
    v-bind="$attrs"
    class="block pl-3 pr-4 py-2 border-l-4 text-base font-medium transition-colors duration-150"
    :class="active
      ? 'border-indigo-400 text-indigo-700 dark:text-indigo-300 bg-indigo-50 dark:bg-gray-900 focus:border-indigo-700'
      : 'border-transparent text-gray-600 dark:text-gray-300 hover:text-gray-800 dark:hover:text-gray-100 hover:bg-gray-50 dark:hover:bg-gray-900 hover:border-gray-300 focus:border-gray-300'"
  >
    <slot />
  </component>
</template>

<script setup>
defineProps({ to: String, as: String, type: String })
</script>
