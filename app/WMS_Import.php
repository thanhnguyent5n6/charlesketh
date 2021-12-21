<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class WMS_Import extends Model
{
    protected $table = 'wms_imports';
    protected $guarded = [];

    public function details(){
        return $this->hasMany('App\WMS_Import_Detail', 'import_id', 'id')->orderBy('id','asc');
    }
}
