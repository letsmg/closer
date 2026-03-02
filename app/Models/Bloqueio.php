<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bloqueio extends Model
{
    protected $table = 'bloqueios';

    protected $fillable = [
        'perfil_id',
        'perfil_bloqueado_id',
    ];

    // Quem bloqueou
    public function perfil()
    {
        return $this->belongsTo(Perfil::class, 'perfil_id');
    }

    // Quem foi bloqueado
    public function perfilBloqueado()
    {
        return $this->belongsTo(Perfil::class, 'perfil_bloqueado_id');
    }
}