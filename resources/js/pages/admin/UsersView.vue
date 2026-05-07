<template>
  <div class="min-h-screen bg-primary-50">
    <AdminNavigationMenu />
    
    <main class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
      <div class="px-4 py-6 sm:px-0">
        <div class="bg-white rounded-lg shadow overflow-hidden">
          <div class="px-6 py-4 border-b border-primary-100 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
              <h1 class="text-2xl font-bold text-primary-900">Gerenciamento de Usuários</h1>
              <p class="mt-1 text-sm text-gray-600">
                Visualize e gerencie todos os usuários do sistema
              </p>
            </div>
            
            <div class="flex flex-wrap gap-2">
              <button 
                @click="setFilter('')" 
                :class="[filter === '' ? 'bg-primary-600 text-white' : 'bg-white text-primary-600 border border-primary-200']"
                class="px-4 py-2 rounded-md text-sm font-medium transition-colors shadow-sm"
              >
                Todos
              </button>
              <button 
                @click="setFilter('staff')" 
                :class="[filter === 'staff' ? 'bg-primary-600 text-white' : 'bg-white text-primary-600 border border-primary-200']"
                class="px-4 py-2 rounded-md text-sm font-medium transition-colors shadow-sm"
              >
                Staff
              </button>
              <button 
                @click="setFilter('regular')" 
                :class="[filter === 'regular' ? 'bg-primary-600 text-white' : 'bg-white text-primary-600 border border-primary-200']"
                class="px-4 py-2 rounded-md text-sm font-medium transition-colors shadow-sm"
              >
                Comuns
              </button>
              <button 
                @click="setFilter('reported')" 
                :class="[filter === 'reported' ? 'bg-red-600 text-white' : 'bg-white text-red-600 border border-red-200']"
                class="px-4 py-2 rounded-md text-sm font-medium transition-colors shadow-sm"
              >
                Com Denúncias
              </button>
            </div>
          </div>

          <!-- Barra de Busca -->
          <div class="px-6 py-4 bg-primary-50/50 border-b border-primary-100">
            <div class="max-w-md relative">
              <input
                v-model="search"
                @input="handleSearch"
                type="text"
                placeholder="Buscar por nome ou email..."
                class="w-full pl-10 pr-4 py-2 rounded-lg border border-primary-200 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent"
              />
              <div class="absolute left-3 top-2.5 text-primary-400">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
              </div>
            </div>
          </div>

          <!-- Tabela -->
          <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-primary-100">
              <thead class="bg-primary-50">
                <tr>
                  <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-primary-700 uppercase tracking-wider">Usuário</th>
                  <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-primary-700 uppercase tracking-wider">Nível</th>
                  <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-primary-700 uppercase tracking-wider">Status</th>
                  <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-primary-700 uppercase tracking-wider">Denúncias</th>
                  <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-primary-700 uppercase tracking-wider">Cadastro</th>
                  <th scope="col" class="relative px-6 py-3">
                    <span class="sr-only">Ações</span>
                  </th>
                </tr>
              </thead>
              <tbody class="bg-white divide-y divide-primary-50">
                <tr v-if="loading" v-for="i in 5" :key="i" class="animate-pulse">
                  <td class="px-6 py-4 whitespace-nowrap"><div class="h-10 w-10 bg-gray-200 rounded-full"></div></td>
                  <td colspan="5" class="px-6 py-4"><div class="h-4 bg-gray-200 rounded w-3/4"></div></td>
                </tr>
                <tr v-else-if="!users || users.length === 0">
                  <td colspan="6" class="px-6 py-10 text-center text-gray-500">
                    Nenhum usuário encontrado.
                  </td>
                </tr>
                <tr v-for="user in users" :key="user.id" class="hover:bg-primary-50/30 transition-colors">
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="flex items-center">
                      <div class="flex-shrink-0 h-10 w-10">
                        <img 
                          v-if="user.main_photo_url" 
                          :src="user.main_photo_url" 
                          class="h-10 w-10 rounded-full object-cover border border-primary-100"
                        />
                        <div v-else class="h-10 w-10 rounded-full bg-primary-100 flex items-center justify-center text-primary-600 font-bold">
                          {{ user.name.charAt(0).toUpperCase() }}
                        </div>
                      </div>
                      <div class="ml-4">
                        <div class="text-sm font-medium text-gray-900">{{ user.name }}</div>
                        <div class="text-sm text-gray-500">{{ user.email }}</div>
                      </div>
                    </div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <span 
                      :class="[
                        user.nivel_acesso >= 3 ? 'bg-wine-100 text-wine-800' : 'bg-primary-100 text-primary-800'
                      ]"
                      class="px-2.5 py-0.5 rounded-full text-xs font-medium"
                    >
                      {{ getLevelName(user.nivel_acesso) }}
                    </span>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <span 
                      :class="[
                        user.ativo ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'
                      ]"
                      class="px-2.5 py-0.5 rounded-full text-xs font-medium"
                    >
                      {{ user.ativo ? 'Ativo' : 'Inativo' }}
                    </span>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <span 
                      v-if="user.reports_count > 0"
                      class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-red-600 text-white"
                    >
                      {{ user.reports_count }}
                    </span>
                    <span v-else class="text-gray-400 text-sm">-</span>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    {{ formatDate(user.created_at) }}
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                    <!-- Ações visíveis apenas para Admin Nível 3+ -->
                    <template v-if="canManage">
                      <button @click="editUser(user)" class="text-primary-600 hover:text-primary-900 mr-3">Editar</button>
                      
                      <button @click="resetPassword(user)" class="text-amber-600 hover:text-amber-900 mr-3">Resetar Senha</button>
                      
                      <template v-if="user.id !== authStore.user?.id">
                        <button @click="toggleUserStatus(user)" :class="user.ativo ? 'text-red-600 hover:text-red-900' : 'text-green-600 hover:text-green-900'">
                          {{ user.ativo ? 'Desativar' : 'Ativar' }}
                        </button>
                      </template>
                      <template v-else>
                        <span class="text-gray-400 text-xs">(Você)</span>
                      </template>
                    </template>
                    <template v-else>
                      <span class="text-gray-400 text-xs">Somente Visualização</span>
                    </template>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

          <!-- Paginação -->
          <div class="bg-white px-4 py-3 border-t border-primary-100 flex items-center justify-between sm:px-6">
            <div class="flex-1 flex justify-between sm:hidden">
              <button @click="prevPage" :disabled="pagination.current_page === 1" class="relative inline-flex items-center px-4 py-2 border border-primary-300 text-sm font-medium rounded-md text-primary-700 bg-white hover:bg-primary-50">Anterior</button>
              <button @click="nextPage" :disabled="pagination.current_page === pagination.last_page" class="ml-3 relative inline-flex items-center px-4 py-2 border border-primary-300 text-sm font-medium rounded-md text-primary-700 bg-white hover:bg-primary-50">Próximo</button>
            </div>
            <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
              <div>
                <p class="text-sm text-gray-700">
                  Mostrando <span class="font-medium">{{ pagination.from }}</span> até <span class="font-medium">{{ pagination.to }}</span> de <span class="font-medium">{{ pagination.total }}</span> usuários
                </p>
              </div>
              <div>
                <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                  <button @click="prevPage" :disabled="pagination.current_page === 1" class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-primary-300 bg-white text-sm font-medium text-primary-500 hover:bg-primary-50">
                    <span class="sr-only">Anterior</span>
                    <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" /></svg>
                  </button>
                  <button @click="nextPage" :disabled="pagination.current_page === pagination.last_page" class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-primary-300 bg-white text-sm font-medium text-primary-500 hover:bg-primary-50">
                    <span class="sr-only">Próximo</span>
                    <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" /></svg>
                  </button>
                </nav>
              </div>
            </div>
          </div>
        </div>
      </div>
    </main>
  </div>
</template>

<script setup>
import { ref, onMounted, watch, computed } from 'vue';
import AdminNavigationMenu from '../../components/AdminNavigationMenu.vue';
import { useAuthStore } from '../../stores/auth';
import api from '../../api';

const authStore = useAuthStore();
const canManage = computed(() => authStore.isStaffLevel);

// Custom debounce implementation to avoid dependency issues
const debounce = (fn, delay) => {
  let timeoutId;
  return (...args) => {
    if (timeoutId) clearTimeout(timeoutId);
    timeoutId = setTimeout(() => fn(...args), delay);
  };
};

const users = ref([]);
const loading = ref(false);
const filter = ref('');
const search = ref('');
const pagination = ref({
  current_page: 1,
  last_page: 1,
  total: 0,
  from: 0,
  to: 0
});

const fetchUsers = async (page = 1) => {
  loading.value = true;
  try {
    const response = await api.get('users', {
      params: {
        page,
        filter: filter.value,
        search: search.value
      }
    });
    
    // Suporte para resposta paginada ou array simples
    if (response.data && response.data.data) {
      users.value = response.data.data;
      pagination.value = {
        current_page: response.data.current_page || 1,
        last_page: response.data.last_page || 1,
        total: response.data.total || 0,
        from: response.data.from || 0,
        to: response.data.to || 0
      };
    } else if (Array.isArray(response.data)) {
      users.value = response.data;
    } else {
      users.value = [];
    }
  } catch (error) {
    console.error('Erro ao buscar usuários:', error);
  } finally {
    loading.value = false;
  }
};

const setFilter = (newFilter) => {
  filter.value = newFilter;
  pagination.value.current_page = 1;
  fetchUsers(1);
};

const handleSearch = debounce(() => {
  pagination.value.current_page = 1;
  fetchUsers(1);
}, 500);

const prevPage = () => {
  if (pagination.value.current_page > 1) {
    fetchUsers(pagination.value.current_page - 1);
  }
};

const nextPage = () => {
  if (pagination.value.current_page < pagination.value.last_page) {
    fetchUsers(pagination.value.current_page + 1);
  }
};

const getLevelName = (level) => {
  const levels = {
    0: 'Free',
    1: 'Plus',
    2: 'Premium',
    3: 'Admin',
    4: 'Operacional',
    5: 'Suporte'
  };
  return levels[level] || 'Usuário';
};

const formatDate = (dateString) => {
  return new Date(dateString).toLocaleDateString('pt-BR');
};

const editUser = (user) => {
  // Redireciona para página de edição usando UUID
  window.location.href = `/admin/usuarios/editar/${user.uuid}`;
};

const resetPassword = async (user) => {
  if (!confirm(`Deseja realmente resetar a senha do usuário ${user.name}? Uma nova senha temporária será enviada.`)) return;
  
  try {
    await api.post(`/users/${user.uuid}/reset-password`);
    alert('Senha resetada com sucesso! O usuário receberá as instruções por e-mail.');
  } catch (error) {
    alert('Erro ao resetar senha. Verifique se o endpoint existe.');
  }
};

const toggleUserStatus = async (user) => {
  const action = user.ativo ? 'desativar' : 'ativar';
  if (!confirm(`Tem certeza que deseja ${action} o usuário ${user.name}?`)) return;

  try {
    await api.put(`/users/${user.uuid}`, {
      ativo: !user.ativo
    });
    user.ativo = !user.ativo;
  } catch (error) {
    alert('Erro ao alterar status do usuário');
  }
};

onMounted(() => {
  fetchUsers();
});
</script>

<style scoped>
.bg-wine-100 { background-color: #fce7f3; }
.text-wine-800 { color: #831843; }
</style>
