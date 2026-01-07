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
        $jadwal = Jadwal::find($jadwalId);
        $tahun  = (int) $jadwal->tahun;

        return $workspace->map(function ($item) use ($jadwalId, $tahun) {

            $item->departments = $item->departments->map(function ($dept) use ($jadwalId, $tahun) {

                $periode = Periode::where('id_jadwal', $jadwalId)->get();

                $periode = $periode->map(function ($p) use ($dept, $jadwalId, $tahun) {
                    $groups = MasterGroup::where('id_department', $dept->id_department)->get();

                    $groups = $groups->map(function ($g) use ($p, $dept, $jadwalId, $tahun) {

                        $jawabanGroup = JawabanGroup::where([
                            'id_group'   => $g->id_group,
                            'id_periode' => $p->id_periode,
                            'status'     => 'approved'
                        ])->first();

                        $g->totalNilai = 0;
                        $g->nilaiAkhir = 0; // default

                        if ($jawabanGroup) {
                            $total = Jawaban::where(
                                'id_jawaban_group',
                                $jawabanGroup->id_jawaban_group
                            )->sum('nilai');

                            $g->totalNilai = $total;
                            $g->submit_by = $jawabanGroup->submit_by;

                            // if ($tahun < 2026) {
                                $nilaiRaw = $total * ((float) $g->persentase / 100);
                                $g->nilaiAkhir = round($nilaiRaw, 2); 
                            // } else {
                            // gak jadi pake ini
                            //     $nilaiGroup = round(
                            //         $total * ((float) $g->persentase / 100),
                            //         2
                            //     ); 

                            //     $baseNilai = $nilaiGroup + 28;

                            //     $deptName = strtoupper($dept->nama_department ?? $dept->department_name ?? '');

                            //     if (str_contains($deptName, 'HRGA') || str_contains($deptName, 'GA') || str_contains($deptName, 'GENERAL AFFAIR') || str_contains($deptName, 'HRDGA')) {
                            //         $g->nilaiAkhir = round($baseNilai * 1.05, 2); 
                            //     } elseif (str_contains($deptName, 'PRD') || str_contains($deptName, 'PROD') || str_contains($deptName, 'PRO')) {
                            //         $g->nilaiAkhir = round($baseNilai * 1.10, 2); 
                            //     } else {
                            //         $g->nilaiAkhir = round($baseNilai, 2); 
                            //     }
                            // }
                            // ====================================================
                        }

                        // Enkripsi key tetap sama
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
                    $p->nilaiAkhir = round($groups->sum('nilaiAkhir'), 2);
                    $p->juri = $groups
                        ->filter(fn($g) => $g->jawabanGroup)
                        ->pluck('jawabanGroup.submit_by')
                        ->unique()
                        ->values()
                        ->toArray();

                    $juriRecord = MasterGroupJuriDepartment::where('id_department', $dept->id_department)
                        ->where('id_periode', $p->id_periode)
                        ->with('group.anggota') // asumsi relasi: group -> anggota (table user/juri)
                        ->first();

                    if ($juriRecord && $juriRecord->group && $juriRecord->group->anggota->isNotEmpty()) {
                        $p->juri = $juriRecord->group->anggota
                            ->pluck('nama_juri') // atau 'name', 'nama_lengkap', sesuaikan kolom nama
                            ->unique()
                            ->values()
                            ->toArray();
                    } else {
                        // $p->juri = [];
                        
                        // FALLBACK (2024 & data lama): ambil dari submit_by
                        $p->juri = $groups
                            ->pluck('submit_by')
                            ->filter()
                            ->unique()
                            ->values()
                            ->toArray();
                    }

                    return $p;
                });

                $dept->periode = $periode;

                // Rata-rata nilai akhir per periode (atau pakai totalNilai jika mau)
                $dept->__total = round(
                    $periode->where('totalNilai', '>', 0)->avg('nilaiAkhir') ?? 0,
                    2
                );

                return $dept;
            });

            return $item;
        });
    }

    public function detail(Request $request)
    {
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
