<template>
  <AppLayout>
    <!-- Loading -->
    <div v-if="loading" class="flex items-center justify-center min-h-[70vh]">
      <div class="text-center">
        <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-pink-500 mx-auto mb-4" />
        <p class="text-gray-500">Carregando perfis...</p>
      </div>
    </div>

    <!-- Needs profile -->
    <div v-else-if="needsProfile" class="flex items-center justify-center min-h-[70vh]">
      <div class="text-center px-4">
        <div class="w-20 h-20 rounded-full bg-pink-100 flex items-center justify-center mx-auto mb-4">
          <svg class="w-10 h-10 text-pink-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
          </svg>
        </div>
        <h2 class="text-xl font-semibold text-gray-800 mb-2">Perfil incompleto</h2>
        <p class="text-gray-500 mb-6">Complete seu perfil antes de descobrir novas pessoas.</p>
        <button
          @click="router.push('/profile')"
          class="px-8 py-3 bg-gradient-to-r from-pink-500 to-purple-600 text-white rounded-xl font-semibold shadow-lg hover:shadow-xl transition-all"
        >
          Completar Perfil
        </button>
      </div>
    </div>

    <!-- Error message -->
    <div v-else-if="errorMessage" class="flex items-center justify-center min-h-[70vh] px-4">
      <div class="text-center">
        <div class="w-20 h-20 rounded-full bg-red-100 flex items-center justify-center mx-auto mb-4">
          <svg class="w-10 h-10 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/>
          </svg>
        </div>
        <h2 class="text-xl font-semibold text-gray-800 mb-2">Ops! Algo deu errado</h2>
        <p class="text-gray-500 mb-6">{{ errorMessage }}</p>
        <button
          @click="loadProfiles"
          class="px-8 py-3 bg-gradient-to-r from-pink-500 to-purple-600 text-white rounded-xl font-semibold shadow-lg hover:shadow-xl transition-all"
        >
          Tentar novamente
        </button>
      </div>
    </div>

    <!-- Empty state -->
    <div v-else-if="profiles.length === 0" class="flex items-center justify-center min-h-[70vh] px-4">
      <div class="text-center">
        <div class="w-20 h-20 rounded-full bg-yellow-100 flex items-center justify-center mx-auto mb-4">
          <svg class="w-10 h-10 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
          </svg>
        </div>
        <h2 class="text-xl font-semibold text-gray-800 mb-2">Nenhum perfil encontrado</h2>
        <p class="text-gray-500 mb-6">No momento não há novos perfis para mostrar.</p>
        <button
          @click="loadProfiles"
          class="px-8 py-3 bg-gradient-to-r from-pink-500 to-purple-600 text-white rounded-xl font-semibold shadow-lg hover:shadow-xl transition-all"
        >
          Atualizar
        </button>
      </div>
    </div>

    <!-- Swipe Cards -->
    <div v-else class="px-4 pt-4 pb-24">
      <!-- Filtros Co-Founder/Elite -->
      <section
        v-if="canUseLevelFilters"
        class="mb-4 rounded-2xl border border-gray-100 bg-white p-4 shadow-sm"
      >
        <div class="flex items-center justify-between gap-3">
          <div>
            <h2 class="text-sm font-semibold text-gray-900">Filtros por nível</h2>
            <p class="text-xs text-gray-500">Disponível para Co-Founder e Elite.</p>
          </div>
          <button
            type="button"
            @click="saveLevelPreferences"
            :disabled="savingLevelPreferences"
            class="rounded-full bg-pink-500 px-4 py-2 text-xs font-semibold text-white shadow hover:bg-pink-600 disabled:opacity-50 transition-all"
          >
            {{ savingLevelPreferences ? 'Salvando...' : 'Salvar' }}
          </button>
        </div>

        <div class="mt-4 grid grid-cols-2 gap-4">
          <fieldset>
            <legend class="mb-2 text-xs font-medium text-gray-700">Quero ver</legend>
            <div class="space-y-2">
              <label
                v-for="level in customerLevelOptions"
                :key="`see-${level.value}`"
                class="flex items-center gap-2 rounded-lg border border-gray-100 px-3 py-2 text-sm text-gray-700 hover:bg-pink-50"
              >
                <input
                  v-model="levelFilters.discoverable_levels"
                  type="checkbox"
                  :value="level.value"
                  class="h-4 w-4 rounded border-gray-300 text-pink-500 focus:ring-pink-400"
                />
                <span>{{ level.label }}</span>
              </label>
            </div>
          </fieldset>

          <fieldset>
            <legend class="mb-2 text-xs font-medium text-gray-700">Podem ver</legend>
            <div class="space-y-2">
              <label
                v-for="level in customerLevelOptions"
                :key="`visible-${level.value}`"
                class="flex items-center gap-2 rounded-lg border border-gray-100 px-3 py-2 text-sm text-gray-700 hover:bg-purple-50"
              >
                <input
                  v-model="levelFilters.visible_levels"
                  type="checkbox"
                  :value="level.value"
                  class="h-4 w-4 rounded border-gray-300 text-purple-500 focus:ring-purple-400"
                />
                <span>{{ level.label }}</span>
              </label>
            </div>
          </fieldset>
        </div>
      </section>

      <!-- Cards -->
      <div class="relative" style="min-height: 520px;">
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
            :card-height="520"
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

      <!-- Action Buttons -->
      <div class="flex items-center justify-center gap-6 mt-6">
        <button
          @click="handleDislike"
          class="w-14 h-14 bg-white rounded-full shadow-lg flex items-center justify-center text-red-400 hover:shadow-xl hover:scale-110 hover:text-red-500 transition-all"
          :disabled="isProcessing"
        >
          <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
          </svg>
        </button>

        <button
          @click="handleLike"
          class="w-20 h-20 bg-gradient-to-br from-pink-500 to-purple-600 rounded-full shadow-xl flex items-center justify-center text-white hover:shadow-2xl hover:scale-110 transition-all"
          :disabled="isProcessing"
        >
          <svg class="w-9 h-9" fill="currentColor" viewBox="0 0 24 24">
            <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/>
          </svg>
        </button>

        <button
          @click="handleLike"
          class="w-14 h-14 bg-white rounded-full shadow-lg flex items-center justify-center text-green-400 hover:shadow-xl hover:scale-110 hover:text-green-500 transition-all"
          :disabled="isProcessing"
        >
          <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 10h4.764a2 2 0 011.789 2.894l-3.5 7A2 2 0 0115.263 21h-4.017c-.163 0-.326-.02-.485-.06L7 20m7-10V5a2 2 0 00-2-2h-.095c-.5 0-.905.405-.905.905 0 .714-.211 1.412-.608 2.006L7 11v9m7-10h-2M7 20H5a2 2 0 01-2-2v-6a2 2 0 012-2h2.5"/>
          </svg>
        </button>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import api from '../api';
import { useAuthStore } from '../stores/auth';
import AppLayout from '../components/AppLayout.vue';
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
