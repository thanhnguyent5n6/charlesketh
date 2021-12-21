<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class WMS_Export_Detail extends Model
{
    protected $table = 'wms_export_details';
    protected $guarded = [];
    public $timestamps = false;

    public function export(){
    	return $this->belongsTo('App\WMS_Export', 'export_id');
    }
}
