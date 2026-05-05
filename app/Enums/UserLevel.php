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
    case FREE = 0;          // Usuário padrão/gratuito
    case PLUS = 1;          // Usuário Plus (pago básico)
    case PREMIUM = 2;       // Usuário Premium (pago avançado)
    case ADMIN = 3;         // Administrador do sistema
    case OPERATIONAL = 4;   // Operacional (abaixo do admin)
    case SUPPORT = 5;       // Suporte ao cliente

    /**
     * Retorna o nome amigável do nível
     */
    public function getName(): string
    {
        return match($this) {
            self::FREE => 'Free',
            self::PLUS => 'Plus',
            self::PREMIUM => 'Premium',
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
            self::PLUS => 'Usuário Plus com recursos adicionais',
            self::PREMIUM => 'Usuário Premium com todos os recursos',
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
            self::PLUS => 'blue',
            self::PREMIUM => 'gold',
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
        return $this->value >= self::PREMIUM->value;
    }

    /**
     * Verifica se o nível pode acessar recursos Plus
     */
    public function hasPlusAccess(): bool
    {
        return $this->value >= self::PLUS->value;
    }

    /**
     * Verifica se é nível administrativo
     */
    public function isAdmin(): bool
    {
        return $this->value >= self::ADMIN->value;
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
        return $this->value >= self::ADMIN->value;
    }

    /**
     * Verifica se pode ver dados analíticos
     */
    public function canViewAnalytics(): bool
    {
        return $this->value >= self::OPERATIONAL->value;
    }

    /**
     * Verifica se pode fazer moderação de conteúdo
     */
    public function canModerateContent(): bool
    {
        return $this->value >= self::OPERATIONAL->value;
    }

    /**
     * Retorna o limite de matches diários
     */
    public function getDailyMatchesLimit(): int
    {
        return match($this) {
            self::FREE => 10,
            self::PLUS => 50,
            self::PREMIUM => PHP_INT_MAX, // Ilimitado
            self::ADMIN => PHP_INT_MAX,
            self::OPERATIONAL => PHP_INT_MAX,
            self::SUPPORT => PHP_INT_MAX,
        };
    }

    /**
     * Retorna o limite de mensagens diárias
     */
    public function getDailyMessagesLimit(): int
    {
        return match($this) {
            self::FREE => 20,
            self::PLUS => 100,
            self::PREMIUM => PHP_INT_MAX,
            self::ADMIN => PHP_INT_MAX,
            self::OPERATIONAL => PHP_INT_MAX,
            self::SUPPORT => PHP_INT_MAX,
        };
    }

    /**
     * Retorna se pode usar shorts
     */
    public function canUseShorts(): bool
    {
        return $this->value >= self::PLUS->value;
    }

    /**
     * Retorna se pode ver quem deu like
     */
    public function canViewLikes(): bool
    {
        return $this->value >= self::PLUS->value;
    }

    /**
     * Retorna se pode usar filtros avançados
     */
    public function canUseAdvancedFilters(): bool
    {
        return $this->value >= self::PLUS->value;
    }

    /**
     * Retorna se pode ter perfil verificado
     */
    public function canHaveVerifiedProfile(): bool
    {
        return $this->value >= self::PLUS->value;
    }

    /**
     * Retorna todos os níveis como array
     */
    public static function getAll(): array
    {
        return [
            self::FREE,
            self::PLUS,
            self::PREMIUM,
            self::ADMIN,
            self::OPERATIONAL,
            self::SUPPORT,
        ];
    }

    /**
     * Retorna apenas os níveis pagos
     */
    public static function getPaidLevels(): array
    {
        return [
            self::PLUS,
            self::PREMIUM,
        ];
    }

    /**
     * Retorna apenas os níveis administrativos
     */
    public static function getAdminLevels(): array
    {
        return [
            self::ADMIN,
            self::OPERATIONAL,
        ];
    }

    /**
     * Converte string para enum (case insensitive)
     */
    public static function fromString(string $level): ?self
    {
        return match(strtoupper($level)) {
            'FREE' => self::FREE,
            'PLUS' => self::PLUS,
            'PREMIUM' => self::PREMIUM,
            'ADMIN' => self::ADMIN,
            'ADMINISTRADOR' => self::ADMIN,
            'OPERATIONAL' => self::OPERATIONAL,
            'OPERACIONAL' => self::OPERATIONAL,
            'SUPPORT' => self::SUPPORT,
            'SUPORTE' => self::SUPPORT,
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
