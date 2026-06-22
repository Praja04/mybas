<?php
namespace App\Http\Controllers\HRConnect;

use App\Exports\HRConnect\KaryawanAktifExport;
use App\HrKaryawan;
use App\Http\Controllers\Controller;
use App\Jobs\HRConnect\KaryawanKeluarSelesaiToHR;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\Datatables\Datatables;

class GAShiftOutController extends Controller
{
    public function index()
    {
        // $tanggalTersedia = HrKaryawan::where('out_complete', 'N')
        //     ->where('is_excuse_out', 'Y')
        //     ->whereNotNull('tgl_shift_out')
        //     ->select('tgl_shift_out')
        //     ->distinct()
        //     ->orderBy('tgl_shift_out', 'desc')
        //     ->pluck('tgl_shift_out');

        return view('hr-connect.ga.shift-out');
        // compact('tanggalTersedia')
    }

    public function getData(Request $req)
    {
        $query = HrKaryawan::with(['penghuni' => function ($q) {
            $q->where('is_active', 'Y');
        }])
            ->select('id', 'nik', 'nama', 'kode_divisi', 'kode_bagian', 'staff', 'tgl_shift_out', 'is_excuse_out', 'out_complete')
            ->where('out_complete', 'N')
            ->where('is_excuse_out', 'Y')
            ->orderBy('tgl_shift_out', 'desc');

        if ($req->tampilkan_semua == 0 && ! empty($req->tanggal)) {
            $query->where('tgl_shift_out', $req->tanggal);
        }

        return Datatables::of($query)
            ->filter(function ($query) use ($req) {
                if ($req->has('search') && ! empty($req->input('search')['value'])) {
                    $keyword = $req->input('search')['value'];

                    $query->where(function ($q) use ($keyword) {
                        $q->where('nama', 'like', '%' . $keyword . '%')
                            ->orWhere('nik', 'like', '%' . $keyword . '%')
                            ->orWhere('kode_divisi', 'like', '%' . $keyword . '%')
                            ->orWhere('kode_bagian', 'like', '%' . $keyword . '%');
                    });
                }
            })
            ->addColumn('checkStaff', function ($row) {
                if ($row->penghuni) {
                    return strtolower($row->penghuni->kategori_karyawan) == 'staff' ? 'Y' : 'N';
                }
                return $row->staff;
            })
            ->make(true);
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
                $nik          = $item['nik'];
                $idKaryawan   = $item['id_karyawan'];
                $alasanKeluar = $item['alasan'] ?? 'Pencabutan Loker Massal';

                $karyawan = HrKaryawan::lockForUpdate()->find($idKaryawan);
                if (! $karyawan) {
                    continue;
                }

                $nama = ucwords(strtolower($karyawan->nama ?? '-'));

                $penghuni = DB::table('loker_penghuni')
                    ->where('nik', $nik)
                    ->where('is_active', 'Y')
                    ->lockForUpdate()
                    ->first();

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
                    // DB::table('loker_transaksi')->insert([
                    //     'nik'            => $nik,
                    //     'nama'           => $nama,
                    //     'kode_rak'       => $penghuni->kode_rak,
                    //     'no_loker'       => $penghuni->no_loker,
                    //     'tipe_transaksi' => 'KELUAR',
                    //     'operator'       => auth()->user()->name ?? 'Sistem GA',
                    //     'keterangan'     => 'Clearance Loker: ' . $alasanKeluar,
                    //     'created_at'     => now(),
                    // ]);

                    DB::table('loker_penghuni')->where('id', $penghuni->id)->delete();
                }

                $karyawan->update([
                    'out_complete' => 'Y',
                ]);
            }

            $email_hr = User::whereHas('group.permissions', function ($query) {
                $query->where('codename', 'hr_connect_ir');
            })
                ->whereNotNull('email')
                ->pluck('email')
                ->unique()
                ->toArray();

            Cache::forget('list_bulan_karyawan_keluar_ga');

            DB::commit();

            if (! empty($email_hr)) {
                $link = url('/hr-connect/dept-hrd/karyawan-keluar');
                KaryawanKeluarSelesaiToHR::dispatch($email_hr, $data, $link);
            }

            return response()->json([
                'success' => true,
                'message' => 'Proses clearance loker berhasil dan email diteruskan ke HRD IR.',
            ]);

        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('GA Shift Out Error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Gagal memproses data clearance: Terjadi kesalahan server.',
            ], 500);
        }
    }

    public function exportExcel(Request $request)
    {
        $byDate  = $request->input('tanggal');
        $showAll = $request->input('tampilkan_semua');

        $fileName = 'Data Karyawan Keluar GA - ' . (! empty($byDate) ? 'Per Tanggal ' . $byDate : 'Data Keseluruhan') . '.xlsx';

        return Excel::download(new KaryawanAktifExport($byDate, $showAll), $fileName);
    }
}
