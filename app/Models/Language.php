<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Language extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'code_iso']; // Ex: ['Portuguese', 'pt-BR']

    /**
     * Profiles that speak this language
     */
    public function profiles()
    {
        return $this->belongsToMany(Profile::class, 'profile_languages')
                    ->withPivot('level')
                    ->withTimestamps();
    }
}
