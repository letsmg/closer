<template>
  <AppLayout>
    <div class="px-4 pt-4">
      <h2 class="text-lg font-bold text-gray-900 mb-4">Seus Matches</h2>

      <div v-if="matches.length === 0" class="text-center py-20">
        <div class="w-20 h-20 rounded-full bg-pink-100 flex items-center justify-center mx-auto mb-4">
          <svg class="w-10 h-10 text-pink-500" fill="currentColor" viewBox="0 0 24 24">
            <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/>
          </svg>
        </div>
        <h3 class="text-lg font-semibold text-gray-800 mb-1">Nenhum match ainda</h3>
        <p class="text-sm text-gray-500">Continue explorando perfis para encontrar seus matches!</p>
      </div>

      <div v-else class="space-y-3">
        <RouterLink
          v-for="match in matches"
          :key="match.id"
          :to="`/chat/${match.user.id}`"
          class="flex items-center gap-4 bg-white rounded-2xl p-4 shadow-sm hover:shadow-md transition-shadow"
        >
          <div class="w-14 h-14 rounded-full bg-gradient-to-br from-pink-400 to-purple-500 flex items-center justify-center text-white text-lg font-bold flex-shrink-0">
            {{ match.user?.name?.[0]?.toUpperCase() || '?' }}
          </div>
          <div class="flex-1 min-w-0">
            <p class="text-sm font-semibold text-gray-900 truncate">{{ match.user?.name }}</p>
            <p class="text-xs text-gray-500 truncate">{{ match.user?.email }}</p>
          </div>
          <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
          </svg>
        </RouterLink>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';
import AppLayout from '../components/AppLayout.vue';

const matches = ref([]);

onMounted(async () => {
  try {
    const res = await axios.get('/api/matches');
    matches.value = res.data.data || res.data || [];
  } catch (e) {
    console.error('Erro ao carregar matches:', e);
  }
});
</script>
