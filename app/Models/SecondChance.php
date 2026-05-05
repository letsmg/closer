<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SecondChance extends Model
{
    protected $table = 'second_chances';

    protected $fillable = [
        'profile_id',
        'like_id',
        'used_at'
    ];

    protected $casts = [
        'used_at' => 'datetime'
    ];

    /*
    |--------------------------------------------------------------------------
    | RELATIONSHIPS
    |--------------------------------------------------------------------------
    */

    public function profile()
    {
        return $this->belongsTo(Profile::class, 'profile_id');
    }

    public function like()
    {
        return $this->belongsTo(LikeModel::class, 'like_id');
    }

    public function isUsed()
    {
        return !is_null($this->used_at);
    }

    public function markAsUsed()
    {
        $this->used_at = now();
        $this->save();
    }
}
