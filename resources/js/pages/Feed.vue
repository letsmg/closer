<template>
  <AppLayout>
    <div class="px-4 pt-4">
      <h2 class="text-lg font-bold text-gray-900 mb-4">Mensagens</h2>

      <div v-if="conversas.length === 0" class="text-center py-20">
        <div class="w-20 h-20 rounded-full bg-purple-100 flex items-center justify-center mx-auto mb-4">
          <svg class="w-10 h-10 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
          </svg>
        </div>
        <h3 class="text-lg font-semibold text-gray-800 mb-1">Nenhuma conversa</h3>
        <p class="text-sm text-gray-500">Dê match com alguém para começar a conversar!</p>
      </div>

      <div v-else class="space-y-3">
        <RouterLink
          v-for="conv in conversas"
          :key="conv.id"
          :to="`/chat/${conv.user_id}`"
          class="flex items-center gap-4 bg-white rounded-2xl p-4 shadow-sm hover:shadow-md transition-shadow"
        >
          <div class="relative flex-shrink-0">
            <div class="w-14 h-14 rounded-full bg-gradient-to-br from-pink-400 to-purple-500 flex items-center justify-center text-white text-lg font-bold">
              {{ conv.name?.[0]?.toUpperCase() || '?' }}
            </div>
            <div v-if="conv.nao_lidas > 0" class="absolute -top-1 -right-1 w-5 h-5 bg-red-500 rounded-full flex items-center justify-center text-white text-[10px] font-bold">
              {{ conv.nao_lidas }}
            </div>
          </div>
          <div class="flex-1 min-w-0">
            <div class="flex items-center justify-between">
              <p class="text-sm font-semibold text-gray-900 truncate">{{ conv.name }}</p>
              <span class="text-[10px] text-gray-400">{{ conv.ultima_mensagem_hora }}</span>
            </div>
            <p class="text-xs text-gray-500 truncate mt-0.5">{{ conv.ultima_mensagem || 'Nenhuma mensagem ainda' }}</p>
          </div>
        </RouterLink>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';
import AppLayout from '../components/AppLayout.vue';

const conversas = ref([]);

onMounted(async () => {
  try {
    const res = await axios.get('/api/conversations');
    conversas.value = res.data.data || res.data || [];
  } catch (e) {
    console.error('Erro ao carregar conversas:', e);
  }
});
</script>
