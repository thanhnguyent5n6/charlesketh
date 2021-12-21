<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PriceDetail extends Model
{
    protected $table = 'price_details';
    protected $guarded = [];
    public $timestamps = false;

    public function price(){
    	return $this->belongsTo('App\Price', 'price_id');
    }

}
