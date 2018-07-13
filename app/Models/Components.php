<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Components extends Model
{
    public $timestamps = false;
    public function nestedComponent() {
        return $this->hasOne('App\Models\Components', 'id', 'nested_component');
    }
    public function Parent() {
        return $this->hasOne('App\Models\Components', 'name', 'name')->where('node', 'parent');
    }
    public function Children() {
        return $this->hasMany('App\Models\Components', 'name', 'name')->where('node', 'child')->orderBy('child_order');
    }
}
