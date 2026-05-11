<template>
  <div class="min-h-screen bg-primary-50 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-3xl mx-auto">
      <div class="bg-white rounded-xl shadow-lg overflow-hidden border border-primary-100">
        <div class="bg-primary-600 px-6 py-4 flex items-center justify-between">
          <h2 class="text-xl font-bold text-white">Editar Usuário</h2>
          <button @click="$router.back()" class="text-white hover:text-primary-100 transition-colors">
            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>

        <div v-if="loading" class="p-12 text-center">
          <div class="inline-block animate-spin rounded-full h-8 w-8 border-t-2 border-b-2 border-primary-600"></div>
          <p class="mt-4 text-gray-500 font-medium">Carregando dados do usuário...</p>
        </div>

        <form v-else @submit.prevent="saveUser" class="p-8 space-y-8">
          <!-- Seção de Dados da Conta -->
          <div>
            <h3 class="text-lg font-semibold text-primary-900 border-b border-primary-100 pb-2 mb-4 flex items-center">
              <svg class="h-5 w-5 mr-2 text-primary-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
              </svg>
              Informações da Conta
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nome Completo</label>
                <input v-model="form.name" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-primary-500 focus:border-transparent outline-none transition-all" required />
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">E-mail</label>
                <input v-model="form.email" type="email" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-primary-500 focus:border-transparent outline-none transition-all" required />
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nível de Acesso</label>
                <select v-model="form.nivel_acesso" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-primary-500 outline-none transition-all">
                  <optgroup label="Consumidores">
                    <option value="0">Free</option>
                    <option value="1">Moderador</option>
                    <option value="2">Plus</option>
                    <option value="3">Premium</option>
                    <option value="4">Co-Founder</option>
                    <option value="5">Elite</option>
                  </optgroup>
                  <optgroup label="Staff">
                    <option value="10">Administrador</option>
                    <option value="11">Operacional</option>
                    <option value="12">Suporte</option>
                  </optgroup>
                </select>
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Status da Conta</label>
                <select v-model="form.ativo" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-primary-500 outline-none transition-all">
                  <option :value="true">Ativa</option>
                  <option :value="false">Inativa</option>
                </select>
              </div>
            </div>
          </div>

          <!-- Seção de Perfil (Apenas para Consumidores) -->
          <div v-if="isConsumer">
            <h3 class="text-lg font-semibold text-primary-900 border-b border-primary-100 pb-2 mb-4 flex items-center">
              <svg class="h-5 w-5 mr-2 text-primary-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2" />
              </svg>
              Detalhes do Perfil
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nickname</label>
                <input v-model="form.profile.nickname" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-primary-500 outline-none transition-all" />
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Gênero</label>
                <select v-model="form.profile.gender" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-primary-500 outline-none transition-all">
                  <option value="male">Masculino</option>
                  <option value="female">Feminino</option>
                  <option value="other">Outro</option>
                </select>
              </div>
              <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Biografia</label>
                <textarea v-model="form.profile.biography" rows="4" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-primary-500 outline-none transition-all resize-none"></textarea>
              </div>
            </div>
          </div>

          <div class="pt-6 border-t border-primary-100 flex items-center justify-between">
            <div class="flex flex-col">
              <p class="text-xs text-gray-400">ID Público: <span class="font-mono">{{ route.params.id }}</span></p>
              <div class="flex gap-4 text-[10px] text-gray-400 mt-1">
                <button @click="fillConsumerForm" type="button" class="hover:text-primary-600 transition-colors underline uppercase tracking-widest">Preencher Teste</button>
                <button @click="clearForm" type="button" class="hover:text-red-500 transition-colors underline uppercase tracking-widest">Limpar</button>
              </div>
            </div>

            <div v-if="error" class="mb-4 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg text-sm">
              {{ error }}
            </div>

            <div class="flex gap-4">
              <button 
                type="button" 
                @click="$router.back()" 
                class="px-6 py-2 rounded-lg border border-gray-300 text-gray-700 font-medium hover:bg-gray-50 transition-colors"
              >
                Cancelar
              </button>
              <button 
                type="submit" 
                :disabled="saving"
                class="px-8 py-2 rounded-lg bg-primary-600 text-white font-bold hover:bg-primary-700 shadow-md hover:shadow-lg transition-all disabled:opacity-50"
              >
                {{ saving ? 'Salvando...' : 'Salvar Alterações' }}
              </button>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import api from '../../api';
import { useFormTester } from '../../composables/useFormTester';

const route = useRoute();
const router = useRouter();
const loading = ref(true);
const saving = ref(false);
const error = ref('');

const form = ref({ 
  name: '', 
  email: '', 
  nivel_acesso: 0, 
  ativo: true,
  profile: {
    nickname: '',
    gender: '',
    biography: ''
  }
});

const { fillConsumerForm, clearForm } = useFormTester(form.value);

const isConsumer = computed(() => {
  const level = parseInt(form.value.nivel_acesso);
  return level < 10; // Níveis abaixo de 10 são consumidores
});

const fetchUser = async () => {
  loading.value = true;
  try {
    const { id } = route.params;
    const { data } = await api.get(`/users/${id}`);
    
    form.value = {
      name: data.name || '',
      email: data.email || '',
      nivel_acesso: data.nivel_acesso ?? 0,
      ativo: data.ativo ?? true,
      profile: {
        nickname: data.perfil?.nickname || '',
        gender: data.perfil?.gender || '',
        biography: data.perfil?.biography || ''
      }
    };
  } catch (error) {
    console.error('Erro ao buscar usuário:', error);
    alert('Erro ao carregar dados do usuário. Verifique se o ID/UUID está correto.');
  } finally {
    loading.value = false;
  }
};

const saveUser = async () => {
  saving.value = true;
  try {
    const { id } = route.params;
    await api.put(`/users/${id}`, form.value);
    alert('Usuário atualizado com sucesso!');
    router.push('/admin/users'); // Redireciona para a lista correta
  } catch (err) {
    console.error('Erro ao salvar:', err);
    error.value = err.response?.data?.message || 'Erro ao salvar as alterações.';
  } finally {
    saving.value = false;
  }
};

onMounted(fetchUser);
</script>

<style scoped>
</style>
