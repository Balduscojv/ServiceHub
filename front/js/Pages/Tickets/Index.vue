<template>
  <AppLayout>
    <template #header>
      <div class="flex justify-between items-center">
        <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-100">Tickets</h2>
        <Link :href="route('tickets.create')" class="bg-indigo-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-indigo-700 transition-colors">
          Novo Ticket
        </Link>
      </div>
    </template>

    <!-- Filters -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-4 mb-4 flex gap-3 transition-colors">
      <input
        v-model="filterForm.search"
        type="text"
        placeholder="Buscar por título..."
        class="flex-1 border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500"
        @input="applyFilters"
      />
      <select
        v-model="filterForm.status"
        class="border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100"
        @change="applyFilters"
      >
        <option value="">Todos os status</option>
        <option value="open">Aberto</option>
        <option value="in_progress">Em andamento</option>
        <option value="closed">Fechado</option>
      </select>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm overflow-hidden transition-colors">
      <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
        <thead class="bg-gray-50 dark:bg-gray-700/50">
          <tr>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">#</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Título</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase hidden md:table-cell">Projeto</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase hidden lg:table-cell">Responsável</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Status</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase hidden sm:table-cell">Prioridade</th>
            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Ações</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
          <tr v-if="tickets.data.length === 0">
            <td colspan="7" class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">Nenhum ticket encontrado.</td>
          </tr>
          <tr v-for="ticket in tickets.data" :key="ticket.id" class="hover:bg-gray-50 dark:hover:bg-gray-700/40 transition-colors">
            <td class="px-6 py-4 text-gray-500 dark:text-gray-400 text-sm">#{{ ticket.id }}</td>
            <td class="px-6 py-4 font-medium text-gray-900 dark:text-gray-100">{{ ticket.title }}</td>
            <td class="px-6 py-4 text-gray-500 dark:text-gray-400 text-sm hidden md:table-cell">{{ ticket.project?.name }}</td>
            <td class="px-6 py-4 text-gray-500 dark:text-gray-400 text-sm hidden lg:table-cell">{{ ticket.user?.name }}</td>
            <td class="px-6 py-4"><StatusBadge :status="ticket.status" /></td>
            <td class="px-6 py-4 hidden sm:table-cell"><PriorityBadge :priority="ticket.priority" /></td>
            <td class="px-6 py-4 text-right space-x-3">
              <Link :href="route('tickets.show', ticket.id)" class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 text-sm">Ver</Link>
              <Link :href="route('tickets.edit', ticket.id)" class="text-yellow-600 dark:text-yellow-400 hover:text-yellow-800 text-sm">Editar</Link>
              <button @click="destroy(ticket)" class="text-red-600 dark:text-red-400 hover:text-red-800 text-sm">Excluir</button>
            </td>
          </tr>
        </tbody>
      </table>

      <div v-if="tickets.last_page > 1" class="px-6 py-4 border-t border-gray-200 dark:border-gray-700 flex justify-between items-center text-sm text-gray-600 dark:text-gray-400">
        <span>Página {{ tickets.current_page }} de {{ tickets.last_page }}</span>
        <div class="flex gap-2">
          <Link v-if="tickets.prev_page_url" :href="tickets.prev_page_url" class="px-3 py-1 border dark:border-gray-600 rounded hover:bg-gray-50 dark:hover:bg-gray-700">Anterior</Link>
          <Link v-if="tickets.next_page_url" :href="tickets.next_page_url" class="px-3 py-1 border dark:border-gray-600 rounded hover:bg-gray-50 dark:hover:bg-gray-700">Próxima</Link>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { route } from 'ziggy';
import { reactive } from 'vue';
import { Link, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import StatusBadge from '@/Components/StatusBadge.vue';
import PriorityBadge from '@/Components/PriorityBadge.vue';

const props = defineProps({ tickets: Object, filters: Object });

const filterForm = reactive({
  search: props.filters?.search ?? '',
  status: props.filters?.status ?? '',
});

function applyFilters() {
  router.get(route('tickets.index'), filterForm, { preserveState: true, replace: true });
}

function destroy(ticket) {
  if (confirm(`Excluir o ticket "${ticket.title}"?`)) {
    router.delete(route('tickets.destroy', ticket.id));
  }
}
</script>
