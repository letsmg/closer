<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    use HasFactory;

    // Table name (optional if following standard plural)
    protected $table = 'messages';

    protected $fillable = [
        'user_match_id',
        'sender_id',
        'content',
        'read'
    ];

    /**
     * Relationship: Message belongs to a Match
     */
    public function match()
    {
        return $this->belongsTo(UserMatch::class, 'user_match_id');
    }

    /**
     * Relationship: Message sender
     */
    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    /**
     * Relationship: Message recipient (through match)
     */
    public function recipient()
    {
        return $this->belongsTo(User::class, 'user_match_id');
    }

    public function isRead()
    {
        return $this->read;
    }

    public function markAsRead()
    {
        $this->read = true;
        $this->save();
    }
}
