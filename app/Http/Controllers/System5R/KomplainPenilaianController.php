<?php

namespace App\Http\Controllers\System5R;

use App\Http\Controllers\Controller;
use App\Models\System5R\GroupJuriAnggota;
use App\Models\System5R\Jawaban;
use App\Models\System5R\JawabanGroup;
use App\Models\System5R\MasterGroup;
use App\Models\System5R\MasterGroupJuriDepartment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class KomplainPenilaianController extends Controller
{
    public function index()
    {
        // Get committee info
        $juriInfo = GroupJuriAnggota::where('nik_juri', auth()->user()->username)
        ->with('group')
        ->get();

        if($juriInfo->count() == 0) {
            return redirect()->route('system5r.dashboard');
        }

        $juriGroups = $juriInfo->pluck('group.id_group_juri')->toArray();

        // Get id group juri department
        $mapping = MasterGroupJuriDepartment::whereIn('id_group_juri', $juriGroups)
        ->get();

        $groups = [];
        $periodes = [];
        
        foreach($mapping as $map) {
            $_groups = MasterGroup::where('id_department', $map->id_department)
            ->get()->pluck('id_group')
            ->toArray();

            $groups = array_merge($groups, $_groups);
            $periodes[] = $map->id_periode;
        }

        // dd($juriGroups, $mapping, $groups, $periodes);

        // Get the transactions
        $data = JawabanGroup::whereIn('id_group', $groups)
        ->whereIn('id_periode', $periodes)
        ->with('periode')
        ->with('periode.jadwal')
        ->where('status', 'complaining')
        ->get();

        return view('system5r.komplain-penilaian.index', compact('data'));
    }

    public function view($id_jawaban_group)
    {
        $data = JawabanGroup::where('id_jawaban_group', decrypt($id_jawaban_group))
        ->with('group')
        ->with('group.department')
        ->with('periode')
        ->with('periode.jadwal')
        ->first();

        // dd($data);

        $id_department = $data->group->department->id_department;
        $id_jadwal = $data->periode->jadwal->id_jadwal;
        $id_periode = $data->periode->id_periode;
        $id_group = $data->group->id_group;

        $jawabanGroup = $data;

        $pertanyaan = MasterGroup::where('id_department', $id_department)
        ->where('id_group', $id_group)
        ->get();

        return view('system5r.komplain-penilaian.view', compact('data', 'jawabanGroup', 'pertanyaan', 'id_jawaban_group'));
    }

    public function submit(Request $request)
    {
        try {
            DB::beginTransaction();

            if($request->new_nilai) {
                foreach($request->new_nilai as $id_jawaban => $nilai) {
                    $jawaban = Jawaban::find($id_jawaban);
                    
                    $jawaban->nilai = $nilai;
                    $jawaban->solve_komplain_ket = $request->keterangan_solve[$id_jawaban];
                    
                    $jawaban->save();
                }
            }

            $jawabanGroup = JawabanGroup::find($request->id_jawaban_group);
            $jawabanGroup->status = 'solved';
            $jawabanGroup->nik_solver = auth()->user()->username;
            $jawabanGroup->solved_at = now();
            $jawabanGroup->save();

            DB::commit();

            return redirect()->back()->with('success', 'Komplain berhasil diselesaikan');
        }catch(\Exception $e) {
            DB::rollback();

            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}
