<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PackageMarker extends Model
{
    public $timestamps = false;
    public function getUser() {
        return $this->hasOne('App\Models\User', 'id', 'user_id');
    }
    public function getContent() {
        return $this->hasOne('App\Models\Content', 'id', 'content_id');
    }
}