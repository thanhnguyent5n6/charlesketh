<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class WMS_Transfer_Detail extends Model
{
    protected $table = 'wms_transfer_details';
    protected $guarded = [];
    public $timestamps = false;

    public function transfer(){
    	return $this->belongsTo('App\WMS_Transfer', 'transfer_id');
    }
}
