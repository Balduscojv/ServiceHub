<template>
  <div class="min-h-screen bg-gray-100 dark:bg-gray-900 flex items-center justify-center">
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-8 w-full max-w-md">
      <div class="text-center mb-6">
        <div class="w-14 h-14 bg-indigo-100 dark:bg-indigo-900 rounded-full flex items-center justify-center mx-auto mb-4">
          <svg class="w-7 h-7 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
          </svg>
        </div>
        <h1 class="text-xl font-bold text-gray-800 dark:text-gray-100">Verificação em duas etapas</h1>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Insira o código do seu aplicativo autenticador</p>
      </div>

      <form @submit.prevent="submit" class="space-y-4">
        <div>
          <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Código de 6 dígitos</label>
          <input
            v-model="form.code"
            type="text"
            inputmode="numeric"
            maxlength="6"
            pattern="[0-9]{6}"
            placeholder="000000"
            class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-3 text-center text-2xl tracking-widest font-mono focus:ring-2 focus:ring-indigo-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100"
            required
            autofocus
          />
          <InputError :message="form.errors.code" />
        </div>

        <button
          type="submit"
          :disabled="form.processing || form.code.length !== 6"
          class="w-full bg-indigo-600 text-white py-2 px-4 rounded-lg hover:bg-indigo-700 disabled:opacity-50 font-medium"
        >
          {{ form.processing ? 'Verificando...' : 'Verificar' }}
        </button>
      </form>

      <form @submit.prevent="logout" class="mt-4 text-center">
        <button type="submit" class="text-sm text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200">
          Usar outra conta
        </button>
      </form>
    </div>
  </div>
</template>

<script setup>
import { route } from 'ziggy';
import { useForm, router } from '@inertiajs/vue3';
import InputError from '@/Components/InputError.vue';

const form = useForm({ code: '' });

function submit() {
  form.post(route('two-factor.verify'));
}

function logout() {
  router.post(route('logout'));
}
</script>
