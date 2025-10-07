<?php

namespace App\Http\Livewire;

use App\HaloSecurity\BaLaporanKejadian;
use App\HaloSecurity\DokumentasiKejadian;
use App\HaloSecurity\FaktaKejadian;
use App\HaloSecurity\SaksiKejadian;
use App\Mail\HaloSecurity\AddLaporanKejadian;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Illuminate\Support\Facades\Mail;
use App\User;
use App\HaloSecurity\SecurityUserGA;

class Createbalaporankejadian extends Component
{
    public function generateID($prefix, $tableName, $columnName, $aditionalWhere = null)
    {
        $prefixLength = strlen($prefix);

        $numberBefore = DB::table($tableName)
        ->selectRaw('SUBSTR('.$columnName.', '.($prefixLength+1).') as code')
        ->orderByRaw('CAST(SUBSTR('.$columnName.', '.($prefixLength+1).') AS SIGNED) desc')
        ->whereRaw('SUBSTR('.$columnName.',1, '.($prefixLength).') = \''.$prefix.'\'');
        
        if($aditionalWhere != null) {
            $numberBefore = $numberBefore->whereRaw($aditionalWhere);
        }

        $numberBefore = $numberBefore->first();

        if($numberBefore == null) {
            return $prefix.'00001';
        }

        $currentNumber = (int)$numberBefore->code+1;
        
        switch ($currentNumber) {
            case $currentNumber < 10:
                $currentCode = $prefix.'0000'.$currentNumber;
                break;
            case $currentNumber < 100:
                $currentCode = $prefix.'000'.$currentNumber;
                break;
            case $currentNumber < 1000:
                $currentCode = $prefix.'00'.$currentNumber;
                break;
            case $currentNumber < 10000:
                $currentCode = $prefix.'0'.$currentNumber;
                break;
            default:
                $currentCode = $prefix.$currentNumber;
                break;
        }

        return $currentCode;
    }

    public function AddBaLaporanKejadian(Request $request)
    {
        $request->validate([
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
                    'fakta_kejadian' => 'required',
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

        try{
            DB::beginTransaction();

            $lk_id = $this->generateID('lk', 'ba_laporan_kejadian', 'lk_id');

            if ($request->input('jenis_kejadian') == 'lainnya') {
                $ambilKejadian = $request->input('jenis_kejadian_lainnya');
            } else {
                $ambilKejadian = $request->input('jenis_kejadian');
            }

            $createbalaporankejadian = new BaLaporanKejadian([
                'lk_id' => $lk_id,
                'danru' => $request->get('danru'),
                'jenis_kejadian' => $ambilKejadian,
                'nama_korban' => $request->get('nama_korban'),
                'nik_korban' => $request->get('nik_korban'),
                'perusahaan_korban' => $request->get('perusahaan_korban'),
                'bagian_korban' => $request->get('bagian_korban'),
                'lokasi_kejadian' => $request->get('lokasi_kejadian'),
                'yang_terjadi' => $request->get('yang_terjadi'),
                'nama_terlapor' => $request->get('nama_terlapor'),
                'umur_terlapor' => $request->get('umur_terlapor'),
                'ttl_terlapor' => $request->get('ttl_terlapor'),
                'pekerjaan_terlapor' => $request->get('pekerjaan_terlapor'),
                'alamat_terlapor' => $request->get('alamat_terlapor'),
                'kelurahan_terlapor' => $request->get('kelurahan_terlapor'),
                'kecamatan_terlapor' => $request->get('kecamatan_terlapor'),
                'provinsi_terlapor' => $request->get('provinsi_terlapor'),
                'status_terlapor' => $request->get('status_terlapor'),
                'agama_terlapor' => $request->get('agama_terlapor'),
                'kebangsaan_terlapor' => $request->get('kebangsaan_terlapor'),
                'no_ktp_terlapor' => $request->get('no_ktp_terlapor'),
                'no_simc_terlapor' => $request->get('no_simc_terlapor'),
                'no_hp_terlapor' => $request->get('no_hp_terlapor'),
                'bagaimana_terjadi' => $request->get('bagaimana_terjadi'),
                'mengapa_terjadi' => $request->get('mengapa_terjadi'),
                'uraian_kejadian' => $request->get('uraian_kejadian'),
                'tindakan_pengamanan' => $request->get('tindakan_pengamanan'),
                'hasil_daritindakan' => $request->get('hasil_daritindakan'),
                'saran' => $request->get('saran'),
            ]);
    
            $createbalaporankejadian->save();

            $usersArray = SecurityUserGA::all()->pluck('nik')->toArray();

            $users = User::whereIn('username', $usersArray)->get()
            ->pluck('email');

            $users = $users->filter(function ($item) {
                return $item != '';
            });

            $emails = $users->toArray();

            $this->sendNewLaporanKejadianConfirmationMail($emails, $createbalaporankejadian, $lk_id);

            foreach($request->fakta_kejadian as $fk){
                $fakta = new FaktaKejadian;
                $fakta->lk_id = $lk_id;
                $fakta->keterangan_fakta = $fk;
                $fakta->save();
            }

            $nama_saksi_array = $request->nama_saksi;
            $nik_saksi_array = $request->nik_saksi;
            $department_saksi_array = $request->departement_saksi;
            $keterangan_saksi_array = $request->keterangan_saksi;

            foreach($nama_saksi_array as $key => $sk) {
                $fakta2 = new SaksiKejadian;
                $fakta2->lk_id = $lk_id;
                $fakta2->nama_saksi = $nama_saksi_array[$key];
                $fakta2->nik_saksi = $nik_saksi_array[$key];
                $fakta2->departement_saksi = $department_saksi_array[$key];
                $fakta2->keterangan_saksi = $keterangan_saksi_array[$key];
                $fakta2->save();
            }

            // dd($request->dokumentasi_kejadian);

            if($request->dokumentasi_kejadian) {
                $__key = 0;
                foreach($request->dokumentasi_kejadian as $key => $attachment) {
                    $files = $attachment;
                    $keterangan_kejadian = $request->keterangan_kejadian;

                    $imageArray = [];

                    foreach($files as $file){
                        // $imageName = 'Gambar Laporan Kejadian'.uniqid().'_'.$file->getClientOriginalName();
                        // $file->move(\public_path("/master_laporan_kejadian_gambar"),$imageName);
                        // $imageArray[] = $imageName;

                        // $folderPathIntrogasi = 'master_laporan_kejadian_gambar/';
                        // $image_partIntrogasi = explode(";base64,", $file);
                        // $image_base88 = base64_decode($image_partIntrogasi[1]);
                        // $imageIntrogasi = uniqid() . '.jpg';

                        // $fileintrogasi = $folderPathIntrogasi . $imageIntrogasi;

                        // \File::put($fileintrogasi, $image_base88);
                        // $imageArray[] = $imageIntrogasi;

                        $folderPathIntrogasi = 'master_laporan_kejadian_gambar/';
                        $image_partIntrogasi = explode(";base64,", $file);
                        $image_base88 = base64_decode($image_partIntrogasi[1]);
                        $imageIntrogasi = uniqid() . '.jpg';

                        $fileintrogasi = $folderPathIntrogasi . $imageIntrogasi;

                        \File::put($fileintrogasi, $image_base88);
                        $imageArray[] = $imageIntrogasi;
                    }
                    
                    $dokumentasi = new DokumentasiKejadian;
                    $dokumentasi->lk_id = $lk_id;
                    $dokumentasi->foto_kejadian = implode(',', $imageArray);
                    $dokumentasi->keterangan_kejadian = $keterangan_kejadian[$__key];
                    $dokumentasi->save();

                    $__key++;
                }
            }
    
            DB::commit();
            return redirect()->route('ba-list-laporankejadian')->with(['success'=>'Data BA Laporan Kejadian berhasil ditambahkan']);
        }catch(\Exception $e) {
            DB::rollBack();
            return redirect()->route('ba-list-laporankejadian')->with(['error'=> $e->getMessage()]);
        }

    }

    public function sendNewLaporanKejadianConfirmationMail($emails, $createbalaporankejadian, $lk_id)
    {
        Mail::mailer('smtp')->to($emails)->send(new AddLaporanKejadian($createbalaporankejadian, $lk_id));
    }

    public function render()
    {
        // $this->permission('hs_kejadian');

        return view('livewire.createbalaporankejadian');
    }
}
