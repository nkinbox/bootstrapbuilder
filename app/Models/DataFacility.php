<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DataFacility extends Model
{
    public $timestamps = false;
    public function getContent() {
        return $this->hasOne('App\Models\Content', 'id', 'content_id');
    }
    public function getUser() {
        return $this->hasOne('App\Models\User', 'id', 'user_id');
    }
    public function hotels() {
        return $this->hasMany('App\Models\HotelFacility');
    }
    protected static function boot() {
        parent::boot();        
        static::deleting(function($model) {
            $model->getContent()->delete();
            $model->hotels()->delete();
        });
    }
}
