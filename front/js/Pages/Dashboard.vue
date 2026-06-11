<template>
  <AppLayout>
    <template #header>
      <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-100">Dashboard</h2>
    </template>

    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
      <StatCard label="Empresas" :value="stats.companies" color="bg-blue-500" />
      <StatCard label="Projetos" :value="stats.projects" color="bg-green-500" />
      <StatCard label="Tickets Total" :value="stats.tickets" color="bg-yellow-500" />
      <StatCard label="Meus Tickets" :value="stats.my_tickets" color="bg-indigo-500" />
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm transition-colors">
      <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center">
        <h3 class="text-lg font-medium text-gray-800 dark:text-gray-100">Meus Tickets Recentes</h3>
        <Link :href="route('tickets.create')" class="bg-indigo-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-indigo-700 transition-colors">
          Novo Ticket
        </Link>
      </div>
      <div class="divide-y divide-gray-100 dark:divide-gray-700">
        <div v-if="recentTickets.length === 0" class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">
          Nenhum ticket encontrado.
        </div>
        <Link
          v-for="ticket in recentTickets"
          :key="ticket.id"
          :href="route('tickets.show', ticket.id)"
          class="flex items-center justify-between px-6 py-4 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors"
        >
          <div>
            <p class="font-medium text-gray-800 dark:text-gray-100">{{ ticket.title }}</p>
            <p class="text-sm text-gray-500 dark:text-gray-400">{{ ticket.project?.company?.name }} / {{ ticket.project?.name }}</p>
          </div>
          <div class="flex gap-2">
            <StatusBadge :status="ticket.status" />
            <PriorityBadge :priority="ticket.priority" />
          </div>
        </Link>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { route } from 'ziggy';
import { Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import StatCard from '@/Components/StatCard.vue';
import StatusBadge from '@/Components/StatusBadge.vue';
import PriorityBadge from '@/Components/PriorityBadge.vue';

defineProps({ stats: Object, recentTickets: Array });
</script>
