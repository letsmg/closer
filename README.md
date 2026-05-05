# Closer - Modern Dating App Platform

[![Development Status](https://img.shields.io/static/v1?label=STATUS&message=PRODUCTION%20READY&color=GREEN&style=for-the-badge)]
[![License](https://img.shields.io/badge/License-MIT-blue?style=for-the-badge)](https://opensource.org/licenses/MIT)
[![Laravel](https://img.shields.io/badge/Laravel-11-red?style=for-the-badge&logo=laravel)](https://laravel.com/)
[![Vue.js](https://img.shields.io/badge/Vue.js-3-green?style=for-the-badge&logo=vue.js)](https://vuejs.org/)

> **🌍 Language Versions:** [🇺🇸 English](#english) | [🇧🇷 Português](#português)

---

## 🏗️ Technology Stack & Libraries

### Backend
- **Framework**: Laravel 11+ (PHP 8.2+)
- **Database**: PostgreSQL 15+
- **Authentication**: Laravel Sanctum + Two-Factor Authentication
- **API**: RESTful API with comprehensive validation
- **Queue System**: Redis + Laravel Queues
- **File Storage**: Local/S3 compatible
- **Security**: Rate limiting, CORS, CSRF protection

### Frontend
- **Framework**: Vue.js 3 with Composition API
- **Build Tool**: Vite
- **UI Framework**: Tailwind CSS
- **State Management**: Pinia
- **Router**: Vue Router 4
- **HTTP Client**: Axios

### Additional Libraries
- **Image Processing**: Intervention Image
- **PDF Generation**: DomPDF
- **Email**: Laravel Mail + Markdown
- **Caching**: Redis
- **Logging**: Monolog + Custom Audit Logs
- **Testing**: PHPUnit + Pest

---

## 🚀 What Closer Does

### Core Features
- **🔐 Complete Authentication System**
  - Email verification
  - Two-factor authentication (2FA)
  - Password reset
  - Social login integration ready
  - Session management

- **👤 User Profile Management**
  - Complete profile creation
  - Photo uploads with validation
  - Preferences and interests
  - Geographic location (countries, states, cities)
  - Privacy settings

- **❤️ Matching & Interaction System**
  - Like/dislike mechanism
  - Mutual matching detection
  - Second chance feature
  - Real-time notifications
  - Message system between matches

- **🛡️ Safety & Moderation**
  - User blocking system
  - Content reporting
  - Content removal without notice
  - Automated abuse detection
  - Access history tracking

- **🌍 Geographic Targeting**
  - Southeast Asia focus (Singapore, Malaysia, Thailand, Indonesia, Philippines, Vietnam)
  - Middle East focus (UAE, Saudi Arabia, Qatar, Kuwait, Bahrain, Oman)
  - VPN detection and policy enforcement

- **⚖️ Legal Compliance**
  - Wyoming/Delaware jurisdiction
  - GDPR compliance
  - PDPA compliance (Southeast Asia)
  - Terms of service acceptance
  - Privacy policy implementation

### Advanced Features
- **🔍 Advanced Search & Filtering**
  - Age range filtering
  - Distance-based search
  - Interest matching
  - Language preferences
  - Custom preferences

- **📱 Mobile-Ready API**
  - RESTful endpoints
  - Pagination support
  - Rate limiting
  - File upload handling
  - Real-time capabilities

- **🔒 Enterprise Security**
  - Multi-factor authentication
  - Device fingerprinting
  - IP-based access control
  - Audit logging
  - Security headers implementation

---

## 🛠️ Installation & Setup

### Prerequisites
```bash
# Required Software
PHP 8.2+
PostgreSQL 15+
Composer 2.0+
Node.js 18+
npm 9+

# Required PHP Extensions
pdo_pgsql
mbstring
openssl
tokenizer
xml
gd
fileinfo
curl
```

### Quick Start

1. **Clone Repository**
```bash
git clone https://github.com/letsmg/closer.git
cd closer
```

2. **Install Dependencies**
```bash
# Backend dependencies
composer install

# Frontend dependencies
npm install
```

3. **Environment Configuration**
```bash
# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate

# Configure database in .env
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=closer
DB_USERNAME=postgres
DB_PASSWORD=your_password
```

4. **Database Setup**
```bash
# Create database
createdb closer

# Run migrations and seed
php artisan migrate:fresh --seed
```

5. **Frontend Build**
```bash
# Install and build assets
npm run build
```

6. **Start Development Server**
```bash
# Start Laravel server
php artisan serve

# Start Vite development server (in another terminal)
npm run dev
```

### Access Points
- **Frontend**: http://localhost:8000
- **API Documentation**: http://localhost:8000/api/documentation
- **Terms of Service**: http://localhost:8000/terms
- **Privacy Policy**: http://localhost:8000/privacy

---

## 📚 API Documentation

### Authentication Endpoints
```bash
POST /api/auth/register      # User registration
POST /api/auth/login         # User login
POST /api/auth/logout        # User logout
POST /api/auth/refresh       # Token refresh
POST /api/auth/2fa/setup    # Setup 2FA
POST /api/auth/2fa/verify    # Verify 2FA
```

### Profile Endpoints
```bash
GET    /api/profile          # Get current profile
PUT    /api/profile          # Update profile
POST   /api/profile/photos   # Upload photos
DELETE /api/profile/photos/{id} # Delete photo
```

### Matching Endpoints
```bash
POST /api/like/{userId}     # Like user
POST /api/dislike/{userId}  # Dislike user
GET  /api/matches           # Get matches
GET  /api/second-chances    # Get second chances
```

### Message Endpoints
```bash
GET    /api/messages/{matchId}  # Get conversation
POST   /api/messages/{matchId}  # Send message
PUT    /api/messages/{id}      # Mark as read
DELETE /api/messages/{id}      # Delete message
```

---

## 🔧 Configuration

### Environment Variables
```env
# Database
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=closer
DB_USERNAME=postgres
DB_PASSWORD=your_password

# Application
APP_NAME=Closer
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000

# Mail
MAIL_MAILER=smtp
MAIL_HOST=mailpit
MAIL_PORT=1025
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS="hello@closer.com"
MAIL_FROM_NAME="${APP_NAME}"

# Queue
QUEUE_CONNECTION=redis

# Cache
CACHE_DRIVER=redis

# File Storage
FILESYSTEM_DISK=local

# Security
SANCTUM_STATEFUL_DOMAINS=localhost:8000
```

### Default Admin Credentials
- **Email**: `admin@1.com`
- **Password**: `Mudar@123`

---

## 🧪 Testing

### Running Tests
```bash
# Run all tests
php artisan test

# Run specific test
php artisan test --filter UserTest

# Run with coverage
php artisan test --coverage
```

### API Testing with Postman
1. Import the Postman collection from `docs/closer-api.postman_collection.json`
2. Set environment variables:
   - `base_url`: `http://localhost:8000/api`
   - `token`: Your auth token from login response

---

## 🚀 Deployment

### Production Deployment
```bash
# Install production dependencies
composer install --no-dev --optimize-autoloader

# Optimize application
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Build frontend for production
npm run build

# Set permissions
chmod -R 755 storage
chmod -R 755 bootstrap/cache
```

### Docker Deployment
```bash
# Build and run with Docker
docker-compose up -d

# View logs
docker-compose logs -f
```

---

## 📊 Monitoring & Logging

### Available Logs
- **Application**: `storage/logs/laravel.log`
- **Audit**: Custom audit logging system
- **Security**: Security event tracking
- **Performance**: Request/response time logging

### Health Checks
- **Application Health**: `/up`
- **Database Health**: `/health/database`
- **Cache Health**: `/health/cache`
- **Queue Health**: `/health/queue`

---

## 🤝 Contributing

1. Fork the repository
2. Create feature branch (`git checkout -b feature/amazing-feature`)
3. Commit changes (`git commit -m 'Add amazing feature'`)
4. Push to branch (`git push origin feature/amazing-feature`)
5. Open Pull Request

### Code Style
- Follow PSR-12 coding standards
- Use Laravel conventions
- Add tests for new features
- Update documentation

---

## 📄 License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

---

## 🆘 Support

### Documentation
- [API Documentation](docs/api.md)
- [Frontend Guide](docs/frontend.md)
- [Deployment Guide](docs/deployment.md)

### Issues & Support
- [GitHub Issues](https://github.com/letsmg/closer/issues)
- [Discussions](https://github.com/letsmg/closer/discussions)
- [Wiki](https://github.com/letsmg/closer/wiki)

---

# 🇧🇷 Português

## 🏗️ Stack de Tecnologias e Bibliotecas

### Backend
- **Framework**: Laravel 11+ (PHP 8.2+)
- **Banco de Dados**: PostgreSQL 15+
- **Autenticação**: Laravel Sanctum + Autenticação de Dois Fatores
- **API**: API RESTful com validação completa
- **Sistema de Filas**: Redis + Laravel Queues
- **Armazenamento**: Local/Compatível com S3
- **Segurança**: Rate limiting, CORS, proteção CSRF

### Frontend
- **Framework**: Vue.js 3 com Composition API
- **Build Tool**: Vite
- **UI Framework**: Tailwind CSS
- **Gerenciamento de Estado**: Pinia
- **Router**: Vue Router 4
- **HTTP Client**: Axios

### Bibliotecas Adicionais
- **Processamento de Imagem**: Intervention Image
- **Geração de PDF**: DomPDF
- **Email**: Laravel Mail + Markdown
- **Cache**: Redis
- **Logging**: Monolog + Audit Logs Personalizados
- **Testes**: PHPUnit + Pest

---

## 🚀 O que o Closer Faz

### Funcionalidades Principais
- **🔐 Sistema Completo de Autenticação**
  - Verificação de email
  - Autenticação de dois fatores (2FA)
  - Redefinição de senha
  - Integração com login social pronta
  - Gerenciamento de sessão

- **👤 Gerenciamento de Perfil de Usuário**
  - Criação completa de perfil
  - Upload de fotos com validação
  - Preferências e interesses
  - Localização geográfica (países, estados, cidades)
  - Configurações de privacidade

- **❤️ Sistema de Matching e Interação**
  - Mecanismo de like/dislike
  - Detecção de matching mútuo
  - Recurso de segunda chance
  - Notificações em tempo real
  - Sistema de mensagens entre matches

- **🛡️ Segurança e Moderação**
  - Sistema de bloqueio de usuários
  - Denúncia de conteúdo
  - Remoção de conteúdo sem aviso prévio
  - Detecção automatizada de abuso
  - Rastreamento de histórico de acesso

- **🌍 Segmentação Geográfica**
  - Foco no Sudeste Asiático (Singapura, Malásia, Tailândia, Indonésia, Filipinas, Vietnã)
  - Foco no Oriente Médio (EAU, Arábia Saudita, Catar, Kuwait, Bahrein, Omã)
  - Detecção e aplicação de política de VPN

- **⚖️ Conformidade Legal**
  - Jurisdição de Wyoming/Delaware
  - Conformidade com GDPR
  - Conformidade com PDPA (Sudeste Asiático)
  - Aceitação de termos de serviço
  - Implementação de política de privacidade

---

## 🛠️ Instalação e Configuração

### Pré-requisitos
```bash
# Softwares Necessários
PHP 8.2+
PostgreSQL 15+
Composer 2.0+
Node.js 18+
npm 9+

# Extensões PHP Necessárias
pdo_pgsql
mbstring
openssl
tokenizer
xml
gd
fileinfo
curl
```

### Início Rápido

1. **Clonar Repositório**
```bash
git clone https://github.com/letsmg/closer.git
cd closer
```

2. **Instalar Dependências**
```bash
# Dependências do backend
composer install

# Dependências do frontend
npm install
```

3. **Configuração do Ambiente**
```bash
# Copiar arquivo de ambiente
cp .env.example .env

# Gerar chave da aplicação
php artisan key:generate

# Configurar banco de dados no .env
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=closer
DB_USERNAME=postgres
DB_PASSWORD=sua_senha
```

4. **Configuração do Banco de Dados**
```bash
# Criar banco de dados
createdb closer

# Executar migrações e seeders
php artisan migrate:fresh --seed
```

5. **Build do Frontend**
```bash
# Instalar e construir assets
npm run build
```

6. **Iniciar Servidor de Desenvolvimento**
```bash
# Iniciar servidor Laravel
php artisan serve

# Iniciar servidor de desenvolvimento Vite (outro terminal)
npm run dev
```

### Pontos de Acesso
- **Frontend**: http://localhost:8000
- **Documentação da API**: http://localhost:8000/api/documentation
- **Termos de Serviço**: http://localhost:8000/terms
- **Política de Privacidade**: http://localhost:8000/privacy

---

## 📚 Documentação da API

### Endpoints de Autenticação
```bash
POST /api/auth/register      # Registro de usuário
POST /api/auth/login         # Login de usuário
POST /api/auth/logout        # Logout de usuário
POST /api/auth/refresh       # Renovar token
POST /api/auth/2fa/setup    # Configurar 2FA
POST /api/auth/2fa/verify    # Verificar 2FA
```

### Endpoints de Perfil
```bash
GET    /api/profile          # Obter perfil atual
PUT    /api/profile          # Atualizar perfil
POST   /api/profile/photos   # Upload de fotos
DELETE /api/profile/photos/{id} # Excluir foto
```

### Endpoints de Matching
```bash
POST /api/like/{userId}     # Curtir usuário
POST /api/dislike/{userId}  # Não curtir usuário
GET  /api/matches           # Obter matches
GET  /api/second-chances    # Obter segundas chances
```

### Endpoints de Mensagens
```bash
GET    /api/messages/{matchId}  # Obter conversa
POST   /api/messages/{matchId}  # Enviar mensagem
PUT    /api/messages/{id}      # Marcar como lida
DELETE /api/messages/{id}      # Excluir mensagem
```

---

## 🔧 Configuração

### Variáveis de Ambiente
```env
# Banco de Dados
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=closer
DB_USERNAME=postgres
DB_PASSWORD=sua_senha

# Aplicação
APP_NAME=Closer
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000

# Email
MAIL_MAILER=smtp
MAIL_HOST=mailpit
MAIL_PORT=1025
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS="hello@closer.com"
MAIL_FROM_NAME="${APP_NAME}"

# Fila
QUEUE_CONNECTION=redis

# Cache
CACHE_DRIVER=redis

# Armazenamento de Arquivos
FILESYSTEM_DISK=local

# Segurança
SANCTUM_STATEFUL_DOMAINS=localhost:8000
```

### Credenciais Padrão de Admin
- **Email**: `admin@1.com`
- **Senha**: `Mudar@123`

---

## 🧪 Testes

### Executar Testes
```bash
# Executar todos os testes
php artisan test

# Executar teste específico
php artisan test --filter UserTest

# Executar com cobertura
php artisan test --coverage
```

### Testes de API com Postman
1. Importe a coleção do Postman de `docs/closer-api.postman_collection.json`
2. Configure variáveis de ambiente:
   - `base_url`: `http://localhost:8000/api`
   - `token`: Seu token de autenticação da resposta de login

---

## 🚀 Deploy

### Deploy em Produção
```bash
# Instalar dependências de produção
composer install --no-dev --optimize-autoloader

# Otimizar aplicação
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Build do frontend para produção
npm run build

# Configurar permissões
chmod -R 755 storage
chmod -R 755 bootstrap/cache
```

### Deploy com Docker
```bash
# Construir e executar com Docker
docker-compose up -d

# Visualizar logs
docker-compose logs -f
```

---

## 📊 Monitoramento e Logging

### Logs Disponíveis
- **Aplicação**: `storage/logs/laravel.log`
- **Auditoria**: Sistema de logging de auditoria personalizado
- **Segurança**: Rastreamento de eventos de segurança
- **Performance**: Logging de tempo de requisição/resposta

### Verificações de Saúde
- **Saúde da Aplicação**: `/up`
- **Saúde do Banco de Dados**: `/health/database`
- **Saúde do Cache**: `/health/cache`
- **Saúde da Fila**: `/health/queue`

---

## 🤝 Contribuindo

1. Fork do repositório
2. Criar branch de feature (`git checkout -b feature/amazing-feature`)
3. Commitar alterações (`git commit -m 'Adicionar feature incrível'`)
4. Push para o branch (`git push origin feature/amazing-feature`)
5. Abrir Pull Request

### Estilo de Código
- Seguir padrões PSR-12
- Usar convenções do Laravel
- Adicionar testes para novas funcionalidades
- Atualizar documentação

---

## 📄 Licença

Este projeto está licenciado sob a Licença MIT - veja o arquivo [LICENSE](LICENSE) para detalhes.

---

## 🆘 Suporte

### Documentação
- [Documentação da API](docs/api.md)
- [Guia do Frontend](docs/frontend.md)
- [Guia de Deploy](docs/deployment.md)

### Issues e Suporte
- [GitHub Issues](https://github.com/letsmg/closer/issues)
- [Discussões](https://github.com/letsmg/closer/discussions)
- [Wiki](https://github.com/letsmg/closer/wiki)