<?php

namespace Modules\Users\Http\Middleware;

use Auth;
use Closure;

/**
 * Class AclMiddleware
 * @package Modules\User\Http\Middleware
 */
class AclMiddleware
{

    /**
     * Run the request filter.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  $role
     * @return mixed
     */
    public function handle($request, Closure $next, $permissions)
    {
        $perms = explode(',',$permissions);
        $user = Auth::user();
        if(!$user->ability('administrador-del-sistema', $perms)){
            abort(403);
        }
        return $next($request);
    }

}