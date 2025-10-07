<?php

namespace App\Http\Livewire;

use App\HaloSecurity\BaSopKaryawan;
use Livewire\Component;
use App\HaloSecurity\SecurityUserGA;
use App\Mail\HaloSecurity\AddBaSopKaryawan;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\User;

class Createbasopkaryawan extends Component
{
    // Data yang di simpan
    public $nama;
    public $nik;
    public $jabatan;
    public $jenis_kelamin;
    public $shift;
    public $nama_pembuat;
    public $jabatan_pembuat;
    public $nama_area;
    public $barang;

    // proses validasi data untuk menambah data
    public function updated($fields)
    {
        $this->validateOnly($fields,[
            'nama' => 'required',
            'nik' => 'required',
            'jabatan' => 'required',
            'jenis_kelamin' => 'required|in:laki-laki,perempuan',
            'shift' => 'required|in:1,2,3',
            'nama_pembuat' => 'required',
            'jabatan_pembuat' => 'required',
            'nama_area' => 'required',
            'barang' => 'required',
        ],[
            'nama.required' => 'Data nama wajib di isi',
            'nik.required' => 'Data nik wajib di isi',
            'jabatan.required' => 'Data jabatan wajib di isi',
            'jenis_kelamin.required' => 'Data jenis kelamin wajib di isi',
            'shift.required' => 'Data shift wajib di isi',
            'nama_pembuat.required' => 'Data nama pembuat wajib di isi',
            'jabatan_pembuat.required' => 'Data jabatan pembuat wajib di isi',
            'nama_area.required' => 'Data nama area wajib di isi',
            'barang.required' => 'Data barang wajib di isi',
        ]);
    }

    public function AddBaSopKaryawan()
    {
        // proses validasi data untuk menambah data
        $this->validate([
            'nama' => 'required',
            'nik' => 'required',
            'jabatan' => 'required',
            'jenis_kelamin' => 'required|in:laki-laki,perempuan',
            'shift' => 'required|in:1,2,3',
            'nama_pembuat' => 'required',
            'jabatan_pembuat' => 'required',
            'nama_area' => 'required',
            'barang' => 'required',
        ],[
            'nama.required' => 'Data nama wajib di isi',
            'nik.required' => 'Data nik wajib di isi',
            'jabatan.required' => 'Data jabatan wajib di isi',
            'jenis_kelamin.required' => 'Data jenis kelamin wajib di isi',
            'shift.required' => 'Data shift wajib di isi',
            'nama_pembuat.required' => 'Data nama pembuat wajib di isi',
            'jabatan_pembuat.required' => 'Data jabatan pembuat wajib di isi',
            'nama_area.required' => 'Data nama area wajib di isi',
            'barang.required' => 'Data barang wajib di isi',
        ]);

        $createbasopkaryawan = new BaSopKaryawan();
        $createbasopkaryawan->nama = $this->nama;
        $createbasopkaryawan->nik = $this->nik;
        $createbasopkaryawan->jabatan = $this->jabatan;
        $createbasopkaryawan->jenis_kelamin = $this->jenis_kelamin;
        $createbasopkaryawan->shift = $this->shift;
        $createbasopkaryawan->nama_pembuat = $this->nama_pembuat;
        $createbasopkaryawan->jabatan_pembuat = $this->jabatan_pembuat;
        $createbasopkaryawan->nama_area = $this->nama_area;
        $createbasopkaryawan->barang = $this->barang;

        $createbasopkaryawan->save();

        $usersArray = SecurityUserGA::all()->pluck('nik')->toArray();

        $users = User::whereIn('username', $usersArray)->get()
        ->pluck('email');

        $users = $users->filter(function ($item) {
            return $item != '';
        });

        $emails = $users->toArray();

        $this->sendNewKaryawanConfirmationMail($emails, $createbasopkaryawan);

        return redirect()->route('ba-sop-list-karyawan')->with(['success'=>'Data BA S.O.P Karyawan berhasil ditambahkan']);
    }

    public function sendNewKaryawanConfirmationMail($emails, $createbasopkaryawan)
    {
        Mail::mailer('smtp')->to($emails)->send(new AddBaSopKaryawan($createbasopkaryawan));
    }

    public function render()
    {
        // $this->permission('hs_sop');
        
        return view('livewire.createbasopkaryawan');
    }
}
