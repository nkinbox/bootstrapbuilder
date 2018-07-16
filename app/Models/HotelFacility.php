<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HotelFacility extends Model
{
    public $timestamps = false;
    public function getUser() {
        return $this->hasOne('App\Models\User', 'id', 'user_id');
    }
    public function getFacility() {
        return $this->belongsTo('App\Models\DataFacility');
    }
    public function getHotel() {
        return $this->belongsTo('App\Models\Hotel');
    }
    public function getRoom() {
        return $this->belongsTo('App\Models\HotelRoom');
    }
}
