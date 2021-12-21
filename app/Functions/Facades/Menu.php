<?php
namespace App\Functions\Facades;

use Illuminate\Support\Facades\Facade;
use App\Functions\MenuFactory;

class Menu extends Facade{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor() {
    	return MenuFactory::class;
    }
}