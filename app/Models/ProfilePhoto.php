<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

use App\Traits\HasUlid;

class ProfilePhoto extends Model
{
    use HasUlid;

    protected $table = 'profile_photos';
    protected $fillable = ['user_id', 'path', 'is_primary', 'order', 'uuid'];

    // Virtual attribute to facilitate Kotlin life
    protected $appends = ['full_url'];

    public function getFullUrlAttribute()
    {
        return asset('storage/' . $this->path);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function profile()
    {
        return $this->belongsTo(Profile::class, 'user_id');
    }
}
