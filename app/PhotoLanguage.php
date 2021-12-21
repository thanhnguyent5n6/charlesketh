<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PhotoLanguage extends Model
{
    protected $table = 'photo_languages';
    protected $guarded = [];
    public $timestamps = false;
    
    public function photo(){
    	return $this->belongsTo('App\Photo', 'photo_id');
    }
}
