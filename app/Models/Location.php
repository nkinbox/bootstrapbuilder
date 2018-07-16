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
    public function hotels() {
        return $this->hasMany('App\Models\Hotel');
    }
    public function packages() {
        return $this->hasMany('App\Models\PackageItinerary');
    }
    protected static function boot() {
        parent::boot();        
        static::deleting(function($model) {
            $model->getContent()->delete();
            $model->hotels()->update(["location_id" => 0]);
            $model->packages()->update(["location_id" => 0]);
        });
    }
}
