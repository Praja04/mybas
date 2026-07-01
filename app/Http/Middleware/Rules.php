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

        // Tambahkan direct permission (auth_user_permission) supaya permission
        // yang di-attach langsung ke user (di luar group) tetap berlaku
        // untuk guard menu di header. Tanpa ini, permission yang hanya
        // tersimpan sebagai direct permission tidak akan pernah match
        // di `in_array('xxx', $permissions)`.
        if (method_exists(Auth::user(), 'getAllPermissionCodenames')) {
            $permissions = array_values(array_unique(array_merge(
                $permissions,
                Auth::user()->getAllPermissionCodenames()
            )));
        }

        \View::share('permissions', $permissions);
        return $next($request);
    }
}
