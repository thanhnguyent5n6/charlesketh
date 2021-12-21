<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AttributeLanguage extends Model
{
    protected $table = 'attribute_languages';
    protected $guarded = [];
    protected $casts = ['meta_seo'=>'json'];
    public $timestamps = false;
    
    public function attribute(){
    	return $this->belongsTo('App\Attribute', 'attribute_id');
    }
}
