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
        'allow_global_search',
        'hide_location',
        'invisible_mode',
        'interested_hobbies',
        'discoverable_levels', // Niveis que este perfil deseja ver (para COFOUNDER e ELITE)
        'visible_levels', // Niveis que podem ver este perfil (para COFOUNDER e ELITE)
    ];

    protected function casts(): array
    {
        return [
            'hide_location' => 'boolean',
            'invisible_mode' => 'boolean',
            'interested_hobbies' => 'array',
            'discoverable_levels' => 'array',
            'visible_levels' => 'array',
        ];
    }

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
