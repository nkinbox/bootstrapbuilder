<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Hotel extends Model
{   
    protected static function boot() {
        parent::boot();        
        static::deleting(function($model) {
            $model->getContent()->delete();
            $model->getPolicy()->delete();
        });
    }
    public function getUser() {
        return $this->hasOne('App\Models\User', 'id', 'user_id');
    }
    public function getContent() {
        return $this->hasOne('App\Models\Content', 'id', 'content_id');
    }
    public function getPolicy() {
        return $this->hasOne('App\Models\Content', 'id', 'policy_id');
    }
    public function HotelContact() {
        return $this->hasMany('App\Models\HotelContact');
    }
    public function HotelFacility() {
        return $this->hasMany('App\Models\HotelFacility')->where('type', 'hotel');
    }
    public function AllHotelFacility() {
        return $this->hasMany('App\Models\HotelFacility');
    }
    public function HotelMarker() {
        return $this->hasMany('App\Models\HotelMarker');
    }
    public function HotelRoom() {
        return $this->hasMany('App\Models\HotelRoom');
    }
    public function Images() {
        return $this->hasMany('App\Models\Images', 'belongs_to', 'id')->where('type', 'hotel');
    }
    public function Gallery() {
        return $this->hasMany('App\Models\Images', 'belongs_to', 'id')->where('type', 'hotelIM');
    }
    public function geoLocation() {
        return $this->hasOne('App\Models\GeoLocation', 'id', 'geolocation_id');
    }
    public function Location() {
        return $this->hasOne('App\Models\Location', 'id', 'location_id');
    }
}
