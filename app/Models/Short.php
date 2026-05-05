<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Short extends Model
{
    protected $fillable = ['user_id', 'content', 'type', 'position'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
