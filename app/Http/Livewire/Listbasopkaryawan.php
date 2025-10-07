<?php

namespace App\Http\Livewire;

use App\Exports\HaloSecurity\HsBaSopKaryawan;
use App\HaloSecurity\BaSopKaryawan;
use App\Mail\HaloSecurity\DeleteBaSopKaryawan;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Http\Request;
use PDF;
use App\HaloSecurity\SecurityUserGA;
use Illuminate\Support\Facades\Mail;
use App\User;
use Excel;

class Listbasopkaryawan extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    protected $listeners = ['deleteConfirmed' => 'deleteKaryawan'];

    public $karyawanIdBeingRemoved = null;

    public function confirmKaryawanRemoval($karyawanId)
    {
        $this->karyawanIdBeingRemoved = $karyawanId;

        $this->dispatchBrowserEvent('show-delete-confirmation');
    }

    public function deleteKaryawan()
    {
        $karyawan = BaSopKaryawan::findOrFail($this->karyawanIdBeingRemoved);

        $karyawan->delete();

        $usersArray = SecurityUserGA::all()->pluck('nik')->toArray();

        $users = User::whereIn('username', $usersArray)->get()
        ->pluck('email');

        $users = $users->filter(function ($item) {
            return $item != '';
        });

        $emails = $users->toArray();

        $this->sendDeleteKaryawanConfirmationMail($emails, $karyawan);

        $this->dispatchBrowserEvent('deleted', ['message' => 'Data BA S.O.P Karyawan berhasil dihapus']);

        return redirect()->route('ba-sop-list-karyawan');
    }

    public function print_pdf($id)
    {
        $item = BaSopKaryawan::find($id);

        $pdf = PDF::loadView('pages.halo-security.ba-sop-karyawan',compact('item'));

        return $pdf->download('Data Berita Acara S.O.P Karyawan.pdf');
    }

    public function exportexcelkaryawan(Request $request)
    {
        return Excel::download(new HsBaSopKaryawan(),'Halo Security Berita Acara S.O.P Karyawan.xlsx');
    }

    public function sendDeleteKaryawanConfirmationMail($emails, $karyawan)
    {
        Mail::mailer('smtp')->to($emails)->send(new DeleteBaSopKaryawan($karyawan));
    }

    public function render(Request $request)
    {
        // $this->permission('hs_sop');
        
        $data = BaSopKaryawan::query();

        // filter by search
        // $basopkaryawan->when($request->search, function ($query) use ($request) {
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
            return $query->where('created_at', '>=', $request->created_at);
        });

        // filter by jenis kelamin
        $data->when($request->jenis_kelamin, function ($query) use ($request) {
            return $query->whereJenis_kelamin($request->jenis_kelamin);
        });

        // filter by shift
        $data->when($request->shift, function ($query) use ($request) {
            return $query->whereShift($request->shift);
        });

        $basopkaryawan = $data->orderBy('created_at', 'DESC')->get();

        return view('livewire.listbasopkaryawan', compact('basopkaryawan'));
    }

}
