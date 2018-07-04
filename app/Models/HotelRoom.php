<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HotelRoom extends Model
{
    public function getUser() {
        return $this->hasOne('App\Models\User', 'id', 'user_id');
    }
    public function RoomFacility() {
        return $this->hasMany('App\Models\HotelFacility')->where('type', 'room');
    }
}
