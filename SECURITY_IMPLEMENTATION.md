# Implementação de Segurança - Closer API

## Resumo das Melhorias de Segurança

### 1. Autenticação JWT + OAuth2 Completo ✅

#### Short-lived Access Tokens (15 minutos)
```php
// Access Token: Válido por 15 minutos
$accessToken = JWTAuth::claims([
    'scopes' => ['read:profile', 'write:messages'],
    'token_type' => 'access_token',
])->fromUser($user);
```

#### Long-lived Refresh Tokens (30 dias com rotação)
```php
// Refresh Token: Válido por 30 dias, rotacionado a cada uso
$refreshToken = RefreshToken::generate(
    $user,
    ['read:profile'],
    $request->ip(),
    $request->userAgent()
);
```

**Benefícios de segurança:**
- Se access token for interceptado, só é válido por 15 min
- Refresh tokens são rotacionados (nunca reusados)
- Detecção de roubo via "token families"
- Revogação imediata possível

### 2. Escopos OAuth2 Granulares ✅

```php
const SCOPES = [
    'read:profile' => 'Ler dados do perfil',
    'write:profile' => 'Modificar perfil',
    'read:feed' => 'Acessar feed',
    'write:interactions' => 'Like, dislike, match',
    'read:messages' => 'Ler mensagens',
    'write:messages' => 'Enviar mensagens',
    'read:matches' => 'Ver matches',
    'write:photos' => 'Upload de fotos',
    'read:shorts' => 'Ver shorts',
    'write:premium' => 'Ações premium',
    'admin:users' => 'Admin (nível 3)',
];
```

**Uso:**
```php
Route::middleware(['auth:api', 'scope:read:profile,write:messages'])
    ->get('/chat/{matchId}', [ChatController::class, 'show']);
```

### 3. Ofuscação de IDs com ULIDs ✅

```php
// Antes (inseguro): /api/users/123
// Depois (seguro): /api/users/01HABDT14A2B3C4D5E6F7G8H9

class User extends Authenticatable
{
    use HasUlid; // Trait implementada
    
    // ULID gerado automaticamente
    // Rota usa UUID ao invés de ID numérico
}
```

**Proteção:**
- Impede enumeração de usuários (`/users/1`, `/users/2`, etc.)
- IDs incrementais nunca expostos na API
- ULIDs são sortable (útil para paginação)

### 4. Argon2id para Hash de Senhas ✅

```php
// config/hashing.php
'driver' => 'argon2id',
'argon2id' => [
    'memory' => 65536,  // 64 MB
    'threads' => 3,
    'time' => 4,        // 4 iterações
],
```

**Benefícios:**
- Vencedor do Password Hashing Competition 2015
- Resistente a ataques de GPU
- Mais seguro que bcrypt para hardware moderno

### 5. Redis para Cache e Sessões ✅

```yaml
# docker-compose.yml
redis:
  image: redis:7-alpine
  command: redis-server --appendonly yes --requirepass secret
```

**Configuração:**
```env
CACHE_DRIVER=redis
SESSION_DRIVER=redis
REDIS_HOST=redis
REDIS_PASSWORD=secret
REDIS_PORT=6379
```

### 6. Infraestrutura Docker Completa ✅

```yaml
# docker-compose.yml inclui:
- PHP 8.3-FPM + Nginx
- PostgreSQL 16 (produção)
- Redis 7 (cache/sessões)
- RabbitMQ 3 (filas)
- Kafka + Zookeeper (eventos)
- Workers e Schedulers
```

**Comandos:**
```bash
# Iniciar toda a stack
docker-compose up -d

# Acessar aplicação
http://localhost:8000

# RabbitMQ Management
http://localhost:15672 (closer/secret)

# Kafka UI
http://localhost:8080
```

### 7. Moderação de Imagens (Google Vision) ✅

```php
$service = new ImageModerationService();
$result = $service->analyze('/path/to/image.jpg');

// Resultado
[
    'is_safe' => false,
    'adult' => 'LIKELY',
    'violence' => 'VERY_UNLIKELY',
    'racy' => 'POSSIBLE',
]
```

**Configuração:**
```env
IMAGE_MODERATION_PROVIDER=google
GOOGLE_VISION_API_KEY=your_api_key
```

### 8. Headers de Segurança (Nginx) ✅

```nginx
add_header X-Frame-Options "SAMEORIGIN" always;
add_header X-Content-Type-Options "nosniff" always;
add_header X-XSS-Protection "1; mode=block" always;
add_header Referrer-Policy "strict-origin-when-cross-origin" always;
add_header Content-Security-Policy "default-src 'self'..." always;
```

---

## Endpoints OAuth2

### Token Endpoint
```http
POST /api/oauth/token
Content-Type: application/json

{
  "grant_type": "password",
  "username": "user@example.com",
  "password": "secret",
  "scope": "read:profile write:messages"
}
```

**Resposta:**
```json
{
  "access_token": "eyJ0eX...",
  "token_type": "Bearer",
  "expires_in": 900,
  "refresh_token": "abc123...",
  "refresh_expires_in": 2592000,
  "scope": "read:profile write:messages",
  "user": {
    "uuid": "01HABDT14A2B3C4D5E6F7G8H9",
    "name": "João Silva",
    "email": "user@example.com"
  }
}
```

### Refresh Token
```http
POST /api/oauth/token
Content-Type: application/json

{
  "grant_type": "refresh_token",
  "refresh_token": "abc123...",
  "scope": "read:profile"
}
```

### Revoke Token
```http
POST /api/oauth/revoke
Authorization: Bearer {access_token}

{
  "token": "abc123...",
  "token_type_hint": "refresh_token"
}
```

---

## Checklist de Segurança para Flutter

### Armazenamento de Tokens
```dart
// Use flutter_secure_storage (Keychain/Keystore)
import 'package:flutter_secure_storage/flutter_secure_storage.dart';

final storage = FlutterSecureStorage();
await storage.write(key: 'access_token', value: token);
await storage.write(key: 'refresh_token', value: refreshToken);
```

### Renew automático de tokens
```dart
class AuthInterceptor extends Interceptor {
  @override
  void onError(DioError err, ErrorInterceptorHandler handler) async {
    if (err.response?.statusCode == 401) {
      // Token expirado, tenta refresh
      final newToken = await refreshToken();
      // Retry request com novo token
    }
    handler.next(err);
  }
}
```

### Pinning de Certificados (Produção)
```dart
// Evita MITM attacks
dio = Dio()
  ..httpClientAdapter = IOHttpClientAdapter(
    onHttpClientCreate: (client) {
      client.badCertificateCallback = (cert, host, port) {
        // Verifica certificado contra hash conhecido
        return cert.pem == trustedCert;
      };
    },
  );
```

---

## Variáveis de Ambiente (.env)

```env
# JWT
JWT_SECRET=your-secret-key
JWT_TTL=15          # Access token: 15 minutos
JWT_REFRESH_TTL=43200  # Refresh: 30 dias

# Database (PostgreSQL)
DB_CONNECTION=pgsql
DB_HOST=postgres
DB_PORT=5432
DB_DATABASE=closer
DB_USERNAME=closer
DB_PASSWORD=secret

# Redis
REDIS_HOST=redis
REDIS_PASSWORD=secret
REDIS_PORT=6379

# RabbitMQ
RABBITMQ_HOST=rabbitmq
RABBITMQ_PORT=5672
RABBITMQ_USER=closer
RABBITMQ_PASSWORD=secret

# Kafka
KAFKA_BROKERS=kafka:9092

# Image Moderation
IMAGE_MODERATION_PROVIDER=google
GOOGLE_VISION_API_KEY=your-api-key

# Queue
QUEUE_CONNECTION=rabbitmq
CACHE_DRIVER=redis
SESSION_DRIVER=redis
```

---

## Testes de Segurança

```bash
# Executar testes de autenticação
php artisan test --filter=JwtAuthTest

# Testar vulnerabilidades de injeção
./vendor/bin/psalm --taint-analysis

# Verificar dependências vulneráveis
composer audit
```

---

## Recomendações para Produção

1. **HTTPS obrigatório** - Nunca use HTTP em produção
2. **Rate Limiting** - Já implementado no Laravel (`throttle`)
3. **WAF** - Cloudflare ou AWS WAF recomendado
4. **Monitoramento** - Sentry para erros, Laravel Telescope para debug
5. **Backups** - PostgreSQL backups diários
6. **Logs** - Centralize logs em Elasticsearch/Splunk
7. **Secrets Management** - Use AWS Secrets Manager ou HashiCorp Vault
8. **CORS restrito** - Configure apenas domínios permitidos

---

## Próximos Passos Sugeridos

1. **Implementar Device Fingerprinting** - Detectar login de novos dispositivos
2. **2FA (TOTP)** - Autenticação de dois fatores
3. **Biometria** - Face ID / Touch ID no Flutter
4. **Audit Logging** - Log de todas as ações sensíveis
5. **Data Encryption at Rest** - Criptografar dados sensíveis no DB
6. **API Rate Limiting por usuário** - Não só por IP

---

## Documentação Adicional

- `API_AUTH_GUIDE.md` - Guia completo para Flutter
- `docker-compose.yml` - Configuração completa Docker
- `app/Services/ImageModerationService.php` - Moderação de imagens
- `app/Http/Controllers/Api/OAuth2Controller.php` - OAuth2 completo
- `app/Traits/HasUlid.php` - ULID implementation
