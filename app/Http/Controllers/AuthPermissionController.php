<?php
namespace App\Http\Controllers;

use App\AuthPermission;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AuthPermissionController extends Controller
{
    public function index()
    {
        $permissions = AuthPermission::orderBy('name')->get();
        return view('permission.auth-permission', ['auth_permissions' => $permissions]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'codename' => 'required|string|unique:auth_permission,codename,' . $request->id,
        ],
            [
                'name.required'     => 'Nama permission harus diisi.',
                'codename.required' => 'Codename permission harus diisi.',
                'codename.unique'   => 'Slug / Codename sudah terpakai.',
            ]);

        $slug = Str::lower(Str::snake($request->name));

        try {
            $permission = AuthPermission::updateOrCreate(
                ['id' => $request->id],
                [
                    'name'     => $request->name,
                    'codename' => $slug,
                ]
            );

            $message = $request->id ? 'Permission berhasil diperbarui.' : 'Permission berhasil dibuat.';

            return response()->json(['status' => 'success', 'message' => $message, 'data' => $permission]);

        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => 'Gagal simpan data: ' . $e->getMessage()]);
        }

    }

    public function edit($id)
    {
        $permission = AuthPermission::find($id);
        return response()->json($permission);
    }

    public function destroy($id)
    {
        try {
            $permission = AuthPermission::findOrFail($id);

            // 1. Lepas relasi dengan group terlebih dahulu
            $permission->groups()->detach();

            // 2. BARU hapus data utamanya
            $permission->delete();

            return response()->json([
                'status'  => 'success',
                'message' => 'Data berhasil dihapus dari sistem dan group terkait',
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Terjadi kesalahan sistem: ' . $e->getMessage(),
            ], 500);
        }
    }
}
