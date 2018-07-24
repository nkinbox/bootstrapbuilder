<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PageContent extends Model
{
    public function Template() {
        return $this->belongsTo('App\Models\Template');
    }
    public function Page() {
        return $this->belongsTo('App\Models\Page');
    }
    public function getUser() {
        return $this->hasOne('App\Models\User', 'id', 'user_id');
    }
    public function getContent() {
        return $this->hasOne('App\Models\Content', 'id', 'content_id');
    }
    public function url() {
        return $this->hasOne('App\Models\WebUrl');
    }
    protected static function boot() {
        parent::boot();        
        static::deleting(function($model) {
            $model->getContent()->delete();
            $model->url->getMetadata()->delete();
            $model->url->getScript()->delete();
            $model->url->getCSS()->delete();
            $model->url->delete();
        });
    }
}
