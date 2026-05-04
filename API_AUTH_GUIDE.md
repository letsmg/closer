# Guia de Autenticação - API Closer

## Resumo das Mudanças

O sistema agora usa **JWT (JSON Web Tokens)** com **Argon2id** para hash de senhas, compatível com Flutter, Mobile e Web.

---

## Configurações

### Argon2id (Hash de Senhas)
```php
// config/hashing.php
driver: 'argon2id'
memory: 65536 KB
threads: 3
time: 4 iterations
```

### JWT
```php
// config/jwt.php
ttl: 60 minutos (1 hora)
refresh_ttl: 20160 minutos (2 semanas)
algo: HS256
```

---

## Endpoints de Autenticação

### Para Flutter / Mobile / SPAs (JSON)

| Método | Endpoint | Descrição | Auth |
|--------|----------|-----------|------|
| POST | `/api/auth/register` | Cadastro de usuário | Pública |
| POST | `/api/auth/login` | Login | Pública |
| POST | `/api/auth/logout` | Logout | JWT |
| POST | `/api/auth/refresh` | Renovar token | JWT |
| GET | `/api/auth/me` | Dados do usuário | JWT |
| POST | `/api/auth/revoke-all` | Revogar todos tokens | JWT |

**Headers obrigatórios:**
```
Content-Type: application/json
Accept: application/json
Authorization: Bearer {seu_token_jwt}
```

### Para Web (Session)

| Método | Endpoint | Descrição | Auth |
|--------|----------|-----------|------|
| GET | `/login` | Formulário de login | Pública |
| POST | `/login` | Processa login | Pública |
| GET | `/register` | Formulário de cadastro | Pública |
| POST | `/register` | Processa cadastro | Pública |
| POST | `/logout` | Logout | Session |

---

## Exemplos de Uso (Flutter/Dart)

### 1. Login

```dart
import 'package:http/http.dart' as http;
import 'dart:convert';

class AuthService {
  final String baseUrl = 'http://seu-servidor.com/api';
  String? _token;

  Future<Map<String, dynamic>> login(String email, String password) async {
    final response = await http.post(
      Uri.parse('$baseUrl/auth/login'),
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
      },
      body: jsonEncode({
        'email': email,
        'password': password,
      }),
    );

    if (response.statusCode == 200) {
      final data = jsonDecode(response.body);
      _token = data['data']['access_token'];
      return data;
    } else {
      throw Exception('Login falhou: ${response.body}');
    }
  }

  Future<Map<String, dynamic>> register(
    String name,
    String email,
    String password,
    String passwordConfirmation,
  ) async {
    final response = await http.post(
      Uri.parse('$baseUrl/auth/register'),
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
      },
      body: jsonEncode({
        'name': name,
        'email': email,
        'password': password,
        'password_confirmation': passwordConfirmation,
      }),
    );

    if (response.statusCode == 201) {
      final data = jsonDecode(response.body);
      _token = data['data']['access_token'];
      return data;
    } else {
      throw Exception('Registro falhou: ${response.body}');
    }
  }

  Future<Map<String, dynamic>> getMe() async {
    final response = await http.get(
      Uri.parse('$baseUrl/auth/me'),
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'Authorization': 'Bearer $_token',
      },
    );

    if (response.statusCode == 200) {
      return jsonDecode(response.body);
    } else {
      throw Exception('Falha ao buscar usuário: ${response.body}');
    }
  }

  Future<void> logout() async {
    final response = await http.post(
      Uri.parse('$baseUrl/auth/logout'),
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'Authorization': 'Bearer $_token',
      },
    );

    if (response.statusCode == 200) {
      _token = null;
    }
  }

  Future<String> refreshToken() async {
    final response = await http.post(
      Uri.parse('$baseUrl/auth/refresh'),
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'Authorization': 'Bearer $_token',
      },
    );

    if (response.statusCode == 200) {
      final data = jsonDecode(response.body);
      _token = data['data']['access_token'];
      return _token!;
    } else {
      throw Exception('Falha ao renovar token');
    }
  }
}
```

### 2. Exemplo de Requisição Autenticada (Feed)

```dart
Future<Map<String, dynamic>> getFeed() async {
  final response = await http.get(
    Uri.parse('$baseUrl/feed'),
    headers: {
      'Content-Type': 'application/json',
      'Accept': 'application/json',
      'Authorization': 'Bearer $_token',
    },
  );

  if (response.statusCode == 200) {
    return jsonDecode(response.body);
  } else if (response.statusCode == 401) {
    // Token expirado, tenta renovar
    await refreshToken();
    return getFeed(); // Retry
  } else {
    throw Exception('Falha ao carregar feed');
  }
}
```

---

## Respostas da API

### Sucesso (Login)
```json
{
  "success": true,
  "message": "Login realizado com sucesso.",
  "data": {
    "access_token": "eyJ0eXAiOiJKV1QiLCJhbGc...",
    "token_type": "Bearer",
    "expires_in": 3600,
    "user": {
      "id": 1,
      "name": "João Silva",
      "email": "joao@email.com",
      "perfil": {...}
    }
  }
}
```

### Erro
```json
{
  "success": false,
  "message": "Credenciais inválidas.",
  "errors": null
}
```

### Erro de Validação
```json
{
  "success": false,
  "message": "Erros de validação.",
  "errors": {
    "email": ["The email field is required."],
    "password": ["The password must be at least 8 characters."]
  }
}
```

---

## Códigos de Status HTTP

| Código | Significado |
|--------|-------------|
| 200 | Sucesso |
| 201 | Criado com sucesso |
| 401 | Não autenticado / Token inválido |
| 403 | Proibido (conta desativada/email não verificado) |
| 404 | Não encontrado |
| 422 | Erro de validação |
| 429 | Muitas requisições (throttle) |
| 500 | Erro interno do servidor |

---

## Fluxo de Autenticação

```
┌─────────────┐     ┌─────────────┐     ┌─────────────┐
│   Flutter   │────▶│   /login    │────▶│    JWT      │
│    / Web    │     │   (JSON)    │     │   Token     │
└─────────────┘     └─────────────┘     └─────────────┘
                                               │
                                               ▼
                                        ┌─────────────┐
                                        │  Armazena   │
                                        │  Token em   │
                                        │   Secure    │
                                        │  Storage    │
                                        └─────────────┘
                                               │
                                               ▼
┌─────────────┐     ┌─────────────┐     ┌─────────────┐
│  Próximas   │◀────│  Bearer     │◀────│  Usa Token  │
│  Requisições│     │  Header     │     │  nas APIs   │
└─────────────┘     └─────────────┘     └─────────────┘
```

---

## Segurança

- **Argon2id**: Hash de senhas resistente a GPU cracking
- **JWT**: Tokens assinados digitalmente
- **Blacklist**: Tokens revogados são invalidados
- **TTL**: Tokens expiram após 1 hora (refresh em até 2 semanas)
- **HTTPS**: Sempre use HTTPS em produção
- **Secure Storage**: Em Flutter, use `flutter_secure_storage` para armazenar tokens

---

## Armazenamento Seguro em Flutter

```dart
import 'package:flutter_secure_storage/flutter_secure_storage.dart';

class TokenStorage {
  final _storage = FlutterSecureStorage();

  Future<void> saveToken(String token) async {
    await _storage.write(key: 'jwt_token', value: token);
  }

  Future<String?> getToken() async {
    return await _storage.read(key: 'jwt_token');
  }

  Future<void> deleteToken() async {
    await _storage.delete(key: 'jwt_token');
  }
}
```

---

## Rotas Protegidas Disponíveis

Após autenticação com JWT, você pode acessar todas as rotas em `routes/api.php`:

- `GET /api/feed` - Feed de perfis
- `POST /api/like/{perfil}` - Dar like
- `POST /api/dislike/{perfil}` - Dar dislike
- `GET /api/perfil` - Ver perfil
- `PUT /api/perfil` - Atualizar perfil
- `POST /api/fotos` - Upload de fotos
- `GET /api/chat/{matchId}` - Acessar chat
- E muitas outras...

---

## Testando com cURL

### Login
```bash
curl -X POST http://localhost:8000/api/auth/login \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{"email":"teste@email.com","password":"12345678"}'
```

### Acessar Feed
```bash
curl -X GET http://localhost:8000/api/feed \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -H "Authorization: Bearer SEU_TOKEN_AQUI"
```

---

## Suporte

Para mais informações:
- [JWT Auth Laravel](https://github.com/tymondesigns/jwt-auth)
- [Argon2](https://argon2-cffi.readthedocs.io/)
- [Laravel Authentication](https://laravel.com/docs/authentication)
