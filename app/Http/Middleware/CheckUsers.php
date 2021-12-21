<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckUsers {
    
    protected $request;
    protected $app;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function handle($request, Closure $next)
    {
        if ( auth()->user()->hasRole('admin') ) {
            return $next($request);
        }else{
            $args = func_get_args();
            unset($args[0], $args[1]);
            $args = array_values($args);
            foreach($args as $arg){
                if ( auth()->user()->type == $arg ) {
                    return $next($request);
                }
            }
        }
        auth()->logout();
        return abort(403, 'Unauthorized action.');
    }

}