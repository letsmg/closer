<?php

/**
 * Cloudflare WAF e Security Configuration
 * 
 * NOTA: Esta configuração é usada APENAS em produção.
 * Local/Docker não requerem Cloudflare.
 * 
 * Para ativar:
 * 1. Configure seu domínio no Cloudflare
 * 2. Adicione as regras de firewall abaixo no dashboard do Cloudflare
 * 3. Defina CF_API_TOKEN e CF_ZONE_ID no .env
 */

return [
    /*
    |--------------------------------------------------------------------------
    | API Credentials
    |--------------------------------------------------------------------------
    |
    | Token de API do Cloudflare com permissões:
    * - Zone:Read, Zone Settings:Edit, Firewall Services:Edit
    |
    */
    
    'api_token' => env('CF_API_TOKEN'),
    'zone_id' => env('CF_ZONE_ID'),
    'account_id' => env('CF_ACCOUNT_ID'),

    /*
    |--------------------------------------------------------------------------
    | Security Level
    |--------------------------------------------------------------------------
    |
    | essentially_off: Apenas ameaças DDoS
    | low: Desafio para visitantes suspeitos
    | medium: Desafio para ameaças moderadas (padrão)
    | high: Desafio para todos os visitantes
    | under_attack: Modo I'm Under Attack (JS challenge)
    |
    */
    
    'security_level' => env('CF_SECURITY_LEVEL', 'medium'),

    /*
    |--------------------------------------------------------------------------
    | Challenge TTL
    |--------------------------------------------------------------------------
    |
    | Tempo (minutos) que um visitante passa em challenge antes de ser
    | bloqueado se falhar.
    |
    */
    
    'challenge_ttl' => 30,

    /*
    |--------------------------------------------------------------------------
    | WAF Rules (Regras de Firewall)
    |--------------------------------------------------------------------------
    |
    | Estas regras são aplicadas automaticamente quando você executa:
    | php artisan cloudflare:setup-waf
    |
    */
    
    'waf_rules' => [
        // Bloco 1: Rate Limiting por IP ( complementa o do Laravel )
        [
            'description' => 'Rate Limit: API endpoints',
            'expression' => '(http.request.uri.path contains "/api/")',
            'action' => 'rate_limit',
            'action_parameters' => [
                'requests_per_period' => 100,
                'period' => 60, // 1 minuto
                'mitigation_timeout' => 600, // 10 minutos de timeout
            ],
        ],
        
        // Bloco 2: Bloquear países de alto risco (opcional)
        // [
        //     'description' => 'Block high-risk countries',
        //     'expression' => '(ip.geoip.country in {"CN" "RU" "KP"})',
        //     'action' => 'block',
        // ],
        
        // Bloco 3: Proteção contra bots
        [
            'description' => 'Bot Fight Mode: API',
            'expression' => '(http.request.uri.path contains "/api/") and (cf.bot_management.score lt 30)',
            'action' => 'managed_challenge',
        ],
        
        // Bloco 4: Proteger login contra brute force
        [
            'description' => 'Protect: Login endpoints',
            'expression' => '(http.request.uri.path contains "/login") or (http.request.uri.path contains "/oauth/token")',
            'action' => 'rate_limit',
            'action_parameters' => [
                'requests_per_period' => 5,
                'period' => 60,
                'mitigation_timeout' => 1800, // 30 min
            ],
        ],
        
        // Bloco 5: Bloquear User-Agents maliciosos
        [
            'description' => 'Block: Bad User-Agents',
            'expression' => '(http.user_agent contains "sqlmap") or (http.user_agent contains "nikto") or (http.user_agent contains "nmap")',
            'action' => 'block',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Page Rules
    |--------------------------------------------------------------------------
    |
    | Configurações de cache e segurança por URL
    |
    */
    
    'page_rules' => [
        [
            'description' => 'API: No caching',
            'target' => 'api.closer.com/*',
            'actions' => [
                'cache_level' => 'bypass',
                'ssl' => 'strict',
            ],
        ],
        [
            'description' => 'Static assets: Aggressive caching',
            'target' => 'closer.com/static/*',
            'actions' => [
                'cache_level' => 'cache_everything',
                'browser_cache_ttl' => 86400, // 1 dia
                'edge_cache_ttl' => 86400,
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | DDoS Protection
    |--------------------------------------------------------------------------
    |
    | Configurações de proteção DDoS
    |
    */
    
    'ddos_protection' => [
        'mode' => 'advanced', // basic, advanced, or essential
        'sensitivity' => 'high', // low, medium, high
    ],

    /*
    |--------------------------------------------------------------------------
    | Bot Management
    |--------------------------------------------------------------------------
    |
    | Requer plano Pro ou superior
    |
    */
    
    'bot_management' => [
        'fight_mode' => true,
        'auto_update_model' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Always Online
    |--------------------------------------------------------------------------
    |
    | Serve páginas cacheadas se o servidor estiver offline
    |
    */
    
    'always_online' => true,

    /*
    |--------------------------------------------------------------------------
    | Automatic HTTPS Rewrites
    |--------------------------------------------------------------------------
    */
    
    'automatic_https_rewrites' => true,

    /*
    |--------------------------------------------------------------------------
    | Brotli Compression
    |--------------------------------------------------------------------------
    */
    
    'brotli' => 'on',

    /*
    |--------------------------------------------------------------------------
    | Minimum TLS Version
    |--------------------------------------------------------------------------
    |
    | 1.0, 1.1, 1.2, 1.3
    |
    */
    
    'min_tls_version' => '1.2',

    /*
    |--------------------------------------------------------------------------
    | Security Headers
    |--------------------------------------------------------------------------
    |
    | Cloudflare pode adicionar headers de segurança automaticamente
    |
    */
    
    'security_headers' => [
        'strict_transport_security' => [
            'enabled' => true,
            'max_age' => 31536000, // 1 ano
            'include_subdomains' => true,
            'preload' => true,
        ],
    ],
];
