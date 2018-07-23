<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoopSource extends Model
{
    public $timestamps = false;
    public function Components() {
        return $this->hasMany('App\Models\Components', 'loop_source', 'id');
    }
    protected static function boot() {
        parent::boot();        
        static::deleting(function($model) {
            $model->Components()->update(["loop_source" => ""]);
        });
    }
}
