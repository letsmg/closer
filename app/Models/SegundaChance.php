<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SegundaChance extends Model
{
    protected $table = 'segundas_chances';

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
    | RELACIONAMENTOS
    |--------------------------------------------------------------------------
    */

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function target()
    {
        return $this->belongsTo(User::class, 'liked_user_id');
    }

    public function segundaChance()
    {
        return $this->hasOne(SegundaChance::class);
    }
}