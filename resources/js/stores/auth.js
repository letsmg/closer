import { defineStore } from 'pinia';
import { ref, computed } from 'vue';
import api from '../api';

export const useAuthStore = defineStore('auth', () => {
  // State
  const user = ref(JSON.parse(localStorage.getItem('user')) || null);
  const token = ref(localStorage.getItem('access_token'));
  const refreshToken = ref(localStorage.getItem('refresh_token'));
  const loading = ref(false);
  const isInitialized = ref(false);
  const error = ref(null);

  // Getters
  const isAuthenticated = computed(() => !!token.value && !!user.value);
  const userUuid = computed(() => user.value?.uuid);
  const isAdminLevel = computed(() => {
    const level = user.value?.nivel_acesso ?? user.value?.nivel;
    return level !== undefined && level >= 3;
  });
  const isStaffLevel = computed(() => {
    const level = user.value?.nivel_acesso ?? user.value?.nivel;
    return level !== undefined && level >= 3;
  });
  const isRegularUser = computed(() => {
    const level = user.value?.nivel_acesso ?? user.value?.nivel;
    return level !== undefined && level < 3;
  });

  // Actions
  const setAuth = (authData) => {
    token.value = authData.access_token;
    refreshToken.value = authData.refresh_token;
    user.value = authData.user;
    
    if (authData.access_token) localStorage.setItem('access_token', authData.access_token);
    if (authData.refresh_token) localStorage.setItem('refresh_token', authData.refresh_token);
    if (authData.user) localStorage.setItem('user', JSON.stringify(authData.user));
    
    // Set default header for axios
    if (authData.access_token) {
      api.defaults.headers.common['Authorization'] = `Bearer ${authData.access_token}`;
    }
  };

  const clearAuth = () => {
    token.value = null;
    refreshToken.value = null;
    user.value = null;
    error.value = null;
    
    localStorage.removeItem('access_token');
    localStorage.removeItem('refresh_token');
    localStorage.removeItem('user');
    
    delete api.defaults.headers.common['Authorization'];
  };

  const login = async (credentials) => {
    loading.value = true;
    error.value = null;
    
    try {
      const response = await api.post('/oauth/token', {
        grant_type: 'password',
        username: credentials.email,
        password: credentials.password,
        scope: 'read:profile write:profile read:feed write:interactions read:messages write:messages',
      });
      
      if (response.data.success !== false) {
        // Support both OAuth2 token response and custom success response
        const authData = response.data.data || response.data;
        setAuth(authData);
        return { success: true };
      }
    } catch (err) {
      error.value = err.response?.data?.error_description || 'Erro ao fazer login';
      return { success: false, error: error.value };
    } finally {
      loading.value = false;
    }
  };

  const register = async (userData) => {
    loading.value = true;
    error.value = null;
    
    try {
      const response = await api.post('auth/register', userData);
      
      if (response.data.success) {
        return { success: true };
      }
    } catch (err) {
      error.value = err.response?.data?.message || 'Erro ao criar conta';
      return { success: false, error: error.value };
    } finally {
      loading.value = false;
    }
  };

  const logout = async () => {
    try {
      if (token.value) {
        await api.post('auth/logout');
      }
    } catch (err) {
      console.error('Logout error:', err);
    } finally {
      clearAuth();
    }
  };

  const checkAuth = async () => {
    if (!token.value) {
      isInitialized.value = true;
      return;
    }
    
    try {
      api.defaults.headers.common['Authorization'] = `Bearer ${token.value}`;
      const response = await api.get('auth/me');
      
      // Flexible check for response data
      const userData = response.data?.data?.user || response.data?.user || response.data;
      
      if (userData && (userData.id || userData.uuid || userData.email)) {
        user.value = userData;
        localStorage.setItem('user', JSON.stringify(user.value));
      } else {
        console.error('CheckAuth: Data structure mismatch. Response:', response.data);
        throw new Error('Invalid user data in response');
      }
    } catch (err) {
      console.error('CheckAuth failed:', err);
      // Limpa a autenticação se o servidor retornar erro (exceto erro de conexão)
      if (err.response) {
        clearAuth();
      }
    } finally {
      isInitialized.value = true;
    }
  };

  const refreshAccessToken = async () => {
    if (!refreshToken.value) {
      clearAuth();
      return false;
    }
    
    try {
      const response = await api.post('/oauth/token', {
        grant_type: 'refresh_token',
        refresh_token: refreshToken.value,
      });
      
      setAuth(response.data);
      return true;
    } catch (err) {
      clearAuth();
      return false;
    }
  };

  const setup2FA = async () => {
    try {
      const response = await api.get('/api/2fa/setup');
      return response.data;
    } catch (err) {
      throw err;
    }
  };

  const confirm2FA = async (code) => {
    try {
      const response = await api.post('/api/2fa/confirm', { code });
      return response.data;
    } catch (err) {
      throw err;
    }
  };

  return {
    user,
    token,
    refreshToken,
    loading,
    isInitialized,
    error,
    isAuthenticated,
    isAdminLevel,
    isStaffLevel,
    isRegularUser,
    userUuid,
    login,
    register,
    logout,
    checkAuth,
    refreshAccessToken,
    setup2FA,
    confirm2FA,
  };
});
