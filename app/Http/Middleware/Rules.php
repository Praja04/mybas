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
     * @param  string[]  ...$requiredPermissions
     * @return mixed
     */
    public function handle($request, Closure $next, ...$requiredPermissions)
    {
        $permissions = [];

        if (Auth::check()) {
            if (Auth::user()->auth_group_id) {
                $group = AuthGroup::find(Auth::user()->auth_group_id);
                if ($group) {
                    $_permissions = $group->permissions()->orderBy('name')->get();
                    foreach ($_permissions as $permission) {
                        $permissions[] = $permission->codename;
                    }
                }
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

            // Jika route mensyaratkan permission tertentu (contoh: rules:qcc-pool)
            if (!empty($requiredPermissions)) {
                $hasPermission = false;
                foreach ($requiredPermissions as $reqPerm) {
                    if (in_array(trim($reqPerm), $permissions)) {
                        $hasPermission = true;
                        break;
                    }
                }

                if (!$hasPermission) {
                    if ($request->ajax() || $request->wantsJson()) {
                        return response()->json([
                            'status' => 'error',
                            'message' => 'Anda tidak memiliki hak akses (' . implode(', ', $requiredPermissions) . ') untuk mengakses halaman ini.',
                        ], 403);
                    }
                    abort(403, 'Akses Ditolak: Anda tidak memiliki hak akses (' . implode(', ', $requiredPermissions) . ') untuk membuka modul ini.');
                }
            }
        }

        \View::share('permissions', $permissions);
        return $next($request);
    }
}
