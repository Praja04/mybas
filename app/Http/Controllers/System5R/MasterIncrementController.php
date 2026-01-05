<?php

namespace App\Http\Controllers\System5R;

use App\Http\Controllers\Controller;
use App\Models\System5R\MasterIncrement;
use App\Models\System5R\MasterDepartment;
use App\Models\System5R\Jadwal;
use App\Models\System5R\Periode;
use Illuminate\Http\Request;

class MasterIncrementController extends Controller
{
    public function index()
    {
        $departments = MasterDepartment::all();
        $jadwal = Jadwal::all();

        return view('system5r.master-increment.index', compact('departments', 'jadwal'));
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

        $data = MasterIncrement::with('department')->where('id_jadwal', $request->id_jadwal)->where('id_periode', $request->id_periode)->get();

        return response()->json(['data' => $data]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_department' => 'required',
            'id_jadwal' => 'required',
            'id_periode' => 'required',
            'nilai' => 'required|min:0',
        ]);

        $exists = MasterIncrement::where('id_department', $request->id_department)->where('id_jadwal', $request->id_jadwal)->where('id_periode', $request->id_periode)->exists();

        if ($exists) {
            return response()->json(
                [
                    'status' => 'error',
                    'message' => 'Data increment untuk departemen ini sudah ada',
                ],
                422,
            );
        }

        MasterIncrement::create([
            'id_department' => $request->id_department,
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
            'nilai' => 'required|min:0',
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

        $data->nilai = $request->nilai;
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
