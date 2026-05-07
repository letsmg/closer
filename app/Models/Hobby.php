<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Hobby extends Model
{
    use HasFactory;
    
    protected $table = 'hobbies';
    protected $fillable = ['name', 'description', 'category', 'icon', 'active'];

    public function profiles()
    {
        return $this->belongsToMany(Profile::class, 'profile_hobbies')->withTimestamps();
    }

    public function profilePreferences()
    {
        return $this->belongsToMany(Profile::class, 'profile_hobby_preferences')->withTimestamps();
    }
}