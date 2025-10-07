<?php

namespace App\Http\Controllers\System5R\Report;

use App\Http\Controllers\Controller;
use App\Models\System5R\GroupJuriAnggota;
use App\Models\System5R\Jadwal;
use App\Models\System5R\Jawaban;
use App\Models\System5R\MasterGroup;
use App\Models\System5R\MasterPertanyaan;
use App\Models\System5R\JawabanGroup;
use App\Models\System5R\MasterDepartment;
use App\Models\System5R\MasterGroupJuriDepartment;
use App\Models\System5R\MasterWorkspace;
use App\Models\System5R\Periode;
use Illuminate\Http\Request;

class ManagementController extends Controller
{
    private $testing = null;

    public function index()
    {
        $workspace = MasterWorkspace::get();

        $allJadwal = Jadwal::all();

        if (isset($_GET['filter_jadwal'])) {
            $workspace = $workspace->map(function ($_item) {
                $_data = $_item->departments->map(function ($item) {
                    $periode = Periode::where('id_jadwal', $_GET['filter_jadwal'])->get();
                    $id_department = $item->id_department;

                    $periode = $periode->map(function ($_item, $key) use ($id_department) {
                        $__group = MasterGroup::where('id_department', $id_department)->get();

                        $id_periode = $_item->id_periode;

                        $_group = $__group->map(function ($__item, $key) use ($id_periode) {
                            $__item->jawabanGroup = JawabanGroup::where('id_group', $__item->id_group)
                                ->where('id_periode', $id_periode)
                                ->first();

                            $totalNilai = 0;

                            if ($__item->jawabanGroup != null) {
                                $jawaban = Jawaban::where('id_jawaban_group', $__item->jawabanGroup->id_jawaban_group)->get();

                                $totalNilai = $jawaban->sum('nilai') * ($__item->persentase / 100);
                            }

                            $__item->totalNilai = $totalNilai;

                            return $__item;
                        });

                        // $juri_periode = MasterGroupJuriDepartment::where('id_department', $id_department)
                        // ->where('id_periode', $id_periode)
                        // ->with('group.anggota')
                        // ->first();

                        // dd($juri_periode->group->anggota);

                        // if($juri_periode != null) {
                        //     $juri_array = $juri_periode->group->anggota->map(function ($item, $key) {
                        //         return $item->nama_juri;
                        //     })->toArray();

                        //     $_item->juri = $juri_array;
                        // }else{
                        $_item->juri = $_group
                            ->filter(fn($g) => $g->jawabanGroup) // hanya yang ada jawaban
                            ->pluck('jawabanGroup.submit_by')    // ambil submit_by
                            ->unique()                           // biar tidak duplikat
                            ->values()
                            ->toArray();

                        // }


                        $_group = $_group->filter(function ($__item, $key) {
                            return $__item->jawabanGroup != null && $__item->jawabanGroup->status == 'approved';
                        });

                        $_item->group = $_group;
                        $_item->totalNilai = $_group->sum('totalNilai');

                        return $_item;
                    });

                    $item->periode = $periode;

                    if ($item->periode->where('totalNilai', '!=', '0')->count() == 0) {
                        $__total = 0;
                    } else {
                        $__total = $item->periode->sum('totalNilai') / $item->periode->where('totalNilai', '!=', '0')->count();
                    }

                    $item->__total = $__total;

                    return $item;
                });

                $_item->departments = $_data;
                return $_item;
            });
        }

        return view('system5r.report.management.index', compact('workspace', 'allJadwal'));
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
            ->with('pertanyaan')
            ->get()
            ->groupBy('pertanyaan.jenis');

        return response()->json([
            'status' => 'success',
            'data' => $data
        ]);
    }

    public function download($encryptedInfo)
    {
        $decryptedInfo = decrypt($encryptedInfo);

        $arrayInfo = explode('/', $decryptedInfo);

        $id_department = $arrayInfo[0];
        $id_jadwal = $arrayInfo[1];
        $id_periode = $arrayInfo[2];
        $id_group = $arrayInfo[3];

        $jawabanGroup = JawabanGroup::where('id_periode', $id_periode)
            ->where('id_group', $id_group)
            ->first();

        $pertanyaan = MasterGroup::where('id_department', $id_department)
            ->where('id_group', $id_group)
            ->get();
        // dd($jawabanGroup, $id_periode, $pertanyaan);
        // $periode = Periode::where('id_jadwal', $_GET['filter_jadwal'])->get();

        $info = [
            'tahun' => Jadwal::where('id_jadwal', $id_jadwal)->first()->tahun,
            'periode' => Periode::where('id_periode', $id_periode)->first()->nama_periode,
            'department' => MasterDepartment::where('id_department', $id_department)->first()->nama_department,
            'group' => MasterGroup::where('id_group', $id_group)->first()->nama_group,
        ];

        return view('system5r.report.management.download', compact('info', 'jawabanGroup', 'pertanyaan'));
    }
}
