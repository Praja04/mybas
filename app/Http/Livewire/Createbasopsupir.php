<?php

namespace App\Http\Livewire;

use App\HaloSecurity\BaSopSupir;
use App\HaloSecurity\SecurityUserGA;
use App\Mail\HaloSecurity\AddBaSopSupir;
use App\User;
use Livewire\Component;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class Createbasopsupir extends Component
{
    // Data yang di simpan
    public $nama;
    public $ekspedisi;
    public $no_ktp;
    public $no_polisi;
    public $no_handphone;
    public $no_kartu;
    public $jenis_kartu;
    public $harga_kartu;
    public $alamat;
    public $shift;
    public $nama_pembuat;
    public $jabatan_pembuat;

    // proses validasi data untuk menambah data
    public function updated($fields)
    {
        $this->validateOnly($fields,[
            'nama' => 'required',
            'ekspedisi' => 'required',
            'no_ktp' => 'required',
            'no_polisi' => 'required',
            'no_handphone' => 'required',
            'no_kartu' => 'required',
            'jenis_kartu' => 'required',
            'harga_kartu' => 'required',
            'alamat' => 'required',
            'shift' => 'required|in:1,2,3',
            'nama_pembuat' => 'required',
            'jabatan_pembuat' => 'required',
        ],[
            'nama.required' => 'Data nama wajib di isi',
            'ekspedisi.required' => 'Data ekspedisi wajib di isi',
            'no_ktp.required' => 'Data nomor ktp wajib di isi',
            'no_polisi.required' => 'Data nomor polisi wajib di isi',
            'no_handphone.required' => 'Data nomor handphone wajib di isi',
            'no_kartu.required' => 'Data nomor kartu wajib di isi',
            'jenis_kartu.required' => 'Data Jenis kartu wajib di isi',
            'harga_kartu.required' => 'Data Harga kartu wajib di isi',
            'alamat.required' => 'Data alamat wajib di isi',
            'shift.required' => 'Data shift wajib di isi',
            'nama_pembuat.required' => 'Data nama pembuat wajib di isi',
            'jabatan_pembuat.required' => 'Data jabatan pembuat wajib di isi',
        ]);
    }

    public function AddBaSopSupir()
    {
        // proses validasi data untuk menambah data
        $this->validate([
            'nama' => 'required',
            'ekspedisi' => 'required',
            'no_ktp' => 'required',
            'no_polisi' => 'required',
            'no_handphone' => 'required|numeric',
            'no_kartu' => 'required',
            'jenis_kartu' => 'required',
            'harga_kartu' => 'required',
            'alamat' => 'required',
            'shift' => 'required|in:1,2,3',
            'nama_pembuat' => 'required',
            'jabatan_pembuat' => 'required',
        ],[
            'nama.required' => 'Data nama wajib di isi',
            'ekspedisi.required' => 'Data ekspedisi wajib di isi',
            'no_ktp.required' => 'Data nomor ktp wajib di isi',
            'no_polisi.required' => 'Data nomor polisi wajib di isi',
            'no_handphone.required' => 'Data nomor handphone wajib di isi',
            'no_kartu.required' => 'Data nomor kartu wajib di isi',
            'jenis_kartu.required' => 'Data Jenis kartu wajib di isi',
            'harga_kartu.required' => 'Data Harga kartu wajib di isi',
            'alamat.required' => 'Data alamat wajib di isi',
            'shift.required' => 'Data shift wajib di isi',
            'nama_pembuat.required' => 'Data nama pembuat wajib di isi',
            'jabatan_pembuat.required' => 'Data jabatan pembuat wajib di isi',
            'no_handphone.numeric' => 'Data nomor handphone hanya diperbolehkan angka saja',
        ]);

        $createbasopsupir = new BaSopSupir();
        $createbasopsupir->nama = $this->nama;
        $createbasopsupir->ekspedisi = $this->ekspedisi;
        $createbasopsupir->no_ktp = $this->no_ktp;
        $createbasopsupir->no_polisi = $this->no_polisi;
        $createbasopsupir->no_handphone = $this->no_handphone;
        $createbasopsupir->no_kartu = $this->no_kartu;
        $createbasopsupir->jenis_kartu = $this->jenis_kartu;
        $createbasopsupir->harga_kartu = $this->harga_kartu;
        $createbasopsupir->alamat = $this->alamat;
        $createbasopsupir->shift = $this->shift;
        $createbasopsupir->nama_pembuat = $this->nama_pembuat;
        $createbasopsupir->jabatan_pembuat = $this->jabatan_pembuat;

        $createbasopsupir->save();

        $usersArray = SecurityUserGA::all()->pluck('nik')->toArray();

        $users = User::whereIn('username', $usersArray)->get()
        ->pluck('email');

        $users = $users->filter(function ($item) {
            return $item != '';
        });

        $emails = $users->toArray();

        $this->sendNewSupirConfirmationMail($emails, $createbasopsupir);

        return redirect()->route('ba-sop-list-supir')->with(['success'=>'Data BA S.O.P Supir berhasil ditambahkan']);
    }

    public function sendNewSupirConfirmationMail($emails, $createbasopsupir)
    {
        Mail::mailer('smtp')->to($emails)->send(new AddBaSopSupir($createbasopsupir));
    }

    public function render()
    {
        // $this->permission('hs_sop');
        
        return view('livewire.createbasopsupir');
    }
}
