<?php

namespace App\Traits;

use App\AuthGroup;
use Illuminate\Support\Facades\Auth;

trait Permission
{
    static function has($permission)
    {
        $permissions = AuthGroup::find(Auth::user()->auth_group_id)->permissions()->orderBy('name')->get();
        if($permissions->where('codename', $permission)->first() == null) {
            // Ini berarti ga boleh akses. Langsung Redirect ke halaman 403
            abort(403);
        }
    }
}