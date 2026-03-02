<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Shorts extends Model
{
    protected $fillable = ['user_id', 'conteudo', 'tipo', 'posicao'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}