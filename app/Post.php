<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    protected $table = 'posts';
    protected $guarded = [];

    public function languages(){
    	return $this->hasMany('App\PostLanguage', 'post_id', 'id')->orderBy('id','asc');
    }

    public function category(){
    	return $this->belongsTo('App\Category', 'category_id');
    }

    public function attribute(){
    	return $this->belongsToMany('App\Attribute','post_attribute','post_id','attribute_id');
    }

    public function getIdsAttribute($type=''){
        return $this->attribute->where('type',$type)->pluck('id')->toArray();
    }
}
