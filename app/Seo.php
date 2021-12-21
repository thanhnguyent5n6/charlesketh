<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Seo extends Model
{
    protected $table = 'seos';
    protected $guarded = [];

    public function languages(){
    	return $this->hasMany('App\SeoLanguage', 'seo_id', 'id')->orderBy('id','asc');
    }
}
