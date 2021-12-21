<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
class Category extends Model
{
    protected $table = 'categories';
    protected $guarded = [];

    public function parent() {
        return $this->belongsTo(static::class, 'parent');
    }
    
    public function children() {
        return $this->hasMany(static::class, 'parent');
    }

    public function languages(){
        return $this->hasMany('App\CategoryLanguage', 'category_id', 'id')->orderBy('id','asc');
    }

    public function products(){
        return $this->hasMany('App\Product', 'category_id', 'id');
    }

    public function posts(){
    	return $this->hasMany('App\Post', 'category_id', 'id');
    }
    
}
