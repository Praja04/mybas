<?php

namespace App\Http\Controllers\HaloSecurity;

use App\HaloSecurity\BaSopSupir;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class BaSopSupirController extends Controller
{
    public function index()
    {
        $this->permission('hs_sop');

        return view('pages.halo-security.list-ba-sop-supir');
    }

    public function trash(Request $request)
    {
        $data = BaSopSupir::query()->onlyTrashed();

        // filter by search
        // $basopsupir->onlyTrashed()->when($request->search, function ($query) use ($request) {
        //     return $query
        //     ->where('nama', 'like', '%'.$request->search.'%')
        //     ->OrWhere('ekspedisi', 'like', '%'.$request->search.'%')
        //     ->OrWhere('no_ktp', 'like', '%'.$request->search.'%')
        //     ->OrWhere('no_polisi', 'like', '%'.$request->search.'%')
        //     ->OrWhere('no_handphone', 'like', '%'.$request->search.'%')
        //     ->OrWhere('no_kartu', 'like', '%'.$request->search.'%')
        //     ->OrWhere('alamat', 'like', '%'.$request->search.'%')
        //     ->OrWhere('nama_pembuat', 'like', '%'.$request->search.'%')
        //     ->OrWhere('jabatan_pembuat', 'like', '%'.$request->search.'%')
        //     ;
        // });

        // filter by date
        $data->when($request->created_at, function ($query) use ($request) {
            return $query->where('created_at', '>=', $request->created_at)->onlyTrashed();
        });

        // filter by shift
        $data->when($request->shift, function ($query) use ($request) {
            return $query->whereShift($request->shift)->onlyTrashed();
        });

        $basopsupir = $data->onlyTrashed()->orderBy('created_at', 'DESC')->get();

        return view('pages.halo-security.trash-ba-sop-supir', compact('basopsupir'));
    }

    public function kembalikan($id)
    {
            $item = BaSopSupir::onlyTrashed()->where('id',$id);
            $item->restore();
            return redirect()->route('ba-sop-list-supir')->with(['success'=>'Data BA S.O.P Supir berhasil dikembalikan']);
    }

    // restore semua data supr yang sudah dihapus
    public function kembalikan_semua()
    {
                
            $item = BaSopSupir::onlyTrashed();
            $item->restore();
    
            return redirect()->route('ba-sop-list-supir')->with(['success'=>'Data BA S.O.P Supir berhasil dikembalikan semua']);
    }

    // hapus permanen
    public function hapus_permanen($id)
    {
            // hapus permanen data guru
            $item = BaSopSupir::onlyTrashed()->where('id',$id);
            $item->forceDelete();
    
            return redirect()->route('ba-sop-list-supir')->with(['success'=>'Data BA S.O.P Supir berhasil dihapus permanen']);
    }

    // hapus permanen semua guru yang sudah dihapus
    public function hapus_permanen_semua()
    {
            // hapus permanen semua data guru yang sudah dihapus
            $item = BaSopSupir::onlyTrashed();
            $item->forceDelete();
    
            return redirect()->route('ba-sop-list-supir')->with(['success'=>'Data BA S.O.P Supir berhasil dihapus permanen semua']);
    }
}
