<template>
  <div class="min-h-screen bg-gray-50 pb-16">
    <!-- Header estilo Tinder com logo -->
    <header class="sticky top-0 z-40 bg-white border-b border-gray-100">
      <div class="flex items-center justify-between h-14 px-4 max-w-lg mx-auto">
        <h1 class="text-2xl font-bold bg-gradient-to-r from-pink-500 via-red-400 to-yellow-500 bg-clip-text text-transparent">
          closer
        </h1>

        <!-- Botões do header -->
        <div class="flex items-center gap-2">
          <!-- Filtros (apenas no discover) -->
          <button
            v-if="showFilterBtn"
            @click="$emit('toggle-filter')"
            class="p-2 text-gray-500 hover:text-pink-500 rounded-full hover:bg-pink-50 transition-colors"
          >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
            </svg>
          </button>

          <!-- Sair -->
          <button
            @click="handleLogout"
            class="p-2 text-gray-500 hover:text-red-500 rounded-full hover:bg-red-50 transition-colors"
            title="Sair"
          >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
            </svg>
          </button>
        </div>
      </div>
    </header>

    <!-- Conteúdo da página -->
    <main class="max-w-lg mx-auto">
      <slot />
    </main>

    <!-- Bottom Navigation -->
    <BottomNav />
  </div>
</template>

<script setup>
import { computed } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { useAuthStore } from '../stores/auth';
import BottomNav from './BottomNav.vue';

const emit = defineEmits(['toggle-filter']);
const route = useRoute();
const router = useRouter();
const authStore = useAuthStore();

// Mostra botão de filtro apenas na página de discover
const showFilterBtn = computed(() => {
  return route.path === '/' || route.path === '/discover';
});

const handleLogout = async () => {
  try {
    await authStore.logout();
    router.push('/login');
  } catch (error) {
    console.error('Erro ao fazer logout:', error);
  }
};
</script>