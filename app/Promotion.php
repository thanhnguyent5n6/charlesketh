<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Cache;

class Promotion extends Model
{
    protected $table = 'promotions';
    protected $guarded = [];

    public static function boot(){
    	parent::boot();
    	static::deleted(function($setting){
			Cache::forget("promotions");
		});
		static::created(function($setting){
			Cache::forget("promotions");
		});
		static::updated(function($setting){
			Cache::forget("promotions");
		});
    }
}
