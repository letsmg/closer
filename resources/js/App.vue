<template>
  <div id="app" class="min-h-screen bg-gray-50">
    <TermsModal @accepted="onAccepted" />
    <RouterView />
  </div>
</template>

<script setup>
import { onMounted, ref } from 'vue';
import { useAuthStore } from './stores/auth';
import { useRouter } from 'vue-router';
import TermsModal from './components/TermsModal.vue';

const authStore = useAuthStore();
const router = useRouter();
const accepted = ref(false);
const TERMS_VERSION = '2026-05-05';
let inactivityTimeout = null;

const resetInactivityTimer = () => {
  if (inactivityTimeout) clearTimeout(inactivityTimeout);
  
  // Só ativa o timer se estiver autenticado
  if (authStore.isAuthenticated) {
    inactivityTimeout = setTimeout(() => {
      authStore.logout();
      router.push('/');
    }, 5 * 60 * 1000); // 5 minutos
  }
};

// Observa mudanças na autenticação para iniciar/parar o timer
import { watch } from 'vue';
watch(() => authStore.isAuthenticated, (val) => {
  if (val) {
    resetInactivityTimer();
  } else {
    if (inactivityTimeout) clearTimeout(inactivityTimeout);
  }
});

onMounted(async () => {
  // Check for stored token on app load
  await authStore.checkAuth();
  // Check if terms accepted
  const stored = localStorage.getItem('acceptedTerms');
  const storedVersion = localStorage.getItem('acceptedTermsVersion');
  accepted.value = stored === 'true' && storedVersion === TERMS_VERSION;

  // Listeners para inatividade
  window.addEventListener('mousemove', resetInactivityTimer);
  window.addEventListener('keydown', resetInactivityTimer);
  window.addEventListener('click', resetInactivityTimer);
  window.addEventListener('scroll', resetInactivityTimer);
  
  if (authStore.isAuthenticated) {
    resetInactivityTimer();
  }
});

// Limpeza ao destruir componente (embora App.vue seja raiz, é boa prática)
import { onUnmounted } from 'vue';
onUnmounted(() => {
  window.removeEventListener('mousemove', resetInactivityTimer);
  window.removeEventListener('keydown', resetInactivityTimer);
  window.removeEventListener('click', resetInactivityTimer);
  window.removeEventListener('scroll', resetInactivityTimer);
  if (inactivityTimeout) clearTimeout(inactivityTimeout);
});

const onAccepted = () => {
  accepted.value = true;
};
</script>
