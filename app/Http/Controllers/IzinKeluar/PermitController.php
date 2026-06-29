<?php
namespace App\Http\Controllers\IzinKeluar;

use App\Exports\IzinKeluarExport;
use App\HrKaryawan;
use App\Http\Controllers\Controller;
use App\LunchBreak;
use App\Mail\IzinKeluarReportMail;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
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

    public function getFilterBulanTahun(Request $req)
    {
        $search = $req->input('q');

        $allMonths = Cache::remember('list_bulan_istirahat_keluar', now()->addHours(24), function () {
            return LunchBreak::select(
                DB::raw("DATE_FORMAT(jam_keluar, '%Y-%m') as id_bulan"),
                DB::raw("DATE_FORMAT(jam_keluar, '%M %Y') as text")
            )
                ->whereNotNull('jam_keluar')
                ->distinct()
                ->orderBy('id_bulan', 'desc')
                ->get()
                ->map(function ($item) {
                    return [
                        'id'   => $item->id_bulan,
                        'text' => $item->text,
                    ];
                });
        });

        if ($search) {
            $allMonths = collect($allMonths)->filter(function ($item) use ($search) {
                return stripos($item['text'], $search) !== false;
            });
        }

        return response()->json(collect($allMonths)->values()->all());
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
                'message' => 'Identitas karyawan tidak terdaftar di sistem.',
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
                        'message' => "Tap masuk terdeteksi kurang dari 1 menit yang lalu. Harap tunggu minimal 1 menit untuk melakukan tap keluar.",
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
                'message' => 'Absen keluar jam istirahat berhasil tercatat. Selamat beristirahat.',
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
                    'message' => "Tap keluar terdeteksi kurang dari 1 menit yang lalu. Harap tunggu minimal 1 menit untuk melakukan tap masuk kembali.",
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

            $responseMessage = ($status === 'Terlambat') ? "Absen masuk jam istirahat berhasil dicatat. Keterlambatan: {$minutesLate} menit. Silakan bekerja kembali." : "Absen masuk jam istirahat berhasil tepat waktu. Selamat bekerja kembali.";

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
            $query->where('jam_keluar', 'like', $req->tanggal . '%');
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

    public function exportExcel(Request $req)
    {
        $filters = [
            'tab'     => $req->input('tab', 'today'),
            'divisi'  => $req->input('divisi'),
            'status'  => $req->input('status'),
            'tanggal' => $req->input('tanggal'),
        ];

        $timePart = 'Semua Riwayat';
        if ($filters['tanggal']) {
            $timePart = 'Tanggal ' . Carbon::parse($filters['tanggal'])->format('Y-m-d');
        } elseif ($filters['tab'] === 'today') {
            $timePart = 'Hari Ini';
        }

        $divisiPart = '';
        if ($filters['divisi']) {
            $divisiPart = ' - Divisi ' . ucfirst($filters['divisi']);
        }

        $statusPart = '';
        if ($filters['status']) {
            $statusPart = ' - ' . ucfirst($filters['status']);
        }

        $fileName = 'Laporan Riwayat Istirahat Karyawan ' . $timePart . $divisiPart . $statusPart . ' - ' . date('Y-m-d') . '.xlsx';

        return Excel::download(new IzinKeluarExport($filters), $fileName);
    }

    public function sendEmail(Request $req)
    {
        $req->validate([
            'email' => 'required|email',
        ]);

        $filters = [
            'tab'     => $req->input('tab', 'today'),
            'divisi'  => $req->input('divisi'),
            'status'  => $req->input('status'),
            'tanggal' => $req->input('tanggal'),
        ];

        $timePart = 'Semua Riwayat';
        if ($filters['tanggal']) {
            $timePart = 'Tanggal ' . Carbon::parse($filters['tanggal'])->format('Y-m-d');
        } elseif ($filters['tab'] === 'today') {
            $timePart = 'Hari Ini';
        }

        $divisiPart = $filters['divisi'] ? ' - Divisi ' . ucfirst($filters['divisi']) : '';
        $statusPart = $filters['status'] ? ' - ' . ucfirst($filters['status']) : '';

        $fileName = 'Laporan Riwayat Istirahat Karyawan ' . $timePart . $divisiPart . $statusPart . ' - ' . date('Y-m-d') . '.xlsx';

        $tempPath = 'temp/' . uniqid() . '-' . $fileName;

        try {
            Excel::store(new IzinKeluarExport($filters), $tempPath, 'public');

            $realPath = storage_path('app/public/' . $tempPath);

            Mail::to($req->email)->send(new IzinKeluarReportMail($realPath, $fileName));

            Storage::disk('public')->delete($tempPath);

            return response()->json([
                'success' => true,
                'message' => 'Laporan berhasil dikirim ke email: ' . $req->email,
            ]);
        } catch (\Exception $e) {
            if (Storage::disk('public')->exists($tempPath)) {
                Storage::disk('public')->delete($tempPath);
            }

            return response()->json([
                'success' => false,
                'message' => 'Gagal mengirim email: ' . $e->getMessage(),
            ], 500);
        }
    }
}
