<?php

namespace App\Http\Livewire;

use App\Exports\HaloSecurity\HsBaLaporanKejadian;
use App\HaloSecurity\BaLaporanKejadian;
use App\HaloSecurity\DokumentasiKejadian;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Http\Request;
use PDF;
use App\HaloSecurity\SecurityUserGA;
use App\Mail\HaloSecurity\DeleteLaporanKejadian;
use Illuminate\Support\Facades\Mail;
use App\User;
use Excel;
use Illuminate\Support\Facades\File;

class Listlaporankejadian extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public function deleteLk($lk_id)
    {
        $item = BaLaporanKejadian::findOrFail($lk_id);

        $item->delete();

        $usersArray = SecurityUserGA::all()->pluck('nik')->toArray();

        $users = User::whereIn('username', $usersArray)->get()
        ->pluck('email');

        $users = $users->filter(function ($item) {
            return $item != '';
        });

        $emails = $users->toArray();

        $this->sendDeleteLaporanKejadianConfirmationMail($emails, $item);

        return redirect()->route('ba-list-laporankejadian')->with(['success'=>'Data BA Laporan Kejadian berhasil dihapus']);
    }

    public function sendDeleteLaporanKejadianConfirmationMail($emails, $item)
    {
        Mail::mailer('smtp')->to($emails)->send(new DeleteLaporanKejadian($item));
    }

    public function print_pdf($lk_id)
    {
        $item = BaLaporanKejadian::find($lk_id);

        $pdf = PDF::loadView('pages.halo-security.ba-laporan-kejadian',compact('item'));

        return $pdf->download('Data Berita Acara Laporan Kejadian.pdf');
    }

    public function exportexcelkejadian(Request $request)
    {
        return Excel::download(new HsBaLaporanKejadian(),'Halo Security Berita Acara Laporan Kejadian.xlsx');
    }

    public function render(Request $request)
    {
        // $this->permission('hs_kejadian');
        
        $data = BaLaporanKejadian::query();

        // filter by search
        // $balaporankejadian->when($request->search, function ($query) use ($request) {
        //     return $query
        //     ->where('nama_korban', 'like', '%'.$request->search.'%')
        //     ->OrWhere('nik_korban', 'like', '%'.$request->search.'%')
        //     ->OrWhere('perusahaan_korban', 'like', '%'.$request->search.'%')
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

        // filter by status terlapor
        $data->when($request->status_terlapor, function ($query) use ($request) {
            return $query->whereStatusTerlapor($request->status_terlapor);
        });

        $balaporankejadian = $data->orderBy('created_at', 'DESC')->get();

        return view('livewire.listlaporankejadian', compact('balaporankejadian'));
    }
}
