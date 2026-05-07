<template>
  <nav class="bg-white shadow-lg border-b border-primary-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="flex justify-between h-16">
        <div class="flex items-center">
          <h1 class="text-xl font-bold text-primary-600">Closer</h1>
        </div>

        <!-- Desktop Menu -->
        <div class="hidden md:flex items-center space-x-4">
          <RouterLink to="/feed" class="text-gray-600 hover:text-primary-600 px-3 py-2 rounded-md text-sm font-medium hover:bg-primary-50 transition-colors">Feed</RouterLink>
          <RouterLink to="/matches" class="text-gray-600 hover:text-primary-600 px-3 py-2 rounded-md text-sm font-medium hover:bg-primary-50 transition-colors">Matches</RouterLink>
          <RouterLink to="/profile" class="text-gray-600 hover:text-primary-600 px-3 py-2 rounded-md text-sm font-medium hover:bg-primary-50 transition-colors">Perfil</RouterLink>
          
          <!-- Staff Menu -->
          <div v-if="userStore.isStaffLevel" class="relative">
            <button
              @click="toggleStaffMenu"
              class="text-gray-600 hover:text-primary-600 px-3 py-2 rounded-md text-sm font-medium flex items-center hover:bg-primary-50 transition-colors"
            >
              Staff
              <svg class="ml-1 h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
              </svg>
            </button>
            
            <div v-if="showStaffMenu" class="absolute right-0 mt-2 w-48 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-50">
              <div class="py-1">
                <!-- Relatórios Submenu -->
                <div class="relative">
                  <button
                    @click="toggleReportsMenu"
                    class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 flex items-center justify-between"
                  >
                    <span>Relatórios</span>
                    <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                      <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10 10.414l-3.293 3.293a1 1 0 01-1.414-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 01-1.414 1.414L10 11.586l-3.293 3.293a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                    </svg>
                  </button>
                  
                  <div v-if="showReportsMenu" class="absolute left-0 mt-0 ml-48 w-48 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-50">
                    <div class="py-1">
                      <RouterLink
                        to="/admin/reports/users"
                        class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                        @click="closeAllMenus"
                      >
                        Relatório de Usuários
                      </RouterLink>
                    </div>
                  </div>
                </div>

                <!-- Usuários -->
                <RouterLink
                  to="/admin/users"
                  class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                  @click="closeAllMenus"
                >
                  Usuários
                </RouterLink>

                <!-- Denúncias -->
                <RouterLink
                  to="/admin/denuncias"
                  class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                  @click="closeAllMenus"
                >
                  Denúncias
                </RouterLink>
              </div>
            </div>
          </div>

          <!-- Botão Sair Desktop -->
          <button
            @click="handleLogout"
            class="text-gray-600 hover:text-red-600 px-3 py-2 rounded-md text-sm font-medium hover:bg-red-50 transition-colors cursor-pointer"
          >
            Sair
          </button>
        </div>

        <!-- Mobile menu button -->
        <div class="md:hidden flex items-center">
          <button
            @click="toggleMobileMenu"
            class="text-gray-600 hover:text-primary-600 p-2 rounded-md transition-colors"
          >
            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
            </svg>
          </button>
        </div>
      </div>

      <!-- Mobile Menu -->
      <div v-if="showMobileMenu" class="md:hidden">
        <div class="px-2 pt-2 pb-3 space-y-1">
          <RouterLink to="/feed" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-primary-600 hover:bg-primary-50 transition-colors">Feed</RouterLink>
          <RouterLink to="/matches" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-primary-600 hover:bg-primary-50 transition-colors">Matches</RouterLink>
          <RouterLink to="/profile" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-primary-600 hover:bg-primary-50 transition-colors">Perfil</RouterLink>
          
          <!-- Mobile Staff Menu -->
          <div v-if="userStore.user?.is_admin_level" class="mt-2 pt-2 border-t border-gray-200">
            <div class="px-3 py-2 text-xs font-semibold text-gray-500 uppercase tracking-wider">Staff</div>
            
            <!-- Mobile Relatórios -->
            <div class="mt-1">
              <div class="px-3 py-2 text-sm font-medium text-gray-700">Relatórios</div>
              <RouterLink
                to="/admin/reports/users"
                class="block pl-6 pr-3 py-2 text-sm text-gray-600 hover:text-gray-900 hover:bg-gray-50"
                @click="closeMobileMenu"
              >
                Relatório de Usuários
              </RouterLink>
            </div>

            <!-- Mobile Usuários -->
            <RouterLink
              to="/admin/users"
              class="block px-3 py-2 text-sm text-gray-600 hover:text-gray-900 hover:bg-gray-50"
              @click="closeMobileMenu"
            >
              Usuários
            </RouterLink>

            <!-- Mobile Denúncias -->
            <RouterLink
              to="/admin/denuncias"
              class="block px-3 py-2 text-sm text-gray-600 hover:text-gray-900 hover:bg-gray-50"
              @click="closeMobileMenu"
            >
              Denúncias
            </RouterLink>
          </div>

          <div class="mt-4 pt-4 border-t border-gray-200">
            <button
              @click="handleLogout"
              class="w-full bg-primary-500 hover:bg-primary-600 text-white px-4 py-2 rounded-md cursor-pointer transition-all duration-200 hover:shadow-lg text-sm font-medium"
            >
              Sair
            </button>
          </div>
        </div>
      </div>
    </div>
  </nav>
</template>

<script setup>
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import { useAuthStore } from '../stores/auth';

const router = useRouter();
const userStore = useAuthStore();

const showMobileMenu = ref(false);
const showStaffMenu = ref(false);
const showReportsMenu = ref(false);

const toggleMobileMenu = () => {
  showMobileMenu.value = !showMobileMenu.value;
  // Close other menus when opening mobile menu
  if (showMobileMenu.value) {
    showStaffMenu.value = false;
    showReportsMenu.value = false;
  }
};

const toggleStaffMenu = () => {
  showStaffMenu.value = !showStaffMenu.value;
  // Close mobile menu and reports menu
  showMobileMenu.value = false;
  showReportsMenu.value = false;
};

const toggleReportsMenu = () => {
  showReportsMenu.value = !showReportsMenu.value;
};

const closeAllMenus = () => {
  showMobileMenu.value = false;
  showStaffMenu.value = false;
  showReportsMenu.value = false;
};

const closeMobileMenu = () => {
  showMobileMenu.value = false;
};

const handleLogout = async () => {
  try {
    await userStore.logout();
    router.push('/login');
  } catch (error) {
    console.error('Erro ao fazer logout:', error);
  }
};

// Close menus when clicking outside
const handleClickOutside = (event) => {
  if (!event.target.closest('.relative') && !event.target.closest('.md\\:hidden')) {
    closeAllMenus();
  }
};

// Add event listener for click outside
if (typeof window !== 'undefined') {
  document.addEventListener('click', handleClickOutside);
}
</script>
