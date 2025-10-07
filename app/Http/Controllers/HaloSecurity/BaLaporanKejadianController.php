<?php

namespace App\Http\Controllers\HaloSecurity;

use App\HaloSecurity\BaLaporanKejadian;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class BaLaporanKejadianController extends Controller
{
    public function index()
    {
        $this->permission('hs_kejadian');

        return view('pages.halo-security.list-ba-laporan-kejadian');
    }

    public function trash(Request $request)
    {
        
        $data = BaLaporanKejadian::query()->onlyTrashed();

        // filter by search
        // $balaporankejadian->onlyTrashed()->when($request->search, function ($query) use ($request) {
        //     return $query
        //     ->where('nama_korban', 'like', '%'.$request->search.'%')
        //     ->OrWhere('nik_korban', 'like', '%'.$request->search.'%')
        //     ->OrWhere('perusahaan_korban', 'like', '%'.$request->search.'%')
        //     ;
        // });

        // filter by date
        $data->when($request->created_at, function ($query) use ($request) {
            return $query->where('created_at', '>=', $request->created_at)->onlyTrashed();
        });

        // filter by jenis_kejadian
        $data->when($request->jenis_kejadian, function ($query) use ($request) {
            return $query->whereJenisKejadian($request->jenis_kejadian)->onlyTrashed();
        });

        // filter by status terlapor
        $data->when($request->status_terlapor, function ($query) use ($request) {
            return $query->whereStatusTerlapor($request->status_terlapor)->onlyTrashed();
        });

        $balaporankejadian = $data->onlyTrashed()->orderBy('created_at', 'DESC')->get();

        return view('pages.halo-security.trash-ba-laporan-kejadian', compact('balaporankejadian'));
    }

    public function kembalikan($lk_id)
    {
            $item = BaLaporanKejadian::onlyTrashed()->where('lk_id',$lk_id);
            $item->restore();
            return redirect()->route('ba-list-laporankejadian')->with(['success'=>'Data Berita Acara Laporan Kejadian berhasil dikembalikan']);
    }

    // restore semua data supr yang sudah dihapus
    public function kembalikan_semua()
    {
                
            $item = BaLaporanKejadian::onlyTrashed();
            $item->restore();
    
            return redirect()->route('ba-list-laporankejadian')->with(['success'=>'Data Berita Acara Laporan Kejadian berhasil dikembalikan semua']);
    }

    // hapus permanen
    public function hapus_permanen($lk_id)
    {
            // hapus permanen data guru
            $item = BaLaporanKejadian::onlyTrashed()->where('lk_id',$lk_id);
            $item->forceDelete();
    
            return redirect()->route('ba-list-laporankejadian')->with(['success'=>'Data Berita Acara Laporan Kejadian berhasil dihapus permanen']);
    }

    // hapus permanen semua guru yang sudah dihapus
    public function hapus_permanen_semua()
    {
            // hapus permanen semua data guru yang sudah dihapus
            $item = BaLaporanKejadian::onlyTrashed();
            $item->forceDelete();
    
            return redirect()->route('ba-list-laporankejadian')->with(['success'=>'Data Berita Acara Laporan Kejadian berhasil dihapus permanen semua']);
    }
}
