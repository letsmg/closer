<template>
  <div class="min-h-screen bg-primary-50">
    <AdminNavigationMenu />
    
    <main class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
      <div class="px-4 py-6 sm:px-0">
        <div class="bg-white rounded-lg shadow p-6">
          <h1 class="text-2xl font-bold text-primary-900 mb-4">Dashboard Administrativo</h1>
          <p class="text-gray-600 mb-6">Bem-vindo à área de administração, {{ authStore.user?.name }}.</p>
          
          <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-primary-50 p-6 rounded-lg border border-primary-100">
              <h3 class="text-lg font-semibold text-primary-800">Usuários</h3>
              <p class="text-3xl font-bold text-primary-600 mt-2">--</p>
              <RouterLink to="/admin/users" class="text-sm text-primary-500 hover:text-primary-700 mt-4 block">Gerenciar usuários →</RouterLink>
            </div>
            
            <div class="bg-primary-50 p-6 rounded-lg border border-primary-100">
              <h3 class="text-lg font-semibold text-primary-800">Denúncias</h3>
              <p class="text-3xl font-bold text-primary-600 mt-2">--</p>
              <RouterLink to="/admin/denuncias" class="text-sm text-primary-500 hover:text-primary-700 mt-4 block">Ver denúncias →</RouterLink>
            </div>
            
            <div class="bg-primary-50 p-6 rounded-lg border border-primary-100">
              <h3 class="text-lg font-semibold text-primary-800">Status do Sistema</h3>
              <p class="text-green-600 font-medium mt-2">Operacional</p>
              <RouterLink to="/admin/logs" class="text-sm text-primary-500 hover:text-primary-700 mt-4 block">Ver logs →</RouterLink>
            </div>
          </div>
        </div>
      </div>
    </main>
  </div>
</template>

<script setup>
import { useAuthStore } from '../../stores/auth';
import AdminNavigationMenu from '../../components/AdminNavigationMenu.vue';

const authStore = useAuthStore();
</script>
