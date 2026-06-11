<template>
  <AppLayout>
    <template #header>
      <div class="flex items-center gap-3">
        <Link :href="route('admin.users.index')" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">← Usuários</Link>
        <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-100">Editar Roles — {{ user.name }}</h2>
      </div>
    </template>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6 max-w-lg mx-auto transition-colors">
      <!-- Info do usuário -->
      <div class="flex items-center gap-4 mb-6 pb-6 border-b border-gray-200 dark:border-gray-700">
        <div class="w-12 h-12 rounded-full bg-indigo-100 dark:bg-indigo-900 flex items-center justify-center text-indigo-600 dark:text-indigo-300 font-bold text-lg">
          {{ user.name.charAt(0).toUpperCase() }}
        </div>
        <div>
          <p class="font-medium text-gray-800 dark:text-gray-100">{{ user.name }}</p>
          <p class="text-sm text-gray-500 dark:text-gray-400">{{ user.email }}</p>
        </div>
      </div>

      <form @submit.prevent="submit" class="space-y-4">
        <div>
          <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">Roles atribuídas</label>
          <div class="space-y-2">
            <label
              v-for="role in roles"
              :key="role.id"
              class="flex items-center gap-3 p-3 rounded-lg border border-gray-200 dark:border-gray-700 cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors"
              :class="form.roles.includes(role.name) ? 'border-indigo-300 dark:border-indigo-600 bg-indigo-50 dark:bg-indigo-900/20' : ''"
            >
              <input
                type="checkbox"
                :value="role.name"
                v-model="form.roles"
                class="rounded border-gray-300 dark:border-gray-600 text-indigo-600"
              />
              <div>
                <p class="font-medium text-gray-800 dark:text-gray-100 text-sm">{{ role.display_name }}</p>
                <p class="text-xs text-gray-500 dark:text-gray-400">{{ roleDescription(role.name) }}</p>
              </div>
            </label>
          </div>
          <InputError :message="form.errors.roles" />
        </div>

        <div class="flex gap-3 pt-2">
          <button
            type="submit"
            :disabled="form.processing || form.roles.length === 0"
            class="bg-indigo-600 text-white px-6 py-2 rounded-lg hover:bg-indigo-700 disabled:opacity-50 font-medium transition-colors"
          >
            {{ form.processing ? 'Salvando...' : 'Salvar' }}
          </button>
          <Link :href="route('admin.users.index')" class="px-6 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700/50">
            Cancelar
          </Link>
        </div>
      </form>
    </div>
  </AppLayout>
</template>

<script setup>
import { route } from 'ziggy';
import { useForm, Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import InputError from '@/Components/InputError.vue';

const props = defineProps({ user: Object, roles: Array });

const form = useForm({
  roles: props.user.roles.map(r => r.name),
});

const descriptions = {
  admin:   'Acesso total ao sistema, incluindo gerenciamento de usuários.',
  manager: 'Gerencia empresas, projetos e tickets. Visualiza usuários.',
  agent:   'Cria e gerencia tickets. Visualiza empresas e projetos.',
};

function roleDescription(name) {
  return descriptions[name] ?? '';
}

function submit() {
  form.patch(route('admin.users.update', props.user.id));
}
</script>
