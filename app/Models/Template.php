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
    public function GlobalVariables() {
        return $this->hasMany('App\Models\Variables');
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
    public function Components() {
        return $this->hasMany('App\Models\Components')->where('category', 'web');
    }
    public function AllComponents() {
        return $this->hasMany('App\Models\Components');
    }
    public function PageContent() {
        return $this->hasMany('App\Models\PageContent');
    }
    protected static function boot() {
        parent::boot();        
        static::deleting(function($model) {
            $model->getScript()->delete();
            $model->getCSS()->delete();
            foreach($model->URLs as $url) {
                $url->getMetadata()->delete();
                $url->getScript()->delete();
                $url->getCSS()->delete();
            }
            $model->URLs()->delete();
            foreach($model->Pages as $page) {
                $page->getMetadata()->delete();
                $page->getScript()->delete();
                $page->getCSS()->delete();
            }
            $model->Pages()->delete();
            $model->GlobalVariables()->delete();
            $model->AllComponents()->delete();
            $model->PageContent()->update(["template_id" => 0, "page_id" => 0, "web_url_id" => 0]);
        });
    }
}
