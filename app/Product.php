<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $table = 'products';
    protected $guarded = [];
    protected $dates = ['expired_at'];

    public function languages(){
    	return $this->hasMany('App\ProductLanguage', 'product_id', 'id')->orderBy('id','asc');
    }

    public function category(){
    	return $this->belongsTo('App\Category', 'category_id');
    }

    public function attribute(){
    	return $this->belongsToMany('App\Attribute','product_attribute','product_id','attribute_id');
    }

    public function getIdsAttribute($type=''){
        return $this->attribute->where('type',$type)->pluck('id')->toArray();
    }
}
