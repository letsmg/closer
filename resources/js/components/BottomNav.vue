<template>
  <!-- Barra inferior estilo Tinder — fixa no bottom -->
  <nav class="fixed bottom-0 left-0 right-0 z-50 bg-white border-t border-gray-200 safe-area-bottom">
    <div class="flex items-center justify-around h-16 max-w-lg mx-auto">
      <!-- Discover (fogo) -->
      <RouterLink
        to="/discover"
        class="flex flex-col items-center justify-center w-16 h-full transition-colors"
        :class="isActive('/discover') ? 'text-pink-500' : 'text-gray-400 hover:text-gray-600'"
      >
        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
          <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
        </svg>
        <span class="text-[10px] mt-0.5 font-medium">Descobrir</span>
      </RouterLink>

      <!-- Matches (coração) -->
      <RouterLink
        to="/matches"
        class="flex flex-col items-center justify-center w-16 h-full transition-colors"
        :class="isActive('/matches') ? 'text-pink-500' : 'text-gray-400 hover:text-gray-600'"
      >
        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
          <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/>
        </svg>
        <span class="text-[10px] mt-0.5 font-medium">Matches</span>
      </RouterLink>

      <!-- Mensagens (balão) -->
      <RouterLink
        to="/feed"
        class="flex flex-col items-center justify-center w-16 h-full transition-colors"
        :class="isActive('/feed') ? 'text-pink-500' : 'text-gray-400 hover:text-gray-600'"
      >
        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
          <path d="M20 2H4c-1.1 0-2 .9-2 2v18l4-4h14c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zm0 14H6l-2 2V4h16v12z"/>
        </svg>
        <span class="text-[10px] mt-0.5 font-medium">Mensagens</span>
      </RouterLink>

      <!-- Perfil (pessoa) -->
      <RouterLink
        to="/profile"
        class="flex flex-col items-center justify-center w-16 h-full transition-colors"
        :class="isActive('/profile') ? 'text-pink-500' : 'text-gray-400 hover:text-gray-600'"
      >
        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
          <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
        </svg>
        <span class="text-[10px] mt-0.5 font-medium">Perfil</span>
      </RouterLink>
    </div>
  </nav>
</template>

<script setup>
import { useRoute } from 'vue-router';

const route = useRoute();

/** Verifica se a rota atual corresponde ao link informado */
function isActive(path) {
  if (path === '/discover') {
    return route.path === '/' || route.path === '/discover';
  }
  return route.path.startsWith(path);
}
</script>

<style scoped>
/* padding inferior seguro para iPhones com notch/home indicator */
.safe-area-bottom {
  padding-bottom: env(safe-area-inset-bottom, 0px);
}
</style>