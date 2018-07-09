<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Page extends Model
{
    public function Template() {
        return $this->belongsTo('App\Models\Template');
    }
    public function URLs() {
        return $this->hasMany('App\Models\WebUrl');
    }
    public function getUser() {
        return $this->hasOne('App\Models\User', 'id', 'user_id');
    }
    public function getMetadata() {
        return $this->hasOne('App\Models\Content', 'id', 'meta_id');
    }
    public function getScript() {
        return $this->hasOne('App\Models\Content', 'id', 'script_id');
    }
    public function getCSS() {
        return $this->hasOne('App\Models\Content', 'id', 'css_id');
    }
    public function Components() {
        return $this->hasMany('App\Models\Components')->orderBy('order');
    }
    protected static function boot() {
        parent::boot();        
        static::deleting(function($model) {
            $model->getMetadata()->delete();
            $model->getScript()->delete();
            $model->getCSS()->delete();
            $model->URLs()->delete();
        });
    }
}
