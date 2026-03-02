<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PerfilPreferencia extends Model
{
    // Define explicitamente a tabela para evitar erros de pluralização do Laravel
    protected $table = 'perfis_preferencias';

    protected $fillable = [
        'perfil_id',
        'sexo',
        'identidade_genero',
        'orientacao_sexual',
        'objetivo',
        'fumante',
        'bebida',
        'estado_civil',
        'pais_id',
        'estado_id',
        'cidade_id',
        'raio_busca_km', // Alinhado com o nome mais descritivo que usamos
        'idade_minima',
        'idade_maxima',
        'visibilidade',
        'permitir_busca_global'
    ];

    /**
     * Relacionamento Inverso: Cada preferência pertence a um perfil.
     */
    public function perfil()
    {
        return $this->belongsTo(Perfil::class, 'perfil_id');
    }
}