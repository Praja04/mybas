<?php

namespace App\Http\Controllers\System5R;

use App\Http\Controllers\Controller;
use App\Models\System5R\MasterDepartment;
use App\Models\System5R\MasterWorkspace;
use Illuminate\Http\Request;

class MasterDepartmentController extends Controller
{
    public function index()
    {
        $data = MasterWorkspace::all();

        return view('system5r.master-department.index', compact('data'));
    }

    protected function createIdWorkspace()
    {
        $lastWorkspace = MasterWorkspace::orderBy('created_at', 'desc')
        ->orderBy('id_workspace', 'desc')
        ->first();

        if ($lastWorkspace == null) {
            return 'W01';
        }

        $lastId = substr($lastWorkspace->id_workspace, 1);
        $newId = $lastId + 1;

        if (strlen($newId) == 1) {
            return 'W0' . $newId;
        }

        return 'W' . $newId;
    }

    public function createWorkspace(Request $request)
    {
        $workspace = new MasterWorkspace;
        $workspace->id_workspace = $this->createIdWorkspace();
        $workspace->name = $request->name;
        $workspace->save();

        return redirect()->back()->with('success', 'Berhasil menambahkan workspace baru');
    }

    public function store(Request $request)
    {
        // Check if id_department already exists
        $check = MasterDepartment::where('id_department', $request->id_department)->first();
        if ($check != null) {
            return redirect()->back()->with('error', 'ID department sudah ada');
        }

        $department = new MasterDepartment;
        $department->id_workspace = $request->id_workspace;
        $department->id_department = $request->id_department;
        $department->nama_department = $request->nama_department;
        $department->save();

        return redirect()->back()->with('success', 'Berhasil menambahkan department baru');
    }

    public function aktifkan(Request $request)
    {
        $department = MasterDepartment::where('id_department', $request->id_department)->first();
        $department->is_active = 'Y';
        $department->save();

        return redirect()->back()->with('success', 'Berhasil mengaktifkan department');
    }
    
    public function nonaktifkan(Request $request)
    {
        $department = MasterDepartment::where('id_department', $request->id_department)->first();
        $department->is_active = 'N';
        $department->save();
    
        return redirect()->back()->with('success', 'Berhasil menonaktifkan department');
    }
}
