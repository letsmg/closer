<template>
  <div class="min-h-screen bg-gray-100 py-12">
    <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
      <div class="bg-white shadow rounded-lg p-6">
        <h2 class="text-2xl font-bold text-gray-900 mb-6">
          Autenticação de Dois Fatores
        </h2>

        <!-- Status -->
        <div v-if="!isEnabled" class="mb-6">
          <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4">
            <p class="text-sm text-yellow-700">
              O 2FA está desativado. Ative para maior segurança.
            </p>
          </div>
        </div>

        <div v-else class="mb-6">
          <div class="bg-green-50 border-l-4 border-green-400 p-4">
            <p class="text-sm text-green-700">
              ✅ 2FA está ativado. Sua conta está mais segura!
            </p>
          </div>
        </div>

        <!-- Setup -->
        <div v-if="!isEnabled && !setupData" class="space-y-4">
          <p class="text-gray-600">
            Use um app de autenticação como Google Authenticator, Authy ou Microsoft Authenticator.
          </p>
          <button
            @click="startSetup"
            :disabled="loading"
            class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50"
          >
            {{ loading ? 'Configurando...' : 'Ativar 2FA' }}
          </button>
        </div>

        <!-- QR Code -->
        <div v-if="setupData" class="space-y-6">
          <div class="text-center">
            <p class="text-sm text-gray-600 mb-4">
              Escaneie este QR Code com seu app de autenticação:
            </p>
            <div class="flex justify-center">
              <!-- QR Code component would go here -->
              <div class="bg-white p-4 border rounded">
                <p class="text-xs text-gray-500 mb-2">Secret (caso precise inserir manualmente):</p>
                <code class="text-sm bg-gray-100 px-2 py-1 rounded">{{ setupData.data.secret }}</code>
              </div>
            </div>
          </div>

          <div class="bg-red-50 border border-red-200 p-4 rounded">
            <p class="text-sm text-red-800 font-medium">⚠️ Códigos de backup - Salve em local seguro:</p>
            <ul class="mt-2 text-sm text-red-700 space-y-1">
              <li v-for="code in setupData.data.recovery_codes" :key="code">{{ code }}</li>
            </ul>
            <p class="text-xs text-red-600 mt-2">Estes códigos só são mostrados uma vez!</p>
          </div>

          <form @submit.prevent="confirmSetup" class="space-y-4">
            <div>
              <label class="block text-sm font-medium text-gray-700">
                Digite o código de 6 dígitos do app:
              </label>
              <input
                v-model="confirmCode"
                type="text"
                maxlength="6"
                required
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                placeholder="000000"
              />
            </div>

            <div class="flex space-x-4">
              <button
                type="submit"
                :disabled="loading"
                class="flex-1 py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 disabled:opacity-50"
              >
                {{ loading ? 'Verificando...' : 'Confirmar' }}
              </button>
              <button
                type="button"
                @click="cancelSetup"
                class="flex-1 py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50"
              >
                Cancelar
              </button>
            </div>
          </form>
        </div>

        <!-- Disable 2FA -->
        <div v-if="isEnabled" class="mt-6 pt-6 border-t">
          <h3 class="text-lg font-medium text-gray-900 mb-4">Desativar 2FA</h3>
          <form @submit.prevent="disable2FA" class="space-y-4">
            <div>
              <label class="block text-sm font-medium text-gray-700">Senha atual</label>
              <input
                v-model="disableForm.password"
                type="password"
                required
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
              />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700">Código 2FA</label>
              <input
                v-model="disableForm.code"
                type="text"
                maxlength="6"
                required
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                placeholder="Código do app"
              />
            </div>
            <button
              type="submit"
              :disabled="loading"
              class="w-full py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 disabled:opacity-50"
            >
              {{ loading ? 'Desativando...' : 'Desativar 2FA' }}
            </button>
          </form>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { useAuthStore } from '../stores/auth';
import api from '../api';

const authStore = useAuthStore();

const isEnabled = ref(false);
const loading = ref(false);
const setupData = ref(null);
const confirmCode = ref('');
const disableForm = ref({ password: '', code: '' });

const checkStatus = async () => {
  try {
    const response = await api.get('/api/2fa/status');
    isEnabled.value = response.data.data.enabled;
  } catch (err) {
    console.error('Error checking 2FA status:', err);
  }
};

const startSetup = async () => {
  loading.value = true;
  try {
    const response = await api.get('/api/2fa/setup');
    setupData.value = response.data;
  } catch (err) {
    alert('Erro ao configurar 2FA');
  } finally {
    loading.value = false;
  }
};

const confirmSetup = async () => {
  loading.value = true;
  try {
    await api.post('/api/2fa/confirm', { code: confirmCode.value });
    alert('2FA ativado com sucesso!');
    setupData.value = null;
    await checkStatus();
  } catch (err) {
    alert('Código incorreto. Tente novamente.');
  } finally {
    loading.value = false;
  }
};

const cancelSetup = () => {
  setupData.value = null;
};

const disable2FA = async () => {
  loading.value = true;
  try {
    await api.post('/api/2fa/disable', disableForm.value);
    alert('2FA desativado.');
    await checkStatus();
  } catch (err) {
    alert('Erro ao desativar 2FA. Verifique senha e código.');
  } finally {
    loading.value = false;
  }
};

onMounted(() => {
  checkStatus();
});
</script>
