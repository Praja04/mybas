<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\Klinik\Obat;
use App\Models\Klinik\Diagnosa;
use App\Department;
use App\Models\Klinik\Faskes;
use App\Models\Klinik\Pemeriksaan;
use App\Models\Klinik\PemeriksaanObat;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;
use App\imports\KlinikImport;
use Validator;
use Session;
use Carbon\Carbon;


class KlinikController extends Controller
{
    public function dokter()
    {
        $obat = Obat::where('active', 'Y')->get();
        $diagnosa = Diagnosa::where('active', 'Y')->orderBy('nama_diagnosa', 'asc')->get();
        $faskes = Faskes::all();

        $user = auth()->user();
        $userDepartment = $user->department;

        return view('klinik.dokter.index', [
            'obat' => $obat,
            'diagnosa' => $diagnosa,
            'faskes' => $faskes,
            'departments' => [$userDepartment],
        ]);
    }

    // // Get data dari secure access berdasarkan nomor kartu
    // $user = DB::connection('192.168.178.44-admin')
    // ->table('MSIDCARD')
    // ->select('NIK')
    // // ->whereNotNull('FOTOTYPE')
    // ->where(['CARDNODEVICE' => $rfid])
    // // ->orderBy('CREATEDON', 'desc')
    // ->orderByRaw('CAST(SUBSTR(NIK, 8) AS SIGNED) desc')
    // ->first();

    public function rekamMedisPage()
    {
        $nik = isset($_GET['nik']) ? $_GET['nik'] : 'asasds';
        if ($nik == '') {
            $nik = 'asdasd';
        }

        $data = DB::connection('192.168.178.44-admin')
            ->table('MSIDCARD')
            ->select('NIK', 'EMPNM', 'DEPTID')
            ->where('NIK', $nik)
            // ->with('rekamMedis', 'rekamMedis.data_diagnosa')
            ->first();
        if ($data != null) {
            $data->rekamMedis = Pemeriksaan::where('nik', $nik)->get();
        }


        // dd($data);

        return view('klinik.rekam-medis', compact('data'));
    }

    public function laporanObat()
    {
        $obat = Obat::where('active', 'Y')->get();
        return view('klinik.laporan-obat', ['obat' => $obat]);
    }

    public function MasterDataObat()
    {
        $obat = Obat::where('active', 'Y')->get();
        return view('klinik.master.master-data-obat', ['obat' => $obat]);
    }

    public function Delete($id)
    {

        $master =  DB::table('klinik_obat')->where('id', $id)->update([
            'active' => 'N'
        ]);
        Session::flash('info', 'Data Anda Berhasil Dihapus');
        return back();
    }

    public function Edit($id)
    {
        $master =  DB::table('klinik_obat')->where('id', $id)->first();
        return view('klinik.master.edit', compact('master'));
    }


    public function Update(Request $request)
    {
        $nama_obat = $request->nama_obat;
        $harga = $request->harga;
        $satuan = $request->satuan;

        DB::table('klinik_obat')->where('id', $request->id)->update([
            'nama_obat' => $nama_obat,
            'harga' => $harga,
            'satuan' => $satuan

        ]);
        Session::flash('info', 'Data Anda Berhasil Diedit');
        return back();
    }
    public function import_excel(Request $request)
    {

        // validasi
        $validasi_file = Validator::make($request->all(), [
            'excel' => 'required|mimes:xls,xlsx',
        ]);
        if ($validasi_file->fails()) {
            Session::flash('error', 'Format File Tidak Sesuai !');
            return back();
        }
        // import data
        Excel::import(new KlinikImport, $request->file('excel'));

        // notifikasi dengan session
        Session::flash('info', 'Data Anda Berhasi Diimport!');

        // alihkan halaman kembali
        return back();
    }

    public function doValidate(Request $request)
    {
        $request->validate([
            'id' => 'required'
        ]);

        $pemeriksaan = Pemeriksaan::find($request->id);
        $pemeriksaan->tanggal_validasi = date('Y-m-d');
        $pemeriksaan->validasi_oleh = Auth::user()->name;
        $pemeriksaan->save();

        return response(['success' => 1, 'message' => 'Berhasil validasi']);
    }


    public function laporanPemeriksaan()
    {
        // $obat = Obat::where('active', 'Y')->get();

        $where = [];

        if (isset($_GET['filter_start_date'])) {
            $where[] = ['klinik_pemeriksaan.tanggal_pemeriksaan', '>=', $_GET['filter_start_date']];
        } else {
            $where[] = ['klinik_pemeriksaan.tanggal_pemeriksaan', '>=', date('Y-m-d', strtotime('-7 days'))];
        }

        if (isset($_GET['filter_end_date'])) {
            $where[] = ['klinik_pemeriksaan.tanggal_pemeriksaan', '<=', $_GET['filter_end_date']];
        } else {
            $where[] = ['klinik_pemeriksaan.tanggal_pemeriksaan', '<=', date('Y-m-d')];
        }

        $permintaan_obat = DB::table('klinik_pemeriksaan')
            ->select(DB::raw('klinik_diagnosa.nama_diagnosa as nama_diagnosa'), DB::raw('COUNT(*) as count'))
            ->join('klinik_diagnosa', 'klinik_pemeriksaan.diagnosa', '=', 'klinik_diagnosa.id')
            ->where($where)
            ->where('jenis_pemeriksaan', 'permintaan_obat')
            ->groupBy('klinik_pemeriksaan.diagnosa')
            ->get();

        $permintaan_obat_chart = [];

        foreach ($permintaan_obat as $p) {
            $permintaan_obat_chart[] = [
                'name' => $p->nama_diagnosa,
                'y' => $p->count
            ];
        }

        $skd = DB::table('klinik_pemeriksaan')
            ->select(DB::raw('klinik_diagnosa.nama_diagnosa as nama_diagnosa'), DB::raw('COUNT(*) as count'))
            ->join('klinik_diagnosa', 'klinik_pemeriksaan.diagnosa', '=', 'klinik_diagnosa.id')
            ->where($where)
            ->where('jenis_pemeriksaan', 'skd')
            ->groupBy('klinik_pemeriksaan.diagnosa')
            ->get();

        $skd_chart = [];

        foreach ($skd as $p) {
            $skd_chart[] = [
                'name' => $p->nama_diagnosa,
                'y' => $p->count
            ];
        }

        $faskes = DB::table('klinik_pemeriksaan')
            ->select(DB::raw('klinik_faskes.nama_faskes as nama_faskes'), DB::raw('COUNT(*) as count'))
            ->leftJoin('klinik_faskes', 'klinik_pemeriksaan.kode_faskes', '=', 'klinik_faskes.kode_faskes')
            ->where($where)
            ->where('jenis_pemeriksaan', 'skd')
            ->groupBy('klinik_pemeriksaan.kode_faskes')
            ->get();

        $faskes_chart = [];

        foreach ($faskes as $f) {
            $faskes_chart[] = [
                'name' => $f->nama_faskes,
                'y' => $f->count
            ];
        }

        // buat alias table untuk memisahkan id, keterangan, dan tindakan yang diambil dari klinik_pemeriksaan
        $permintaan_obat_raw = DB::table('klinik_pemeriksaan')
            ->select('klinik_pemeriksaan.id as kp_id', 'klinik_pemeriksaan.tindakan as kp_tindakan', 'klinik_pemeriksaan.keterangan as kp_keterangan', 'klinik_pemeriksaan.nik', 'klinik_pemeriksaan.nama', 'klinik_pemeriksaan.bagian', 'klinik_pemeriksaan.tanggal_pemeriksaan', 'klinik_pemeriksaan.waktu_pemeriksaan', 'klinik_pemeriksaan.dokter', 'klinik_pemeriksaan.keluhan', 'klinik_pemeriksaan.komorbid', 'klinik_diagnosa.nama_diagnosa')
            ->join('klinik_diagnosa', 'klinik_pemeriksaan.diagnosa', '=', 'klinik_diagnosa.id')
            ->where($where)
            ->where('jenis_pemeriksaan', 'permintaan_obat')
            ->get();

        // dd($permintaan_obat_raw);

        $skd_raw = DB::table('klinik_pemeriksaan')
            ->join('klinik_diagnosa', 'klinik_pemeriksaan.diagnosa', '=', 'klinik_diagnosa.id')
            ->leftJoin('klinik_faskes', 'klinik_pemeriksaan.kode_faskes', '=', 'klinik_faskes.kode_faskes')
            ->where($where)
            ->where('jenis_pemeriksaan', 'skd')
            ->get();

        $tindakan_keterangan = DB::table('klinik_pemeriksaan')
            ->select('id', 'tindakan', 'keterangan')
            ->where($where)
            ->get();

        return view('klinik.laporan-pemeriksaan', [
            'permintaan_obat_chart' => json_encode($permintaan_obat_chart),
            'skd_chart' => json_encode($skd_chart),
            'faskes_chart' => json_encode($faskes_chart),
            'permintaan_obat_raw' => $permintaan_obat_raw,
            'skd_raw' => $skd_raw,
            'tindakan_keterangan' => $tindakan_keterangan
        ]);
    }


    public function updateTindakanKeterangan(Request $request)
    {
        $id = $request->input('id');
        $tindakan = $request->input('tindakan');
        $keterangan = $request->input('keterangan');

        // dd($id . '-' . $tindakan . '-' . $keterangan);

        $request->validate([
            'tindakan' => 'required',
            'keterangan' => 'required',
        ]);

        $updated = DB::table('klinik_pemeriksaan')
            ->where('id', $id)
            ->update([
                'tindakan' => $tindakan,
                'keterangan' => $keterangan
            ]);

        if ($updated) {
            $request->session()->flash('success', 'Data tindakan dan keterangan berhasil diubah.');
        } else {
            $request->session()->flash('error', 'Gagal mengubah data tindakan dan keterangan.');
        }

        return redirect()->back();
    }


    public function getRekamMedis($nik)
    {
        $pemeriksaan = Pemeriksaan::where('nik', $nik)->with('data_diagnosa')->with('obat')->orderBy('created_at', 'desc')->get();
        return response()->json(['success' => 1, 'data' => $pemeriksaan]);
    }

    public function save(Request $request)
    {
        // dd($request->all());

        $request->validate([
            'keluhan' => 'required',
            'diagnosa' => 'required',
            'suhu' => 'required',
            'dokter' => 'required',
            'tindakan' => 'required',
            'keterangan' => 'required',
            'komorbid' => 'required',
            'jenis_pemeriksaan' => 'required',
        ]);
        // if ($request->jenis_pemeriksaan != 'permintaan_obat') {

        //     $request->validate([
        //         'fileskd' => 'required|mimes:jpg, png, jpeg',
        //     ]);
        //     $file = $request->file('fileskd');
        //     $filename = date('YmdHi') . $file->getClientOriginalName();
        //     $file->move(public_path('bukti_skd'), $filename);
        // }




        $pemeriksaan = new Pemeriksaan;
        $pemeriksaan->tanggal_pemeriksaan   = date('Y-m-d');
        $pemeriksaan->waktu_pemeriksaan     = date('H:i:s');
        $pemeriksaan->pic                   = Auth::user()->name;
        $pemeriksaan->keluhan               = $request->keluhan;
        $pemeriksaan->diagnosa              = $request->diagnosa;
        $pemeriksaan->suhu                  = $request->suhu;
        $pemeriksaan->tensi                 = $request->tensi;
        $pemeriksaan->nik                   = $request->nik;
        $pemeriksaan->dokter                = $request->dokter;
        $pemeriksaan->tindakan              = $request->tindakan;
        $pemeriksaan->keterangan            = $request->keterangan;
        $pemeriksaan->jenis_pemeriksaan     = $request->jenis_pemeriksaan;
        $pemeriksaan->komorbid              = $request->komorbid;
        $pemeriksaan->tanggal_skd_mulai     = $request->tanggal_skd_mulai;
        $pemeriksaan->tanggal_skd_selesai   = $request->tanggal_skd_selesai;
        $pemeriksaan->kode_faskes           = $request->faskes;
        $pemeriksaan->nama                  = $request->nama;
        $pemeriksaan->bagian                = $request->bagian;
        if ($request->jenis_pemeriksaan != 'permintaan_obat') {
            // $pemeriksaan->bukti_skd             = $filename;
            $pemeriksaan->bukti_skd             = "-";
        }
        $pemeriksaan->save();

        $id_pemeriksaan = $pemeriksaan->id;
        if (count(json_decode($request->obat)) == 0) {
            $request->session()->flash('success', 'Save data succeed!');
            return response()->json(['success' => 1, 'message' => 'Save data succeed']);
        }
        foreach (json_decode($request->obat) as $obat) {
            $pemeriksaan_obat                   = new PemeriksaanObat;
            $pemeriksaan_obat->id_pemeriksaan   = $id_pemeriksaan;
            $pemeriksaan_obat->id_obat          = $obat->id;
            $pemeriksaan_obat->quantity         = $obat->sudah_dibuka == 0 ? $obat->qty : 0;
            $pemeriksaan_obat->harga            = $obat->sudah_dibuka == 0 ? $obat->harga : 0;
            $pemeriksaan_obat->sudah_dibuka     = $obat->sudah_dibuka == 0 ? 'N' : 'Y';
            $pemeriksaan_obat->save();
        }

        $request->session()->flash('success', 'Save data succeed!');

        return response()->json(['success' => 1, 'message' => 'Save data succeed']);
    }

    public function scan(Request $request)
    {
        $data = [];

        if (count(explode('-', $request->rfid)) == 2) {
            // Ini berarti inputan nya nik
            $user = DB::connection('192.168.178.44-admin')
                ->table('MSIDCARD')
                ->select('NIK')
                // ->whereNotNull('FOTOTYPE')
                ->where(['NIK' => $request->rfid])
                // ->orderBy('CREATEDON', 'desc')
                ->where('STATUS', 'X')
                ->orderByRaw('CAST(EMPCARDID AS SIGNED) desc')
                ->first();
        } else {
            // Ini berarti inputan nya scan kartu
            $rfid = (int)$request->rfid;

            // Get data dari secure access berdasarkan nomor kartu
            $user = DB::connection('192.168.178.44-admin')
                ->table('MSIDCARD')
                ->select('NIK')
                // ->whereNotNull('FOTOTYPE')
                ->where(['CARDNODEVICE' => $rfid])
                // ->orderBy('CREATEDON', 'desc')
                ->where('STATUS', 'X')
                ->orderByRaw('CAST(EMPCARDID AS SIGNED) desc')
                ->first();
        }

        // Cek apakah ada data nya ada apa engga di server security

        if ($user == null) {
            return response()->json(['success' => 0, 'message' => 'Data not found']);
        }

        $user = DB::connection('192.168.178.44-admin')
            ->table('MSIDCARD')
            ->select('CARDNODEVICE', 'NIK', 'EMPNM', 'DEPTID', 'FOTOBLOB')
            ->where(['BARCODE' => $user->NIK])
            ->orderBy('EMPCARDID', 'desc')
            ->first();

        // validasi dulu sebelum masuk ke #fotoblob
        // buat query, select, untuk ke table klinik_pemeriksaan baru masuk ke fotoblob
        // $dataNik = DB::table('klinik_pemeriksaan')
        //     ->where('nik', $user->NIK)
        //     ->whereDate('tanggal_pemeriksaan', Carbon::now()->toDateString())
        //     ->exists();

        // if ($dataNik) {
        //     return response()->json(['error' => '3', 'data' => [], 'message' => 'Data NIK sudah pernah di daftarkan pada tanggal yang sama']);
        // }

        $user->FOTOBLOB = base64_encode($user->FOTOBLOB);

        return response()->json([
            'success' => 1,
            'data' => $user,
            'message' => 'Get data succeed'
        ]);
    }

    public function dashboard($nik)
    {
        $user = DB::connection('192.168.178.44-admin')
            ->table('MSIDCARD')
            ->select('CARDNODEVICE', 'NIK', 'EMPNM', 'DEPTID', 'FOTOBLOB')
            ->where('NIK', $nik)
            ->first();

        $foto_user = base64_encode($user->FOTOBLOB);
        dd($foto_user);
    }

    public function createDiagnosa(Request $request)
    {
        $request->validate([
            'diagnosa' => 'required'
        ]);

        // Check if diagnosa alredy exist
        $cek = Diagnosa::where('nama_diagnosa', $request->diagnosa)->where('active', 'Y')->first();
        if ($cek != null) {
            return response(['success' => 0, 'message' => 'Diagnosa sudah ada']);
        }

        $diagnosa = new Diagnosa;
        $diagnosa->nama_diagnosa = $request->diagnosa;
        $diagnosa->active = 'Y';
        $diagnosa->save();

        return response(['success' => 1, 'message' => 'Berhasil menambah diagnosa', 'data' => $diagnosa]);
    }

    public function createFaskes(Request $request)
    {
        $request->validate([
            'nama_faskes' => 'required'
        ]);

        // Check if diagnosa alredy exist
        $cek = Faskes::where('nama_faskes', $request->nama_faskes)->first();
        if ($cek != null) {
            return response(['success' => 0, 'message' => 'Faskes sudah ada']);
        }

        $kode_faskes = $this->generateID('FS', 'klinik_faskes', 'kode_faskes');

        $faskes = new Faskes;
        $faskes->kode_faskes = $kode_faskes;
        $faskes->nama_faskes = $request->nama_faskes;
        $faskes->save();

        return response(['success' => 1, 'message' => 'Berhasil menambah faskes', 'data' => $faskes]);
    }
}
