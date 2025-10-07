<?php

namespace App\Http\Controllers;

use App\PKWAnakAnak;
use App\PKWKaryawan;
use App\PKWPengalamanKerja;
use App\PKWSaudaraKandung;
use App\PKWDivisi;
use App\PKWGroup;
use App\PKWAdmin;
use App\PKW;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class PKWController extends Controller
{
    private $search;
    private $jenis;

    public function formDataKaryawan()
    {
        return view('pkw.form-data-karyawan');
    }

    public function formatTanggal($date)
    {
        $year = explode('-', $date)[0];
        $month = explode('-', $date)[1];
        $date = explode('-', $date)[2];
        return $date . '/' . $month . '/' . $year;
    }

    public function getAll($jenis, Request $request)
    {

        $data = [];
        $where = [];
        if ($jenis == 'belum-terdaftar') {
            // Ini untuk mengambil data karyawan yang belum terdaftar pkw
            $karyawan = PKWKaryawan::where('status', 'pkw')->get();
        } else {
            $this->jenis = $jenis;
            $status = $this->jenis == 'pkwt' ? 'kontrak' : 'probation';
            $karyawan = PKWKaryawan::where('status', $status)->whereHas('pkw', function (Builder $query) {
                $query->where('jenis', $this->jenis);
            })->orderByRaw('SUBSTR(nik, 8) asc')->get();
        }
        // dd($karyawan);
        // return $jenis_kelamin;
        foreach ($karyawan as $k) {
            $id = $k->pkw->id ?? $k->id;
            $kesimpulan = '-';
            if ($k->pkw != null) {
                if ($k->pkw->form_pa != null) {
                    if ($k->pkw->form_pa->status == 'done') {
                        $kesimpulan = $k->pkw->form_pa->kesimpulan ?? '-';
                    } else {
                        $kesimpulan = '-';
                    }
                }
            }

            $data[] = [
                'id' => $id,
                'nik' => $k->nik,
                'nama' => $k->nama,
                'jenis_kelamin' => $k->jenis_kelamin,
                'divisi' => $k->divisi->nama_divisi ?? '-',
                'bagian' => $k->bagian->nama_bagian ?? '-',
                'jenis' => $k->pkw == null ? '-' : $k->pkw->jenis,
                'kontrak_ke' => $k->pkw == null ? '-' : $k->pkw->kontrak_ke,
                'tanggal_mulai' => $k->pkw == null ? '-' : $this->formatTanggal($k->pkw->tanggal_mulai),
                'pure_tanggal_mulai' => $k->pkw == null ? '-' : $k->pkw->tanggal_mulai,
                'tanggal_selesai' => $k->pkw == null ? '-' : $this->formatTanggal($k->pkw->tanggal_selesai),
                'pure_tanggal_selesai' => $k->pkw == null ? '-' : $k->pkw->tanggal_selesai,
                'form_pa' => $k->pkw->form_pa->status ?? '-',
                'kesimpulan' => $kesimpulan,
            ];
        }
        $response = [
            'meta' => [
                'page' => 1,
                'pages' => 1,
                'perpage' => 10,
                'total' => count($karyawan),
                'field' => 'id'
            ],
            'data' => $data
        ];
        return response()->json(['success' => 1, 'data' => $response]);
    }

    //    public function belum_terdaftar()
    //    {
    //        $divisis = PKWDivisi::all();
    //        $groups = PKWGroup::all();
    //        $admins = PKWAdmin::all();
    //        return view('pkw.pkw-belum-terdaftar', [
    //            'divisis' => $divisis,
    //            'groups' => $groups,
    //            'admins' => $admins
    //        ]);
    //    }

    public function pkwt()
    {
        $divisis = PKWDivisi::all();
        $groups = PKWGroup::all();
        $admins = PKWAdmin::all();
        return view('pkw.pkwt', [
            'divisis' => $divisis,
            'groups' => $groups,
            'admins' => $admins
        ]);
    }

    public function pkwtt()
    {
        $divisis = PKWDivisi::all();
        $groups = PKWGroup::all();
        $admins = PKWAdmin::all();
        return view('pkw.pkwtt', [
            'divisis' => $divisis,
            'groups' => $groups,
            'admins' => $admins
        ]);
    }

    public function storeDataKaryawan(Request $request)
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

    public function start(Request $request)
    {

        $id_divisi = $request->divisi;
        $id_bagian = $request->bagian;
        $id_jabatan = $request->jabatan;

        $jenis = $request->jenis;
        $periode = $request->periode;
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        foreach ($request->id as $id) {
            // Get last kontrak
            $pkw = PKW::where('id_karyawan', $id)->orderBy('kontrak_ke', 'desc')->first();
            if ($pkw == null) {
                $kontrak_ke = 1;
            } else {
                $kontrak_ke = (int)$pkw->kontrak_ke + 1;
            }

            // Ambil pkw terakhir untuk mendapatkan nomor perjanjian terakhir

            $last_pkw = PKW::orderBy('created_at', 'desc')->first();
            if ($last_pkw == null) {
                $no_perjanjian = 0;
            } else {
                $no_perjanjian = (int)$last_pkw->no_perjanjian + 1;
            }

            $pkw = new PKW;
            $pkw->id_karyawan = $id;
            $pkw->jenis = $jenis;
            $pkw->kontrak_ke = $kontrak_ke;
            $pkw->periode = $periode;
            $pkw->no_perjanjian = $no_perjanjian;
            $pkw->tanggal_mulai = $start_date;
            $pkw->tanggal_selesai = $end_date;
            $pkw->save();

            // Set status karyawan
            $karyawan = PKWKaryawan::find($id);
            $karyawan->status = $jenis == 'pkwt' ? 'kontrak' : 'probation';
            $karyawan->probation_confirm_time = date('Y-m-d H:i:s');
            $karyawan->id_divisi = $id_divisi;
            $karyawan->id_bagian = $id_bagian;
            $karyawan->id_jabatan = $id_jabatan;
            $karyawan->tanggal_masuk = $start_date;
            $karyawan->recruitment_confirm_time = date('Y-m-d H:i:s');
            $karyawan->save();
        }
        return response()->json(['success' => 1, 'message' => $jenis . ' start succeed']);
    }
}
