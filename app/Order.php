<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $table = 'orders';
    protected $casts = ['invoice'=>'json','delivery'=>'json'];
    protected $guarded = [];

    public function details(){
        return $this->hasMany('App\OrderDetail', 'order_id', 'id')->orderBy('id','asc');
    }
}
