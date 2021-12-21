<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PostLanguage extends Model
{
    protected $table = 'post_languages';
    protected $guarded = [];
    protected $casts = ['meta_seo'=>'json', 'attributes'=>'json'];
    public $timestamps = false;
    
    public function post(){
    	return $this->belongsTo('App\Post', 'post_id');
    }
}
