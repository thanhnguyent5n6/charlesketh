<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MediaLibrary extends Model
{
    protected $table = 'media_libraries';
    protected $guarded = [];
    protected $casts = ['editor'=>'json'];
}
