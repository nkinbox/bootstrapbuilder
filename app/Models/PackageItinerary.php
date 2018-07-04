<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PackageItinerary extends Model
{
    public $timestamps = false;
    public function getContent() {
        return $this->hasOne('App\Models\Content', 'id', 'content_id');
    }
    public function geoLocation() {
        return $this->hasOne('App\Models\GeoLocation', 'id', 'geolocation_id');
    }
    public function hotel() {
        return $this->hasOne('App\Models\Hotel', 'id', 'hotel_id');
    }
    public function PackageDetail() {
        return $this->belongsTo('App\Models\PackageDetail');
    }
    protected static function boot() {
        parent::boot();        
        static::deleting(function($model) {
            $model->getContent()->delete();
        });
    }
}
