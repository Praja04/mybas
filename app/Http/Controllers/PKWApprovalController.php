<?php

namespace App\Http\Controllers;

use App\PKWApproval;
use App\PKWDivisi;
use App\User;
use Illuminate\Http\Request;

class PKWApprovalController extends Controller
{
    public function index()
    {
        $approval = PKWApproval::all();
        $divisi = PKWDivisi::all();
        $users = User::all();
        return view('master.approval', [
            'approval' => $approval,
            'divisi' => $divisi,
            'users' => $users
        ]);
    }

    public function store(Request $request)
    {
        $approval = new PKWApproval;
        $approval->id_bagian = $request->bagian;
        $approval->approval1 = $request->approval1;
        $approval->approval2 = $request->approval2;
        $approval->approval3 = $request->approval3;
        $approval->save();
        return response()->json(['success' => 1, 'message' => 'Create approval succeed']);
    }
}
