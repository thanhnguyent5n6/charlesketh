<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CategoryLanguage extends Model
{
    protected $table = 'category_languages';
    protected $guarded = [];
    protected $casts = ['meta_seo'=>'json'];
    public $timestamps = false;
    
    public function category(){
    	return $this->belongsTo('App\Category', 'category_id');
    }
}
