<?php

namespace App\Http\Controllers\HaloSecurity;

use App\HaloSecurity\BaSopKaryawan;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class BaSopKaryawanController extends Controller
{
    public function index()
    {
        $this->permission('hs_sop');
        
        return view('pages.halo-security.list-ba-sop-karyawan');
    }

    public function trash(Request $request)
    {
        
        $data = BaSopKaryawan::query()->onlyTrashed();

        // filter by search
        // $basopkaryawan->onlyTrashed()->when($request->search, function ($query) use ($request) {
        //     return $query
        //     ->where('nama', 'like', '%'.$request->search.'%')
        //     ->OrWhere('nik', 'like', '%'.$request->search.'%')
        //     ->OrWhere('jabatan', 'like', '%'.$request->search.'%')
        //     ->OrWhere('nama_pembuat', 'like', '%'.$request->search.'%')
        //     ->OrWhere('jabatan_pembuat', 'like', '%'.$request->search.'%')
        //     ->OrWhere('nama_area', 'like', '%'.$request->search.'%')
        //     ->OrWhere('barang', 'like', '%'.$request->search.'%')
        //     ;
        // });

        // filter by date
        $data->when($request->created_at, function ($query) use ($request) {
            return $query->where('created_at', '>=', $request->created_at)->onlyTrashed();
        });

        // filter by jenis kelamin
        $data->when($request->jenis_kelamin, function ($query) use ($request) {
            return $query->whereJenis_kelamin($request->jenis_kelamin)->onlyTrashed();
        });

        // filter by shift
        $data->when($request->shift, function ($query) use ($request) {
            return $query->whereShift($request->shift)->onlyTrashed();
        });

        $basopkaryawan = $data->onlyTrashed()->orderBy('created_at', 'DESC')->get();

        return view('pages.halo-security.trash-ba-sop-karyawan', compact('basopkaryawan'));
    }

    public function kembalikan($id)
    {
            $item = BaSopKaryawan::onlyTrashed()->where('id',$id);
            $item->restore();
            return redirect()->route('ba-sop-list-karyawan')->with(['success'=>'Data BA S.O.P Karyawan berhasil dikembalikan']);
    }

    // restore semua data supr yang sudah dihapus
    public function kembalikan_semua()
    {
                
            $item = BaSopKaryawan::onlyTrashed();
            $item->restore();
    
            return redirect()->route('ba-sop-list-karyawan')->with(['success'=>'Data BA S.O.P Karyawan berhasil dikembalikan semua']);
    }

    // hapus permanen
    public function hapus_permanen($id)
    {
            // hapus permanen data guru
            $item = BaSopKaryawan::onlyTrashed()->where('id',$id);
            $item->forceDelete();
    
            return redirect()->route('ba-sop-list-karyawan')->with(['success'=>'Data BA S.O.P Karyawan berhasil dihapus permanen']);
    }

    // hapus permanen semua guru yang sudah dihapus
    public function hapus_permanen_semua()
    {
            // hapus permanen semua data guru yang sudah dihapus
            $item = BaSopKaryawan::onlyTrashed();
            $item->forceDelete();
    
            return redirect()->route('ba-sop-list-karyawan')->with(['success'=>'Data BA S.O.P Karyawan berhasil dihapus permanen semua']);
    }
}
