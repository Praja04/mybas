<?php

namespace App\Http\Controllers\System5R\Report;

use Barryvdh\DomPDF\Facade as PDF;
use Illuminate\Http\Request;
use App\Models\System5R\Jadwal;
use App\Models\System5R\Jawaban;
use App\Models\System5R\Periode;
use App\Http\Controllers\Controller;
use App\Models\System5R\MasterGroup;
use App\Models\System5R\JawabanGroup;
use App\Models\System5R\MasterWorkspace;
use App\Models\System5R\GroupJuriAnggota;
use App\Models\System5R\MasterDepartment;
use App\Models\System5R\MasterPertanyaan;
use App\Models\System5R\MasterGroupJuriDepartment;

class ManagementController extends Controller
{

    public function index()
    {
        $allJadwal = Jadwal::orderBy('tahun', 'desc')->get();

        // jadwal terbaru
        $latestJadwal = $allJadwal->first();

        return view('system5r.report.management.index', compact(
            'allJadwal',
            'latestJadwal'
        ));
    }

    public function getReport(Request $request)
    {
        $workspace = MasterWorkspace::with('departments')->get();

        $workspace = $this->buildReportData($workspace, $request->jadwal_id);

        return response()->json([
            'status' => 'success',
            'workspace' => $workspace
        ]);
    }

    private function buildReportData($workspace, $jadwalId)
    {
        return $workspace->map(function ($item) use ($jadwalId) {

            $item->departments = $item->departments->map(function ($dept) use ($jadwalId) {

                $periode = Periode::where('id_jadwal', $jadwalId)->get();

                $periode = $periode->map(function ($p) use ($dept, $jadwalId) {

                    $groups = MasterGroup::where('id_department', $dept->id_department)->get();

                    $groups = $groups->map(function ($g) use ($p, $dept, $jadwalId) {

                        $jawabanGroup = JawabanGroup::where([
                            'id_group'   => $g->id_group,
                            'id_periode' => $p->id_periode,
                            'status'     => 'approved'
                        ])->first();

                        $g->totalNilai = 0;

                        if ($jawabanGroup) {
                            $total = Jawaban::where(
                                'id_jawaban_group',
                                $jawabanGroup->id_jawaban_group
                            )->sum('nilai');

                            $g->totalNilai = $total;
                            $g->nilaiAkhir = $total + 28;
                            $g->submit_by = $jawabanGroup->submit_by;
                        }

                        // ✅ SEKARANG AMAN
                        $g->encryptedKey = encrypt(
                            $dept->id_department . '/' .
                                $jadwalId . '/' .
                                $p->id_periode . '/' .
                                $g->id_group
                        );

                        return $g;
                    })->filter(fn($g) => $g->totalNilai > 0);

                    $p->group = $groups->toArray();
                    $p->totalNilai = $groups->sum('totalNilai');
                    $p->juri = $groups->pluck('submit_by')
                        ->filter()
                        ->unique()
                        ->values()
                        ->toArray();

                    return $p;
                });

                $dept->periode = $periode;
                $dept->__total = $periode->where('totalNilai', '>', 0)->avg('totalNilai') ?? 0;

                return $dept;
            });

            return $item;
        });
    }

    public function detail(Request $request)
    {
        dd($request->all());
        $group = JawabanGroup::where('id_group', $request->id_group)
            ->where('id_periode', $request->id_periode)
            ->first();

        if ($group == null) {
            return response()->json([
                'status' => 'error',
                'message' => 'Data tidak ditemukan'
            ]);
        }

        $data = Jawaban::where('id_jawaban_group', $group->id_jawaban_group)
            ->with(['pertanyaan', 'temuan.area'])
            ->get()
            ->groupBy('pertanyaan.jenis');


        return response()->json([
            'status' => 'success',
            'data' => $data
        ]);
    }

    public function download($encryptedInfo)
    {
        try {
            $arrayInfo = explode('/', decrypt($encryptedInfo));

            if (count($arrayInfo) !== 4) {
                abort(400, 'Invalid parameter');
            }

            [
                $id_department,
                $id_jadwal,
                $id_periode,
                $id_group
            ] = $arrayInfo;
        } catch (\Exception $e) {
            abort(403, 'Invalid token');
        }

        // Ambil jawaban group (SAMA)
        $group = JawabanGroup::where('id_group', $id_group)
            ->where('id_periode', $id_periode)
            ->firstOrFail();

        // Ambil JAWABAN + PERTANYAAN + TEMUAN (SAMA PERSIS DETAIL)
        $data = Jawaban::where('id_jawaban_group', $group->id_jawaban_group)
            ->with([
                'pertanyaan',
                'temuan.area'
            ])
            ->get()
            ->groupBy('pertanyaan.jenis'); // kalau mau sama persis

        // Info header PDF
        $info = [
            'tahun'      => Jadwal::findOrFail($id_jadwal)->tahun,
            'periode'    => Periode::findOrFail($id_periode)->nama_periode,
            'department' => MasterDepartment::findOrFail($id_department)->nama_department,
            'group'      => MasterGroup::findOrFail($id_group)->nama_group,
        ];

        $pdf = PDF::loadView(
            'system5r.report.management.download',
            compact('info', 'group', 'data')
        )
            ->setPaper('A4', 'portrait')
            ->setOptions([
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => true,       // tetap biar aman
                'isPhpEnabled' => true,          // penting untuk @php di blade
                'defaultFont' => 'DejaVu Sans',  // karena kamu pakai font itu
            ]);

        return $pdf->stream('Report-5R.pdf');
    }
}
