<template>
  <div
    class="relative w-full max-w-sm mx-auto rounded-2xl overflow-hidden shadow-2xl bg-white"
    :style="{ height: cardHeight + 'px' }"
  >
    <!-- Foto principal -->
    <div class="absolute inset-0">
      <img
        v-if="profile.primary_photo_url || primaryPhoto"
        :src="profile.primary_photo_url || primaryPhoto?.full_url"
        :alt="profile.nickname"
        class="w-full h-full object-cover"
      />
      <div v-else class="w-full h-full bg-gradient-to-br from-indigo-400 to-purple-500 flex items-center justify-center">
        <span class="text-6xl text-white/70">👤</span>
      </div>
    </div>

    <!-- Gradient overlay -->
    <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-transparent to-transparent" />

    <!-- Verified Badge -->
    <div v-if="profile.is_verified" class="absolute top-3 right-3 z-10">
      <span class="bg-blue-500 text-white text-xs px-2 py-1 rounded-full flex items-center gap-1 shadow-lg">
        <span>✓</span>
        <span>Verified</span>
      </span>
    </div>

    <!-- Info do perfil -->
    <div class="absolute bottom-0 left-0 right-0 p-5 text-white">
      <div class="flex items-center gap-2 mb-1">
        <h2 class="text-2xl font-bold">{{ profile.nickname || profile.user?.name }}</h2>
        <span class="text-xl" v-if="profile.age">, {{ profile.age }}</span>
        <span v-if="profile.is_verified" class="text-blue-300 text-sm" title="Verified Profile">✓</span>
      </div>
      <!-- Nível do usuário -->
      <div v-if="profile.user?.nivel_acesso !== undefined" class="mb-1">
        <span 
          class="text-xs px-2 py-0.5 rounded-full font-medium"
          :class="getLevelBadgeClass(profile.user.nivel_acesso)"
        >
          {{ getLevelName(profile.user.nivel_acesso) }}
        </span>
      </div>
      <p v-if="profile.profession" class="text-sm text-white/80">{{ profile.profession }}</p>
      <div v-if="profile.city" class="flex items-center gap-1 text-sm text-white/70 mt-1">
        <span>📍</span>
        <span>{{ profile.city.name }}{{ profile.city.state ? ', ' + profile.city.state.name : '' }}</span>
      </div>
      <p v-if="profile.biography" class="mt-2 text-sm text-white/90 line-clamp-2">{{ profile.biography }}</p>
    </div>

    <!-- Ícones de interesse/informação -->
    <div class="absolute top-3 left-3 flex flex-wrap gap-1">
      <span
        v-for="hobby in profile.hobbies"
        :key="hobby.id"
        class="px-2 py-0.5 bg-white/20 backdrop-blur-sm rounded-full text-xs text-white"
      >
        {{ hobby.name }}
      </span>
    </div>
  </div>
</template>

<script setup>
const props = defineProps({
  profile: {
    type: Object,
    required: true,
  },
  cardHeight: {
    type: Number,
    default: 560,
  },
});

const emit = defineEmits(['like', 'dislike', 'superlike']);

// User level names
const levelNames = {
  0: 'Free',
  1: 'Moderador',
  2: 'Plus',
  3: 'Premium',
  4: 'Co-Founder',
  5: 'Elite',
  10: 'Admin',
  11: 'Operacional',
  12: 'Suporte',
};

// Get level name
const getLevelName = (level) => {
  return levelNames[level] || 'Free';
};

// Get badge class based on level
const getLevelBadgeClass = (level) => {
  const classes = {
    0: 'bg-gray-500/80 text-white',
    1: 'bg-teal-500/80 text-white',
    2: 'bg-blue-500/80 text-white',
    3: 'bg-yellow-500/80 text-white',
    4: 'bg-purple-500/80 text-white',
    5: 'bg-black/80 text-white border border-white/30',
    10: 'bg-red-500/80 text-white',
    11: 'bg-orange-500/80 text-white',
    12: 'bg-green-500/80 text-white',
  };
  return classes[level] || 'bg-gray-500/80 text-white';
};
</script>
