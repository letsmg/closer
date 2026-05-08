<template>
  <div class="min-h-screen bg-primary-50 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-2xl mx-auto">
      <div class="bg-white rounded-xl shadow-lg overflow-hidden border border-primary-100">
        <div class="bg-primary-600 px-6 py-4 flex items-center justify-between">
          <h2 class="text-xl font-bold text-white">Novo Usuário do Staff</h2>
          <button @click="$router.back()" class="text-white hover:text-primary-100 transition-colors">
            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>

        <form @submit.prevent="createUser" class="p-8 space-y-6">
          <div class="grid grid-cols-1 gap-6">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Nome Completo</label>
              <input 
                v-model="form.name" 
                type="text"
                class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-primary-500 focus:border-transparent outline-none transition-all" 
                required 
                placeholder="Ex: João Silva"
              />
            </div>
            
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">E-mail</label>
              <input 
                v-model="form.email" 
                type="email" 
                class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-primary-500 focus:border-transparent outline-none transition-all" 
                required 
                placeholder="email@closer.com"
              />
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Senha Inicial</label>
              <PasswordInput 
                v-model="form.password" 
                placeholder="Ex: Staff@2026! (8+ carac.)"
                required 
              />
              <PasswordRequirements :password="form.password" />
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Nível de Acesso (Staff)</label>
              <select 
                v-model="form.nivel_acesso" 
                class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-primary-500 outline-none transition-all"
                required
              >
                <option value="3">Administrador</option>
                <option value="4">Operacional</option>
                <option value="5">Suporte</option>
              </select>
              <p class="mt-1 text-xs text-gray-500 italic">
                Apenas níveis administrativos podem ser criados por aqui.
              </p>
            </div>
          </div>

          <div v-if="error" class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg text-sm">
            {{ error }}
          </div>

          <div class="pt-6 border-t border-primary-100 flex items-center justify-between">
            <div class="flex gap-4 text-[10px] text-gray-400">
              <button @click="fillStaffForm" type="button" class="hover:text-primary-600 transition-colors underline uppercase tracking-widest">Preencher Teste</button>
              <button @click="clearForm" type="button" class="hover:text-red-500 transition-colors underline uppercase tracking-widest">Limpar</button>
            </div>
            <div class="flex items-center gap-4">
              <button 
                type="button" 
                @click="$router.back()" 
                class="px-6 py-2 rounded-lg border border-gray-300 text-gray-700 font-medium hover:bg-gray-50 transition-colors"
              >
                Cancelar
              </button>
              <button 
                type="submit" 
                :disabled="loading"
                class="px-8 py-2 rounded-lg bg-primary-600 text-white font-bold hover:bg-primary-700 shadow-md hover:shadow-lg transition-all disabled:opacity-50"
              >
                {{ loading ? 'Criando...' : 'Criar Usuário' }}
              </button>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import api from '../../api';
import PasswordInput from '../../components/PasswordInput.vue';
import PasswordRequirements from '../../components/PasswordRequirements.vue';
import { useFormTester } from '../../composables/useFormTester';

const router = useRouter();
const loading = ref(false);
const error = ref('');

const form = ref({
  name: '',
  email: '',
  password: '',
  nivel_acesso: 3, // Padrão Admin
});

const { fillStaffForm, clearForm } = useFormTester(form.value);

const createUser = async () => {
  loading.value = true;
  error.value = '';
  
  try {
    await api.post('/users', form.value);
    alert('Usuário do staff criado com sucesso!');
    router.push('/admin/users');
  } catch (err) {
    console.error('Erro ao criar usuário:', err);
    error.value = err.response?.data?.message || 'Erro ao criar usuário. Verifique se o e-mail já existe.';
  } finally {
    loading.value = false;
  }
};
</script>
