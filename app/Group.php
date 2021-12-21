<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    protected $table = 'groups';
    protected $guarded = [];
    protected $casts = ['role_permission'=>'json'];

    public function users(){
    	return $this->belongsToMany('App\User','user_group','group_id','user_id');
    }
}
