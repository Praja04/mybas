<?php

namespace App\Http\Controllers\Sigra;

use App\Http\Controllers\Controller;
use App\Models\Sigra\MasterVendor;
use App\Models\Sigra\Perusahaan;
use Illuminate\Http\Request;

class MasterVendorController extends Controller
{
    public function index()
    {
        $perusahaan = Perusahaan::all();
        $vendors = MasterVendor::where('status', '!=', 'deleted')->get();
        return view('sigra.master-vendor', [
            'vendors' => $vendors,
            'perusahaan' => $perusahaan
        ]);
    }

    public function store(Request $request)
    {
        $vendor = new MasterVendor;
        $vendor->id_perusahaan = $request->perusahaan;
        $vendor->nama_vendor = $request->nama_vendor;
        $vendor->jenis_pekerjaan = $request->jenis_pekerjaan;
        $vendor->status = $request->status;
        $vendor->save();

        return response()->json(['success' => 1, 'message' => 'Create master vendor succeed']);
    }

    public function update(Request $request)
    {
        $vendor = MasterVendor::find($request->id);
        $vendor->id_perusahaan = $request->perusahaan;
        $vendor->nama_vendor = $request->nama_vendor;
        $vendor->jenis_pekerjaan = $request->jenis_pekerjaan;
        $vendor->status = $request->status;
        $vendor->save();

        return response()->json(['success' => 1, 'message' => 'Update master vendor succeed']);
    }

    public function delete($id)
    {
        $data = MasterVendor::find($id);
        $data->status = 'deleted';
        $data->save();
        return response()->json(['success' => 1, 'message' => 'Delete data succeed']);
    }

    public function get($id)
    {
        $data = MasterVendor::find($id);
        return response()->json(['success' => 1, 'message' => 'Get data succeed', 'data' => $data]);
    }
}
