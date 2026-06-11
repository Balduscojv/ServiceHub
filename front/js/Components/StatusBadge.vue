<template>
  <span :class="['px-2 py-1 rounded-full text-xs font-medium', classes[status]]">
    {{ labels[status] }}
  </span>
</template>

<script setup>
defineProps({ status: String });

const labels = {
  open: 'Aberto',
  in_progress: 'Em andamento',
  closed: 'Fechado',
  active: 'Ativo',
  inactive: 'Inativo',
  completed: 'Concluído',
};

const classes = {
  open: 'bg-blue-100 text-blue-800',
  in_progress: 'bg-yellow-100 text-yellow-800',
  closed: 'bg-gray-100 text-gray-700',
  active: 'bg-green-100 text-green-800',
  inactive: 'bg-red-100 text-red-800',
  completed: 'bg-purple-100 text-purple-800',
};
</script>
