<?php

namespace App\Http\Controllers\System5R;

use App\Http\Controllers\Controller;
use App\Models\System5R\Jawaban;
use App\Models\System5R\MasterDepartment;
use App\Models\System5R\MasterGroup;
use App\Models\System5R\MasterPertanyaan;
use Illuminate\Http\Request;

class MasterGroupController extends Controller
{
    public function index()
    {
        $department = MasterDepartment::where('is_active', 'Y')->get();

        return view('system5r.master-group.index', compact('department'));
    }

    public function data(Request $request)
    {
        $id_department = $_GET['department'];
        $data = MasterGroup::where('id_department', $id_department)
        ->where('is_active', 'Y')
        ->get();

        return response()
        ->json([
            'data' => $data
        ]);
    }

    public function byDepartment($id_department)
    {
        $data = MasterGroup::where('id_department', $id_department)
        ->where('is_active', 'Y')
        ->get();

        return response()
        ->json([
            'data' => $data
        ]);
    }

    protected function createGroup()
    {
        // Get last record
        $lastRecord = MasterGroup::latest('created_at')->first();

        if($lastRecord == null) {
            $id_group = 0;
        } else {
            // Substing GR001 -> 001
            $id_group = substr($lastRecord->id_group, 2);
        }

        // Make new id_group
        $id_group = 'GR' . sprintf("%03d", $id_group + 1);

        return $id_group;
    }

    public function store(Request $request)
    {
        $validation = $request->validate([
            'id_department' => 'required',
            'nama_group' => 'required',
            'persentase' => 'required',
            'is_digitalisasi' => 'required'
        ]);

        if(!$validation) {
            return response()
            ->json([
                'status' => 'error',
                'message' => 'Data tidak valid'
            ]);
        }

        $id_group = $this->createGroup();
        $group = new MasterGroup;
        $group->id_group = $id_group;
        $group->id_department = $request->id_department;
        $group->nama_group = $request->nama_group;
        $group->persentase = $request->persentase;
        $group->is_digitalisasi = $request->is_digitalisasi;
        $group->save();

        return response()
        ->json([
            'status' => 'success',
            'message' => 'Data berhasil disimpan'
        ]);
    }

    public function delete(Request $request)
    {
        $id_group = $request->id_group;

        // Cek apakah id group sudah digunakan di tabel penilaian
        $pertanyaan = MasterPertanyaan::where('id_group', $id_group)->get();
        
        // Apakah id pertanyaan sudah digunakan di tabel jawaban
        foreach($pertanyaan as $p) {
            $jawaban = Jawaban::where('id_pertanyaan', $p->id_pertanyaan)->get();
            if(count($jawaban) > 0) {
                return response()
                ->json([
                    'status' => 'error',
                    'message' => 'Data tidak dapat dihapus karena sudah digunakan di penilaian, kamu hanya bisa melakukan edit persentase'
                ]);
            }
        }

        $group = MasterGroup::find($id_group);
        $group->delete();

        return response()
        ->json([
            'status' => 'success',
            'message' => 'Data berhasil dihapus'
        ]);
    }

    public function nonaktifkan(Request $request)
    {
        $id_group = $request->id_group;

        $group = MasterGroup::find($id_group);
        $group->is_active = 'N';
        $group->save();

        return response()
        ->json([
            'status' => 'success',
            'message' => 'Data berhasil dinonaktifkan'
        ]);
    }

    public function updatePersentase(Request $request)
    {
        $id_group = $request->id_group;
        $persentase = $request->persentase;

        // Turn old group status to N
        $oldGroup = MasterGroup::find($id_group);
        $oldGroup->is_active = 'N';
        $oldGroup->save();

        // Create new group
        $newGroup = new MasterGroup;
        $newGroup->id_group = $this->createGroup();
        $newGroup->id_department = $oldGroup->id_department;
        $newGroup->nama_group = $oldGroup->nama_group;
        $newGroup->persentase = $persentase;
        $newGroup->is_active = 'Y';
        $newGroup->save();

        return response()
        ->json([
            'status' => 'success',
            'message' => 'Data berhasil diupdate'
        ]);
    }
}
