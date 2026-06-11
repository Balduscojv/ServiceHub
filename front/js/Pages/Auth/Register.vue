<template>
  <div class="min-h-screen bg-gray-100 dark:bg-gray-900 flex items-center justify-center py-8 transition-colors duration-200">
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-8 w-full max-w-md">
      <div class="text-center mb-8">
        <h1 class="text-3xl font-bold text-indigo-600 dark:text-indigo-400">ServiceHub</h1>
        <p class="text-gray-500 dark:text-gray-400 mt-1 text-sm">Criar nova conta</p>
      </div>

      <form @submit.prevent="submit" class="space-y-4">
        <div>
          <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nome *</label>
          <input v-model="form.name" type="text"
            class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100"
            required autofocus />
          <InputError :message="form.errors.name" />
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">E-mail *</label>
          <input v-model="form.email" type="email"
            class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100"
            required />
          <InputError :message="form.errors.email" />
        </div>

        <div class="grid grid-cols-2 gap-3">
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
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Senha *</label>
          <input v-model="form.password" type="password"
            class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100"
            required />
          <InputError :message="form.errors.password" />
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Confirmar Senha *</label>
          <input v-model="form.password_confirmation" type="password"
            class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100"
            required />
        </div>

        <button type="submit" :disabled="form.processing"
          class="w-full bg-indigo-600 text-white py-2.5 px-4 rounded-lg hover:bg-indigo-700 disabled:opacity-50 font-medium transition-colors">
          {{ form.processing ? 'Registrando...' : 'Criar Conta' }}
        </button>
      </form>

      <div class="mt-6 text-center">
        <p class="text-sm text-gray-600 dark:text-gray-400">
          Já tem conta?
          <Link :href="route('login')" class="text-indigo-600 dark:text-indigo-400 hover:underline font-medium">Entrar</Link>
        </p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { route } from 'ziggy';
import { useForm, Link } from '@inertiajs/vue3';
import InputError from '@/Components/InputError.vue';

const form = useForm({ name: '', email: '', phone: '', position: '', password: '', password_confirmation: '' });

function submit() {
  form.post(route('register'), { onFinish: () => form.reset('password', 'password_confirmation') });
}
</script>
