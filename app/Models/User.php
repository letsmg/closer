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
     * Get the identifier that will be stored in the subject claim of the JWT.
     */
    public function getJWTIdentifier(): mixed
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     */
    public function getJWTCustomClaims(): array
    {
        return [
            'email' => $this->email,
            'name' => $this->name,
            'nivel' => $this->nivel ?? 0,
        ];
    }

}