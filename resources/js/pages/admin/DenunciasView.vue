<template>
  <div class="min-h-screen bg-primary-50">
    <AdminNavigationMenu />
    
    <main class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
      <div class="px-4 py-6 sm:px-0">
        <div class="bg-white rounded-lg shadow overflow-hidden">
          <div class="px-6 py-4 border-b border-primary-100 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
              <h1 class="text-2xl font-bold text-primary-900">Gerenciamento de Denúncias</h1>
              <p class="mt-1 text-sm text-gray-600">
                Analise e resolva as denúncias enviadas pelos usuários.
              </p>
            </div>

          <!-- Filtros -->
          <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex flex-col sm:flex-row gap-4">
              <select
                v-model="filters.status"
                class="rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm"
              >
                <option value="">Todos os status</option>
                <option value="pending">Pendentes</option>
                <option value="analyzed">Analisadas</option>
                <option value="resolved">Resolvidas</option>
              </select>
              
              <select
                v-model="filters.motivo"
                class="rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm"
              >
                <option value="">Todos os motivos</option>
                <option value="harassment">Importunação</option>
                <option value="disrespect">Desrespeito</option>
                <option value="fake_profile">Perfil Falso</option>
                <option value="other">Outro</option>
              </select>

              <button
                @click="loadDenuncias"
                class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-colors"
              >
                Filtrar
              </button>
            </div>
          </div>

          <!-- Lista de Denúncias -->
          <div class="overflow-hidden">
            <div v-if="loading" class="px-6 py-8 text-center">
              <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-primary-600"></div>
              <p class="mt-2 text-gray-600 font-medium">Carregando denúncias...</p>
            </div>

            <div v-else-if="denuncias.length === 0" class="px-6 py-8 text-center">
              <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
              <h3 class="mt-2 text-sm font-medium text-gray-900">Nenhuma denúncia encontrada</h3>
              <p class="mt-1 text-sm text-gray-500">Nenhuma denúncia corresponde aos filtros selecionados.</p>
            </div>

            <div v-else class="divide-y divide-gray-200">
              <div
                v-for="denuncia in denuncias"
                :key="denuncia.id"
                class="px-6 py-4 hover:bg-gray-50"
              >
                <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between">
                  <div class="flex-1 min-w-0">
                    <div class="flex items-center space-x-3">
                      <div class="flex-shrink-0">
                        <div class="h-10 w-10 rounded-full bg-red-100 flex items-center justify-center">
                          <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
                          </svg>
                        </div>
                      </div>
                      <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-900 truncate">
                          Denúncia #{{ denuncia.id }}
                        </p>
                        <p class="text-sm text-gray-500">
                          Por: {{ denuncia.denunciante?.name }} → Contra: {{ denuncia.denunciado?.name }}
                        </p>
                      </div>
                    </div>
                    
                    <div class="mt-2 flex flex-wrap gap-2">
                      <span
                        :class="getStatusClass(denuncia.status)"
                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                      >
                        {{ getStatusText(denuncia.status) }}
                      </span>
                      <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                        {{ getMotivoText(denuncia.motivo) }}
                      </span>
                      <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                        {{ formatDate(denuncia.created_at) }}
                      </span>
                    </div>
                  </div>

                  <div class="mt-4 lg:mt-0 lg:ml-4 flex flex-col sm:flex-row gap-2">
                    <button
                      @click="viewDenuncia(denuncia)"
                      class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-xs font-bold rounded-lg text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-all hover:shadow-md"
                    >
                      Visualizar Detalhes
                    </button>
                    
                    <button
                      v-if="denuncia.status !== 'analyzed'"
                      @click="markAsAnalyzed(denuncia)"
                      class="inline-flex items-center px-4 py-2 border border-primary-200 shadow-sm text-xs font-bold rounded-lg text-primary-700 bg-primary-50 hover:bg-primary-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-all"
                    >
                      Marcar como Analisada
                    </button>
                    
                    <button
                      v-if="denuncia.status !== 'resolved'"
                      @click="markAsResolved(denuncia)"
                      class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-xs font-bold rounded-lg text-white bg-success-600 hover:bg-success-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-success-500 transition-all hover:shadow-md"
                    >
                      Marcar como Resolvida
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Paginação -->
          <div v-if="denuncias.length > 0" class="px-6 py-4 border-t border-gray-200">
            <div class="flex items-center justify-between">
              <div class="flex-1 flex justify-between sm:hidden">
                <button
                  @click="previousPage"
                  :disabled="currentPage === 1"
                  class="relative inline-flex items-center px-4 py-2 border border-primary-200 text-sm font-bold rounded-lg text-primary-700 bg-primary-50 hover:bg-primary-100 disabled:opacity-50 disabled:cursor-not-allowed transition-all"
                >
                  Anterior
                </button>
                <button
                  @click="nextPage"
                  :disabled="!hasNextPage"
                  class="ml-3 relative inline-flex items-center px-4 py-2 border border-primary-200 text-sm font-bold rounded-lg text-primary-700 bg-primary-50 hover:bg-primary-100 disabled:opacity-50 disabled:cursor-not-allowed transition-all"
                >
                  Próximo
                </button>
              </div>
              <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                <div>
                  <p class="text-sm text-gray-700">
                    Mostrando <span class="font-medium">{{ (currentPage - 1) * 15 + 1 }}</span> a
                    <span class="font-medium">{{ Math.min(currentPage * 15, total) }}</span> de
                    <span class="font-medium">{{ total }}</span> resultados
                  </p>
                </div>
                <div>
                  <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px">
                    <button
                      @click="previousPage"
                      :disabled="currentPage === 1"
                      class="relative inline-flex items-center px-2 py-2 rounded-l-lg border border-primary-200 bg-primary-50 text-sm font-bold text-primary-700 hover:bg-primary-100 disabled:opacity-50 transition-all"
                    >
                      Anterior
                    </button>
                    <button
                      @click="nextPage"
                      :disabled="!hasNextPage"
                      class="relative inline-flex items-center px-2 py-2 rounded-r-lg border border-primary-200 bg-primary-50 text-sm font-bold text-primary-700 hover:bg-primary-100 disabled:opacity-50 transition-all"
                    >
                      Próximo
                    </button>
                  </nav>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </main>

    <!-- Modal de Visualização -->
    <div v-if="showModal" class="fixed inset-0 z-50 overflow-y-auto">
      <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <!-- Overlay mais moderno -->
        <div class="fixed inset-0 bg-gray-900/50 transition-opacity" @click="closeModal"></div>

        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

        <div class="inline-block align-bottom bg-white rounded-xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-xl sm:w-full border border-primary-100">
          <!-- Cabeçalho do Modal -->
          <div class="bg-primary-50 px-6 py-4 border-b border-primary-100 flex items-center justify-between">
            <h3 class="text-xl font-bold text-primary-900">
              Detalhes da Denúncia #{{ selectedDenuncia?.id }}
            </h3>
            <button @click="closeModal" class="text-gray-400 hover:text-gray-600 transition-colors">
              <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
              </svg>
            </button>
          </div>

          <!-- Conteúdo do Modal -->
          <div class="bg-white px-6 py-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <div class="space-y-4">
                <div>
                  <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider">Denunciante</label>
                  <p class="mt-1 text-sm font-semibold text-gray-900">{{ selectedDenuncia?.denunciante?.name }}</p>
                  <p class="text-xs text-gray-500">{{ selectedDenuncia?.denunciante?.email }}</p>
                </div>
                
                <div>
                  <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider">Denunciado</label>
                  <p class="mt-1 text-sm font-semibold text-gray-900">{{ selectedDenuncia?.denunciado?.name }}</p>
                  <p class="text-xs text-gray-500">{{ selectedDenuncia?.denunciado?.email }}</p>
                </div>
              </div>

              <div class="space-y-4">
                <div>
                  <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider">Motivo</label>
                  <div class="mt-1 flex items-center">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-yellow-100 text-yellow-800 border border-yellow-200">
                      {{ getMotivoText(selectedDenuncia?.motivo) }}
                    </span>
                  </div>
                </div>

                <div>
                  <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider">Status Atual</label>
                  <div class="mt-1 flex items-center">
                    <span :class="getStatusClass(selectedDenuncia?.status)" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold border border-current">
                      {{ getStatusText(selectedDenuncia?.status) }}
                    </span>
                  </div>
                </div>
              </div>
            </div>

            <div class="mt-8 border-t border-gray-100 pt-6">
              <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Descrição da Denúncia</label>
              <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                <p class="text-sm text-gray-800 leading-relaxed whitespace-pre-wrap">
                  {{ selectedDenuncia?.descricao || 'Nenhuma descrição detalhada fornecida.' }}
                </p>
              </div>
            </div>

            <div class="mt-6 flex items-center text-xs text-gray-500">
              <svg class="h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
              Denúncia recebida em {{ formatDate(selectedDenuncia?.created_at) }}
            </div>
          </div>

          <!-- Rodapé do Modal -->
          <div class="bg-primary-50 px-6 py-4 border-t border-primary-100 flex flex-col sm:flex-row-reverse gap-3">
            <button
              @click="closeModal"
              type="button"
              class="w-full inline-flex justify-center rounded-lg border border-transparent shadow-sm px-5 py-2.5 bg-primary-600 text-sm font-bold text-white hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-all sm:w-auto"
            >
              Fechar
            </button>
            <button
              v-if="selectedDenuncia?.status !== 'resolved'"
              @click="markAsResolved(selectedDenuncia); closeModal()"
              type="button"
              class="w-full inline-flex justify-center rounded-lg border border-transparent shadow-sm px-5 py-2.5 bg-success-600 text-sm font-bold text-white hover:bg-success-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-success-500 transition-all sm:w-auto"
            >
              Resolver Agora
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import AdminNavigationMenu from '../../components/AdminNavigationMenu.vue';
import api from '../../api';

const denuncias = ref([]);
const loading = ref(false);
const currentPage = ref(1);
const total = ref(0);
const showModal = ref(false);
const selectedDenuncia = ref(null);

const filters = ref({
  status: '',
  motivo: ''
});

const hasNextPage = ref(false);

const loadDenuncias = async () => {
  loading.value = true;
  try {
    const response = await api.get('/reports', {
      params: {
        page: currentPage.value,
        status: filters.value.status,
        reason: filters.value.motivo
      }
    });
    
    if (response.data && response.data.data) {
      denuncias.value = response.data.data;
      total.value = response.data.total;
      hasNextPage.value = response.data.current_page < response.data.last_page;
    }
  } catch (error) {
    console.error('Erro ao carregar denúncias:', error);
  } finally {
    loading.value = false;
  }
};

const viewDenuncia = (denuncia) => {
  selectedDenuncia.value = denuncia;
  showModal.value = true;
};

const closeModal = () => {
  showModal.value = false;
  selectedDenuncia.value = null;
};

const markAsAnalyzed = async (denuncia) => {
  try {
    await api.put(`/reports/${denuncia.id}`, {
      status: 'analyzed'
    });
    denuncia.status = 'analyzed';
  } catch (error) {
    console.error('Erro ao marcar denúncia como analisada:', error);
  }
};

const markAsResolved = async (denuncia) => {
  try {
    await api.put(`/reports/${denuncia.id}`, {
      status: 'resolved'
    });
    denuncia.status = 'resolved';
  } catch (error) {
    console.error('Erro ao marcar denúncia como resolvida:', error);
  }
};

const getStatusClass = (status) => {
  const classes = {
    pending: 'bg-yellow-50 text-yellow-700 border-yellow-200',
    analyzed: 'bg-primary-50 text-primary-700 border-primary-200',
    resolved: 'bg-success-50 text-success-700 border-success-200'
  };
  return classes[status] || 'bg-gray-50 text-gray-700 border-gray-200';
};

const getStatusText = (status) => {
  const texts = {
    pending: 'Pendente',
    analyzed: 'Analisada',
    resolved: 'Resolvida'
  };
  return texts[status] || 'Desconhecido';
};

const getMotivoText = (motivo) => {
  const texts = {
    harassment: 'Importunação',
    disrespect: 'Desrespeito',
    fake_profile: 'Perfil Falso',
    other: 'Outro'
  };
  return texts[motivo] || motivo;
};

const formatDate = (dateString) => {
  return new Date(dateString).toLocaleDateString('pt-BR', {
    day: '2-digit',
    month: '2-digit',
    year: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  });
};

const previousPage = () => {
  if (currentPage.value > 1) {
    currentPage.value--;
    loadDenuncias();
  }
};

const nextPage = () => {
  if (hasNextPage.value) {
    currentPage.value++;
    loadDenuncias();
  }
};

onMounted(() => {
  loadDenuncias();
});
</script>
