<template>
  <AppLayout>
    <template #header>
      <div class="flex justify-between items-center">
        <h2 class="text-xl font-semibold text-gray-800">Projetos</h2>
        <Link :href="route('projects.create')" class="bg-indigo-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-indigo-700">
          Novo Projeto
        </Link>
      </div>
    </template>

    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Projeto</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Empresa</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tickets</th>
            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Ações</th>
          </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
          <tr v-if="projects.data.length === 0">
            <td colspan="5" class="px-6 py-8 text-center text-gray-500">Nenhum projeto cadastrado.</td>
          </tr>
          <tr v-for="project in projects.data" :key="project.id" class="hover:bg-gray-50">
            <td class="px-6 py-4 font-medium text-gray-900">{{ project.name }}</td>
            <td class="px-6 py-4 text-gray-500">{{ project.company?.name }}</td>
            <td class="px-6 py-4"><StatusBadge :status="project.status" /></td>
            <td class="px-6 py-4 text-gray-500">{{ project.tickets_count }}</td>
            <td class="px-6 py-4 text-right space-x-3">
              <Link :href="route('projects.show', project.id)" class="text-indigo-600 hover:text-indigo-800 text-sm">Ver</Link>
              <Link :href="route('projects.edit', project.id)" class="text-yellow-600 hover:text-yellow-800 text-sm">Editar</Link>
              <button @click="destroy(project)" class="text-red-600 hover:text-red-800 text-sm">Excluir</button>
            </td>
          </tr>
        </tbody>
      </table>

      <div v-if="projects.last_page > 1" class="px-6 py-4 border-t flex justify-between items-center text-sm text-gray-600">
        <span>Página {{ projects.current_page }} de {{ projects.last_page }}</span>
        <div class="flex gap-2">
          <Link v-if="projects.prev_page_url" :href="projects.prev_page_url" class="px-3 py-1 border rounded hover:bg-gray-50">Anterior</Link>
          <Link v-if="projects.next_page_url" :href="projects.next_page_url" class="px-3 py-1 border rounded hover:bg-gray-50">Próxima</Link>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { route } from 'ziggy';
import { Link, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import StatusBadge from '@/Components/StatusBadge.vue';

defineProps({ projects: Object });

function destroy(project) {
  if (confirm(`Excluir o projeto "${project.name}"?`)) {
    router.delete(route('projects.destroy', project.id));
  }
}
</script>
