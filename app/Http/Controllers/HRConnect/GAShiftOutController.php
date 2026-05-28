<?php
namespace App\Http\Controllers\HRConnect;

use App\Exports\HRConnect\KaryawanAktifExport;
use App\HrKaryawan;
use App\Http\Controllers\Controller;
use App\Jobs\HRConnect\KaryawanKeluarSelesaiToHR;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\Datatables\Datatables;

class GAShiftOutController extends Controller
{
    public function getData(Request $req)
    {
        $query = HrKaryawan::with(['penghuni' => function ($q) {
            $q->where('is_active', 'Y');
        }])
            ->where('out_complete', 'N')
            ->where('tanggal_masuk', '>', '2025-01-01')
            ->orderBy('tanggal_masuk', 'desc');

        if ($req->tampilkan_semua == 0 && ! empty($req->tanggal)) {
            $query->where('tanggal_masuk', $req->tanggal);
        }

        $data = $query->get();

        return Datatables::of($data)
            ->addColumn('checkStaff', function ($row) {
                $dataLoker = DB::table('loker_penghuni')
                    ->where('nik', $row->nik)
                    ->first();

                if ($dataLoker) {
                    if (strtolower($dataLoker->kategori_karyawan) == 'staff') {
                        return 'Y';
                    } else {
                        return 'N';
                    }
                }

                return $row->staff;
            })
            ->make(true);
    }

    public function index()
    {
        $tanggalTersedia = HrKaryawan::select('tanggal_masuk')
            ->where('out_complete', 'N')
            ->whereNotNull('tanggal_masuk')
            ->groupBy('tanggal_masuk')
            ->orderBy('tanggal_masuk', 'desc')
            ->pluck('tanggal_masuk');

        return view('hr-connect.ga.shift-out', compact('tanggalTersedia'));
    }

    public function update(Request $req)
    {
        $data = $req->input('data');

        if (empty($data)) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak ada data karyawan yang dipilih. Silakan centang minimal satu karyawan.',
            ], 400);
        }

        DB::beginTransaction();

        try {
            foreach ($data as $item) {
                $nik        = $item['nik'];
                $idKaryawan = $item['id_karyawan'];

                $alasanKeluar = $item['alasan'] ?? 'Pencabutan Loker Massal';

                $karyawan = HrKaryawan::find($idKaryawan);
                if (! $karyawan) {
                    continue;
                }
                $nama = ucwords(strtolower($karyawan->nama ?? '-'));

                $penghuni = DB::table('loker_penghuni')->where('nik', $nik)->first();

                DB::table('loker_transaksi')->insert([
                    'nik'            => $nik,
                    'nama'           => $nama,
                    'kode_rak'       => $penghuni ? $penghuni->kode_rak : '-',
                    'no_loker'       => $penghuni ? $penghuni->no_loker : '-',
                    'tipe_transaksi' => 'KELUAR',
                    'operator'       => auth()->user()->name ?? 'Sistem GA',
                    'keterangan'     => $penghuni
                        ? 'Clearance Loker: ' . $alasanKeluar
                        : 'Clearance Non-Loker: ' . $alasanKeluar,
                    'created_at'     => now(),
                ]);

                if ($penghuni) {
                    DB::table('loker_penghuni')->where('id', $penghuni->id)->delete();
                }

                $karyawan->update([
                    'out_complete' => 'Y',
                    // 'is_active'      => 'N',
                    // 'tanggal_keluar' => now()->format('Y-m-d'),
                ]);
            }

            DB::commit();

            $email_hr = User::whereHas('group.permissions', function ($query) {
                $query->where('codename', 'hr_connect_notified_out');
            })->select('email')
                ->whereNotNull('email')
                ->groupBy('email')
                ->get();

            if ($email_hr->isNotEmpty()) {
                $to   = $email_hr->pluck('email')->toArray();
                $link = url('/hr-connect/dept-ga/karyawan-keluar');

                KaryawanKeluarSelesaiToHR::dispatch($to, $data, $link);
            }

            return response()->json([
                'success' => true,
                'message' => 'Proses clearance berhasil dan email sedang dikirim.',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('GA Shift Out Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal memproses data clearance: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function exportExcel(Request $request)
    {
        $byDate  = $request->input('tanggal');
        $showAll = $request->input('tampilkan_semua');

        return Excel::download(
            new KaryawanAktifExport($byDate, $showAll),
            'Data Karyawan - ' . ($byDate != null ? 'Per Tanggal ' . $byDate : 'Data Keseluruhan') . '.xlsx'
        );
    }
}
