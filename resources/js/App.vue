<template>
  <div id="app" class="min-h-screen bg-gray-100">
    <TermsModal @accepted="onAccepted" />
    <div v-if="accepted">
      <RouterView />
    </div>
  </div>
</template>

<script setup>
import { onMounted, ref } from 'vue';
import { useAuthStore } from './stores/auth';
import TermsModal from './components/TermsModal.vue';

const authStore = useAuthStore();
const accepted = ref(false);
const TERMS_VERSION = '2026-05-05';

onMounted(() => {
  // Check for stored token on app load
  authStore.checkAuth();
  // Check if terms accepted
  const stored = localStorage.getItem('acceptedTerms');
  const storedVersion = localStorage.getItem('acceptedTermsVersion');
  accepted.value = stored === 'true' && storedVersion === TERMS_VERSION;
});

const onAccepted = () => {
  accepted.value = true;
};
</script>
