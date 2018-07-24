<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WebUrl extends Model
{
    public function Page() {
        return $this->belongsTo('App\Models\Page');
    }
    public function Template() {
        return $this->belongsTo('App\Models\Template');
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
    // public function Components() {
    //     return $this->hasMany('App\Models\Components', 'page_id', 'page_id')->orderBy('order');
    // }
    public function PageContent() {
        return $this->hasOne('App\Models\PageContent');
    }
    protected static function boot() {
        parent::boot();        
        static::deleting(function($model) {
            $model->getMetadata()->delete();
            $model->getScript()->delete();
            $model->getCSS()->delete();
            if($model->page_content_id) {
                $model->PageContent->update(["web_url_id" => 0]);
            }
        });
    }
}
