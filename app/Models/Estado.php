<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Estado extends Model
{
    protected $fillable = ['nome', 'uf', 'pais_id'];

    public function pais() { return $this->belongsTo(Pais::class); }
    public function cidades() { return $this->hasMany(Cidade::class); }
}