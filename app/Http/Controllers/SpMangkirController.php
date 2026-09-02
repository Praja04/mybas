<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\SpPelanggaran;
use App\SpPelanggaranDate;
use App\SpKodePelanggaran;
use App\SpApprovalLog;
use App\HrKaryawan;
use App\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;
use App\Mail\SpNotification;

class SpMangkirController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $user = Auth::user();
        $permissions = view()->shared('permissions') ?: [];
        $isIrRole = in_array('sp_pelanggaran_ir_staff', $permissions) || in_array('sp_pelanggaran_ir_head', $permissions);
        $userDept = ($user ? $user->dept_id : null) ?: session('kode_department');
        $deptCodes = $this->getDeptCodes($userDept);

        $employeeQuery = HrKaryawan::where('active', 'Y');

        if (!$isIrRole && !empty($deptCodes)) {
            $employeeQuery->where(function ($q) use ($deptCodes) {
                $q->whereIn('kode_divisi', $deptCodes)
                    ->orWhereIn('kode_bagian', $deptCodes);
            });
        }

        $employees = $employeeQuery->orderBy('nama', 'asc')->get();

        $query = SpPelanggaran::with(['employee', 'creator', 'dates'])
            ->where('sumber_data', 'MANGKIR')
            ->orderBy('created_at', 'desc');

        if (!$isIrRole && !empty($deptCodes)) {
            $query->whereHas('employee', function ($empQ) use ($deptCodes) {
                $empQ->whereIn('kode_divisi', $deptCodes)
                    ->orWhereIn('kode_bagian', $deptCodes);
            });
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('kode_admin', 'like', "%{$search}%")
                    ->orWhere('kode_ir', 'like', "%{$search}%")
                    ->orWhereHas('employee', function ($empQ) use ($search) {
                        $empQ->where('nama', 'like', "%{$search}%")
                            ->orWhere('nik', 'like', "%{$search}%");
                    });
            });
        }

        if ($request->filled('bulan')) {
            $query->where('bulan_mangkir', $request->bulan);
        }

        $mangkirRecords = $query->paginate(10);

        return view('sp_mangkir.index', compact('employees', 'mangkirRecords'));
    }

    public function trace(Request $request)
    {
        $user = Auth::user();
        $permissions = view()->shared('permissions') ?: [];
        $isAdmin = in_array('sp_pelanggaran_admin', $permissions);
        $isIrRole = in_array('sp_pelanggaran_ir_staff', $permissions) || in_array('sp_pelanggaran_ir_head', $permissions);
        $userDept = ($user ? $user->dept_id : null) ?: session('kode_department');
        $deptCodes = $this->getDeptCodes($userDept);

        $query = SpPelanggaran::with(['employee', 'creator', 'dates'])
            ->where('sumber_data', 'MANGKIR')
            ->orderBy('created_at', 'desc');

        if (!$isIrRole && !empty($deptCodes)) {
            $query->whereHas('employee', function ($empQ) use ($deptCodes) {
                $empQ->whereIn('kode_divisi', $deptCodes)
                    ->orWhereIn('kode_bagian', $deptCodes);
            });
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nomor_sp_generated', 'like', "%{$search}%")
                    ->orWhere('kode_admin', 'like', "%{$search}%")
                    ->orWhere('kode_ir', 'like', "%{$search}%")
                    ->orWhereHas('employee', function ($empQ) use ($search) {
                        $empQ->where('nama', 'like', "%{$search}%")
                            ->orWhere('nik', 'like', "%{$search}%");
                    });
            });
        }

        if ($request->filled('status')) {
            $status = $request->status;
            if ($status === 'AKTIF') {
                $sixMonthsAgo = Carbon::now()->subMonths(6);
                $query->where('current_status', SpPelanggaran::STATUS_APPROVED)
                    ->where(function ($q) use ($sixMonthsAgo) {
                        $q->whereHas('dates', function ($dq) use ($sixMonthsAgo) {
                            $dq->where('tanggal', '>=', $sixMonthsAgo);
                        })->orWhere('created_at', '>=', $sixMonthsAgo);
                    });
            } elseif ($status === 'EXPIRED') {
                $sixMonthsAgo = Carbon::now()->subMonths(6);
                $query->where('current_status', SpPelanggaran::STATUS_APPROVED)
                    ->where(function ($q) use ($sixMonthsAgo) {
                        $q->whereDoesntHave('dates', function ($dq) use ($sixMonthsAgo) {
                            $dq->where('tanggal', '>=', $sixMonthsAgo);
                        })->where('created_at', '<', $sixMonthsAgo);
                    });
            } elseif ($status === 'REJECTED') {
                $query->where('current_status', SpPelanggaran::STATUS_REJECTED);
            } elseif ($status === 'CANCELLED') {
                $query->where('current_status', SpPelanggaran::STATUS_CANCELLED);
            } elseif ($status === 'PROSES_CANCEL') {
                $query->whereIn('current_status', [SpPelanggaran::STATUS_CANCEL_PENDING_DH, SpPelanggaran::STATUS_CANCEL_PENDING_IR, SpPelanggaran::STATUS_CANCEL_PENDING_IR_HEAD]);
            } else {
                $query->where('current_status', $status);
            }
        }

        if ($request->filled('bulan')) {
            $query->where('bulan_mangkir', $request->bulan);
        }

        $sps = $query->paginate($request->input('per_page', 10));

        if ($request->ajax() || $request->wantsJson()) {
            $sps->getCollection()->transform(function ($sp) {
                $sp->is_expired = $sp->isExpiredSp();
                return $sp;
            });

            return response()->json([
                'status' => 'success',
                'data' => $sps,
                'is_admin' => $isAdmin,
                'is_ir_role' => $isIrRole,
            ]);
        }

        return view('sp_mangkir.trace', compact('sps'));
    }

    public function checkAccumulation(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|exists:hr_karyawan,id',
            'tanggal_mangkir' => 'required|date'
        ]);

        $empId = $request->employee_id;
        $tanggal = $request->tanggal_mangkir;
        $bulan = Carbon::parse($tanggal)->format('Y-m');

        $existingHistory = SpPelanggaran::with('dates')
            ->where('employee_id', $empId)
            ->where('sumber_data', 'MANGKIR')
            ->where('bulan_mangkir', $bulan)
            ->whereNotIn('current_status', [SpPelanggaran::STATUS_CANCELLED, SpPelanggaran::STATUS_REJECTED])
            ->orderBy('id', 'asc')
            ->get();

        $existingCount = $existingHistory->count();
        $nextMangkirKe = $existingCount + 1;
        $suggestedKodeAdmin = "Mangkir " . $nextMangkirKe;

        $masterKode = SpKodePelanggaran::where('kode', $suggestedKodeAdmin)
            ->where('kategori_kode', 'MANGKIR')
            ->first();

        // Check active SP status for preview
        $activeSpInfo = app(SpPelanggaranController::class)->checkActiveSp($empId);

        return response()->json([
            'status' => 'success',
            'data' => [
                'bulan' => $bulan,
                'bulan_formatted' => Carbon::parse($tanggal)->isoFormat('MMMM YYYY'),
                'existing_count' => $existingCount,
                'next_mangkir_ke' => $nextMangkirKe,
                'suggested_kode_admin' => $suggestedKodeAdmin,
                'jenis_sp' => $masterKode ? $masterKode->jenis_sp : 'SP I',
                'dasar_pertimbangan' => $masterKode ? $masterKode->dasar_pertimbangan : '-',
                'bentuk_pelanggaran' => $masterKode ? $masterKode->bentuk_pelanggaran : '-',
                'history' => $existingHistory,
                'active_sp_info' => json_decode($activeSpInfo->getContent(), true)
            ]
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|exists:hr_karyawan,id',
            'tanggal_mangkir' => 'required|date',
            'lampiran' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'submit_direct' => 'nullable|boolean'
        ]);

        $empId = $request->employee_id;
        $tanggal = $request->tanggal_mangkir;
        $bulan = Carbon::parse($tanggal)->format('Y-m');

        // Check double submission on same date
        $duplicate = SpPelanggaran::where('employee_id', $empId)
            ->where('sumber_data', 'MANGKIR')
            ->whereHas('dates', function ($dq) use ($tanggal) {
                $dq->whereDate('tanggal', $tanggal);
            })
            ->whereNotIn('current_status', [SpPelanggaran::STATUS_CANCELLED, SpPelanggaran::STATUS_REJECTED])
            ->first();

        if ($duplicate) {
            return response()->json([
                'status' => 'error',
                'message' => 'Karyawan ini sudah memiliki pengajuan Mangkir pada tanggal yang sama (' . Carbon::parse($tanggal)->format('d M Y') . ')!'
            ], 422);
        }

        // Count existing mangkir entries in this month
        $mangkirKe = SpPelanggaran::where('employee_id', $empId)
            ->where('sumber_data', 'MANGKIR')
            ->where('bulan_mangkir', $bulan)
            ->whereNotIn('current_status', [SpPelanggaran::STATUS_CANCELLED, SpPelanggaran::STATUS_REJECTED])
            ->count() + 1;

        $kodeAdmin = "Mangkir " . $mangkirKe;

        $lampiranPath = null;
        if ($request->hasFile('lampiran')) {
            $lampiranPath = $request->file('lampiran')->store('sp_mangkir_attachments', 'public');
        }

        $employee = HrKaryawan::findOrFail($empId);
        $kodeDept = $employee->kode_divisi ?? $employee->kode_bagian ?? null;
        $isSubmitDirect = $request->boolean('submit_direct', false);
        $initialStatus = $isSubmitDirect ? SpPelanggaran::STATUS_PENDING_DH : SpPelanggaran::STATUS_DRAFT;

        // Find Dept Head User
        $deptHeadUser = null;

        // Step 1: User di dept yang sama punya permission Dept Head
        if ($kodeDept) {
            $deptHeadUser = User::where('dept_id', $kodeDept)
                ->where(function ($q) {
                    $q->whereHas('directPermissions', function ($p) {
                        $p->whereIn('codename', ['sp_pelanggaran_dh', 'sp_pelanggaran_approval_dh']);
                    })->orWhereHas('group.permissions', function ($p) {
                        $p->whereIn('codename', ['sp_pelanggaran_dh', 'sp_pelanggaran_approval_dh']);
                    });
                })
                ->whereNotNull('email')->where('email', '!=', '')
                ->first();
        }

        // Step 2: System-wide Dept Head user
        if (!$deptHeadUser) {
            $deptHeadUser = User::where(function ($q) {
                $q->whereHas('directPermissions', function ($p) {
                    $p->whereIn('codename', ['sp_pelanggaran_dh', 'sp_pelanggaran_approval_dh']);
                })->orWhereHas('group.permissions', function ($p) {
                    $p->whereIn('codename', ['sp_pelanggaran_dh', 'sp_pelanggaran_approval_dh']);
                });
            })->whereNotNull('email')->where('email', '!=', '')->first();
        }

        // Step 3: Fallback — user lain di dept yang punya email
        if (!$deptHeadUser && $kodeDept) {
            $deptHeadUser = User::where('dept_id', $kodeDept)
                ->where('id', '!=', $employee->id)
                ->whereNotNull('email')->where('email', '!=', '')
                ->first();
        }

        $spMangkir = SpPelanggaran::create([
            'sumber_data' => 'MANGKIR',
            'employee_id' => $empId,
            'kode_admin' => $kodeAdmin,
            'mangkir_ke' => $mangkirKe,
            'bulan_mangkir' => $bulan,
            'jenis_pelanggaran' => null,
            'pasal_dilanggar' => null,
            'alasan' => "Mangkir/Alpha ke-{$mangkirKe} dalam bulan " . Carbon::parse($tanggal)->isoFormat('MMMM YYYY'),
            'lampiran' => $lampiranPath,
            'status' => 'DRAFT',
            'current_status' => $initialStatus,
            'created_by_user_id' => Auth::id(),
            'assigned_dept_head_id' => $deptHeadUser ? $deptHeadUser->id : null,
            'email_dept_head' => $deptHeadUser ? $deptHeadUser->email : null,
            'email_dept_user' => $employee->email
        ]);

        SpPelanggaranDate::create([
            'sp_pelanggaran_id' => $spMangkir->id,
            'tanggal' => $tanggal,
        ]);

        // Log creation
        SpApprovalLog::create([
            'sp_pelanggaran_id' => $spMangkir->id,
            'user_id' => Auth::id(),
            'role' => 'admin',
            'action' => $isSubmitDirect ? 'SUBMIT_DEPT_HEAD' : 'CREATE_DRAFT',
            'status_from' => null,
            'status_to' => $initialStatus,
            'notes' => 'Input SP Mangkir ke-' . $mangkirKe . ' bulan ' . Carbon::parse($tanggal)->format('F Y')
        ]);

        // Send email if submitted direct to Dept Head
        if ($isSubmitDirect && $deptHeadUser && $deptHeadUser->email) {
            try {
                Mail::to($deptHeadUser->email)->send(new SpNotification($spMangkir, 'SUBMIT_DEPT_HEAD'));
            } catch (\Exception $e) {
                logger()->error('Gagal mengirim email notifikasi SP Mangkir ke Dept Head: ' . $e->getMessage());
            }
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Pengajuan SP Mangkir ke-' . $mangkirKe . ' berhasil disimpan' . ($isSubmitDirect ? ' dan diteruskan ke Dept Head!' : ' sebagai Draft!'),
            'data' => $spMangkir
        ]);
    }

    public function destroy($id)
    {
        $sp = SpPelanggaran::where('sumber_data', 'MANGKIR')->findOrFail($id);

        $permissions = view()->shared('permissions') ?: [];
        $isIrRole = in_array('sp_pelanggaran_ir_staff', $permissions) || in_array('sp_pelanggaran_ir_head', $permissions);

        // Poin 1: Admin hanya bisa hapus sebelum Dept Head approve (DRAFT / PENDING_DH).
        // Kalau sudah disetujui Dept Head, hanya IR role yang bisa hapus.
        if (!$isIrRole && !in_array($sp->current_status, [SpPelanggaran::STATUS_DRAFT, SpPelanggaran::STATUS_PENDING_DH])) {
            return response()->json([
                'status' => 'error',
                'message' => 'Data SP Mangkir yang telah disetujui Dept Head hanya dapat dihapus oleh pihak IR.'
            ], 403);
        }

        if ($sp->lampiran && Storage::disk('public')->exists($sp->lampiran)) {
            Storage::disk('public')->delete($sp->lampiran);
        }

        $sp->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Data SP Mangkir berhasil dihapus.'
        ]);
    }

    private function getDeptCodes($userDept)
    {
        if (!$userDept) {
            return [];
        }

        $deptCodes = [$userDept, (string)$userDept];

        if (is_numeric($userDept)) {
            $dept = \Illuminate\Support\Facades\DB::table('departments')->where('id', $userDept)->first();
            if ($dept && !empty($dept->name)) {
                $deptCodes[] = strtoupper(trim($dept->name));
                $deptCodes[] = $dept->name;
            }
        }

        return array_unique(array_filter($deptCodes));
    }
}
