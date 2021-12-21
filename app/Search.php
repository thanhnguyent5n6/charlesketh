<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Search extends Model
{
    protected $table = 'searchs';
    protected $guarded = [];

    public function languages(){
    	return $this->hasMany('App\SearchLanguage', 'search_id', 'id')->orderBy('id','asc');
    }
}
