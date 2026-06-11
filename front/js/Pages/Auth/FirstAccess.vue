<template>
  <div class="min-h-screen bg-gray-100 dark:bg-gray-900 flex items-center justify-center transition-colors duration-200">
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-8 w-full max-w-md">
      <!-- Header -->
      <div class="text-center mb-8">
        <div class="w-14 h-14 bg-indigo-100 dark:bg-indigo-900 rounded-full flex items-center justify-center mx-auto mb-4">
          <svg class="w-7 h-7 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
          </svg>
        </div>
        <h1 class="text-xl font-bold text-gray-800 dark:text-gray-100">Primeiro acesso</h1>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
          Olá, <strong>{{ name }}</strong>. Defina sua senha para continuar.
        </p>
      </div>

      <!-- E-mail exibido -->
      <div class="flex items-center gap-2 p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg border border-gray-200 dark:border-gray-700 mb-6">
        <svg class="w-4 h-4 text-gray-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207" />
        </svg>
        <span class="text-sm text-gray-600 dark:text-gray-300 truncate">{{ email }}</span>
      </div>

      <form @submit.prevent="submit" class="space-y-4">
        <div>
          <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nova senha</label>
          <input
            v-model="form.password"
            type="password"
            class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100"
            placeholder="Mínimo 8 caracteres"
            required
            autofocus
          />
          <InputError :message="form.errors.password" />
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Confirmar senha</label>
          <input
            v-model="form.password_confirmation"
            type="password"
            class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100"
            required
          />
        </div>

        <!-- Requisitos de senha -->
        <ul class="text-xs text-gray-500 dark:text-gray-400 space-y-1 pl-1">
          <li :class="form.password.length >= 8 ? 'text-green-600 dark:text-green-400' : ''">
            {{ form.password.length >= 8 ? '✓' : '·' }} Mínimo de 8 caracteres
          </li>
          <li :class="form.password === form.password_confirmation && form.password.length > 0 ? 'text-green-600 dark:text-green-400' : ''">
            {{ form.password === form.password_confirmation && form.password.length > 0 ? '✓' : '·' }} Senhas conferem
          </li>
        </ul>

        <button
          type="submit"
          :disabled="form.processing || form.password.length < 8 || form.password !== form.password_confirmation"
          class="w-full bg-indigo-600 text-white py-2.5 px-4 rounded-lg hover:bg-indigo-700 disabled:opacity-50 font-medium transition-colors mt-2"
        >
          {{ form.processing ? 'Salvando...' : 'Definir senha e continuar' }}
        </button>
      </form>

      <p class="text-center text-xs text-gray-400 dark:text-gray-500 mt-6">
        Após definir a senha você será redirecionado para configurar o Google Authenticator.
      </p>
    </div>
  </div>
</template>

<script setup>
import { route } from 'ziggy';
import { useForm } from '@inertiajs/vue3';
import InputError from '@/Components/InputError.vue';

const props = defineProps({
  email: String,
  name:  String,
});

const form = useForm({
  password:              '',
  password_confirmation: '',
});

function submit() {
  form.post(route('first-access.set'), { onFinish: () => form.reset('password', 'password_confirmation') });
}
</script>
