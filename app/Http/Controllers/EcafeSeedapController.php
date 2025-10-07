<?php

namespace App\Http\Controllers;

use App\ecafeSedaapBas;
use App\Imports\Ecafesedaap\OvertimeImport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Session;
use App\Imports\ExcelImport;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Controllers\Controller;


class EcafeSeedapController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('hr.ecafesedaap.upload-jumlah-pesanan.index');
    }

    public function scanPage()
    {
        return view('hr.ecafesedaap.scan-page');
    }

    public function doScan(Request $request)
    {
        // Cek apakah data sudah ada
        if (date('H') >= 0 && date('H') <= 06) {
            $date1 = date('Y-m-d', strtotime('-1 day'));
            $date2 = date('Y-m-d');
        } else {
            $date1 = date('Y-m-d');
            $date2 = date('Y-m-d', strtotime('+1 day'));
        }

        $rfid = (int)$request->rfid;

        $nik = DB::connection('192.168.178.44-admin')
        ->table('MSIDCARD')
        ->select('NIK', 'EMPNM')
        ->where(['CARDNODEVICE' => $rfid])
        ->where('STATUS', 'X') 
        ->orderByRaw('CAST(EMPCARDID AS SIGNED) desc')
        ->first();

        // menampilkan jumlah sisa porsi pada display scan
        $currentTime = strtotime(date('H:i:s'));
        $startTime   =  strtotime("00:00:01");
        $endTime     = strtotime("05:59:00");

        if ($currentTime >= strtotime("09:00:01") and  $currentTime <= strtotime("16:00:00")) {
            $shift = 1;
            $tanggal = date('Y-m-d');
            $startDate = date('Y-m-d 09:00:00');
            $endDate = date('Y-m-d 16:00:00');
        } else if ($currentTime >= strtotime("16:00:01") and  $currentTime <= strtotime("22:30:00")) {
            $shift = 2;
            $tanggal = date('Y-m-d');
            $startDate = date('Y-m-d 16:00:00');
            $endDate = date('Y-m-d 22:30:00');
        } else if ($currentTime >= strtotime("22:30:01") and  $currentTime <= strtotime("23:59:00")) {
            $shift = 3;
            $tanggal = date('Y-m-d');
            $startDate = date('Y-m-d 22:30:00');
            $endDate = date('Y-m-d', strtotime('1 day')) . ' 07:00:00';
        } else if ($currentTime >= strtotime("00:00:01") and  $currentTime <= strtotime("07:00:00")) {
            $shift = 3;
            $tanggal = date('Y-m-d', strtotime('-1 day'));
            $startDate = $tanggal . ' 00:00:00';
            $endDate = $tanggal . '  07:00:00';
        } else {
            $shift = 1;
            $tanggal = null;
            $startDate = null;
            $endDate = null;
        }

        $shift_number = $shift;

        // menarik data scan pada db ecafesedaap_scan
        $jumlah_scan = DB::table('ecafesedaap_scan')
            // ->whereBetween('waktu', [$startDate, $endDate])
            ->where('shift', $shift_number)
            ->where('tanggal', $tanggal)
            // ->where('kategori', 'non-staff')
            ->count();
        // menarik data jumlah pesanan pada db ecafesedaapbas
        $jumlah_pesanan = DB::table('ecafesedaapbas')
            ->where('tanggal', $tanggal)
            ->where('shift', $shift_number)
            ->where('kategori', 'non-staff')
            ->first()->jumlah;

        // menamilkan sisa porsi
        $sisa_porsi = $jumlah_pesanan - $jumlah_scan;

        $data['sisa_porsi'] = $sisa_porsi;

        // Ini kalo data nya ga ada di secure accesss
        if ($nik == null || $rfid == 0) {
            return response(['success' => 0, 'message' => 'Data tidak ditemukan. Hubungi HRD', 'data' => $data]);
        }

        $cek = DB::table('ecafesedaap_scan')->where('rf_id', $request->rfid)->where('waktu', '>=', $date1 . ' 06:00:00')->where('waktu', '<=', $date2 . ' 06:00:00')->first();

        // Ini kalo data nya udah ada di database secan makan, berarti udah pernah scan
        if ($cek != null) {
            return response(['success' => 0, 'data' => $data, 'message' => 'Kamu sudah scan makan pada : <br /> <strong>' . $cek->waktu . '</strong>']);
        }

        // Ambil data yang lebih lengkap
        $user = DB::connection('192.168.178.44-admin')
            ->table('MSIDCARD')
            ->select('NIK', 'CARDNODEVICE', 'EMPNM', 'DEPTID', 'FOTOBLOB')
            ->where(['NIK' => $nik->NIK, 'CARDNODEVICE' => (int)$request->rfid])
            ->first();
        DB::table('ecafesedaap_scan')->insert([
            'rf_id' => $request->rfid,
            'nik' => $user->NIK,
            'nama' => $user->EMPNM,
            'waktu' => date('Y-m-d H:i:s'),
            'tanggal' => $tanggal,
            'shift' => $shift_number
        ]);

        $data = [
            'nik' => $user->NIK,
            'name' => $user->EMPNM,
            'department' => $user->DEPTID,
            'image' => base64_encode($user->FOTOBLOB)
        ];

        $data['sisa_porsi'] = $sisa_porsi - 1;

        return response(['success' => 1, 'message' => 'Selamat menikmati', 'data' => $data]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

    public function store(Request $request)
    {
        // dd($request->all());
        // Generate ID Pesanan base

        // Loop through each category
        // foreach ($request->input('kategori', []) as $kategori => $value) {
        //     // Prefix unik berdasarkan kategori
        //     $prefix = $kategori === 'staff' ? 'S-' : 'NS-';

        //     // Loop through each shift for the category
        //     foreach ($request->input('shift.' . $kategori, []) as $shift => $shiftValue) {
        //         // Buat ID Pesanan unik dengan menambahkan prefix, base, dan shift
        //         $id_pesanan = $prefix . $id_pesanan_base . '-' . $shift;

        //         // Retrieve the quantity for the shift
        //         $jumlah = $request->input("shift_qty.{$kategori}.{$shift}", 0);

        //         // Cek dan Insert data
        //         DB::table('ecafesedaapbas')->updateOrInsert(
        //             [
        //                 'id_pesanan' => $id_pesanan, // Kolom untuk cek duplikasi
        //                 'tanggal' => $request->tanggal,
        //                 'shift' => $shift,
        //                 'kategori' => $kategori
        //             ],
        //             [
        //                 'jumlah' => $jumlah // Update jumlah jika ada duplikasi
        //             ]
        //         );
        //     }
        // }

        $id_pesanan_base = 'TR-' . implode('', array_reverse(explode('-', $request->tanggal)));

        $shiftData = $request->input('shift');
        $shiftQtyData = $request->input('shift_qty');

        foreach ($shiftData as $category => $indexes) {
            foreach ($indexes as $index => $value) {
                $quantity = $shiftQtyData[$category][$index];
                $id_pesanan = $id_pesanan_base . $index;

                if ($category == 'staff') {
                    $id_pesanan .= 'S';
                } elseif ($category == 'non-staff') {
                    $id_pesanan .= 'N';
                }

                // Check if ID_PESANAN already exists
                $existingEntry = ecafeSedaapBas::where('id_pesanan', $id_pesanan)->first();

                if ($existingEntry) {
                    Session::flash('error', "pesanan pada tanggal ({$request->tanggal}) sudah ada.");
                    return back();
                } else {
                    // If ID_PESANAN does not exist, create a new one
                    ecafeSedaapBas::create([
                        'id_pesanan' => $id_pesanan,
                        'tanggal' => $request->tanggal,
                        'kategori' => $category,
                        'shift' => $index,
                        'jumlah' => $quantity,
                    ]);
                }
            }
        }

        // If loop finishes without finding duplicates
        Session::flash('info', 'Data Upload Anda Berhasil Disimpan');
        return back();
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */


    public function import_excel(Request $request)
    {

        // validasi
        $this->validate($request, [
            'file' => 'required|mimes:csv,xls,xlsx'
        ]);

        // menangkap file excel
        $file = $request->file('file');

        // import data
        Excel::import(new ExcelImport, $request->file('file'));

        // notifikasi dengan session
        Session::flash('info', 'Data Anda Berhasi Diimport!');

        // alihkan halaman kembali
        return back();
    }

    public function editJumlahPesanan(Request $request)
    {
        $data = ecafeSedaapBas::where('tanggal', $request->date)->get();

        return response()->json($data);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function updateJumlahPesanan(Request $request)
    {
        $shiftData = $request->input('shift');
        $shiftQtyData = $request->input('shift_qty');
        $isUpdated = false; 
    
        foreach ($shiftData as $category => $indexes) {
            foreach ($indexes as $index => $value) {
                $quantity = $shiftQtyData[$category][$index];
    
                $updatedRows = ecafeSedaapBas::where('tanggal', $request->tanggal_pesan)
                                ->where('kategori', $category)
                                ->where('shift', $index)
                                ->update(['jumlah' => $quantity]);
    
                if ($updatedRows > 0) {
                    $isUpdated = true;
                }
            }
        }
    
        if (!$isUpdated) {
            Session::flash('error', 'Error: Silahkan upload pesanan terlebih dahulu.');
            return back();
        }
            Session::flash('info', 'Data Update Anda Berhasil Disimpan');
        return back();
    }
    

    public function showeditJumlahPesanan()
    {
        return view('hr.ecafesedaap.update-jumlah-pesanan.index');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function getJumlahPesanan(Request $request)
    {
        $tanggal = $request->tanggal_pesan;
        $shift = $request->shift;
        $data = DB::table('ecafesedaapbas')->where('tanggal', $request->tanggal_pesan)->where('shift', $request->shift)->first();
        // dd($request->all);

        return view('hr.ecafesedaap.update-jumlah-pesanan.index', compact('data', 'tanggal', 'shift'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    // public function editJumlahPesanan($id_pesanan)
    // {
    //     // Get data pesanan
    //     $data = DB::table('ecafesedaapbas')->where('id_pesanan', $id_pesanan)->first();

    //     $tanggal = $data->tanggal;
    //     $shift = $data->shift;
    //     $diff = date('Y-m-d', strtotime('-1 day', strtotime($data->tanggal)));
    //     if ($diff and date('H:i') >= '17:00') {
    //         Session::flash('error', 'Update Telah Berakhir!');
    //         return back();
    //     } else {
    //         return view('hr.ecafesedaap.update-jumlah-pesanan.edit', compact('data', 'tanggal', 'shift'));
    //     }
    // }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {

        // penambahan maka nilai nya plus 
        // Jika pengurangan nilain nya minus 
        $jumlah_porsi = $request->jumlah_porsi;
        $jenis = $request->jenis;
        $tanggal_pesan = $request->tanggal_pesan;
        $shift = $request->shift;
        $alasan_update = $request->alasan_update;


        // Ambil jumlah pesanan yang sekarang
        $data_sekarang = DB::table('ecafesedaapbas')->where('tanggal', $tanggal_pesan)->where('shift', $shift)->first();

        if ($jenis == 'pengurangan') {
            // Ambil jumlah pesanan sekarang
            $jumlah_setelah_perubahan = $data_sekarang->jumlah - $jumlah_porsi;
        } else {
            $jumlah_setelah_perubahan = $data_sekarang->jumlah + $jumlah_porsi;
        }

        // Tambahkan dengan jumlah porsi penambahan atau pengurangan

        // dd($jumlah_setelah_perubahan);
        DB::table('ecafesedaapbas')->where('tanggal', $tanggal_pesan)->where('shift', $shift)->update([
            'jumlah' => $jumlah_setelah_perubahan
        ]);

        // dd('Update berhasil');

        //update jumlah pesanan catering
        DB::table('ecafesedaapbas_histori')->where('id', $request->id)->insert([
            'id_pesanan' => $data_sekarang->id_pesanan,
            'jumlah' => $jumlah_porsi,
            'alasan_update' => $alasan_update,
            'jenis' => $jenis,
            'tanggal_update' => date('Y-m-d'),
            'jam_update' => date('H:i:s'),
            'update_by' => auth()->user()->name

        ]);
        Session::flash('info', 'Data Update Anda Berhasil Disimpan');
        return back();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function view()
    {
        $data = DB::table('ecafesedaapbas')->join('ecafesedaapbas_histori', 'ecafesedaapbas.id_pesanan', '=', 'ecafesedaapbas_histori.id_pesanan')->get();
        return view('hr.ecafesedaap.view-jumlah-pesanan.index', compact('data'));
    }
    public function PencarianPesanan(Request $request)
    {
        $data = DB::table('ecafesedaapbas')
            ->join('ecafesedaapbas_histori', 'ecafesedaapbas.id_pesanan', '=', 'ecafesedaapbas_histori.id_pesanan')
            ->where('tanggal', $request->tanggal)
            ->where('shift', $request->shift)
            ->get();

        $tanggal = $request->tanggal;
        return view('hr.ecafesedaap.view-jumlah-pesanan.result_pencarian', compact('data', 'tanggal'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function ReportPesananCatering()
    {
        $data = DB::table('ecafesedaapbas')->get();
        $scan = DB::table('ecafesedaap_scan')->get();
        return view('hr.ecafesedaap.reporting.index', compact('data', 'scan'));
    }

    public function reportingDetail($id_pesanan)
    {
        $data_pesanan = DB::table('ecafesedaapbas')->where('id_pesanan', $id_pesanan)->first();

        if (date('l') == 'Saturday') {
            $start_shift1 = $data_pesanan->tanggal . ' 06:00:00';
            $end_shift1 = $data_pesanan->tanggal . ' 11:00:00';
            $start_shift2 = $data_pesanan->tanggal . ' 11:00:00';
            $end_shift2 = $data_pesanan->tanggal . ' 16:00:00';
            $start_shift3 = $data_pesanan->tanggal . ' 16:00:00';
            $end_shift3 = $data_pesanan->tanggal . ' 21:00:00';
        } else {
            $start_shift1 = $data_pesanan->tanggal . ' 09:00:00';
            $end_shift1 = $data_pesanan->tanggal . ' 16:00:00';
            $start_shift2 = $data_pesanan->tanggal . ' 16:00:00';
            $end_shift2 = $data_pesanan->tanggal . ' 22:30:00';
            $start_shift3 = $data_pesanan->tanggal . ' 22:30:00';
            $end_shift3 = date('Y-m-d', strtotime($data_pesanan->tanggal . ' + 1 day')) . ' 07:00:00';
        }

        if ($data_pesanan->shift == 1) {
            $scan = DB::table('ecafesedaap_scan')
                ->whereBetween('waktu', [$start_shift1, $end_shift1])
                ->get();
        } elseif ($data_pesanan->shift == 2) {
            $scan = DB::table('ecafesedaap_scan')
                ->whereBetween('waktu', [$start_shift2, $end_shift2])
                ->get();
        } else {
            $scan = DB::table('ecafesedaap_scan')
                ->whereBetween('waktu', [$start_shift3, $end_shift3])
                ->get();
        }

        return view('hr.ecafesedaap.reporting.detail', compact('data_pesanan', 'scan'));
    }

    public function PencarianReport(Request $request)
    {
        if (date('l') == 'Saturday') {
            $start_shift1 = $request->tanggal_mulai . ' 06:00:00';
            $end_shift1 = $request->tanggal_     . ' 11:00:00';
            $start_shift2 = $request->tanggal_mulai . ' 11:00:00';
            $end_shift2 = $request->tanggal_selesai . ' 16:00:00';
            $start_shift3 = $request->tanggal_mulai . ' 16:00:00';
            $end_shift3 = $request->tanggal_selesai . ' 21:00:00';
        } else {
            $start_shift1 = $request->tanggal_mulai . ' 09:00:00';
            $end_shift1 = $request->tanggal_selesai . ' 16:00:00';
            $start_shift2 = $request->tanggal_mulai . ' 16:00:00';
            $end_shift2 = $request->tanggal_selesai . ' 22:30:00';
            $start_shift3 = $request->tanggal_mulai . ' 22:30:00';
            $end_shift3 = date('Y-m-d', strtotime($request->tanggal . ' + 1 day')) . ' 07:00:00';
        }

        //dd($request->all());
        $master = DB::table('ecafesedaapbas')
            ->whereBetween('tanggal', [$request->tanggal_mulai, $request->tanggal_selesai])
            ->orderBy('tanggal', 'asc')
            ->get();

        $scan = DB::table('ecafesedaap_scan')
            ->whereBetween('tanggal', [$request->tanggal_mulai, $request->tanggal_selesai])
            ->orderBy('tanggal', 'asc')
            ->get();
        // ->groupBy('shift');
        $tanggal_mulai = $request->tanggal_mulai;
        $tanggal_selesai = $request->tanggal_selesai;
        return view('hr.ecafesedaap.reporting.report_pencarian', compact('master', 'scan', 'tanggal_mulai', 'tanggal_selesai'));
    }

    public function uploadOvertime()
    {
        $where = [];

        if (isset($_GET['tanggal'])) {
            $where[] = ['tanggal', '=', $_GET['tanggal']];
        }

        $dataOvertime = DB::table('ecafesedaap_overtime')
            ->where($where)
            ->get();

        return view('hr.ecafesedaap.upload-overtime', compact('dataOvertime'));
    }

    public function doUploadOvertime(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv',
            'tanggal' => 'required|date'
        ]);

        $file = $request->file('file');
        $tanggal = $request->tanggal;

        $data = Excel::toArray(new OvertimeImport, $file);

        foreach ($data[0] as $key => $_data) {

            if ($key == 0) {
                continue;
            }

            $cek_data_exist = DB::table('ecafesedaap_overtime')
                ->where('tanggal', $tanggal)
                ->where('nik', $_data[0])
                ->first();

            if ($cek_data_exist != null) {
                continue;
            }

            DB::table('ecafesedaap_overtime')->insert([
                'tanggal' => $tanggal,
                'nik' => $_data[0],
                'nama' => $_data[1],
            ]);
        }

        return redirect()->back()->with('success', 'Data berhasil diupload');
    }
}
