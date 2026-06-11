<template>
  <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6 transition-colors">
    <div :class="['w-10 h-10 rounded-lg mb-3 flex items-center justify-center text-white font-bold text-lg', color]">
      {{ String(value) }}
    </div>
    <p class="text-2xl font-bold text-gray-800 dark:text-gray-100">{{ value }}</p>
    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">{{ label }}</p>
  </div>
</template>

<script setup>
defineProps({ label: String, value: Number, color: String });
</script>
