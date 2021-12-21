<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProductLanguage extends Model
{
    protected $table = 'product_languages';
    protected $guarded = [];
    protected $casts = ['meta_seo'=>'json', 'attributes'=>'json'];
    public $timestamps = false;
    
    public function product(){
    	return $this->belongsTo('App\Product', 'product_id');
    }
}
