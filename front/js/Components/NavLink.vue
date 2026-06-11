<template>
  <Link
    :href="href"
    :class="[
      'px-3 py-2 rounded-md text-sm font-medium transition-colors',
      active
        ? 'bg-indigo-800 dark:bg-indigo-700 text-white'
        : 'text-indigo-200 hover:bg-indigo-600 hover:text-white',
    ]"
  >
    <slot />
  </Link>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';
defineProps({ href: String, active: Boolean });
</script>
