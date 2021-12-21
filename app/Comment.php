<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    protected $table = 'comments';
    protected $guarded = [];

    public function parent() {
        return $this->belongsTo(static::class, 'parent');
    }

    public function children() {
        return $this->hasMany(static::class, 'parent');
    }

    public function product() {
        return $this->belongsTo('App\Product', 'product_id');
    }

    public function post() {
        return $this->belongsTo('App\Post', 'post_id');
    }
}
