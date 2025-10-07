<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\AuthGroup;
use App\AuthPermission;
use App\AuthGroupPermission;

class AuthGroupController extends Controller
{
    public function index()
    {
        $auth_groups = AuthGroup::all();
        return view('permission.auth-group', ['auth_groups' => $auth_groups]);
    }

    public function store(Request $request)
    {
        $validate = $request->validate([
            'name' => 'required'
        ]);
        $group = new AuthGroup;
        $group->name = $request->name;
        if ($group->save()) {
            return response()->json(['success' => 1]);
        } else {
            return response()->json(['success' => 0]);
        }
    }

    public function get_permissions(Request $request)
    {
    	$all_permissions = AuthPermission::all();
    	$auth_permissions = AuthGroup::find($request->id)->permissions;

    	$all_permissions_array = [];
    	$auth_permissions_array = [];

    	foreach ($auth_permissions as $permission) {
    		$auth_permissions_array[] = [
                'id' => $permission->id,
                'codename' => $permission->codename
            ];
    	}

    	foreach ($all_permissions as $permission) {
    		$all_permissions_array[] = ['id' => $permission->id, 'codename' => $permission->codename];
    	}
    	$permissions_left = $this->permission_left($all_permissions_array, $auth_permissions_array);

    	sort($auth_permissions_array);
    	sort($permissions_left);

    	return response()->json([
    		'success' => 1,
    		'auth_permissions' => $auth_permissions_array,
    		'all_permissions' => $all_permissions_array,
    		'permissions_left' => $permissions_left
    	]);
    }

    public function permission_left($array1, $array2)
    {
        foreach ($array1 as $key => $data) {
            if (in_array($data, $array2)) {
                unset($array1[$key]);
            }
        }
        return $array1;
    }

    public function change_permissions(Request $request)
    {
        // Hapus dulu semua permission yang sebelumnya.
        // Berdasarkan id group


        $delete_permission = AuthGroupPermission::where('group_id', $request->group_id)->delete();
        // Jika sudah dihapus, simpan permission baru yang dicentang
        foreach ($request->permissions as $permission) {
            $save_permission = new AuthGroupPermission;
            $save_permission->group_id = $request->group_id;
            $save_permission->permission_id = $permission;
            $save_permission->save();
        }

        return response()->json(['success' => 1]);

        // if($request->permissions)
        //     // Ini jika ada salah satu yang dicentang
        // }
    }
}
