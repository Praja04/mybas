<?php

namespace App\Http\Livewire;

use App\HaloSecurity\BaiItems;
use App\HaloSecurity\BaIntrogasi;
use App\HaloSecurity\BaLaporanKejadian;
use App\HaloSecurity\DokumentasiIntrogasi;
use App\HaloSecurity\SaksiKejadian;
use App\HaloSecurity\SecurityUserGA;
use App\HaloSecurity\TemplateBaiItems;
use App\Mail\HaloSecurity\AddBaIntrogasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Livewire\WithPagination;
use Yajra\DataTables\DataTables;
use Carbon\Carbon;
use Response;
use Livewire\Component;
use Illuminate\Support\Facades\Mail;
use App\User;
use App\Traits\Permission;
use PDF;
use Storage;

class Createbaintrogasi extends Component
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

    public function AddBaIntrogasi(Request $request)
    {
        $request->validate([
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
                    'bai_oneimage' => 'required',
                    'image' => 'required',
                ],[
                    'jenis_kejadian.required' => 'Data jenis kejadian wajib di isi',
                    'nama_introgasi.required' => 'Data nama introgasi wajib di isi',
                    'umur_introgasi.required' => 'Data umur introgasi wajib di isi',
                    'pekerjaan_introgasi.required' => 'Data pekerjaan introgasi wajib di isi',
                    'bagian_introgasi.required' => 'Data bagian introgasi wajib di isi',
                    'nama_pelapor.required' => 'Data nama pelapor wajib di isi',
                    'detail_barang_kejadian.required' => 'Data detail barang dan kejadian wajib di isi',
                    'tempat_kejadian.required' => 'Data tempat kejadian wajib di isi',
                    'nama_korban.required' => 'Data nama korban wajib di isi',
                    'nik_korban.required' => 'Data nik (nomor induk karyawan) korban wajib di isi',
                    'bagian_korban.required' => 'Data bagian korban wajib di isi',
                    'nama_pelaku.required' => 'Data nama pelaku wajib di isi',
                    'umur_pelaku.required' => 'Data umur pelaku wajib di isi',
                    'ttl_pelaku.required' => 'Data tempat tinggal lahir pelaku wajib di isi',
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
                    'shift.required' => 'Data shift karyawan wajib di isi',
                    'bai_oneimage.required' => 'Data Foto Dokumentasi Kejadian wajib di isi',
                    'image.required' => 'Data Foto Dokumentasi Proses BAI wajib di isi'
        ]);

        try{
            DB::beginTransaction();

            $bai_id = $this->generateID('bai', 'ba_introgasi', 'bai_id');

            $createbaintrogasi = new BaIntrogasi([
                'bai_id' => $bai_id,
                // 'jenis_kejadian' => $request->get('jenis_kejadian'),
                // 'nama_introgasi' => $request->get('nama_introgasi'),
                // 'umur_introgasi' => $request->get('umur_introgasi'),
                // 'pekerjaan_introgasi' => $request->get('pekerjaan_introgasi'),
                // 'bagian_introgasi' => $request->get('bagian_introgasi'),
                // 'nama_pelapor' => $request->get('nama_pelapor'),
                // 'detail_barang_kejadian' => $request->get('detail_barang_kejadian'),
                // 'tempat_kejadian' => $request->get('tempat_kejadian'),
                // 'nama_korban' => $request->get('nama_korban'),
                // 'nik_korban' => $request->get('nik_korban'),
                // 'bagian_korban' => $request->get('bagian_korban'),
                // 'nama_pelaku' => $request->get('nama_pelaku'),
                // 'umur_pelaku' => $request->get('umur_pelaku'),
                // 'ttl_pelaku' => $request->get('ttl_pelaku'),
                // 'pekerjaan_pelaku' => $request->get('pekerjaan_pelaku'),
                // 'nik_pelaku' => $request->get('nik_pelaku'),
                // 'bagian_pelaku' => $request->get('bagian_pelaku'),
                // 'alamat_pelaku' => $request->get('alamat_pelaku'),
                // 'agama_pelaku' => $request->get('agama_pelaku'),
                // 'suku_pelaku' => $request->get('suku_pelaku'),
                // 'status_pelaku' => $request->get('status_pelaku'),
                // 'pendidikan_pelaku' => $request->get('pendidikan_pelaku'),
                // 'nik_ktp_pelaku' => $request->get('nik_ktp_pelaku'),
                // 'no_hp_pelaku' => $request->get('no_hp_pelaku'),
                // 'tempat_introgasi' => $request->get('tempat_introgasi'),
                // 'keterangan_kejadian' => $request->get('keterangan_kejadian'),
                // 'shift' => $request->get('shift'),
            ]);

            if ($request->input('jenis_kejadian') == 'lainnya') {
                $ambilKejadian = $request->input('jenis_kejadian_lainnya');
            } else {
                $ambilKejadian = $request->input('jenis_kejadian');
            }
            
            $createbaintrogasi->jenis_kejadian = $ambilKejadian;
            $createbaintrogasi->nama_introgasi = $request->input('nama_introgasi');
            $createbaintrogasi->umur_introgasi = $request->input('umur_introgasi');
            $createbaintrogasi->pekerjaan_introgasi = $request->input('pekerjaan_introgasi');
            $createbaintrogasi->bagian_introgasi = $request->input('bagian_introgasi');
            $createbaintrogasi->nama_pelapor = $request->input('nama_pelapor');
            $createbaintrogasi->detail_barang_kejadian = $request->input('detail_barang_kejadian');
            $createbaintrogasi->tempat_kejadian = $request->input('tempat_kejadian');
            $createbaintrogasi->nama_korban = $request->input('nama_korban');
            $createbaintrogasi->nik_korban = $request->input('nik_korban');
            $createbaintrogasi->bagian_korban = $request->input('bagian_korban');
            $createbaintrogasi->nama_pelaku = $request->input('nama_pelaku');
            $createbaintrogasi->umur_pelaku = $request->input('umur_pelaku');
            $createbaintrogasi->ttl_pelaku = $request->input('ttl_pelaku');
            $createbaintrogasi->pekerjaan_pelaku = $request->input('pekerjaan_pelaku');
            $createbaintrogasi->nik_pelaku = $request->input('nik_pelaku');
            $createbaintrogasi->bagian_pelaku = $request->input('bagian_pelaku');
            $createbaintrogasi->alamat_pelaku = $request->input('alamat_pelaku');
            $createbaintrogasi->agama_pelaku = $request->input('agama_pelaku');
            $createbaintrogasi->suku_pelaku = $request->input('suku_pelaku');
            $createbaintrogasi->status_pelaku = $request->input('status_pelaku');
            $createbaintrogasi->pendidikan_pelaku = $request->input('pendidikan_pelaku');
            $createbaintrogasi->nik_ktp_pelaku = $request->input('nik_ktp_pelaku');
            $createbaintrogasi->no_hp_pelaku = $request->input('no_hp_pelaku');
            $createbaintrogasi->tempat_introgasi = $request->input('tempat_introgasi');
            $createbaintrogasi->keterangan_kejadian = $request->input('keterangan_kejadian');
            $createbaintrogasi->shift = $request->input('shift');
            $createbaintrogasi->lk_id = $request->input('lk_id');

            // Untuk Foto Dokumentasi Kejadian
            $gambar = $request->bai_oneimage;
            $folderPathGambar = 'baioneimage-halosecurity/';
            $image_parts88 = explode(";base64,", $gambar);
            $image_base88 = base64_decode($image_parts88[1]);
            $imageGambar = uniqid() . '.jpg';

            $fileone = $folderPathGambar . $imageGambar;
            // Storage::put($file, $image_base88);
            \File::put($fileone, $image_base88);
            $createbaintrogasi->bai_oneimage = $imageGambar;

            // Untuk Foto Dokumentasi Proses BAI
            $img = $request->image;
            $folderPath = 'webcam-halosecurity/';
            $image_parts = explode(";base64,", $img);
            $image_base64 = base64_decode($image_parts[1]);
            $fileName = uniqid() . '.jpg';

            $file = $folderPath . $fileName;
            // Storage::put($file, $image_base64);
            \File::put($file, $image_base64);
            $createbaintrogasi->image = $fileName;
    
            $createbaintrogasi->save();

            $usersArray = SecurityUserGA::all()->pluck('nik')->toArray();

            $users = User::whereIn('username', $usersArray)->get()
            ->pluck('email');

            $users = $users->filter(function ($item) {
                return $item != '';
            });

            $emails = $users->toArray();

            $this->sendNewIntrogasiConfirmationMail($emails, $createbaintrogasi, $bai_id);

            $pertanyaan_introgasi_array = $request->pertanyaan_introgasi;
            $jawaban_introgasi_array = $request->jawaban_introgasi;

            foreach($pertanyaan_introgasi_array as $key => $pi){
                $introgasi = new BaiItems;
                $introgasi->bai_id = $bai_id;
                $introgasi->pertanyaan_introgasi = $pertanyaan_introgasi_array[$key];
                $introgasi->jawaban_introgasi = $jawaban_introgasi_array[$key];
                $introgasi->save();
            }

            if($request->foto_introgasi) {
                $__key = 0;
                foreach($request->foto_introgasi as $key => $attachment) {

                    $files = $attachment;
                    $keterangan_introgasi = $request->keterangan_introgasi;

                    $imageArray = [];

                    foreach($files as $file){
                        $folderPathIntrogasi = 'master_introgasi_gambar/';
                        $image_partIntrogasi = explode(";base64,", $file);
                        $image_base88 = base64_decode($image_partIntrogasi[1]);
                        $imageIntrogasi = uniqid() . '.jpg';

                        $fileintrogasi = $folderPathIntrogasi . $imageIntrogasi;

                        \File::put($fileintrogasi, $image_base88);
                        $imageArray[] = $imageIntrogasi;
                    }
                    
                    $dokumentasi = new DokumentasiIntrogasi();
                    $dokumentasi->bai_id = $bai_id;
                    $dokumentasi->foto_introgasi = implode(',', $imageArray);
                    $dokumentasi->keterangan_introgasi = $keterangan_introgasi[$__key];
                    $dokumentasi->save();

                    $__key++;
                }
            }
    
            DB::commit();
            return redirect()->route('ba-list-introgasi')->with(['success'=>'Data BA Introgasi berhasil ditambahkan']);
        }catch(\Exception $e) {
            DB::rollBack();
            return redirect()->route('ba-list-introgasi')->with(['error'=> $e->getMessage()]);
        }

    }

    public function saveDraftIntrogasi(Request $request)
    {
        try{
            DB::beginTransaction();

            $bai_id = $this->generateID('bai', 'ba_introgasi', 'bai_id');

            // Untuk Foto Dokumentasi Kejadian
            $gambar = $request->bai_oneimage;
            $folderPathGambar = 'baioneimage-halosecurity/';
            $image_parts88 = explode(";base64,", $gambar);
            $image_base88 = base64_decode($image_parts88[1]);
            $imageGambar = uniqid() . '.jpg';

            $fileone = $folderPathGambar . $imageGambar;
            \File::put($fileone, $image_base88);

            // Untuk Foto Dokumentasi Proses BAI (Kamera)
            $img = $request->image;
            $folderPath = 'webcam-halosecurity/';
            $image_parts = explode(";base64,", $img);
            $image_base64 = base64_decode($image_parts[1]);
            $fileName = uniqid() . '.jpg';

            $file = $folderPath . $fileName;
            \File::put($file, $image_base64);

            if ($request->get('jenis_kejadian') == 'lainnya') {
                $ambilKejadian = $request->get('jenis_kejadian_lainnya');
            } else {
                $ambilKejadian = $request->get('jenis_kejadian');
            }

            $draftbaintrogasi = new BaIntrogasi([
                'bai_id' => $bai_id,
                'lk_id' => $request->get('laporan_kejadian_id'),
                'jenis_kejadian' => $ambilKejadian,
                'nama_introgasi' => $request->get('nama_introgasi'),
                'umur_introgasi' => $request->get('umur_introgasi'),
                'pekerjaan_introgasi' => $request->get('pekerjaan_introgasi'),
                'bagian_introgasi' => $request->get('bagian_introgasi'),
                'nama_pelapor' => $request->get('nama_pelapor'),
                'detail_barang_kejadian' => $request->get('detail_barang_kejadian'),
                'tempat_kejadian' => $request->get('tempat_kejadian'),
                'nama_korban' => $request->get('nama_korban'),
                'nik_korban' => $request->get('nik_korban'),
                'bagian_korban' => $request->get('bagian_korban'),
                'nama_pelaku' => $request->get('nama_pelaku'),
                'umur_pelaku' => $request->get('umur_pelaku'),
                'ttl_pelaku' => $request->get('ttl_pelaku'),
                'pekerjaan_pelaku' => $request->get('pekerjaan_pelaku'),
                'nik_pelaku' => $request->get('nik_pelaku'),
                'bagian_pelaku' => $request->get('bagian_pelaku'),
                'alamat_pelaku' => $request->get('alamat_pelaku'),
                'agama_pelaku' => $request->get('agama_pelaku'),
                'suku_pelaku' => $request->get('suku_pelaku'),
                'status_pelaku' => $request->get('status_pelaku'),
                'shift' => $request->get('shift'),
                'pendidikan_pelaku' => $request->get('pendidikan_pelaku'),
                'nik_ktp_pelaku' => $request->get('nik_ktp_pelaku'),
                'no_hp_pelaku' => $request->get('no_hp_pelaku'),
                'tempat_introgasi' => $request->get('tempat_introgasi'),
                'keterangan_kejadian' => $request->get('keterangan_kejadian'),
                'bai_oneimage' => $imageGambar,
                'image' => $fileName,
                'status_draft' => 'yes'
            ]);
    
            $draftbaintrogasi->save();

            $cekPertanyaan = json_decode($request->pertanyaan, true);
            $cekJawaban = json_decode($request->jawaban, true);

            foreach ($cekPertanyaan as $key => $pi) {
                $introgasi = new BaiItems;
                $introgasi->bai_id = $bai_id;
                $introgasi->pertanyaan_introgasi = $cekPertanyaan[$key]['pertanyaan_introgasi'];
                $introgasi->jawaban_introgasi = $cekJawaban[$key]['jawaban_introgasi'];
                $introgasi->save();
            }

            $cekFoto = json_decode($request->foto, true);
            $cekKeterangan = json_decode($request->keterangan, true);

            if($cekFoto) {
                $__key = 0;
                foreach($cekFoto as $key => $attachment) {
                    // $files = $attachment;

                    // dd($files);

                    $imageArray = [];

                    // foreach($files as $file){
                        $folderPathIntrogasi = 'master_introgasi_gambar/';
                        $image_partIntrogasi = explode(";base64,", $cekFoto[$key]['foto_introgasi']);
                        $image_base88 = base64_decode($image_partIntrogasi[1]);
                        $imageIntrogasi = uniqid() . '.jpg';

                        $fileintrogasi = $folderPathIntrogasi . $imageIntrogasi;

                        \File::put($fileintrogasi, $image_base88);
                        $imageArray[] = $imageIntrogasi;
                    // }
                    
                    $dokumentasi = new DokumentasiIntrogasi();
                    $dokumentasi->bai_id = $bai_id;
                    $dokumentasi->foto_introgasi = implode(',', $imageArray);
                    $dokumentasi->keterangan_introgasi = $cekKeterangan[$key]['keterangan_introgasi'];
                    $dokumentasi->save();

                    $__key++;
                }
            }

            DB::commit();

            return response()->json(['message' => 'Data BA Introgasi berhasil ditambahkan', 'status' => 'success', 'bai_id' => $bai_id], 200);
        }catch(\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage(), 'status' => 'error'], 500);
        }
    }

    public function previewDraftIntrogasi($bai_id)
    {
        $item = BaIntrogasi::find($bai_id);

        $pdf = PDF::loadView('pages.halo-security.ba-introgasi',compact('item'));

        return $pdf->stream('Data Berita Acara Introgasi.pdf');
    }

    public function sendNewIntrogasiConfirmationMail($emails, $createbaintrogasi, $bai_id)
    {
        Mail::mailer('smtp')->to($emails)->send(new AddBaIntrogasi($createbaintrogasi, $bai_id));
    }

    // Datatable add
    public function AddTemplateBaIntrogasi(Request $request)
    {
        $template = new TemplateBaiItems;

        $template->pertanyaan_introgasi = $request->pertanyaan_introgasi;
        $template->jawaban_introgasi = $request->jawaban_introgasi;

        $result = $template->save();

        if($result) {
            return response()->json([
                'message' => "Data Template Tanya Jawab BA Introgasi berhasil ditambahkan!",
                "code"    => 200
            ]);
        } else  {
            return response()->json([
                'message' => "Data Template Tanya Jawab BA Introgasi gagal ditambahkan!",
                "code"    => 500
            ]);
        }
    }

    public function edittemplate($id)
    {
        $template = TemplateBaiItems::find($id);
        return response()->json($template);
    }

    public function updateTemplate(Request $request)
    {
        $template = TemplateBaiItems::find($request->id);
        $template->pertanyaan_introgasi = $request->pertanyaan_introgasi;
        $template->jawaban_introgasi = $request->jawaban_introgasi;
        $template->save();
        return response()->json($template);
    }

    public function deleteTemplate($id)
    {
        $template = TemplateBaiItems::find($id);
        $template->delete();
        return response()->json(['success'=>'Data Template Tanya Jawab berhasil dihapus']);
    }

    public function getkejadian()
    {   
        $kejadian = BaLaporanKejadian::with('saksis')->get();

        if($kejadian) {
            return response()->json([
                'message' => "Data Found",
                "code"    => 200,
                "data"  => $kejadian
            ]);
        } else  {
            return response()->json([
                'message' => "Internal Server Error",
                "code"    => 500
            ]);
        }
    }

    // Datatable read
    public function fetchtemplate()
    {
        $templates = TemplateBaiItems::select('template_bai_items.id','template_bai_items.pertanyaan_introgasi', 'template_bai_items.jawaban_introgasi')->get();
        
        if($templates) {
            return response()->json([
                'message' => "Data Found",
                "code"    => 200,
                "data"  => $templates
            ]);
        } else  {
            return response()->json([
                'message' => "Internal Server Error",
                "code"    => 500
            ]);
        }
    }

    // Datatable edit
    public function edit(Request $request)
    {
        $result = TemplateBaiItems::where('id', $request->id)->first();

        if($result) {
            return response()->json([
                'message' => "Data Found",
                "code"    => 200,
                "data"    => $result
            ]);
        } else  {
            return response()->json([
                'message' => "Internal Server Error",
                "code"    => 500
            ]);
        }
    }

    // Data table proses edit
    public function update(Request $request)
    {
        $result = TemplateBaiItems::where('id', $request->id)->update([
            'pertanyaan_introgasi'      => $request->edit_pertanyaan_introgasi,
            'jawaban_introgasi'     => $request->edit_jawaban_introgasi,
        ]);

        if($result) {
            return response()->json([
                'message' => "Data Template Tanya Jawab BA Introgasi berhasil diubah!",
                "code"    => 200,
            ]);
        } else  {
            return response()->json([
                'message' => "Data Template Tanya Jawab BA Introgasi gagal diubah!",
                "code"    => 500
            ]);
        }
    }

    // Datatable proses hapus
    public function destroy(Request $request)
    {
        $result = TemplateBaiItems::where('id', $request->id)->delete();

        if($result) {
            return response()->json([
                'message' => "Data Template Tanya Jawab BA Introgasi berhasil dihapus!",
                "code"    => 200,
            ]);
        } else  {
            return response()->json([
                'message' => "Data Template Tanya Jawab BA Introgasi gagal dihapus!",
                "code"    => 500
            ]);
        }
    }

    public function render()
    {
        Permission::has('hs_createbai');
        
        return view('livewire.createbaintrogasi');
    }
}
