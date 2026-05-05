<template>
  <div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-indigo-500 to-purple-600">
    <div class="bg-white p-8 rounded-xl shadow-2xl w-full max-w-md">
      <div class="text-center mb-8">
        <h1 class="text-3xl font-bold text-gray-800">Create Account</h1>
        <p class="text-gray-600 mt-2">Start your journey on Closer</p>
      </div>

      <form @submit.prevent="handleRegister" class="space-y-6">
        <div>
          <label class="block text-sm font-medium text-gray-700">Name</label>
          <input
            v-model="form.name"
            type="text"
            required
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
            placeholder="Your name"
          />
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700">Email</label>
          <input
            v-model="form.email"
            type="email"
            required
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
            placeholder="your@email.com"
          />
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700">Password</label>
          <PasswordInput
            v-model="form.password"
            placeholder="Minimum 8 characters"
            required
          />
          <p class="mt-1 text-xs text-gray-500">
            Minimum 8 characters, 1 uppercase letter and 1 special character (!@#$%^&*())
          </p>
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700">Confirm Password</label>
          <PasswordInput
            v-model="form.password_confirmation"
            placeholder="•••••••"
            required
          />
        </div>

        <div v-if="error" class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded">
          {{ error }}
        </div>

        <button
          type="submit"
          :disabled="loading"
          class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50"
        >
          <span v-if="loading">Creating account...</span>
          <span v-else>Sign Up</span>
        </button>
      </form>

      <p class="mt-6 text-center text-sm text-gray-600">
        Already have an account?
        <RouterLink to="/login" class="font-medium text-indigo-600 hover:text-indigo-500">
          Sign In
        </RouterLink>
      </p>
    </div>
  </div>
</template>

<script setup>
import { reactive, ref } from 'vue';
import { useRouter } from 'vue-router';
import { useAuthStore } from '../stores/auth';
import PasswordInput from '../components/PasswordInput.vue';

const router = useRouter();
const authStore = useAuthStore();

const form = reactive({
  name: 'Teste User', // Nome padrão para testes
  email: 'test@closer.com', // Email padrão para testes
  password: 'Mudar@123', // Senha padrão para testes (8+ chars, maiúscula, especial)
  password_confirmation: 'Mudar@123', // Confirmação padrão para testes
});

const loading = ref(false);
const error = ref('');

const handleRegister = async () => {
  if (form.password !== form.password_confirmation) {
    error.value = 'As senhas não coincidem';
    return;
  }
  
  loading.value = true;
  error.value = '';
  
  const result = await authStore.register(form);
  
  loading.value = false;
  
  if (result.success) {
    router.push('/login');
    alert('Conta criada! Verifique seu email.');
  } else {
    error.value = result.error;
  }
};
</script>
