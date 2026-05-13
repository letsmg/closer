<template>
  <div class="min-h-screen bg-gray-50 pb-16">
    <!-- Header simplificado para chat -->
    <div class="sticky top-0 z-40 bg-white border-b border-gray-100">
      <div class="flex items-center h-14 px-4 max-w-lg mx-auto">
        <button @click="router.back()" class="p-2 -ml-2 text-gray-500 hover:text-gray-700 rounded-full hover:bg-gray-100">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
          </svg>
        </button>
        <h1 class="text-lg font-bold text-gray-900 ml-2">Chat</h1>
      </div>
    </div>

    <main class="max-w-lg mx-auto p-4">
      <div class="bg-white rounded-2xl shadow-sm h-[580px] flex flex-col overflow-hidden">
        <div class="p-4 border-b border-gray-100">
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
