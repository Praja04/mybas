<?php

namespace App\Http\Controllers\System5R;

use App\Http\Controllers\Controller;
use App\Models\System5R\MasterArea;
use App\Models\System5R\MasterDepartment;
use Illuminate\Http\Request;

class MasterAreaController extends Controller
{
    public function index()
    {
        $department = MasterDepartment::where('is_active', 'Y')->get();
        // $department = MasterDepartment::all();

        return view('system5r.master-area.index', compact('department'));
    }

    public function getAll(Request $request)
    {
        $id_department = $_GET['department'];
        $data = MasterArea::where('id_department', $id_department)
            ->where('is_active', 'Y')
            ->get();

        return response()
            ->json([
                'data' => $data
            ]);
    }

    public function getByDepartment($id_department)
    {
        $data = MasterArea::where('id_department', $id_department)
            ->where('is_active', 'Y')
            ->get();

        return response()
            ->json([
                'data' => $data
            ]);
    }


    protected function createArea()
    {
        $lastRecord = MasterArea::latest('created_at')->first();

        if ($lastRecord == null) {
            $id_area = 0;
        } else {
            // Substing AR001 -> 001
            $id_area = substr($lastRecord->id_area, 2);
        }

        // Make new id_area
        $id_area = 'AR' . sprintf("%03d", $id_area + 1);

        return $id_area;
    }

    public function store(Request $request)
    {
        $validation = $request->validate([
            'id_department' => 'required',
            'nama_area' => 'required',
        ]);

        if (!$validation) {
            return response()
                ->json([
                    'status' => 'error',
                    'message' => 'Data tidak valid'
                ]);
        }

        $id_area = $this->createArea();
        $area = new MasterArea;

        $area->id_area = $id_area;
        $area->id_department = $request->id_department;
        $area->nama_area = $request->nama_area;
        $area->save();

        return response()
            ->json([
                'status' => 'success',
                'message' => 'Data area berhasil disimpan'
            ]);
    }

    public function edit($id_area)
    {
        $area = MasterArea::find($id_area);

        if (!$area) {
            return response()->json([
                'status' => 'error',
                'message' => 'Data area tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $area
        ]);
    }

    public function update(Request $request)
    {
        $request->validate([
            'id_area' => 'required',
            'nama_area' => 'required'
        ]);

        $area = MasterArea::find($request->id_area);

        if (!$area) {
            return response()->json([
                'status' => 'error',
                'message' => 'Data area tidak ditemukan'
            ], 404);
        }

        $area->nama_area = $request->nama_area;
        $area->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Data area berhasil diperbarui'
        ]);
    }

    public function delete(Request $request)
    {
        $request->validate([
            'id_area' => 'required'
        ]);

        $area = MasterArea::find($request->id_area);

        if (!$area) {
            return response()->json([
                'status' => 'error',
                'message' => 'Data area tidak ditemukan'
            ], 404);
        }

        $area->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Data area berhasil dihapus'
        ]);
    }


    public function nonaktifkan(Request $request)
    {
        $request->validate([
            'id_area' => 'required'
        ]);

        $area = MasterArea::find($request->id_area);

        if (!$area) {
            return response()->json([
                'status' => 'error',
                'message' => 'Data area tidak ditemukan'
            ], 404);
        }

        $area->is_active = 'N';
        $area->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Data area berhasil dinonaktifkan'
        ]);
    }
}
