<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Package extends Model
{
    public function getUser() {
        return $this->hasOne('App\Models\User', 'id', 'user_id');
    }
    public function getContent() {
        return $this->hasOne('App\Models\Content', 'id', 'content_id');
    }
    public function PackageDetail() {
        return $this->hasMany('App\Models\PackageDetail');
    }
    public function PackageItinerary() {
        return $this->hasManyThrough('App\Models\PackageItinerary', 'App\Models\PackageDetail');
    }
    public function PackageMarker() {
        return $this->hasManyThrough('App\Models\PackageMarker', 'App\Models\PackageDetail')->orderBy('type')->orderBy('order');
    }
    public function PackagePrice() {
        return $this->hasManyThrough('App\Models\PackagePrice', 'App\Models\PackageDetail');
    }
    public function Images() {
        return $this->hasMany('App\Models\Images', 'belongs_to', 'id')->where('type', 'package');
    }
    protected static function boot() {
        parent::boot();        
        static::deleting(function($model) {
            $model->getContent()->delete();
        });
    }
}
