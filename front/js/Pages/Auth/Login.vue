<template>
  <div class="min-h-screen bg-gray-100 dark:bg-gray-900 flex items-center justify-center transition-colors duration-200">
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-8 w-full max-w-md">
      <div class="text-center mb-8">
        <h1 class="text-3xl font-bold text-indigo-600 dark:text-indigo-400">ServiceHub</h1>
        <p class="text-gray-500 dark:text-gray-400 mt-1 text-sm">Gestão de ordens de serviço</p>
      </div>

      <!-- Passo 1: e-mail -->
      <form v-if="step === 'email'" @submit.prevent="submitIdentify" class="space-y-4">
        <div>
          <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">E-mail</label>
          <input
            v-model="identifyForm.email"
            type="email"
            class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500"
            placeholder="seu@email.com"
            required
            autofocus
          />
          <InputError :message="identifyForm.errors.email" />
        </div>

        <button
          type="submit"
          :disabled="identifyForm.processing"
          class="w-full bg-indigo-600 text-white py-2.5 px-4 rounded-lg hover:bg-indigo-700 disabled:opacity-50 font-medium transition-colors"
        >
          {{ identifyForm.processing ? 'Verificando...' : 'Continuar' }}
        </button>
      </form>

      <!-- Passo 2: senha -->
      <form v-else @submit.prevent="submitLogin" class="space-y-4">
        <!-- Cabeçalho do passo 2 com e-mail exibido -->
        <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg border border-gray-200 dark:border-gray-700 mb-2">
          <div class="flex items-center gap-2 min-w-0">
            <div class="w-7 h-7 rounded-full bg-indigo-100 dark:bg-indigo-900 flex items-center justify-center text-indigo-600 dark:text-indigo-300 text-xs font-bold shrink-0">
              {{ props.email.charAt(0).toUpperCase() }}
            </div>
            <span class="text-sm text-gray-700 dark:text-gray-300 truncate">{{ props.email }}</span>
          </div>
          <Link :href="route('login.reset')" class="text-xs text-indigo-600 dark:text-indigo-400 hover:underline shrink-0 ml-2">
            Trocar
          </Link>
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Senha</label>
          <input
            v-model="loginForm.password"
            type="password"
            class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100"
            required
            autofocus
          />
          <InputError :message="loginForm.errors.password" />
        </div>

        <button
          type="submit"
          :disabled="loginForm.processing"
          class="w-full bg-indigo-600 text-white py-2.5 px-4 rounded-lg hover:bg-indigo-700 disabled:opacity-50 font-medium transition-colors"
        >
          {{ loginForm.processing ? 'Entrando...' : 'Entrar' }}
        </button>
      </form>
    </div>
  </div>
</template>

<script setup>
import { route } from 'ziggy';
import { useForm, Link } from '@inertiajs/vue3';
import InputError from '@/Components/InputError.vue';

const props = defineProps({
  step:  { type: String, default: 'email' },
  email: { type: String, default: '' },
});

const identifyForm = useForm({ email: props.email || '' });
const loginForm    = useForm({ password: '' });

function submitIdentify() {
  identifyForm.post(route('login.identify'));
}

function submitLogin() {
  loginForm.post(route('login.attempt'), { onFinish: () => loginForm.reset('password') });
}
</script>
