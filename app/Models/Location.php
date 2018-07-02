<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    public function getUser() {
        return $this->hasOne('App\Models\User', 'id', 'user_id');
    }
    public function getContent() {
        return $this->hasOne('App\Models\Content', 'id', 'content_id');
    }
    public function geoLocation() {
        return $this->hasOne('App\Models\GeoLocation', 'id', 'geolocation_id');
    }
}