<template>
  <span :class="['px-2 py-1 rounded-full text-xs font-medium', classes[priority]]">
    {{ labels[priority] }}
  </span>
</template>

<script setup>
defineProps({ priority: String });

const labels = { low: 'Baixa', medium: 'Média', high: 'Alta' };
const classes = {
  low: 'bg-green-100 text-green-800',
  medium: 'bg-yellow-100 text-yellow-800',
  high: 'bg-red-100 text-red-800',
};
</script>
