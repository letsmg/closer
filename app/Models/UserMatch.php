<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class UserMatch extends Model
{
    use HasFactory;
    
    protected $table = 'user_matches';
    protected $fillable = [
        'user_one_id',
        'user_two_id',
        'is_favorite'
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'is_favorite' => 'boolean',
        'matched_at' => 'datetime',
    ];

    // Relacionamentos para pegar os dois usuários do match
    public function userOne() { return $this->belongsTo(User::class, 'user_one_id'); }
    public function userTwo() { return $this->belongsTo(User::class, 'user_two_id'); }

    /**
     * Check if this match is marked as favorite
     */
    public function isFavorite(): bool
    {
        return $this->is_favorite;
    }

    /**
     * Mark this match as favorite
     */
    public function markAsFavorite(): void
    {
        $this->is_favorite = true;
        $this->save();
    }

    /**
     * Mark this match as not favorite
     */
    public function markAsNotFavorite(): void
    {
        $this->is_favorite = false;
        $this->save();
    }

    /**
     * Scope to get only favorite matches
     */
    public function scopeFavorites($query)
    {
        return $query->where('is_favorite', true);
    }

    /**
     * Scope to get only non-favorite matches
     */
    public function scopeNotFavorites($query)
    {
        return $query->where('is_favorite', false);
    }
}