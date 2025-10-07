<?php

namespace App\Http\Middleware;

use Closure;
use App\AuthGroup;
use Illuminate\Support\Facades\Auth;

class Rules
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $permissions = [];
        $_permissions = AuthGroup::find(Auth::user()->auth_group_id)->permissions()->orderBy('name')->get();
        foreach ($_permissions as $permission) {
            $permissions[] = $permission->codename;
        }
        \View::share('permissions', $permissions);
        return $next($request);
    }
}
