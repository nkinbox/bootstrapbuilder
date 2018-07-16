<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HotelRoom extends Model
{
    public function getUser() {
        return $this->hasOne('App\Models\User', 'id', 'user_id');
    }
    public function RoomFacility() {
        return $this->belongsToMany('App\Models\DataFacility', 'hotel_facilities');
    }
    public function Facilities() {
        return $this->hasMany('App\Models\HotelFacility');
    }
    protected static function boot() {
        parent::boot();        
        static::deleting(function($model) {
            $model->Facilities()->delete();
        });
    }
}
