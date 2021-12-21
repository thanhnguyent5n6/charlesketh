<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SeoLanguage extends Model
{
    protected $table = 'seo_languages';
    protected $guarded = [];
    protected $casts = ['meta_seo'=>'json'];
    public $timestamps = false;
    
    public function seo(){
    	return $this->belongsTo('App\Seo', 'seo_id');
    }
}
