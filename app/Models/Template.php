<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Template extends Model
{
    public function Pages() {
        return $this->hasMany('App\Models\Page');
    }
    public function URLs() {
        return $this->hasMany('App\Models\WebUrl');
    }
    public function getUser() {
        return $this->hasOne('App\Models\User', 'id', 'user_id');
    }
    public function getScript() {
        return $this->hasOne('App\Models\Content', 'id', 'script_id');
    }
    public function getCSS() {
        return $this->hasOne('App\Models\Content', 'id', 'css_id');
    }
    public function Images() {
        return $this->hasMany('App\Models\Images', 'belongs_to', 'id')->where('type', 'page');
    }
    protected static function boot() {
        parent::boot();        
        static::deleting(function($model) {
            $model->getScript()->delete();
            $model->getCSS()->delete();
            $model->URLs()->delete();
            $model->Pages()->delete();
        });
    }
}
