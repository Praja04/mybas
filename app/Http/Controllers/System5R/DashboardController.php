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

        // Jika tidak ada id_jadwal dari frontend, ambil jadwal terbaru otomatis
        if (!$jadwalId) {
            $jadwalId = DB::table('5r_jadwal_penilaian') // sesuaikan nama tabel jadwal kamu
                ->orderByDesc('tahun')         // asumsi ada kolom 'tahun' (2025, 2026, 2027)
                ->value('id_jadwal');

            // Jika masih kosong (belum ada jadwal sama sekali), return empty
            if (!$jadwalId) {
                return response()->json([
                    'status'   => 'success',
                    'labels'   => [],
                    'datasets' => []
                ]);
            }
        }

        // Validasi workspace wajib
        if (!$workspaceId) {
            return response()->json(['status' => 'error', 'message' => 'Workspace required'], 422);
        }

        $rows = DB::table('5r_periode_penilaian as p')
            ->crossJoin('5r_master_department as d')
            ->leftJoin('5r_master_group as g', 'g.id_department', '=', 'd.id_department')
            ->leftJoin('5r_jawaban_group as jg', function ($join) {
                $join->on('jg.id_group', '=', 'g.id_group')
                    ->on('jg.id_periode', '=', 'p.id_periode');
            })
            ->leftJoin('5r_jawaban as j', 'j.id_jawaban_group', '=', 'jg.id_jawaban_group')
            ->where('d.id_workspace', $workspaceId)
            ->where('p.id_jadwal', $jadwalId) // SELALU filter jadwal (baik dari frontend atau default)
            ->select(
                'p.nama_periode',
                'd.nama_department',
                DB::raw("
                CASE 
                    WHEN COUNT(j.id) > 0 
                    THEN SUM(j.nilai) + 28
                    ELSE 0
                END as total_nilai
            ")
            )
            ->groupBy('p.nama_periode', 'd.nama_department')
            ->orderBy('p.nama_periode')
            ->get();

        // Jika tidak ada data, return empty chart
        if ($rows->isEmpty()) {
            return response()->json([
                'status'   => 'success',
                'labels'   => [],
                'datasets' => []
            ]);
        }

        $departments = $rows->pluck('nama_department')->unique()->values();
        $periodes    = $rows->pluck('nama_periode')->unique()->values();

        $datasets = [];
        foreach ($periodes as $periode) {
            $data = [];
            foreach ($departments as $dept) {
                $row = $rows->first(fn($r) => $r->nama_periode === $periode && $r->nama_department === $dept);
                $data[] = $row ? (float) $row->total_nilai : 0;
            }
            $datasets[] = [
                'label' => $periode,
                'data'  => $data
            ];
        }

        return response()->json([
            'status'   => 'success',
            'labels'   => $departments,
            'datasets' => $datasets
        ]);
    }

    public function getDataRankPeriodeByWorkspace(Request $request)
    {
        $workspaceId = $request->id_workspace;
        $jadwalId    = $request->id_jadwal;

        // Jika tidak ada id_jadwal dari frontend, ambil jadwal terbaru otomatis
        if (!$jadwalId) {
            $jadwalId = DB::table('5r_jadwal_penilaian') // sesuaikan nama tabel jadwal kamu
                ->orderByDesc('tahun')         // asumsi kolom 'tahun' (2025, 2026, 2027)
                ->value('id_jadwal');

            // Jika masih tidak ada jadwal sama sekali, return empty
            if (!$jadwalId) {
                return response()->json([
                    'status' => 'success',
                    'labels' => [],
                    'data'   => []
                ]);
            }
        }

        // Validasi workspace wajib
        if (!$workspaceId) {
            return response()->json(['status' => 'error', 'message' => 'Workspace required'], 422);
        }

        $rows = DB::table('5r_master_department as d')
            ->leftJoin('5r_master_group as g', 'g.id_department', '=', 'd.id_department')
            ->leftJoin('5r_jawaban_group as jg', function ($join) {
                $join->on('jg.id_group', '=', 'g.id_group');
            })
            ->leftJoin('5r_jawaban as j', 'j.id_jawaban_group', '=', 'jg.id_jawaban_group')
            ->leftJoin('5r_periode_penilaian as p', 'p.id_periode', '=', 'jg.id_periode')
            ->where('d.id_workspace', $workspaceId)
            ->where('p.id_jadwal', $jadwalId) // SELALU filter jadwal
            ->select(
                'd.nama_department',
                DB::raw("
                CASE 
                    WHEN COUNT(j.id) > 0 
                    THEN SUM(j.nilai) + 28
                    ELSE 0
                END as total_nilai
            ")
            )
            ->groupBy('d.nama_department')
            ->orderByDesc('total_nilai')
            ->get();

        return response()->json([
            'status' => 'success',
            'labels' => $rows->pluck('nama_department')->values(),
            'data'   => $rows->pluck('total_nilai')->map(fn($v) => (float) $v)
        ]);
    }
}
