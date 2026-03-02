<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Hobby extends Model
{
    protected $fillable = ['nome', 'categoria'];

    public function perfis()
    {
        return $this->belongsToMany(Perfil::class, 'perfil_hobbies')->withTimestamps();
    }

    public function buscadoPorPerfis()
    {
        return $this->belongsToMany(Perfil::class, 'perfil_hobbies_preferencias')->withTimestamps();
    }
}