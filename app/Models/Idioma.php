<?php

namespace App\Models;

// O CORRETO É ESTE:
use Illuminate\Database\Eloquent\Model; 
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Idioma extends Model
{
    use HasFactory;

    protected $fillable = ['nome', 'codigo_iso']; // Ex: ['Português', 'pt-BR']

    /**
     * Perfis que falam este idioma.
     */
    public function perfis()
    {
        return $this->belongsToMany(Perfil::class, 'perfil_idiomas')
                    ->withPivot('nivel')
                    ->withTimestamps();
    }

    /**
     * Perfis que estão buscando pessoas que falem este idioma.
     */
    public function buscadoPorPerfis()
    {
        return $this->belongsToMany(Perfil::class, 'perfil_idiomas_preferencias')
                    ->withTimestamps();
    }
}