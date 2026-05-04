# Vue.js Frontend Setup - Closer

## Estrutura Vue.js Implementada

### Arquitetura
```
resources/js/
├── api/
│   └── index.js              # Axios com interceptores
├── components/               # Componentes reutilizáveis
├── pages/                    # Páginas/Routes
│   ├── App.vue              # Root component
│   ├── Login.vue            # Login com OAuth2
│   ├── Register.vue         # Cadastro
│   ├── Home.vue             # Dashboard
│   ├── Feed.vue             # Feed de perfis
│   ├── Profile.vue          # Perfil do usuário
│   ├── Matches.vue          # Lista de matches
│   ├── Chat.vue             # Chat
│   ├── TwoFactor.vue        # Configuração 2FA
│   └── NotFound.vue         # 404
├── router/
│   └── index.js             # Vue Router config
├── stores/
│   └── auth.js              # Pinia store (auth)
├── app.js                   # Entry point
└── bootstrap.js             # Bootstrap
```

## Dependências Instaladas

### Package.json atualizado:
```json
{
  "dependencies": {
    "vue": "^3.4.0",
    "vue-router": "^4.2.0",
    "pinia": "^2.1.0",
    "@vueuse/core": "^10.5.0",
    "axios": "^1.11.0",
    "qrcode.vue": "^3.4.0"
  },
  "devDependencies": {
    "@vitejs/plugin-vue": "^5.0.0",
    "typescript": "^5.3.0",
    "vue-tsc": "^1.8.0"
  }
}
```

## Instalação

```bash
# Instalar dependências
npm install

# Compilar assets
npm run build

# Modo desenvolvimento
npm run dev
```

## Recursos Implementados

### 1. Autenticação JWT + OAuth2
- Login com password grant
- Refresh token automático
- Logout
- Rotas protegidas

### 2. 2FA (Two-Factor Authentication)
- Setup de TOTP
- QR Code para scan
- Códigos de backup
- Verificação em login

### 3. Device Fingerprinting
- Detecção de novos dispositivos
- Email de notificação
- Lista de dispositivos conhecidos

### 4. Security Headers
- Proteção XSS
- Content Security Policy
- CSRF protection

## Fluxo de Autenticação

```
┌─────────┐     POST /oauth/token     ┌─────────┐
│  Login  │ ──────────────────────────▶ │  API    │
│  Vue    │                           │ Laravel │
└─────────┘ ◀──────────────────────────┘         │
     │              {access_token,              │
     │               refresh_token,               │
     │               user}                        │
     │                                            │
     ▼                                            │
┌─────────────┐                                   │
│ localStorage│                                   │
│  ├─ token   │                                   │
│  ├─ refresh │                                   │
│  └─ user    │                                   │
└─────────────┘                                   │
                                                  │
┌─────────────┐     GET /api/auth/me              │
│  Axios      │ ───────────────────────────────▶  │
│ Interceptor │                                   │
│             │ ◀──────────────────────────────   │
└─────────────┘                                   │
     │                                            │
     │ 401?                                       │
     ▼                                            │
POST /oauth/token (refresh) ───────────────────▶   │
                                                  │
```

## Componentes Vue

### Login.vue
- Formulário de login
- Suporte a 2FA
- Validação em tempo real
- Loading states

### TwoFactor.vue
- Ativar/desativar 2FA
- Exibir QR Code
- Códigos de backup
- Verificação de código

### Home.vue
- Dashboard do usuário
- Info de segurança
- Navegação principal

## Configuração Vite

```javascript
// vite.config.js
export default defineConfig({
  plugins: [
    laravel({
      input: ['resources/css/app.css', 'resources/js/app.js'],
    }),
    tailwindcss(),
    vue(),
  ],
  resolve: {
    alias: {
      '@': '/resources/js',
      '@components': '/resources/js/components',
      '@pages': '/resources/js/pages',
      '@stores': '/resources/js/stores',
      '@api': '/resources/js/api',
    },
  },
});
```

## Rotas Vue Router

```javascript
const routes = [
  { path: '/', name: 'home', component: Home, meta: { requiresAuth: true } },
  { path: '/login', name: 'login', component: Login, meta: { guest: true } },
  { path: '/register', name: 'register', component: Register, meta: { guest: true } },
  { path: '/2fa', name: 'two-factor', component: TwoFactor, meta: { requiresAuth: true } },
  { path: '/feed', name: 'feed', component: Feed, meta: { requiresAuth: true } },
  { path: '/profile', name: 'profile', component: Profile, meta: { requiresAuth: true } },
  { path: '/matches', name: 'matches', component: Matches, meta: { requiresAuth: true } },
  { path: '/chat/:id', name: 'chat', component: Chat, meta: { requiresAuth: true } },
];
```

## Pinia Store (Auth)

```javascript
const authStore = useAuthStore();

// State
authStore.user          // Dados do usuário
authStore.token         // JWT token
authStore.refreshToken  // Refresh token
authStore.isAuthenticated

// Actions
await authStore.login({ email, password });
await authStore.register({ name, email, password });
await authStore.logout();
await authStore.refreshAccessToken();
```

## Estilização

- **Tailwind CSS v4** configurado
- Design responsivo
- Gradientes modernos
- Cards e sombras
- Estados de loading

## Próximos Passos

1. Implementar WebSocket para chat em tempo real
2. Adicionar upload de fotos com preview
3. Implementar feed infinito com scroll
4. Adicionar notificações push
5. Implementar PWA (Progressive Web App)

## API Integration

Todas as chamadas API usam:
- Base URL: `/api`
- Headers: `Accept: application/json`, `Authorization: Bearer {token}`
- Interceptor automático para refresh token
- Error handling consistente
