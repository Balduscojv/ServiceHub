<template>
  <AppLayout>
    <template #header>
      <div class="flex items-center gap-3">
        <Link :href="route('projects.index')" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">← Projetos</Link>
        <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-100">Editar Projeto</h2>
      </div>
    </template>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6 max-w-lg mx-auto">
      <form @submit.prevent="submit" class="space-y-4">
        <div>
          <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Empresa *</label>
          <select v-model="form.company_id" class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100" required>
            <option value="">Selecione uma empresa</option>
            <option v-for="c in companies" :key="c.id" :value="c.id">{{ c.name }}</option>
          </select>
          <InputError :message="form.errors.company_id" />
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nome *</label>
          <input v-model="form.name" type="text" class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100" required />
          <InputError :message="form.errors.name" />
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Descrição</label>
          <textarea v-model="form.description" rows="3" class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100"></textarea>
          <InputError :message="form.errors.description" />
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Status *</label>
          <select v-model="form.status" class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100" required>
            <option value="active">Ativo</option>
            <option value="inactive">Inativo</option>
            <option value="completed">Concluído</option>
          </select>
          <InputError :message="form.errors.status" />
        </div>

        <div class="flex gap-3 pt-2">
          <button type="submit" :disabled="form.processing" class="bg-indigo-600 text-white px-6 py-2 rounded-lg hover:bg-indigo-700 disabled:opacity-50 font-medium">
            {{ form.processing ? 'Salvando...' : 'Salvar' }}
          </button>
          <Link :href="route('projects.index')" class="px-6 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700">Cancelar</Link>
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

const props = defineProps({ project: Object, companies: Array });

const form = useForm({
  company_id: props.project.company_id,
  name: props.project.name,
  description: props.project.description ?? '',
  status: props.project.status,
});

function submit() {
  form.patch(route('projects.update', props.project.id));
}
</script>
