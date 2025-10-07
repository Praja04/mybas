<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\AuthPermission;

class AuthPermissionController extends Controller
{
	public function index()
	{
		$permissions = AuthPermission::orderBy('name')->get();
		return view('permission.auth-permission', ['auth_permissions' => $permissions]);
	}
	public function store(Request $request)
	{
		$validate = $request->validate([
			'name' => 'required',
			'codename' => 'required'
		]);
		$permission = new AuthPermission;
		$permission->name = $request->name;
		$permission->codename = $request->codename;
		if($permission->save())
		{
			// Jika sukses disimpan
			return response()->json(['success' => 1]);
		}else{
			// Jika gagal disimpan
			return response()->json(['success' => 0]);
		}
	}
}
