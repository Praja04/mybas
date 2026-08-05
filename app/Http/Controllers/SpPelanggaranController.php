<?php

namespace App\Http\Controllers;

use App\HrKaryawan;
use App\SpPelanggaran;
use App\SpApprovalLog;
use App\SpKodePelanggaran;
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

        $masterKodes = SpKodePelanggaran::orderBy('kode', 'asc')->get();

        return view('sp_pelanggaran.index', compact('employees', 'editSp', 'masterKodes'));
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
        $sixMonthsAgo = Carbon::now()->subMonths(6);

        // 1. SP AKTIF (APPROVED & Masa berlaku <= 6 Bulan)
        $totalSpActive = (clone $query)->where('current_status', SpPelanggaran::STATUS_APPROVED)
            ->where('tanggal_pelanggaran', '>=', $sixMonthsAgo)
            ->count();

        // 2. SP TIDAK AKTIF / EXPIRED (APPROVED & Masa berlaku > 6 Bulan)
        $totalSpExpired = (clone $query)->where('current_status', SpPelanggaran::STATUS_APPROVED)
            ->where('tanggal_pelanggaran', '<', $sixMonthsAgo)
            ->count();

        // 3. SP+3 / SP BERAT (APPROVED & Jenis SP 3)
        $totalSpBerat = (clone $query)->where('current_status', SpPelanggaran::STATUS_APPROVED)
            ->whereIn('jenis_pelanggaran', ['SP 3', 'Surat Peringatan 3 (SP 3)'])
            ->count();

        // 4. SP DITOLAK (REJECTED oleh Dept Head / IR Staff / IR Head)
        $totalSpRejected = (clone $query)->where('current_status', SpPelanggaran::STATUS_REJECTED)->count();

        // 5. SP CANCEL (CANCELLED oleh Admin / Dept Head / IR Staff)
        $totalSpCancelled = (clone $query)->where('current_status', SpPelanggaran::STATUS_CANCELLED)->count();

        // 6. SP Sedang Diproses
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
        $trends = (clone $query)->select(DB::raw('MONTH(tanggal_pelanggaran) as bulan'), DB::raw('count(*) as total'))
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
        $deptData = (clone $query)->select(DB::raw('COALESCE(hr_karyawan.kode_divisi, hr_karyawan.kode_bagian) as kode_department'), DB::raw('count(sp_pelanggarans.id) as total'))
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
            'totalSpActive', 'totalSpExpired', 'totalSpBerat', 'totalSpRejected', 'totalSpCancelled', 'totalSpProcess', 'chartDistribusi', 'chartTrendData', 'topDepartments', 'currentYear', 'userRole'
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

        $data['nomor_sp_generated'] = null;
        $data['no_sp'] = null;

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

    public function deptHeadMassApprove(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'integer|exists:sp_pelanggarans,id',
            'notes' => 'nullable|string',
        ]);

        $ids = $request->input('ids', []);
        $notes = $request->input('notes', 'Mass Approved by Dept Head');

        $spRecords = SpPelanggaran::whereIn('id', $ids)
            ->where('current_status', SpPelanggaran::STATUS_PENDING_DH)
            ->get();

        if ($spRecords->isEmpty()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Tidak ada SP berstatus Pending Dept Head yang dapat diapprove.'
            ], 422);
        }

        $approvedCount = 0;
        foreach ($spRecords as $sp) {
            $sp->update([
                'current_status' => SpPelanggaran::STATUS_PENDING_IR,
                'dept_head_approved_at' => now(),
                'dept_head_notes' => $notes,
            ]);

            SpApprovalLog::logAction(
                $sp->id,
                Auth::id() ?: session('user_id'),
                SpApprovalLog::ACTION_DEPT_HEAD_APPROVE,
                $notes
            );

            $approvedCount++;
        }

        return response()->json([
            'status' => 'success',
            'message' => "Berhasil menyetujui {$approvedCount} SP sekaligus dan diteruskan ke IR Staff."
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

        if ($request->filled('status')) {
            $st = $request->status;
            if ($st === 'AKTIF') {
                $query->where('kategori_sp', 'AKTIF');
            } elseif ($st === 'EXPIRED') {
                $query->where('kategori_sp', 'EXPIRED');
            } elseif ($st === 'SP3') {
                $query->where('kategori_sp', 'SP3');
            } elseif ($st === 'REJECTED') {
                $query->where('kategori_sp', 'DITOLAK');
            } elseif ($st === 'CANCELLED') {
                $query->where('kategori_sp', 'CANCEL');
            } else {
                $query->where('current_status', $st);
            }
        }

        $sps = $query->paginate(10);
        $spRecords = $sps;

        return view('sp_pelanggaran.trace', compact('sps', 'spRecords'));
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
        // Anti-Looping & Anti-Spam Guard: cegah kirim ulang jika sudah dikirim sebelumnya
        if ($sp->email_sent === 'Y') {
            logger()->info("Email final SP #{$sp->id} sudah pernah dikirim sebelumnya. Pengiriman dibatalkan untuk mencegah looping.");
            return;
        }

        $recipients = [];

        // 1. Email Karyawan yang kena SP
        if ($sp->employee && !empty($sp->employee->email)) {
            $recipients[] = trim($sp->employee->email);
        }

        // 2. Email Dept Head dari Karyawan yang kena SP
        if ($sp->deptHead && !empty($sp->deptHead->email)) {
            $recipients[] = trim($sp->deptHead->email);
        } elseif ($sp->employee) {
            $kodeDept = $sp->employee->kode_divisi ?: $sp->employee->kode_bagian;
            if ($kodeDept) {
                $dhUser = User::where('dept_id', $kodeDept)
                    ->whereNotNull('email')
                    ->where('email', '!=', '')
                    ->first();
                if ($dhUser) {
                    $recipients[] = trim($dhUser->email);
                }
            }
        }

        // 3. Email IR Staff
        if ($sp->irStaff && !empty($sp->irStaff->email)) {
            $recipients[] = trim($sp->irStaff->email);
        } else {
            $irStaffUsers = User::where(function($q) {
                $q->whereHas('directPermissions', function($permQ) {
                    $permQ->where('codename', 'sp_pelanggaran_ir_staff');
                })->orWhereHas('group.permissions', function($permQ) {
                    $permQ->where('codename', 'sp_pelanggaran_ir_staff');
                });
            })->whereNotNull('email')->where('email', '!=', '')->pluck('email')->toArray();

            foreach ($irStaffUsers as $email) {
                if (!empty($email)) {
                    $recipients[] = trim($email);
                }
            }
        }

        // Unique & filter empty emails
        $recipients = array_values(array_unique(array_filter($recipients)));

        if (empty($recipients)) {
            logger()->warning("Tidak ada penerima email yang valid untuk SP final ID #{$sp->id}");
            return;
        }

        $sentCount = 0;
        foreach ($recipients as $recipientEmail) {
            // Validasi format sintaks email (mencegah typo fatal)
            if (!filter_var($recipientEmail, FILTER_VALIDATE_EMAIL)) {
                logger()->warning("Format email tidak valid (diabaikan): {$recipientEmail} untuk SP #{$sp->id}");
                continue;
            }

            try {
                Mail::to($recipientEmail)->send(new SpNotification($sp, 'final'));
                $sentCount++;
                logger()->info("Email PDF Final SP #{$sp->id} berhasil dikirim ke: {$recipientEmail}");
            } catch (\Exception $e) {
                // Jika 1 email gagal / typo, perulangan TETAP berlanjut ke email penerima lain!
                logger()->error("Gagal mengirim email final SP #{$sp->id} ke {$recipientEmail}: " . $e->getMessage());
            }
        }

        if ($sentCount > 0) {
            $sp->update([
                'email_sent' => 'Y',
                'email_sent_at' => now(),
            ]);
        }
    }

    public function cancelSp(Request $request, $id)
    {
        $sp = SpPelanggaran::findOrFail($id);

        $request->validate([
            'notes' => 'required|string'
        ]);

        $sp->update([
            'current_status' => SpPelanggaran::STATUS_CANCELLED,
            'dept_head_notes' => $request->input('notes'),
        ]);

        SpApprovalLog::logAction(
            $sp->id,
            Auth::id() ?: session('user_id'),
            SpApprovalLog::ACTION_CANCEL,
            'SP Cancelled: ' . $request->input('notes')
        );

        return response()->json([
            'status' => 'success',
            'message' => 'SP berhasil dibatalkan (CANCELLED).'
        ]);
    }

    public function masterKodeIndex(Request $request)
    {
        if (!Auth::check() && !session('login') && !session('username')) {
            return redirect('/login');
        }

        $query = SpKodePelanggaran::query()->orderBy('kode', 'asc');
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('kode', 'like', "%{$search}%")
                  ->orWhere('nama_pelanggaran', 'like', "%{$search}%")
                  ->orWhere('jenis_sp', 'like', "%{$search}%");
            });
        }

        $masterKodes = $query->paginate(10);
        return view('sp_pelanggaran.master_kode', compact('masterKodes'));
    }

    public function masterKodeStore(Request $request)
    {
        $request->validate([
            'kode' => 'required|string|max:50|unique:sp_kode_pelanggarans,kode',
            'nama_pelanggaran' => 'required|string|max:255',
            'jenis_sp' => 'required|string',
            'pasal_dilanggar' => 'nullable|string',
            'deskripsi' => 'nullable|string',
        ]);

        SpKodePelanggaran::create($request->only([
            'kode', 'nama_pelanggaran', 'jenis_sp', 'pasal_dilanggar', 'deskripsi'
        ]));

        return response()->json([
            'status' => 'success',
            'message' => 'Kode Pelanggaran berhasil ditambahkan!'
        ]);
    }

    public function masterKodeUpdate(Request $request, $id)
    {
        $kodeModel = SpKodePelanggaran::findOrFail($id);

        $request->validate([
            'kode' => 'required|string|max:50|unique:sp_kode_pelanggarans,kode,' . $id,
            'nama_pelanggaran' => 'required|string|max:255',
            'jenis_sp' => 'required|string',
            'pasal_dilanggar' => 'nullable|string',
            'deskripsi' => 'nullable|string',
        ]);

        $kodeModel->update($request->only([
            'kode', 'nama_pelanggaran', 'jenis_sp', 'pasal_dilanggar', 'deskripsi'
        ]));

        return response()->json([
            'status' => 'success',
            'message' => 'Kode Pelanggaran berhasil diperbarui!'
        ]);
    }

    public function masterKodeDestroy($id)
    {
        $kodeModel = SpKodePelanggaran::findOrFail($id);
        $kodeModel->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Kode Pelanggaran berhasil dihapus!'
        ]);
    }

    public function getKodeDetail($id)
    {
        $kodeModel = SpKodePelanggaran::findOrFail($id);
        return response()->json([
            'status' => 'success',
            'data' => $kodeModel
        ]);
    }

    public function exportData(Request $request)
    {
        if (!Auth::check() && !session('login') && !session('username')) {
            return redirect('/login');
        }

        $user = Auth::user();
        $permissions = view()->shared('permissions') ?: [];
        $isIrRole = in_array('sp_pelanggaran_ir_staff', $permissions) || in_array('sp_pelanggaran_ir_head', $permissions);
        $userDept = ($user ? $user->dept_id : null) ?: session('kode_department');

        $query = SpPelanggaran::with('employee')->orderBy('tanggal_pelanggaran', 'desc');

        if (!$isIrRole && $userDept) {
            $query->whereHas('employee', function ($empQ) use ($userDept) {
                $empQ->where('kode_divisi', $userDept)
                     ->orWhere('kode_bagian', $userDept);
            });
        }

        // Filter Klasifikasi
        $kategori = $request->get('kategori', 'ALL');
        if ($kategori !== 'ALL') {
            if (in_array($kategori, ['AKTIF', 'EXPIRED', 'SP3', 'DITOLAK', 'CANCEL', 'PROSES'])) {
                $query->where('kategori_sp', $kategori);
            }
        }

        // Filter Tanggal
        if ($request->filled('start_date')) {
            $query->whereDate('tanggal_pelanggaran', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('tanggal_pelanggaran', '<=', $request->end_date);
        }

        $sps = $query->get();
        $format = strtolower($request->get('format', 'excel'));

        $kategoriLabelMap = [
            'ALL' => 'Semua Klasifikasi (Aktif, Expired, SP3, Ditolak, Cancel)',
            'AKTIF' => 'SP Aktif (<= 6 Bulan)',
            'EXPIRED' => 'Tidak Aktif (Expired > 6 Bulan)',
            'SP3' => 'SP+3 / SP Berat',
            'DITOLAK' => 'SP Ditolak',
            'CANCEL' => 'SP Cancel / Dibatalkan',
            'PROSES' => 'SP Sedang Diproses',
        ];
        $kategoriLabel = $kategoriLabelMap[$kategori] ?? 'Semua Data';

        if ($format === 'pdf') {
            $pdf = \Barryvdh\DomPDF\Facade::loadHTML(view('sp_pelanggaran.export_pdf', compact('sps', 'kategoriLabel', 'kategori'))->render());
            $pdf->setPaper('a4', 'landscape');
            $fileName = 'Rekapitulasi_SP_' . $kategori . '_' . date('Ymd_His') . '.pdf';
            return $pdf->download($fileName);
        }

        // Default: Export to Excel (.xlsx) using PhpSpreadsheet
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Rekap SP');

        // Header Title
        $sheet->setCellValue('A1', 'PT. BUMI ALAM SEGAR');
        $sheet->setCellValue('A2', 'LAPORAN REKAPITULASI & RIWAYAT SURAT PERINGATAN (SP) KARYAWAN');
        $sheet->setCellValue('A3', 'Filter: ' . $kategoriLabel . ' | Tanggal Cetak: ' . date('d/m/Y H:i'));

        $sheet->mergeCells('A1:J1');
        $sheet->mergeCells('A2:J2');
        $sheet->mergeCells('A3:J3');

        $sheet->getStyle('A1:A2')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1:A3')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        // Table Headers
        $headers = [
            'A5' => 'NO',
            'B5' => 'NOMOR SP',
            'C5' => 'NIK',
            'D5' => 'NAMA KARYAWAN',
            'E5' => 'DEPT / BAGIAN',
            'F5' => 'GROUP',
            'G5' => 'JENIS SP',
            'H5' => 'TGL KEJADIAN',
            'I5' => 'BERLAKU SAMPAI',
            'J5' => 'KLASIFIKASI STATUS',
        ];

        foreach ($headers as $cell => $val) {
            $sheet->setCellValue($cell, $val);
        }

        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => '1E3C72']],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
            'borders' => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN]],
        ];
        $sheet->getStyle('A5:J5')->applyFromArray($headerStyle);

        $row = 6;
        foreach ($sps as $idx => $sp) {
            $emp = $sp->employee;
            $divisiName = null;
            $bagianName = null;

            if ($emp) {
                if (!empty($emp->kode_divisi)) {
                    $div = \DB::table('pkw_divisi')->where('id', $emp->kode_divisi)->orWhere('kode_divisi', $emp->kode_divisi)->first();
                    $divisiName = $div ? ($div->nama_divisi ?? $div->kode_divisi) : (\DB::table('departments')->where('id', $emp->kode_divisi)->value('name') ?: $emp->kode_divisi);
                }
                if (!empty($emp->kode_bagian)) {
                    $bag = \DB::table('pkw_bagian')->where('id', $emp->kode_bagian)->orWhere('kode_bagian', $emp->kode_bagian)->first();
                    $bagianName = $bag ? ($bag->nama_bagian ?? $bag->kode_bagian) : $emp->kode_bagian;
                }
            }
            $deptBagian = $divisiName && $bagianName ? "{$divisiName} - {$bagianName}" : ($divisiName ?: ($bagianName ?: '-'));
            $groupVal = $emp ? ($emp->kode_group ?? $emp->group ?? '-') : '-';

            $sheet->setCellValue('A' . $row, $idx + 1);
            $sheet->setCellValue('B' . $row, $sp->nomor_sp_generated ?: ($sp->no_sp ?: 'DRAFT'));
            $sheet->setCellValue('C' . $row, $emp->nik ?? '-');
            $sheet->setCellValue('D' . $row, $emp->nama ?? '-');
            $sheet->setCellValue('E' . $row, $deptBagian);
            $sheet->setCellValue('F' . $row, $groupVal ?: '-');
            $sheet->setCellValue('G' . $row, $sp->jenis_pelanggaran);
            $sheet->setCellValue('H' . $row, \Carbon\Carbon::parse($sp->tanggal_pelanggaran)->format('d/m/Y'));
            $sheet->setCellValue('I' . $row, $sp->masa_berlaku_sampai ? \Carbon\Carbon::parse($sp->masa_berlaku_sampai)->format('d/m/Y') : '-');
            $sheet->setCellValue('J' . $row, $sp->kategori_sp ?: $sp->current_status);

            $sheet->getStyle('A' . $row . ':J' . $row)->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
            $sheet->getStyle('A' . $row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('C' . $row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('F' . $row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('H' . $row . ':J' . $row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

            $row++;
        }

        foreach (range('A', 'J') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $fileName = 'Rekapitulasi_SP_' . $kategori . '_' . date('Ymd_His') . '.xlsx';
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $fileName . '"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        exit;
    }

    public function importMasterKode(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls'
        ]);

        try {
            $file = $request->file('file');
            $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file->getPathname());

            $importedCount = 0;
            $updatedCount = 0;

            foreach ($spreadsheet->getSheetNames() as $sheetName) {
                $sheet = $spreadsheet->getSheetByName($sheetName);
                $rows = $sheet->toArray(null, true, true, true);

                foreach ($rows as $row) {
                    $colA = trim($row['A'] ?? '');
                    $colB = trim($row['B'] ?? '');
                    $colC = trim($row['C'] ?? '');
                    $colD = trim($row['D'] ?? '');

                    if (empty($colA) || strtolower($colA) === 'nama pelanggaran' || strtolower($colA) === 'kode') {
                        continue;
                    }

                    $namaPelanggaran = $colA;
                    $deskripsi = $colB ?: $colA;
                    $pasal = $colC ?: null;
                    $jenisSpRaw = $colD ?: 'SP 1';

                    $jenisSp = $jenisSpRaw;
                    if (stripos($jenisSpRaw, 'Teguran') !== false) {
                        $jenisSp = 'Teguran Lisan';
                    } elseif (stripos($jenisSpRaw, 'SP I') !== false && stripos($jenisSpRaw, 'SP II') === false && stripos($jenisSpRaw, 'SP III') === false) {
                        $jenisSp = 'SP 1';
                    } elseif (stripos($jenisSpRaw, 'SP II') !== false && stripos($jenisSpRaw, 'SP III') === false) {
                        $jenisSp = 'SP 2';
                    } elseif (stripos($jenisSpRaw, 'SP III') !== false || stripos($jenisSpRaw, 'SP 3') !== false) {
                        $jenisSp = 'SP 3';
                    }

                    $slugKode = 'KODE-' . strtoupper(substr(md5($namaPelanggaran), 0, 6));

                    $existing = SpKodePelanggaran::where('nama_pelanggaran', $namaPelanggaran)->first();

                    if ($existing) {
                        $existing->update([
                            'jenis_sp' => $jenisSp,
                            'pasal_dilanggar' => $pasal,
                            'deskripsi' => $deskripsi,
                        ]);
                        $updatedCount++;
                    } else {
                        SpKodePelanggaran::create([
                            'kode' => $slugKode,
                            'nama_pelanggaran' => $namaPelanggaran,
                            'jenis_sp' => $jenisSp,
                            'pasal_dilanggar' => $pasal,
                            'deskripsi' => $deskripsi,
                        ]);
                        $importedCount++;
                    }
                }
            }

            return response()->json([
                'status' => 'success',
                'message' => "Import Master Kode SP Berhasil! Total Baru: {$importedCount}, Diperbarui: {$updatedCount}"
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal mengimpor file Excel: ' . $e->getMessage()
            ], 500);
        }
    }
}
