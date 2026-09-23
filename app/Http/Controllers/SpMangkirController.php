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
use App\Services\SpMangkirAuditService;

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

    public function audit(Request $request)
    {
        $permissions = view()->shared('permissions') ?: [];
        $isIrStaff  = in_array('sp_pelanggaran_ir_staff', $permissions);
        $isIrHead   = in_array('sp_pelanggaran_ir_head', $permissions);
        $isDeptHead = in_array('sp_pelanggaran_dh', $permissions) || in_array('sp_pelanggaran_approval_dh', $permissions);

        if (!$isIrStaff && !$isIrHead && !$isDeptHead) {
            abort(403, 'Akses Ditolak. Fitur Audit Mangkir hanya dapat diakses oleh IR Staff, Dept Head, dan IR Head.');
        }

        $canInput = $isIrStaff;

        // Available periods for dropdown selector
        $availablePeriods = \App\SpAuditMangkir::select('periode', 'periode_label')
            ->distinct()->orderByDesc('periode')->get();

        $firstPeriod    = $availablePeriods->first();
        $selectedPeriod = $request->input('periode', $firstPeriod ? $firstPeriod->periode : null);
        $filterStatus   = $request->input('filter', 'ALL');
        $search         = trim($request->input('search', ''));
        $perPage        = 25;

        $kpiData = null;
        $auditRecordsPaginated = null;
        $periodLabel = null;
        $auditFilename = null;
        $auditUploadedAt = null;

        if ($selectedPeriod) {
            $periodMeta      = \App\SpAuditMangkir::where('periode', $selectedPeriod)->first();
            try {
                $periodLabel = \Carbon\Carbon::parse($selectedPeriod . '-01')->locale('id')->isoFormat('MMMM YYYY');
            } catch (\Exception $e) {
                $periodLabel = $periodMeta ? ($periodMeta->periode_label ?? $selectedPeriod) : $selectedPeriod;
            }
            $auditFilename   = $periodMeta ? $periodMeta->filename : null;
            $auditUploadedAt = ($periodMeta && $periodMeta->created_at) ? $periodMeta->created_at->format('d/m/Y H:i') : null;

            // Load all rows for period to cross-check with SP records
            $auditRows = \App\SpAuditMangkir::where('periode', $selectedPeriod)->get();
            $empIds    = $auditRows->pluck('employee_id')->filter()->unique()->values()->toArray();

            $existingSps = \App\SpPelanggaran::with(['dates'])
                ->where('sumber_data', 'MANGKIR')
                ->whereIn('employee_id', $empIds)
                ->where('bulan_mangkir', $selectedPeriod)
                ->whereNotIn('current_status', [\App\SpPelanggaran::STATUS_CANCELLED, \App\SpPelanggaran::STATUS_REJECTED])
                ->get();

            $spIndex = [];
            foreach ($existingSps as $sp) {
                foreach ($sp->dates as $d) {
                    $spIndex[($sp->employee_id ?? '') . '|' . $d->tanggal] = $sp;
                }
            }

            $enriched = $auditRows->map(function ($row) use ($spIndex) {
                $key             = $row->employee_id . '|' . $row->tanggal;
                $sp              = $row->employee_id ? ($spIndex[$key] ?? null) : null;
                $row->status_audit = $row->employee_id ? ($sp ? 'SUDAH_INPUT' : 'BELUM_INPUT') : 'NOT_FOUND_IN_DB';
                $row->sp_nomor   = $sp ? ($sp->nomor_sp_generated ?? $sp->kode_admin) : null;
                $row->sp_status  = $sp ? $sp->current_status : null;
                $row->sp_id      = $sp ? $sp->id : null;
                return $row;
            });

            $kpiData = [
                'total_excel_mangkir' => $auditRows->count(),
                'total_sudah_input'   => $enriched->where('status_audit', 'SUDAH_INPUT')->count(),
                'total_belum_input'   => $enriched->where('status_audit', 'BELUM_INPUT')->count(),
                'total_karyawan'      => $auditRows->pluck('nik')->unique()->count(),
            ];

            $filtered = $enriched;
            if ($search !== '') {
                $searchLower = mb_strtolower($search);
                $filtered = $filtered->filter(function ($row) use ($searchLower) {
                    return str_contains(mb_strtolower($row->nama ?? ''), $searchLower)
                        || str_contains(mb_strtolower($row->nik ?? ''), $searchLower)
                        || str_contains(mb_strtolower($row->department ?? ''), $searchLower)
                        || str_contains(mb_strtolower($row->section ?? ''), $searchLower);
                });
            }
            if ($filterStatus === 'BELUM_INPUT') {
                $filtered = $filtered->whereIn('status_audit', ['BELUM_INPUT', 'NOT_FOUND_IN_DB']);
            } elseif ($filterStatus === 'SUDAH_INPUT') {
                $filtered = $filtered->where('status_audit', 'SUDAH_INPUT');
            }

            $currentPage = (int) $request->input('page', 1);
            $totalItems  = $filtered->count();
            $sliced      = $filtered->values()->slice(($currentPage - 1) * $perPage, $perPage)->values();

            $auditRecordsPaginated = new \Illuminate\Pagination\LengthAwarePaginator(
                $sliced,
                $totalItems,
                $perPage,
                $currentPage,
                ['path' => $request->url(), 'query' => $request->query()]
            );
        }

        return view('sp_mangkir.audit', compact(
            'canInput', 'isIrStaff', 'isIrHead', 'isDeptHead',
            'availablePeriods', 'selectedPeriod', 'filterStatus', 'search',
            'kpiData', 'auditRecordsPaginated', 'periodLabel',
            'auditFilename', 'auditUploadedAt'
        ));
    }

    public function processAudit(Request $request)
    {
        $permissions = view()->shared('permissions') ?: [];
        $isIrStaff  = in_array('sp_pelanggaran_ir_staff', $permissions);
        $isIrHead   = in_array('sp_pelanggaran_ir_head', $permissions);
        $isDeptHead = in_array('sp_pelanggaran_dh', $permissions) || in_array('sp_pelanggaran_approval_dh', $permissions);

        if (!$isIrStaff && !$isIrHead && !$isDeptHead) {
            return response()->json([
                'status' => 'error',
                'message' => 'Akses Ditolak. Anda tidak memiliki izin untuk melakukan audit mangkir.',
            ], 403);
        }

        $request->validate([
            'file' => 'required|file|mimes:xls,xlsx,csv|max:10240',
            'target_codes' => 'nullable|array',
            'overwrite' => 'nullable|boolean',
        ]);

        try {
            $file = $request->file('file');
            $filePath = $file->getRealPath();
            $targetCodes = $request->input('target_codes');
            if (empty($targetCodes) || !is_array($targetCodes)) {
                $targetCodes = ['A', 'ALFA', 'MANGKIR', 'KD', 'L3', 'L1', 'L2'];
            }
            $isOverwrite = $request->boolean('overwrite', false);

            $auditService = new SpMangkirAuditService();
            $result = $auditService->auditExcelFile($filePath, $targetCodes);

            $periodKey = $result['period_key'];
            $periodFormatted = $result['period_formatted'];

            // Check if audit data for this period already exists in sp_audit_mangkirs
            $existsInDb = \App\SpAuditMangkir::where('periode', $periodKey)->exists();

            if ($existsInDb && !$isOverwrite) {
                return response()->json([
                    'status' => 'confirm_overwrite',
                    'message' => "Data audit mangkir untuk periode {$periodFormatted} sudah ada di database. Apakah Anda yakin ingin memperbarui/menimpa data audit dengan file Excel yang baru di-upload ini?",
                    'period_key' => $periodKey,
                    'period_formatted' => $periodFormatted,
                    'data' => $result,
                ]);
            }

            // Save parsed audit entries into sp_audit_mangkirs database table
            $auditService->saveAuditRecordsToDb($result, $file->getClientOriginalName(), Auth::id());

            // Re-fetch updated audit status from database
            $finalAuditResult = $auditService->auditFromDatabase($periodKey);

            return response()->json([
                'status' => 'success',
                'message' => "Data audit mangkir periode {$periodFormatted} berhasil disimpan ke database!",
                'data' => !empty($finalAuditResult) ? $finalAuditResult : $result,
            ]);
        } catch (\Exception $e) {
            logger()->error('Gagal melakukan audit mangkir Excel: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal membaca atau memproses file Excel absensi: ' . $e->getMessage(),
            ], 422);
        }
    }

    public function batchStoreFromAudit(Request $request)
    {
        $permissions = view()->shared('permissions') ?: [];
        $isIrStaff = in_array('sp_pelanggaran_ir_staff', $permissions);

        // Hanya IR Staff yang berhak menyimpan/menginput SP Mangkir dari audit
        if (!$isIrStaff) {
            return response()->json([
                'status' => 'error',
                'message' => 'Akses Ditolak. Hanya IR Staff yang berhak melakukan penginputan SP Mangkir.',
            ], 403);
        }
        $request->validate([
            'items' => 'required|array|min:1',
            'items.*.employee_id' => 'required|exists:hr_karyawan,id',
            'items.*.tanggal_mangkir' => 'required|date',
            'submit_direct' => 'nullable|boolean',
        ]);

        $items = $request->items;
        $isSubmitDirect = $request->boolean('submit_direct', false);
        $createdCount = 0;
        $skippedCount = 0;
        $errors = [];

        DB::beginTransaction();
        try {
            foreach ($items as $item) {
                $empId = $item['employee_id'];
                $tanggal = $item['tanggal_mangkir'];
                $bulan = Carbon::parse($tanggal)->format('Y-m');

                // Check duplicate
                $duplicate = SpPelanggaran::where('employee_id', $empId)
                    ->where('sumber_data', 'MANGKIR')
                    ->whereHas('dates', function ($dq) use ($tanggal) {
                        $dq->whereDate('tanggal', $tanggal);
                    })
                    ->whereNotIn('current_status', [SpPelanggaran::STATUS_CANCELLED, SpPelanggaran::STATUS_REJECTED])
                    ->first();

                if ($duplicate) {
                    $skippedCount++;
                    continue;
                }

                $mangkirKe = SpPelanggaran::where('employee_id', $empId)
                    ->where('sumber_data', 'MANGKIR')
                    ->where('bulan_mangkir', $bulan)
                    ->whereNotIn('current_status', [SpPelanggaran::STATUS_CANCELLED, SpPelanggaran::STATUS_REJECTED])
                    ->count() + 1;

                $kodeAdmin = "Mangkir " . $mangkirKe;

                $employee = HrKaryawan::findOrFail($empId);
                $kodeDept = $employee->kode_divisi ?? $employee->kode_bagian ?? null;
                $initialStatus = $isSubmitDirect ? SpPelanggaran::STATUS_PENDING_DH : SpPelanggaran::STATUS_DRAFT;

                // Find Dept Head
                $deptHeadUser = null;
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

                if (!$deptHeadUser) {
                    $deptHeadUser = User::where(function ($q) {
                        $q->whereHas('directPermissions', function ($p) {
                            $p->whereIn('codename', ['sp_pelanggaran_dh', 'sp_pelanggaran_approval_dh']);
                        })->orWhereHas('group.permissions', function ($p) {
                            $p->whereIn('codename', ['sp_pelanggaran_dh', 'sp_pelanggaran_approval_dh']);
                        });
                    })->whereNotNull('email')->where('email', '!=', '')->first();
                }

                $spMangkir = SpPelanggaran::create([
                    'sumber_data' => 'MANGKIR',
                    'employee_id' => $empId,
                    'kode_admin' => $kodeAdmin,
                    'mangkir_ke' => $mangkirKe,
                    'bulan_mangkir' => $bulan,
                    'alasan' => "Mangkir/Alpha ke-{$mangkirKe} dalam bulan " . Carbon::parse($tanggal)->isoFormat('MMMM YYYY') . " (Hasil Audit Excel)",
                    'status' => 'DRAFT',
                    'current_status' => $initialStatus,
                    'created_by_user_id' => Auth::id(),
                    'assigned_dept_head_id' => $deptHeadUser ? $deptHeadUser->id : null,
                    'email_dept_head' => $deptHeadUser ? $deptHeadUser->email : null,
                    'email_dept_user' => $employee->email,
                ]);

                SpPelanggaranDate::create([
                    'sp_pelanggaran_id' => $spMangkir->id,
                    'tanggal' => $tanggal,
                ]);

                SpApprovalLog::create([
                    'sp_pelanggaran_id' => $spMangkir->id,
                    'user_id' => Auth::id(),
                    'role' => 'admin',
                    'action' => $isSubmitDirect ? 'SUBMIT_DEPT_HEAD' : 'CREATE_DRAFT',
                    'status_from' => null,
                    'status_to' => $initialStatus,
                    'notes' => 'Input SP Mangkir ke-' . $mangkirKe . ' dari Audit Excel',
                ]);

                if ($isSubmitDirect && $deptHeadUser && $deptHeadUser->email) {
                    try {
                        Mail::to($deptHeadUser->email)->send(new SpNotification($spMangkir, 'SUBMIT_DEPT_HEAD'));
                    } catch (\Exception $e) {
                        logger()->error('Gagal mengirim email audit SP Mangkir: ' . $e->getMessage());
                    }
                }

                $createdCount++;
            }

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => "Berhasil memproses {$createdCount} data SP Mangkir dari audit!" . ($skippedCount > 0 ? " ({$skippedCount} data terlewat karena sudah ada)" : ''),
                'created_count' => $createdCount,
                'skipped_count' => $skippedCount,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            logger()->error('Gagal simpan batch dari audit mangkir: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal menyimpan data SP Mangkir dari audit: ' . $e->getMessage(),
            ], 500);
        }
    }
}
