<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Block extends Model
{
    protected $table = 'blocks';

    protected $fillable = [
        'profile_id',
        'blocked_profile_id'
    ];

    // Who blocked
    public function profile()
    {
        return $this->belongsTo(Profile::class, 'profile_id');
    }

    // Who was blocked
    public function blockedProfile()
    {
        return $this->belongsTo(Profile::class, 'blocked_profile_id');
    }
}
