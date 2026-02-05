<?php

namespace App\Http\Controllers\HRConnect;

use App\Department;
use App\Http\Controllers\Controller;
use App\Imports\LokerUserImportNew;
use App\Services\LokerAssignmentService;
use App\Services\LokerCapacityService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\DataTables;

class LokerMasterUserController extends Controller
{

    protected $capacityService;

    public function __construct(LokerCapacityService $capacityService)
    {
        $this->capacityService = $capacityService;
    }


    public function index()
    {
        $departments = Department::where('status', '1')->get();
        return view('hr-connect.masters.user', compact('departments'));
    }

    public function getData(Request $request)
    {
        $subQuery = DB::table('loker_penghuni')
            ->select(
                'nik',
                'nama',
                'divisi',
                'jk',
                'staff',
                DB::raw("
                    MAX(CASE
                        WHEN kode_rak IN ('PB','WB')
                        THEN CAST(no_loker AS UNSIGNED)
                    END) AS no_loker
                "),
                DB::raw("
                    MAX(CASE
                        WHEN kode_rak IN ('PB','WB')
                        THEN CONCAT(kode_rak, '-', no_loker)
                    END) AS loker_baju
                "),
                DB::raw("
                    MAX(CASE
                        WHEN kode_rak IN ('PS','WS')
                        THEN CONCAT(kode_rak, '-', no_loker)
                    END) AS loker_sepatu
                ")
            )
            ->where('is_active', 'Y')

            ->when($request->jk, function ($q) use ($request) {
                $q->where('jk', $request->jk);
            })

            ->groupBy('nik', 'nama', 'divisi', 'jk', 'staff');

        $query = DB::query()
            ->fromSub($subQuery, 'locker_view')
            ->orderBy('no_loker', 'asc');

        return DataTables::of($query)
            ->orderColumn('loker_baju', function ($q, $order) {
                $q->orderBy('no_loker', $order);
            })
            ->addColumn('action', function ($row) {
                return '
                    <button class="btn btn-sm btn-success btnEdit" data-nik="' . $row->nik . '">Edit</button>
                    <button class="btn btn-sm btn-danger btnDelete" data-nik="' . $row->nik . '">Delete</button>
                ';
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    public function store(Request $request)
    {
        $request->validate([
            'nik' => 'required',
            'nama' => 'required',
            'divisi' => 'required',
            'jk' => 'required|in:L,P',
            'no_loker' => 'required|integer',
            'staff' => 'required|in:staff,non_staff,mitra_kerja',
        ]);

        app(LokerAssignmentService::class)->assign([
            'nik' => $request->nik,
            'nama' => $request->nama,
            'divisi' => $request->divisi,
            'jk' => $request->jk,
            'no_loker' => $request->no_loker,
            'staff' => $request->staff,
        ]);
    }

    public function show($id)
    {
        $data = DB::table('loker_penghuni')->where('id', $id)->first();

        if (!$data) {
            return response()->json(
                [
                    'message' => 'Data tidak ditemukan',
                ],
                404,
            );
        }

        return response()->json($data);
    }

    public function getByNik($nik)
    {
        $data = DB::table('loker_penghuni')->where('nik', $nik)->first();

        if (!$data) {
            return response()->json(
                [
                    'message' => 'Data tidak ditemukan',
                ],
                404,
            );
        }

        return response()->json($data);
    }

    public function update(Request $request, string $nik)
    {
        $request->validate([
            'nik' => 'required',
            'nama' => 'required',
            'divisi' => 'required',
            'jk' => 'required|in:L,P',
            'no_loker' => 'required|integer|min:1',
            'staff' => 'required|in:staff,non_staff,mitra_kerja',
        ]);

        // Rak baru
        $kodeRakUtama = $request->jk === 'L' ? 'PB' : 'WB';
        $kodeRakPair  = $this->pairRak($kodeRakUtama);
        $rakDicek    = array_filter([$kodeRakUtama, $kodeRakPair]);

        try {
            DB::transaction(function () use ($request, $kodeRakUtama, $kodeRakPair, $rakDicek) {

                // Ambil data lama user
                $oldRecord = DB::table('loker_penghuni')
                    ->where('nik', $request->nik)
                    ->where('is_active', 'Y')
                    ->first();

                if (!$oldRecord) {
                    throw new \Exception("Data loker lama tidak ditemukan.");
                }

                // Cek isi loker TUJUAN (kecuali NIK ini sendiri)
                $rows = DB::table('loker_penghuni')
                    ->select('staff', DB::raw('COUNT(DISTINCT nik) as cnt'))
                    ->whereIn('kode_rak', $rakDicek)
                    ->where('no_loker', (int) $request->no_loker)
                    ->where('nik', '!=', $request->nik)
                    ->groupBy('staff')
                    ->lockForUpdate()
                    ->get();

                // tidak boleh campur kategori
                if ($rows->count() > 1) {
                    throw new \Exception(
                        "Loker {$kodeRakUtama}-{$request->no_loker} tidak valid karena terdapat campuran kategori."
                    );
                }

                $existingType  = $rows->first()->staff ?? null;
                $existingCount = (int) ($rows->first()->cnt ?? 0);

                // tipe berbeda
                if ($existingType !== null && $existingType !== $request->staff) {
                    $staffLabel = ucwords(str_replace('_', ' ', $existingType));
                    throw new \Exception(
                        "Loker {$kodeRakUtama}-{$request->no_loker} sudah dipakai oleh {$staffLabel}."
                    );
                }

                // Cek kapasitas
                switch ($request->staff) {
                    case 'staff':
                        $maxCapacity = 1;
                        break;
                    case 'non_staff':
                    case 'mitra_kerja':
                        $maxCapacity = 2;
                        break;
                    default:
                        throw new \Exception('Kategori karyawan tidak valid.');
                }

                if ($existingCount >= $maxCapacity) {
                    throw new \Exception(
                        "Loker {$kodeRakUtama}-{$request->no_loker} sudah penuh."
                    );
                }

                // Hapus loker lama (UTAMA + PASANGAN)
                DB::table('loker_penghuni')
                    ->where('nik', $request->nik)
                    ->where('is_active', 'Y')
                    ->delete();

                // Insert rak utama baru
                DB::table('loker_penghuni')->insert([
                    'nik' => $request->nik,
                    'nama' => $request->nama,
                    'divisi' => $request->divisi,
                    'jk' => $request->jk,
                    'staff' => $request->staff,
                    'kode_rak' => $kodeRakUtama,
                    'no_loker' => (int) $request->no_loker,
                    'is_active' => 'Y',
                    'tgl_masuk' => $oldRecord->tgl_masuk,
                    'created_at' => $oldRecord->created_at,
                    'updated_at' => now(),
                ]);

                // Insert rak pasangan
                if ($kodeRakPair) {
                    DB::table('loker_penghuni')->insert([
                        'nik' => $request->nik,
                        'nama' => $request->nama,
                        'divisi' => $request->divisi,
                        'jk' => $request->jk,
                        'staff' => $request->staff,
                        'kode_rak' => $kodeRakPair,
                        'no_loker' => (int) $request->no_loker,
                        'is_active' => 'Y',
                        'tgl_masuk' => $oldRecord->tgl_masuk,
                        'created_at' => $oldRecord->created_at,
                        'updated_at' => now(),
                    ]);
                }
            });

            return response()->json([
                'status' => 'success',
                'message' => 'Data loker user berhasil diperbarui',
            ]);
        } catch (\Throwable $e) {
            return response()->json(
                [
                    'status' => 'error',
                    'message' => $e->getMessage(),
                ],
                422,
            );
        }
    }

    public function destroy($id)
    {
        DB::table('loker_penghuni')->where('id', $id)->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Data loker user berhasil dihapus',
        ]);
    }

    public function destroyByNik(string $nik)
    {
        DB::transaction(function () use ($nik) {
            DB::table('loker_penghuni')->where('nik', $nik)->delete();
        });

        return response()->json([
            'status' => 'success',
            'message' => 'Semua loker user berhasil dihapus',
        ]);
    }

    private function pairRak($kodeRak)
    {
        $map = [
            'PB' => 'PS',
            'WB' => 'WS',
        ];

        return $map[$kodeRak] ?? null;
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls'
        ]);

        try {
            Excel::import(
                new LokerUserImportNew($this),
                $request->file('file')
            );

            return response()->json([
                'status' => 'success',
                'message' => 'Import data loker berhasil'
            ]);

        } catch (\Throwable $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error import loker: ' . $e->getMessage()
            ], 422);
        }
    }
}
