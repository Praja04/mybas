<?php

namespace App\Http\Livewire;

use App\HaloSecurity\BaSopSupir;
use Livewire\Component;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;
use App\Traits\Permission;

class Editbasopsupir extends Component
{
    public function EditBaSopSupir(Request $request,$id)
    {
        $data = $request->except('_method','_token','submit');
  
        $validator = Validator::make($request->all(), [
            'nama' => 'required',
            'ekspedisi' => 'required',
            'no_ktp' => 'required',
            'no_polisi' => 'required',
            'no_handphone' => 'required|numeric',
            'no_kartu' => 'required',
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
            'alamat.required' => 'Data alamat wajib di isi',
            'shift.required' => 'Data shift wajib di isi',
            'nama_pembuat.required' => 'Data nama pembuat wajib di isi',
            'jabatan_pembuat.required' => 'Data jabatan pembuat wajib di isi',
            'no_handphone.numeric' => 'Data nomor handphone hanya diperbolehkan angka saja',
        ]);
  
        if ($validator->fails()) {
           return redirect()->Back()->withInput()->withErrors($validator);
        }

        $basopsupir = BaSopSupir::find($id);
  
        if($basopsupir->update($data)){
  
            return redirect()->route('ba-sop-list-supir')->with(['success'=>'Data BA S.O.P Supir berhasil diubah']);
        }else{
            return redirect()->route('edit-ba-sop-supir')->with(['success'=>'Data BA S.O.P Supir gagal diubah']);
        }
  
        return Back()->withInput();
    }

    public function render(BaSopSupir $basopsupir, $id)
    {
        Permission::has('hs_edit_sop_supir');
        
        $basopsupir = BaSopSupir::find($id);

        return view('livewire.editbasopsupir', ['basopsupir' => $basopsupir]);
    }
}
