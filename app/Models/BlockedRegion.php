<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BlockedRegion extends Model
{
    protected $table = 'blocked_regions';

    protected $fillable = [
        'profile_id',
        'country_id',
        'state_id',
        'city_id',
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