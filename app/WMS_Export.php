<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class WMS_Export extends Model
{
    protected $table = 'wms_exports';
    protected $guarded = [];

    public function details(){
        return $this->hasMany('App\WMS_Export_Detail', 'export_id', 'id')->orderBy('id','asc');
    }
}
