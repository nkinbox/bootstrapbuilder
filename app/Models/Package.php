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
        return $this->hasMany('App\Models\PackageMarker');
    }
    public function PackagePrice() {
        return $this->hasManyThrough('App\Models\PackagePrice', 'App\Models\PackageDetail');
    }
    public function fromGeoLocation() {
        return $this->hasOne('App\Models\GeoLocation', 'id', 'from_geolocation_id');
    }
    public function Images() {
        return $this->hasMany('App\Models\Images', 'belongs_to', 'id')->where('type', 'package');
    }
    protected static function boot() {
        parent::boot();        
        static::deleting(function($model) {
            $model->getContent()->delete();
            $model->PackageDetail()->delete();
            $model->PackageItinerary()->delete();
            $model->PackageMarker()->delete();
            $model->PackagePrice()->delete();
        });
    }
}
