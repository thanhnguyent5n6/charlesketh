<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Attribute extends Model
{
    protected $table = 'attributes';
    protected $guarded = [];

    public function languages(){
    	return $this->hasMany('App\AttributeLanguage', 'attribute_id', 'id')->orderBy('id','asc');
    }

    public function product(){
    	return $this->belongsToMany('App\Product','product_attribute','attribute_id','product_id');
    }
}
