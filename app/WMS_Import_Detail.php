<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class WMS_Import_Detail extends Model
{
    protected $table = 'wms_import_details';
    protected $guarded = [];
    public $timestamps = false;

    public function import(){
    	return $this->belongsTo('App\WMS_Import', 'import_id');
    }
}
