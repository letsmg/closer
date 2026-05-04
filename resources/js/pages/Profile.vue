<template>
  <div class="min-h-screen bg-gray-100">
    <nav class="bg-white shadow">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
          <div class="flex items-center">
            <h1 class="text-xl font-bold text-gray-800">Perfil</h1>
          </div>
          <div class="flex items-center space-x-4">
            <RouterLink to="/" class="text-gray-600 hover:text-gray-900">Home</RouterLink>
            <RouterLink to="/2fa" class="text-indigo-600 hover:text-indigo-800">Segurança</RouterLink>
            <button @click="logout" class="text-red-600 hover:text-red-800">Sair</button>
          </div>
        </div>
      </div>
    </nav>

    <main class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
      <div class="px-4 py-6 sm:px-0">
        <div class="bg-white rounded-lg shadow p-6">
          <h2 class="text-2xl font-bold text-gray-900 mb-6">
            Seu Perfil
          </h2>
          
          <div v-if="authStore.user" class="space-y-4">
            <div>
              <label class="text-sm font-medium text-gray-500">Nome</label>
              <p class="text-lg text-gray-900">{{ authStore.user.name }}</p>
            </div>
            
            <div>
              <label class="text-sm font-medium text-gray-500">Email</label>
              <p class="text-lg text-gray-900">{{ authStore.user.email }}</p>
            </div>
            
            <div>
              <label class="text-sm font-medium text-gray-500">ID Único (UUID)</label>
              <p class="text-sm text-gray-900 font-mono">{{ authStore.user.uuid }}</p>
            </div>
            
            <div class="pt-4 border-t">
              <RouterLink
                to="/2fa"
                class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700"
              >
                🔒 Configurar 2FA
              </RouterLink>
            </div>
          </div>
        </div>
      </div>
    </main>
  </div>
</template>

<script setup>
import { useRouter } from 'vue-router';
import { useAuthStore } from '../stores/auth';

const router = useRouter();
const authStore = useAuthStore();

const logout = async () => {
  await authStore.logout();
  router.push('/login');
};
</script>
