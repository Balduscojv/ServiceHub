<template>
  <AppLayout>
    <template #header>
      <div class="flex items-center gap-3">
        <Link :href="route('admin.roles.index')" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
          ← Permissões
        </Link>
        <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-100">
          Configurar — {{ role.display_name }}
        </h2>
      </div>
    </template>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6 max-w-2xl mx-auto transition-colors">
      <form @submit.prevent="submit" class="space-y-6">
        <!-- Grupos de permissões -->
        <div v-for="(group, groupName) in grouped" :key="groupName" class="space-y-2">
          <div class="flex items-center justify-between mb-1">
            <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wide">
              {{ groupName }}
            </h3>
            <button
              type="button"
              @click="toggleGroup(group)"
              class="text-xs text-indigo-600 dark:text-indigo-400 hover:underline"
            >
              {{ allGroupSelected(group) ? 'Desmarcar todas' : 'Marcar todas' }}
            </button>
          </div>

          <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
            <label
              v-for="perm in group"
              :key="perm.id"
              class="flex items-center gap-3 p-3 rounded-lg border cursor-pointer transition-colors"
              :class="form.permissions.includes(perm.id)
                ? 'border-indigo-300 dark:border-indigo-600 bg-indigo-50 dark:bg-indigo-900/20'
                : 'border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700/40'"
            >
              <input
                type="checkbox"
                :value="perm.id"
                v-model="form.permissions"
                class="rounded border-gray-300 dark:border-gray-600 text-indigo-600"
              />
              <span class="text-sm text-gray-800 dark:text-gray-200">{{ perm.display_name }}</span>
            </label>
          </div>
        </div>

        <InputError :message="form.errors.permissions" />

        <div class="flex gap-3 pt-2 border-t border-gray-200 dark:border-gray-700">
          <button
            type="submit"
            :disabled="form.processing"
            class="bg-indigo-600 text-white px-6 py-2 rounded-lg hover:bg-indigo-700 disabled:opacity-50 font-medium transition-colors"
          >
            {{ form.processing ? 'Salvando...' : 'Salvar permissões' }}
          </button>
          <Link
            :href="route('admin.roles.index')"
            class="px-6 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors"
          >
            Cancelar
          </Link>
        </div>
      </form>
    </div>
  </AppLayout>
</template>

<script setup>
import { route } from 'ziggy';
import { computed } from 'vue';
import { useForm, Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import InputError from '@/Components/InputError.vue';

const props = defineProps({ role: Object, permissions: Array });

const form = useForm({
  permissions: props.role.permissions.map(p => p.id),
});

const groupLabels = {
  companies: 'Empresas',
  projects:  'Projetos',
  tickets:   'Tickets',
  users:     'Usuários',
};

const grouped = computed(() => {
  const groups = {};
  for (const perm of props.permissions) {
    const key = perm.name.split('.')[0];
    const label = groupLabels[key] ?? key;
    if (!groups[label]) groups[label] = [];
    groups[label].push(perm);
  }
  return groups;
});

function allGroupSelected(group) {
  return group.every(p => form.permissions.includes(p.id));
}

function toggleGroup(group) {
  const ids = group.map(p => p.id);
  if (allGroupSelected(group)) {
    form.permissions = form.permissions.filter(id => !ids.includes(id));
  } else {
    const toAdd = ids.filter(id => !form.permissions.includes(id));
    form.permissions = [...form.permissions, ...toAdd];
  }
}

function submit() {
  form.patch(route('admin.roles.update', props.role.id));
}
</script>
