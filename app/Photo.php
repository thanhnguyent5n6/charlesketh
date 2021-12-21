<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Photo extends Model
{
    protected $table = 'photos';
    protected $guarded = [];

    public function languages(){
    	return $this->hasMany('App\PhotoLanguage', 'photo_id', 'id')->orderBy('id','asc');
    }
}
