<template>
  <div class="min-h-screen bg-admin-50">
    <AdminNavigationMenu />
    
    <main class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
      <div class="px-4 py-6 sm:px-0">
        <div class="bg-white rounded-lg shadow">
          <div class="px-6 py-4 border-b border-gray-200">
            <h1 class="text-2xl font-bold text-admin-900">Denúncias</h1>
            <p class="mt-1 text-sm text-gray-600">
              Gerencie todas as denúncias do sistema
            </p>
          </div>

          <!-- Filtros -->
          <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex flex-col sm:flex-row gap-4">
              <select
                v-model="filters.status"
                class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
              >
                <option value="">Todos os status</option>
                <option value="pending">Pendentes</option>
                <option value="analyzed">Analisadas</option>
                <option value="resolved">Resolvidas</option>
              </select>
              
              <select
                v-model="filters.motivo"
                class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
              >
                <option value="">Todos os motivos</option>
                <option value="importunacao">Importunação</option>
                <option value="desrespeito">Desrespeito</option>
                <option value="perfil_falso">Perfil Falso</option>
                <option value="outro">Outro</option>
              </select>

              <button
                @click="loadDenuncias"
                class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-admin-600 hover:bg-admin-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-admin-500"
              >
                Filtrar
              </button>
            </div>
          </div>

          <!-- Lista de Denúncias -->
          <div class="overflow-hidden">
            <div v-if="loading" class="px-6 py-8 text-center">
              <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-admin-600"></div>
              <p class="mt-2 text-gray-600">Carregando denúncias...</p>
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
                      class="inline-flex items-center px-3 py-1.5 border border-gray-300 shadow-sm text-xs font-medium rounded text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                    >
                      Visualizar
                    </button>
                    
                    <button
                      v-if="denuncia.status !== 'analyzed'"
                      @click="markAsAnalyzed(denuncia)"
                      class="inline-flex items-center px-3 py-1.5 border border-transparent shadow-sm text-xs font-medium rounded text-white bg-admin-600 hover:bg-admin-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-admin-500"
                    >
                      Marcar como Analisada
                    </button>
                    
                    <button
                      v-if="denuncia.status !== 'resolved'"
                      @click="markAsResolved(denuncia)"
                      class="inline-flex items-center px-3 py-1.5 border border-transparent shadow-sm text-xs font-medium rounded text-white bg-success-600 hover:bg-success-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-success-500"
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
                  class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
                >
                  Anterior
                </button>
                <button
                  @click="nextPage"
                  :disabled="!hasNextPage"
                  class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
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
                      class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50"
                    >
                      Anterior
                    </button>
                    <button
                      @click="nextPage"
                      :disabled="!hasNextPage"
                      class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50"
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
      <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 transition-opacity" @click="closeModal">
          <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
        </div>

        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
          <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
            <div class="sm:flex sm:items-start">
              <div class="mt-3 text-center sm:mt-0 sm:text-left w-full">
                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">
                  Detalhes da Denúncia #{{ selectedDenuncia?.id }}
                </h3>
                
                <div class="space-y-4">
                  <div>
                    <label class="block text-sm font-medium text-gray-700">Denunciante</label>
                    <p class="mt-1 text-sm text-gray-900">{{ selectedDenuncia?.denunciante?.name }} ({{ selectedDenuncia?.denunciante?.email }})</p>
                  </div>
                  
                  <div>
                    <label class="block text-sm font-medium text-gray-700">Denunciado</label>
                    <p class="mt-1 text-sm text-gray-900">{{ selectedDenuncia?.denunciado?.name }} ({{ selectedDenuncia?.denunciado?.email }})</p>
                  </div>
                  
                  <div>
                    <label class="block text-sm font-medium text-gray-700">Motivo</label>
                    <p class="mt-1 text-sm text-gray-900">{{ getMotivoText(selectedDenuncia?.motivo) }}</p>
                  </div>
                  
                  <div v-if="selectedDenuncia?.descricao">
                    <label class="block text-sm font-medium text-gray-700">Descrição</label>
                    <p class="mt-1 text-sm text-gray-900 whitespace-pre-wrap">{{ selectedDenuncia?.descricao }}</p>
                  </div>
                  
                  <div>
                    <label class="block text-sm font-medium text-gray-700">Data da Denúncia</label>
                    <p class="mt-1 text-sm text-gray-900">{{ formatDate(selectedDenuncia?.created_at) }}</p>
                  </div>
                  
                  <div>
                    <label class="block text-sm font-medium text-gray-700">Status</label>
                    <span :class="getStatusClass(selectedDenuncia?.status)" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium">
                      {{ getStatusText(selectedDenuncia?.status) }}
                    </span>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
            <button
              @click="closeModal"
              type="button"
              class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-admin-600 text-base font-medium text-white hover:bg-admin-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-admin-500 sm:ml-3 sm:w-auto sm:text-sm"
            >
              Fechar
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
    // Simulação - em produção, fazer chamada API real
    const mockDenuncias = [
      {
        id: 1,
        denunciante: { name: 'Ana Silva', email: 'ana@exemplo.com' },
        denunciado: { name: 'Carlos Santos', email: 'carlos@exemplo.com' },
        motivo: 'importunacao',
        descricao: 'Usuário enviou mensagens inapropriadas e insistiu após ser bloqueado.',
        status: 'pending',
        created_at: '2026-05-04T10:30:00Z'
      },
      {
        id: 2,
        denunciante: { name: 'Pedro Oliveira', email: 'pedro@exemplo.com' },
        denunciado: { name: 'Mariana Costa', email: 'mariana@exemplo.com' },
        motivo: 'perfil_falso',
        descricao: 'Perfil parece ser falso, fotos não correspondem à pessoa real.',
        status: 'analyzed',
        created_at: '2026-05-03T15:45:00Z'
      }
    ];
    
    denuncias.value = mockDenuncias;
    total.value = mockDenuncias.length;
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
    // Em produção, fazer chamada API real
    denuncia.status = 'analyzed';
    console.log('Denúncia marcada como analisada:', denuncia.id);
  } catch (error) {
    console.error('Erro ao marcar denúncia como analisada:', error);
  }
};

const markAsResolved = async (denuncia) => {
  try {
    // Em produção, fazer chamada API real
    denuncia.status = 'resolved';
    console.log('Denúncia marcada como resolvida:', denuncia.id);
  } catch (error) {
    console.error('Erro ao marcar denúncia como resolvida:', error);
  }
};

const getStatusClass = (status) => {
  const classes = {
    pending: 'bg-yellow-100 text-yellow-800',
    analyzed: 'bg-blue-100 text-blue-800',
    resolved: 'bg-green-100 text-green-800'
  };
  return classes[status] || 'bg-gray-100 text-gray-800';
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
    importunacao: 'Importunação',
    desrespeito: 'Desrespeito',
    perfil_falso: 'Perfil Falso',
    outro: 'Outro'
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
