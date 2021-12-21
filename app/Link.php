<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Link extends Model
{
    protected $table = 'links';
    protected $guarded = [];

    public function languages(){
    	return $this->hasMany('App\LinkLanguage', 'link_id', 'id')->orderBy('id','asc');
    }
}
