<template>
  <AppLayout>
    <template #header>
      <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-100">Gerenciamento de Permissões</h2>
    </template>

    <div class="grid gap-6 sm:grid-cols-2">
      <div
        v-for="role in roles"
        :key="role.id"
        class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6 transition-colors"
      >
        <div class="flex items-start justify-between mb-4">
          <div>
            <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100">{{ role.display_name }}</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">{{ role.permissions.length }} permissões ativas</p>
          </div>
          <span :class="roleBadge(role.name)" class="px-2.5 py-0.5 rounded-full text-xs font-medium">
            {{ role.name }}
          </span>
        </div>

        <div class="flex flex-wrap gap-1.5 mb-5 min-h-[2rem]">
          <span
            v-for="perm in role.permissions"
            :key="perm.id"
            class="px-2 py-0.5 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded text-xs"
          >
            {{ perm.display_name }}
          </span>
          <span v-if="role.permissions.length === 0" class="text-sm text-gray-400 dark:text-gray-500 italic">
            Nenhuma permissão atribuída
          </span>
        </div>

        <Link
          :href="route('admin.roles.edit', role.id)"
          class="inline-flex items-center gap-1.5 text-sm text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 dark:hover:text-indigo-300 font-medium transition-colors"
        >
          Configurar permissões →
        </Link>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { route } from 'ziggy';
import { Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

defineProps({ roles: Array });

const roleBadge = (name) => ({
  admin: 'bg-red-100 text-red-800 dark:bg-red-900/40 dark:text-red-300',
  agent: 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300',
}[name] ?? 'bg-gray-100 text-gray-700');
</script>
