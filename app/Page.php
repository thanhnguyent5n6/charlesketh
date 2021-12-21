<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Page extends Model
{
    protected $table = 'pages';
    protected $guarded = [];

    public function languages(){
    	return $this->hasMany('App\PageLanguage', 'page_id', 'id')->orderBy('id','asc');
    }
}
