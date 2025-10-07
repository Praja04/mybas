<?php

namespace App\Http\Controllers;

use App\Models\Province;
use App\PKWAnakAnak;
use App\HrKaryawan;
use App\PKWEmailNotification;
use App\PKWPengalamanKerja;
use App\PKWSaudaraKandung;
use Illuminate\Http\Request;
use App\PKWKaryawan;
use App\PKWDivisi;
use App\PKWBagian;
use App\PKWGroup;
use App\PKWAdmin;
use Illuminate\Support\Facades\Mail;
use App\Mail\PKWNotification;

use Maatwebsite\Excel\Facades\Excel;
use App\Imports\PKWEmployeeImport;
use App\Exports\PKWKaryawanExportPayroll;
use App\Exports\PKWKaryawanExportIris;
use App\Exports\PKWKaryawanExportBPJS;
use App\Exports\PKWKaryawanExportBPJSTK;
use App\Exports\PKWKaryawanExportBankMandiri;
use App\Exports\PKWKaryawanExportMaster;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

use Image;


class PKWKaryawanController extends Controller
{
    protected $search;

    public function uploadSecureAccess(Request $request)
    {
        // http://localhost/newpas/images/foto/
        $karyawan = PKWKaryawan::find($request->id);
        $nik = $karyawan->nik;
        $foto_name = $karyawan->foto_diri;

        $url = \URL::to('/') . '/images/foto/' . $foto_name;
        $image = file_get_contents($url);
        // return $url;
        // $image = $request->file('image');
        // $contents = $image->openFile()->fread($image->getSize());

        $data = DB::connection('192.168.178.44-admin')
            ->table('MSIDCARD')
            ->select('EMPCARDID')
            ->where(DB::raw('CAST(BARCODE AS SIGNED)'), $nik)
            // ->where('STATUS', 'X')
            // ->where('RFID', '899976977')
            ->orderBy('CREATEDON', 'desc')
            ->first();

        $user = DB::connection('192.168.178.44-admin')
            ->table('MSIDCARD')
            ->where('EMPCARDID', $data->EMPCARDID)
            ->update([
                'FOTOTYPE' => 'image/jpg',
                'FOTOBLOB' => $image
            ]);

        $karyawan->upload_secure_access = 'sudah';
        $karyawan->upload_secure_access_time = date('Y-m-d H:i:s');
        $karyawan->save();

        return response()->json([
            'success' => 1,
            'message' => 'Upload image to secure access succeed'
        ]);
    }

    public function exportPayroll($ids)
    {
        return Excel::download(new PKWKaryawanExportPayroll($ids), 'export-payroll-' . time() . '.xls');
    }

    public function exportIris($ids)
    {
        return Excel::download(new PKWKaryawanExportIris($ids), 'export-iris-' . time() . '.xls');
    }

    public function exportBPJS($ids)
    {
        return Excel::download(new PKWKaryawanExportBPJS($ids), 'export-bpjs-' . time() . '.xls');
    }

    public function exportBPJSTK($ids)
    {
        return Excel::download(new PKWKaryawanExportBPJSTK($ids), 'EXPORT-BPJS-TK-' . time() . '.xls');
    }

    public function exportBankMandiri($ids)
    {
        return Excel::download(new PKWKaryawanExportBankMandiri($ids), 'EXPORT-BANK-MANDIRI-' . time() . '.xls');
    }

    public function exportMasterAll($ids)
    {
        return Excel::download(new PKWKaryawanExportMaster($ids), 'EXPORT-MASTER-ALL-' . time() . '.xls');
    }

    public function exportPKWTT($ids)
    {
        $employees = PKWKaryawan::whereIn('id', explode(',', $ids))->get();
        return view('pkw.print.pkwtt', [
            'employees' => $employees
        ]);
    }

    public function pengambilanIdCardScan(Request $request)
    {
        $data = [];

        $id_card = (int)$request->id_card;

        // Get data dari secure access berdasarkan nomor kartu
        $user = DB::connection('192.168.154.44')
            ->table('MSIDCARD')
            ->select('NIK', 'EMPNM', 'CREATEDON')
            ->where(['CARDNODEVICE' => $id_card])
            ->orderByRaw('CAST(SUBSTR(NIK, 8) AS SIGNED) desc')
            ->first();

        if ($user == null) {
            return response()->json(['success' => 0, 'message' => 'Data not found']);
        }

        $nik = $user->NIK;

        $karyawan = PKWKaryawan::where('nik', $nik)->first();

        if ($karyawan == null) {
            return response()->json(['success' => 0, 'message' => 'Data not found']);
        }

        // Berarti id card sudah diambil
        if ($karyawan->ambil_id_card == 'sudah') {
            return response()->json(['success' => 0, 'message' => 'ID Card sudah diambil']);
        }

        $karyawan->bagian;
        $karyawan->jabatan;

        return response()->json([
            'success' => 1,
            'message' => 'Get data succeed',
            'data' => $karyawan
        ]);
    }

    public function pengambilanIdCardSubmit(Request $request)
    {
        $data = [];

        $rf = (int)$request->rf;
        $nik = $request->nik;

        // Update
        $karyawan = PKWKaryawan::where('nik', $nik)->first();
        $karyawan->ambil_id_card = 'sudah';
        $karyawan->waktu_pengambilan_id_card = date('Y-m-d H:i:s');
        $karyawan->pic_pengambilan_id_card = Auth::user()->username;
        $karyawan->rf_ktp = $rf;
        $karyawan->save();

        return response()->json([
            'success' => 1,
            'message' => 'Save data succeed'
        ]);
    }

    public function calon()
    {
        $divisis = PKWDivisi::all();
        return view('pkw.calon-karyawan', [
            'divisis' => $divisis,
        ]);
    }

    public function uploadImage(Request $request)
    {
        $karyawan = PKWKaryawan::find($request->id_user);
        // dd($request->all());

        if ($request->file('file_foto')) {
            $foto = $request->file('file_foto');
            $foto_name = 'foto-' . time() . uniqid() . '.jpg';
            Image::make($foto)->resize(null, 1000, function ($constraint) {
                $constraint->aspectRatio();
            })->save('images/foto/' . $foto_name);
            $karyawan->foto_diri = $foto_name;
        }
        if ($request->file('file_ktp')) {
            $ktp = $request->file('file_ktp');
            $ktp_name = 'ktp-' . time() . uniqid() . '.jpg';
            Image::make($ktp)->save('images/ktp/' . $ktp_name);
            $karyawan->foto_ktp = $ktp_name;
        }

        if ($request->file('file_npwp')) {
            $npwp = $request->file('file_npwp');
            $npwp_name = 'npwp-' . time() . uniqid() . '.jpg';
            Image::make($npwp)->save('images/npwp/' . $npwp_name);
            $karyawan->foto_npwp = $npwp_name;
        }

        if ($request->file('file_kk')) {
            $kk = $request->file('file_kk');
            $kk_name = 'kk-' . time() . uniqid() . '.jpg';
            Image::make($kk)->save('images/kk/' . $kk_name);
            $karyawan->foto_kk = $kk_name;
        }

        $karyawan->save();

        return response()->json(['success' => 1, 'message' => 'Upload foto succeed']);
    }

    public function uploadImages(Request $request)
    {
        $array = [];
        $employees = Excel::toArray(new PKWEmployeeImport, $request->file('file'));
        foreach ($employees[0] as $key => $employee) {
            if ($key == 0) {
                foreach ($employee as $_key => $e) {
                    $column[$e] = $_key;
                }
            } else {
                try {
                    $karyawan = PKWKaryawan::where('nik', $employee[$column['nik']])->first();
                    if ($karyawan != null) {
                        if ($karyawan->foto_diri == '') {

                            // Ini proses upload foto
                            $foto_url = $employee[$column['foto_url']];
                            $ktp_url = $employee[$column['ktp_url']];
                            $npwp_url = $employee[$column['npwp_url']];
                            $kk_url = $employee[$column['kk_url']];

                            if ($foto_url != '') {
                                $foto_name = 'foto-' . time() . uniqid() . '.jpg';
                                Image::make($foto_url)->resize(null, 1000, function ($constraint) {
                                    $constraint->aspectRatio();
                                })->save('images/foto/' . $foto_name);
                            } else {
                                $foto_name = '';
                            }

                            if ($ktp_url != '') {
                                $ktp_name = 'ktp-' . time() . uniqid() . '.jpg';
                                Image::make($ktp_url)->save('images/ktp/' . $ktp_name);
                            } else {
                                $ktp_name = '';
                            }

                            if ($npwp_url != '') {
                                $npwp_name = 'npwp-' . time() . uniqid() . '.jpg';
                                Image::make($npwp_url)->save('images/npwp/' . $npwp_name);
                            } else {
                                $npwp_name = '';
                            }

                            if ($kk_url != '') {
                                $kk_name = 'kk-' . time() . uniqid() . '.jpg';
                                Image::make($kk_url)->save('images/kk/' . $kk_name);
                            } else {
                                $kk_name = '';
                            }

                            $karyawan->foto_diri = $foto_name;
                            $karyawan->foto_ktp = $ktp_name;
                            $karyawan->foto_npwp = $npwp_name;
                            $karyawan->foto_kk = $kk_name;
                            $karyawan->save();
                        }
                    }
                } catch (\Throwable $th) {
                    // dd([
                    //     'karyawan' => $karyawan,
                    //     'error' => $th
                    // ]);
                }
            }
        }

        return response()->json(['success' => 1, 'message' => 'Upload images succeed']);
    }

    public function upload(Request $request)
    {
        // dd($request->file('file'));
        $array = [];
        $employees = Excel::toArray(new PKWEmployeeImport, $request->file('file'));
        foreach ($employees[0] as $key => $employee) {
            // $array[] = $employee;
            // Here is the cool thing.
            if ($key == 0) {
                foreach ($employee as $_key => $e) {
                    $column[$e] = $_key;
                }
            } else {

                // Ceck dulu apakah karyawan dengan nik ini udah ada atau belum
                $karyawan = PKWKaryawan::where('nik', $employee[$column['nik']])->first();
                if ($karyawan == null) {
                    // Insert ke database
                    $karyawan = new PKWKaryawan;
                    $karyawan->nik = $employee[$column['nik']];
                    $karyawan->nik_ktp = $employee[$column['nomor_ktp']];
                    $karyawan->nama = ucwords(strtolower($employee[$column['nama_lengkap']]));
                    $karyawan->agama = ucwords(strtolower($employee[$column['agama']]));
                    $karyawan->jenis_kelamin = $employee[$column['jenis_kelamin']];
                    $karyawan->tempat_lahir = ucwords(strtolower($employee[$column['tempat_lahir']]));
                    $karyawan->tanggal_lahir = $employee[$column['tanggal_lahir']];
                    $karyawan->golongan_darah = $employee[$column['golongan_darah']];
                    $karyawan->tanggal_masuk = $employee[$column['tanggal_masuk']];
                    $karyawan->status_perdata = $employee[$column['status_perdata']];
                    $karyawan->nama_pasangan = $employee[$column['nama_pasangan']];
                    $karyawan->tempat_pernikahan = $employee[$column['tempat_pernikahan']];
                    $karyawan->tanggal_pernikahan = $employee[$column['tanggal_pernikahan']];
                    $karyawan->tanggal_lahir_pasangan = $employee[$column['tanggal_lahir_pasangan']] == '' ? null : $employee[$column['tanggal_lahir_pasangan']];
                    $karyawan->tempat_lahir_pasangan = $employee[$column['tempat_lahir_pasangan']];
                    $karyawan->pekerjaan_pasangan = $employee[$column['pekerjaan_pasangan']];
                    $karyawan->tempat_pasangan_bekerja = $employee[$column['tempat_pasangan_bekerja']];
                    $karyawan->nama_ayah = $employee[$column['orang_tua_nama_ayah']];
                    $karyawan->tempat_lahir_ayah = $employee[$column['orang_tua_tempat_lahir_ayah']];
                    $karyawan->tanggal_lahir_ayah = $employee[$column['orang_tua_tanggal_lahir_ayah']] == '' ? null : $employee[$column['orang_tua_tanggal_lahir_ayah']];
                    $karyawan->nama_ibu = $employee[$column['orang_tua_nama_ibu']];
                    $karyawan->tempat_lahir_ibu = $employee[$column['orang_tua_tempat_lahir_ibu']];
                    $karyawan->tanggal_lahir_ibu = $employee[$column['orang_tua_tanggal_lahir_ibu']] == '' ? null : $employee[$column['orang_tua_tanggal_lahir_ibu']];
                    $karyawan->nama_ayah_mertua = $employee[$column['mertua_nama_ayah']];
                    $karyawan->tempat_lahir_ayah_mertua = $employee[$column['mertua_tempat_lahir_ayah']];
                    $karyawan->tanggal_lahir_ayah_mertua = $employee[$column['mertua_tanggal_lahir_ayah']] == '' ? null : $employee[$column['mertua_tanggal_lahir_ayah']];
                    $karyawan->nama_ibu_mertua = $employee[$column['mertua_nama_ibu']];
                    $karyawan->tempat_lahir_ibu_mertua = $employee[$column['mertua_tempat_lahir_ibu']];
                    $karyawan->tanggal_lahir_ibu_mertua = $employee[$column['mertua_tanggal_lahir_ibu']] == '' ? null : $employee[$column['mertua_tanggal_lahir_ibu']];
                    $karyawan->nama_kontak_darurat = $employee[$column['kontak_darurat_nama']];
                    $karyawan->hubungan_kontak_darurat = $employee[$column['kontak_darurat_hubungan']];
                    $karyawan->no_telepon_kontak_darurat = $employee[$column['kontak_darurat_no_telepon']];
                    $karyawan->nomor_rekening_bank = $employee[$column['nomor_rekening_bank']];
                    $karyawan->nomor_kartu_bpjs_ketenagakerjaan = $employee[$column['nomor_kartu_bpjs_ketenagakerjaan']];
                    $karyawan->keterangan_kartu_bpjs_ketenagakerjaan = $employee[$column['keterangan_kartu_bpjs_ketenagakerjaan']];
                    $karyawan->nomor_kartu_bpjs_kesehatan = $employee[$column['nomor_kartu_bpjs_kesehatan']];
                    $karyawan->keterangan_kartu_bpjs_kesehatan = $employee[$column['keterangan_kartu_bpjs_kesehatan']];
                    $karyawan->status = 'recruitment';
                    $karyawan->no_kk = $employee[$column['nomor_kk']];
                    $karyawan->no_npwp = $employee[$column['nomor_npwp']];
                    $karyawan->alamat_ktp_provinsi = $employee[$column['province']];
                    $karyawan->alamat_ktp_kota = $employee[$column['regency']];
                    $karyawan->alamat_ktp_kecamatan = $employee[$column['district']];
                    $karyawan->alamat_ktp_desa = $employee[$column['village']];
                    $karyawan->alamat_ktp = $employee[$column['addressDetail']];
                    $karyawan->alamat_sekarang_provinsi = $employee[$column['alamat_sekarang_provinsi']] == '' ? $employee[$column['province']] : $employee[$column['alamat_sekarang_provinsi']];
                    $karyawan->alamat_sekarang_kota = $employee[$column['alamat_sekarang_kota']] == '' ? $employee[$column['regency']] : $employee[$column['alamat_sekarang_kota']];
                    $karyawan->alamat_sekarang_kecamatan = $employee[$column['alamat_sekarang_kecamatan']] == '' ? $employee[$column['district']] : $employee[$column['alamat_sekarang_kecamatan']];
                    $karyawan->alamat_sekarang_desa = $employee[$column['alamat_sekarang_desa']] == '' ? $employee[$column['village']] : $employee[$column['alamat_sekarang_desa']];
                    $karyawan->alamat_sekarang = $employee[$column['alamat_sekarang_alamat']] == '' ? $employee[$column['addressDetail']] : $employee[$column['alamat_sekarang_alamat']];
                    $karyawan->nomor_hp = $employee[$column['nomor_hp']];
                    $karyawan->email = $employee[$column['email']];
                    $karyawan->sosmed_facebook = $employee[$column['facebook']];
                    $karyawan->sosmed_twitter = $employee[$column['twitter']];
                    $karyawan->sosmed_linkedin = $employee[$column['linkedin']];
                    $karyawan->sosmed_instagram = $employee[$column['instagram']];
                    $karyawan->pendidikan = $employee[$column['pendidikan_terakhir']];
                    $karyawan->nama_sekolah = $employee[$column['pendidikan_nama_sekolah']];
                    $karyawan->jurusan = $employee[$column['pendidikan_jurusan']];
                    $karyawan->kursus = $employee[$column['pendidikan_kursus']];
                    $karyawan->foto_diri = '';
                    $karyawan->foto_ktp = '';
                    $karyawan->foto_npwp = '';
                    $karyawan->foto_kk = '';
                    $karyawan->save();
                }
            }
        }
        return response()->json(['success' => 1, 'message' => 'Upload data succeed']);
    }

    public function update(Request $request)
    {
        $karyawan = PKWKaryawan::find($request->pk);
        $karyawan->{$request->name} = $request->value;
        $karyawan->save();
        return response()->json(['success' => 1, 'message' => 'Data Karyawan Update succeed']);
    }

    public function karyawan()
    {
        $divisis = PKWDivisi::all();
        $groups = PKWGroup::all();
        $admins = PKWAdmin::all();
        return view('pkw.karyawan', [
            'divisis' => $divisis,
            'groups' => $groups,
            'admins' => $admins
        ]);
    }

    public function formatTanggal($date)
    {
        $year = explode('-', $date)[0];
        $month = explode('-', $date)[1];
        $date = explode('-', $date)[2];
        return $date . '/' . $month . '/' . $year;
    }

    public function store(Request $request)
    {
        $karyawan = new PKWKaryawan;
        $karyawan->nik = $request->nik;
        $karyawan->nama = $request->nama;
        $karyawan->agama = $request->agama;
        $karyawan->jenis_kelamin = $request->jenis_kelamin;
        $karyawan->tempat_lahir = $request->tempat_lahir;
        $karyawan->tanggal_lahir = $request->tanggal_lahir;
        $karyawan->alamat_rumah = $request->alamat_rumah;
        $karyawan->alamat_rumah_luar_kota = $request->alamat_rumah_luar_kota;
        $karyawan->tanggal_masuk = $request->tanggal_masuk;
        $karyawan->status_perdata = $request->status_perdata;
        $karyawan->nama_pasangan = $request->nama_pasangan;
        $karyawan->tempat_pernikahan = $request->tempat_pernikahan;
        $karyawan->tanggal_pernikahan = $request->tanggal_pernikahan;
        $karyawan->tempat_lahir_pasangan = $request->tempat_lahir_pasangan;
        $karyawan->tanggal_lahir_pasangan = $request->tanggal_lahir_pasangan;
        $karyawan->pekerjaan_pasangan = $request->pekerjaan_pasangan;
        $karyawan->tempat_pasangan_bekerja = $request->tempat_pasangan_bekerja;
        $karyawan->nama_ayah = $request->orang_tua_nama_ayah;
        $karyawan->tempat_lahir_ayah = $request->orang_tua_tempat_lahir_ayah;
        $karyawan->tanggal_lahir_ayah = $request->orang_tua_tanggal_lahir_ayah;
        $karyawan->nama_ibu = $request->orang_tua_nama_ibu;
        $karyawan->tempat_lahir_ibu = $request->orang_tua_tempat_lahir_ibu;
        $karyawan->tanggal_lahir_ibu = $request->orang_tua_tanggal_lahir_ibu;
        $karyawan->nama_ayah_mertua = $request->mertua_nama_ayah;
        $karyawan->tempat_lahir_ayah_mertua = $request->mertua_tempat_lahir_ayah;
        $karyawan->tanggal_lahir_ayah_mertua = $request->mertua_tanggal_lahir_ayah;
        $karyawan->nama_ibu_mertua = $request->mertua_nama_ibu;
        $karyawan->tempat_lahir_ibu_mertua = $request->mertua_tempat_lahir_ibu;
        $karyawan->tanggal_lahir_ibu_mertua = $request->mertua_tanggal_lahir_ibu;
        $karyawan->nama_kontak_darurat = $request->kontak_darurat_nama;
        $karyawan->hubungan_kontak_darurat = $request->kontak_darurat_hubungan;
        $karyawan->no_telepon_kontak_darurat = $request->kontak_darurat_no_telepon;
        $karyawan->nomor_rekening_bank = $request->nomor_rekening_bank;
        $karyawan->nomor_kartu_bpjs_ketenagakerjaan = $request->nomor_kartu_bpjs_ketenagakerjaan;
        $karyawan->keterangan_kartu_bpjs_ketenagakerjaan = $request->keterangan_kartu_bpjs_ketenagakerjaan;
        $karyawan->nomor_kartu_bpjs_kesehatan = $request->nomor_kartu_bpjs_kesehatan;
        $karyawan->keterangan_kartu_bpjs_kesehatan = $request->keterangan_kartu_bpjs_kesehatan;
        $karyawan->status = 'recruitment';
        $karyawan->save();

        $id = $karyawan->id;
        //        $id = 2;
        for ($i = 1; $i <= 4; $i++) {
            if ($request['pengalaman_kerja_perusahaan_' . $i] != null) {
                $pengalaman = new PKWPengalamanKerja;
                $pengalaman->id_karyawan = $id;
                $pengalaman->nama_perusahaan = $request['pengalaman_kerja_perusahaan_' . $i];
                $pengalaman->jabatan = $request['pengalaman_kerja_jabatan_' . $i];
                $pengalaman->tanggal_mulai = $request['pengalaman_kerja_tanggal_mulai_' . $i];
                $pengalaman->tanggal_selesai = $request['pengalaman_kerja_tanggal_selesai_' . $i];
                $pengalaman->kota = $request['pengalaman_kerja_kota_' . $i];
                $pengalaman->save();
            }

            if ($request['anak_anak_nama_' . $i] != null) {
                $anak = new PKWAnakAnak;
                $anak->id_karyawan = $id;
                $anak->nama = $request['anak_anak_nama_' . $i];
                $anak->jenis_kelamin = $request['anak_anak_jenis_kelamin_' . $i];
                $anak->tempat_lahir = $request['anak_anak_tempat_lahir_' . $i];
                $anak->tanggal_lahir = $request['anak_anak_tanggal_lahir_' . $i];
                $anak->save();
            }

            if ($request['saudara_kandung_nama_' . $i] != null) {
                $saudara = new PKWSaudaraKandung;
                $saudara->id_karyawan = $id;
                $saudara->nama = $request['saudara_kandung_nama_' . $i];
                $saudara->jenis_kelamin = $request['saudara_kandung_jenis_kelamin_' . $i];
                $saudara->tempat_lahir = $request['saudara_kandung_tempat_lahir_' . $i];
                $saudara->tanggal_lahir = $request['saudara_kandung_tanggal_lahir_' . $i];
                $saudara->save();
            }
        }

        return response()->json(['success' => 1, 'message' => 'Save data succeed']);
    }

    public function deleteKaryawan($id)
    {
        $karyawan = PKWKaryawan::destroy($id);

        return response()->json(['success' => 1, 'message' => 'Delete data succeed']);
    }

    public function getKaryawan($status, Request $request)
    {
        $data = [];
        $where = [];

        if ($status == 'recruitment') {
            $where[] = ['status', '=', $status];
        } elseif ($status == 'karyawan') {
            $where[] = ['status', '!=', 'cut'];
            $where[] = ['status', '!=', 'recruitment'];
        } else {
            $where[] = ['status', '=', 'recruitment'];
        }

        $this->search = $request['query']['generalSearch'] ?? '';

        if (isset($request['query']['jenis_kelamin'])) {
            $where[] = ['jenis_kelamin', '=', $request['query']['jenis_kelamin']];
        }

        if (isset($request['query']['divisi'])) {
            $where[] = ['id_divisi', '=', $request['query']['divisi']];
        }

        if (isset($request['query']['tanggal_masuk'])) {
            $where[] = ['tanggal_masuk', '=', $request['query']['tanggal_masuk']];
        } else {
            if ($status == 'karyawan') {
                $where[] = ['tanggal_masuk', '=', date('Y-m-d')];
            }
        }

        $sort = 'desc';
        $field = 'nik';

        if (!is_null($request->sort) && isset($request->sort['field'])) {
            $fieldMapping = [
                'divisi' => 'id_divisi',
                'bagian' => 'id_bagian',
                'jabatan' => 'id_jabatan'
            ];

            $field = $fieldMapping[$request->sort['field']] ?? $request->sort['field'];
            $sort = $request->sort['sort'] ?? 'desc';
        }

        $karyawan = PKWKaryawan::where($where)
            ->where(function ($query) {
                $query->where('nik', 'like', '%' . $this->search . '%')
                    ->orWhere('nama', 'like', '%' . $this->search . '%')
                    ->orWhere('agama', 'like', '%' . $this->search . '%')
                    ->orWhere('tempat_lahir', 'like', '%' . $this->search . '%')
                    ->orWhere('nama_ayah', 'like', '%' . $this->search . '%')
                    ->orWhere('nama_ibu', 'like', '%' . $this->search . '%')
                    ->orWhere('nomor_rekening_bank', 'like', '%' . $this->search . '%')
                    ->orWhere('nomor_kartu_bpjs_ketenagakerjaan', 'like', '%' . $this->search . '%')
                    ->orWhere('nomor_kartu_bpjs_kesehatan', 'like', '%' . $this->search . '%');
            })
            ->orderBy($field, $sort)
            ->get();
        foreach ($karyawan as $k) {
            $data[] = [
                'id' => $k->id,
                'nik' => $k->nik,
                'nik_ktp' => $k->nik_ktp,
                'nama' => $k->nama,
                'alamat_rumah' => $k->alamat_rumah,
                'jenis_kelamin' => $k->jenis_kelamin == 'L' ? 'Laki-laki' : 'Perempuan',
                'tempat_lahir' => $k->tempat_lahir,
                'tanggal_lahir' => $this->formatTanggal($k->tanggal_lahir),
                'divisi' => $k->divisi->nama_divisi ?? '-',
                'bagian' => $k->bagian->nama_bagian ?? '-',
                'jabatan' => $k->jabatan->nama_jabatan ?? '-',
                'group' => $k->group->nama_group ?? '-',
                'admin' => $k->admin->nama_admin ?? '-',
                'agama' => $k->agama,
                'tanggal_masuk' => $k->tanggal_masuk != '' ? $this->formatTanggal($k->tanggal_masuk) : '',
                'status_perdata' => $k->status_perdata,
                'nama_pasangan' => $k->nama_pasangan,
                'tempat_pernikahan' => $k->tempat_pernikahan,
                'tanggal_pernikahan' => $k->tanggal_pernikahan != '' ? $this->formatTanggal($k->tanggal_pernikahan) : '',
                'tempat_lahir_pasangan' => $k->tempat_lahir_pasangan,
                'tanggal_lahir_pasangan' => $k->tanggal_lahir_pasangan,
                'pekerjaan_pasangan' => $k->pekerjaan_pasangan,
                'tempat_pasangan_bekarja' => $k->tempat_pasangan_bekarja,
                'nama_ayah' => $k->nama_ayah,
                'tempat_lahir_ayah' => $k->tempat_lahir_ayah,
                'tanggal_lahir_ayah' => $k->tanggal_lahir_ayah,
                'nama_ibu' => $k->nama_ibu,
                'tempat_lahir_ibu' => $k->tempat_lahir_ibu,
                'tanggal_lahir_ibu' => $k->tanggal_lahir_ibu,
                'nama_ayah_mertua' => $k->nama_ayah_mertua,
                'tempat_lahir_ayah_mertua' => $k->tempat_lahir_ayah_mertua,
                'tanggal_lahir_ayah_mertua' => $k->tanggal_lahir_ayah_mertua,
                'nama_ibu_mertua' => $k->nama_ibu_mertua,
                'tempat_lahir_ibu_mertua' => $k->tempat_lahir_ibu_mertua,
                'tanggal_lahir_ibu_mertua' => $k->tanggal_lahir_ibu_mertua,
                'nama_kontak_darurat' => $k->nama_kontak_darurat,
                'hubungan_kontak_darurat' => $k->hubungan_kontak_darurat,
                'no_tlepon_kontak_darurat' => $k->no_telepon_kontak_darurat,
                'nomor_rekening_bank' => $k->nomor_rekening_bank,
                'nomor_kartu_bpjs_ketenagakerjaan' => $k->nomor_kartu_bpjs_ketenagakerjaan,
                'keterangan_kartu_bpjs_ketenagakerjaan' => $k->keterangan_kartu_bpjs_ketenagakerjaan,
                'nomor_kartu_bpjs_kesehatan' => $k->nomor_kartu_bpjs_kesehatan,
                'keterangan_kartu_bpjs_kesehatan' => $k->keterangan_kartu_bpjs_kesehatan,
                'upload_secure_access' => $k->upload_secure_access,
                'foto_diri' => $k->foto_diri,
                'foto_ktp' => $k->foto_ktp,
                'foto_npwp' => $k->foto_npwp,
                'foto_kk' => $k->foto_kk
            ];
        }
        $response = [
            'meta' => [
                'page' => 1,
                'pages' => 1,
                'perpage' => 10,
                'total' => count($karyawan),
                'field' => $field
            ],
            'data' => $data
        ];

        return response()->json(['success' => 1, 'data' => $response]);
    }

    public function setDivisiBagianJabatan(Request $request)
    {
        $id_divisi = $request->divisi;
        $id_bagian = $request->bagian;
        $id_jabatan = $request->jabatan;

        foreach ($request->id as $id) {
            $karyawan = PKWKaryawan::find($id);
            $karyawan->id_divisi = $id_divisi;
            $karyawan->id_bagian = $id_bagian;
            $karyawan->id_jabatan = $id_jabatan;
            $karyawan->status = 'payroll';
            $karyawan->recruitment_confirm_time = date('Y-m-d H:i:s');
            $karyawan->save();
        }
        // $description = '
        //     <p></p>
        // ';
        // $notif = PKWEmailNotification::where('group', 'payroll')->get();
        // foreach ($notif as $n) {
        //     Mail::to($n->user->email)->send(new PKWNotification(
        //         'PAS PKW',
        //         'Notifikasi Karyawan Baru',
        //         'description',
        //         '/pkw/karyawan'
        //     ));
        // }
        return response()->json(['success' => 1, 'message' => 'Recruitment check succeed']);
    }

    public function setGroupAdmin(Request $request)
    {
        $group = $request->group;
        $admin = $request->admin;
        foreach ($request->id as $id) {
            $karyawan = PKWKaryawan::find($id);
            $karyawan->id_group = $group;
            $karyawan->id_admin = $admin;
            //            $karyawan->status = 'pkw';
            $karyawan->payroll_confirm_time = date('Y-m-d H:i:s');
            $karyawan->save();
            HrKaryawan::create([
                'nik' => $karyawan->nik,
                'nama' => $karyawan->nama,
                'agama' => $karyawan->agama,
                'pendidikan' => $karyawan->pendidikan,
                'nama_sekolah' => $karyawan->nama_sekolah,
                'jurusan' => $karyawan->jurusan,
                'kursus' => $karyawan->kursus,
                'jenis_kelamin' => $karyawan->jenis_kelamin,
                'tempat_lahir' => $karyawan->tempat_lahir,
                'tanggal_lahir' => $karyawan->tanggal_lahir,
                'golongan_darah' => $karyawan->golongan_darah,
                'nomor_hp' => $karyawan->nomor_hp,
                'email' => $karyawan->email,
                'sosmed_instagram' => $karyawan->sosmed_instagram,
                'sosmed_linkedin' => $karyawan->sosmed_linkedin,
                'sosmed_twitter' => $karyawan->sosmed_twitter,
                'sosmed_facebook' => $karyawan->sosmed_facebook,
                'tanggal_masuk' => $karyawan->tanggal_masuk,
                'status_perdata' => $karyawan->status_perdata,
                'nama_pasangan' => $karyawan->nama_pasangan,
                'tempat_pernikahan' => $karyawan->tempat_pernikahan,
                'tanggal_pernikahan' => $karyawan->tanggal_pernikahan,
                'tempat_lahir_pasangan' => $karyawan->tempat_lahir_pasangan,
                'tanggal_lahir_pasangan' => $karyawan->tanggal_lahir_pasangan,
                'pekerjaan_pasangan' => $karyawan->pekerjaan_pasangan,
                'tempat_pasangan_bekerja' => $karyawan->tempat_pasangan_bekerja,
                'nama_ayah' => $karyawan->nama_ayah,
                'tempat_lahir_ayah' => $karyawan->tempat_lahir_ayah,
                'tanggal_lahir_ayah' => $karyawan->tanggal_lahir_ayah,
                'nama_ibu' => $karyawan->nama_ibu,
                'tempat_lahir_ibu' => $karyawan->tempat_lahir_ibu,
                'tanggal_lahir_ibu' => $karyawan->tanggal_lahir_ibu,
                'nama_ayah_mertua' => $karyawan->nama_ayah_mertua,
                'tempat_lahir_ayah_mertua' => $karyawan->tempat_lahir_ayah_mertua,
                'tanggal_lahir_ayah_mertua' => $karyawan->tanggal_lahir_ayah_mertua,
                'nama_ibu_mertua' => $karyawan->nama_ibu_mertua,
                'tempat_lahir_ibu_mertua' => $karyawan->tempat_lahir_ibu_mertua,
                'tanggal_lahir_ibu_mertua' => $karyawan->tanggal_lahir_ibu_mertua,
                'nama_kontak_darurat' => $karyawan->nama_kontak_darurat,
                'hubungan_kontak_darurat' => $karyawan->hubungan_kontak_darurat,
                'no_telepon_kontak_darurat' => $karyawan->no_telepon_kontak_darurat,
                'nomor_rekening_bank' => $karyawan->nomor_rekening_bank,
                'nomor_kartu_bpjs_ketenagakerjaan' => $karyawan->nomor_kartu_bpjs_ketenagakerjaan,
                'keterangan_kartu_bpjs_ketenagakerjaan' => $karyawan->keterangan_kartu_bpjs_ketenagakerjaan,
                'nomor_kartu_bpjs_kesehatan' => $karyawan->nomor_kartu_bpjs_kesehatan,
                'keterangan_kartu_bpjs_kesehatan' => $karyawan->keterangan_kartu_bpjs_kesehatan,
                'nik_ktp' => $karyawan->nik_ktp,
                'no_kk' => $karyawan->no_kk,
                'no_npwp' => $karyawan->no_npwp,
                'alamat_ktp_provinsi' => $karyawan->alamat_ktp_provinsi,
                'alamat_ktp_kota' => $karyawan->alamat_ktp_kota,
                'alamat_ktp_kecamatan' => $karyawan->alamat_ktp_kecamatan,
                'alamat_ktp_desa' => $karyawan->alamat_ktp_desa,
                'alamat_ktp' => $karyawan->alamat_ktp,
                'alamat_sekarang_provinsi' => $karyawan->alamat_sekarang_provinsi,
                'alamat_sekarang_kota' => $karyawan->alamat_sekarang_kota,
                'alamat_sekarang_kecamatan' => $karyawan->alamat_sekarang_kecamatan,
                'alamat_sekarang_desa' => $karyawan->alamat_sekarang_desa,
                'alamat_sekarang' => $karyawan->alamat_sekarang,
                'foto_diri' => $karyawan->foto_diri,
                'foto_ktp' => $karyawan->foto_ktp,
                'foto_npwp' => $karyawan->foto_npwp,
                'foto_kk' => $karyawan->foto_kk,
                'level' => $karyawan->level,
                'kode_divisi' => optional($karyawan->divisi)->kode_divisi,
                'kode_bagian' => optional($karyawan->bagian)->kode_bagian,
                'kode_group'  => optional($karyawan->group)->nama_group,
                'kode_jabatan' => $karyawan->id_jabatan,
                'kode_admin' =>  optional($karyawan->admin)->kode_admin,
            ]);
        }
        return response()->json(['success' => 1, 'message' => 'Payroll check succeed']);
    }

    public function sendToRecruitment(Request $request)
    {
        foreach ($request->id as $id) {
            $karyawan = PKWKaryawan::find($id);
            $karyawan->status = 'recruitment';
            $karyawan->save();
        }
        return response()->json(['success' => 1, 'message' => 'Send back to recruitment succeed']);
    }

    public function formDataKaryawan()
    {
        $provinces = Province::all();
        return view('pkw.form-data-karyawan', ['provinces' => $provinces]);
    }

    public function storeDataKaryawan(Request $request)
    {
        $karyawan = new PKWKaryawan;
        $karyawan->nik_ktp = $request->nomor_ktp;
        $karyawan->no_kk = $request->nomor_kk;
        $karyawan->no_npwp = $request->nomor_npwp;
        $karyawan->nama = $request->nama;
        $karyawan->agama = $request->agama;
        $karyawan->jenis_kelamin = $request->jenis_kelamin;
        $karyawan->tempat_lahir = $request->tempat_lahir;
        $karyawan->tanggal_lahir = $request->tanggal_lahir;
        //        $karyawan->alamat_rumah = $request->alamat_rumah;

        // Di sini untuk alamat KTP

        $karyawan->alamat_ktp_provinsi = explode('-', $request->alamat_ktp_provinsi)[0];
        $karyawan->alamat_ktp_kota = explode('-', $request->alamat_ktp_kota)[0];
        $karyawan->alamat_ktp_kecamatan = explode('-', $request->alamat_ktp_kecamatan)[0];
        $karyawan->alamat_ktp_desa = explode('-', $request->alamat_ktp_desa)[0];
        $karyawan->alamat_ktp = explode('-', $request->alamat_ktp_alamat_rumah)[0];

        if ($request->alamat_sekarang_sesuai_ktp) {
            $karyawan->alamat_sekarang_provinsi = explode('-', $request->alamat_ktp_provinsi)[0];
            $karyawan->alamat_sekarang_kota = explode('-', $request->alamat_ktp_kota)[0];
            $karyawan->alamat_sekarang_kecamatan = explode('-', $request->alamat_ktp_kecamatan)[0];
            $karyawan->alamat_sekarang_desa = explode('-', $request->alamat_ktp_desa)[0];
            $karyawan->alamat_sekarang = explode('-', $request->alamat_ktp_alamat_rumah)[0];
        } else {
            $karyawan->alamat_sekarang_provinsi = $request->alamat_sekarang_provinsi;
            $karyawan->alamat_sekarang_kota = $request->alamat_sekarang_kota;
            $karyawan->alamat_sekarang_kecamatan = $request->alamat_sekarang_kecamatan;
            $karyawan->alamat_sekarang_desa = $request->alamat_sekarang_desa;
            $karyawan->alamat_sekarang = $request->alamat_sekarang;
        }
        $karyawan->tanggal_masuk = $request->tanggal_masuk;
        $karyawan->status_perdata = $request->status_perdata;
        $karyawan->nama_pasangan = $request->nama_pasangan;
        $karyawan->tempat_pernikahan = $request->tempat_pernikahan;
        $karyawan->tanggal_pernikahan = $request->tanggal_pernikahan;
        $karyawan->tempat_lahir_pasangan = $request->tempat_lahir_pasangan;
        $karyawan->tanggal_lahir_pasangan = $request->tanggal_lahir_pasangan;
        $karyawan->pekerjaan_pasangan = $request->pekerjaan_pasangan;
        $karyawan->tempat_pasangan_bekerja = $request->tempat_pasangan_bekerja;
        $karyawan->nama_ayah = $request->orang_tua_nama_ayah;
        $karyawan->tempat_lahir_ayah = $request->orang_tua_tempat_lahir_ayah;
        $karyawan->tanggal_lahir_ayah = $request->orang_tua_tanggal_lahir_ayah;
        $karyawan->nama_ibu = $request->orang_tua_nama_ibu;
        $karyawan->tempat_lahir_ibu = $request->orang_tua_tempat_lahir_ibu;
        $karyawan->tanggal_lahir_ibu = $request->orang_tua_tanggal_lahir_ibu;
        $karyawan->nama_ayah_mertua = $request->mertua_nama_ayah;
        $karyawan->tempat_lahir_ayah_mertua = $request->mertua_tempat_lahir_ayah;
        $karyawan->tanggal_lahir_ayah_mertua = $request->mertua_tanggal_lahir_ayah;
        $karyawan->nama_ibu_mertua = $request->mertua_nama_ibu;
        $karyawan->tempat_lahir_ibu_mertua = $request->mertua_tempat_lahir_ibu;
        $karyawan->tanggal_lahir_ibu_mertua = $request->mertua_tanggal_lahir_ibu;
        $karyawan->nama_kontak_darurat = $request->kontak_darurat_nama;
        $karyawan->hubungan_kontak_darurat = $request->kontak_darurat_hubungan;
        $karyawan->no_telepon_kontak_darurat = $request->kontak_darurat_no_telepon;
        $karyawan->nomor_rekening_bank = $request->nomor_rekening_bank;
        $karyawan->nomor_kartu_bpjs_ketenagakerjaan = $request->nomor_kartu_bpjs_ketenagakerjaan;
        $karyawan->keterangan_kartu_bpjs_ketenagakerjaan = $request->keterangan_kartu_bpjs_ketenagakerjaan;
        $karyawan->nomor_kartu_bpjs_kesehatan = $request->nomor_kartu_bpjs_kesehatan;
        $karyawan->keterangan_kartu_bpjs_kesehatan = $request->keterangan_kartu_bpjs_kesehatan;
        $karyawan->save();

        $id = $karyawan->id;
        //        $id = 2;
        for ($i = 1; $i <= 4; $i++) {
            if ($request['pengalaman_kerja_perusahaan_' . $i] != null) {
                $pengalaman = new PKWPengalamanKerja;
                $pengalaman->id_karyawan = $id;
                $pengalaman->nama_perusahaan = $request['pengalaman_kerja_perusahaan_' . $i];
                $pengalaman->jabatan = $request['pengalaman_kerja_jabatan_' . $i];
                $pengalaman->tanggal_mulai = $request['pengalaman_kerja_tanggal_mulai_' . $i];
                $pengalaman->tanggal_selesai = $request['pengalaman_kerja_tanggal_selesai_' . $i];
                $pengalaman->kota = $request['pengalaman_kerja_kota_' . $i];
                $pengalaman->save();
            }

            if ($request['anak_anak_nama_' . $i] != null) {
                $anak = new PKWAnakAnak;
                $anak->id_karyawan = $id;
                $anak->nama = $request['anak_anak_nama_' . $i];
                $anak->jenis_kelamin = $request['anak_anak_jenis_kelamin_' . $i];
                $anak->tempat_lahir = $request['anak_anak_tempat_lahir_' . $i];
                $anak->tanggal_lahir = $request['anak_anak_tanggal_lahir_' . $i];
                $anak->save();
            }

            if ($request['saudara_kandung_nama_' . $i] != null) {
                $saudara = new PKWSaudaraKandung;
                $saudara->id_karyawan = $id;
                $saudara->nama = $request['saudara_kandung_nama_' . $i];
                $saudara->jenis_kelamin = $request['saudara_kandung_jenis_kelamin_' . $i];
                $saudara->tempat_lahir = $request['saudara_kandung_tempat_lahir_' . $i];
                $saudara->tanggal_lahir = $request['saudara_kandung_tanggal_lahir_' . $i];
                $saudara->save();
            }
        }

        return response()->json(['success' => 1, 'message' => 'Save data succeed']);
    }
}
