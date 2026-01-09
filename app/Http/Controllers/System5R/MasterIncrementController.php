<?php

namespace App\Http\Controllers\System5R;

use App\Http\Controllers\Controller;
use App\Models\System5R\MasterIncrement;
use App\Models\System5R\MasterDepartment;
use App\Models\System5R\Jadwal;
use App\Models\System5R\MasterGroup;
use App\Models\System5R\Periode;
use Illuminate\Http\Request;

class MasterIncrementController extends Controller
{
    public function index()
    {
        // $departments = MasterDepartment::all();
        $departments = MasterDepartment::where('is_active', 'Y')->get();
        $jadwal = Jadwal::all();

        return view('system5r.master-increment.index', compact('departments', 'jadwal'));
    }

    public function getGroupByDepartment($id_department)
    {
        return MasterGroup::where('id_department', $id_department)->where('is_active', 'Y')->get();
    }

    public function getPeriodeByJadwal($id_jadwal)
    {
        $data = Periode::where('id_jadwal', $id_jadwal)
            ->orderBy('created_at', 'asc')
            ->get(['id_periode', 'nama_periode']);

        // dd($data);

        return response()->json([
            'data' => $data,
        ]);
    }

    public function getAll(Request $request)
    {
        $request->validate([
            'id_jadwal' => 'required',
            'id_periode' => 'required',
        ]);

        $data = MasterIncrement::with(['department', 'group'])
            ->where('id_jadwal', $request->id_jadwal)
            ->where('id_periode', $request->id_periode)
            ->get();

        return response()->json(['data' => $data]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_department' => 'required',
            'id_group' => 'required',
            'id_jadwal' => 'required',
            'id_periode' => 'required',
            'nilai' => 'required|min:0',
        ]);

        $exists = MasterIncrement::where('id_group', $request->id_group)->where('id_jadwal', $request->id_jadwal)->where('id_periode', $request->id_periode)->exists();

        if ($exists) {
            return response()->json(
                [
                    'status' => 'error',
                    'message' => 'Data increment untuk departemen dan group ini sudah ada',
                ],
                422,
            );
        }

        MasterIncrement::create([
            'id_department' => $request->id_department,
            'id_group' => $request->id_group,
            'id_jadwal' => $request->id_jadwal,
            'id_periode' => $request->id_periode,
            'nilai' => $request->nilai,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Nilai increment berhasil disimpan',
        ]);
    }

    public function update(Request $request)
    {
        $request->validate([
            'id' => 'required',
        ]);

        $data = MasterIncrement::find($request->id);

        if (!$data) {
            return response()->json(
                [
                    'status' => 'error',
                    'message' => 'Data tidak ditemukan',
                ],
                404,
            );
        }

        $data->update([
            'id_department' => $request->id_department,
            'id_group' => $request->id_group,
            'nilai' => $request->nilai,
        ]);

        $data->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Nilai increment berhasil diperbarui',
        ]);
    }

    public function delete(Request $request)
    {
        $request->validate([
            'id' => 'required',
        ]);

        $data = MasterIncrement::find($request->id);

        if (!$data) {
            return response()->json(
                [
                    'status' => 'error',
                    'message' => 'Data tidak ditemukan',
                ],
                404,
            );
        }

        $data->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Data berhasil dihapus',
        ]);
    }
}
