<template>
  <AppLayout>
    <template #header>
      <div class="flex items-center justify-between">
        <div class="flex items-center gap-3">
          <Link :href="route('companies.index')" class="text-gray-500 hover:text-gray-700">← Empresas</Link>
          <h2 class="text-xl font-semibold text-gray-800">{{ company.name }}</h2>
        </div>
        <Link :href="route('companies.edit', company.id)" class="bg-yellow-500 text-white px-4 py-2 rounded-lg text-sm hover:bg-yellow-600">
          Editar
        </Link>
      </div>
    </template>

    <div class="grid grid-cols-1 gap-6">
      <!-- Company Info -->
      <div class="bg-white rounded-xl shadow-sm p-6">
        <h3 class="text-lg font-medium text-gray-800 mb-4">Informações</h3>
        <dl class="grid grid-cols-2 gap-4">
          <div>
            <dt class="text-sm text-gray-500">Nome</dt>
            <dd class="font-medium text-gray-800">{{ company.name }}</dd>
          </div>
          <div>
            <dt class="text-sm text-gray-500">CNPJ</dt>
            <dd class="font-medium text-gray-800">{{ company.cnpj || '—' }}</dd>
          </div>
        </dl>
      </div>

      <!-- Projects -->
      <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b flex justify-between items-center">
          <h3 class="text-lg font-medium text-gray-800">Projetos ({{ company.projects?.length }})</h3>
          <Link :href="route('projects.create')" class="bg-indigo-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-indigo-700">
            Novo Projeto
          </Link>
        </div>
        <div class="divide-y divide-gray-100">
          <div v-if="!company.projects?.length" class="px-6 py-8 text-center text-gray-500">
            Nenhum projeto cadastrado.
          </div>
          <Link
            v-for="project in company.projects"
            :key="project.id"
            :href="route('projects.show', project.id)"
            class="flex items-center justify-between px-6 py-4 hover:bg-gray-50"
          >
            <div>
              <p class="font-medium text-gray-800">{{ project.name }}</p>
              <p class="text-sm text-gray-500">{{ project.tickets?.length ?? 0 }} tickets</p>
            </div>
            <StatusBadge :status="project.status" />
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

defineProps({ company: Object });
</script>
