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
        $latestJadwal = $allJadwal->first();

        return view(
            'system5r.report.committee.index',
            compact('allJadwal', 'latestJadwal')
        );
    }

    public function data(Request $request)
    {
        $jadwalId = $request->jadwal_id;

        // Department yang dipegang oleh committee yang login
        $myDepartments = DepartmentComittee::where(
            'nik_committee',
            auth()->user()->username
        )->pluck('id_department')->toArray();

        if (count($myDepartments) === 0) {
            return response()->json([
                'status' => 'success',
                'workspace' => []
            ]);
        }

        $workspace = MasterWorkspace::whereHas('departments', function ($q) use ($myDepartments) {
            $q->whereIn('id_department', $myDepartments);
        })->with(['departments' => function ($q) use ($myDepartments) {
            $q->whereIn('id_department', $myDepartments);
        }])->get();

        $workspace = $workspace->map(function ($ws) use ($jadwalId) {

            $ws->departments = $ws->departments->map(function ($dep) use ($jadwalId) {

                $periode = Periode::where('id_jadwal', $jadwalId)->get();

                $periode = $periode->map(function ($p) use ($dep) {

                    $groups = MasterGroup::where('id_department', $dep->id_department)->get();

                    $groups = $groups->map(function ($g) use ($p, $dep) {

                        $jawabanGroup = JawabanGroup::where([
                            'id_group'   => $g->id_group,
                            'id_periode' => $p->id_periode,
                            'status'     => 'approved'
                        ])->first();

                        if (!$jawabanGroup) {
                            return null;
                        }

                        $totalNilai = Jawaban::where(
                            'id_jawaban_group',
                            $jawabanGroup->id_jawaban_group
                        )->sum('nilai');

                        // === LOGIKA PENGALIAN BERDASARKAN NAMA DEPARTEMEN ===
                        // $deptName = strtoupper($dep->nama_department ??
                        //     $dep->department_name ??
                        //     $dep->name ?? ''); // sesuaikan dengan kolom nama di tabelmu

                        // if (str_contains($deptName, 'HRGA') || str_contains($deptName, 'GA') || str_contains($deptName, 'GENERAL AFFAIR') || str_contains($deptName, 'HRDGA')) {
                        //     $faktor = 1.05;
                        // } elseif (str_contains($deptName, 'PRD') || str_contains($deptName, 'PROD') || str_contains($deptName, 'PRO')) {
                        //     $faktor = 1.10;
                        // } else {
                        //     $faktor = 1;
                        // }

                        // $nilaiAkhir = ($totalNilai + 28) * $faktor;

                        $nilaiAkhir = $totalNilai * ((float) $g->persentase / 100);

                        // ====================================================

                        return [
                            'id_group'     => $g->id_group,
                            'persentase'   => $g->persentase,
                            'nama_group'   => $g->nama_group,
                            'nilaiAkhir'   => round($nilaiAkhir, 2),
                            'encryptedKey' => encrypt(
                                implode('/', [
                                    $dep->id_department,  // penting: pakai dari $dep, bukan $g
                                    $p->id_jadwal,
                                    $p->id_periode,
                                    $g->id_group
                                ])
                            )
                        ];
                    })->filter()->values();

                    $p->group = $groups;
                    $p->totalNilai = $groups->sum('nilaiAkhir');

                    return $p;
                });

                $dep->periode = $periode;

                // Rata-rata nilai akhir dari periode yang punya nilai > 0
                $valid = $periode->where('totalNilai', '>', 0);
                $dep->__total = $valid->count()
                    ? round($valid->sum('totalNilai') / $valid->count(), 2)
                    : 0;

                return $dep;
            });

            return $ws;
        });

        return response()->json([
            'status'    => 'success',
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
