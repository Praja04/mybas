<?php

namespace App\Http\Livewire;

use App\Exports\HaloSecurity\HsBaSopSupir;
use App\HaloSecurity\BaSopSupir;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Http\Request;
use PDF;
use App\HaloSecurity\SecurityUserGA;
use App\Mail\HaloSecurity\DeleteBaSopSupir;
use Illuminate\Support\Facades\Mail;
use Excel;
use App\User;

class Listbasopsupir extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    protected $listeners = ['deleteConfirmed' => 'deleteSupir'];

    public $supirIdBeingRemoved = null;

    public function confirmSupirRemoval($supirId)
    {
        $this->supirIdBeingRemoved = $supirId;

        $this->dispatchBrowserEvent('show-delete-confirmation');
    }

    public function deleteSupir()
    {
        $supir = BaSopSupir::findOrFail($this->supirIdBeingRemoved);

        $supir->delete();

        $usersArray = SecurityUserGA::all()->pluck('nik')->toArray();

        $users = User::whereIn('username', $usersArray)->get()
        ->pluck('email');

        $users = $users->filter(function ($item) {
            return $item != '';
        });

        $emails = $users->toArray();

        $this->sendDeleteSupirConfirmationMail($emails, $supir);

        $this->dispatchBrowserEvent('deleted', ['message' => 'Data BA S.O.P Supir berhasil dihapus']);

        return redirect()->route('ba-sop-list-supir');
    }

    public function print_pdf($id)
    {
        $item = BaSopSupir::find($id);

        $pdf = PDF::loadView('pages.halo-security.ba-sop-supir', compact('item'));

        return $pdf->download('Data Berita Acara S.O.P Supir.pdf');
    }

    public function exportexcelsupir(Request $request)
    {
        return Excel::download(new HsBaSopSupir(),'Halo Security Berita Acara S.O.P Supir.xlsx');
    }

    public function sendDeleteSupirConfirmationMail($emails, $supir)
    {
        Mail::mailer('smtp')->to($emails)->send(new DeleteBaSopSupir($supir));
    }

    public function render(Request $request)
    {
        // $this->permission('hs_sop');
        
        $data = BaSopSupir::query();

        // filter by search
        // $basopsupir->when($request->search, function ($query) use ($request) {
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
            return $query->where('created_at', '>=', $request->created_at);
        });

        // filter by shift
        $data->when($request->shift, function ($query) use ($request) {
            return $query->whereShift($request->shift);
        });

        $basopsupir = $data->orderBy('created_at', 'DESC')->get();

        return view('livewire.listbasopsupir', compact('basopsupir'));
    }
}
