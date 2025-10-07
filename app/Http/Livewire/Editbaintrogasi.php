<?php

namespace App\Http\Livewire;

use App\HaloSecurity\BaiItems;
use App\HaloSecurity\BaIntrogasi;
use App\HaloSecurity\DokumentasiIntrogasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\File;
use App\HaloSecurity\TemplateBaiItems;
use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Storage;
use App\Http\Controllers\Controller;
use App\Traits\Permission;

class Editbaintrogasi extends Component
{
    public function EditBaIntrogasi(Request $request,$bai_id)
    {
        $data = $request->except(
            '_method','_token','submit', 'dokumentasi_id', 'bai_oneimage', 'pertanyaan_introgasi', 'jawaban_introgasi', 'foto_introgasi', 'keterangan_introgasi', 'item_id'
        );
  
        $validator = Validator::make($request->all(), [
            'jenis_kejadian' => 'required',
            'nama_introgasi' => 'required',
            'umur_introgasi' => 'required',
            'pekerjaan_introgasi' => 'required',
            'bagian_introgasi' => 'required',
            'nama_pelapor' => 'required',
            'detail_barang_kejadian' => 'required',
            'tempat_kejadian' => 'required',
            'nama_korban' => 'required',
            'nik_korban' => 'required',
            'bagian_korban' => 'required',
            'nama_pelaku' => 'required',
            'umur_pelaku' => 'required',
            'ttl_pelaku' => 'required',
            'pekerjaan_pelaku' => 'required',
            'nik_pelaku' => 'required',
            'bagian_pelaku' => 'required',
            'alamat_pelaku' => 'required',
            'agama_pelaku' => 'required',
            'suku_pelaku' => 'required',
            'status_pelaku' => 'required',
            'pendidikan_pelaku' => 'required',
            'nik_ktp_pelaku' => 'required',
            'no_hp_pelaku' => 'required',
            'tempat_introgasi' => 'required',
            'keterangan_kejadian' => 'required',
            'shift' => 'required',
            // 'bai_oneimage' => 'required',
            // 'image' => 'required',
        ],[
            'jenis_kejadian.required' => 'Data jenis kejadian wajib di isi',
            'nama_introgasi.required' => 'Data nama introgasi wajib di isi',
            'umur_introgasi.required' => 'Data umur introgasi wajib di isi',
            'pekerjaan_introgasi.required' => 'Data pekerjaan introgasi wajib di isi',
            'bagian_introgasi.required' => 'Data bagian introgasi wajib di isi',
            'nama_pelapor.required' => 'Data nama pelapor wajib di isi',
            'detail_barang_kejadian.required' => 'Data detail barang kejadian / motif kejadian wajib di isi',
            'tempat_kejadian.required' => 'Data tempat kejadian wajib di isi',
            'nama_korban.required' => 'Data nama korban wajib di isi',
            'nik_korban.required' => 'Data nik korban wajib di isi',
            'bagian_korban.required' => 'Data bagian korban wajib di isi',
            'nama_pelaku.required' => 'Data nama pelaku wajib di isi',
            'umur_pelaku.required' => 'Data umur pelaku wajib di isi',
            'ttl_pelaku.required' => 'Data tempat tanggal lahir pelaku wajib di isi',
            'pekerjaan_pelaku.required' => 'Data pekerjaan pelaku wajib di isi',
            'nik_pelaku.required' => 'Data nik (nomor induk karyawan) pelaku wajib di isi',
            'bagian_pelaku.required' => 'Data bagian pelaku wajib di isi',
            'alamat_pelaku.required' => 'Data alamat pelaku wajib di isi',
            'agama_pelaku.required' => 'Data agama pelaku wajib di isi',
            'suku_pelaku.required' => 'Data suku pelaku wajib di isi',
            'status_pelaku.required' => 'Data status pelaku wajib di isi',
            'pendidikan_pelaku.required' => 'Data pendidikan pelaku wajib di isi',
            'nik_ktp_pelaku.required' => 'Data nik ktp pelaku wajib di isi',
            'no_hp_pelaku.required' => 'Data nomor hp pelaku wajib di isi',
            'tempat_introgasi.required' => 'Data tempat introgasi wajib di isi',
            'keterangan_kejadian.required' => 'Data keterangan kejadian wajib di isi',
            'shift.required' => 'Data shift wajib di isi',
            // 'bai_oneimage.required' => 'Data Foto Dokumentasi Kejadian wajib di isi',
            // 'image.required' => 'Data Foto Dokumentasi Proses BAI wajib di isi'
        ]);
  
        if ($validator->fails()) {
            return redirect()->Back()->withInput()->withErrors($validator);
        }
 
        $baintrogasi = BaIntrogasi::find($bai_id);

        if($request->hasfile('bai_oneimage'))
        {
            $destination = 'baioneimage-halosecurity'.$baintrogasi->bai_oneimage;
            if(File::exists($destination))
            {
                File::delete($destination);
            }
            $file = $request->file('bai_oneimage');
            $extention = $file->getClientOriginalExtension();
            $filename = time().'.'.$extention;
            $file->move('baioneimage-halosecurity', $filename);
            $baintrogasi->bai_oneimage = $filename;
        }
   
         if($baintrogasi->update($data)){
 
            try {
                DB::beginTransaction();

            $pertanyaan_introgasi_array = $request->pertanyaan_introgasi;
            $jawaban_introgasi_array = $request->jawaban_introgasi;

            $pertanyaan_db = BaiItems::where('bai_id', $bai_id)->orderByRaw('created_at', 'desc')->get();

            // Ubah Data BAI Items
            foreach($pertanyaan_db as $pi) {
                if(isset($pertanyaan_introgasi_array[$pi->id])) {
                    // Update
                    $pi->pertanyaan_introgasi = $pertanyaan_introgasi_array[$pi->id];
                    $pi->jawaban_introgasi = $jawaban_introgasi_array[$pi->id];
                    $pi->save();

                    unset($pertanyaan_introgasi_array[$pi->id]);
                    unset($jawaban_introgasi_array[$pi->id]);
                }else{
                    // Delete
                    $pi->delete();
                }
            }
            
            // Tambah Data Baru BAI Items
            foreach($pertanyaan_introgasi_array as $key => $pi){
                $introgasi = new BaiItems;
                $introgasi->bai_id = $bai_id;
                $introgasi->pertanyaan_introgasi = $pertanyaan_introgasi_array[$key];
                $introgasi->jawaban_introgasi = $jawaban_introgasi_array[$key];
                $introgasi->save();
            }

            // Proses tambah data dan ubah data dokumentasi introgasi
 
            $dokumentasi_id = $request->dokumentasi_id;

                // Menambah data dokumentasi kejadian
                $foto_introgasi = $request->foto_introgasi;
                
                if($foto_introgasi) {
                    foreach($foto_introgasi as $key => $dokumentasi) {
                        $files = $dokumentasi;

                        // dd($files);
                        
                        if($files[0] ==  null) {
                            continue;
                        }

                        $imageArray = [];

                        // dd($files);

                        foreach($files as $file){
                            $imageName = 'Gambar Introgasi'.uniqid().'_'.$file->getClientOriginalName();
                            $file->move("master_introgasi_gambar",$imageName);

                            $imageArray[] = $imageName;
                        }
                        
                        $dokumentasi = DokumentasiIntrogasi::find($key);
                        // $dokumentasi->bai_id = $bai_id;
                        if($dokumentasi != null) {
                            $dokumentasi->foto_introgasi = implode(',', $imageArray);
                            $dokumentasi->keterangan_introgasi = $request->keterangan_introgasi[$key];
                            $dokumentasi->save();
                        }else{
                            $dokumentasi = new DokumentasiIntrogasi;
                            $dokumentasi->bai_id = $bai_id;
                            $dokumentasi->foto_introgasi = implode(',', $imageArray);
                            $dokumentasi->keterangan_introgasi = $request->keterangan_introgasi[$key];
                            $dokumentasi->save();
                            
                            $dokumentasi_id[] = $dokumentasi->id;
                        }
                    }
                }
                $keterangan_introgasi = $request->keterangan_introgasi;
                if($keterangan_introgasi) {
                    foreach($keterangan_introgasi as $key => $dokumentasi) {
                        $dokumentasi = DokumentasiIntrogasi::find($key);
                        // $dokumentasi->bai_id = $bai_id;
                        if($dokumentasi != null) {
                            // $dokumentasi->foto_introgasi = implode(',', $imageArray);
                            $dokumentasi->keterangan_introgasi = $request->keterangan_introgasi[$key];
                            $dokumentasi->save();
                        }
                        // dd($dokumentasi);
                    }
                }

                // Get semua dokumentasi kejadian
                $foto_introgasi = DokumentasiIntrogasi::where('bai_id', $bai_id)->get();
                foreach($foto_introgasi as $dokumentasi) {
                    // Jika tidak ada maka hapus
                    if(!in_array($dokumentasi->id, $dokumentasi_id)) {
                        $dokumentasi->delete();
                    }
                }

                DB::commit();
                return redirect()->route('ba-list-introgasi')->with(['success'=>'Data BA Introgasi berhasil diubah']);
            }catch(\Exception $e) {
                DB::rollBack();
                return redirect()->route('ba-list-introgasi')->with(['error' => $e->getMessage()]);
            }
         }else{
             return redirect()->route('ba-list-introgasi')->with(['error'=>'Data BA Introgasi gagal diubah']);
         }
   
         return Back()->withInput();
    }

    public function render(BaIntrogasi $baintrogasi, $bai_id)
    {
        Permission::has('hs_edit_bai');
        
        $baintrogasi = BaIntrogasi::with('dokumentasibais')->find($bai_id);
        $templates = TemplateBaiItems::paginate(10);

        return view('livewire.editbaintrogasi', ['baintrogasi' => $baintrogasi, 'templates' => $templates]);
    }
}
