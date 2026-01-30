<?php

namespace App\Http\Controllers\System5R\Report;

use App\Http\Controllers\Controller;
use App\Models\System5R\Jadwal;
use App\Models\System5R\DepartmentComittee;
use App\Models\System5R\Jawaban;
use App\Models\System5R\JawabanGroup;
use App\Models\System5R\MasterDepartment;
use App\Models\System5R\MasterWorkspace;
use App\Models\System5R\MasterGroupJuriDepartment;
use App\Models\System5R\MasterGroup;
use App\Models\System5R\Periode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Exception;

class CommitteeController extends Controller
{
    public function index()
    {
        $allJadwal = Jadwal::orderBy('tahun', 'desc')->get();
        $allPeriode = Periode::with('jadwal')
            ->orderByDesc('id_periode')
            ->get();

        $latestJadwal = $allJadwal->first();

        return view(
            'system5r.report.committee.index',
            compact('allJadwal', 'latestJadwal', 'allPeriode')
        );
    }

    public function data(Request $request)
    {
        $jadwalId  = $request->jadwal_id;
        $periodeId = $request->periode_id;

        $jadwal = Jadwal::findOrFail($jadwalId);
        $tahun  = (int) $jadwal->tahun;

        // pastikan periode milik jadwal tsb
        Periode::where('id_periode', $periodeId)
            ->where('id_jadwal', $jadwalId)
            ->firstOrFail();

        // ================= IS LATEST YEAR =================
        $latestJadwalId = Jadwal::orderByDesc('tahun')->value('id_jadwal');
        $isLatestYear   = $jadwalId == $latestJadwalId;

        // ================= MY DEPARTMENTS =================
        $myDepartments = DepartmentComittee::where('nik_committee', auth()->user()->username)
            ->where('is_active', 'Y')
            ->pluck('id_department')
            ->toArray();

        if (empty($myDepartments)) {
            return response()->json([
                'status' => 'success',
                'workspace' => []
            ]);
        }

        $workspace = MasterWorkspace::whereHas('departments', function ($q) use (
            $myDepartments,
            $isLatestYear,
            $periodeId
        ) {
            $q->whereIn('id_department', $myDepartments);

            // TAHUN TERBARU (2026) → tampilkan hanya dept AKTIF
            if ($isLatestYear) {
                $q->where('is_active', 'Y');
            }

            // TAHUN LAMA → hanya dept yg SUDAH DINILAI
            if (!$isLatestYear) {
                $q->whereExists(function ($sq) use ($periodeId) {
                    $sq->select(DB::raw(1))
                        ->from('5r_master_group as mg')
                        ->join('5r_jawaban_group as jg', 'jg.id_group', '=', 'mg.id_group')
                        ->whereColumn('mg.id_department', '5r_master_department.id_department')
                        ->where('jg.status', 'approved')
                        ->where('jg.id_periode', $periodeId);
                });
            }
        })->with(['departments' => function ($q) use (
            $myDepartments,
            $isLatestYear,
            $periodeId
        ) {
            // committee scope
            $q->whereIn('id_department', $myDepartments);

            // tahun terbaru → aktif saja
            if ($isLatestYear) {
                $q->where('is_active', 'Y');
            }

            // tahun lama → sudah dinilai
            if (!$isLatestYear) {
                $q->whereExists(function ($sq) use ($periodeId) {
                    $sq->select(DB::raw(1))
                        ->from('5r_master_group as mg')
                        ->join('5r_jawaban_group as jg', 'jg.id_group', '=', 'mg.id_group')
                        ->whereColumn('mg.id_department', '5r_master_department.id_department')
                        ->where('jg.status', 'approved')
                        ->where('jg.id_periode', $periodeId);
                });
            }
        }])->get();

        $workspace = $workspace->map(function ($ws) use ($periodeId, $tahun) {

            $ws->departments = $ws->departments->map(function ($dep) use ($periodeId, $tahun) {

                $periode = Periode::find($periodeId);

                $groups = MasterGroup::where('id_department', $dep->id_department)->get();

                $groups = $groups->map(function ($g) use ($periode, $dep) {

                    $jawabanGroup = JawabanGroup::where([
                        'id_group'   => $g->id_group,
                        'id_periode' => $periode->id_periode,
                        'status'     => 'approved'
                    ])->first();

                    if (!$jawabanGroup) {
                        return null;
                    }

                    $total = Jawaban::where(
                        'id_jawaban_group',
                        $jawabanGroup->id_jawaban_group
                    )->sum('nilai');

                    return [
                        'id_group'   => $g->id_group,
                        'nama_group' => $g->nama_group,
                        'persentase' => $g->persentase,
                        'totalNilai' => $total,
                        'nilaiAkhir' => round($total * ((float) $g->persentase / 100), 2),
                        'submit_by'  => $jawabanGroup->submit_by,
                        'encryptedKey' => encrypt(
                            "{$dep->id_department}/{$periode->id_jadwal}/{$periode->id_periode}/{$g->id_group}"
                        )
                    ];
                })->filter()->values();

                $periode->group = $groups;
                $nilaiGroupSum  = round($groups->sum('nilaiAkhir'), 2);

                if ($tahun < 2026) {
                    $periode->nilaiAkhir = $nilaiGroupSum;
                } else {
                    $juriDept = MasterGroupJuriDepartment::where('id_department', $dep->id_department)
                        ->where('id_periode', $periode->id_periode)
                        ->first();

                    $bobot = ($juriDept && $juriDept->index_tingkat_kesulitan !== null)
                        ? (float) $juriDept->index_tingkat_kesulitan
                        : 1.00;

                    $periode->nilaiAkhir = round(($nilaiGroupSum + 28) * $bobot, 2);
                    $periode->bobot = $bobot;
                }

                $periode->juri = $groups
                    ->pluck('submit_by')
                    ->filter()
                    ->unique()
                    ->values()
                    ->toArray();

                $dep->periode = [$periode];
                $dep->__total = $periode->nilaiAkhir ?? 0;

                return $dep;
            });

            return $ws;
        });

        return response()->json([
            'status' => 'success',
            'workspace' => $workspace
        ]);
    }

    public function masterCommitte()
    {
        $department = MasterDepartment::where('is_active', 'Y')->get();
        $group = MasterGroup::orderBy('id_department')->get();
        return view('system5r.master-comitte.index', compact('department', 'group'));
    }


    public function getDataComittee()
    {
        $dataComittees = DepartmentComittee::orderBy('created_at', 'desc')->get();

        $totalDataComittee = [];

        foreach ($dataComittees as $dataComittee) {
            $comittes = [
                'id' => $dataComittee->id,
                'id_department' => $dataComittee->id_department,
                'nik_committee' => $dataComittee->nik_committee,
                'nama_committee' => $dataComittee->nama_committee,
                'is_active' => $dataComittee->is_active,
                'committee_utama' => $dataComittee->committee_utama,
            ];

            $totalDataComittee[] = $comittes;
        }

        $response = [
            'data' => $totalDataComittee,
            'status' => 'success',
            'code' => 200,
        ];

        return response()->json($response);
    }


    public function storeDataComittee(Request $request)
    {
        try {
            $comitte = new DepartmentComittee();
            $comitte->id_department = $request->department;
            $comitte->nik_committee = $request->nik_committee;
            $comitte->nama_committee = $request->nama_committee;
            $comitte->is_active = 'Y';
            $comitte->committee_utama = 'N';

            $comitte->save();

            return response()->json([
                'status' => 1,
                'message' => 'Berhasil menambahkan user comittee'
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 2,
                'message' => 'Ada kesalahan: ' . $e->getMessage()
            ]);
        }
    }

    public function deleteComittee(Request $request)
    {
        $committee = DepartmentComittee::where('id', $request->id)->first();

        if ($committee) {
            $committee->delete();
            return response()->json(['message' => 'Committee berhasil dihapus', 'status' => 'success']);
        } else {
            return response()->json(['message' => 'Committee tidak ditemukan', 'status' => 'error'], 404);
        }
    }

    public function editDataComitte(Request $request)
    {
        $committee = DepartmentComittee::where('id', $request->id)->first();

        if ($committee) {
            $committee->id_department = $request->department;
            $committee->nik_committee = $request->nik_committee;
            $committee->nama_committee = $request->nama_committee;
            $committee->save();

            return response()->json(['message' => 'Data committee berhasil diupdate', 'status' => 'success']);
        } else {
            return response()->json(['message' => 'Data committee tidak ditemukan', 'status' => 'error'], 404);
        }
    }

    public function ubahStatusComittee(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:5r_department_committee,id',
            'is_active' => 'required|in:Y,N',
        ]);

        $id_comittee = $request->id;
        $newStatus = $request->is_active;

        try {
            DB::table('5r_department_committee')
                ->where('id', $id_comittee)
                ->update(['is_active' => $newStatus]);

            return response()->json(['message' => 'Status berhasil diperbarui'], 200);
        } catch (Exception $e) {
            return response()->json(['message' => 'Terjadi kesalahan saat mengubah status', 'error' => $e->getMessage()], 500);
        }
    }
}
