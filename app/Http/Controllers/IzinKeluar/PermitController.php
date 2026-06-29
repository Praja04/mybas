<?php
namespace App\Http\Controllers\IzinKeluar;

use App\HrKaryawan;
use App\Http\Controllers\Controller;
use App\LunchBreak;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;

class PermitController extends Controller
{

    public function index()
    {
        $today = LunchBreak::whereNotNull('jam_keluar')
            ->whereDate('jam_keluar', today())
            ->latest()
            ->paginate(10);
        $all = LunchBreak::whereNotNull('jam_keluar')
            ->latest()
            ->get();

        return view('izin_keluar.index', compact('today', 'all'));
    }

    public function checkKaryawan(Request $req)
    {
        $req->validate([
            'rfidOrNik' => 'required|string',
        ]);

        $search = trim((string) $req->rfidOrNik);

        $hris = HrKaryawan::where(function ($q) use ($search) {
            $q->whereRaw("CAST(nik AS UNSIGNED) = CAST(? AS UNSIGNED)", [$search])
                ->orWhere('nama', 'LIKE', "%$search%")
                ->orWhere('cardnodevice', $search);
        })->first();

        $dataPusat = null;
        try {
            $dataPusat = DB::connection('192.168.178.44-admin')
                ->table('MSIDCARD')
                ->select('NIK', 'EMPNM', 'DEPTID', 'CARDNODEVICE', 'RFID', 'FOTOBLOB', 'STATUS', 'TYPECARD')
                ->where(function ($q) use ($search, $hris) {
                    $q->whereRaw("CAST(NIK AS UNSIGNED) = CAST(? AS UNSIGNED)", [$search])
                        ->orWhere('EMPNM', 'LIKE', "%$search%")
                        ->orWhere('CARDNODEVICE', $search)
                        ->orWhere('RFID', $search);

                    if ($hris) {
                        $q->orWhereRaw("CAST(NIK AS UNSIGNED) = CAST(? AS UNSIGNED)", [$hris->nik]);
                    }
                })
                ->where('STATUS', 'X')
                ->first();
        } catch (\Throwable $e) {
            Log::error("Koneksi DB Pusat Gagal: ", [$e->getMessage()]);
        }

        if (! $hris && $dataPusat) {
            $hris = HrKaryawan::whereRaw("CAST(nik AS UNSIGNED) = CAST(? AS UNSIGNED)", [$dataPusat->NIK])->first();
        }

        if (! $hris && ! $dataPusat) {
            return response()->json([
                'success' => false,
                'message' => 'Karyawan tidak terdaftar.',
            ], 404);
        }

        $nik    = $hris->nik ?? ($dataPusat->NIK ?? $search);
        $nama   = $hris->nama ?? ($dataPusat->EMPNM ?? 'Tidak dikenali.');
        $divisi = $hris->kode_divisi ?? ($dataPusat->DEPTID ?? 'Divisi tidak dikenali.');

        $foto = null;
        if ($dataPusat && isset($dataPusat->FOTOBLOB)) {
            try {
                $foto = 'data:image/jpeg;base64,' . base64_encode($dataPusat->FOTOBLOB);
            } catch (\Throwable $e) {
                Log::error('Gagal encode foto', ['error' => $e->getMessage(), 'nik' => $nik]);
            }
        }

        $activeOuting = LunchBreak::where('nik', $nik)
            ->whereNull('jam_masuk')
            ->whereDate('jam_keluar', today())
            ->first();

        $now = Carbon::now();

        if (! $activeOuting) {
            $lastIn = LunchBreak::where('nik', $nik)
                ->whereNotNull('jam_masuk')
                ->whereDate('jam_keluar', today())
                ->latest('jam_masuk')
                ->first();

            // $lunchStart = Carbon::today()->setTime(12, 0, 0);
            // $lunchEnd   = Carbon::today()->setTime(13, 0, 0);

            if ($lastIn) {
                $lastCheckInTime = Carbon::parse($lastIn->jam_masuk);

                if ($lastCheckInTime->diffInMinutes($now) < 1) {
                    return response()->json([
                        'success' => false,
                        'message' => "Anda baru saja tap MASUK. Harap tunggu minimal 1 menit untuk tap KELUAR.",
                    ], 400);
                }
            }

            // if ($now->isBefore($lunchStart)) {
            //     return response()->json([
            //         'success' => false,
            //         'message' => "Belum jam istirahat. Silakan absen kembali mulai pukul " . $lunchStart->format('H:i') . ".",
            //     ], 400);
            // }

            // if ($now->isAfter($lunchEnd)) {
            //     return response()->json([
            //         'success' => false,
            //         'message' => 'Jam istirahat makan siang sudah selesai.',
            //     ], 400);
            // }

            LunchBreak::create([
                'nik'        => $nik,
                'nama'       => $nama,
                'divisi'     => $divisi,
                'jam_keluar' => $now,
                'status'     => 'Belum Kembali',
            ]);

            return response()->json([
                'success' => true,
                'action'  => 'keluar',
                'message' => 'Selamat istirahat',
                'data'    => [
                    'nik'        => $nik,
                    'nama'       => $nama,
                    'divisi'     => $divisi,
                    'foto'       => $foto,
                    'jam_keluar' => $now->format('Y-m-d H:i:s'),
                ],
            ]);
        } else {
            // $limitTime = Carbon::today()->setTime(13, 0, 0);
            $checkOutTime = Carbon::parse($activeOuting->jam_keluar);

            if ($checkOutTime->diffInMinutes($now) < 1) {
                return response()->json([
                    'success' => false,
                    'message' => "Anda baru saja tap KELUAR. Harap tunggu minimal 1 menit untuk tap MASUK.",
                ], 400);
            }

            $limitTimeHours = $checkOutTime->copy()->addMinutes(60)->startOfMinute();
            $lastReturnTime = Carbon::today()->setTime(13, 0, 0);
            $nowMinute      = $now->copy()->startOfMinute();
            $strictLimit    = $limitTimeHours->min($lastReturnTime);

            $minutesLate = 0;
            $status      = 'Tepat Waktu';

            if ($nowMinute->gt($strictLimit)) {
                $minutesLate = $nowMinute->diffInMinutes($strictLimit);
                $status      = 'Terlambat';
            }

            $activeOuting->update([
                'jam_masuk'       => $now,
                'menit_terlambat' => $minutesLate,
                'status'          => $status,
            ]);

            $responseMessage = ($status === 'Terlambat') ? "Terima kasih sudah kembali, total keterlambatan Anda adalah {$minutesLate} menit." : "Selamat bekerja kembali.";

            return response()->json([
                'success' => true,
                'action'  => 'kembali',
                'message' => $responseMessage,
                'data'    => [
                    'nik'             => $nik,
                    'nama'            => $nama,
                    'divisi'          => $divisi,
                    'foto'            => $foto,
                    'jam_masuk'       => $now->format('Y-m-d H:i:s'),
                    'menit_terlambat' => $minutesLate,
                    'status'          => $status,
                ],
            ]);
        }
    }

    public function getData(Request $req)
    {
        $query = LunchBreak::query();

        if ($req->tab === 'today') {
            $query->whereDate('jam_keluar', today());
        }

        if ($req->filled('divisi')) {
            $query->where('divisi', $req->divisi);
        }

        if ($req->filled('status')) {
            $query->where('status', $req->status);
        }

        if ($req->filled('tanggal')) {
            $query->whereDate('jam_keluar', $req->tanggal);
        }

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('tanggal', function ($row) {
                return Carbon::parse($row->jam_keluar)->format('Y-m-d');
            })
            ->make(true);
    }

    public function reportPage()
    {
        $data['divisi'] = LunchBreak::whereNotNull('divisi')->where('divisi', '!=', '')->distinct()->pluck('divisi')->toArray();
        $data['status'] = LunchBreak::whereNotNull('status')->where('status', '!=', '')->distinct()->pluck('status')->toArray();

        return view('izin_keluar.report', $data);
    }
}
