<?php
namespace App\Http\Controllers;

use App\AuthGroup;
use App\AuthPermission;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AuthGroupController extends Controller
{
    public function index()
    {
        $auth_groups = AuthGroup::with('permissions')->latest()->get();
        return view('permission.auth-group', compact('auth_groups'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:auth_group,name|max:255',
        ],
            [
                'name.required' => 'Nama group harus diisi.',
                'name.unique'   => 'Nama group sudah terpakai.',
            ]);

        // $group       = new AuthGroup;
        // $group->name = $request->name;
        // if ($group->save()) {
        //     return response()->json(['success' => 1]);
        // } else {
        //     return response()->json(['success' => 0]);
        // }

        $group = AuthGroup::create(['name' => $request->name]);

        return response()->json(['success' => $group ? 1 : 0]);
    }

    public function get_permissions(Request $request)
    {
        $group = AuthGroup::findOrFail($request->id);

        $existingId = $group->permissions()->pluck('auth_permission.id')->toArray();

        $allPermissions = AuthPermission::select('id', 'codename')->get();

        $auth_permissions = $allPermissions->whereIn('id', $existingId)->values();

        $permissions_left = $allPermissions->whereNotIn('id', $existingId)->values();

        return response()->json([
            'success'          => 1,
            'auth_permissions' => $auth_permissions,
            'permissions_left' => $permissions_left,
        ]);
    }
    // $all_permissions  = AuthPermission::all();
    // $auth_permissions = AuthGroup::find($request->id)->permissions;

    // $all_permissions_array  = [];
    // $auth_permissions_array = [];

    // foreach ($auth_permissions as $permission) {
    //     $auth_permissions_array[] = [
    //         'id'       => $permission->id,
    //         'codename' => $permission->codename,
    //     ];
    // }

    // foreach ($all_permissions as $permission) {
    //     $all_permissions_array[] = ['id' => $permission->id, 'codename' => $permission->codename];
    // }
    // $permissions_left = $this->permission_left($all_permissions_array, $auth_permissions_array);

    // sort($auth_permissions_array);
    // sort($permissions_left);

    // return response()->json([
    //     'success'          => 1,
    //     'auth_permissions' => $auth_permissions_array,
    //     'all_permissions'  => $all_permissions_array,
    //     'permissions_left' => $permissions_left,
    // ]);

    // public function permission_left($array1, $array2)
    // {
    //     foreach ($array1 as $key => $data) {
    //         if (in_array($data, $array2)) {
    //             unset($array1[$key]);
    //         }
    //     }
    //     return $array1;
    // }

    public function change_permissions(Request $request)
    {
        $request->validate([
            'group_id'      => 'required|exists:auth_group,id',
            'permissions'   => 'nullable|array',
            'permissions.*' => 'exists:auth_permission,id',
        ]);

        // Hapus dulu semua permission yang sebelumnya.
        // Berdasarkan id group

        // $delete_permission = AuthGroupPermission::where('group_id', $request->group_id)->delete();
        // // Jika sudah dihapus, simpan permission baru yang dicentang
        // foreach ($request->permissions as $permission) {
        //     $save_permission                = new AuthGroupPermission;
        //     $save_permission->group_id      = $request->group_id;
        //     $save_permission->permission_id = $permission;
        //     $save_permission->save();
        // }

        // return response()->json(['success' => 1]);

        // if($request->permissions)
        //     // Ini jika ada salah satu yang dicentang
        // }

        try {
            DB::beginTransaction();

            $group = AuthGroup::findOrFail($request->group_id);

            $group->permissions()->sync($request->permissions ?? []); // Sync akan otomatis hapus yang tidak ada di array dan tambah yang baru
            DB::commit();

            return response()->json(['success' => 1, 'message' => 'Permissions berhasil diperbarui.'], 200);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['success' => 0, 'message' => 'Gagal memperbarui permissions: ' . $e->getMessage()], 500);
        }
    }

    public function update(Request $request)
    {
        $request->validate([
            'id'   => 'required|exists:auth_group,id',
            'name' => 'required|unique:auth_group,name,' . $request->id . '|max:255',
        ],
            [
                'id.required'   => 'ID group tidak ditemukan.',
                'id.exists'     => 'Group tidak ditemukan.',
                'name.required' => 'Nama group harus diisi.',
                'name.unique'   => 'Nama group sudah terpakai.',
            ]);

        $group = AuthGroup::findOrFail($request->id);
        $group->update($request->only('name'));

        return response()->json(['success' => 1, 'message' => 'Group berhasil diperbarui.'], 200);
    }

    public function delete(Request $request)
    {
        $group = AuthGroup::findOrFail($request->id);
        $group->permissions()->detach(); // Lepas semua relasi dengan permissions
        $group->delete();                // Hapus group

        return response()->json(['success' => 1, 'message' => 'Group berhasil dihapus.'], 200);
    }
}
