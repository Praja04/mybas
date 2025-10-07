<?php

namespace App\Http\Controllers\HaloSecurity;

use App\HaloSecurity\BaIntrogasi;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class BaIntrogasiController extends Controller
{
    public function index()
    {
        $this->permission('hs_listbai');

        return view('pages.halo-security.list-ba-introgasi');
    }

    public function trash(Request $request)
    {
        
        $data = BaIntrogasi::query()->onlyTrashed();

        // filter by search
        // $baintrogasi->onlyTrashed()->when($request->search, function ($query) use ($request) {
        //     return $query
        //     ->where('nama_introgasi', 'like', '%'.$request->search.'%')
        //     ->OrWhere('nama_pelapor', 'like', '%'.$request->search.'%')
        //     ->OrWhere('nama_pelaku', 'like', '%'.$request->search.'%')
        //     ->OrWhere('nama_korban', 'like', '%'.$request->search.'%')
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

        // filter by status pelaku
        $data->when($request->status_pelaku, function ($query) use ($request) {
            return $query->whereStatusPelaku($request->status_pelaku)->onlyTrashed();
        });

        // filter by shift
        $data->when($request->shift, function ($query) use ($request) {
            return $query->whereShift($request->shift)->onlyTrashed();
        });

        $baintrogasi = $data->onlyTrashed()->orderBy('created_at', 'DESC')->get();

        return view('pages.halo-security.trash-ba-introgasi', compact('baintrogasi'));
    }

    public function kembalikan($bai_id)
    {
            $item = BaIntrogasi::onlyTrashed()->where('bai_id',$bai_id);
            $item->restore();
            return redirect()->route('ba-list-introgasi')->with(['success'=>'Data Berita Acara Introgasi berhasil dikembalikan']);
    }

    // restore semua data supr yang sudah dihapus
    public function kembalikan_semua()
    {
                
            $item = BaIntrogasi::onlyTrashed();
            $item->restore();
    
            return redirect()->route('ba-list-introgasi')->with(['success'=>'Data Berita Acara Introgasi berhasil dikembalikan semua']);
    }

    // hapus permanen
    public function hapus_permanen($bai_id)
    {
            // hapus permanen data guru
            $item = BaIntrogasi::onlyTrashed()->where('bai_id',$bai_id);
            $item->forceDelete();
    
            return redirect()->route('ba-list-introgasi')->with(['success'=>'Data Berita Acara Introgasi berhasil dihapus permanen']);
    }

    // hapus permanen semua guru yang sudah dihapus
    public function hapus_permanen_semua()
    {
            // hapus permanen semua data guru yang sudah dihapus
            $item = BaIntrogasi::onlyTrashed();
            $item->forceDelete();
    
            return redirect()->route('ba-list-introgasi')->with(['success'=>'Data Berita Acara Introgasi berhasil dihapus permanen semua']);
    }
}
