<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LikeModel extends Model
{
    protected $table = 'likes';

    protected $fillable = [
        'user_id',
        'liked_user_id',
        'is_like'
    ];

    protected $casts = [
        'is_like' => 'boolean'
    ];

    /*
    |--------------------------------------------------------------------------
    | RELATIONSHIPS
    |--------------------------------------------------------------------------
    */

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function likedUser()
    {
        return $this->belongsTo(User::class, 'liked_user_id');
    }
}
