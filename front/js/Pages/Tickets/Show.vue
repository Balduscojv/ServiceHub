<template>
  <AppLayout>
    <template #header>
      <div class="flex items-center justify-between">
        <div class="flex items-center gap-3">
          <Link :href="route('tickets.index')" class="text-gray-500 hover:text-gray-700">← Tickets</Link>
          <h2 class="text-xl font-semibold text-gray-800">#{{ ticket.id }} — {{ ticket.title }}</h2>
        </div>
        <div class="flex gap-2">
          <Link :href="route('tickets.edit', ticket.id)" class="bg-yellow-500 text-white px-4 py-2 rounded-lg text-sm hover:bg-yellow-600">Editar</Link>
          <button @click="destroy" class="bg-red-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-red-700">Excluir</button>
        </div>
      </div>
    </template>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
      <!-- Main Info -->
      <div class="lg:col-span-2 space-y-6">
        <div class="bg-white rounded-xl shadow-sm p-6">
          <h3 class="text-lg font-medium text-gray-800 mb-4">Descrição</h3>
          <p class="text-gray-700 whitespace-pre-wrap">{{ ticket.description }}</p>
        </div>

        <!-- Ticket Detail -->
        <div class="bg-white rounded-xl shadow-sm p-6">
          <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-medium text-gray-800">Detalhes Técnicos</h3>
            <span v-if="ticket.detail?.processed_at" class="text-xs text-green-600 bg-green-50 px-2 py-1 rounded-full">
              Processado em {{ formatDate(ticket.detail.processed_at) }}
            </span>
            <span v-else-if="ticket.attachment_path" class="text-xs text-yellow-600 bg-yellow-50 px-2 py-1 rounded-full">
              Aguardando processamento
            </span>
          </div>

          <div v-if="ticket.detail?.technical_notes" class="mb-4">
            <p class="text-sm font-medium text-gray-600 mb-1">Notas Técnicas</p>
            <p class="text-gray-700 whitespace-pre-wrap bg-gray-50 rounded-lg p-3 text-sm">{{ ticket.detail.technical_notes }}</p>
          </div>

          <div v-if="ticket.detail?.additional_data">
            <p class="text-sm font-medium text-gray-600 mb-1">Dados Adicionais (JSON)</p>
            <pre class="bg-gray-900 text-green-400 rounded-lg p-4 text-xs overflow-auto max-h-64">{{ JSON.stringify(ticket.detail.additional_data, null, 2) }}</pre>
          </div>

          <p v-if="!ticket.detail?.technical_notes && !ticket.detail?.additional_data" class="text-gray-500 text-sm">
            Nenhum detalhe técnico disponível ainda.
          </p>
        </div>
      </div>

      <!-- Sidebar -->
      <div class="space-y-4">
        <div class="bg-white rounded-xl shadow-sm p-6 space-y-4">
          <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wide">Informações</h3>

          <div>
            <p class="text-xs text-gray-500">Status</p>
            <StatusBadge :status="ticket.status" class="mt-1" />
          </div>
          <div>
            <p class="text-xs text-gray-500">Prioridade</p>
            <PriorityBadge :priority="ticket.priority" class="mt-1" />
          </div>
          <div>
            <p class="text-xs text-gray-500">Projeto</p>
            <Link :href="route('projects.show', ticket.project?.id)" class="text-indigo-600 text-sm hover:underline">
              {{ ticket.project?.name }}
            </Link>
          </div>
          <div>
            <p class="text-xs text-gray-500">Empresa</p>
            <p class="text-sm text-gray-800">{{ ticket.project?.company?.name }}</p>
          </div>
          <div>
            <p class="text-xs text-gray-500">Responsável</p>
            <p class="text-sm text-gray-800">{{ ticket.user?.name }}</p>
            <p v-if="ticket.user?.profile?.position" class="text-xs text-gray-500">{{ ticket.user.profile.position }}</p>
          </div>
          <div v-if="ticket.attachment_path">
            <p class="text-xs text-gray-500">Anexo</p>
            <p class="text-sm text-gray-800 bg-gray-50 rounded px-2 py-1 font-mono text-xs">
              {{ ticket.attachment_path.split('/').pop() }}
            </p>
          </div>
          <div>
            <p class="text-xs text-gray-500">Criado em</p>
            <p class="text-sm text-gray-800">{{ formatDate(ticket.created_at) }}</p>
          </div>
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
import PriorityBadge from '@/Components/PriorityBadge.vue';

const props = defineProps({ ticket: Object });

function destroy() {
  if (confirm(`Excluir o ticket "${props.ticket.title}"?`)) {
    router.delete(route('tickets.destroy', props.ticket.id));
  }
}

function formatDate(date) {
  if (!date) return '—';
  return new Date(date).toLocaleString('pt-BR');
}
</script>
