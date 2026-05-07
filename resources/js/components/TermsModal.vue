<template>
  <div v-if="!accepted" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4">
      <h2 class="text-2xl font-bold mb-4 text-center">Welcome to Closer</h2>
      <p class="mb-4 text-center">
        You must accept the Terms of Service to continue using the app.
      </p>
      <p class="mb-4 text-center">
        <a href="/terms.html" target="_blank" class="text-blue-500 underline">Read full Terms of Service</a>
      </p>
      <div class="mb-4">
        <label class="flex items-center justify-center">
          <input type="checkbox" v-model="agreed" class="mr-2">
          I have read and agree to the Terms of Service
        </label>
      </div>
      <div class="flex justify-center">
        <button @click="accept" :disabled="!agreed" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600 disabled:opacity-50">
          Accept and Continue
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, defineEmits } from 'vue';

const emit = defineEmits(['accepted']);

const TERMS_VERSION = '2026-05-05';
const accepted = ref(false);
const agreed = ref(false);

onMounted(() => {
  const stored = localStorage.getItem('acceptedTerms');
  const storedVersion = localStorage.getItem('acceptedTermsVersion');
  accepted.value = stored === 'true' && storedVersion === TERMS_VERSION;
});

const accept = () => {
  if (agreed.value) {
    localStorage.setItem('acceptedTerms', 'true');
    localStorage.setItem('acceptedTermsVersion', TERMS_VERSION);
    // Also set cookie for server-side middleware
    document.cookie = 'terms_accepted=true; path=/; max-age=31536000; SameSite=Lax';
    document.cookie = `terms_accepted_version=${TERMS_VERSION}; path=/; max-age=31536000; SameSite=Lax`;
    accepted.value = true;
    emit('accepted');
  }
};
</script>