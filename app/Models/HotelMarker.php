<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HotelMarker extends Model
{
    public $timestamps = false;
    public function getUser() {
        return $this->hasOne('App\Models\User', 'id', 'user_id');
    }
}
