<?php

namespace App\Models;

// Interface that enables email verification in Laravel
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
    | Mass assignable fields
    |--------------------------------------------------------------------------
    */
    protected $fillable = [
        'name',
        'email',
        'password',
        'uuid',              // Public ULID (obscures incremental ID)
        'ativo',
        'nivel_acesso',      // 0 = Free | 1 = Plus | 2 = Premium
        'ultimo_login_em',
        'ultimo_ip',
        'daily_likes_count',
        'daily_likes_date',
        'daily_messages_count',
        'daily_messages_date',
    ];

    /**
     * Atributos adicionados ao JSON
     */
    protected $appends = [
        'is_admin_level',
        'is_staff_level',
        'nivel',
        'main_photo_url',
    ];

    /*
    |--------------------------------------------------------------------------
    | Hidden fields
    |--------------------------------------------------------------------------
    */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /*
    |--------------------------------------------------------------------------
    | Type casting
    |--------------------------------------------------------------------------
    */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime', // Converts to Carbon object
            'ativo' => 'boolean',
            'password' => 'hashed', // Automatic hash on save
            'nivel_acesso' => 'integer',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | RELATIONSHIPS
    |--------------------------------------------------------------------------
    */

    // User has one profile
    public function perfil()
    {
        return $this->hasOne(Profile::class);
    }

    // Alias: perfil() = profile() for English-friendly code
    public function profile()
    {
        return $this->hasOne(Profile::class);
    }

    // User can have multiple preferences (pivot table)
    public function preferencias()
    {
        return $this->belongsToMany(
            Preferencia::class,
            'preferencias_usuario',
            'user_id',
            'preferencia_id'
        );
    }

    // Hidden locations
    public function localidades_ocultas()
    {
        return $this->hasMany(LocalidadeOculta::class);
    }

    // Profile photos
    public function fotos()
    {
        return $this->hasMany(ProfilePhoto::class)
                    ->orderBy('order', 'asc');
    }

    /**
     * Denúncias recebidas por este usuário
     */
    public function reportsReceived()
    {
        return $this->hasMany(Report::class, 'reported_id');
    }

    /**
     * Denúncias feitas por este usuário
     */
    public function reportsMade()
    {
        return $this->hasMany(Report::class, 'reporter_id');
    }

    public function isOnline()
    {
        // Consider online if last activity was within 5 minutes
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
     * Accessor para is_admin_level
     */
    public function getIsAdminLevelAttribute(): bool
    {
        return $this->isAdminLevel();
    }

    /**
     * Accessor para is_staff_level
     */
    public function getIsStaffLevelAttribute(): bool
    {
        return $this->isAdminLevel();
    }

    /**
     * Accessor para nivel (compatibilidade)
     */
    public function getNivelAttribute(): int
    {
        return (int) $this->nivel_acesso;
    }

    /**
     * Accessor para foto principal
     */
    public function getMainPhotoUrlAttribute(): ?string
    {
        $photo = $this->fotos()->where('is_primary', true)->first() ?? $this->fotos()->first();
        return $photo ? $photo->full_url : null;
    }

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
        return $this->nivel_acesso >= UserLevel::ADMIN->value;
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
     * Verifica se pode enviar mensagens para perfis sem match ativo
     */
    public function canSendMessagesWithoutMatch(): bool
    {
        return $this->getLevelAttribute()->canSendMessagesWithoutMatch();
    }

    /**
     * Verifica se pode bloquear regiões
     */
    public function canBlockRegion(): bool
    {
        return $this->getLevelAttribute()->canBlockRegion();
    }

    /**
     * Verifica se pode esconder a localização
     */
    public function canHideLocation(): bool
    {
        return $this->getLevelAttribute()->canHideLocation();
    }

    /**
     * Verifica se pode ficar invisível para outros usuários
     */
    public function canBeInvisible(): bool
    {
        return $this->getLevelAttribute()->canBeInvisible();
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
            UserLevel::COFOUNDER->value,
            UserLevel::ELITE->value,
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
            UserLevel::SUPPORT->value,
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