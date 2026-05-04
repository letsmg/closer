<template>
  <div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-indigo-500 to-purple-600">
    <div class="bg-white p-8 rounded-xl shadow-2xl w-full max-w-md">
      <div class="text-center mb-8">
        <h1 class="text-3xl font-bold text-gray-800">Bem-vindo de volta</h1>
        <p class="text-gray-600 mt-2">Entre na sua conta para continuar</p>
      </div>

      <form @submit.prevent="handleLogin" class="space-y-6">
        <div>
          <label class="block text-sm font-medium text-gray-700">Email</label>
          <input
            v-model="form.email"
            type="email"
            required
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
            placeholder="seu@email.com"
          />
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700">Senha</label>
          <input
            v-model="form.password"
            type="password"
            required
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
            placeholder="••••••••"
          />
        </div>

        <div v-if="requires2FA" class="bg-yellow-50 border border-yellow-200 p-4 rounded-md">
          <label class="block text-sm font-medium text-yellow-800">Código 2FA</label>
          <input
            v-model="form.twoFactorCode"
            type="text"
            maxlength="6"
            class="mt-1 block w-full rounded-md border-yellow-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500"
            placeholder="000000"
          />
          <p class="text-xs text-yellow-600 mt-1">
            Abra seu app de autenticação e digite o código
          </p>
        </div>

        <div v-if="authStore.error" class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded">
          {{ authStore.error }}
        </div>

        <button
          type="submit"
          :disabled="authStore.loading"
          class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50"
        >
          <span v-if="authStore.loading">Entrando...</span>
          <span v-else>Entrar</span>
        </button>
      </form>

      <p class="mt-6 text-center text-sm text-gray-600">
        Não tem uma conta?
        <RouterLink to="/register" class="font-medium text-indigo-600 hover:text-indigo-500">
          Cadastre-se
        </RouterLink>
      </p>
    </div>
  </div>
</template>

<script setup>
import { reactive, ref } from 'vue';
import { useRouter } from 'vue-router';
import { useAuthStore } from '../stores/auth';

const router = useRouter();
const authStore = useAuthStore();

const form = reactive({
  email: '',
  password: '',
  twoFactorCode: '',
});

const requires2FA = ref(false);

const handleLogin = async () => {
  const result = await authStore.login({
    email: form.email,
    password: form.password,
  });
  
  if (result.success) {
    router.push('/');
  } else if (result.requires_2fa) {
    requires2FA.value = true;
  }
};
</script>
