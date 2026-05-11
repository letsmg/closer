<?php

namespace App\Enums;

/**
 * Enum de Níveis de Acesso do Usuário
 * 
 * Define os níveis de acesso com seus respectivos privilégios
 * Usado em todo o sistema para controle de permissões
 */
enum UserLevel: int
{
    // ──────────────────────────────────────────────
    // NÍVEIS DE CONSUMIDOR (Customer)
    // ──────────────────────────────────────────────
    case FREE = 0;          // Level 0: Usuário padrão/gratuito
    case MODERATOR = 1;     // Level 1: Moderador (pode bloquear customers abaixo de level 4)
    case PLUS = 2;          // Level 2: Plus (pago básico)
    case PREMIUM = 3;       // Level 3: Premium (pago avançado)
    case COFOUNDER = 4;     // Level 4: Co-Founder (pode enviar solicitações diretas, filtrar níveis)
    case ELITE = 5;         // Level 5: Elite (acesso máximo entre customers, filtrar níveis)

    // ──────────────────────────────────────────────
    // NÍVEIS DE STAFF
    // ──────────────────────────────────────────────
    case ADMIN = 10;         // Level 10: Administrador do sistema
    case OPERATIONAL = 11;   // Level 11: Operacional (abaixo do admin)
    case SUPPORT = 12;       // Level 12: Suporte ao cliente

    /**
     * Retorna o nome amigável do nível
     */
    public function getName(): string
    {
        return match($this) {
            self::FREE => 'Free',
            self::MODERATOR => 'Moderador',
            self::PLUS => 'Plus',
            self::PREMIUM => 'Premium',
            self::COFOUNDER => 'Co-Founder',
            self::ELITE => 'Elite',
            self::ADMIN => 'Administrador',
            self::OPERATIONAL => 'Operacional',
            self::SUPPORT => 'Suporte',
        };
    }

    /**
     * Retorna a descrição do nível
     */
    public function getDescription(): string
    {
        return match($this) {
            self::FREE => 'Usuário gratuito com acesso básico',
            self::MODERATOR => 'Usuário moderador que pode bloquear acesso de outros customers',
            self::PLUS => 'Usuário Plus com recursos adicionais',
            self::PREMIUM => 'Usuário Premium com recursos avançados',
            self::COFOUNDER => 'Co-Founder com acesso a solicitações diretas',
            self::ELITE => 'Elite com acesso máximo entre os customers',
            self::ADMIN => 'Administrador com acesso total ao sistema',
            self::OPERATIONAL => 'Operacional com acesso limitado à administração',
            self::SUPPORT => 'Suporte ao cliente com acesso a ferramentas de atendimento',
        };
    }

    /**
     * Retorna a cor associada ao nível (para UI)
     */
    public function getColor(): string
    {
        return match($this) {
            self::FREE => 'gray',
            self::MODERATOR => 'teal',
            self::PLUS => 'blue',
            self::PREMIUM => 'gold',
            self::COFOUNDER => 'purple',
            self::ELITE => 'black',
            self::ADMIN => 'red',
            self::OPERATIONAL => 'orange',
            self::SUPPORT => 'green',
        };
    }

    /**
     * Verifica se o nível pode acessar recursos premium
     */
    public function hasPremiumAccess(): bool
    {
        return $this->value >= self::PREMIUM->value && $this->value < self::ADMIN->value;
    }

    /**
     * Verifica se o nível pode acessar recursos Plus
     */
    public function hasPlusAccess(): bool
    {
        return $this->value >= self::PLUS->value && $this->value < self::ADMIN->value;
    }

    /**
     * Verifica se é nível administrativo
     */
    public function isAdmin(): bool
    {
        return $this === self::ADMIN;
    }

    /**
     * Verifica se é nível operacional ou superior
     */
    public function isOperational(): bool
    {
        return $this->value >= self::OPERATIONAL->value;
    }

    /**
     * Verifica se pode gerenciar outros usuários
     */
    public function canManageUsers(): bool
    {
        return $this === self::ADMIN;
    }

    /**
     * Verifica se pode ver dados analíticos
     */
    public function canViewAnalytics(): bool
    {
        return $this->isStaff();
    }

    /**
     * Verifica se pode fazer moderação de conteúdo
     */
    public function canModerateContent(): bool
    {
        return $this->isStaff();
    }

    /**
     * Verifica se o nível pertence ao staff (administrativo)
     */
    public function isStaff(): bool
    {
        return $this->value >= self::ADMIN->value;
    }

    /**
     * Verifica se o nível é de consumidor (usuário final)
     */
    public function isConsumer(): bool
    {
        return $this->value < self::ADMIN->value;
    }

    /**
     * Retorna o limite de matches diários
     */
    public function getDailyMatchesLimit(): int
    {
        return match($this) {
            self::FREE => 70,
            self::MODERATOR => 100,
            self::PLUS => PHP_INT_MAX,
            self::PREMIUM => PHP_INT_MAX,
            self::COFOUNDER => PHP_INT_MAX,
            self::ELITE => PHP_INT_MAX,
            self::ADMIN => PHP_INT_MAX,
            self::OPERATIONAL => PHP_INT_MAX,
            self::SUPPORT => PHP_INT_MAX,
        };
    }

    /**
     * Retorna o limite de mensagens diárias para perfis sem match
     */
    public function getDailyMessagesLimit(): int
    {
        return match($this) {
            self::FREE => 0,
            self::MODERATOR => 5,
            self::PLUS => 10,
            self::PREMIUM => 50,
            self::COFOUNDER => 100,
            self::ELITE => PHP_INT_MAX,
            self::ADMIN => PHP_INT_MAX,
            self::OPERATIONAL => PHP_INT_MAX,
            self::SUPPORT => PHP_INT_MAX,
        };
    }

    /**
     * Verifica se pode enviar mensagens para perfis sem match ativo
     */
    public function canSendMessagesWithoutMatch(): bool
    {
        return $this->value >= self::PLUS->value && $this->value < self::ADMIN->value;
    }

    /**
     * Verifica se pode bloquear regiões
     */
    public function canBlockRegion(): bool
    {
        return $this->value >= self::PLUS->value && $this->value < self::ADMIN->value;
    }

    /**
     * Verifica se pode esconder a localização
     */
    public function canHideLocation(): bool
    {
        return $this->value >= self::PLUS->value && $this->value < self::ADMIN->value;
    }

    /**
     * Verifica se pode ficar invisível para outros usuários
     */
    public function canBeInvisible(): bool
    {
        return $this->value >= self::PREMIUM->value && $this->value < self::ADMIN->value;
    }

    /**
     * Retorna se pode usar shorts
     */
    public function canUseShorts(): bool
    {
        return $this->value >= self::PLUS->value && $this->value < self::ADMIN->value;
    }

    /**
     * Retorna se pode ver quem deu like
     */
    public function canViewLikes(): bool
    {
        return $this->value >= self::PLUS->value && $this->value < self::ADMIN->value;
    }

    /**
     * Retorna se pode usar filtros avançados
     */
    public function canUseAdvancedFilters(): bool
    {
        return $this->value >= self::ELITE->value && $this->value < self::ADMIN->value;
    }

    /**
     * Retorna se pode ter perfil verificado
     */
    public function canHaveVerifiedProfile(): bool
    {
        return $this->value >= self::PLUS->value && $this->value < self::ADMIN->value;
    }

    /**
     * Retorna se pode definir quais níveis visualizam seu perfil (apenas ELITE e COFOUNDER)
     */
    public function canFilterByLevel(): bool
    {
        return $this->value >= self::COFOUNDER->value && $this->value < self::ADMIN->value;
    }

    /**
     * Retorna se pode bloquear outros customers (MODERATOR pode bloquear abaixo de COFOUNDER)
     */
    public function canBlockCustomers(): bool
    {
        return $this->value >= self::MODERATOR->value && $this->value < self::ADMIN->value;
    }

    /**
     * Retorna se pode enviar solicitações diretas (COFOUNDER e ELITE)
     */
    public function canSendDirectRequests(): bool
    {
        return $this->value >= self::COFOUNDER->value && $this->value < self::ADMIN->value;
    }

    /**
     * Retorna todos os níveis como array
     */
    public static function getAll(): array
    {
        return [
            self::FREE,
            self::MODERATOR,
            self::PLUS,
            self::PREMIUM,
            self::COFOUNDER,
            self::ELITE,
            self::ADMIN,
            self::OPERATIONAL,
            self::SUPPORT,
        ];
    }

    /**
     * Retorna apenas os níveis pagos (customer)
     */
    public static function getPaidLevels(): array
    {
        return [
            self::PLUS,
            self::PREMIUM,
            self::COFOUNDER,
            self::ELITE,
        ];
    }

    /**
     * Retorna apenas os níveis administrativos (Staff)
     */
    public static function getAdminLevels(): array
    {
        return self::getStaffLevels();
    }

    /**
     * Retorna todos os níveis de staff
     */
    public static function getStaffLevels(): array
    {
        return [
            self::ADMIN,
            self::OPERATIONAL,
            self::SUPPORT,
        ];
    }

    /**
     * Retorna todos os níveis de consumidor
     */
    public static function getConsumerLevels(): array
    {
        return [
            self::FREE,
            self::MODERATOR,
            self::PLUS,
            self::PREMIUM,
            self::COFOUNDER,
            self::ELITE,
        ];
    }

    /**
     * Converte string para enum (case insensitive)
     */
    public static function fromString(string $level): ?self
    {
        return match(strtoupper($level)) {
            'FREE' => self::FREE,
            'MODERATOR' => self::MODERATOR,
            'MODERADOR' => self::MODERATOR,
            'PLUS' => self::PLUS,
            'PREMIUM' => self::PREMIUM,
            'COFOUNDER' => self::COFOUNDER,
            'CO-FOUNDER' => self::COFOUNDER,
            'ELITE' => self::ELITE,
            'ADMIN' => self::ADMIN,
            'ADMINISTRADOR' => self::ADMIN,
            'OPERATIONAL' => self::OPERATIONAL,
            'OPERACIONAL' => self::OPERATIONAL,
            'SUPPORT' => self::SUPPORT,
            'SUPORTE' => self::SUPPORT,
            'STAFF' => self::ADMIN,
            'CONSUMER' => self::FREE,
            'CONSUMIDOR' => self::FREE,
            default => null,
        };
    }

    /**
     * Valida se um valor é um nível válido
     */
    public static function isValid(int $value): bool
    {
        return in_array($value, array_column(self::getAll(), 'value'));
    }
}