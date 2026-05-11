<template>
  <div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-indigo-500 to-purple-600 p-4">
    <div class="flex flex-col md:flex-row gap-6 w-full max-w-5xl">
      <!-- Quick-access sidebar -->
      <div class="bg-white/95 backdrop-blur-sm rounded-xl shadow-2xl p-6 w-full md:w-72 shrink-0">
        <h2 class="text-sm font-bold text-gray-800 mb-1">Quick Access</h2>
        <p class="text-xs text-gray-500 mb-4">Clique num dos emails pré-cadastrados para logar como teste</p>

        <div class="space-y-3">
          <div v-for="group in groupedUsers" :key="group.label">
            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1.5">{{ group.label }}</p>
            <div class="flex flex-wrap gap-1.5">
              <button
                v-for="user in group.users"
                :key="user.email"
                type="button"
                class="px-2.5 py-1.5 rounded text-xs border transition-all"
                :class="form.email === user.email ? 'bg-indigo-100 border-indigo-400 text-indigo-800 font-medium' : 'bg-white border-gray-200 text-gray-700 hover:bg-indigo-50 hover:border-indigo-300'"
                @click="form.email = user.email"
                :title="user.email"
              >
                {{ user.name }}
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- Login Form -->
      <div class="bg-white p-8 rounded-xl shadow-2xl flex-1">
        <div class="text-center mb-8">
          <img 
            src="/storage/logo.png" 
            alt="Closer" 
            class="h-16 mx-auto mb-4"
            onerror="this.style.display='none'"
          />
          <h1 class="text-3xl font-bold text-gray-800">Welcome Back</h1>
          <p class="text-gray-600 mt-2">Sign in to your account to continue</p>
        </div>

        <form @submit.prevent="handleLogin" class="space-y-6">
          <div>
            <label class="block text-sm font-medium text-gray-700">Email</label>
            <input
              v-model="form.email"
              type="email"
              required
              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
              placeholder="user@example.com"
            />
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700">Password</label>
            <PasswordInput
              v-model="form.password"
              placeholder="••••••••"
            />
          </div>

          <div v-if="requires2FA" class="bg-yellow-50 border border-yellow-200 p-4 rounded-md">
            <label class="block text-sm font-medium text-yellow-800">2FA Code</label>
            <input
              v-model="form.twoFactorCode"
              type="text"
              maxlength="6"
              class="mt-1 block w-full rounded-md border-yellow-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500"
              placeholder="000000"
            />
            <p class="text-xs text-yellow-600 mt-1">
              Open your authentication app and enter the code
            </p>
          </div>

          <div v-if="authStore.error" class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded">
            {{ authStore.error }}
          </div>

          <button
            type="submit"
            :disabled="authStore.loading"
            class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50"
          >
            <span v-if="authStore.loading">Signing in...</span>
            <span v-else>Sign In</span>
          </button>
        </form>

        <p class="mt-6 text-center text-sm text-gray-600">
          Don't have an account?
          <RouterLink to="/register" class="font-medium text-indigo-600 hover:text-indigo-500">
            Sign Up
          </RouterLink>
        </p>
      </div>
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

const usersByLevel = [
  {
    label: 'Staff',
    users: [
      { name: 'Admin (10)', email: 'admin@1.com' },
      { name: 'Operacional (11)', email: 'operator@2.com' },
      { name: 'Suporte (12)', email: 'support@3.com' },
    ],
  },
  {
    label: 'Free (0)',
    users: [
      { name: 'Free 1', email: 'free@closer.com' },
      { name: 'Free 2', email: 'free2@closer.com' },
      { name: 'Free 3', email: 'free3@closer.com' },
    ],
  },
  {
    label: 'Moderador (1)',
    users: [
      { name: 'Moderador 1', email: 'moderator@closer.com' },
      { name: 'Moderador 2', email: 'moderator2@closer.com' },
    ],
  },
  {
    label: 'Plus (2)',
    users: [
      { name: 'Plus 1', email: 'plus@closer.com' },
      { name: 'Plus 2', email: 'plus2@closer.com' },
    ],
  },
  {
    label: 'Premium (3)',
    users: [
      { name: 'Premium 1', email: 'premium@closer.com' },
      { name: 'Premium 2', email: 'premium2@closer.com' },
    ],
  },
  {
    label: 'Co-Founder (4)',
    users: [
      { name: 'Co-Founder 1', email: 'cofounder@closer.com' },
      { name: 'Co-Founder 2', email: 'cofounder2@closer.com' },
    ],
  },
  {
    label: 'Elite (5)',
    users: [
      { name: 'Elite 1', email: 'elite@closer.com' },
      { name: 'Elite 2', email: 'elite2@closer.com' },
    ],
  },
];

const groupedUsers = usersByLevel;

const form = reactive({
  email: 'admin@1.com', // Default test admin user
  password: 'Mudar@123', // Default test password (matches seeders)
  twoFactorCode: '',
});

const requires2FA = ref(false);

const handleLogin = async () => {
  const result = await authStore.login({
    email: form.email,
    password: form.password,
  });
  
  if (result.success) {
    if (authStore.isStaffLevel) {
      router.push('/admin/dashboard');
    } else {
      router.push('/');
    }
  } else if (result.requires_2fa) {
    requires2FA.value = true;
  }
};
</script>