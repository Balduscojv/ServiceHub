<template>
  <AppLayout>
    <template #header>
      <div class="flex items-center justify-between">
        <div class="flex items-center gap-3">
          <Link :href="route('projects.index')" class="text-gray-500 hover:text-gray-700">← Projetos</Link>
          <h2 class="text-xl font-semibold text-gray-800">{{ project.name }}</h2>
        </div>
        <div class="flex gap-2">
          <Link :href="route('tickets.create')" class="bg-indigo-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-indigo-700">Novo Ticket</Link>
          <Link :href="route('projects.edit', project.id)" class="bg-yellow-500 text-white px-4 py-2 rounded-lg text-sm hover:bg-yellow-600">Editar</Link>
        </div>
      </div>
    </template>

    <div class="space-y-6">
      <div class="bg-white rounded-xl shadow-sm p-6">
        <dl class="grid grid-cols-3 gap-4">
          <div>
            <dt class="text-sm text-gray-500">Empresa</dt>
            <dd class="font-medium">{{ project.company?.name }}</dd>
          </div>
          <div>
            <dt class="text-sm text-gray-500">Status</dt>
            <dd><StatusBadge :status="project.status" /></dd>
          </div>
          <div v-if="project.description">
            <dt class="text-sm text-gray-500">Descrição</dt>
            <dd class="text-gray-700">{{ project.description }}</dd>
          </div>
        </dl>
      </div>

      <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b">
          <h3 class="text-lg font-medium text-gray-800">Tickets ({{ project.tickets?.length }})</h3>
        </div>
        <div class="divide-y divide-gray-100">
          <div v-if="!project.tickets?.length" class="px-6 py-8 text-center text-gray-500">Nenhum ticket.</div>
          <Link
            v-for="ticket in project.tickets"
            :key="ticket.id"
            :href="route('tickets.show', ticket.id)"
            class="flex items-center justify-between px-6 py-4 hover:bg-gray-50"
          >
            <div>
              <p class="font-medium text-gray-800">{{ ticket.title }}</p>
              <p class="text-sm text-gray-500">{{ ticket.user?.name }}</p>
            </div>
            <div class="flex gap-2">
              <StatusBadge :status="ticket.status" />
              <PriorityBadge :priority="ticket.priority" />
            </div>
          </Link>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { route } from 'ziggy';
import { Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import StatusBadge from '@/Components/StatusBadge.vue';
import PriorityBadge from '@/Components/PriorityBadge.vue';

defineProps({ project: Object });
</script>
