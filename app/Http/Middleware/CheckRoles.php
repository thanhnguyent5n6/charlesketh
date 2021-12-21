<?php

namespace App\Http\Middleware;

use Closure;
use Notifiable;
use App\Role;
use App\Permission;

class checkRoles
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */

    public function handle($request, Closure $next, $roles)
    {
        if ( auth()->user()->hasRole('admin') ) {
            return $next($request);
        }else{
            $args = func_get_args();
            unset($args[0], $args[1]);
            $args = array_values($args);

            $act = $request->route()->getActionMethod();
            $type = $request->type ? $request->type : '' ;
            switch($act){
                case 'index'; case 'customer'; case 'show'; case 'mask'; case 'maskSmall'; case 'price'; case 'export'; case 'ajax': $act = 'view'; break;
                case 'create';case 'print'; case 'store': $act = 'create'; break;
                case 'edit'; case 'update': $act = 'edit'; break;
                case 'delete': $act = 'delete'; break;
            }

            $groups = auth()->user()->groups()->pluck('id')->toArray();
            $perms = [];
            
            if($type == '' || $type == 'default'){
                foreach($args as $arg){
                    foreach ($groups as $group) {
                        $perms[] = $arg.'-'.$act.'-'.$group;
                    }
                }
                if ( auth()->user()->hasRole($args) ) {
                    if ( auth()->user()->can($perms) ){
                        return $next($request);
                    }
                }
            }else{
                foreach ($groups as $group) {
                    $perms[] = $type.'-'.$act.'-'.$group;
                }

                if ( auth()->user()->hasRole($type) ) {
                    if ( auth()->user()->can($perms) ){
                        return $next($request);
                    }
                }
            }
            
        }
        return abort(403, 'Bạn không được phép truy cập.');
    }
}
