<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\HR\Karyawan;
use App\Models\Loker\Rak;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class LokerController extends Controller
{
    private function getPrefix($gender)
    {
        $gender = strtolower($gender);
        return ($gender === 'l' || $gender === 'pria') ? 'LP' : 'LW';
    }

    public function index()
    {
        $genders       = ['L' => 'Pria', 'P' => 'Wanita'];
        $dashboardData = [];
        $grandTotal    = ['total' => 0, 'penuh' => 0, 'tersedia' => 0, 'rusak' => 0];

        $allLockers = Rak::leftJoin('loker_penghuni', function ($join) {
            $join->on('loker_rak.kode_rak', '=', 'loker_penghuni.kode_rak')
                ->on('loker_rak.no_loker', '=', 'loker_penghuni.no_loker')
                ->whereNull('loker_penghuni.tgl_keluar')
                ->where('loker_penghuni.is_active', 'Y');
        })
            ->select('loker_rak.*', DB::raw('COUNT(loker_penghuni.id) as terisi'))
            ->groupBy('loker_rak.id', 'loker_rak.kode_rak', 'loker_rak.no_loker', 'loker_rak.kode_blok', 'loker_rak.is_active', 'loker_rak.kapasitas')
            ->orderByRaw('CAST(loker_rak.no_loker AS UNSIGNED) ASC')
            ->get();

        foreach ($genders as $code => $label) {
            $prefix   = $this->getPrefix($code);
            $filtered = $allLockers->where('kode_rak', $prefix);

            $processed = $filtered->map(function ($rak) use ($label) {
                $status = 'tersedia';
                if ($rak->is_active === 'N') {
                    $status = 'rusak';
                } elseif ($rak->terisi >= $rak->kapasitas) {
                    $status = 'penuh';
                }

                return [
                    'id'     => $rak->id,
                    'no'     => $rak->no_loker,
                    'block'  => $rak->kode_blok,
                    'count'  => (int) $rak->terisi,
                    'max'    => (int) $rak->kapasitas,
                    'status' => $status,
                    'gender' => $label,
                ];
            });

            $stats = [
                'total'    => $processed->count(),
                'penuh'    => $processed->where('status', 'penuh')->count(),
                'tersedia' => $processed->where('status', 'tersedia')->count(),
                'rusak'    => $processed->where('status', 'rusak')->count(),
            ];

            foreach ($stats as $key => $val) {
                $grandTotal[$key] += $val;
            }

            $dashboardData[$label] = [
                'blocks' => $processed->groupBy('block'),
                'stats'  => $stats,
            ];
        }

        return view('loker.index', compact('dashboardData', 'grandTotal'));
    }

    public function getBlokByGender($gender)
    {
        $prefix = $this->getPrefix($gender);
        $bloks  = DB::table('loker_rak')
            ->selectRaw("SUBSTRING_INDEX(kode_blok, ' ', -1) as blok_nomor")
            ->where('kode_rak', $prefix)
            ->groupBy('blok_nomor')
            ->orderByRaw("CAST(SUBSTRING_INDEX(blok_nomor, '-', 1) AS UNSIGNED)")
            ->get();

        return response()->json($bloks);
    }

    public function getNomorByBlok(Request $request)
    {
        $prefix = $this->getPrefix($request->gender);
        $blok   = $request->blok;

        $data = DB::table('loker_rak as lr')
            ->leftJoin('loker_penghuni as lp', function ($join) {
                $join->on('lr.kode_rak', '=', 'lp.kode_rak')
                    ->on('lr.no_loker', '=', 'lp.no_loker')
                    ->whereNull('lp.tgl_keluar')
                    ->where('lp.is_active', 'Y');
            })
            ->select('lr.no_loker', 'lr.is_active', 'lr.kapasitas', DB::raw('COUNT(lp.id) as terisi'))
            ->where('lr.kode_rak', $prefix)
        // Optimasi: Gunakan LIKE untuk menghindari spasi ganda atau format yang agak beda
            ->where('lr.kode_blok', 'LIKE', "%{$blok}%")
            ->groupBy('lr.no_loker', 'lr.is_active', 'lr.kapasitas')
            ->orderByRaw('CAST(lr.no_loker AS UNSIGNED) ASC')
            ->get()
            ->map(function ($row) {
                return [
                    'no_loker'  => $row->no_loker,
                    'kapasitas' => (int) $row->kapasitas,
                    'terisi'    => (int) $row->terisi,
                    'status'    => ($row->is_active === 'N') ? 'perbaikan' : (($row->terisi >= $row->kapasitas) ? 'penuh' : 'tersedia'),
                ];
            });

        return response()->json($data);
    }

    public function getPenghuni(Request $request)
    {
        $prefix = $this->getPrefix($request->gender);

        $data = DB::table('loker_penghuni')
            ->select('nama', 'nik', 'divisi', 'kategori_karyawan')
            ->where('kode_rak', $prefix)
            ->where('no_loker', $request->no_loker)
            ->whereNull('tgl_keluar')
            ->where('is_active', 'Y')
            ->orderBy('nama')
            ->get();

        return response()->json($data);
    }

    public function getFoto($nik)
    {
        // Cache hanya menyimpan string Base64, bukan Object Response
        $imageData = Cache::remember("foto_karyawan_{$nik}", 3600, function () use ($nik) {
            try {
                $user = DB::connection('192.168.178.44-admin')
                    ->table('MSIDCARD')
                    ->select('FOTOBLOB')
                    ->whereRaw('CAST(BARCODE AS SIGNED) = ?', [$nik])
                    ->first();

                return ($user && $user->FOTOBLOB) ? 'data:image/jpeg;base64,' . base64_encode($user->FOTOBLOB) : null;
            } catch (\Throwable $e) {
                return 'error';
            }
        });

        if (! $imageData || $imageData === 'error') {
            return response()->json(['success' => false, 'message' => $imageData === 'error' ? 'DB Error' : 'No Image']);
        }

        return response()->json(['success' => true, 'image' => $imageData]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'nik'      => 'required',
            'nama'     => 'required',
            'no_loker' => 'required',
            'gender'   => 'required',
        ]);

        try {
            DB::table('loker_penghuni')->insert([
                'kode_rak'          => $this->getPrefix($request->gender),
                'no_loker'          => $request->no_loker,
                'nik'               => $request->nik,
                'nama'              => $request->nama,
                'divisi'            => $request->dept ?? '-',
                'kategori_karyawan' => $request->kategori ?? 'non_staff',
                'is_active'         => 'Y',
                'tgl_masuk'         => now(), // Tambahkan tgl_masuk jika ada di migrasi lo
                'created_at'        => now(),
                'updated_at'        => now(),
            ]);

            return response()->json(['status' => 'success', 'message' => 'Data loker berhasil disimpan!']);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    public function apiSuggestLoker(Request $request)
    {
        $prefix   = $this->getPrefix($request->gender);
        $kategori = $request->kategori;

        $query = Rak::where('loker_rak.kode_rak', $prefix)
            ->where('loker_rak.is_active', 'Y')
            ->leftJoin('loker_penghuni', function ($join) {
                $join->on('loker_rak.kode_rak', '=', 'loker_penghuni.kode_rak')
                    ->on('loker_rak.no_loker', '=', 'loker_penghuni.no_loker')
                    ->where('loker_penghuni.is_active', 'Y')
                    ->whereNull('loker_penghuni.tgl_keluar');
            })
            ->select('loker_rak.no_loker', 'loker_rak.kapasitas')
            ->selectRaw('COUNT(loker_penghuni.id) as terisi')
            ->groupBy('loker_rak.no_loker', 'loker_rak.kapasitas');

        if ($kategori == 'staff') {
            $query->havingRaw('COUNT(loker_penghuni.id) = 0');
        } else {
            $query->havingRaw('COUNT(loker_penghuni.id) < loker_rak.kapasitas');
        }

        $suggest = $query->orderByRaw('CAST(loker_rak.no_loker AS UNSIGNED) ASC')->first();

        return response()->json([
            'status'            => 'success',
            'rekomendasi_loker' => $suggest ? $suggest->no_loker : 'penuh',
        ]);
    }

    public function tarikKunci(Request $request)
    {
        $request->validate([
            'nik'    => 'required|string',
            'alasan' => 'required|string|max:255',
        ]);

        return DB::transaction(function () use ($request) {
            // Hanya ambil record yang beneran aktif
            $penghuniActive = DB::table('loker_penghuni')
                ->where('nik', $request->nik)
                ->where('is_active', 'Y')
                ->whereNull('tgl_keluar')
                ->get();

            if ($penghuniActive->isEmpty()) {
                return response()->json(['status' => 'error', 'message' => 'Karyawan tidak memiliki loker aktif.'], 422);
            }

            foreach ($penghuniActive as $p) {
                DB::table('loker_transaksi')->insert([
                    'nik'            => $request->nik,
                    'nama'           => $p->nama,
                    'kode_rak'       => $p->kode_rak,
                    'no_loker'       => $p->no_loker,
                    'tipe_transaksi' => 'KELUAR',
                    'operator'       => auth()->user()->name ?? 'Admin IT',
                    'keterangan'     => 'Penarikan: ' . $request->alasan,
                    'created_at'     => now(),
                ]);
            }

            DB::table('loker_penghuni')
                ->where('nik', $request->nik)
                ->where('is_active', 'Y') // Filter tambahan biar aman
                ->update([
                    'is_active'  => 'N',
                    'tgl_keluar' => now(),
                    'updated_at' => now(),
                ]);

            return response()->json(['status' => 'success', 'message' => 'Kunci berhasil ditarik.']);
        });
    }

    public function searchKaryawan($nik)
    {
        $karyawan = Karyawan::where('nik', $nik)->where('active', 'Y')->first();

        if (! $karyawan) {
            return response()->json(['success' => false, 'message' => 'Karyawan tidak ditemukan!']);
        }

        return response()->json([
            'success' => true,
            'data'    => [
                'nik'      => $karyawan->nik,
                'nama'     => $karyawan->nama,
                'gender'   => $karyawan->jenis_kelamin,
                'kategori' => ($karyawan->staff == 'Y') ? 'staff' : 'non_staff',
                'dept'     => $karyawan->kode_divisi,
            ],
        ]);
    }

    public function getDetailLoker($gender, $no_loker)
    {
        if (! $gender || ! $no_loker) {
            return response()->json(['error' => 'Parameter tidak lengkap'], 400);
        }

        // Langsung panggil logic getPenghuni tanpa merge request untuk efisiensi
        return $this->getPenghuni(new Request(['gender' => $gender, 'no_loker' => $no_loker]));
    }
}
