<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DataMarker extends Model
{
    public $timestamps = false;
    public function getContent() {
        return $this->hasOne('App\Models\Content', 'id', 'content_id');
    }
    public function getUser() {
        return $this->hasOne('App\Models\User', 'id', 'user_id');
    }
    public function hotels() {
        return $this->hasMany('App\Models\HotelMarker');
    }
    public function packages() {
        return $this->hasMany('App\Models\PackageMarker');
    }
    protected static function boot() {
        parent::boot();        
        static::deleting(function($model) {
            $model->getContent()->delete();
            $model->hotels()->delete();
            $model->packages()->delete();
        });
    }
}
