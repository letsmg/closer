<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cidade extends Model
{
    use HasFactory;

    // Campos que podem ser preenchidos via código
    protected $fillable = [
        'nome',
        'display_name',
        'estado_id',
        'pais_code',
        'geoname_id',
        'latitude',
        'longitude',
    ];    

    /**
     * Relacionamento com o Estado (para cidades do Brasil)
     * O 'nullable' na migration permite que cidades estrangeiras não tenham estado_id
     */
    public function estado()
    {
        return $this->belongsTo(Estado::class);
    }
}