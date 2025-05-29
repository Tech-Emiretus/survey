<template>
  <component
    :is="as || 'router-link'"
    :to="to"
    v-bind="$attrs"
    class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-900 w-full text-left"
  >
    <slot />
  </component>
</template>

<script setup>
defineProps({ to: String, as: String, type: String })
</script>
