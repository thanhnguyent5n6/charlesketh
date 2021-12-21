<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PageLanguage extends Model
{
    protected $table = 'page_languages';
    protected $guarded = [];
    protected $casts = ['meta_seo'=>'json'];
    public $timestamps = false;
    
    public function page(){
    	return $this->belongsTo('App\Page', 'page_id');
    }
}
