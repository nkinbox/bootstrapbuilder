<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Components extends Model
{
    public $timestamps = false;
    public function nestedComponent() {
        return $this->hasOne('App\Models\Components', 'id', 'nested_component');
    }
}
