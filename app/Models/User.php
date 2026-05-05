<?php

namespace App\Models;

// Interface que ativa a verificação de e-mail no Laravel
use Illuminate\Contracts\Auth\MustVerifyEmail;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Tymon\JWTAuth\Contracts\JWTSubject;
use App\Traits\HasUlid;
use App\Enums\UserLevel;


class User extends Authenticatable implements MustVerifyEmail, JWTSubject
{
    use HasApiTokens, HasFactory, Notifiable, HasUlid;

    /*
    |--------------------------------------------------------------------------
    | Campos que podem ser preenchidos em massa
    |--------------------------------------------------------------------------
    */
    protected $fillable = [
        'name',
        'email',
        'password',
        'uuid',              // ULID público (ofusca ID incremental)
        'ativo',
        'nivel_acesso',      // 0 = Free | 1 = Plus | 2 = Premium
        'reputacao',
        'ultima_interacao_at',
        'ultima_conversa_at',
        'assinatura_id',
        'premium_expira_em',
    ];

    /*
    |--------------------------------------------------------------------------
    | Campos que não devem aparecer no JSON
    |--------------------------------------------------------------------------
    */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /*
    |--------------------------------------------------------------------------
    | Conversão automática de tipos
    |--------------------------------------------------------------------------
    */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime', // Converte para objeto Carbon
            'premium_expira_em' => 'datetime',
            'ativo' => 'boolean',
            'password' => 'hashed', // Faz hash automático ao salvar
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | RELACIONAMENTOS
    |--------------------------------------------------------------------------
    */

    // Um usuário possui um perfil
    public function perfil()
    {
        return $this->hasOne(Perfil::class);
    }

    // Usuário pode ter várias preferências (tabela pivô)
    public function preferencias()
    {
        return $this->belongsToMany(
            Preferencia::class,
            'preferencias_usuario',
            'user_id',
            'preferencia_id'
        );
    }

    // Localidades ocultas
    public function localidades_ocultas()
    {
        return $this->hasMany(LocalidadeOculta::class);
    }

    // Fotos do perfil
    public function fotos()
    {
        return $this->hasMany(FotoPerfil::class)
                    ->orderBy('ordem', 'asc');
    }

    public function isOnline()
    {
        // Considera online se a última atividade foi nos últimos 5 minutos
        return $this->last_seen_at && $this->last_seen_at->diffInMinutes(now()) < 5;
    }

    /*
    |--------------------------------------------------------------------------
    | JWT Subject Methods
    |--------------------------------------------------------------------------
    */

    /**
     * Get the JWT identifier.
     */
    public function getJWTIdentifier(): string
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     */
    public function getJWTCustomClaims(): array
    {
        return [
            'uuid' => $this->uuid,
            'level' => $this->nivel_acesso,
        ];
    }

    /**
     * --------------------------------------------------------------------------
     * USER LEVEL METHODS
     * --------------------------------------------------------------------------
     */

    /**
     * Retorna o nível de acesso como enum
     */
    public function getLevelAttribute(): UserLevel
    {
        return UserLevel::from((int) $this->nivel_acesso);
    }

    /**
     * Define o nível de acesso usando enum
     */
    public function setLevelAttribute(UserLevel $level): void
    {
        $this->attributes['nivel_acesso'] = $level->value;
    }

    /**
     * Verifica se o usuário é Free
     */
    public function isFree(): bool
    {
        return $this->nivel_acesso === UserLevel::FREE->value;
    }

    /**
     * Verifica se o usuário é Plus
     */
    public function isPlus(): bool
    {
        return $this->nivel_acesso === UserLevel::PLUS->value;
    }

    /**
     * Verifica se o usuário é Premium
     */
    public function isPremium(): bool
    {
        return $this->nivel_acesso === UserLevel::PREMIUM->value;
    }

    /**
     * Verifica se o usuário é Admin
     */
    public function isAdmin(): bool
    {
        return $this->nivel_acesso === UserLevel::ADMIN->value;
    }

    /**
     * Verifica se o usuário é Operacional
     */
    public function isOperational(): bool
    {
        return $this->nivel_acesso === UserLevel::OPERATIONAL->value;
    }

    /**
     * Verifica se tem acesso Plus ou superior
     */
    public function hasPlusAccess(): bool
    {
        return $this->getLevelAttribute()->hasPlusAccess();
    }

    /**
     * Verifica se tem acesso Premium ou superior
     */
    public function hasPremiumAccess(): bool
    {
        return $this->getLevelAttribute()->hasPremiumAccess();
    }

    /**
     * Verifica se é nível administrativo (Admin ou Operacional)
     */
    public function isAdminLevel(): bool
    {
        return $this->getLevelAttribute()->isAdmin();
    }

    /**
     * Verifica se pode gerenciar usuários
     */
    public function canManageUsers(): bool
    {
        return $this->getLevelAttribute()->canManageUsers();
    }

    /**
     * Verifica se pode ver analytics
     */
    public function canViewAnalytics(): bool
    {
        return $this->getLevelAttribute()->canViewAnalytics();
    }

    /**
     * Verifica se pode moderar conteúdo
     */
    public function canModerateContent(): bool
    {
        return $this->getLevelAttribute()->canModerateContent();
    }

    /**
     * Retorna o limite de matches diários
     */
    public function getDailyMatchesLimit(): int
    {
        return $this->getLevelAttribute()->getDailyMatchesLimit();
    }

    /**
     * Retorna o limite de mensagens diárias
     */
    public function getDailyMessagesLimit(): int
    {
        return $this->getLevelAttribute()->getDailyMessagesLimit();
    }

    /**
     * Verifica se pode usar Shorts
     */
    public function canUseShorts(): bool
    {
        return $this->getLevelAttribute()->canUseShorts();
    }

    /**
     * Verifica se pode ver quem deu like
     */
    public function canViewLikes(): bool
    {
        return $this->getLevelAttribute()->canViewLikes();
    }

    /**
     * Verifica se pode usar filtros avançados
     */
    public function canUseAdvancedFilters(): bool
    {
        return $this->getLevelAttribute()->canUseAdvancedFilters();
    }

    /**
     * Verifica se pode ter perfil verificado
     */
    public function canHaveVerifiedProfile(): bool
    {
        return $this->getLevelAttribute()->canHaveVerifiedProfile();
    }

    /**
     * Scope para filtrar por nível específico
     */
    public function scopeByLevel($query, UserLevel $level)
    {
        return $query->where('nivel_acesso', $level->value);
    }

    /**
     * Scope para filtrar usuários pagos (Plus e Premium)
     */
    public function scopePaid($query)
    {
        return $query->whereIn('nivel_acesso', [
            UserLevel::PLUS->value,
            UserLevel::PREMIUM->value,
        ]);
    }

    /**
     * Scope para filtrar administrativos
     */
    public function scopeAdmins($query)
    {
        return $query->whereIn('nivel_acesso', [
            UserLevel::ADMIN->value,
            UserLevel::OPERATIONAL->value,
        ]);
    }

    /**
     * Scope para usuários gratuitos
     */
    public function scopeFree($query)
    {
        return $query->where('nivel_acesso', UserLevel::FREE->value);
    }
}