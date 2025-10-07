<?php

namespace App\Http\Controllers\HRConnect;

use App\User;
use App\PKWAdmin;
use App\AdminDepartment;
use Illuminate\Http\Request;
use Yajra\Datatables\Datatables;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\PKWBagian;
use Illuminate\Support\Facades\Validator;

class MastersAdminController extends Controller
{
    public function getData()
    {
       $adm = AdminDepartment::all();
       return Datatables::of($adm)->make(true);
    }

    public function show($id)
    {
        $adm = AdminDepartment::findOrFail($id); // Use findOrFail to handle if the record doesn't exist
        return new JsonResponse($adm);
    }

    public function index()
    {
        $data['title'] = 'Masters Admin';
        $data['kode_bagian'] = PKWBagian::all();
        $data['kode_admin'] = PKWAdmin::all();
        $data['users'] = User::all();

        return view('hr-connect.masters.list_adm', $data);
    }

    public function store(Request $req)
    {
        $validator = Validator::make($req->all(), [
            'kode_bagian' => 'required',
            'kode_admin' => 'required',
            'nama_admin' => 'required'
        ]);

        if($validator->fails()){
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors(),
            ], 422);
        }else{
            $nik_admin = User::where('name', $req->nama_admin)->first();

            AdminDepartment::create([
                "kode_bagian" => $req->kode_bagian,
                "kode_admin" => $req->kode_admin,
                "nik_admin" => $nik_admin->username,
                "nama_admin" => $req->nama_admin,
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Berhasil tambah data masters admin!'
            ]);
        }
    }

    public function update(Request $req, $id)
    {
        $this->validate($req, [
            'kode_bagian' => 'required',
            'kode_admin' => 'required',
            'nama_admin' => 'required'
        ]);

        $nik_admin = User::where('name', $req->nama_admin)->first();

        AdminDepartment::where('id', $id)->update([
            "kode_bagian" => $req->kode_bagian,
            "kode_admin" => $req->kode_admin,
            "nik_admin" => $nik_admin->username,
            "nama_admin" => $req->nama_admin,
        ]);

        return response()->json(['msg' => 'Berhasil memperbarui data masters admin!']);
    }

    public function destroy($id)
    {
        $admin = AdminDepartment::find($id);

        if (!$admin) {
            return response()->json(['msg' => 'Data tidak ditemukan'], 404);
        }

        $admin->delete();

        return response()->json(['msg' => 'Data deleted successfully'], 200);
    }
}
