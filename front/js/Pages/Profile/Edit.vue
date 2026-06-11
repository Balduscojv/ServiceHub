<template>
  <AppLayout>
    <template #header>
      <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-100">Meu Perfil</h2>
    </template>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 max-w-3xl mx-auto">
      <!-- Dados pessoais -->
      <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6 transition-colors">
        <h3 class="text-lg font-medium text-gray-800 dark:text-gray-100 mb-4">Dados Pessoais</h3>
        <form @submit.prevent="submit" class="space-y-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nome *</label>
            <input v-model="form.name" type="text"
              class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100"
              required />
            <InputError :message="form.errors.name" />
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">E-mail</label>
            <input :value="user.email" type="email"
              class="w-full border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700/50 rounded-lg px-3 py-2 text-gray-500 dark:text-gray-400 cursor-not-allowed"
              disabled />
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Telefone</label>
            <input v-model="form.phone" type="text"
              class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100" />
            <InputError :message="form.errors.phone" />
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Cargo</label>
            <input v-model="form.position" type="text"
              class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100" />
            <InputError :message="form.errors.position" />
          </div>

          <button type="submit" :disabled="form.processing"
            class="bg-indigo-600 text-white px-6 py-2 rounded-lg hover:bg-indigo-700 disabled:opacity-50 font-medium transition-colors">
            {{ form.processing ? 'Salvando...' : 'Salvar' }}
          </button>
        </form>
      </div>

      <!-- Segurança -->
      <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6 transition-colors">
        <h3 class="text-lg font-medium text-gray-800 dark:text-gray-100 mb-4">Segurança</h3>
        <div class="flex items-center justify-between p-4 border border-gray-200 dark:border-gray-700 rounded-lg">
          <div>
            <p class="font-medium text-gray-800 dark:text-gray-100 text-sm">Autenticação em Dois Fatores</p>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
              {{ user.two_factor_enabled_at ? '✓ Ativo' : 'Não configurado' }}
            </p>
          </div>
          <Link
            :href="route('two-factor.setup')"
            :class="user.two_factor_enabled_at
              ? 'border-yellow-300 dark:border-yellow-700 text-yellow-700 dark:text-yellow-400 hover:bg-yellow-50 dark:hover:bg-yellow-900/20'
              : 'border-indigo-300 dark:border-indigo-700 text-indigo-700 dark:text-indigo-400 hover:bg-indigo-50 dark:hover:bg-indigo-900/20'"
            class="px-3 py-1.5 border rounded-lg text-sm font-medium transition-colors"
          >
            {{ user.two_factor_enabled_at ? 'Gerenciar' : 'Ativar' }}
          </Link>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { route } from 'ziggy';
import { useForm, Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import InputError from '@/Components/InputError.vue';

const props = defineProps({ user: Object });

const form = useForm({
  name: props.user.name,
  phone: props.user.profile?.phone ?? '',
  position: props.user.profile?.position ?? '',
});

function submit() {
  form.patch(route('profile.update'));
}
</script>
