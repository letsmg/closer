<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
    protected $table = 'profiles';
    
    protected $fillable = [
        'user_id', 'nickname', 'birth_date', 'gender', 'gender_identity',
        'sexual_orientation', 'purpose', 'profession', 'biography', 'smoker',
        'drinker', 'marital_status', 'country_id', 'state_id', 'city_id',
        'visibility', 'latitude', 'longitude'
    ];

    // ---------------------------------------------------------
    // Direct Relations (1:1 and 1:N)
    // ---------------------------------------------------------
    public function user() { return $this->belongsTo(User::class); }
    public function country() { return $this->belongsTo(Country::class); }
    public function state() { return $this->belongsTo(State::class); }
    public function city() { return $this->belongsTo(City::class); }

    /**
     * General preferences table (Age, Radius, etc)
     * As migration calls 'profile_preferences', Model should be ProfilePreference
     */
    public function preference() 
    { 
        return $this->hasOne(ProfilePreference::class, 'profile_id'); 
    }

    // ---------------------------------------------------------
    // NxN Relations - WHAT I AM / HAVE
    // ---------------------------------------------------------
    
    public function languages()
    {
        return $this->belongsToMany(Language::class, 'profile_languages')
                    ->withPivot('level')
                    ->withTimestamps();
    }

    public function hobbies()
    {
        return $this->belongsToMany(Hobby::class, 'profile_hobbies')
                    ->withTimestamps();
    }

    public function photos()
    {
        return $this->hasMany(ProfilePhoto::class, 'user_id');
    }

    public function preferences()
    {
        return $this->hasMany(ProfilePreference::class, 'profile_id');
    }

    public function blockedProfiles()
    {
        return $this->belongsToMany(Profile::class, 'blocks', 'profile_id', 'blocked_profile_id')
                    ->withTimestamps();
    }

    public function blockedByProfiles()
    {
        return $this->belongsToMany(Profile::class, 'blocks', 'blocked_profile_id', 'profile_id')
                    ->withTimestamps();
    }

    public function sentMessages()
    {
        return $this->hasMany(Message::class, 'sender_id');
    }

    public function receivedMessages()
    {
        return $this->hasMany(Message::class, 'user_match_id');
    }

    public function reports()
    {
        return $this->hasMany(Report::class, 'reported_id');
    }

    public function reportsMade()
    {
        return $this->hasMany(Report::class, 'reporter_id');
    }

    public function likes()
    {
        return $this->hasMany(Like::class, 'user_id');
    }

    public function likedBy()
    {
        return $this->hasMany(Like::class, 'liked_user_id');
    }

    public function secondChances()
    {
        return $this->hasMany(SecondChance::class, 'profile_id');
    }

    public function blockedEmails()
    {
        return $this->hasMany(BlockedEmail::class, 'user_id');
    }

    public function shorts()
    {
        return $this->hasMany(Short::class, 'user_id');
    }

    public function promotionalContracts()
    {
        return $this->hasMany(PromotionalContract::class, 'user_id');
    }

    public function accessHistory()
    {
        return $this->hasMany(AccessHistory::class, 'user_id');
    }

    // ---------------------------------------------------------
    // Scopes
    // ---------------------------------------------------------
    public function scopeVisible($query)
    {
        return $query->where('visibility', 'public');
    }

    public function scopeWithinRadius($query, $latitude, $longitude, $radiusKm = 50)
    {
        // Haversine formula for distance calculation
        $haversine = "(6371 * acos(cos(radians($latitude)) * cos(radians(latitude)) * cos(radians(longitude) - radians($longitude)) + sin(radians($latitude)) * sin(radians(latitude)) * sin(radians(longitude) - radians($longitude))))";
        
        return $query->selectRaw("*")
                    ->selectRaw("{$haversine} AS distance")
                    ->havingRaw("distance <= ?", [$radiusKm]);
    }

    // ---------------------------------------------------------
    // Accessors & Mutators
    // ---------------------------------------------------------
    public function getAgeAttribute()
    {
        return $this->birth_date ? $this->birth_date->age : null;
    }

    public function getFullNameAttribute()
    {
        return $this->user ? $this->user->name : null;
    }

    public function getPrimaryPhotoAttribute()
    {
        return $this->photos()->where('is_primary', true)->first();
    }

    public function isVisible()
    {
        return $this->visibility === 'public';
    }

    public function isBlocked()
    {
        return $this->visibility === 'hidden';
    }

    // ---------------------------------------------------------
    // Helper Methods
    // ---------------------------------------------------------
    public function canBeViewedBy($user)
    {
        if (!$user) return false;
        
        // User can view own profile
        if ($this->user_id === $user->id) return true;
        
        // Check if blocked
        if ($this->blockedByProfiles()->where('profile_id', $user->profile->id)->exists()) {
            return false;
        }
        
        // Check visibility settings
        return match($this->visibility) {
            'public' => true,
            'hidden' => false,
            'matches_only' => $this->isMatchedWith($user),
            default => false,
        };
    }

    public function isMatchedWith($user)
    {
        // Implementation depends on your matching logic
        return false; // Placeholder
    }

    public function getCompletionPercentage()
    {
        $fields = ['nickname', 'birth_date', 'gender', 'biography', 'profession'];
        $completed = 0;
        
        foreach ($fields as $field) {
            if (!empty($this->$field)) {
                $completed++;
            }
        }
        
        return ($completed / count($fields)) * 100;
    }
}
