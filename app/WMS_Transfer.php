<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class WMS_Transfer extends Model
{
    protected $table = 'wms_transfers';
    protected $guarded = [];

    public function details(){
        return $this->hasMany('App\WMS_Transfer_Detail', 'transfer_id', 'id')->orderBy('id','asc');
    }
}
