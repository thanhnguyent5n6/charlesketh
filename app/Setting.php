<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Cache;

class Setting extends Model
{
    protected $table = 'settings';
    protected $guarded = [];
    public $timestamps = false;

    public static function boot(){
    	parent::boot();
    	static::deleted(function($setting){
			Cache::forget("settings");
		});
		static::created(function($setting){
			Cache::forget("settings");
		});
		static::updated(function($setting){
			Cache::forget("settings");
		});
    }
}
