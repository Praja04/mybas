<?php

namespace App\Http\Controllers;

use App\HrKaryawan;
use App\SpPelanggaran;
use App\SpApprovalLog;
use App\User;
use App\Mail\SpNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SpPelanggaranController extends Controller
{
    public function index(Request $request)
    {
        if (!Auth::check() && !session('login') && !session('username')) {
            return redirect('/login');
        }

        $user = Auth::user();
        $permissions = view()->shared('permissions') ?: [];
        $isIrRole = in_array('sp_pelanggaran_ir_staff', $permissions) || in_array('sp_pelanggaran_ir_head', $permissions);
        $userDept = ($user ? $user->dept_id : null) ?: session('kode_department');

        // Fetch list of active employees for selection dropdown from hr_karyawan
        $employeeQuery = HrKaryawan::query();
        if (!$isIrRole && $userDept) {
            $employeeQuery->where(function($q) use ($userDept) {
                $q->where('kode_divisi', $userDept)
                  ->orWhere('kode_bagian', $userDept);
            });
        }
        $employees = $employeeQuery->orderBy('nama', 'asc')->get();

        if ($employees->isEmpty()) {
            $userQuery = User::query();
            if (!$isIrRole && $userDept) {
                $userQuery->where('dept_id', $userDept);
            }
            $employees = $userQuery->orderBy('name', 'asc')->get()->map(function($u) {
                $u->nama = $u->name;
                $u->nik = $u->username;
                $u->kode_divisi = $u->dept_id;
                return $u;
            });
        }

        // Check for edit mode
        $editSp = null;
        if ($request->filled('edit')) {
            $editSp = SpPelanggaran::find($request->edit);
            if ($editSp && !in_array($editSp->current_status, [SpPelanggaran::STATUS_DRAFT, SpPelanggaran::STATUS_REJECTED])) {
                $editSp = null;
            }
        }

        return view('sp_pelanggaran.index', compact('employees', 'editSp'));
    }

    public function dashboard(Request $request)
    {
        if (!Auth::check() && !session('login') && !session('username')) {
            return redirect('/login');
        }

        $userRole = session('user_role');
        $query = SpPelanggaran::with('employee');

        // Filter for Dept Head
        if ($userRole === 'dept_head') {
            $kodeDept = session('kode_department');
            $query->whereHas('employee', function ($q) use ($kodeDept) {
                $q->where('kode_divisi', $kodeDept)
                  ->orWhere('kode_bagian', $kodeDept);
            });
        }

        $currentYear = Carbon::now()->year;

        // Clone queries for metrics
        $activeQuery = clone $query;
        $trendQuery = clone $query;
        $deptQuery = clone $query;

        // 1. Total SP Aktif (Tahun ini, APPROVED)
        $totalSpActive = $activeQuery->where('current_status', SpPelanggaran::STATUS_APPROVED)
            ->whereYear('tanggal_pelanggaran', $currentYear)
            ->count();

        // 2. SP Sedang Diproses
        $totalSpProcess = (clone $query)->whereIn('current_status', [
            SpPelanggaran::STATUS_DRAFT,
            SpPelanggaran::STATUS_PENDING_DH,
            SpPelanggaran::STATUS_PENDING_IR,
            SpPelanggaran::STATUS_PENDING_IR_HEAD,
        ])->count();

        // 3. Distribusi Jenis Pelanggaran
        $distribution = (clone $query)->select('jenis_pelanggaran', DB::raw('count(*) as total'))
            ->where('current_status', SpPelanggaran::STATUS_APPROVED)
            ->whereYear('tanggal_pelanggaran', $currentYear)
            ->groupBy('jenis_pelanggaran')
            ->get();

        $chartDistribusi = [];
        foreach ($distribution as $dist) {
            $chartDistribusi[] = [
                'name' => $dist->jenis_pelanggaran,
                'y' => (int)$dist->total
            ];
        }

        // 4. Tren SP per Bulan
        $trends = $trendQuery->select(DB::raw('MONTH(tanggal_pelanggaran) as bulan'), DB::raw('count(*) as total'))
            ->where('current_status', SpPelanggaran::STATUS_APPROVED)
            ->whereYear('tanggal_pelanggaran', $currentYear)
            ->groupBy(DB::raw('MONTH(tanggal_pelanggaran)'))
            ->get();
        
        $chartTrendData = array_fill(0, 12, 0);
        foreach ($trends as $trend) {
            $chartTrendData[$trend->bulan - 1] = (int)$trend->total;
        }

        // 5. Top Departemen Penyumbang SP
        $topDepartments = [];
        $deptData = $deptQuery->select(DB::raw('COALESCE(hr_karyawan.kode_divisi, hr_karyawan.kode_bagian) as kode_department'), DB::raw('count(sp_pelanggarans.id) as total'))
            ->join('hr_karyawan', 'hr_karyawan.id', '=', 'sp_pelanggarans.employee_id')
            ->where('sp_pelanggarans.current_status', SpPelanggaran::STATUS_APPROVED)
            ->whereYear('sp_pelanggarans.tanggal_pelanggaran', $currentYear)
            ->groupBy(DB::raw('COALESCE(hr_karyawan.kode_divisi, hr_karyawan.kode_bagian)'))
            ->orderBy('total', 'desc')
            ->limit(5)
            ->get();
        
        foreach ($deptData as $d) {
            $topDepartments[] = [
                'dept' => $d->kode_department ?? 'UNKNOWN',
                'total' => $d->total
            ];
        }

        return view('sp_pelanggaran.dashboard', compact(
            'totalSpActive', 'totalSpProcess', 'chartDistribusi', 'chartTrendData', 'topDepartments', 'currentYear', 'userRole'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:hr_karyawan,id',
            'no_sp' => 'nullable|string|max:100',
            'tanggal_pelanggaran' => 'required|date',
            'jenis_pelanggaran' => 'required|string',
            'status' => 'required|in:DRAFT,SELESAI',
            'alasan' => 'nullable|string',
            'lampiran' => 'nullable|file|mimes:pdf,jpg,jpeg,png,doc,docx|max:5120',
            'sesuai_ketentuan' => 'required|boolean',
            'sumber_data' => 'nullable|string',
            'pasal_dilanggar' => 'nullable|string',
            'uraian_pelanggaran' => 'nullable|string',
        ]);

        $data = $validated;
        $data['created_by_user_id'] = Auth::id() ?: session('user_id');
        $data['current_status'] = SpPelanggaran::STATUS_DRAFT;

        if ($request->hasFile('lampiran')) {
            $file = $request->file('lampiran');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('uploads/sp'), $filename);
            $data['lampiran'] = 'uploads/sp/' . $filename;
        }

        $sp = SpPelanggaran::create($data);

        return response()->json([
            'status' => 'success',
            'message' => 'Data pelanggaran berhasil disimpan sebagai Draft.',
            'data' => $sp
        ]);
    }

    public function update(Request $request, $id)
    {
        $sp = SpPelanggaran::findOrFail($id);

        if (!in_array($sp->current_status, [SpPelanggaran::STATUS_DRAFT, SpPelanggaran::STATUS_REJECTED])) {
            return response()->json([
                'status' => 'error',
                'message' => 'Data pelanggaran tidak dapat diedit pada tahap ini.'
            ], 422);
        }

        $validated = $request->validate([
            'employee_id' => 'required|exists:hr_karyawan,id',
            'no_sp' => 'nullable|string|max:100',
            'tanggal_pelanggaran' => 'required|date',
            'jenis_pelanggaran' => 'required|string',
            'status' => 'required|in:DRAFT,SELESAI',
            'alasan' => 'nullable|string',
            'lampiran' => 'nullable|file|mimes:pdf,jpg,jpeg,png,doc,docx|max:5120',
            'sesuai_ketentuan' => 'required|boolean',
            'sumber_data' => 'nullable|string',
            'pasal_dilanggar' => 'nullable|string',
            'uraian_pelanggaran' => 'nullable|string',
        ]);

        $data = $validated;

        if ($request->hasFile('lampiran')) {
            if ($sp->lampiran && file_exists(public_path($sp->lampiran))) {
                @unlink(public_path($sp->lampiran));
            }

            $file = $request->file('lampiran');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('uploads/sp'), $filename);
            $data['lampiran'] = 'uploads/sp/' . $filename;
        }

        $sp->update($data);

        return response()->json([
            'status' => 'success',
            'message' => 'Data pelanggaran berhasil diperbarui.',
            'data' => $sp
        ]);
    }

    public function destroy($id)
    {
        $sp = SpPelanggaran::findOrFail($id);

        if ($sp->current_status !== SpPelanggaran::STATUS_DRAFT) {
            return response()->json([
                'status' => 'error',
                'message' => 'Data pelanggaran tidak dapat dihapus pada tahap ini.'
            ], 422);
        }

        if ($sp->lampiran && file_exists(public_path($sp->lampiran))) {
            @unlink(public_path($sp->lampiran));
        }

        $sp->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Data pelanggaran berhasil dihapus.'
        ]);
    }

    public function checkActiveSp($employee_id)
    {
        $sixMonthsAgo = Carbon::now()->subMonths(6);

        $activeSp = SpPelanggaran::where('employee_id', $employee_id)
            ->where('current_status', SpPelanggaran::STATUS_APPROVED)
            ->whereDate('tanggal_pelanggaran', '>=', $sixMonthsAgo)
            ->orderBy('tanggal_pelanggaran', 'desc')
            ->first();

        if ($activeSp) {
            return response()->json([
                'is_active' => true,
                'message' => 'Karyawan sedang dalam masa SP aktif.',
                'data' => [
                    'no_sp' => $activeSp->nomor_sp_generated ?: $activeSp->no_sp,
                    'jenis_pelanggaran' => $activeSp->jenis_pelanggaran,
                    'tanggal_pelanggaran' => $activeSp->tanggal_pelanggaran,
                    'alasan' => $activeSp->alasan
                ]
            ]);
        }

        return response()->json([
            'is_active' => false
        ]);
    }

    public function reportToAdmin(Request $request, $id)
    {
        $sp = SpPelanggaran::findOrFail($id);
        $sp->update(['reported_to_admin' => true]);

        return response()->json([
            'status' => 'success',
            'message' => 'Laporan pelanggaran tidak sesuai berhasil dikirim ke Admin.'
        ]);
    }

    public function submitToDeptHead(Request $request, $id)
    {
        $sp = SpPelanggaran::with('employee')->findOrFail($id);

        if (!$sp->canSubmitToDeptHead()) {
            return response()->json([
                'status' => 'error',
                'message' => 'SP tidak dapat disubmit ke Dept Head. Pastikan semua data terisi lengkap.'
            ], 422);
        }

        $kodeDept = $sp->employee->kode_divisi ?? $sp->employee->kode_bagian ?? null;
        
        // 1. Search user with Dept Head permission in employee's department
        $deptHead = User::where(function($q) use ($kodeDept) {
            if ($kodeDept) {
                $q->where('dept_id', $kodeDept);
            }
        })->where(function($q) {
            $q->whereHas('directPermissions', function($permQ) {
                $permQ->whereIn('codename', ['sp_pelanggaran_dh', 'sp_pelanggaran_approval_dh']);
            })->orWhereHas('group.permissions', function($permQ) {
                $permQ->whereIn('codename', ['sp_pelanggaran_dh', 'sp_pelanggaran_approval_dh']);
            });
        })->whereNotNull('email')->where('email', '!=', '')->first();

        // 2. Search any user with Dept Head permission in system with valid email
        if (!$deptHead) {
            $deptHead = User::where(function($q) {
                $q->whereHas('directPermissions', function($permQ) {
                    $permQ->whereIn('codename', ['sp_pelanggaran_dh', 'sp_pelanggaran_approval_dh']);
                })->orWhereHas('group.permissions', function($permQ) {
                    $permQ->whereIn('codename', ['sp_pelanggaran_dh', 'sp_pelanggaran_approval_dh']);
                });
            })->whereNotNull('email')->where('email', '!=', '')->first();
        }

        // 3. Fallback: Search user in employee's department with non-empty email
        if (!$deptHead && $kodeDept) {
            $deptHead = User::where('dept_id', $kodeDept)
                ->where('id', '!=', $sp->employee_id)
                ->whereNotNull('email')
                ->where('email', '!=', '')
                ->first();
        }

        $sp->update([
            'current_status' => SpPelanggaran::STATUS_PENDING_DH,
            'assigned_dept_head_id' => $deptHead ? $deptHead->id : null,
        ]);

        SpApprovalLog::logAction(
            $sp->id,
            Auth::id() ?: session('user_id'),
            SpApprovalLog::ACTION_SUBMIT,
            'SP submitted to Dept Head'
        );

        if ($deptHead) {
            $this->sendApprovalNotification($sp, $deptHead, 'pending_dh');
        }

        return response()->json([
            'status' => 'success',
            'message' => 'SP berhasil disubmit ke Dept Head.'
        ]);
    }

    public function deptHeadApprove(Request $request, $id)
    {
        $sp = SpPelanggaran::findOrFail($id);

        if (!$sp->canDeptHeadReview()) {
            return response()->json([
                'status' => 'error',
                'message' => 'SP tidak dapat diapprove pada tahap ini.'
            ], 422);
        }

        $sp->update([
            'current_status' => SpPelanggaran::STATUS_PENDING_IR,
            'dept_head_approved_at' => now(),
            'dept_head_notes' => $request->input('notes'),
        ]);

        SpApprovalLog::logAction(
            $sp->id,
            Auth::id() ?: session('user_id'),
            SpApprovalLog::ACTION_DEPT_HEAD_APPROVE,
            $request->input('notes', 'Approved by Dept Head')
        );

        return response()->json([
            'status' => 'success',
            'message' => 'SP berhasil diapprove dan diteruskan ke IR Staff.'
        ]);
    }

    public function deptHeadReject(Request $request, $id)
    {
        $sp = SpPelanggaran::findOrFail($id);

        if (!$sp->canDeptHeadReview()) {
            return response()->json([
                'status' => 'error',
                'message' => 'SP tidak dapat ditolak pada tahap ini.'
            ], 422);
        }

        $request->validate([
            'notes' => 'required|string'
        ]);

        $sp->update([
            'current_status' => SpPelanggaran::STATUS_DRAFT,
            'dept_head_notes' => $request->input('notes'),
        ]);

        SpApprovalLog::logAction(
            $sp->id,
            Auth::id() ?: session('user_id'),
            SpApprovalLog::ACTION_DEPT_HEAD_REJECT,
            $request->input('notes')
        );

        return response()->json([
            'status' => 'success',
            'message' => 'SP ditolak dan dikembalikan ke Admin untuk perbaikan.'
        ]);
    }

    public function irStaffSubmit(Request $request, $id)
    {
        $sp = SpPelanggaran::findOrFail($id);

        if (!$sp->canIrStaffReview()) {
            return response()->json([
                'status' => 'error',
                'message' => 'SP tidak dapat di-submit pada tahap ini.'
            ], 422);
        }

        $sp->update([
            'current_status' => SpPelanggaran::STATUS_PENDING_IR_HEAD,
            'ir_staff_notes' => $request->input('notes'),
        ]);

        SpApprovalLog::logAction(
            $sp->id,
            Auth::id() ?: session('user_id'),
            SpApprovalLog::ACTION_IR_STAFF_SUBMIT,
            $request->input('notes', 'Submitted to IR Head')
        );

        return response()->json([
            'status' => 'success',
            'message' => 'SP berhasil diajukan ke IR Head untuk persetujuan final.'
        ]);
    }

    public function irHeadApprove(Request $request, $id)
    {
        $sp = SpPelanggaran::findOrFail($id);

        if (!$sp->canIrHeadReview()) {
            return response()->json([
                'status' => 'error',
                'message' => 'SP tidak dapat diapprove pada tahap ini.'
            ], 422);
        }

        $nomorSp = SpPelanggaran::generateNomorSp($sp->employee_id);

        $sp->update([
            'current_status' => SpPelanggaran::STATUS_APPROVED,
            'ir_head_approved_at' => now(),
            'ir_head_notes' => $request->input('notes'),
            'nomor_sp_generated' => $nomorSp,
        ]);

        SpApprovalLog::logAction(
            $sp->id,
            Auth::id() ?: session('user_id'),
            SpApprovalLog::ACTION_IR_HEAD_APPROVE,
            $request->input('notes', 'Approved by IR Head - Final')
        );

        $this->sendFinalEmail($sp);

        return response()->json([
            'status' => 'success',
            'message' => "SP berhasil diapprove! Nomor SP: {$nomorSp}.",
            'data' => [
                'nomor_sp' => $nomorSp
            ]
        ]);
    }

    public function irHeadReject(Request $request, $id)
    {
        $sp = SpPelanggaran::findOrFail($id);

        if (!$sp->canIrHeadReview()) {
            return response()->json([
                'status' => 'error',
                'message' => 'SP tidak dapat ditolak pada tahap ini.'
            ], 422);
        }

        $request->validate([
            'notes' => 'required|string'
        ]);

        $sp->update([
            'current_status' => SpPelanggaran::STATUS_DRAFT,
            'ir_head_notes' => $request->input('notes'),
        ]);

        SpApprovalLog::logAction(
            $sp->id,
            Auth::id() ?: session('user_id'),
            SpApprovalLog::ACTION_IR_HEAD_REJECT,
            $request->input('notes')
        );

        return response()->json([
            'status' => 'success',
            'message' => 'SP ditolak dan dikembalikan ke Admin untuk perbaikan.'
        ]);
    }

    public function approvalList(Request $request)
    {
        if (!Auth::check() && !session('login') && !session('username')) {
            return redirect('/login');
        }

        $permissions = view()->shared('permissions') ?: [];

        $userRole = null;
        if (in_array('sp_pelanggaran_ir_head', $permissions)) {
            $userRole = 'ir_head';
        } elseif (in_array('sp_pelanggaran_ir_staff', $permissions)) {
            $userRole = 'ir_staff';
        } elseif (in_array('sp_pelanggaran_dh', $permissions)) {
            $userRole = 'dept_head';
        }

        $user = Auth::user();
        $userDept = ($user ? $user->dept_id : null) ?: session('kode_department');

        $query = SpPelanggaran::with('employee', 'creator')
            ->orderBy('updated_at', 'desc');

        if ($userRole === 'dept_head' && $userDept) {
            $query->whereHas('employee', function ($empQ) use ($userDept) {
                $empQ->where('kode_divisi', $userDept)
                     ->orWhere('kode_bagian', $userDept);
            });
        }

        switch ($userRole) {
            case 'dept_head':
                $query->where('current_status', SpPelanggaran::STATUS_PENDING_DH);
                break;
            case 'ir_staff':
                $query->where('current_status', SpPelanggaran::STATUS_PENDING_IR);
                break;
            case 'ir_head':
                $query->where('current_status', SpPelanggaran::STATUS_PENDING_IR_HEAD);
                break;
        }

        $spRecords = $query->paginate(10);

        $viewMap = [
            'dept_head' => 'sp_pelanggaran.approval_dept_head',
            'ir_staff'  => 'sp_pelanggaran.approval_ir_staff',
            'ir_head'   => 'sp_pelanggaran.approval_ir_head',
        ];

        $viewName = $viewMap[$userRole] ?? 'sp_pelanggaran.approval_ir_staff';

        return view($viewName, compact('spRecords'));
    }

    public function trace(Request $request)
    {
        if (!Auth::check() && !session('login') && !session('username')) {
            return redirect('/login');
        }

        $user = Auth::user();
        $permissions = view()->shared('permissions') ?: [];
        $isIrRole = in_array('sp_pelanggaran_ir_staff', $permissions) || in_array('sp_pelanggaran_ir_head', $permissions);
        $userDept = ($user ? $user->dept_id : null) ?: session('kode_department');

        $query = SpPelanggaran::with('employee', 'creator')->orderBy('updated_at', 'desc');

        if (!$isIrRole && $userDept) {
            $query->whereHas('employee', function ($empQ) use ($userDept) {
                $empQ->where('kode_divisi', $userDept)
                     ->orWhere('kode_bagian', $userDept);
            });
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('no_sp', 'like', "%{$search}%")
                  ->orWhere('nomor_sp_generated', 'like', "%{$search}%")
                  ->orWhereHas('employee', function ($empQ) use ($search) {
                      $empQ->where('nama', 'like', "%{$search}%")
                           ->orWhere('nik', 'like', "%{$search}%");
                  });
            });
        }

        $spRecords = $query->paginate(10);

        return view('sp_pelanggaran.trace', compact('spRecords'));
    }

    public function getSpDetail($id)
    {
        $sp = SpPelanggaran::with('employee', 'creator', 'approvalLogs', 'deptHead', 'irStaff')
            ->findOrFail($id);

        return response()->json([
            'status' => 'success',
            'data' => $sp
        ]);
    }

    private function sendApprovalNotification($sp, $user, $type)
    {
        if (!$user || !$user->email) {
            return;
        }

        try {
            Mail::to($user->email)->send(new SpNotification($sp, $type));
        } catch (\Exception $e) {
            logger()->error('Gagal mengirim email notifikasi SP: ' . $e->getMessage());
        }
    }

    private function sendFinalEmail(SpPelanggaran $sp)
    {
        $employee = $sp->employee;
        if (!$employee || !$employee->email) {
            return;
        }

        try {
            Mail::to($employee->email)->send(new SpNotification($sp, 'final'));
            $sp->update([
                'email_sent' => 'Y',
                'email_sent_at' => now(),
            ]);
        } catch (\Exception $e) {
            logger()->error('Gagal mengirim email final SP: ' . $e->getMessage());
        }
    }
}
