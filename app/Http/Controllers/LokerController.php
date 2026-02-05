<?php

namespace App\Http\Controllers;

use App\Department;
use App\Services\LokerCapacityService;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class LokerController extends Controller
{

    protected $capacityService;

    public function __construct(LokerCapacityService $capacityService)
    {
        $this->capacityService = $capacityService;
    }
    
    public function index()
    {
        $lokerQuery = DB::table('loker_rak as lr')
            ->leftJoin('loker_penghuni as lp', function($join) {
                $join->on('lr.kode_rak', '=', 'lp.kode_rak')
                    ->on('lr.no_loker', '=', 'lp.no_loker')
                    ->whereNull('lp.tgl_keluar');
            })
            ->select('lr.no_loker', 'lr.is_active', DB::raw('COUNT(lp.id) as terisi'), DB::raw('MIN(lp.staff) as kategori_staff'))
            ->groupBy('lr.no_loker', 'lr.is_active');

        // Hitung global
        // $lokerGlobal = (clone $lokerQuery)->get()->map(function($row) {
        //     if ($row->is_active === 'N') {
        //         $status = 'perbaikan';
        //     } elseif ($row->terisi >= ($row->kategori_staff === 'staff' ? 1 : 2)) {
        //         $status = 'terisi';
        //     } else {
        //         $status = 'tersedia';
        //     }

        //     return $status;
        // });

        // $countTersedia = $lokerGlobal->filter(fn($s) => $s === 'tersedia')->count();
        // $countTerisi    = $lokerGlobal->filter(fn($s) => $s === 'terisi')->count();
        // $countPerbaikan = $lokerGlobal->filter(fn($s) => $s === 'perbaikan')->count();


        // Hitung per gender
        $genders = ['pria' => 'P%', 'wanita' => 'W%'];
        $countPerGender = [];

        foreach ($genders as $key => $prefix) {
            $lokerGender = (clone $lokerQuery)
                ->where('lr.kode_rak', 'LIKE', $prefix)
                ->get()
                ->map(function ($row) {
                    // PRIORITAS 1: perbaikan
                    if ($row->is_active === 'N') {
                        return 'perbaikan';
                    }

                    // PRIORITAS 2: hitung kapasitas hanya kalau aktif
                    $max = ($row->kategori_staff === 'staff') ? 1 : 2;

                    if ($row->terisi >= $max && $row->terisi > 0) {
                        return 'terisi';
                    }

                    return 'tersedia';
                });

            $countPerGender[$key] = [
                'tersedia' => $lokerGender->filter(fn($s) => $s === 'tersedia')->count(),
                'terisi' => $lokerGender->filter(fn($s) => $s === 'terisi')->count(),
                'perbaikan' => $lokerGender->filter(fn($s) => $s === 'perbaikan')->count(),
            ];
        }

        // Hitung total tersedia, terisi, perbaikan
        $countTersedia  = array_sum(array_column($countPerGender, 'tersedia'));
        $countTerisi    = array_sum(array_column($countPerGender, 'terisi'));
        $countPerbaikan = array_sum(array_column($countPerGender, 'perbaikan'));

        $departments = Department::where('status', '1')->get();

        return view('loker.index', compact(
            'countTersedia',
            'countTerisi',
            'countPerbaikan',
            'countPerGender',
            'departments'
        ));

        return view('loker.index', compact(
            'countTersedia', 'countTerisi', 'countPerbaikan', 'countPerGender'
        ));
    }

    public function getBlokByGender($gender)
    {
        $prefix = $gender === 'pria' ? 'P' : 'W';

        $bloks = DB::table('loker_rak')
            ->selectRaw(
                "
            SUBSTRING_INDEX(kode_blok, ' ', -1) as blok_nomor
        ",
            )
            ->where('kode_rak', 'LIKE', $prefix . '%')
            // ->where('is_active', 'Y')
            ->groupBy('blok_nomor')
            ->orderByRaw("CAST(SUBSTRING_INDEX(blok_nomor, '-', 1) AS UNSIGNED)")
            ->get();

        return response()->json($bloks);
    }

    public function getNomorByBlok(Request $request)
    {
        $gender = $request->gender; // pria | wanita
        $blok = $request->blok; // contoh: "11-20"

        $kodeRak = $gender === 'pria' ? ['PB'] : ['WB'];

        $data = DB::table('loker_rak as lr')
            ->leftJoin('loker_penghuni as lp', function ($join) {
                $join->on('lr.kode_rak', '=', 'lp.kode_rak')->on('lr.no_loker', '=', 'lp.no_loker')->whereNull('lp.tgl_keluar');
            })
            ->select('lr.no_loker', 'lr.is_active', DB::raw('COUNT(lp.id) as terisi'), DB::raw('MIN(lp.staff) as kategori_staff'), DB::raw('COUNT(DISTINCT lp.staff) as staff_count'))
            ->whereIn('lr.kode_rak', $kodeRak)
            ->where('lr.kode_blok', 'LIKE', '%' . $blok)
            ->groupBy('lr.no_loker', 'lr.is_active')
            ->orderBy('lr.no_loker')
            ->get()
            ->map(function ($row) {
                // tentukan kapasitas berdasarkan kategori
                if ($row->kategori_staff === 'staff') {
                    $kapasitas = 1;
                } elseif (in_array($row->kategori_staff, ['non_staff', 'mitra_kerja'])) {
                    $kapasitas = 2;
                } else {
                    // belum ada penghuni
                    $kapasitas = 2; // default maksimum
                }

                // validasi kategori ganda (harusnya tidak pernah)
                $invalidKategori = $row->staff_count > 1;

                if ($row->is_active === 'N') {
                    $status = 'perbaikan';
                } elseif ($row->terisi >= $kapasitas) {
                    $status = 'terisi';
                } else {
                    $status = 'tersedia';
                }

                return [
                    'no_loker' => $row->no_loker,
                    'kapasitas' => $kapasitas,
                    'terisi' => (int) $row->terisi,
                    'status' => $status,
                    'kategori_staff' => $row->kategori_staff, // null kalau kosong
                    'invalid_kategori' => $invalidKategori,
                ];
            });

        return response()->json($data);
    }

    public function getPenghuni(Request $request)
    {
        $gender = $request->gender;
        $noLoker = $request->no_loker;

        $kodeRak = $gender === 'pria' ? ['PB'] : ['WB'];

        $data = DB::table('loker_penghuni')->select('nama', 'nik', 'divisi', 'staff')->whereIn('kode_rak', $kodeRak)->where('no_loker', $noLoker)->whereNull('tgl_keluar')->where('is_active', 'Y')->orderBy('nama')->get();

        // dd($gender, $noLoker, $kodeRak, $data);

        return response()->json($data);
    }

    public function getDetailLoker($gender, $blok, $no_loker)
    {
        $kodeRak = $gender === 'pria' ? ['PB'] : ['WB'];

        $penghuni = DB::table('loker_penghuni')
            ->select('nama', 'nik', 'divisi as dept', 'staff')
            ->whereIn('kode_rak', $kodeRak)
            ->where('no_loker', $no_loker)
            ->whereNull('tgl_keluar')
            ->where('is_active', 'Y')
            ->orderBy('nama')
            ->get();

        $terisi = $penghuni->count();

        $staffType = $penghuni->first()->staff ?? null;

        $max = $staffType
            ? $this->capacityService->resolveMaxCapacity($staffType)
            : 2; // default saat kosong

        return response()->json([
            'kode_blok'     => $blok,
            'no_loker'      => (int) $no_loker,
            'jenis_kelamin' => ucfirst($gender),
            'status'        => $terisi >= $max ? 'terisi' : 'tersedia',
            'kapasitas'     => $max,
            'terisi'        => $terisi,
            'penghuni'      => $penghuni,
        ]);
    }

    public function getFoto($nik)
    {
        try {
            $user = DB::connection('192.168.178.44-admin')
                ->table('MSIDCARD')
                ->select('FOTOBLOB')
                ->whereRaw('CAST(BARCODE AS SIGNED) = ?', [$nik])
                ->first();

            if (!$user || !$user->FOTOBLOB) {
                return response()->json([
                    'success' => false
                ]);
            }

            return response()->json([
                'success' => true,
                'image' => 'data:image/jpeg;base64,' . base64_encode($user->FOTOBLOB)
            ]);

        } catch (\Throwable $e) {
            return response()->json([
                'success' => false
            ]);
        }
    }

   public function tandaiRusak(Request $request)
    {
        $request->validate([
            'gender'   => 'required',
            'blok'     => 'required',
            'no_loker' => 'required',
        ]);

        $kodeRak = $request->gender === 'pria' ? 'PB' : 'WB';
        $kodeBlok = $kodeRak . ' ' . $request->blok;

        DB::beginTransaction();

        try {
            // cek penghuni aktif
            $hasActivePenghuni = DB::table('loker_penghuni')
                ->where('kode_rak', $kodeRak)
                // ->where('kode_blok', $kodeBlok)
                ->where('no_loker', $request->no_loker)
                // ->where('is_active', 'Y')
                ->lockForUpdate()
                ->exists();

            if ($hasActivePenghuni) {
                DB::rollBack();

                return response()->json([
                    'status'  => 'error',
                    'message' => 'Loker masih memiliki penghuni aktif. Silakan keluarkan penghuni terlebih dahulu.',
                ], 422);
            }

            // update status loker
            DB::table('loker_rak')
                ->where('kode_rak', $kodeRak)
                ->where('kode_blok', $kodeBlok)
                ->where('no_loker', $request->no_loker)
                ->update([
                    'is_active'  => 'N',
                ]);

            DB::commit();

            return response()->json([
                'status'  => 'success',
                'message' => 'Loker berhasil ditandai rusak.',
            ]);

        } catch (\Throwable $e) {
            DB::rollBack();

            return response()->json([
                'status'  => 'error',
                'message' => 'Terjadi kesalahan saat menandai loker.' . $e->getMessage(),
            ], 500);
        }
    }

    public function tandaiAktif(Request $request)
    {
        $request->validate([
            'gender'   => 'required|in:pria,wanita',
            'blok'     => 'required',
            'no_loker' => 'required|integer',
        ]);

        $kodeRak  = $request->gender === 'pria' ? 'PB' : 'WB';
        $kodeBlok = $kodeRak . ' ' . $request->blok;

        $hasPenghuni = DB::table('loker_penghuni')
            ->where('kode_rak', $kodeRak)
            // ->where('kode_blok', $kodeBlok)
            ->where('no_loker', $request->no_loker)
            // ->where('is_active', 'Y')
            ->exists();

        if ($hasPenghuni) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Tidak bisa mengaktifkan loker yang masih memiliki penghuni.'
            ], 422);
        }

        DB::table('loker_rak')
            ->where('kode_rak', $kodeRak)
            ->where('kode_blok', $kodeBlok)
            ->where('no_loker', $request->no_loker)
            ->update([
                'is_active'  => 'Y',
                // 'updated_at' => now(),
            ]);

        return response()->json([
            'status'  => 'success',
            'message' => 'Loker berhasil diaktifkan kembali.'
        ]);
    }

    public function tarikKunci(Request $request)
    {
        $request->validate([
            'nik' => 'required|string',
            'alasan' => 'required|string',
        ]);

        $nik = $request->nik;
        $alasan = $request->alasan;

        DB::transaction(function() use ($nik, $alasan) {
            // Ambil data loker yang dihuni
            $penghuni = DB::table('loker_penghuni')->where('nik', $nik)->first();

            if ($penghuni) {
                // Insert ke loker_user_transaksi (status out)
                DB::table('loker_user_transaksi')->insert([
                    'nik' => $nik,
                    'no_loker' => $penghuni->no_loker,
                    'status' => 'OUT',
                    'keterangan' => $alasan,
                    'nama_pengisi' => auth()->user()->name ?? '',
                    'nik_pengisi' => auth()->user()->username ?? '',
                    'tgl_pengisi' => now()->format('Y-m-d'),
                    'jam_pengisi' => now()->format('H:i:s'),
                    'pindah_to' => null,
                    'penghuni_sebelumnya' => $penghuni->nama ?? '',
                    'alasan' => $alasan,
                    'kode_area' => $penghuni->kode_area ?? '',
                    'kode_blok' => $penghuni->kode_blok ?? '',
                    'kode_rak' => $penghuni->kode_rak ?? '',
                ]);

                // Hapus dari loker_penghuni
                DB::table('loker_penghuni')->where('nik', $nik)->delete();
            }
        });

        return response()->json([
            'status' => 'success',
            'message' => 'Kunci berhasil ditarik dan transaksi tercatat'
        ]);
    }

    public function getAvailableLoker(Request $req)
    {
        $gender = $req->gender; // L / P

        $kodeRak = $gender === 'L' ? 'PB' : 'WB';

        $lockers = DB::table('loker_rak as lr')
            ->leftJoin('loker_penghuni as lp', function ($join) {
                $join->on('lr.kode_rak', '=', 'lp.kode_rak')
                    ->on('lr.no_loker', '=', 'lp.no_loker')
                    ->where('lp.is_active', 'Y');
            })
            ->where('lr.kode_rak', $kodeRak)
            ->where('lr.is_active', 'Y')
            ->select(
                'lr.id',
                'lr.kode_rak',
                'lr.no_loker',
                DB::raw('COUNT(lp.nik) as total_penghuni'),
                DB::raw('MAX(lp.staff) as staff_type')
            )
            ->groupBy('lr.id', 'lr.kode_rak', 'lr.no_loker')
            ->havingRaw('
                COUNT(lp.nik) <
                CASE
                    WHEN MAX(lp.staff) = "staff" THEN 1
                    WHEN MAX(lp.staff) IN ("non_staff","mitra_kerja") THEN 2
                    ELSE 2
                END
            ')
            ->orderBy('lr.no_loker')
            ->get();

        return response()->json($lockers);
    }
}
