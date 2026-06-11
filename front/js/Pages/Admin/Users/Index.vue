<template>
  <AppLayout>
    <template #header>
      <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-100">Gerenciamento de Usuários</h2>
    </template>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm overflow-hidden transition-colors">
      <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
        <thead class="bg-gray-50 dark:bg-gray-700/50">
          <tr>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Usuário</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Cargo</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Roles</th>
            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Ações</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
          <tr v-if="users.data.length === 0">
            <td colspan="4" class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">Nenhum usuário encontrado.</td>
          </tr>
          <tr v-for="user in users.data" :key="user.id" class="hover:bg-gray-50 dark:hover:bg-gray-700/40 transition-colors">
            <td class="px-6 py-4">
              <p class="font-medium text-gray-900 dark:text-gray-100">{{ user.name }}</p>
              <p class="text-sm text-gray-500 dark:text-gray-400">{{ user.email }}</p>
            </td>
            <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
              {{ user.profile?.position || '—' }}
            </td>
            <td class="px-6 py-4">
              <div class="flex flex-wrap gap-1">
                <span
                  v-for="role in user.roles"
                  :key="role.id"
                  :class="roleBadge(role.name)"
                  class="px-2 py-0.5 rounded-full text-xs font-medium"
                >
                  {{ role.display_name }}
                </span>
              </div>
            </td>
            <td class="px-6 py-4 text-right space-x-3">
              <Link
                v-if="can('users.promote')"
                :href="route('admin.users.edit', user.id)"
                class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 text-sm"
              >
                Editar Roles
              </Link>
              <button
                v-if="can('users.delete') && user.id !== $page.props.auth.user.id"
                @click="destroy(user)"
                class="text-red-600 dark:text-red-400 hover:text-red-800 text-sm"
              >
                Excluir
              </button>
            </td>
          </tr>
        </tbody>
      </table>

      <div v-if="users.last_page > 1" class="px-6 py-4 border-t border-gray-200 dark:border-gray-700 flex justify-between items-center text-sm text-gray-600 dark:text-gray-400">
        <span>Página {{ users.current_page }} de {{ users.last_page }}</span>
        <div class="flex gap-2">
          <Link v-if="users.prev_page_url" :href="users.prev_page_url" class="px-3 py-1 border dark:border-gray-600 rounded hover:bg-gray-50 dark:hover:bg-gray-700">Anterior</Link>
          <Link v-if="users.next_page_url" :href="users.next_page_url" class="px-3 py-1 border dark:border-gray-600 rounded hover:bg-gray-50 dark:hover:bg-gray-700">Próxima</Link>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { route } from 'ziggy';
import { Link, router, usePage } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

defineProps({ users: Object });

const page = usePage();

function can(permission) {
  return page.props.auth.user?.permissions?.includes(permission);
}

const roleBadge = (name) => ({
  admin:   'bg-red-100 text-red-800 dark:bg-red-900/40 dark:text-red-300',
  manager: 'bg-blue-100 text-blue-800 dark:bg-blue-900/40 dark:text-blue-300',
  agent:   'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300',
}[name] ?? 'bg-gray-100 text-gray-700');

function destroy(user) {
  if (confirm(`Excluir o usuário "${user.name}"? Esta ação não pode ser desfeita.`)) {
    router.delete(route('admin.users.destroy', user.id));
  }
}
</script>
