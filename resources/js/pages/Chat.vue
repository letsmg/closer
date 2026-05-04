<template>
  <div class="min-h-screen bg-gray-100">
    <nav class="bg-white shadow">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
          <div class="flex items-center">
            <h1 class="text-xl font-bold text-gray-800">Chat</h1>
          </div>
          <div class="flex items-center space-x-4">
            <RouterLink to="/matches" class="text-gray-600 hover:text-gray-900">Voltar</RouterLink>
            <button @click="logout" class="text-red-600 hover:text-red-800">Sair</button>
          </div>
        </div>
      </div>
    </nav>

    <main class="max-w-4xl mx-auto py-6 sm:px-6 lg:px-8">
      <div class="bg-white rounded-lg shadow h-[600px] flex flex-col">
        <div class="p-4 border-b">
          <h2 class="text-lg font-bold text-gray-900">Conversa</h2>
        </div>
        <div class="flex-1 p-4 overflow-y-auto">
          <p class="text-gray-500 text-center">Área de chat (integrar com WebSocket/API)</p>
        </div>
        <div class="p-4 border-t">
          <div class="flex space-x-2">
            <input
              type="text"
              placeholder="Digite sua mensagem..."
              class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
            />
            <button class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">
              Enviar
            </button>
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
