<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DatabaseVariable extends Model
{
    public $timestamps = false;
    public function Related() {
        return $this->hasOne('App\Models\DatabaseVariable', 'id', 'related_to');
    }
    protected static function boot() {
        parent::boot();        
        static::deleting(function($model) {
            DatabaseVariable::where('related_to', $model->id)->delete();
        });
    }
}
