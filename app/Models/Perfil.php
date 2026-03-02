<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Perfil extends Model
{
    protected $table = 'perfis';
    
    protected $fillable = [
        'user_id', 'apelido', 'data_nascimento', 'sexo', 'identidade_genero',
        'orientacao_sexual', 'objetivo', 'profissao', 'biografia', 'fumante',
        'bebida', 'estado_civil', 'pais_id', 'estado_id', 'cidade_id',
        'visibilidade', 'latitude', 'longitude'
    ];

    // ---------------------------------------------------------
    // Relações Diretas (1:1 e 1:N)
    // ---------------------------------------------------------
    public function user() { return $this->belongsTo(User::class); }
    public function pais() { return $this->belongsTo(Pais::class); }
    public function estado() { return $this->belongsTo(Estado::class); }
    public function cidade() { return $this->belongsTo(Cidade::class); }

    /**
     * Tabela de preferências gerais (Idade, Raio, etc)
     * Como a migration chama 'perfis_preferencias', o Model deve ser PerfilPreferencia
     */
    public function preferencia() 
    { 
        return $this->hasOne(PerfilPreferencia::class, 'perfil_id'); 
    }

    // ---------------------------------------------------------
    // Relações NxN - O QUE EU SOU / TENHO
    // ---------------------------------------------------------
    
    public function idiomas()
    {
        return $this->belongsToMany(Idioma::class, 'perfil_idiomas')
                    ->withPivot('nivel')
                    ->withTimestamps();
    }

    public function hobbies()
    {
        return $this->belongsToMany(Hobby::class, 'perfil_hobbies')
                    ->withTimestamps();
    }

    // ---------------------------------------------------------
    // Relações NxN - O QUE EU BUSCO (Preferências de Match)
    // ---------------------------------------------------------

    /**
     * Idiomas que o usuário aceita que o "match" fale.
     */
    public function idiomasBuscados()
    {
        return $this->belongsToMany(Idioma::class, 'perfil_idiomas_preferencias')
                    ->withTimestamps();
    }

    /**
     * Hobbies que o usuário gostaria que o "match" tivesse.
     */
    public function hobbiesBuscados()
    {
        return $this->belongsToMany(Hobby::class, 'perfil_hobbies_preferencias')
                    ->withTimestamps();
    }

    // ---------------------------------------------------------
    // Bloqueios e Privacidade
    // ---------------------------------------------------------

    public function bloqueados()
    {
        return $this->belongsToMany(Perfil::class, 'bloqueios', 'perfil_id', 'perfil_bloqueado_id');
    }

    public function bloqueadoPor()
    {
        return $this->belongsToMany(Perfil::class, 'bloqueios', 'perfil_bloqueado_id', 'perfil_id');
    }
}