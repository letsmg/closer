
import { createRouter, createWebHistory } from 'vue-router';
import { useAuthStore } from '../stores/auth';

const routes = [
  {
    path: '/admin/usuarios/editar/:id',
    name: 'admin-user-edit',
    component: () => import('../pages/admin/UserEdit.vue'),
    meta: { requiresAuth: true, requiresStaff: true },
  },
  {
    path: '/admin/usuarios/novo',
    name: 'admin-user-create',
    component: () => import('../pages/admin/UserCreate.vue'),
    meta: { requiresAuth: true, requiresStaff: true },
  },
  {
    path: '/',
    name: 'home',
    component: () => import('../pages/Discover.vue'),
    meta: { requiresAuth: true, requiresCommon: true },
  },
  {
    path: '/discover',
    name: 'discover',
    component: () => import('../pages/Discover.vue'),
    meta: { requiresAuth: true, requiresCommon: true },
  },
  {
    path: '/login',
    name: 'login',
    component: () => import('../pages/Login.vue'),
    meta: { guest: true },
  },
  {
    path: '/register',
    name: 'register',
    component: () => import('../pages/Register.vue'),
    meta: { guest: true },
  },
  {
    path: '/2fa',
    name: 'two-factor',
    component: () => import('../pages/TwoFactor.vue'),
    meta: { requiresAuth: true, requiresCommon: true },
  },
  {
    path: '/feed',
    name: 'feed',
    component: () => import('../pages/Feed.vue'),
    meta: { requiresAuth: true, requiresCommon: true },
  },
  {
    path: '/profile',
    name: 'profile',
    component: () => import('../pages/Profile.vue'),
    meta: { requiresAuth: true, requiresCommon: true },
  },
  {
    path: '/matches',
    name: 'matches',
    component: () => import('../pages/Matches.vue'),
    meta: { requiresAuth: true, requiresCommon: true },
  },
  {
    path: '/chat/:id',
    name: 'chat',
    component: () => import('../pages/Chat.vue'),
    meta: { requiresAuth: true, requiresCommon: true },
  },
  {
    path: '/admin',
    redirect: '/admin/dashboard',
  },
  {
    path: '/admin/dashboard',
    name: 'admin-dashboard',
    component: () => import('../pages/admin/DashboardView.vue'),
    meta: { requiresAuth: true, requiresStaff: true },
  },
  {
    path: '/admin/users',
    name: 'admin-users',
    component: () => import('../pages/admin/UsersView.vue'),
    meta: { requiresAuth: true, requiresStaff: true },
  },
  {
    path: '/admin/denuncias',
    name: 'admin-denuncias',
    component: () => import('../pages/admin/DenunciasView.vue'),
    meta: { requiresAuth: true, requiresStaff: true },
  },
  {
    path: '/admin/reports/users',
    name: 'admin-reports-users',
    component: () => import('../pages/admin/DashboardView.vue'), // Placeholder
    meta: { requiresAuth: true, requiresStaff: true },
  },
  {
    path: '/admin/analytics',
    name: 'admin-analytics',
    component: () => import('../pages/admin/DashboardView.vue'), // Placeholder
    meta: { requiresAuth: true, requiresStaff: true },
  },
  {
    path: '/admin/logs',
    name: 'admin-logs',
    component: () => import('../pages/admin/DashboardView.vue'), // Placeholder
    meta: { requiresAuth: true, requiresStaff: true },
  },
  {
    path: '/:pathMatch(.*)*',
    redirect: '/',
  },
];

const router = createRouter({
  history: createWebHistory(),
  routes,
});

// Navigation guards
router.beforeEach(async (to, from, next) => {
  const authStore = useAuthStore();
  
  // Wait for auth to be initialized if we have a token but no user yet
  // or if we haven't checked with the server yet
  if (!authStore.isInitialized && authStore.token) {
    await authStore.checkAuth();
  } else if (!authStore.token) {
    authStore.isInitialized = true;
  }
  
  const isAuthenticated = authStore.isAuthenticated;
  const isStaff = authStore.isStaffLevel;

  // 1. Unauthenticated users trying to access protected routes
  if (to.meta.requiresAuth && !isAuthenticated) {
    next({ name: 'login' });
    return;
  }

  // 2. Authenticated users trying to access guest routes (Login/Register)
  if (to.meta.guest && isAuthenticated) {
    if (isStaff) {
      next({ name: 'admin-dashboard' });
    } else {
      next({ name: 'home' });
    }
    return;
  }

  // 3. Staff trying to access common user areas
  if (to.meta.requiresCommon && isStaff) {
    next({ name: 'admin-dashboard' });
    return;
  }

  // 4. Common users trying to access staff areas
  if (to.meta.requiresStaff && !isStaff) {
    next({ name: 'home' });
    return;
  }
  
  // Final fallback for protected routes
  if (to.meta.requiresAuth && !authStore.isAuthenticated) {
    next({ name: 'login' });
    return;
  }
  
  next();
});

export default router;
