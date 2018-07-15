<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PageComponent extends Model
{
    public $timestamps = false;
    public function Component() {
        return $this->hasOne('App\Models\Components', 'id', 'component_id');
    }
}
