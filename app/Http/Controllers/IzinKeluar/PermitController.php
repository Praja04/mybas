<?php
namespace App\Http\Controllers\IzinKeluar;

use App\HrKaryawan;
use App\Http\Controllers\Controller;
use App\LunchBreak;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PermitController extends Controller
{
    // private function formatData($riwayat)
    // {
    //     return [
    //         'nik'             => $riwayat->nik,
    //         'nama'            => $riwayat->nama,
    //         'divisi'          => $riwayat->divisi,
    //         'jam_keluar'      => $riwayat->jam_keluar ? $riwayat->jam_keluar->format('Y-m-d H:i:s') : null,
    //         'jam_masuk'       => $riwayat->jam_masuk ? $riwayat->jam_masuk->format('Y-m-d H:i:s') : null,
    //         'menit_terlambat' => $riwayat->menit_terlambat,
    //         'status'          => $riwayat->status,
    //     ];
    // }

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
            $lunchStart = Carbon::today()->setTime(12, 0, 0);
            $lunchEnd   = Carbon::today()->setTime(13, 0, 0);

            if ($now->isBefore($lunchStart)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Belum jam istirahat. Silakan absen kembali mulai pukul 12.00.',
                ], 400);
            }

            if ($now->isAfter($lunchEnd)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Jam istirahat makan siang sudah selesai.',
                ], 400);
            }

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
            $limitTime = Carbon::today()->setTime(13, 0, 0);

            $minutesLate = 0;
            $status      = 'Tepat Waktu';

            if ($now->gt($limitTime)) {
                $minutesLate = $now->diffInMinutes($limitTime);
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
}
