<?php

namespace Modules\Users\Http\Middleware;

use Closure;

/**
 * Class AclMiddleware
 * @package Modules\Users\Http\Middleware
 */
class AclMiddleware
{

    /**
     * @param $request
     * @param Closure $next
     * @param $permissions
     * @return mixed
     */
    public function handle($request, Closure $next, $permissions)
    {
        foreach ($permissions = explode(',', trim($permissions)) as $permission) {
            if (!$request->user()->hasPermissionTo($permission)) {
                abort(403);
            }
        }
        return $next($request);
    }

}