<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Price extends Model
{
    protected $table = 'prices';
    protected $guarded = [];

    public function details(){
        return $this->hasMany('App\PriceDetail', 'price_id', 'id')->orderBy('id','asc');
    }

    public function user(){
    	return $this->belongsTo('App\User', 'user_id');
    }
}
