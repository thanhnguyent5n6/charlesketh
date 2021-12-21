<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LinkLanguage extends Model
{
    protected $table = 'link_languages';
    protected $guarded = [];
    public $timestamps = false;
    
    public function link(){
    	return $this->belongsTo('App\Link', 'link_id');
    }
}
