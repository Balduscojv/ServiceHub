<template>
  <AppLayout>
    <template #header>
      <div class="flex items-center gap-3">
        <Link v-if="!firstAccess" :href="route('profile.edit')" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">← Perfil</Link>
        <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-100">
          {{ firstAccess ? 'Configure o Google Authenticator' : 'Autenticação em Dois Fatores' }}
        </h2>
      </div>
    </template>

    <div class="max-w-lg mx-auto space-y-6">
      <!-- Status atual -->
      <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6">
        <div class="flex items-center gap-3 mb-2">
          <span :class="enabled ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300' : 'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-300'"
            class="px-3 py-1 rounded-full text-sm font-medium">
            {{ enabled ? '✓ Ativo' : 'Inativo' }}
          </span>
          <h3 class="font-medium text-gray-800 dark:text-gray-100">Google Authenticator</h3>
        </div>
        <p class="text-sm text-gray-500 dark:text-gray-400">
          Adiciona uma camada extra de segurança exigindo um código temporário no login.
        </p>
      </div>

      <!-- Desativar -->
      <div v-if="enabled" class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6">
        <h3 class="font-medium text-gray-800 dark:text-gray-100 mb-4">Desativar 2FA</h3>
        <form @submit.prevent="disableForm.delete(route('two-factor.disable'))" class="space-y-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Confirme sua senha</label>
            <input v-model="disableForm.password" type="password" class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 focus:ring-2 focus:ring-red-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100" required />
            <InputError :message="disableForm.errors.password" />
          </div>
          <button type="submit" :disabled="disableForm.processing" class="bg-red-600 text-white px-6 py-2 rounded-lg hover:bg-red-700 disabled:opacity-50">
            Desativar 2FA
          </button>
        </form>
      </div>

      <!-- Configurar/Ativar -->
      <div v-if="!enabled" class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6 space-y-4">
        <h3 class="font-medium text-gray-800 dark:text-gray-100">Configurar 2FA</h3>

        <ol class="text-sm text-gray-600 dark:text-gray-400 space-y-1 list-decimal list-inside">
          <li>Instale o <strong>Google Authenticator</strong> ou <strong>Authy</strong> no seu celular</li>
          <li>Escaneie o QR Code abaixo, ou insira a chave manualmente</li>
          <li>Digite o código de 6 dígitos para confirmar</li>
        </ol>

        <!-- QR Code -->
        <div class="flex flex-col items-center py-4">
          <canvas ref="qrCanvas" class="rounded-lg border border-gray-200 dark:border-gray-700"></canvas>
          <p class="text-xs text-gray-500 dark:text-gray-400 mt-3">
            Chave manual:
            <code class="bg-gray-100 dark:bg-gray-700 px-2 py-0.5 rounded font-mono text-gray-800 dark:text-gray-200 select-all">{{ secret }}</code>
          </p>
        </div>

        <!-- Confirmação -->
        <form @submit.prevent="enable" class="space-y-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Código de verificação</label>
            <input
              v-model="enableForm.code"
              type="text"
              inputmode="numeric"
              maxlength="6"
              pattern="[0-9]{6}"
              placeholder="000000"
              class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 text-center text-xl tracking-widest font-mono focus:ring-2 focus:ring-indigo-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100"
              required
            />
            <InputError :message="enableForm.errors.code" />
          </div>
          <button type="submit" :disabled="enableForm.processing || enableForm.code.length !== 6" class="bg-indigo-600 text-white px-6 py-2 rounded-lg hover:bg-indigo-700 disabled:opacity-50 font-medium">
            {{ enableForm.processing ? 'Ativando...' : 'Ativar 2FA' }}
          </button>
        </form>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { route } from 'ziggy';
import { ref, onMounted } from 'vue';
import { useForm, Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import InputError from '@/Components/InputError.vue';
import QRCode from 'qrcode';

const props = defineProps({ qrUri: String, secret: String, enabled: Boolean, firstAccess: Boolean });

const qrCanvas = ref(null);
const enableForm = useForm({ code: '' });
const disableForm = useForm({ password: '' });

onMounted(() => {
  if (!props.enabled && qrCanvas.value) {
    QRCode.toCanvas(qrCanvas.value, props.qrUri, { width: 200, margin: 2 });
  }
});

function enable() {
  enableForm.post(route('two-factor.enable'));
}
</script>
