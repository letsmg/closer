<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AccessHistory extends Model
{
    protected $table = 'access_history';

    protected $fillable = [
        'user_id',
        'ip',
        'device',
        'access_time'
    ];

    protected $casts = [
        'access_time' => 'datetime'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
