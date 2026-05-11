# Task Progress - Closer App

## ✅ 1. Storage Link & Logo
- [x] Verificar se o symlink public/storage existe
- [x] Recriar o symlink public/storage
- [x] Verificar se logo.png existe em storage/app/public

## ✅ 2. Login Screen com Logo do Closer
- [x] Adicionar logo do Closer na tela de login
- [x] Adicionar logo no AdminNavigationMenu
- [x] Adicionar logo no Discover.vue (header)

## ✅ 3. Dashboard Staff
- [x] DashboardView.vue já existe e funcional
- [x] AdminNavigationMenu.vue já existe e funcional

## ✅ 4. Dashboard Customer Logado
- [x] Discover.vue já existe como tela principal

## ✅ 5. Campo Bio com limite 250 caracteres
- [x] Verificar migration - biography já existe como text
- [x] Adicionar validação de 250 chars no model Profile (sanitizeBio)
- [x] Adicionar validação no frontend (line-clamp-2 no SwipeProfileCard)

## ✅ 6. Campo Interests (até 8 hobbies)
- [x] Verificar tabela hobbies já existe
- [x] Verificar tabela profile_hobbies já existe
- [x] Verificar se há seed de hobbies
- [x] Adicionar campo interested_hobbies no ProfilePreference
- [x] Criar migration 2026_05_11_000003_add_interests_filter_to_preferences
- [x] Adicionar validação de até 8 interesses na trait HasSanitization

## ✅ 7. Campo Contact Methods (até 3 apps)
- [x] Migration já adiciona contact_methods (JSON)
- [x] Adicionar validação no model Profile (casts para array)
- [x] Adicionar lista pré-programada de apps de contato na trait HasSanitization
- [x] Adicionar fillable contact_methods no Profile

## ✅ 8. Verificar Modelo de Negócios (UserLevel)
- [x] Ajustar UserLevel FREE: 70 likes/dia, 0 mensagens sem match
- [x] Ajustar UserLevel PLUS: likes ilimitados, 10 msg/dia sem match, R$15
- [x] Ajustar UserLevel PREMIUM: likes ilimitados, 50 msg/dia sem match, R$19,90
- [x] Adicionar canSendMessagesWithoutMatch()
- [x] Adicionar canBlockRegion()
- [x] Adicionar canHideLocation()
- [x] Adicionar canViewLikes() (ver quem curtiu)
- [x] Adicionar canBeInvisible()
- [x] Adicionar métodos correspondentes no User model

## ✅ 9. DiscoveryController - Ordem de Exibição
- [x] Filtrar apenas ativos e não invisíveis
- [x] Região de interesse com raio (max 200km)
- [x] Excluir bloqueados
- [x] Ordem por sexo/orientação compatível
- [x] Idade (18-85)
- [x] Interesses iguais (filtro por hobbies em comum)
- [x] Aviso quando esgotar perfis (exhausted + message + suggestion)
- [x] Middleware CheckDailyLimits para likes e mensagens
- [x] Incrementar contador de likes no like()

## ✅ 10. Sanitização e Validação
- [x] SanitizeInput middleware (strip_tags + trim) já existe e está ativo
- [x] SanitizedRequest base class já existe
- [x] SanitizesOutput trait (htmlspecialchars) já existe
- [x] HasSanitization trait criada com métodos adicionais
- [x] Validação frontend via Vue (v-model + required)

## ✅ 11. Perfil Verificado
- [x] Migration já adiciona is_verified e verified_at
- [x] Adicionar casts boolean no Profile
- [x] Adicionar selo de verificado no SwipeProfileCard
- [x] Adicionar selo de verificado no Discover.vue (via SwipeProfileCard)
- [x] Adicionar método isVerified() no Profile model