<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PackageDetail extends Model
{
    public $timestamps = false;
    public function getUser() {
        return $this->hasOne('App\Models\User', 'id', 'user_id');
    }
    public function getContent() {
        return $this->hasOne('App\Models\Content', 'id', 'content_id');
    }
    public function Package() {
        return $this->belongsTo('App\Models\Package');
    }
    public function PackageItinerary() {
        return $this->hasMany('App\Models\PackageItinerary');
    }
    public function PackageMarker() {
        return $this->hasMany('App\Models\PackageMarker')->orderBy('type')->orderBy('order');
    }
    public function PackagePrice() {
        return $this->hasMany('App\Models\PackagePrice');
    }
    protected static function boot() {
        parent::boot();        
        static::deleting(function($model) {
            $model->getContent()->delete();
        });
    }
}
