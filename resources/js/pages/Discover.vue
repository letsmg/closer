<template>
  <div class="min-h-screen bg-gray-100">
    <!-- Top Navigation -->
    <header class="bg-white shadow-sm">
      <div class="max-w-7xl mx-auto px-4 py-3 flex items-center justify-between">
        <div class="flex items-center gap-2">
          <img 
            src="/storage/logo.png" 
            alt="Closer" 
            class="h-8 w-auto"
            onerror="this.style.display='none'"
          />
        </div>
        <div class="flex items-center gap-3">
          <RouterLink to="/matches" class="text-gray-500 hover:text-indigo-600 transition-colors">
            <span class="text-xl">💬</span>
          </RouterLink>
          <RouterLink to="/profile" class="text-gray-500 hover:text-indigo-600 transition-colors">
            <span class="text-xl">👤</span>
          </RouterLink>
        </div>
      </div>
    </header>

    <!-- Loading -->
    <div v-if="loading" class="flex items-center justify-center min-h-[70vh]">
      <div class="text-center">
        <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-indigo-600 mx-auto mb-4" />
        <p class="text-gray-500">Carregando perfis...</p>
      </div>
    </div>

    <!-- Needs profile -->
    <div v-else-if="needsProfile" class="flex items-center justify-center min-h-[70vh]">
      <div class="text-center px-4">
        <span class="text-6xl block mb-4">👤</span>
        <h2 class="text-xl font-semibold text-gray-800 mb-2">Perfil incompleto</h2>
        <p class="text-gray-500 mb-6">Você precisa completar seu perfil antes de descobrir novas pessoas.</p>
        <button
          @click="router.push('/profile')"
          class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors"
        >
          Completar Perfil
        </button>
      </div>
    </div>

    <!-- Error message -->
    <div v-else-if="errorMessage" class="flex items-center justify-center min-h-[70vh]">
      <div class="text-center px-4">
        <span class="text-6xl block mb-4">⚠️</span>
        <h2 class="text-xl font-semibold text-gray-800 mb-2">Ops! Algo deu errado</h2>
        <p class="text-gray-500 mb-6">{{ errorMessage }}</p>
        <button
          @click="loadProfiles"
          class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors"
        >
          Tentar novamente
        </button>
      </div>
    </div>

    <!-- Empty state -->
    <div v-else-if="profiles.length === 0" class="flex items-center justify-center min-h-[70vh]">
      <div class="text-center px-4">
        <span class="text-6xl block mb-4">🔍</span>
        <h2 class="text-xl font-semibold text-gray-800 mb-2">Nenhum perfil encontrado</h2>
        <p class="text-gray-500 mb-6">No momento não há novos perfis para mostrar. Volte mais tarde!</p>
        <button
          @click="loadProfiles"
          class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors"
        >
          Atualizar
        </button>
      </div>
    </div>

    <!-- Swipe Cards -->
    <div v-else class="max-w-sm mx-auto mt-8 px-4">
      <section
        v-if="canUseLevelFilters"
        class="mb-4 rounded-lg border border-gray-200 bg-white p-4 shadow-sm"
      >
        <div class="flex items-center justify-between gap-3">
          <div>
            <h2 class="text-sm font-semibold text-gray-900">Filtros por nivel</h2>
            <p class="text-xs text-gray-500">Disponivel para Co-Founder e Elite.</p>
          </div>
          <button
            type="button"
            @click="saveLevelPreferences"
            :disabled="savingLevelPreferences"
            class="rounded-md bg-indigo-600 px-3 py-2 text-xs font-semibold text-white transition-colors hover:bg-indigo-700 disabled:opacity-50"
          >
            {{ savingLevelPreferences ? 'Salvando...' : 'Salvar' }}
          </button>
        </div>

        <div class="mt-4 grid grid-cols-1 gap-4">
          <fieldset>
            <legend class="mb-2 text-xs font-medium text-gray-700">Quero ver</legend>
            <div class="grid grid-cols-2 gap-2">
              <label
                v-for="level in customerLevelOptions"
                :key="`see-${level.value}`"
                class="flex items-center gap-2 rounded-md border border-gray-200 px-3 py-2 text-sm text-gray-700"
              >
                <input
                  v-model="levelFilters.discoverable_levels"
                  type="checkbox"
                  :value="level.value"
                  class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                />
                <span>{{ level.label }}</span>
              </label>
            </div>
          </fieldset>

          <fieldset>
            <legend class="mb-2 text-xs font-medium text-gray-700">Podem ver meu perfil</legend>
            <div class="grid grid-cols-2 gap-2">
              <label
                v-for="level in customerLevelOptions"
                :key="`visible-${level.value}`"
                class="flex items-center gap-2 rounded-md border border-gray-200 px-3 py-2 text-sm text-gray-700"
              >
                <input
                  v-model="levelFilters.visible_levels"
                  type="checkbox"
                  :value="level.value"
                  class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                />
                <span>{{ level.label }}</span>
              </label>
            </div>
          </fieldset>
        </div>
      </section>

      <div class="relative" style="min-height: 580px;">
      <!-- Card atual -->
      <div
        v-for="(profile, index) in visibleCards"
        :key="profile.id"
        class="absolute inset-0 transition-transform duration-300 cursor-grab active:cursor-grabbing"
        :style="{
          zIndex: visibleCards.length - index,
          transform: `scale(${1 - index * 0.02}) translateY(${index * 8}px)`,
          opacity: 1 - index * 0.1,
        }"
      >
        <SwipeProfileCard
          :profile="profile"
          :card-height="560"
        />
      </div>

      <!-- Match overlay -->
      <div
        v-if="showMatchOverlay"
        class="absolute inset-0 z-50 bg-gradient-to-b from-pink-500/90 to-purple-600/90 rounded-2xl flex items-center justify-center"
      >
        <div class="text-center text-white animate-bounce">
          <span class="text-6xl block mb-4">💕</span>
          <h2 class="text-3xl font-bold mb-2">It's a Match!</h2>
          <p class="text-white/80 mb-6">Você e {{ matchedUserName }} deram like um no outro</p>
          <button
            @click="showMatchOverlay = false"
            class="px-8 py-3 bg-white text-purple-600 rounded-full font-semibold hover:bg-purple-50 transition-colors"
          >
            Continuar
          </button>
        </div>
      </div>
      </div>
    </div>

    <!-- Action Buttons -->
    <div
      v-if="profiles.length > 0 && !loading"
      class="fixed bottom-8 left-1/2 -translate-x-1/2 flex items-center gap-6"
    >
      <!-- Dislike (X) -->
      <button
        @click="handleDislike"
        class="w-16 h-16 bg-white rounded-full shadow-lg flex items-center justify-center text-red-500 hover:shadow-xl hover:scale-110 transition-all"
        :disabled="isProcessing"
      >
        <span class="text-3xl">✕</span>
      </button>

      <!-- Super Like (Star) -->
      <button
        @click="handleLike"
        class="w-20 h-20 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-full shadow-lg flex items-center justify-center text-white hover:shadow-xl hover:scale-110 transition-all"
        :disabled="isProcessing"
      >
        <span class="text-4xl">⭐</span>
      </button>

      <!-- Like (Heart) -->
      <button
        @click="handleLike"
        class="w-16 h-16 bg-white rounded-full shadow-lg flex items-center justify-center text-green-500 hover:shadow-xl hover:scale-110 transition-all"
        :disabled="isProcessing"
      >
        <span class="text-3xl">❤</span>
      </button>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import api from '../api';
import { useAuthStore } from '../stores/auth';
import SwipeProfileCard from '../components/SwipeProfileCard.vue';

const router = useRouter();
const authStore = useAuthStore();

const profiles = ref([]);
const loading = ref(true);
const isProcessing = ref(false);
const currentIndex = ref(0);
const showMatchOverlay = ref(false);
const matchedUserName = ref('');
const errorMessage = ref('');
const needsProfile = ref(false);
const savingLevelPreferences = ref(false);
const levelFilters = ref({
  discoverable_levels: [],
  visible_levels: [],
});

const customerLevelOptions = [
  { value: 0, label: 'Free' },
  { value: 1, label: 'Moderador' },
  { value: 2, label: 'Plus' },
  { value: 3, label: 'Premium' },
  { value: 4, label: 'Co-Founder' },
  { value: 5, label: 'Elite' },
];

const visibleCards = computed(() => {
  return profiles.value.slice(currentIndex.value, currentIndex.value + 3);
});

const canUseLevelFilters = computed(() => {
  const level = Number(authStore.user?.nivel_acesso ?? authStore.user?.nivel ?? 0);
  return level >= 4 && level < 10;
});

onMounted(() => {
  loadLevelPreferences();
  loadProfiles();
});

const loadProfiles = async () => {
  loading.value = true;
  errorMessage.value = '';
  needsProfile.value = false;
  try {
    const response = await api.get('/discover');
    profiles.value = response.data.data || [];
    currentIndex.value = 0;
  } catch (err) {
    console.error('Erro ao carregar perfis:', err);
    if (err.response?.status === 422) {
      needsProfile.value = true;
      errorMessage.value = err.response?.data?.message || 'Complete seu perfil primeiro.';
    } else if (err.response?.status === 401) {
      errorMessage.value = 'Sessão expirada. Faça login novamente.';
    } else {
      errorMessage.value = err.response?.data?.message || 'Erro ao carregar perfis. Tente novamente.';
    }
  } finally {
    loading.value = false;
  }
};

const loadLevelPreferences = async () => {
  if (!canUseLevelFilters.value) return;

  try {
    const response = await api.get('/perfil');
    const preference = response.data?.preference || {};
    levelFilters.value = {
      discoverable_levels: normalizeLevels(preference.discoverable_levels),
      visible_levels: normalizeLevels(preference.visible_levels),
    };
  } catch (err) {
    console.error('Erro ao carregar filtros de nivel:', err);
  }
};

const saveLevelPreferences = async () => {
  if (!canUseLevelFilters.value) return;

  savingLevelPreferences.value = true;
  try {
    await api.put('/perfil', {
      preference: {
        discoverable_levels: normalizeLevels(levelFilters.value.discoverable_levels),
        visible_levels: normalizeLevels(levelFilters.value.visible_levels),
      },
    });
    await loadProfiles();
  } catch (err) {
    console.error('Erro ao salvar filtros de nivel:', err);
  } finally {
    savingLevelPreferences.value = false;
  }
};

const normalizeLevels = (levels) => {
  if (!Array.isArray(levels)) return [];

  return [...new Set(levels.map((level) => Number(level)))]
    .filter((level) => customerLevelOptions.some((option) => option.value === level))
    .sort((a, b) => a - b);
};

const handleLike = async () => {
  if (isProcessing.value || currentIndex.value >= profiles.value.length) return;
  isProcessing.value = true;

  const profile = profiles.value[currentIndex.value];
  try {
    const response = await api.post(`/discover/${profile.id}/like`);
    if (response.data.match) {
      matchedUserName.value = response.data.match.user.nickname || response.data.match.user.name;
      showMatchOverlay.value = true;
    }
    currentIndex.value++;
  } catch (err) {
    console.error('Erro ao dar like:', err);
  } finally {
    isProcessing.value = false;
  }
};

const handleDislike = async () => {
  if (isProcessing.value || currentIndex.value >= profiles.value.length) return;
  isProcessing.value = true;

  const profile = profiles.value[currentIndex.value];
  try {
    await api.post(`/discover/${profile.id}/dislike`);
    currentIndex.value++;
  } catch (err) {
    console.error('Erro ao dar dislike:', err);
  } finally {
    isProcessing.value = false;
  }
};
</script>
