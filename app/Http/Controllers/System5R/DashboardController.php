<?php

namespace App\Http\Controllers\System5R;

use Illuminate\Http\Request;
use App\Models\System5R\Jadwal;
use App\Models\System5R\Jawaban;
use App\Models\System5R\Periode;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\System5R\MasterGroup;
use App\Models\System5R\JawabanGroup;
use App\Models\System5R\MasterArea;
use App\Models\System5R\MasterWorkspace;
use App\Models\System5R\MasterDepartment;
use App\Models\System5R\MasterGroupJuriDepartment;

class DashboardController extends Controller
{
    public function index()
    {
        $allJadwal = Jadwal::orderBy('tahun', 'desc')->get();
        $workspaces = MasterWorkspace::where('is_active', 'Y')->get();

        return view(
            'system5r.dashboard.index',
            compact('allJadwal', 'workspaces')
        );
    }

    public function getDataWidget()
    {
        $totalWorkspace = MasterWorkspace::where('is_active', 'Y')->count();
        $totalDepartment = MasterDepartment::where('is_active', 'Y')->count();
        $totalGroup = MasterGroup::where('is_active', 'Y')->count();
        $totalArera = MasterArea::where('is_active', 'Y')->count();

        return response()->json([
            'status' => 'success',
            'widget' => [
                'workspace' => $totalWorkspace,
                'department' => $totalDepartment,
                'group' => $totalGroup,
                'area' => $totalArera,
            ]
        ]);
    }

    public function getDataPeriodeByWorkspace(Request $request)
    {
        $workspaceId = $request->id_workspace;
        $jadwalId    = $request->id_jadwal;

        if (!$jadwalId) {
            $jadwalId = DB::table('5r_jadwal_penilaian')
                ->orderByDesc('tahun')
                ->value('id_jadwal');

            if (!$jadwalId) {
                return response()->json([
                    'status'   => 'success',
                    'labels'   => [],
                    'datasets' => []
                ]);
            }
        }

        if (!$workspaceId) {
            return response()->json(['status' => 'error', 'message' => 'Workspace required'], 422);
        }

        // 1. Ambil semua departemen di workspace
        $allDepartments = DB::table('5r_master_department')
            ->where('id_workspace', $workspaceId)
            ->orderBy('nama_department')
            ->pluck('nama_department', 'id_department');

        if ($allDepartments->isEmpty()) {
            return response()->json([
                'status'   => 'success',
                'labels'   => [],
                'datasets' => []
            ]);
        }

        // 2. Query nilai dengan binding aman untuk id_workspace string
        $rows = DB::table('5r_periode_penilaian as p')
            ->join('5r_master_department as d', function ($join) use ($workspaceId) {
                $join->on('d.id_workspace', '=', DB::raw('?'))
                    ->addBinding($workspaceId);
            })
            ->join('5r_master_group as g', 'g.id_department', '=', 'd.id_department')
            ->join('5r_jawaban_group as jg', function ($join) {
                $join->on('jg.id_group', '=', 'g.id_group')
                    ->on('jg.id_periode', '=', 'p.id_periode')
                    ->where('jg.status', '=', 'approved');
            })
            ->join('5r_jawaban as j', 'j.id_jawaban_group', '=', 'jg.id_jawaban_group')
            ->where('p.id_jadwal', $jadwalId)
            ->select(
                'p.nama_periode',
                'd.id_department',
                'd.nama_department',
                DB::raw('SUM(j.nilai) as total_skor')
            )
            ->groupBy('p.id_periode', 'p.nama_periode', 'd.id_department', 'd.nama_department')
            ->get();

        // 3. Ambil semua periode
        $periodes = DB::table('5r_periode_penilaian')
            ->where('id_jadwal', $jadwalId)
            ->orderBy('nama_periode')
            ->pluck('nama_periode');

        // 4. Bangun data chart — semua departemen muncul
        $datasets = [];

        foreach ($periodes as $periode) {
            $data = [];

            foreach ($allDepartments as $deptId => $deptName) {
                $row = $rows->first(fn($r) => $r->nama_periode === $periode && $r->id_department == $deptId);

                $totalSkor = $row ? (float) $row->total_skor : 0;

                // (total + 28) * faktor, tapi kalau total = 0 → nilai akhir = 0 (biar chart kelihatan belum dinilai)
                $baseNilai = $totalSkor > 0 ? ($totalSkor + 28) : 0;

                $deptUpper = strtoupper($deptName);

                if (str_contains($deptUpper, 'HRGA') || str_contains($deptUpper, 'GA')) {
                    $nilaiAkhir = $baseNilai * 105;
                } elseif (str_contains($deptUpper, 'PRD') || str_contains($deptUpper, 'PROD')) {
                    $nilaiAkhir = $baseNilai * 110;
                } else {
                    $nilaiAkhir = $baseNilai * 100;
                }

                $data[] = round($nilaiAkhir, 2);
            }

            $datasets[] = [
                'label' => $periode,
                'data'  => $data
            ];
        }

        return response()->json([
            'status'   => 'success',
            'labels'   => $allDepartments->values()->toArray(),
            'datasets' => $datasets
        ]);
    }

    public function getDataRankPeriodeByWorkspace(Request $request)
    {
        $workspaceId = $request->id_workspace;
        $jadwalId    = $request->id_jadwal;

        // Ambil jadwal terbaru jika tidak ada
        if (!$jadwalId) {
            $jadwalId = DB::table('5r_jadwal_penilaian')
                ->orderByDesc('tahun')
                ->value('id_jadwal');

            if (!$jadwalId) {
                return response()->json([
                    'status' => 'success',
                    'labels' => [],
                    'data'   => []
                ]);
            }
        }

        if (!$workspaceId) {
            return response()->json(['status' => 'error', 'message' => 'Workspace required'], 422);
        }

        // 1. Ambil SEMUA departemen di workspace ini (wajib muncul semua di ranking)
        $allDepartments = DB::table('5r_master_department')
            ->where('id_workspace', $workspaceId)
            ->orderBy('nama_department')
            ->pluck('nama_department', 'id_department');

        if ($allDepartments->isEmpty()) {
            return response()->json([
                'status' => 'success',
                'labels' => [],
                'data'   => []
            ]);
        }

        // 2. Hitung nilai aktual (hanya dari jawaban approved di jadwal ini)
        $rows = DB::table('5r_master_department as d')
            ->join('5r_master_group as g', 'g.id_department', '=', 'd.id_department')
            ->join('5r_jawaban_group as jg', function ($join) use ($jadwalId) {
                $join->on('jg.id_group', '=', 'g.id_group')
                    ->where('jg.status', '=', 'approved')
                    ->whereExists(function ($query) use ($jadwalId) {
                        $query->select(DB::raw(1))
                            ->from('5r_periode_penilaian as p')
                            ->whereColumn('p.id_periode', 'jg.id_periode')
                            ->where('p.id_jadwal', $jadwalId);
                    });
            })
            ->join('5r_jawaban as j', 'j.id_jawaban_group', '=', 'jg.id_jawaban_group')
            ->where('d.id_workspace', $workspaceId)
            ->select(
                'd.id_department',
                'd.nama_department',
                DB::raw('SUM(j.nilai) as total_skor')
            )
            ->groupBy('d.id_department', 'd.nama_department')
            ->get();

        // 3. Proses nilai akhir untuk semua departemen (termasuk yang nilainya 0)
        $processed = $allDepartments->map(function ($deptName, $deptId) use ($rows) {
            // Cari apakah ada data nilai untuk departemen ini
            $row = $rows->first(fn($r) => $r->id_department == $deptId);

            $totalSkor = $row ? (float) $row->total_skor : 0;

            // Logika nilai akhir: (total + 28) × faktor, tapi kalau total = 0 → nilai akhir = 0
            $baseNilai = $totalSkor > 0 ? ($totalSkor + 28) : 0;

            $deptUpper = strtoupper($deptName);

            if (str_contains($deptUpper, 'HRGA') || str_contains($deptUpper, 'GA')) {
                $nilaiAkhir = $baseNilai * 105;
            } elseif (str_contains($deptUpper, 'PRD') || str_contains($deptUpper, 'PROD')) {
                $nilaiAkhir = $baseNilai * 110;
            } else {
                $nilaiAkhir = $baseNilai * 100;
            }

            return (object) [
                'nama_department' => $deptName,
                'nilai_akhir'     => round($nilaiAkhir, 2)
            ];
        })
            ->sortByDesc('nilai_akhir') // ranking dari tertinggi
            ->values();

        return response()->json([
            'status' => 'success',
            'labels' => $processed->pluck('nama_department')->toArray(),
            'data'   => $processed->pluck('nilai_akhir')->toArray()
        ]);
    }
}
