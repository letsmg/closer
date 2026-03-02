# Laravel Dating API Backend – Exemplo Educacional

![Badge em Desenvolvimento](https://img.shields.io/static/v1?label=STATUS&message=EM%20DESENVOLVIMENTO&color=ORANGE&style=for-the-badge)
[![Badge Licença](https://img.shields.io/badge/LICENÇA-CC%20BY--NC--ND%204.0-purple?logo=Creative+Commons&logoColor=white&style=for-the-badge)](https://creativecommons.org/licenses/by-nc-nd/4.0/deed.pt-br)
![Badge Tecnologias](https://img.shields.io/static/v1?label=TECNOLOGIAS&message=Laravel%20%7C%20Sanctum%20%7C%20MySQL%20%7C%20PHP%20%7C%20API%20REST&color=blue&style=for-the-badge)

## Descrição

Este repositório é um **exemplo educacional completo** de um **backend API RESTful moderno construído com Laravel** (versão 11+), projetado especificamente para suportar um aplicativo de relacionamentos (dating app / matchmaking).

**Objetivo principal**:  
Demonstrar boas práticas de desenvolvimento de APIs seguras e escaláveis com Laravel, incluindo autenticação via tokens (Sanctum), CRUD de perfis/usuários, matching básico, likes/swipes, mensagens, geolocalização simples e mais.  
Tudo pensado para ser usado como material de **estudo e aprendizado** por desenvolvedores que querem entender como construir o backend de um app mobile/web de relacionamentos moderno.

**Importante**: Este projeto é **exclusivamente para fins educacionais e de estudo**.  
**Proibido o uso comercial, monetização, deploy em produção para fins lucrativos ou qualquer forma de exploração econômica**. Veja a seção de Licença para detalhes.

Repositório: https://github.com/letsmg/closer

## Funcionalidades Implementadas (ou em progresso)

- Autenticação via API com **Laravel Sanctum** (tokens pessoais, login/register com verificação de email opcional)
- CRUD completo de perfis de usuários (idade, gênero, preferências, bio, fotos, localização)
- Sistema de likes / swipes / matches (lógica básica de matching mútuo)
- Endpoint de mensagens entre matches (com timestamps e status lido/não lido)
- Filtros avançados (idade, distância, interesses)
- Upload de múltiplas fotos com validação e storage (S3/local)
- Rate limiting e proteção contra abuso (ex: limite de swipes por dia)
- Documentação básica de rotas com exemplos (Postman collection ou Swagger-like)

## Funcionalidades Pendentes (ideias para estudo)

- Integração com geolocalização real (ex: Google Maps API ou Haversine)
- Notificações push (Firebase ou Laravel Echo + Pusher)
- Sistema de bloqueio/report
- Algoritmo de recomendação simples (baseado em preferências)
- Testes automatizados (Pest/PHPUnit)
- Deploy com Docker + CI/CD

## Tecnologias Utilizadas

- **Framework**: Laravel 11+
- **Autenticação API**: Laravel Sanctum (recomendado para SPAs/mobile)
- **Banco de Dados**: MySQL / MariaDB (com Eloquent ORM)
- **Outros**: PHP 8.2+, Composer, Laravel Breeze/Sanctum starter, Laravel Sanctum para tokens
- **Ferramentas**: Postman/Insomnia para testes de API, Git

## Pré-requisitos

- PHP 8.2+
- Composer
- MySQL 8+ ou MariaDB
- Node.js + npm (opcional, para frontend de teste se quiser)
- Laravel Installer (opcional)

## Instalação

1. Clone o repositório:
   ```bash
   git clone https://github.com/[seu-usuario]/seu-repo-dating-api.git
   cd seu-repo-dating-api