<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SearchLanguage extends Model
{
    protected $table = 'search_languages';
    protected $guarded = [];
    protected $casts = ['meta_seo'=>'json'];
    public $timestamps = false;
    
    public function search(){
    	return $this->belongsTo('App\Search', 'search_id');
    }
}
