<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BlockedEmail extends Model
{
    protected $table = 'blocked_emails';

    protected $fillable = [
        'user_id',
        'email_hash',
        'reason',
        'banned_by'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function bannedBy()
    {
        return $this->belongsTo(User::class, 'banned_by');
    }
}
