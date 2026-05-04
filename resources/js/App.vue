<template>
  <div id="app" class="min-h-screen bg-gray-100">
    <RouterView />
  </div>
</template>

<script setup>
import { onMounted } from 'vue';
import { useAuthStore } from './stores/auth';

const authStore = useAuthStore();

onMounted(() => {
  // Check for stored token on app load
  authStore.checkAuth();
});
</script>
