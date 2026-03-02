<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserMatch extends Model
{
    protected $table = 'user_matches';
    protected $fillable = [
        'user_one_id',
        'user_two_id'
    ];

    // Relacionamentos para pegar os dois usuários do match
    public function userOne() { return $this->belongsTo(User::class, 'user_one_id'); }
    public function userTwo() { return $this->belongsTo(User::class, 'user_two_id'); }
}