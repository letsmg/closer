<?php

namespace App\Models;

// Interface que ativa a verificação de e-mail no Laravel
use Illuminate\Contracts\Auth\MustVerifyEmail;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;


class User extends Authenticatable implements MustVerifyEmail // 👈 Obrigatório
{
    use HasApiTokens, HasFactory, Notifiable;

    /*
    |--------------------------------------------------------------------------
    | Campos que podem ser preenchidos em massa
    |--------------------------------------------------------------------------
    */
    protected $fillable = [
        'name',
        'email',
        'password',
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

}