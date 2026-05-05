<template>
  <div v-if="!accepted" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4">
      <h2 class="text-2xl font-bold mb-4 text-center">Bem-vindo ao Closer</h2>
      <p class="mb-4 text-center">
        Você precisa aceitar os termos de uso para continuar usando o aplicativo.
      </p>
      <p class="mb-4 text-center">
        <a href="/terms.html" target="_blank" class="text-blue-500 underline">Ver termos de uso completos</a>
      </p>
      <div class="mb-4">
        <label class="flex items-center justify-center">
          <input type="checkbox" v-model="agreed" class="mr-2">
          Li e aceito os termos de uso
        </label>
      </div>
      <div class="flex justify-center">
        <button @click="accept" :disabled="!agreed" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600 disabled:opacity-50">
          Aceitar e Continuar
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, defineEmits } from 'vue';

const emit = defineEmits(['accepted']);

const accepted = ref(false);
const agreed = ref(false);

onMounted(() => {
  const stored = localStorage.getItem('acceptedTerms');
  accepted.value = stored === 'true';
});

const accept = () => {
  if (agreed.value) {
    localStorage.setItem('acceptedTerms', 'true');
    // Also set cookie for server-side middleware
    document.cookie = 'terms_accepted=true; path=/; max-age=31536000; SameSite=Lax';
    accepted.value = true;
    emit('accepted');
  }
};
</script>