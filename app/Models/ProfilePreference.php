<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProfilePreference extends Model
{
    // Explicitly define table to avoid Laravel pluralization errors
    protected $table = 'profile_preferences';

    protected $fillable = [
        'profile_id',
        'gender',
        'gender_identity',
        'sexual_orientation',
        'purpose',
        'smoker',
        'drinker',
        'marital_status',
        'country_id',
        'state_id',
        'city_id',
        'search_radius_km', // Aligned with more descriptive name
        'min_age',
        'max_age',
        'visibility',
        'allow_global_search'
    ];

    public function profile()
    {
        return $this->belongsTo(Profile::class);
    }

    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    public function state()
    {
        return $this->belongsTo(State::class);
    }

    public function city()
    {
        return $this->belongsTo(City::class);
    }
}
