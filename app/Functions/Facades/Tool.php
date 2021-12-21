<?php
namespace App\Functions\Facades;
use App\Functions\ToolFactory;
use Illuminate\Support\Facades\Facade;

class Tool extends Facade {
    protected static function getFacadeAccessor() {
        return ToolFactory::class;
    }
}