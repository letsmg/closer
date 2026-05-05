<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    protected $fillable = ['name', 'display_name', 'state_id', 'country_code', 'geoname_id', 'latitude', 'longitude'];

    public function state() { return $this->belongsTo(State::class); }
    public function profiles() { return $this->hasMany(Profile::class); }
}
