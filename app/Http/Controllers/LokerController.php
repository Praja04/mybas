<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Auth;
use DB;
use Alert;
use Session;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\HistoryLokerKaryawanExport;
use App\Exports\LokerkaryawanExport;
use App\Exports\HistoryLokerKaryawanSpesifikExport;
use App\Imports\LokerBlokImport;
use App\Imports\LokerUserImport;
use App\User;
use App\Models\HR\Karyawan;

class LokerController extends Controller
{
    public function index()
    {
        $pilih_loker =      DB::table('loker_master_area')->get();
        $master_loker =     DB::table('loker_master_blok')->get();
        $master_user =      DB::table('loker_master_user')->get();
        $karyawan_baru =    DB::connection('192.168.178.44-admin')
            ->table('MSIDCARD')
            ->select('NIK', 'EMPNM', 'DEPTID')
            ->get();
        $karyawan_kabur =   DB::table('hr_karyawan_kabur')
            ->join('loker_master_user', 'loker_master_user.nik', '=', 'hr_karyawan_kabur.nik')
            ->where('hr_karyawan_kabur.tarik_kunci_loker', 'Y')
            ->orderBy('hr_karyawan_kabur.nama', 'ASC')
            ->get();

        $pb1 = [];
        $pb2 = [];
        $ps1 = [];
        $ps2 = [];
        $wb1 = [];
        $wb2 = [];
        $ws1 = [];
        $ws2 = [];
        $cek_loker_kosong = DB::table('loker_master_user')
            ->select('no_loker', 'kode_blok', 'kode_area', 'nik')
            ->where('nik', '')
            ->orWhereNull('nik')
            ->get();
        // dd($cek_loker_kosong);

        $cek_loker_terisi = DB::table('loker_master_user')
            ->select('id')
            ->whereNotNull('nik')
            ->count();

        $cek_loker_rusak = DB::table('loker_master_nomer')
            ->select('id')
            ->where('status', '=', 2)
            ->count();

        foreach ($cek_loker_kosong as $val) {

            if ($val->kode_area == "ps1") {
                $ps1[] = [
                    'no_loker' => $val->no_loker,
                    'kode_blok' => $val->kode_blok,
                ];
            }
            if ($val->kode_area == "ps2") {
                $ps2[] = [
                    'no_loker' => $val->no_loker,
                    'kode_blok' => $val->kode_blok,
                ];
            }
            if ($val->kode_area == "pb1") {
                $pb1[] = [
                    'no_loker' => $val->no_loker,
                    'kode_blok' => $val->kode_blok,
                ];
            }
            if ($val->kode_area == "pb2") {
                $pb2[] = [
                    'no_loker' => $val->no_loker,
                    'kode_blok' => $val->kode_blok,
                ];
            }
            if ($val->kode_area == "ws1") {
                $ws1[] = [
                    'no_loker' => $val->no_loker,
                    'kode_blok' => $val->kode_blok,
                ];
            }
            if ($val->kode_area == "ws2") {
                $ws2[] = [
                    'no_loker' => $val->no_loker,
                    'kode_blok' => $val->kode_blok,
                ];
            }
            if ($val->kode_area == "wb1") {
                $wb1[] = [
                    'no_loker' => $val->no_loker,
                    'kode_blok' => $val->kode_blok,
                ];
            }
            if ($val->kode_area == "wb2") {
                $wb2[] = [
                    'no_loker' => $val->no_loker,
                    'kode_blok' => $val->kode_blok,
                ];
            }
        }

        return view('loker.index', compact('pilih_loker', 'master_loker', 'karyawan_baru', 'cek_loker_kosong', 'ps1', 'ps2', 'pb1', 'pb2', 'ws1', 'ws2', 'wb1', 'wb2', 'master_user', 'karyawan_kabur', 'cek_loker_terisi', 'cek_loker_rusak'));
    }

    public function copotKaryawanPHK(Request $request)
    {
        // dd($request->data_phk);
        foreach ($request->data_phk as $data_phk) {
            // Copot dari pengguna
            DB::table('loker_master_user')
                ->where('kode_area', $data_phk['kode_area'])
                ->where('kode_blok', $data_phk['kode_blok'])
                ->where('no_loker', $data_phk['no_loker'])
                ->update([
                    'nama' => '',
                    'nik' => '',
                ]);

            // Ubah status nomor loker
            DB::table('loker_master_nomer')
                ->where('kode_area', $data_phk['kode_area'])
                ->where('kode_blok', $data_phk['kode_blok'])
                ->where('no_loker', $data_phk['no_loker'])
                ->update([
                    'status' => 0,
                ]);

            // Insert ke tabel transaksi
            $karyawan_baru = DB::table('loker_user_transaksi')->insert([
                'nik' => $data_phk['nik'],
                'no_loker' => $data_phk['no_loker'],
                'status' => 'out',
                'kode_blok' => $data_phk['kode_blok'],
                'kode_area' => $data_phk['kode_area'],
                'penghuni_sebelumnya' => $data_phk['nama'],
                'keterangan' => 'Sudah keluar',
                'alasan' => 'phk',
                'nama_pengisi' => Auth::user()->name,
                'nik_pengisi' => Auth::user()->username,
                'tgl_pengisi' => date('Y-m-d'),
                'jam_pengisi' => date('H:i:s'),
            ]);
        }

        return response()->json(['success' => '1', 'message' => 'Copot kunci berhasil'], 200);
    }

    public function dataKaryawanMasihPunyaLoker()
    {
        $data_karyawan = [];

        $hr_karyawan = Karyawan::select('nik', 'nama', 'kode_divisi', 'kode_bagian', 'kode_group', 'kode_kontrak')
            ->where('staff', 'N')
            ->where('active', 'N')
            ->orderBy('jenis_kelamin', 'desc')
            ->orderByRaw('CAST(SUBSTR(nik, 7) AS SIGNED) asc')
            ->get()->pluck('nik')->toArray();

        // List all user loker
        $user_loker = DB::table('loker_master_user')
            // ->where('kode_area', 'desc')
            ->orderBy('kode_area', 'desc')
            ->orderByRaw('SUBSTR(kode_blok,1,1) asc')
            ->orderByRaw('SUBSTR(kode_blok,2,1) asc')
            ->orderByRaw('CAST(no_loker AS SIGNED) asc')
            ->get();

        $index = 0;
        foreach ($user_loker as $karyawan) {
            if (in_array($karyawan->nik, $hr_karyawan)) {
                // Jika karyawan belum ada di list user loker
                $data_karyawan[] = $karyawan;

                $index++;
            }
        }

        return response()->json(['success' => 1, 'data' => $data_karyawan], 200);
    }

    public function storeKaryawanBelumPunyaLoker(Request $request)
    {
        $data_store_loker = $request->data_belum_punya_loker;
        $all_loker = [];
        $used_loker = [];
        $same_loker = [];
        $insert_data = [];

        foreach ($data_store_loker as $data) {
            $no_loker = $data['kode_area'] . ' - ' . $data['kode_blok'] . $data['no_loker'];
            // Check if the nomor loker alredy used
            $nomor_loker = DB::table('loker_master_nomer')
                ->where('kode_area', $data['kode_area'])
                ->where('kode_blok', $data['kode_blok'])
                ->where('no_loker', $data['no_loker'])
                ->where('status', 0)
                ->first();

            if ($nomor_loker == null) {
                $used_loker[] = $no_loker;
            }

            if (in_array($no_loker, $all_loker)) {
                $same_loker[] = $data['nik'];
            }

            $all_loker[] = $no_loker;

            $kode_area = $data['kode_area'];
            $kode_blok = $data['kode_blok'];
            $no_loker = $data['no_loker'];
            $nik = $data['nik'];
            $nama = $data['nama'];
            $jk = substr($data['kode_area'], 0, 1) == 'p' ? 'L' : 'P';
            $kode_divisi = $data['kode_divisi'];
            $kode_bagian = $data['kode_bagian'];
            $kode_group = $data['kode_group'];
            $kode_kontrak = $data['kode_kontrak'];

            $insert_data[] = [
                'nik' => $nik,
                'nama' => $nama,
                'jk' => $jk,
                'divisi' => $kode_divisi,
                'bagian' => $kode_bagian,
                'group' => $kode_group,
                'kode_area' => $kode_area,
                'kode_blok' => $kode_blok,
                'no_loker' => $no_loker,
            ];
        }

        if (count($used_loker) > 0) {
            return response()->json(['success' => 0, 'message' => 'Nomor loker berikut sudah digunakan', 'data' => implode(', ', $used_loker)], 200);
        }

        if (count($same_loker) > 0) {
            return response()->json(['success' => 0, 'message' => 'Tidak boleh menggunakan nomor loker yang sama', 'data' => implode(', ', $same_loker)], 200);
        }

        // DB::table('loker_master_user')->insert($insert_data);
        $this->storePenghuniLoker($insert_data);

        return response()->json(['success' => 1, 'message' => 'Save data succeed']);
    }

    private function storePenghuniLoker($insert_data)
    {
        foreach ($insert_data as $data) {

            $nama_karyawan = DB::table('hr_karyawan')->where('nik', $data['nik'])->first();
            $nama_penghuni = DB::table('loker_master_user')
                ->where('kode_area', $data['kode_area'])
                ->where('kode_blok', $data['kode_blok'])
                ->where('no_loker', $data['no_loker'])
                ->first();

            $karyawan_baru = DB::table('loker_user_transaksi')->insert([
                'nik' => $data['nik'],
                'no_loker' => $data['no_loker'],
                'kode_blok' => $data['kode_blok'],
                'kode_area' => $data['kode_area'],
                'status' => 'IN',
                'keterangan' => 'Insert by payroll',
                'penghuni_sebelumnya' => $nama_penghuni->nama ?? '',
                'nama_pengisi' => Auth::user()->name,
                'nik_pengisi' => Auth::user()->username,
                'tgl_pengisi' => date('Y-m-d'),
                'jam_pengisi' => date('H:i:s'),
            ]);

            DB::table('loker_master_nomer')
                ->where('no_loker', $data['no_loker'])
                ->where('kode_area', $data['kode_area'])
                ->where('kode_blok', $data['kode_blok'])
                ->update([
                    'status' => 1
                ]);

            if ($nama_penghuni != NULL) {
                $update_master = DB::table('loker_master_user')
                    ->where('kode_area', $data['kode_area'])
                    ->where('kode_blok', $data['kode_blok'])
                    ->where('no_loker', $data['no_loker'])
                    ->update([
                        'nama' => $nama_karyawan->nama,
                        'nik' => $nama_karyawan->nik,
                        'jk' => $nama_karyawan->jenis_kelamin,
                        'divisi' => $nama_karyawan->kode_divisi,
                        'bagian' => $nama_karyawan->kode_bagian,
                        'group' => $nama_karyawan->kode_group,
                        'kode_kontrak' => $nama_karyawan->kode_kontrak,
                        'kode_area' => $data['kode_area'],
                        'kode_blok' => $data['kode_blok'],
                        'no_loker' => $data['no_loker'],
                    ]);
            } else {
                DB::table('loker_master_user')
                    ->insert([
                        'nama' => $nama_karyawan->nama,
                        'nik' => $nama_karyawan->nik,
                        'jk' => $nama_karyawan->jenis_kelamin,
                        'divisi' => $nama_karyawan->kode_divisi,
                        'bagian' => $nama_karyawan->kode_bagian,
                        'group' => $nama_karyawan->kode_group,
                        'kode_kontrak' => $nama_karyawan->kode_kontrak,
                        'kode_area' => $data['kode_area'],
                        'kode_blok' => $data['kode_blok'],
                        'no_loker' => $data['no_loker'],
                    ]);
            }
        }
    }

    public function dataKaryawanBelumPunyaLoker($jenis_kelamin)
    {
        $jk = strtoupper($jenis_kelamin) == 'L' ? 'p' : 'w';

        $nik_karywana = [];
        $data_karyawan = [];

        // List all karyawan aktif
        $hr_karyawan = Karyawan::select('nik', 'nama', 'kode_divisi', 'kode_bagian', 'kode_group', 'kode_kontrak')
            ->where('jenis_kelamin', strtoupper($jenis_kelamin))
            ->where('staff', 'N')
            ->where('active', 'Y')
            ->orderByRaw('CAST(SUBSTR(nik, 7) AS SIGNED) asc')
            ->get();

        // List all user loker

        $user_loker = DB::table('loker_master_user')
            ->whereRaw("SUBSTR(kode_area,1,1) = '$jk'")
            ->get()->pluck('nik')->toArray();

        // Get nomor loker yang kosong

        $loker = DB::table('loker_master_nomer')
            ->select('kode_area', 'kode_blok', 'no_loker')
            ->where('status', 0)
            ->whereRaw("SUBSTR(kode_area,1,1) = '$jk'")
            ->orderBy('kode_area', 'desc')
            ->orderByRaw('SUBSTR(kode_blok,1,1) asc')
            ->orderByRaw('SUBSTR(kode_blok,2,1) asc')
            ->orderByRaw('CAST(no_loker AS SIGNED) asc')
            ->get();

        $index = 0;
        foreach ($hr_karyawan as $karyawan) {
            if (!in_array($karyawan->nik, $user_loker)) {
                // Jika karyawan belum ada di list user loker
                $karyawan->kode_area = $loker[$index] != null ? $loker[$index]->kode_area : '';
                $karyawan->kode_blok = $loker[$index] != null ? $loker[$index]->kode_blok : '';
                $karyawan->no_loker = $loker[$index] != null ? $loker[$index]->no_loker : '';
                $data_karyawan[] = $karyawan;

                $index++;
            }
        }

        return response()->json(['success' => 1, 'data' => $data_karyawan], 200);

        // Lets loop the diff..
    }

    public function cari_blok($kategori)
    {
        $data = DB::table('loker_master_blok')
            ->where('kode_area', $kategori)
            ->get();

        return response()->json([
            'status' => 1,
            'data' => $data
        ]);
    }

    public function cari_no_loker($kode_area, $kode_blok)
    {
        $data = DB::table('loker_master_nomer')
            ->where('kode_blok', $kode_blok)
            ->where('kode_area', $kode_area)
            ->get();

        return response()->json([
            'status' => 1,
            'data' => $data
        ]);
    }

    public function cek_penghuni_loker($no_loker, $kode_area, $kode_blok)
    {
        $data['user'] = DB::table('loker_master_user')
            ->where('kode_blok', $kode_blok)
            ->where('kode_area', $kode_area)
            ->where('no_loker', $no_loker)
            ->first();

        $data['status'] = DB::table('loker_master_nomer')
            ->where('kode_blok', $kode_blok)
            ->where('kode_area', $kode_area)
            ->where('no_loker', $no_loker)
            ->first();

        return response()->json([
            'status' => 1,
            'data' => $data
        ]);
    }

    public function get_foto_user($nik)
    {
        $user = DB::connection('192.168.178.44-admin')
            ->table('MSIDCARD')
            ->select('NIK', 'FOTOBLOB')
            ->whereRaw("CAST(BARCODE AS SIGNED) = $nik")
            ->first();
        $detail_user = DB::table('loker_master_user')->where('nik', $nik)->first();

        $data['image']          = base64_encode($user->FOTOBLOB);
        $data['nama']           = $detail_user->nama;
        $data['nik']            = $detail_user->nik;
        $data['divisi']         = $detail_user->divisi;
        // $data['bagian']         = $detail_user->bagian;
        // $data['kode_kontrak']   = $detail_user->kode_kontrak;
        $data['kode_blok']      = $detail_user->kode_blok;
        $data['no_loker']       = $detail_user->no_loker;

        return response()->json([
            'status' => 1,
            'data' => $data
        ]);
    }

    public function post_user_loker(Request $request)
    {
        $nik       = $request->nik_karyawan_baru;
        $kode_area = $request->kode_area;
        $kode_blok = $request->kode_blok;
        $no_loker  = $request->no_loker;

        $nama_karyawan = DB::connection('192.168.178.44-admin')->table('MSIDCARD')->select('EMPNM', 'NIK', 'DEPTID')->where('NIK', $nik)->first();
        $nama_penghuni = DB::table('loker_master_user')
            ->where('kode_area', $kode_area)
            ->where('kode_blok', $kode_blok)
            ->where('no_loker', $no_loker)
            ->first();

        $karyawan_baru = DB::table('loker_user_transaksi')->insert([
            'nik' => $nik,
            'no_loker' => $request->no_loker,
            'kode_blok' => $request->kode_blok,
            'kode_area' => $request->kode_area,
            'status' => 'IN',
            'keterangan' => $request->keterangan,
            'penghuni_sebelumnya' => $nama_penghuni->nama ?? '',
            'nama_pengisi' => Auth::user()->name,
            'nik_pengisi' => Auth::user()->username,
            'tgl_pengisi' => date('Y-m-d'),
            'jam_pengisi' => date('H:i:s'),
        ]);

        DB::table('loker_master_nomer')
            ->where('no_loker', $no_loker)
            ->where('kode_area', $kode_area)
            ->where('kode_blok', $kode_blok)
            ->update([
                'status' => 1
            ]);

        if ($nama_penghuni->nama != NULL) {
            $update_master = DB::table('loker_master_user')
                ->where('kode_area', $kode_area)
                ->where('kode_blok', $kode_blok)
                ->where('no_loker', $no_loker)
                ->update([
                    'nama' => $nama_karyawan->ENPNM,
                    'nik' => $nama_karyawan->NIK,
                    'divisi' => $nama_karyawan->DEPTID,
                    'kode_area' => $kode_area,
                    'kode_blok' => $kode_blok,
                    'no_loker' => $no_loker,
                ]);
        } else {
            DB::table('loker_master_user')
                ->where('kode_area', $kode_area)
                ->where('kode_blok', $kode_blok)
                ->where('no_loker', $no_loker)
                ->update([
                    'nama' => $nama_karyawan->EMPNM,
                    'nik' => $nama_karyawan->NIK,
                    'divisi' => $nama_karyawan->DEPTID,
                ]);
        }

        Session::flash('info', $nama_karyawan->EMPNM . ' ' . 'Berhasil Di Masukan Kedalam Loker' . ' ' . $no_loker);
        return back();
    }
    public function tarik_kunci($no_loker, $nik, $keterangan, $kode_blok, $kode_area)
    {
        $nama_penghuni = DB::table('loker_master_user')
            ->where('nik', $nik)
            ->first();

        $no_loker = DB::table('loker_master_nomer')
            ->where('no_loker', $no_loker)
            ->where('kode_blok', $kode_blok)
            ->where('kode_area', $kode_area)
            ->first();

        $karyawan_baru = DB::table('loker_user_transaksi')->insert([
            'nik' => $nik,
            'no_loker' => $no_loker->no_loker,
            'status' => 'out',
            'kode_blok' => $no_loker->kode_blok,
            'kode_area' => $no_loker->kode_area,
            'penghuni_sebelumnya' => $nama_penghuni->nama ?? '',
            'keterangan' => $keterangan,
            'alasan' => $keterangan,
            'nama_pengisi' => Auth::user()->name,
            'nik_pengisi' => Auth::user()->username,
            'tgl_pengisi' => date('Y-m-d'),
            'jam_pengisi' => date('H:i:s'),
        ]);

        $update = DB::table('loker_master_nomer')
            ->where('kode_area', $no_loker->kode_area)
            ->where('kode_blok', $no_loker->kode_blok)
            ->where('no_loker', $no_loker->no_loker)
            ->update([
                'status' => 0,
            ]);

        $update_user = DB::table('loker_master_user')
            ->where('kode_area', $no_loker->kode_area)
            ->where('kode_blok', $no_loker->kode_blok)
            ->where('no_loker', $no_loker->no_loker)
            ->update([
                'nama' => null,
                'nik' => null,
            ]);

        $update = DB::table('hr_karyawan_kabur')
            ->where('nik', $nik)
            ->where('tarik_kunci_loker', '=', 'Y')
            ->update([
                'tarik_kunci_loker' => 'N',
            ]);

        return response()->json([
            'status' => 1,
        ]);
    }
    public function tarik_kunci_manual($no_loker, $kode_blok, $kode_area, $nik, $keterangan)
    {
        $nama_penghuni = DB::table('loker_master_user')
            ->where('nik', $nik)
            ->first();

        $no_loker = DB::table('loker_master_nomer')
            ->where('no_loker', $no_loker)
            ->where('kode_blok', $kode_blok)
            ->where('kode_area', $kode_area)
            ->first();

        $karyawan_baru = DB::table('loker_user_transaksi')->insert([
            'nik' => $nik,
            'no_loker' => $no_loker->no_loker,
            'status' => 'out',
            'kode_blok' => $no_loker->kode_blok,
            'kode_area' => $no_loker->kode_area,
            'penghuni_sebelumnya' => $nama_penghuni->nama ?? '',
            'keterangan' => $keterangan,
            'alasan' => $keterangan,
            'nama_pengisi' => Auth::user()->name,
            'nik_pengisi' => Auth::user()->username,
            'tgl_pengisi' => date('Y-m-d'),
            'jam_pengisi' => date('H:i:s'),
        ]);

        $update = DB::table('loker_master_nomer')
            ->where('kode_area', $no_loker->kode_area)
            ->where('kode_blok', $no_loker->kode_blok)
            ->where('no_loker', $no_loker->no_loker)
            ->update([
                'status' => 0,
            ]);

        $update_user = DB::table('loker_master_user')
            ->where('kode_area', $no_loker->kode_area)
            ->where('kode_blok', $no_loker->kode_blok)
            ->where('no_loker', $no_loker->no_loker)
            ->update([
                'nama' => '',
                'nik' => '',
            ]);

        return response()->json([
            'status' => 1,
        ]);
    }

    public function history_loker($kategori)
    {
        $kategori = $kategori;

        $master_loker = DB::table('loker_master_nomer')
            ->select('no_loker')
            ->orderBy('id', 'ASC')
            ->where('kode_area', $kategori)->get();

        return view('loker.history_loker', compact('kategori', 'master_loker'));
    }

    public function pencarian_history_loker(Request $request)
    {
        $loker = $request->loker;
        $kategori = $request->kategori;
        $tgl_mulai = $request->tgl_mulai;
        $tgl_selesai = $request->tgl_selesai;
        // dd($request->all());

        $result = DB::table('loker_master_nomer')
            ->join('loker_user_transaksi', 'loker_user_transaksi.no_loker', '=', 'loker_master_nomer.no_loker')
            ->join('loker_master_user', 'loker_master_user.kode_area', '=', 'loker_master_nomer.kode_area')
            ->where('loker_user_transaksi.kode_area', $kategori)
            ->where('loker_user_transaksi.no_loker', $loker)
            ->groupBy('loker_user_transaksi.jam_pengisi')
            ->whereBetween('loker_user_transaksi.tgl_pengisi', [$tgl_mulai, $tgl_selesai])
            ->get();

        $master_loker = DB::table('loker_master_nomer')
            ->orderBy('id', 'ASC')
            ->select('no_loker')
            ->where('kode_area', $kategori)
            ->get();

        return view('loker.history_loker_result', compact('result', 'kategori', 'master_loker', 'loker', 'tgl_mulai', 'tgl_selesai'));
    }

    public function database($kategori)
    {
        $kategori = $kategori;

        $master_select = DB::table('loker_master_blok')
            ->select('loker_master_blok.kode_blok')
            ->where('kode_area', $kategori)
            ->get();

        $master_blok = DB::table('loker_master_nomer')
            ->where('kode_area', $kategori)
            ->orderBy('id', 'ASC')
            ->get();

        $blok = DB::table('loker_master_blok')
            ->where('kode_area', $kategori)
            ->orderBy('id', 'ASC')
            ->get();

        return view('loker.database', compact('kategori', 'master_blok', 'master_select', 'blok'));
    }

    public function post_master_loker(Request $request)
    {

        if ($request->post == 'post') {
            $kode_blok     = $request->kode_blok;
            $kode_blok_add = $request->kode_blok_add;
            $mulai_dari = (int)$request->mulai_dari + 1;
            $sampai = (int)$request->sampai;
            $mulai_dari_add = (int)$request->mulai_dari_add;
            $sampai_add = (int)$request->sampai_add;

            if ($kode_blok != NULL and $kode_blok_add == NULL) {

                for ($i = $mulai_dari; $i <= $sampai; $i++) {
                    $inser_master_blok = DB::table('loker_master_nomer')
                        ->insert([
                            'kode_blok' => $kode_blok,
                            'kode_area' => $request->kode_area,
                            'no_loker' =>   $i,
                        ]);

                    $ambil_dulu[] = DB::table('loker_master_nomer')
                        ->where('kode_blok', $kode_blok)
                        ->where('kode_area', $request->kode_area)
                        ->where('no_loker', $i)
                        ->get();
                }
                for ($k = 0; $k <= count($ambil_dulu); $k++) {
                    $cek_user[] = DB::table('loker_master_user')
                        ->where('kode_blok', $ambil_dulu[$k][0]->kode_blok ?? '')
                        ->where('kode_area', $ambil_dulu[$k][0]->kode_area ?? '')
                        ->where('no_loker', $ambil_dulu[$k][0]->no_loker ?? '')
                        ->get();
                }
                for ($o = 0; $o <= count($cek_user); $o++) {
                    $update[] = DB::table('loker_master_nomer')
                        ->where('kode_blok', $cek_user[$o][0]->kode_blok ?? '')
                        ->where('kode_area', $cek_user[$o][0]->kode_area ?? '')
                        ->where('no_loker', $cek_user[$o][0]->no_loker ?? '')
                        ->update([
                            'status' => 1,
                        ]);
                }
            } else {
                $validasi = DB::table('loker_master_blok')->where('kode_area', $request->kode_area)->where('kode_blok', $kode_blok_add)->first();
                if ($validasi == null) {
                    $inser_master_blok = DB::table('loker_master_blok')
                        ->insert([
                            'kode_blok' =>  $kode_blok_add,
                            'kode_area' => $request->kode_area,
                        ]);

                    for ($j = $mulai_dari_add; $j <= $sampai_add; $j++) {
                        $inser_master_nomer = DB::table('loker_master_nomer')
                            ->insert([
                                'kode_blok' => $kode_blok_add,
                                'kode_area' => $request->kode_area,
                                'no_loker' =>   $j,
                            ]);

                        $ambil_dulu[] = DB::table('loker_master_nomer')
                            ->where('kode_blok', $kode_blok_add)
                            ->where('kode_area', $request->kode_area)
                            ->where('no_loker', $j)
                            ->get();
                    }
                    for ($k = 0; $k <= count($ambil_dulu); $k++) {
                        $cek_user[] = DB::table('loker_master_user')
                            ->where('kode_blok', $ambil_dulu[$k][0]->kode_blok ?? '')
                            ->where('kode_area', $ambil_dulu[$k][0]->kode_area ?? '')
                            ->where('no_loker', $ambil_dulu[$k][0]->no_loker ?? '')
                            ->get();
                    }
                    for ($o = 0; $o <= count($cek_user); $o++) {
                        $update[] = DB::table('loker_master_nomer')
                            ->where('kode_blok', $cek_user[$o][0]->kode_blok ?? '')
                            ->where('kode_area', $cek_user[$o][0]->kode_area ?? '')
                            ->where('no_loker', $cek_user[$o][0]->no_loker ?? '')
                            ->update([
                                'status' => 1,
                            ]);
                    }
                } else {
                    Session::flash('error', 'Gagal Menyimpan, Data Blok Sudah Ada..');
                    return back();
                }
                Session::flash('info', 'Data Loker Berhasil Di Simpan');
                return back();
            }
            Session::flash('info', 'Data Loker Berhasil Di Simpan');
            return back();
        } elseif ($request->hapus == 'hapus') {
            $id = $request->id;
            $ambil = [];
            $jika_ada = [];
            $hapus_master_loker = [];
            for ($i = 0; $i < count($id); $i++) {
                //ambil dulu///
                $ambil[] = DB::table('loker_master_nomer')
                    ->where('id', $id[$i])
                    ->get();

                //cek jika ada user//
                $jika_ada[] = DB::table('loker_master_user')
                    ->where('no_loker', $ambil[$i][0]->no_loker)
                    ->where('kode_blok', $ambil[$i][0]->kode_blok)
                    ->where('kode_area', $ambil[$i][0]->kode_area)
                    ->delete();

                $hapus_master_loker[] = DB::table('loker_master_nomer')
                    ->where('id', $id[$i])
                    ->delete();
            }
            // dd($jika_ada);

            Session::flash('info', 'Data Loker Berhasil Di Hapus');
            return back();
        }
    }

    public function hapus_master_blok($id)
    {
        DB::table('loker_master_blok')->where('id', $id)->delete();
        Session::flash('info', 'Data Blok Loker Berhasil Di Hapus');
        return back();
    }

    public function history_loker_karyawan($nik)
    {
        $data = DB::table('loker_user_transaksi')->where('nik', $nik)->orderBy('id', 'ASC')->get();

        return response()->json([
            'status' => 1,
            'data' => $data
        ]);
    }

    public function last_number_loker($kode_area, $kode_blok)
    {
        $ada = DB::table('loker_master_nomer')
            ->where('kode_area', $kode_area)
            ->where('kode_blok', $kode_blok)
            ->orderBy('id', 'DESC')
            ->first();
        $data = substr($ada->no_loker, -2);

        return response()->json([
            'status' => 1,
            'data' => $data
        ]);
    }

    public function export_history_karyawan($nik)
    {
        return Excel::download(new HistoryLokerKaryawanExport($nik), 'Report-Loker-Karyawan' . '-' . '.xls');
    }

    public function export_loker_spesifik($kode_area, $tgl_mulai, $tgl_selesai)
    {
        return Excel::download(new HistoryLokerKaryawanSpesifikExport($kode_area, $tgl_mulai, $tgl_selesai), 'Report-Loker-Karyawan-By-Area' . '-' . '.xls');
    }

    public function import_blok_loker(Request $request)
    {
        $this->validate($request, [
            'file' => 'required|mimes:csv,xls,xlsx'
        ]);

        $excel = $request->file('file');
        $data = Excel::import(new LokerBlokImport, $excel);

        Session::flash('info', 'Data Blok Berhasil Di Import..');
        return back();
    }

    public function import_loker_user(Request $request)
    {
        $this->validate($request, [
            'file' => 'required|mimes:csv,xls,xlsx'
        ]);

        $excel = $request->file('file');
        $data = Excel::import(new LokerUserImport, $excel);

        return back();
    }

    public function tandai_rusak($kode_blok, $kode_area, $no_loker)
    {
        DB::table('loker_master_nomer')
            ->where('kode_blok', $kode_blok)
            ->where('kode_area', $kode_area)
            ->where('no_loker', $no_loker)
            ->update([
                'status' => 2,
            ]);
        Session::flash('info', 'Loker Berhasil Di Tandai Rusak..');
        return back();
    }

    public function sudah_benar($kode_blok, $kode_area, $no_loker)
    {
        DB::table('loker_master_nomer')
            ->where('kode_blok', $kode_blok)
            ->where('kode_area', $kode_area)
            ->where('no_loker', $no_loker)
            ->update([
                'status' => 0,
            ]);

        Session::flash('info', 'Loker Berhasil Di Update...');
        return back();
    }

    public function edit_loker($kode_area)
    {
        $kategori = $kode_area;
        $data = DB::table('loker_master_nomer')->where('kode_area', $kode_area)->get();
        $master_blok = DB::table('loker_master_nomer')->select('kode_blok')->where('kode_area', $kode_area)->groupBy('kode_blok')->orderBy('kode_blok', 'ASC')->get();

        return view('loker.edit_loker', compact('data', 'kategori', 'master_blok'));
    }

    public function export_excel($kode_area)
    {
        return Excel::download(new LokerkaryawanExport($kode_area), 'Report-Loker.' . $kode_area . '-' . '.xls');
    }
}
