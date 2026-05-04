<template>
  <div class="min-h-screen bg-gray-100">
    <nav class="bg-white shadow">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
          <div class="flex items-center">
            <h1 class="text-xl font-bold text-gray-800">Closer</h1>
          </div>
          <div class="flex items-center space-x-4">
            <RouterLink to="/feed" class="text-gray-600 hover:text-gray-900">Feed</RouterLink>
            <RouterLink to="/matches" class="text-gray-600 hover:text-gray-900">Matches</RouterLink>
            <RouterLink to="/profile" class="text-gray-600 hover:text-gray-900">Perfil</RouterLink>
            <button
              @click="logout"
              class="text-red-600 hover:text-red-800"
            >
              Sair
            </button>
          </div>
        </div>
      </div>
    </nav>

    <main class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
      <div class="px-4 py-6 sm:px-0">
        <div class="bg-white rounded-lg shadow p-6">
          <h2 class="text-2xl font-bold text-gray-900 mb-4">
            Bem-vindo, {{ authStore.user?.name }}!
          </h2>
          <p class="text-gray-600">
            Você está autenticado com segurança usando JWT + OAuth2.
          </p>
          
          <div class="mt-6 grid grid-cols-1 gap-5 sm:grid-cols-3">
            <div class="bg-indigo-50 overflow-hidden shadow rounded-lg">
              <div class="p-5">
                <div class="flex items-center">
                  <div class="flex-shrink-0">
                    <span class="text-2xl">🔒</span>
                  </div>
                  <div class="ml-5 w-0 flex-1">
                    <dl>
                      <dt class="text-sm font-medium text-gray-500 truncate">
                        Segurança
                      </dt>
                      <dd class="text-sm text-gray-900">
                        {{ authStore.user?.two_factor_enabled ? '2FA Ativado' : '2FA Desativado' }}
                      </dd>
                    </dl>
                  </div>
                </div>
              </div>
            </div>

            <div class="bg-purple-50 overflow-hidden shadow rounded-lg">
              <div class="p-5">
                <div class="flex items-center">
                  <div class="flex-shrink-0">
                    <span class="text-2xl">🔑</span>
                  </div>
                  <div class="ml-5 w-0 flex-1">
                    <dl>
                      <dt class="text-sm font-medium text-gray-500 truncate">
                        ID Único
                      </dt>
                      <dd class="text-sm text-gray-900 truncate">
                        {{ authStore.user?.uuid }}
                      </dd>
                    </dl>
                  </div>
                </div>
              </div>
            </div>

            <div class="bg-pink-50 overflow-hidden shadow rounded-lg">
              <div class="p-5">
                <div class="flex items-center">
                  <div class="flex-shrink-0">
                    <span class="text-2xl">⭐</span>
                  </div>
                  <div class="ml-5 w-0 flex-1">
                    <dl>
                      <dt class="text-sm font-medium text-gray-500 truncate">
                        Nível
                      </dt>
                      <dd class="text-sm text-gray-900">
                        {{ authStore.user?.nivel === 3 ? 'Admin' : 'Usuário' }}
                      </dd>
                    </dl>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </main>
  </div>
</template>

<script setup>
import { useRouter } from 'vue-router';
import { useAuthStore } from '../stores/auth';

const router = useRouter();
const authStore = useAuthStore();

const logout = async () => {
  await authStore.logout();
  router.push('/login');
};
</script>
