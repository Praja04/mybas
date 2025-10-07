<?php

namespace App\Http\Livewire;

use App\HaloSecurity\BaLaporanKejadian;
use App\HaloSecurity\DokumentasiKejadian;
use App\HaloSecurity\FaktaKejadian;
use App\HaloSecurity\SaksiKejadian;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use App\Traits\Permission;
use App\Http\Controllers\Controller;

class Editbalaporankejadian extends Component
{
    public function EditBaLaporanKejadian(Request $request,$lk_id)
    {
        $data = $request->except(
            '_method','_token','submit', 'dokumentasi_id',
            'foto_kejadian', 'keterangan_kejadian', 'nama_saksi', 'nik_saksi', 'departement_saksi',
            'keterangan_saksi', 'keterangan_fakta','fakta_kejadian', 'dokumentasi_kejadian'
        );
  
        $validator = Validator::make($request->all(), [
            'danru' => 'required',
            'jenis_kejadian' => 'required',
            'nama_korban' => 'required',
            'nik_korban' => 'required',
            'perusahaan_korban' => 'required',
            'bagian_korban' => 'required',
            'lokasi_kejadian' => 'required',
            'yang_terjadi' => 'required',
            'nama_terlapor' => 'required',
            'umur_terlapor' => 'required',
            'ttl_terlapor' => 'required',
            'pekerjaan_terlapor' => 'required',
            'alamat_terlapor' => 'required',
            'kelurahan_terlapor' => 'required',
            'kecamatan_terlapor' => 'required',
            'provinsi_terlapor' => 'required',
            'status_terlapor' => 'required',
            'agama_terlapor' => 'required',
            'kebangsaan_terlapor' => 'required',
            'no_ktp_terlapor' => 'required',
            'no_simc_terlapor' => 'required',
            'no_hp_terlapor' => 'required',
            'bagaimana_terjadi' => 'required',
            'mengapa_terjadi' => 'required',
            'uraian_kejadian' => 'required',
            'tindakan_pengamanan' => 'required',
            'hasil_daritindakan' => 'required',
            'saran' => 'required',
        ],[
            'danru.required' => 'Data danru wajib di isi',
            'jenis_kejadian.required' => 'Data jenis kejadian wajib di isi',
            'nama_korban.required' => 'Data nama korban wajib di isi',
            'nik_korban.required' => 'Data nik korban wajib di isi',
            'perusahaan_korban.required' => 'Data perusahaan korban wajib di isi',
            'bagian_korban.required' => 'Data bagian korban wajib di isi',
            'lokasi_kejadian.required' => 'Data lokasi kejadian wajib di isi',
            'yang_terjadi.required' => 'Data yang terjadi wajib di isi',
            'nama_terlapor.required' => 'Data nama terlapor wajib di isi',
            'umur_terlapor.required' => 'Data umur terlapor wajib di isi',
            'ttl_terlapor.required' => 'Data tempat tanggal lahir terlapor wajib di isi',
            'pekerjaan_terlapor.required' => 'Data pekerjaan terlapor wajib di isi',
            'alamat_terlapor.required' => 'Data alamat terlapor wajib di isi',
            'kelurahan_terlapor.required' => 'Data kelurahan terlapor wajib di isi',
            'kecamatan_terlapor.required' => 'Data kecamatan terlapor wajib di isi',
            'provinsi_terlapor.required' => 'Data provinsi terlapor wajib di isi',
            'status_terlapor.required' => 'Data status terlapor wajib di isi',
            'agama_terlapor.required' => 'Data agama terlapor wajib di isi',
            'kebangsaan_terlapor.required' => 'Data kebangsaan terlapor wajib di isi',
            'no_ktp_terlapor.required' => 'Data nomor ktp terlapor wajib di isi',
            'no_simc_terlapor.required' => 'Data nomor sim c terlapor wajib di isi',
            'no_hp_terlapor.required' => 'Data nomor handphone terlapor wajib di isi',
            'bagaimana_terjadi.required' => 'Data bagaimana terjadi wajib di isi',
            'mengapa_terjadi.required' => 'Data mengapa terjadi wajib di isi',
            'uraian_kejadian.required' => 'Data uraian kejadian wajib di isi',
            'tindakan_pengamanan.required' => 'Data tindakan pengamanan wajib di isi',
            'hasil_daritindakan.required' => 'Data hasil dari tindakan wajib di isi',
            'saran.required' => 'Data saran wajib di isi',
        ]);
  
        if ($validator->fails()) {
           return redirect()->Back()->withInput()->withErrors($validator);
        }

        $balaporankejadian = BaLaporanKejadian::find($lk_id);

        $fakta_array = $request->fakta_kejadian;
        $fakta_array3 = $request->dokumentasi_kejadian;
  
        if($balaporankejadian->update($data)){

            try {
                DB::beginTransaction();

                $fakta_kejadian_db = FaktaKejadian::where('lk_id', $lk_id)->orderByRaw('created_at', 'desc')->get();
                // $dokumentasi_kejadian_db = DokumentasiKejadian::where('lk_id', $lk_id)->orderByRaw('created_at', 'desc')->get();
                
                // Ubah Data
                // Fakta Kejadian
                foreach($fakta_kejadian_db as $fk) {
                    if(isset($fakta_array[$fk->id])) {
                        // Update
                        $fk->keterangan_fakta = $fakta_array[$fk->id];
                        $fk->save();
                        
                        unset($fakta_array[$fk->id]);
                    }else{
                        // Delete
                        $fk->delete();
                    }
                }
                
                $nama_saksi_array = $request->nama_saksi;
                $nik_saksi_array = $request->nik_saksi;
                $department_saksi_array = $request->departement_saksi;
                $keterangan_saksi_array = $request->keterangan_saksi;
                
                $saksi_kejadian_db = SaksiKejadian::where('lk_id', $lk_id)->orderByRaw('created_at', 'desc')->get();
                // dd($request->all());
                
                // Saksi Kejadian
                foreach($saksi_kejadian_db as $sk) {
                    if(isset($nama_saksi_array[$sk->id])) {
                        // Update
                        $sk->nama_saksi = $nama_saksi_array[$sk->id];
                        $sk->nik_saksi = $nik_saksi_array[$sk->id];
                        $sk->departement_saksi = $department_saksi_array[$sk->id];
                        $sk->keterangan_saksi = $keterangan_saksi_array[$sk->id];
                        $sk->save();


                        unset($nama_saksi_array[$sk->id]);
                        unset($nik_saksi_array[$sk->id]);
                        unset($department_saksi_array[$sk->id]);
                        unset($keterangan_saksi_array[$sk->id]);
                    }else{
                        // Delete
                        $sk->delete();
                    }
                }

                // Menambah data fakta kejadian
                foreach($fakta_array as $fk) {
                    $fakta = new FaktaKejadian;
                    $fakta->lk_id = $lk_id;
                    $fakta->keterangan_fakta = $fk;
                    $fakta->save();
                }

                // Menambah data saksi kejadian
                foreach($nama_saksi_array as $key => $sk) {
                    $fakta2 = new SaksiKejadian;
                    $fakta2->lk_id = $lk_id;
                    $fakta2->nama_saksi = $nama_saksi_array[$key];
                    $fakta2->nik_saksi = $nik_saksi_array[$key];
                    $fakta2->departement_saksi = $department_saksi_array[$key];
                    $fakta2->keterangan_saksi = $keterangan_saksi_array[$key];
                    $fakta2->save();
                }

                // dd($request->all());

                // dd($request->dokumentasi_id);    

                $dokumentasi_id = $request->dokumentasi_id;

                // Menambah data dokumentasi kejadian
                $dokumentasi_kejadian = $request->dokumentasi_kejadian;
                // dd($dokumentasi_kejadian);
                if($dokumentasi_kejadian) {
                    foreach($dokumentasi_kejadian as $key => $dokumentasi) {
                        $files = $dokumentasi;

                        // dd($files);
                        
                        if($files[0] ==  null) {
                            continue;
                        }

                        $imageArray = [];

                        // dd($files);

                        foreach($files as $file){
                            $imageName = 'Gambar Laporan Kejadian'.uniqid().'_'.$file->getClientOriginalName();
                            $file->move(\public_path("/master_laporan_kejadian_gambar"),$imageName);

                            $imageArray[] = $imageName;
                        }
                        
                        $dokumentasi = DokumentasiKejadian::find($key);
                        // $dokumentasi->lk_id = $lk_id;
                        if($dokumentasi != null) {
                            $dokumentasi->foto_kejadian = implode(',', $imageArray);
                            $dokumentasi->keterangan_kejadian = $request->keterangan_kejadian[$key];
                            $dokumentasi->save();
                        }else{
                            $dokumentasi = new DokumentasiKejadian;
                            $dokumentasi->lk_id = $lk_id;
                            $dokumentasi->foto_kejadian = implode(',', $imageArray);
                            $dokumentasi->keterangan_kejadian = $request->keterangan_kejadian[$key];
                            $dokumentasi->save();
                            
                            $dokumentasi_id[] = $dokumentasi->id;
                        }
                    }
                }
                
                $keterangan_kejadian = $request->keterangan_kejadian;
                if($keterangan_kejadian) {
                    foreach($keterangan_kejadian as $key => $dokumentasi) {
                        $dokumentasi = DokumentasiKejadian::find($key);
                        // $dokumentasi->lk_id = $lk_id;
                        if($dokumentasi != null) {
                            // $dokumentasi->foto_kejadian = implode(',', $imageArray);
                            $dokumentasi->keterangan_kejadian = $request->keterangan_kejadian[$key];
                            $dokumentasi->save();
                        }
                        // dd($dokumentasi);
                    }
                }

                // Get semua dokumentasi kejadian
                $dokumentasi_kejadian = DokumentasiKejadian::where('lk_id', $lk_id)->get();
                foreach($dokumentasi_kejadian as $dokumentasi) {
                    // Jika tidak ada maka hapus
                    if(!in_array($dokumentasi->id, $dokumentasi_id)) {
                        $dokumentasi->delete();
                    }
                }

                DB::commit();
                return redirect()->route('ba-list-laporankejadian')->with(['success'=>'Data BA Laporan Kejadian berhasil diubah']);
            }catch(\Exception $e) {
                DB::rollBack();
                return redirect()->route('ba-list-laporankejadian')->with(['error' => $e->getMessage()]);
            }
        }else{
            return redirect()->route('ba-list-laporankejadian')->with(['error'=>'Data BA Laporan Kejadian gagal diubah']);
        }
  
        return Back()->withInput();
    }

    public function render(BaLaporanKejadian $balaporankejadian, $lk_id)
    {
        Permission::has('hs_edit_lk');

        $balaporankejadian = BaLaporanKejadian::with('dokumentasis')->find($lk_id);

        return view('livewire.editbalaporankejadian', ['balaporankejadian' => $balaporankejadian]);
    }
}
