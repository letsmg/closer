<?php

// app/Models/FotoPerfil.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class FotoPerfil extends Model
{
    protected $table = 'fotos_perfil';
    protected $fillable = ['user_id', 'path', 'is_principal', 'ordem'];

    // Atributo virtual para facilitar a vida do Kotlin
    protected $appends = ['url_completa'];

    public function getUrlCompletaAttribute()
    {
        return asset('storage/' . $this->path);
    }
}