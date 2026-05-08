<template>
  <div class="min-h-screen flex items-center justify-center bg-primary-50 px-4 py-12">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-md overflow-hidden border border-primary-100">
      <!-- Progress Bar -->
      <div class="bg-primary-50 h-1.5 w-full flex">
        <div 
          v-for="step in 3" 
          :key="step"
          class="h-full transition-all duration-500"
          :class="[
            step <= currentStep ? 'bg-primary-600' : 'bg-transparent',
            'flex-1'
          ]"
        ></div>
      </div>

      <div class="p-8">
        <div class="text-center mb-8">
          <h1 class="text-2xl font-bold text-primary-900">Crie sua conta</h1>
          <p class="text-gray-500 text-sm mt-1">Passo {{ currentStep }} de 3</p>
        </div>

        <form @submit.prevent="handleRegister" class="space-y-6">
          <!-- Step 1: Account -->
          <div v-if="currentStep === 1" class="space-y-4 animate-in fade-in slide-in-from-right-4 duration-300">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">E-mail</label>
              <input
                v-model="form.email"
                type="email"
                required
                class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-primary-500 outline-none"
                placeholder="exemplo@email.com"
              />
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Senha</label>
              <PasswordInput
                v-model="form.password"
                placeholder="Ex: Senha@123"
                required
              />
              <PasswordRequirements :password="form.password" />
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Confirmar Senha</label>
              <PasswordInput
                v-model="form.password_confirmation"
                placeholder="Repita sua senha"
                required
              />
            </div>
            
            <button
              type="button"
              @click="nextStep"
              :disabled="!isStep1Valid"
              class="w-full py-3 bg-primary-600 text-white rounded-xl font-bold hover:bg-primary-700 transition-all shadow-md disabled:opacity-50"
            >
              Próximo
            </button>
          </div>

          <!-- Step 2: Basics -->
          <div v-if="currentStep === 2" class="space-y-4 animate-in fade-in slide-in-from-right-4 duration-300">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Nome Completo</label>
              <input
                v-model="form.name"
                type="text"
                required
                class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-primary-500 outline-none"
                placeholder="Como está no seu documento"
              />
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Apelido (Nickname)</label>
              <input
                v-model="form.nickname"
                type="text"
                required
                class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-primary-500 outline-none"
                placeholder="Como as pessoas te verão"
              />
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Data de Nascimento</label>
              <input
                v-model="form.birth_date"
                type="date"
                required
                class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-primary-500 outline-none"
              />
            </div>

            <div class="flex gap-4">
              <button
                type="button"
                @click="prevStep"
                class="flex-1 py-3 border border-gray-300 text-gray-700 rounded-xl font-medium hover:bg-gray-50 transition-all"
              >
                Voltar
              </button>
              <button
                type="button"
                @click="nextStep"
                :disabled="!isStep2Valid"
                class="flex-1 py-3 bg-primary-600 text-white rounded-xl font-bold hover:bg-primary-700 transition-all shadow-md disabled:opacity-50"
              >
                Próximo
              </button>
            </div>
          </div>

          <!-- Step 3: Identity & Purpose -->
          <div v-if="currentStep === 3" class="space-y-4 animate-in fade-in slide-in-from-right-4 duration-300">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Gênero</label>
              <select
                v-model="form.gender"
                required
                class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-primary-500 outline-none"
              >
                <option value="">Selecione...</option>
                <option value="male">Masculino</option>
                <option value="female">Feminino</option>
                <option value="non_binary">Não-binário</option>
                <option value="other">Outro</option>
              </select>
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Identidade de Gênero</label>
              <input
                v-model="form.gender_identity"
                type="text"
                class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-primary-500 outline-none"
                placeholder="Ex: Cisgênero, Transgênero..."
              />
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Orientação Sexual</label>
              <input
                v-model="form.sexual_orientation"
                type="text"
                class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-primary-500 outline-none"
                placeholder="Ex: Heterossexual, Homossexual..."
              />
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">O que você busca?</label>
              <select
                v-model="form.purpose"
                required
                class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-primary-500 outline-none"
              >
                <option value="serious">Relacionamento Sério</option>
                <option value="casual">Algo Casual</option>
                <option value="friendship">Amizade</option>
                <option value="networking">Networking</option>
                <option value="all">Tudo um pouco</option>
              </select>
            </div>

            <div v-if="error" class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg text-sm">
              {{ error }}
            </div>

            <div class="flex gap-4">
              <button
                type="button"
                @click="prevStep"
                class="flex-1 py-3 border border-gray-300 text-gray-700 rounded-xl font-medium hover:bg-gray-50 transition-all"
              >
                Voltar
              </button>
              <button
                type="submit"
                :disabled="loading"
                class="flex-1 py-3 bg-primary-600 text-white rounded-xl font-bold hover:bg-primary-700 transition-all shadow-md disabled:opacity-50"
              >
                {{ loading ? 'Criando...' : 'Finalizar' }}
              </button>
            </div>
          </div>
        </form>

        <div class="mt-4 flex justify-center gap-4 text-[10px] text-gray-400">
          <button @click="fillConsumerForm" type="button" class="hover:text-primary-600 transition-colors underline uppercase tracking-widest">Preencher Teste</button>
          <button @click="clearForm" type="button" class="hover:text-red-500 transition-colors underline uppercase tracking-widest">Limpar</button>
        </div>

        <p v-if="currentStep === 1" class="mt-8 text-center text-sm text-gray-500">
          Já tem uma conta?
          <RouterLink to="/login" class="font-bold text-primary-600 hover:text-primary-700">
            Fazer Login
          </RouterLink>
        </p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { reactive, ref, computed } from 'vue';
import { useRouter } from 'vue-router';
import { useAuthStore } from '../stores/auth';
import PasswordInput from '../components/PasswordInput.vue';
import PasswordRequirements from '../components/PasswordRequirements.vue';
import { useFormTester } from '../composables/useFormTester';

const router = useRouter();
const authStore = useAuthStore();

const currentStep = ref(1);
const loading = ref(false);
const error = ref('');

const form = reactive({
  name: '',
  email: '',
  password: '',
  password_confirmation: '',
  nickname: '',
  birth_date: '',
  gender: '',
  gender_identity: '',
  sexual_orientation: '',
  purpose: 'all',
});

const { fillConsumerForm, clearForm } = useFormTester(form);

const isStep1Valid = computed(() => {
  const hasUpper = /[A-Z]/.test(form.password);
  const hasLower = /[a-z]/.test(form.password);
  const hasSpecial = /[!@#$%^&*()_+\-=\[\]{};:"\\|,.<>\/?]/.test(form.password);
  return form.email && form.password && form.password.length >= 8 && hasUpper && hasLower && hasSpecial && form.password === form.password_confirmation;
});

const isStep2Valid = computed(() => {
  return form.name && form.nickname && form.birth_date;
});

const nextStep = () => {
  if (currentStep.value < 3) currentStep.value++;
};

const prevStep = () => {
  if (currentStep.value > 1) currentStep.value--;
};

const handleRegister = async () => {
  loading.value = true;
  error.value = '';
  
  try {
    const result = await authStore.register(form);
    
    if (result.success) {
      alert('Conta criada com sucesso! Por favor, verifique seu e-mail para ativar sua conta.');
      router.push('/login');
    } else {
      error.value = result.error || 'Ocorreu um erro ao criar sua conta.';
      // Se houver erros específicos de campos, poderíamos voltar para o passo correto
    }
  } catch (err) {
    error.value = 'Erro de conexão com o servidor.';
  } finally {
    loading.value = false;
  }
};
</script>

<style scoped>
.animate-in {
  animation-duration: 0.3s;
}
</style>
