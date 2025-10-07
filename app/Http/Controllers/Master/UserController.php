<?php

namespace App\Http\Controllers\Master;

use App\AuthGroup;
use App\Department;
use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index()
    {
        $authGroup = AuthGroup::all();
        $department = Department::where('status', '1')->get();

        return view('master.user.index', compact('authGroup', 'department'));
    }

    public function data()
    {
        $data = User::select('*')
            ->with(['group', 'department']);
        return datatables()->of($data)
            ->make(true);
    }

    public function store(Request $request)
    {
        $request->validate([
            'username' => 'required',
            'name' => 'required',
            'auth_group_id' => 'required',
            'department_id' => 'required',
            'password' => 'required', 
        ]);
    
        $ifExist = User::where('username', $request->username)->first();
    
        if ($ifExist) {
            return response()->json([
                'status' => 'error',
                'message' => 'Username sudah ada',
            ]);
        }
    
        $password = null;
    
        if ($request->id == null) {
            $password = bcrypt($request->password);
        }
    
        $data = User::updateOrCreate(
            ['id' => $request->id],
            [
                'username' => $request->username,
                'name' => $request->name,
                'email' => $request->email,
                'auth_group_id' => $request->auth_group_id,
                'dept_id' => $request->department_id,
                'status' => 1, // 'status' => $request->status,
                'password' => $password, 
            ]
        );
    
        if ($data) {
            $user = User::find($data->id);
            $user->group()->associate($request->auth_group_id);
            $user->save();
    
            return response()->json([
                'status' => 'success',
                'message' => 'Data berhasil disimpan',
            ]);
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'Data gagal disimpan',
            ]);
        }
    }
    

    public function update($id)
    {
        try {
            $user = User::findOrFail($id);
            $user->status = $user->status == 1 ? 0 : 1;
            $user->save();

            $message = $user->status == 1 ? 'Data user berhasil diaktifkan.' : 'Data user berhasil dinonaktifkan.';

            return response()->json([
                'status' => 'success',
                'message' => $message,
                'new_status' => $user->status, // Tambahkan new_status untuk memberikan status baru ke frontend
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan saat mengupdate user.'
            ], 500);
        }
    }

    public function ubah($id)
    {
        $user = User::findOrFail($id);
        if ($user) {
            return response()->json([
                'status' => 'success',
                'user' => $user
            ]);
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'User not found'
            ]);
        }
    }

    public function prosesUbah(Request $request)
    {
        $id = $request->id;
        $user = User::find($id);

        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'User not found',
            ]);
        }

        // Update data pengguna dengan nilai-nilai baru dari permintaan
        $user->username = $request->username;
        $user->name = $request->name;
        $user->email = $request->email;
        $user->auth_group_id = $request->auth_group_id;
        $user->dept_id = $request->department_id;
        $user->status = 1;

        // Cek dan update password jika ada
        if ($request->has('edit_password') && $request->edit_password) {
            $user->password = bcrypt($request->edit_password);
        }

        // Simpan perubahan data pengguna
        if ($user->save()) {
            return response()->json([
                'status' => 'success',
                'message' => 'User updated successfully',
                'user' => $user,
            ]);
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to update user',
            ]);
        }
    }

}
