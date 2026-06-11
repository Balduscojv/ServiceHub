<template>
  <AppLayout>
    <template #header>
      <div class="flex items-center gap-3">
        <Link :href="route('tickets.show', ticket.id)" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">← Ticket #{{ ticket.id }}</Link>
        <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-100">Editar Ticket</h2>
      </div>
    </template>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6 max-w-2xl mx-auto">
      <form @submit.prevent="submit" class="space-y-4">
        <div>
          <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Projeto *</label>
          <select v-model="form.project_id" class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100" required>
            <option value="">Selecione um projeto</option>
            <option v-for="p in projects" :key="p.id" :value="p.id">{{ p.name }}</option>
          </select>
          <InputError :message="form.errors.project_id" />
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Título *</label>
          <input v-model="form.title" type="text" class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100" required />
          <InputError :message="form.errors.title" />
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Descrição *</label>
          <textarea v-model="form.description" rows="4" class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100" required></textarea>
          <InputError :message="form.errors.description" />
        </div>

        <div class="grid grid-cols-2 gap-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Status *</label>
            <select v-model="form.status" class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100" required>
              <option value="open">Aberto</option>
              <option value="in_progress">Em andamento</option>
              <option value="closed">Fechado</option>
            </select>
            <InputError :message="form.errors.status" />
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Prioridade *</label>
            <select v-model="form.priority" class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100" required>
              <option value="low">Baixa</option>
              <option value="medium">Média</option>
              <option value="high">Alta</option>
            </select>
            <InputError :message="form.errors.priority" />
          </div>
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Novo Anexo (opcional)</label>
          <input
            type="file"
            accept=".json,.txt"
            class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 text-sm bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-300 file:mr-3 file:py-1 file:px-3 file:rounded file:border-0 file:bg-indigo-50 file:text-indigo-700 dark:file:bg-indigo-900 dark:file:text-indigo-300"
            @change="e => form.attachment = e.target.files[0]"
          />
          <p v-if="ticket.attachment_path" class="text-xs text-gray-500 dark:text-gray-400 mt-1">
            Anexo atual: {{ ticket.attachment_path.split('/').pop() }} — enviar novo arquivo substituirá o atual.
          </p>
          <InputError :message="form.errors.attachment" />
        </div>

        <div class="flex gap-3 pt-2">
          <button type="submit" :disabled="form.processing" class="bg-indigo-600 text-white px-6 py-2 rounded-lg hover:bg-indigo-700 disabled:opacity-50 font-medium">
            {{ form.processing ? 'Salvando...' : 'Salvar' }}
          </button>
          <Link :href="route('tickets.show', ticket.id)" class="px-6 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700">Cancelar</Link>
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

const props = defineProps({ ticket: Object, projects: Array });

const form = useForm({
  _method: 'PATCH',
  project_id: props.ticket.project_id,
  title: props.ticket.title,
  description: props.ticket.description,
  status: props.ticket.status,
  priority: props.ticket.priority,
  attachment: null,
});

function submit() {
  form.post(route('tickets.update', props.ticket.id), { forceFormData: true });
}
</script>
