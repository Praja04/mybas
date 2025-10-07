<?php

namespace App\Http\Livewire;

use App\Exports\HaloSecurity\HsBaIntrogasi;
use App\HaloSecurity\BaIntrogasi;
use App\HaloSecurity\DokumentasiIntrogasi;
use Illuminate\Http\Request;
use Livewire\Component;
use Livewire\WithPagination;
use App\HaloSecurity\SecurityUserGA;
use App\Mail\HaloSecurity\DeleteBaIntrogasi;
use App\Mail\HaloSecurity\DeleteLaporanKejadian;
use Illuminate\Support\Facades\Mail;
use App\Traits\Permission;
use App\User;
use Excel;
use PDF;

class Listbaintrogasi extends Component
{
    // use WithPagination;

    // protected $paginationTheme = 'bootstrap';

    public function deleteBai($bai_id)
    {
        $item = BaIntrogasi::findOrFail($bai_id);
        // $item = DokumentasiIntrogasi::findOrFail($bai_id);
        // $item->dokumentasibais->each(function ($dokumentasibai) {
        //     $dokumentasibai->delete();
        // });

        // $item->baiitems->each(function ($baiitem) {
        //     $baiitem->delete();
        // });

        $item->delete();

        $usersArray = SecurityUserGA::all()->pluck('nik')->toArray();

        $users = User::whereIn('username', $usersArray)->get()
        ->pluck('email');

        $users = $users->filter(function ($item) {
            return $item != '';
        });

        $emails = $users->toArray();

        $this->sendDeleteIntrogasiConfirmationMail($emails, $item);

        return redirect()->route('ba-list-introgasi')->with(['success'=>'Data BA Introgasi berhasil dihapus']);;
    }

    public function sendDeleteIntrogasiConfirmationMail($emails, $item)
    {
        Mail::mailer('smtp')->to($emails)->send(new DeleteBaIntrogasi($item));
    }

    public function print_pdf($bai_id)
    {
        $item = BaIntrogasi::find($bai_id);

        $pdf = PDF::loadView('pages.halo-security.ba-introgasi',compact('item'));

        return $pdf->download('Data Berita Acara Introgasi.pdf');
    }

    public function print_pdfonepage($bai_id)
    {
        $item = BaIntrogasi::find($bai_id);

        $pdf = PDF::loadView('pages.halo-security.ba-introgasi-onepage',compact('item'));

        return $pdf->download('Data Berita Acara Introgasi Satu Halaman.pdf');
    }

    public function print_dokumenttd($bai_id)
    {
        $item = BaIntrogasi::find($bai_id);

        $pdf = PDF::loadView('pages.halo-security.ba-introgasi-dokumenttd',compact('item'));

        return $pdf->download('Dokumen Berita Acara Introgasi Yang Sudah Di Tanda Tangan.pdf');
    }

    public function exportexcelintrogasi(Request $request)
    {
        return Excel::download(new HsBaIntrogasi(),'Halo Security Berita Acara Introgasi.xlsx');
    }

    public function get_introgasi($bai_id)
    {
        $introgasi = BaIntrogasi::find($bai_id);
        return response()->json($introgasi);
    }

    public function upload_dokumenttd(Request $request)
    {
        $introgasi = BaIntrogasi::find($request->bai_id);

        if($request->hasfile('dokumen_ttd'))
        {
            $file = $request->file('dokumen_ttd');
            $extention = $file->getClientOriginalExtension();
            $filename = time().'.'.$extention;
            $file->move('onepage_ttd', $filename);
            $introgasi->dokumen_ttd = $filename;
        }
        
        $introgasi->save();
        return response()->json(['success'=>'Data Dokumen Satu Halaman Yang Sudah Di Tanda Tangan berhasil di simpan', $introgasi]);
    }

    public function render(Request $request)
    {   
        $data = BaIntrogasi::query();

        // filter by search
        // $data->when($request->search, function ($query) use ($request) {
        //     return $query
        //     ->where('nama_introgasi', 'like', '%'.$request->search.'%')
        //     ->OrWhere('nama_pelapor', 'like', '%'.$request->search.'%')
        //     ->OrWhere('nama_pelaku', 'like', '%'.$request->search.'%')
        //     ->OrWhere('nama_korban', 'like', '%'.$request->search.'%')
        //     ;
        // });

        // filter by date
        $data->when($request->created_at, function ($query) use ($request) {
            return $query->where('created_at', '>=', $request->created_at);
        });

        // filter by jenis_kejadian
        $data->when($request->jenis_kejadian, function ($query) use ($request) {
            return $query->whereJenisKejadian($request->jenis_kejadian);
        });

        // filter by status pelaku
        $data->when($request->status_pelaku, function ($query) use ($request) {
            return $query->whereStatusPelaku($request->status_pelaku);
        });

        // filter by shift
        $data->when($request->shift, function ($query) use ($request) {
            return $query->whereShift($request->shift);
        });

        $baintrogasi = $data->orderBy('created_at', 'DESC')->whereNull('status_draft')->get();

        return view('livewire.listbaintrogasi', compact('baintrogasi'));
    }
}
