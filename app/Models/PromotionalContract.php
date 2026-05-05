<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PromotionalContract extends Model
{
    protected $table = 'promotional_contracts';

    protected $fillable = [
        'user_id',
        'offer_name',
        'accepted_at',
        'start_at',
        'end_at',
        'complying_with_rules'
    ];

    protected $casts = [
        'accepted_at' => 'datetime',
        'start_at' => 'datetime',
        'end_at' => 'datetime',
        'complying_with_rules' => 'boolean'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function isActive()
    {
        return $this->start_at && $this->start_at->isPast() && 
               (!$this->end_at || $this->end_at->isFuture());
    }

    public function isExpired()
    {
        return $this->end_at && $this->end_at->isPast();
    }
}
