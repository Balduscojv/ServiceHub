<template>
  <AppLayout>
    <template #header>
      <div class="flex justify-between items-center">
        <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-100">Empresas</h2>
        <Link :href="route('companies.create')" class="bg-indigo-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-indigo-700 transition-colors">
          Nova Empresa
        </Link>
      </div>
    </template>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm overflow-hidden transition-colors">
      <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
        <thead class="bg-gray-50 dark:bg-gray-700/50">
          <tr>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Empresa</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">CNPJ</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Projetos</th>
            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Ações</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
          <tr v-if="companies.data.length === 0">
            <td colspan="4" class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">Nenhuma empresa cadastrada.</td>
          </tr>
          <tr v-for="company in companies.data" :key="company.id" class="hover:bg-gray-50 dark:hover:bg-gray-700/40 transition-colors">
            <td class="px-6 py-4 font-medium text-gray-900 dark:text-gray-100">{{ company.name }}</td>
            <td class="px-6 py-4 text-gray-500 dark:text-gray-400">{{ company.cnpj || '—' }}</td>
            <td class="px-6 py-4 text-gray-500 dark:text-gray-400">{{ company.projects_count }}</td>
            <td class="px-6 py-4 text-right space-x-3">
              <Link :href="route('companies.show', company.id)" class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 dark:hover:text-indigo-200 text-sm">Ver</Link>
              <Link :href="route('companies.edit', company.id)" class="text-yellow-600 dark:text-yellow-400 hover:text-yellow-800 dark:hover:text-yellow-200 text-sm">Editar</Link>
              <button @click="destroy(company)" class="text-red-600 dark:text-red-400 hover:text-red-800 dark:hover:text-red-200 text-sm">Excluir</button>
            </td>
          </tr>
        </tbody>
      </table>

      <div v-if="companies.last_page > 1" class="px-6 py-4 border-t border-gray-200 dark:border-gray-700 flex justify-between items-center text-sm text-gray-600 dark:text-gray-400">
        <span>Página {{ companies.current_page }} de {{ companies.last_page }}</span>
        <div class="flex gap-2">
          <Link v-if="companies.prev_page_url" :href="companies.prev_page_url" class="px-3 py-1 border dark:border-gray-600 rounded hover:bg-gray-50 dark:hover:bg-gray-700">Anterior</Link>
          <Link v-if="companies.next_page_url" :href="companies.next_page_url" class="px-3 py-1 border dark:border-gray-600 rounded hover:bg-gray-50 dark:hover:bg-gray-700">Próxima</Link>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { route } from 'ziggy';
import { Link, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

defineProps({ companies: Object });

function destroy(company) {
  if (confirm(`Excluir a empresa "${company.name}"? Isso removerá todos os projetos e tickets vinculados.`)) {
    router.delete(route('companies.destroy', company.id));
  }
}
</script>
