<?php

namespace App\Http\Controllers\System5R;

use App\Http\Controllers\Controller;
use App\Models\System5R\GroupJuri;
use App\Models\System5R\GroupJuriAnggota;
use App\Models\System5R\Jadwal;
use App\Models\System5R\MasterDepartment;
use App\Models\System5R\MasterGroupJuriDepartment;
use App\Models\System5R\Periode;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ScheduleJuriController extends Controller
{
    public function index()
    {
        $schedule = Jadwal::orderBy('created_at', 'desc')->get();
        $periode_penilaian = Periode::orderBy('created_at', 'desc')->get();

        if(isset($_GET['periode'])){
            $periode_id = $_GET['periode'];
            $data = MasterGroupJuriDepartment::where('id_periode', $periode_id)
            ->get()
            ->sortBy('group.nama_group');
        }else{
            $data = collect();
        }

        $departments = MasterDepartment::where('is_active', 'Y')->get();

        $users = User::orderBy('name', 'asc')->get();

        return view('system5r.schedule-juri.index', compact('periode_penilaian', 'data', 'users', 'schedule', 'departments'));
    }

    public function addJuri(Request $request)
    {
        $username = $request->username;
        $_username = explode('|', $username)[0];
        $_name = explode('|', $username)[1];
        $id_group_juri = $request->id_group_juri;
    
        // Check if it is already exist
        $isExist = GroupJuriAnggota::where('id_group_juri', $id_group_juri)
            ->where('nik_juri', $_username)
            ->first();

        if($isExist != null){
            return redirect()->back()->with('error', 'Juri sudah ada');
        }

        GroupJuriAnggota::create([
            'id_group_juri' => $id_group_juri,
            'nik_juri' => $_username,
            'nama_juri' => $_name,
            'is_active' => 'Y',
            'juri_utama' => 'N',
        ]);

        return redirect()->back()->with('success', 'Juri berhasil ditambahkan');
    }

    public function addJadwal(Request $request)
    {
        $validation = $request->validate([
            'tahun' => 'required|unique:5r_jadwal_penilaian,tahun',
        ]);

        try {
            DB::beginTransaction();

            Jadwal::create($request->all());

            DB::commit();

            return back()->with('success', 'Berhasil membuat jadwal');  
        }catch(\Exception $e)
        {
            DB::rollBack();

            return back()->with('error', $e->getMessage());
        }
    }

    public function addPeriode(Request $request)
    {
        $validation = $request->validate([
            'id_jadwal' => 'required',
            'nama_periode' => 'required',
            'keterangan' => 'required'
        ]);

        // Create unique id
        $id_periode = date('YmdHis');
        $request->merge(['id_periode' => $id_periode]);

        try {
            DB::beginTransaction();

            Periode::create($request->all());

            DB::commit();

            return back()->with('success', 'Berhasil membuat periode');  
        }catch(\Exception $e)
        {
            DB::rollBack();

            return back()->with('error', $e->getMessage());
        }
    }

    public function createGroupJuri(Request $request)
    {
        $validation = $request->validate([
            'id_periode' => 'required',
            'department' => 'required',
            'nama_group' => 'required',
            'index_tingkat_kesulitan' => 'required'
        ]);

        // Create unique id
        $id_group_juri = date('YmdHis');
        $request->merge(['id_group_juri' => $id_group_juri]);

        try {
            DB::beginTransaction();

            $group = new GroupJuri();
            $group->id_group_juri = $id_group_juri;
            $group->nama_group = $request->nama_group;
            $group->is_active = 'Y';
            $group->save();

            $group_department = new MasterGroupJuriDepartment();
            $group_department->id_group_juri = $id_group_juri;
            $group_department->id_periode = $request->id_periode;
            $group_department->id_department = $request->department;
            $group_department->index_tingkat_kesulitan = $request->index_tingkat_kesulitan;
            $group_department->save();

            DB::commit();

            return back()->with('success', 'Berhasil membuat group juri');
        }catch(\Exception $e)
        {
            DB::rollBack();

            return back()->with('error', $e->getMessage());
        }
    }
}
