<?php

namespace App\Http\Livewire;

use App\HaloSecurity\BaSopKaryawan;
use Livewire\Component;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;
use App\Traits\Permission;

class Editbasopkaryawan extends Component
{
    public function EditBaSopKaryawan(Request $request,$id)
    {
        $data = $request->except('_method','_token','submit');
  
        $validator = Validator::make($request->all(), [
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
  
        if ($validator->fails()) {
           return redirect()->Back()->withInput()->withErrors($validator);
        }

        $basopkaryawan = BaSopKaryawan::find($id);
  
        if($basopkaryawan->update($data)){
  
            return redirect()->route('ba-sop-list-karyawan')->with(['success'=>'Data BA S.O.P Karyawan berhasil diubah']);
        }else{
            return redirect()->route('edit-ba-sop-karyawan')->with(['success'=>'Data BA S.O.P Karyawan gagal diubah']);
        }
  
        return Back()->withInput();
    }

    public function render(BaSopKaryawan $basopkaryawan, $id)
    {
        Permission::has('hs_edit_sop_karyawan');
        
        $basopkaryawan = BaSopKaryawan::find($id);

        return view('livewire.editbasopkaryawan', ['basopkaryawan' => $basopkaryawan]);
    }
}
