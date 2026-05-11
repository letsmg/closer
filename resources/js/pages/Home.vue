<template>
  <div class="min-h-screen bg-romance-50">
    <NavigationMenu />

    <main class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
      <div class="px-4 py-6 sm:px-0">
        <div class="bg-white rounded-lg shadow p-6">
          <h2 class="text-2xl font-bold text-romance-900 mb-4">
            Bem-vindo, {{ authStore.user?.name }}!
          </h2>
          <p class="text-gray-600">
            Você está autenticado com segurança usando JWT + OAuth2.
          </p>

          <div class="mt-6 space-y-4">
            <RouterLink
              to="/feed"
              class="inline-block bg-romance-500 hover:bg-romance-600 text-white px-6 py-3 rounded-md font-medium transition-colors"
            >
              Ver Feed
            </RouterLink>
            <RouterLink
              to="/matches"
              class="inline-block bg-romance-500 hover:bg-romance-600 text-white px-6 py-3 rounded-md font-medium transition-colors ml-4"
            >
              Meus Matches
            </RouterLink>
            <RouterLink
              to="/profile"
              class="inline-block bg-romance-400 hover:bg-romance-500 text-white px-6 py-3 rounded-md font-medium transition-colors ml-4"
            >
              Editar Perfil
            </RouterLink>
          </div>

          <div class="mt-6 grid grid-cols-1 gap-5 sm:grid-cols-3">
            <div class="bg-romance-50 overflow-hidden shadow rounded-lg">
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
                        {{ getLevelName(authStore.user?.nivel_acesso ?? authStore.user?.nivel) }}
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
import NavigationMenu from '../components/NavigationMenu.vue';

const router = useRouter();
const authStore = useAuthStore();

const getLevelName = (level) => {
  const levels = {
    0: 'Free',
    1: 'Moderador',
    2: 'Plus',
    3: 'Premium',
    4: 'Co-Founder',
    5: 'Elite',
    10: 'Administrador',
    11: 'Operacional',
    12: 'Suporte'
  };
  return levels[level] || 'Usuário';
};

const logout = async () => {
  await authStore.logout();
  router.push('/login');
};
</script>
