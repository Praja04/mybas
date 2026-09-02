<?php

namespace App\Http\Controllers;

use App\HrKaryawan;
use App\SpPelanggaran;
use App\SpPelanggaranDate;
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
        $deptCodes = $this->getDeptCodes($userDept);

        // Fetch list of active employees for selection dropdown from hr_karyawan
        $employeeQuery = HrKaryawan::query();
        if (!$isIrRole && !empty($deptCodes)) {
            $employeeQuery->where(function ($q) use ($deptCodes) {
                $q->whereIn('kode_divisi', $deptCodes)
                    ->orWhereIn('kode_bagian', $deptCodes);
            });
        }

        $employees = $employeeQuery->orderBy('nama', 'asc')->get();

        // Check for edit mode
        $editSp = null;
        if ($request->filled('edit')) {
            $editSp = SpPelanggaran::with('dates')->find($request->edit);
            if ($editSp && !in_array($editSp->current_status, [SpPelanggaran::STATUS_DRAFT, SpPelanggaran::STATUS_REJECTED])) {
                $editSp = null;
            }
        }

        $masterKodes = SpKodePelanggaran::where('kategori_kode', 'ADMIN')->orWhereNull('kategori_kode')->orderBy('kode', 'asc')->get();

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
            $userDept = (Auth::user() ? Auth::user()->dept_id : null) ?: session('kode_department');
            $deptCodes = $this->getDeptCodes($userDept);
            if (!empty($deptCodes)) {
                $query->whereHas('employee', function ($q) use ($deptCodes) {
                    $q->whereIn('kode_divisi', $deptCodes)
                        ->orWhereIn('kode_bagian', $deptCodes);
                });
            }
        }

        $currentYear = Carbon::now()->year;
        $sixMonthsAgo = Carbon::now()->subMonths(6);

        // 1. SP AKTIF (APPROVED & Masa berlaku <= 6 Bulan)
        $totalSpActive = (clone $query)->where('current_status', SpPelanggaran::STATUS_APPROVED)
            ->where(function ($q) use ($sixMonthsAgo) {
                $q->whereHas('dates', function ($dq) use ($sixMonthsAgo) {
                    $dq->where('tanggal', '>=', $sixMonthsAgo);
                })->orWhere('created_at', '>=', $sixMonthsAgo);
            })
            ->count();

        // 2. SP TIDAK AKTIF / EXPIRED (APPROVED & Masa berlaku > 6 Bulan)
        $totalSpExpired = (clone $query)->where('current_status', SpPelanggaran::STATUS_APPROVED)
            ->where(function ($q) use ($sixMonthsAgo) {
                $q->whereDoesntHave('dates', function ($dq) use ($sixMonthsAgo) {
                    $dq->where('tanggal', '>=', $sixMonthsAgo);
                })->where('created_at', '<', $sixMonthsAgo);
            })
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
            ->where(function ($q) use ($currentYear) {
                $q->whereHas('dates', function ($dq) use ($currentYear) {
                    $dq->whereYear('tanggal', $currentYear);
                })->orWhereYear('created_at', $currentYear);
            })
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
        $trends = (clone $query)
            ->leftJoin('sp_pelanggaran_dates', 'sp_pelanggarans.id', '=', 'sp_pelanggaran_dates.sp_pelanggaran_id')
            ->select(DB::raw('MONTH(COALESCE(sp_pelanggaran_dates.tanggal, sp_pelanggarans.created_at)) as bulan'), DB::raw('count(DISTINCT sp_pelanggarans.id) as total'))
            ->where('sp_pelanggarans.current_status', SpPelanggaran::STATUS_APPROVED)
            ->whereYear(DB::raw('COALESCE(sp_pelanggaran_dates.tanggal, sp_pelanggarans.created_at)'), $currentYear)
            ->groupBy(DB::raw('MONTH(COALESCE(sp_pelanggaran_dates.tanggal, sp_pelanggarans.created_at))'))
            ->get();

        $chartTrendData = array_fill(0, 12, 0);
        foreach ($trends as $trend) {
            $chartTrendData[$trend->bulan - 1] = (int)$trend->total;
        }

        // 5. Top Departemen Penyumbang SP
        $topDepartments = [];
        $deptData = (clone $query)
            ->leftJoin('sp_pelanggaran_dates', 'sp_pelanggarans.id', '=', 'sp_pelanggaran_dates.sp_pelanggaran_id')
            ->select(DB::raw('COALESCE(hr_karyawan.kode_divisi, hr_karyawan.kode_bagian) as kode_department'), DB::raw('count(DISTINCT sp_pelanggarans.id) as total'))
            ->join('hr_karyawan', 'hr_karyawan.id', '=', 'sp_pelanggarans.employee_id')
            ->where('sp_pelanggarans.current_status', SpPelanggaran::STATUS_APPROVED)
            ->whereYear(DB::raw('COALESCE(sp_pelanggaran_dates.tanggal, sp_pelanggarans.created_at)'), $currentYear)
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
            'totalSpActive',
            'totalSpExpired',
            'totalSpBerat',
            'totalSpRejected',
            'totalSpCancelled',
            'totalSpProcess',
            'chartDistribusi',
            'chartTrendData',
            'topDepartments',
            'currentYear',
            'userRole'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:hr_karyawan,id',
            'kode_admin' => 'nullable|string|max:255',
            'no_sp' => 'nullable|string|max:100',
            'tanggal_pelanggaran' => 'nullable|date',
            'jenis_pelanggaran' => 'nullable|string',
            'status' => 'required|in:DRAFT,SELESAI',
            'alasan' => 'nullable|string',
            'lampiran' => 'nullable|file|mimes:pdf,jpg,jpeg,png,doc,docx|max:5120',
            'sesuai_ketentuan' => 'required|boolean',
            'sumber_data' => 'nullable|string',
            'pasal_dilanggar' => 'nullable|string',
            'uraian_pelanggaran' => 'nullable|string',
        ]);

        $data = $validated;
        $primaryDate = $request->input('tanggal_pelanggaran', date('Y-m-d'));
        unset($data['tanggal_pelanggaran']);

        $data['created_by_user_id'] = Auth::id() ?: session('user_id');
        $data['current_status'] = SpPelanggaran::STATUS_DRAFT;
        $data['sumber_data'] = $request->input('sumber_data') ?: 'PELANGGARAN';

        $rawDates = [$primaryDate];
        if ($request->has('tanggal_terlambat_list') && is_array($request->tanggal_terlambat_list)) {
            $rawDates = array_merge($rawDates, array_values(array_filter($request->tanggal_terlambat_list)));
        } elseif ($request->has('additional_dates') && is_array($request->additional_dates)) {
            $rawDates = array_merge($rawDates, array_values(array_filter($request->additional_dates)));
        }
        $rawDates = array_values(array_filter($rawDates));

        if (count($rawDates) !== count(array_unique($rawDates))) {
            return response()->json([
                'status' => 'error',
                'message' => 'Terdapat tanggal yang sama (duplicate) dalam pengajuan ini! Mohon pastikan setiap tanggal bersifat unik.'
            ], 422);
        }
        $allInputDates = $rawDates;

        if (count($allInputDates) > 1) {
            $formattedDates = array_map(function ($d) {
                return Carbon::parse($d)->format('d/m/Y');
            }, $allInputDates);
            $datesSummary = "[Terlambat " . count($allInputDates) . " Hari: " . implode(', ', $formattedDates) . "]";
            if (empty($data['alasan'])) {
                $data['alasan'] = $datesSummary;
            } elseif (strpos($data['alasan'], '[Terlambat') === false) {
                $data['alasan'] = trim($data['alasan']) . ' ' . $datesSummary;
            }
        }

        $data['nomor_sp_generated'] = null;

        // Auto Lookup Dept Head & Emails
        $employee = HrKaryawan::find($request->employee_id);
        $deptHeadUser = null;
        if ($employee) {
            $kodeDept = $employee->kode_divisi ?: $employee->kode_bagian;
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
        }

        $data['assigned_dept_head_id'] = $deptHeadUser ? $deptHeadUser->id : null;
        $data['email_dept_head'] = $deptHeadUser ? $deptHeadUser->email : null;
        $data['email_dept_user'] = ($employee && !empty($employee->email)) ? $employee->email : null;

        if ($request->hasFile('lampiran')) {
            $file = $request->file('lampiran');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('uploads/sp'), $filename);
            $data['lampiran'] = 'uploads/sp/' . $filename;
        }

        $sp = SpPelanggaran::create($data);

        // Sync ke tabel sp_pelanggaran_dates (Tabel Relasional Multi-Date Dinamis)
        $sp->dates()->delete();
        foreach ($allInputDates as $d) {
            SpPelanggaranDate::create([
                'sp_pelanggaran_id' => $sp->id,
                'tanggal' => $d,
            ]);
        }

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
            'kode_admin' => 'nullable|string|max:255',
            'no_sp' => 'nullable|string|max:100',
            'tanggal_pelanggaran' => 'nullable|date',
            'jenis_pelanggaran' => 'nullable|string',
            'status' => 'required|in:DRAFT,SELESAI',
            'alasan' => 'nullable|string',
            'lampiran' => 'nullable|file|mimes:pdf,jpg,jpeg,png,doc,docx|max:5120',
            'sesuai_ketentuan' => 'required|boolean',
            'sumber_data' => 'nullable|string',
            'pasal_dilanggar' => 'nullable|string',
            'uraian_pelanggaran' => 'nullable|string',
        ]);

        $data = $validated;
        $primaryDate = $request->input('tanggal_pelanggaran', $sp->tanggal_pelanggaran);
        unset($data['tanggal_pelanggaran']);

        $rawDates = [$primaryDate];
        if ($request->has('tanggal_terlambat_list') && is_array($request->tanggal_terlambat_list)) {
            $rawDates = array_merge($rawDates, array_values(array_filter($request->tanggal_terlambat_list)));
        } elseif ($request->has('additional_dates') && is_array($request->additional_dates)) {
            $rawDates = array_merge($rawDates, array_values(array_filter($request->additional_dates)));
        }
        $rawDates = array_values(array_filter($rawDates));

        if (count($rawDates) !== count(array_unique($rawDates))) {
            return response()->json([
                'status' => 'error',
                'message' => 'Terdapat tanggal yang sama (duplicate) dalam pengajuan ini! Mohon pastikan setiap tanggal bersifat unik.'
            ], 422);
        }
        $allInputDates = $rawDates;

        if (count($allInputDates) > 1) {
            $formattedDates = array_map(function ($d) {
                return Carbon::parse($d)->format('d/m/Y');
            }, $allInputDates);
            $datesSummary = "[Terlambat " . count($allInputDates) . " Hari: " . implode(', ', $formattedDates) . "]";
            if (empty($data['alasan'])) {
                $data['alasan'] = $datesSummary;
            } elseif (strpos($data['alasan'], '[Terlambat') === false) {
                $data['alasan'] = trim($data['alasan']) . ' ' . $datesSummary;
            }
        }

        if ($request->hasFile('lampiran')) {
            if ($sp->lampiran && file_exists(public_path($sp->lampiran))) {
                @unlink(public_path($sp->lampiran));
            }

            $file = $request->file('lampiran');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('uploads/sp'), $filename);
            $data['lampiran'] = 'uploads/sp/' . $filename;
        }

        // Reset status ke DRAFT jika sebelumnya DITOLAK/DRAFT agar bisa diajukan ulang
        $data['current_status'] = SpPelanggaran::STATUS_DRAFT;

        $sp->update($data);

        // Sync ke tabel sp_pelanggaran_dates (Tabel Relasional Multi-Date Dinamis)
        $sp->dates()->delete();
        foreach ($allInputDates as $d) {
            SpPelanggaranDate::create([
                'sp_pelanggaran_id' => $sp->id,
                'tanggal' => $d,
            ]);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Data pelanggaran berhasil diperbarui.',
            'data' => $sp
        ]);
    }

    public function destroy($id)
    {
        $sp = SpPelanggaran::findOrFail($id);

        $permissions = view()->shared('permissions') ?: [];
        $isIrRole = in_array('sp_pelanggaran_ir_staff', $permissions) || in_array('sp_pelanggaran_ir_head', $permissions);

        // Admin hanya bisa hapus sebelum disetujui Dept Head (DRAFT / PENDING_DH).
        // SP yang DITOLAK (REJECTED), diproses IR, atau Approved tidak dapat dihapus oleh Admin (hanya bisa Edit & Perbaiki).
        if (!$isIrRole && !in_array($sp->current_status, [SpPelanggaran::STATUS_DRAFT, SpPelanggaran::STATUS_PENDING_DH])) {
            return response()->json([
                'status' => 'error',
                'message' => 'Data SP yang ditolak atau dalam proses persetujuan tidak dapat dihapus oleh Admin (hanya dapat Diperbaiki atau dikelola IR).'
            ], 403);
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

        // Check SP aktif dari SEMUA sumber (Pelanggaran maupun Mangkir)
        $activeSp = SpPelanggaran::with('dates')
            ->where('employee_id', $employee_id)
            ->where('current_status', SpPelanggaran::STATUS_APPROVED)
            ->whereNotNull('jenis_pelanggaran')
            ->where(function ($q) use ($sixMonthsAgo) {
                $q->whereHas('dates', function ($dq) use ($sixMonthsAgo) {
                    $dq->where('tanggal', '>=', $sixMonthsAgo);
                })->orWhere('created_at', '>=', $sixMonthsAgo);
            })
            ->orderBy('id', 'desc')
            ->first();

        // Riwayat SP 6 bulan terakhir (Pelanggaran + Mangkir) sebagai konteks IR Staff
        $spHistory = SpPelanggaran::with(['employee', 'dates'])
            ->where('employee_id', $employee_id)
            ->whereIn('current_status', [SpPelanggaran::STATUS_APPROVED, SpPelanggaran::STATUS_PENDING_IR, SpPelanggaran::STATUS_PENDING_IR_HEAD])
            ->where(function ($q) use ($sixMonthsAgo) {
                $q->whereHas('dates', function ($dq) use ($sixMonthsAgo) {
                    $dq->where('tanggal', '>=', $sixMonthsAgo);
                })->orWhere('created_at', '>=', $sixMonthsAgo);
            })
            ->orderBy('id', 'desc')
            ->get()
            ->map(function ($s) {
                $allDates = [];
                if ($s->dates && $s->dates->count() > 0) {
                    $allDates = $s->dates->pluck('tanggal')->toArray();
                } else if ($s->tanggal_pelanggaran) {
                    $allDates = [$s->tanggal_pelanggaran];
                }
                return [
                    'id'                  => $s->id,
                    'nomor'               => $s->nomor_sp_generated ?: ($s->no_sp ?: 'PROSES'),
                    'tanggal'             => $s->tanggal_pelanggaran,
                    'all_dates'           => array_values(array_unique(array_filter($allDates))),
                    'dates_count'         => count($allDates),
                    'sumber_data'         => $s->sumber_data ?: 'PELANGGARAN',
                    'kode_admin'          => $s->kode_admin,
                    'jenis_pelanggaran'   => $s->jenis_pelanggaran,
                    'current_status'      => $s->current_status,
                ];
            });

        // Kode IR khusus Pelanggaran (ADMIN/IR)
        $irKodesPelanggaran = SpKodePelanggaran::whereIn('kategori_kode', ['IR', 'ADMIN'])
            ->orWhereNull('kategori_kode')
            ->orderBy('kode', 'asc')
            ->get();

        // Kode IR khusus Mangkir
        $irKodesMangkir = SpKodePelanggaran::where('kategori_kode', 'MANGKIR')
            ->orderBy('kode', 'asc')
            ->get();

        // Gabungkan semua kode untuk fallback
        $irKodes = SpKodePelanggaran::orderBy('kategori_kode', 'asc')->orderBy('kode', 'asc')->get();

        if ($activeSp) {
            $currentJenis = trim($activeSp->jenis_pelanggaran ?: '');
            $nextLevel = 'SP II';
            $prefix = 'SP 1 +';

            if (in_array($currentJenis, ['SP 2', 'SP II', 'Surat Peringatan 2 (SP 2)'])) {
                $nextLevel = 'SP III';
                $prefix = 'SP 2 +';
            } elseif (in_array($currentJenis, ['SP 3', 'SP III', 'SP III+', 'SP 3+', 'Surat Peringatan 3 (SP 3)'])) {
                $nextLevel = 'SP III+';
                $prefix = 'SP 3 +';
            }

            return response()->json([
                'is_active' => true,
                'message'   => "Karyawan sedang dalam masa SP Aktif ({$currentJenis}). Rekomendasi Eskalasi: {$nextLevel}.",
                'data'      => [
                    'no_sp'             => $activeSp->nomor_sp_generated ?: $activeSp->no_sp,
                    'jenis_pelanggaran' => $activeSp->jenis_pelanggaran,
                    'sumber_data'       => $activeSp->sumber_data ?: 'PELANGGARAN',
                    'tanggal_pelanggaran' => $activeSp->tanggal_pelanggaran,
                    'alasan'            => $activeSp->alasan,
                    'next_level'        => $nextLevel,
                    'prefix'            => $prefix,
                ],
                'sp_history'            => $spHistory,
                'ir_kodes'              => $irKodes,
                'ir_kodes_pelanggaran'  => $irKodesPelanggaran,
                'ir_kodes_mangkir'      => $irKodesMangkir,
            ]);
        }

        return response()->json([
            'is_active'             => false,
            'message'               => 'Karyawan tidak memiliki SP aktif saat ini.',
            'next_level'            => 'SP I',
            'prefix'                => '',
            'sp_history'            => $spHistory,
            'ir_kodes'              => $irKodes,
            'ir_kodes_pelanggaran'  => $irKodesPelanggaran,
            'ir_kodes_mangkir'      => $irKodesMangkir,
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
        $deptHead = User::where(function ($q) use ($kodeDept) {
            if ($kodeDept) {
                $q->where('dept_id', $kodeDept);
            }
        })->where(function ($q) {
            $q->whereHas('directPermissions', function ($permQ) {
                $permQ->whereIn('codename', ['sp_pelanggaran_dh', 'sp_pelanggaran_approval_dh']);
            })->orWhereHas('group.permissions', function ($permQ) {
                $permQ->whereIn('codename', ['sp_pelanggaran_dh', 'sp_pelanggaran_approval_dh']);
            });
        })->whereNotNull('email')->where('email', '!=', '')->first();

        // 2. Search any user with Dept Head permission in system with valid email
        if (!$deptHead) {
            $deptHead = User::where(function ($q) {
                $q->whereHas('directPermissions', function ($permQ) {
                    $permQ->whereIn('codename', ['sp_pelanggaran_dh', 'sp_pelanggaran_approval_dh']);
                })->orWhereHas('group.permissions', function ($permQ) {
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
            'assigned_dept_head_id' => $sp->assigned_dept_head_id ?: (Auth::id() ?: session('user_id')),
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
                'assigned_dept_head_id' => $sp->assigned_dept_head_id ?: (Auth::id() ?: session('user_id')),
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

        $updateData = [
            'current_status' => SpPelanggaran::STATUS_PENDING_IR_HEAD,
            'ir_staff_notes' => $request->input('notes'),
            'ir_staff_id'    => Auth::id() ?: session('user_id'),
            'email_dept_hr'  => Auth::user() ? Auth::user()->email : session('user_email'),
        ];

        if ($request->filled('kode_ir')) {
            $updateData['kode_ir'] = $request->input('kode_ir');
        }
        if ($request->filled('jenis_pelanggaran')) {
            $updateData['jenis_pelanggaran'] = $request->input('jenis_pelanggaran');
        }
        if ($request->filled('pasal_dilanggar')) {
            $updateData['pasal_dilanggar'] = $request->input('pasal_dilanggar');
        }
        if ($request->filled('alasan')) {
            $updateData['alasan'] = $request->input('alasan');
        }

        $sp->update($updateData);

        SpApprovalLog::logAction(
            $sp->id,
            Auth::id() ?: session('user_id'),
            SpApprovalLog::ACTION_IR_STAFF_SUBMIT,
            $request->input('notes', 'Submitted to IR Head with Kode IR: ' . ($request->input('kode_ir') ?: '-'))
        );

        return response()->json([
            'status' => 'success',
            'message' => 'SP dan Kode IR berhasil diajukan ke IR Head untuk persetujuan final.'
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

    public function irHeadMassApprove(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'integer|exists:sp_pelanggarans,id',
            'notes' => 'nullable|string',
        ]);

        $ids = $request->input('ids', []);
        $notes = $request->input('notes', 'Mass Approved Final by IR Head');

        $spRecords = SpPelanggaran::whereIn('id', $ids)
            ->where('current_status', SpPelanggaran::STATUS_PENDING_IR_HEAD)
            ->get();

        if ($spRecords->isEmpty()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Tidak ada SP berstatus Pending IR Head yang dapat diapprove.'
            ], 422);
        }

        $approvedCount = 0;
        foreach ($spRecords as $sp) {
            $nomorSp = SpPelanggaran::generateNomorSp($sp->employee_id);

            $sp->update([
                'current_status' => SpPelanggaran::STATUS_APPROVED,
                'ir_head_approved_at' => now(),
                'ir_head_notes' => $notes,
                'nomor_sp_generated' => $nomorSp,
            ]);

            SpApprovalLog::logAction(
                $sp->id,
                Auth::id() ?: session('user_id'),
                SpApprovalLog::ACTION_IR_HEAD_APPROVE,
                $notes
            );

            $this->sendFinalEmail($sp);

            $approvedCount++;
        }

        return response()->json([
            'status' => 'success',
            'message' => "Berhasil menyetujui final {$approvedCount} SP sekaligus dan menerbitkan Nomor SP."
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

        // Poin 2: IR Head reject dikembalikan ke IR Staff (PENDING_IR), bukan ke Admin
        $sp->update([
            'current_status' => SpPelanggaran::STATUS_PENDING_IR,
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
            'message' => 'SP ditolak oleh IR Head dan dikembalikan ke IR Staff untuk peninjauan ulang.'
        ]);
    }

    public function irStaffReject(Request $request, $id)
    {
        $sp = SpPelanggaran::findOrFail($id);

        if (!$sp->canIrStaffReview()) {
            return response()->json([
                'status' => 'error',
                'message' => 'SP tidak dapat ditolak pada tahap ini.'
            ], 422);
        }

        $request->validate([
            'notes' => 'required|string'
        ]);

        // Poin 5b: Jika SP di-reject IR Staff, masuk ke DB dengan status REJECTED (tidak dikembalikan ke Admin)
        $sp->update([
            'current_status' => SpPelanggaran::STATUS_REJECTED,
            'ir_staff_notes' => $request->input('notes'),
            'ir_staff_id'    => Auth::id() ?: session('user_id'),
        ]);

        SpApprovalLog::logAction(
            $sp->id,
            Auth::id() ?: session('user_id'),
            SpApprovalLog::ACTION_IR_STAFF_REJECT,
            $request->input('notes')
        );

        return response()->json([
            'status' => 'success',
            'message' => 'SP ditolak oleh IR Staff dan diarsipkan dalam sistem dengan status DITOLAK.'
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
        $deptCodes = $this->getDeptCodes($userDept);

        $query = SpPelanggaran::with(['employee', 'creator', 'dates'])
            ->orderBy('updated_at', 'desc');

        if ($userRole === 'dept_head' && !empty($deptCodes)) {
            $query->whereHas('employee', function ($empQ) use ($deptCodes) {
                $empQ->whereIn('kode_divisi', $deptCodes)
                    ->orWhereIn('kode_bagian', $deptCodes);
            });
        }

        $normalStatusMap = [
            'dept_head' => SpPelanggaran::STATUS_PENDING_DH,
            'ir_staff'  => SpPelanggaran::STATUS_PENDING_IR,
            'ir_head'   => SpPelanggaran::STATUS_PENDING_IR_HEAD,
        ];

        $cancelStatusMap = [
            'dept_head' => SpPelanggaran::STATUS_CANCEL_PENDING_DH,
            'ir_staff'  => SpPelanggaran::STATUS_CANCEL_PENDING_IR,
            'ir_head'   => SpPelanggaran::STATUS_CANCEL_PENDING_IR_HEAD,
        ];

        $normalStatus = $normalStatusMap[$userRole] ?? SpPelanggaran::STATUS_PENDING_IR;
        $cancelStatus = $cancelStatusMap[$userRole] ?? SpPelanggaran::STATUS_CANCEL_PENDING_IR;

        // Hitung total pengajuan masing-masing kategori untuk badge tab
        $countApproval = (clone $query)->where('current_status', $normalStatus)->count();
        $countCancel   = (clone $query)->where('current_status', $cancelStatus)->count();

        // Filter berdasarkan tab active: 'approval', 'cancel', atau 'all'
        $activeTab = $request->get('tab');
        if (!$activeTab) {
            $activeTab = ($countApproval == 0 && $countCancel > 0) ? 'cancel' : 'approval';
        }

        if ($activeTab === 'approval') {
            $query->where('current_status', $normalStatus);
        } elseif ($activeTab === 'cancel') {
            $query->where('current_status', $cancelStatus);
        } else {
            $query->whereIn('current_status', [$normalStatus, $cancelStatus]);
        }

        $spRecords = $query->paginate(10)->appends($request->query());

        $viewMap = [
            'dept_head' => 'sp_pelanggaran.approval_dept_head',
            'ir_staff'  => 'sp_pelanggaran.approval_ir_staff',
            'ir_head'   => 'sp_pelanggaran.approval_ir_head',
        ];

        $viewName = $viewMap[$userRole] ?? 'sp_pelanggaran.approval_ir_staff';

        return view($viewName, compact('spRecords', 'countApproval', 'countCancel', 'activeTab'));
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
        $deptCodes = $this->getDeptCodes($userDept);

        $query = SpPelanggaran::with(['employee', 'creator', 'dates'])
            ->where(function ($q) {
                $q->where('sumber_data', 'PELANGGARAN')
                    ->orWhereNull('sumber_data');
            })
            ->orderBy('updated_at', 'desc');

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
            } elseif ($st === 'PROSES_CANCEL') {
                $query->whereIn('current_status', [SpPelanggaran::STATUS_CANCEL_PENDING_DH, SpPelanggaran::STATUS_CANCEL_PENDING_IR, SpPelanggaran::STATUS_CANCEL_PENDING_IR_HEAD]);
            } else {
                $query->where('current_status', $st);
            }
        }

        $sps = $query->paginate(10);
        // dd($sps);
        $spRecords = $sps;

        return view('sp_pelanggaran.trace', compact('sps', 'spRecords'));
    }

    public function getSpDetail($id)
    {
        $sp = SpPelanggaran::with(['employee', 'creator', 'approvalLogs', 'deptHead', 'irStaff', 'dates', 'uploaderKonseling'])
            ->findOrFail($id);

        return response()->json([
            'status' => 'success',
            'data' => $sp
        ]);
    }

    /**
     * Export PDF Surat Peringatan Resmi (format sama persis dengan lampiran email final)
     * Bisa diakses dari halaman Trace SP Pelanggaran & Trace SP Mangkir
     */
    public function exportSpPdf($id)
    {
        $sp = SpPelanggaran::with(['employee', 'deptHead', 'irStaff', 'dates', 'approvalLogs'])
            ->findOrFail($id);

        // Hanya SP yang sudah APPROVED yang bisa di-download PDF-nya
        if ($sp->current_status !== SpPelanggaran::STATUS_APPROVED) {
            return redirect()->back()->with('error', 'PDF hanya tersedia untuk SP yang sudah disetujui (APPROVED).');
        }

        $htmlContent = view('emails.sp_template_official', ['sp' => $sp])->render();

        $pdf = \Barryvdh\DomPDF\Facade::loadHTML($htmlContent);
        $pdf->setPaper('a4', 'portrait');

        $spNum = $sp->nomor_sp_generated ?: 'DRAFT';
        $pdfFileName = 'Surat_Peringatan_' . str_replace(['/', '\\', ' '], '_', $spNum) . '.pdf';

        return $pdf->download($pdfFileName);
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

        // Pastikan relasi penting di-load
        $sp->loadMissing(['employee', 'deptHead', 'irStaff']);

        $recipients = [];

        // 1. Email Karyawan yang kena SP (murni dari hr_karyawan)
        if ($sp->employee && !empty($sp->employee->email)) {
            $recipients[] = trim($sp->employee->email);
        } elseif (!empty($sp->email_dept_user)) {
            $recipients[] = trim($sp->email_dept_user);
        }

        // 2. Email Dept Head berwenang yang menyetujui / menangani SP ini
        if ($sp->deptHead && !empty($sp->deptHead->email)) {
            $recipients[] = trim($sp->deptHead->email);
        } elseif (!empty($sp->email_dept_head)) {
            $recipients[] = trim($sp->email_dept_head);
        } elseif ($sp->employee) {
            $kodeDept = $sp->employee->kode_divisi ?: $sp->employee->kode_bagian;
            if ($kodeDept) {
                // Cari HANYA Dept Head spesifik di departemen yang sama
                $dhUser = User::where('dept_id', $kodeDept)
                    ->where(function ($q) {
                        $q->whereHas('directPermissions', function ($p) {
                            $p->whereIn('codename', ['sp_pelanggaran_dh', 'sp_pelanggaran_approval_dh']);
                        })->orWhereHas('group.permissions', function ($p) {
                            $p->whereIn('codename', ['sp_pelanggaran_dh', 'sp_pelanggaran_approval_dh']);
                        });
                    })
                    ->whereNotNull('email')->where('email', '!=', '')
                    ->first();

                if ($dhUser && !empty($dhUser->email)) {
                    $recipients[] = trim($dhUser->email);
                }
            }
        }

        // 3. Email IR Staff yang memproses / meninjau SP ini
        if ($sp->irStaff && !empty($sp->irStaff->email)) {
            $recipients[] = trim($sp->irStaff->email);
        } else {
            $irStaffUsers = User::where(function ($q) {
                $q->whereHas('directPermissions', function ($permQ) {
                    $permQ->where('codename', 'sp_pelanggaran_ir_staff');
                })->orWhereHas('group.permissions', function ($permQ) {
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

        logger()->info("Pengiriman Email Final SP #{$sp->id} ditujukan ke (" . count($recipients) . " penerima khusus): " . implode(', ', $recipients));

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
            'notes' => 'required|string',
            'lampiran_cancel' => 'nullable|file|mimes:pdf,jpg,jpeg,png,doc,docx|max:5120',
        ]);

        $lampiranCancelPath = null;
        if ($request->hasFile('lampiran_cancel')) {
            $file = $request->file('lampiran_cancel');
            $filename = 'cancel_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/sp_cancel'), $filename);
            $lampiranCancelPath = 'uploads/sp_cancel/' . $filename;
        }

        $permissions = view()->shared('permissions') ?: [];
        $isIrRole = in_array('sp_pelanggaran_ir_staff', $permissions) || in_array('sp_pelanggaran_ir_head', $permissions);

        // Poin 5a: Jika SP sudah diterbitkan (APPROVED), jalankan workflow pengajuan pembatalan (Admin -> Dept Head -> IR Staff -> IR Head)
        if ($sp->current_status === SpPelanggaran::STATUS_APPROVED) {
            $updateData = [
                'current_status' => SpPelanggaran::STATUS_CANCEL_PENDING_DH,
                'dept_head_notes' => $request->input('notes'),
            ];
            if ($lampiranCancelPath) {
                $updateData['lampiran_cancel'] = $lampiranCancelPath;
            }
            $sp->update($updateData);

            SpApprovalLog::logAction(
                $sp->id,
                Auth::id() ?: session('user_id'),
                SpApprovalLog::ACTION_REQUEST_CANCEL,
                'Pengajuan Pembatalan SP Terbit: ' . $request->input('notes')
            );

            if ($sp->deptHead) {
                $this->sendApprovalNotification($sp, $sp->deptHead, 'pending_dh');
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Pengajuan pembatalan SP (Cancel) berhasil diajukan dan dikirim ke Dept Head untuk persetujuan.'
            ]);
        }

        // Poin 1: Jika SP belum terbit, Admin hanya bisa cancel sebelum Dept Head approve (DRAFT / PENDING_DH)
        if (!$isIrRole && !in_array($sp->current_status, [SpPelanggaran::STATUS_DRAFT, SpPelanggaran::STATUS_PENDING_DH])) {
            return response()->json([
                'status' => 'error',
                'message' => 'Pembatalan SP yang telah disetujui Dept Head hanya dapat dilakukan oleh pihak IR.'
            ], 403);
        }

        $updateData = [
            'current_status' => SpPelanggaran::STATUS_CANCELLED,
            'dept_head_notes' => $request->input('notes'),
        ];
        if ($lampiranCancelPath) {
            $updateData['lampiran_cancel'] = $lampiranCancelPath;
        }
        $sp->update($updateData);

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

    public function dhApproveCancel(Request $request, $id)
    {
        $sp = SpPelanggaran::findOrFail($id);

        if ($sp->current_status !== SpPelanggaran::STATUS_CANCEL_PENDING_DH) {
            return response()->json([
                'status' => 'error',
                'message' => 'SP tidak dalam status pengajuan pembatalan Dept Head.'
            ], 422);
        }

        $sp->update([
            'current_status' => SpPelanggaran::STATUS_CANCEL_PENDING_IR,
            'dept_head_notes' => $request->input('notes'),
        ]);

        SpApprovalLog::logAction(
            $sp->id,
            Auth::id() ?: session('user_id'),
            SpApprovalLog::ACTION_CANCEL_APPROVE_DH,
            $request->input('notes', 'Pembatalan SP disetujui Dept Head')
        );

        return response()->json([
            'status' => 'success',
            'message' => 'Persetujuan pembatalan SP berhasil dan diteruskan ke IR Staff.'
        ]);
    }

    public function dhMassApproveCancel(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'integer|exists:sp_pelanggarans,id',
            'notes' => 'nullable|string',
        ]);

        $ids = $request->input('ids', []);
        $notes = $request->input('notes', 'Pembatalan SP disetujui sekaligus oleh Dept Head');

        $spRecords = SpPelanggaran::whereIn('id', $ids)
            ->where('current_status', SpPelanggaran::STATUS_CANCEL_PENDING_DH)
            ->get();

        if ($spRecords->isEmpty()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Tidak ada pengajuan pembatalan SP berstatus Pending Dept Head yang dapat disetujui.'
            ], 422);
        }

        $approvedCount = 0;
        foreach ($spRecords as $sp) {
            $sp->update([
                'current_status' => SpPelanggaran::STATUS_CANCEL_PENDING_IR,
                'dept_head_notes' => $notes,
            ]);

            SpApprovalLog::logAction(
                $sp->id,
                Auth::id() ?: session('user_id'),
                SpApprovalLog::ACTION_CANCEL_APPROVE_DH,
                $notes
            );

            $approvedCount++;
        }

        return response()->json([
            'status' => 'success',
            'message' => "Berhasil menyetujui pembatalan {$approvedCount} SP sekaligus dan diteruskan ke IR Staff."
        ]);
    }

    public function irStaffApproveCancel(Request $request, $id)
    {
        $sp = SpPelanggaran::findOrFail($id);

        if ($sp->current_status !== SpPelanggaran::STATUS_CANCEL_PENDING_IR) {
            return response()->json([
                'status' => 'error',
                'message' => 'SP tidak dalam status konfirmasi pembatalan IR Staff.'
            ], 422);
        }

        $sp->update([
            'current_status' => SpPelanggaran::STATUS_CANCEL_PENDING_IR_HEAD,
            'ir_staff_notes' => $request->input('notes'),
        ]);

        SpApprovalLog::logAction(
            $sp->id,
            Auth::id() ?: session('user_id'),
            SpApprovalLog::ACTION_CANCEL_APPROVE_IR,
            $request->input('notes', 'Pembatalan SP dikonfirmasi IR Staff')
        );

        return response()->json([
            'status' => 'success',
            'message' => 'Pembatalan SP dikonfirmasi IR Staff dan diteruskan ke IR Head untuk persetujuan final.'
        ]);
    }

    public function irHeadApproveCancel(Request $request, $id)
    {
        $sp = SpPelanggaran::findOrFail($id);

        if ($sp->current_status !== SpPelanggaran::STATUS_CANCEL_PENDING_IR_HEAD) {
            return response()->json([
                'status' => 'error',
                'message' => 'SP tidak dalam status persetujuan final pembatalan IR Head.'
            ], 422);
        }

        $sp->update([
            'current_status' => SpPelanggaran::STATUS_CANCELLED,
            'ir_head_notes' => $request->input('notes'),
        ]);

        SpApprovalLog::logAction(
            $sp->id,
            Auth::id() ?: session('user_id'),
            SpApprovalLog::ACTION_CANCEL_FINAL,
            $request->input('notes', 'Pembatalan SP disetujui final oleh IR Head')
        );

        return response()->json([
            'status' => 'success',
            'message' => 'Surat Peringatan resmi dibatalkan (CANCELLED) oleh IR Head.'
        ]);
    }

    public function masterKodeIndex(Request $request)
    {
        if (!Auth::check() && !session('login') && !session('username')) {
            return redirect('/login');
        }

        $kategori = strtoupper($request->get('kategori', 'ALL'));
        $query = SpKodePelanggaran::query()->orderBy('kategori_kode', 'asc')->orderBy('kode', 'asc');

        if ($kategori === 'ADMIN') {
            $query->where('kategori_kode', 'ADMIN');
        } elseif ($kategori === 'IR') {
            $query->where('kategori_kode', 'IR');
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('kode', 'like', "%{$search}%")
                    ->orWhere('nama_pelanggaran', 'like', "%{$search}%")
                    ->orWhere('bentuk_pelanggaran', 'like', "%{$search}%")
                    ->orWhere('dasar_pertimbangan', 'like', "%{$search}%")
                    ->orWhere('jenis_sp', 'like', "%{$search}%");
            });
        }

        $masterKodes = $query->paginate(15);
        return view('sp_pelanggaran.master_kode', compact('masterKodes', 'kategori'));
    }

    public function masterKodeStore(Request $request)
    {
        $request->validate([
            'kategori_kode' => 'required|in:ADMIN,IR',
            'kode' => 'required|string|max:255',
            'nama_pelanggaran' => 'required|string|max:255',
            'jenis_sp' => 'required|string',
            'pasal_dilanggar' => 'nullable|string',
            'bentuk_pelanggaran' => 'nullable|string',
            'dasar_pertimbangan' => 'nullable|string',
            'deskripsi' => 'nullable|string',
        ]);

        SpKodePelanggaran::create([
            'kategori_kode' => $request->kategori_kode,
            'kode' => $request->kode,
            'nama_pelanggaran' => $request->nama_pelanggaran,
            'jenis_sp' => $request->jenis_sp,
            'pasal_dilanggar' => $request->dasar_pertimbangan ?: $request->pasal_dilanggar,
            'bentuk_pelanggaran' => $request->bentuk_pelanggaran ?: $request->nama_pelanggaran,
            'dasar_pertimbangan' => $request->dasar_pertimbangan ?: $request->pasal_dilanggar,
            'deskripsi' => $request->bentuk_pelanggaran ?: $request->deskripsi,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Kode Pelanggaran berhasil ditambahkan!'
        ]);
    }

    public function masterKodeUpdate(Request $request, $id)
    {
        $kodeModel = SpKodePelanggaran::findOrFail($id);

        $request->validate([
            'kategori_kode' => 'required|in:ADMIN,IR',
            'kode' => 'required|string|max:255',
            'nama_pelanggaran' => 'required|string|max:255',
            'jenis_sp' => 'required|string',
            'pasal_dilanggar' => 'nullable|string',
            'bentuk_pelanggaran' => 'nullable|string',
            'dasar_pertimbangan' => 'nullable|string',
            'deskripsi' => 'nullable|string',
        ]);

        $kodeModel->update([
            'kategori_kode' => $request->kategori_kode,
            'kode' => $request->kode,
            'nama_pelanggaran' => $request->nama_pelanggaran,
            'jenis_sp' => $request->jenis_sp,
            'pasal_dilanggar' => $request->dasar_pertimbangan ?: $request->pasal_dilanggar,
            'bentuk_pelanggaran' => $request->bentuk_pelanggaran ?: $request->nama_pelanggaran,
            'dasar_pertimbangan' => $request->dasar_pertimbangan ?: $request->pasal_dilanggar,
            'deskripsi' => $request->bentuk_pelanggaran ?: $request->deskripsi,
        ]);

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
        $deptCodes = $this->getDeptCodes($userDept);

        $query = SpPelanggaran::with(['employee', 'dates'])->orderBy('id', 'desc');

        if (!$isIrRole && !empty($deptCodes)) {
            $query->whereHas('employee', function ($empQ) use ($deptCodes) {
                $empQ->whereIn('kode_divisi', $deptCodes)
                    ->orWhereIn('kode_bagian', $deptCodes);
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
            $query->where(function ($q) use ($request) {
                $q->whereHas('dates', function ($dq) use ($request) {
                    $dq->whereDate('tanggal', '>=', $request->start_date);
                })->orWhereDate('created_at', '>=', $request->start_date);
            });
        }
        if ($request->filled('end_date')) {
            $query->where(function ($q) use ($request) {
                $q->whereHas('dates', function ($dq) use ($request) {
                    $dq->whereDate('tanggal', '<=', $request->end_date);
                })->orWhereDate('created_at', '<=', $request->end_date);
            });
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

        // ── Header Title ────────────────────────────────────────────────────────
        $sheet->setCellValue('A1', 'PT. BUMI ALAM SEGAR');
        $sheet->setCellValue('A2', 'LAPORAN REKAPITULASI & RIWAYAT SURAT PERINGATAN (SP) KARYAWAN');
        $sheet->setCellValue('A3', 'Filter: ' . $kategoriLabel . ' | Tanggal Cetak: ' . date('d/m/Y H:i'));

        $sheet->mergeCells('A1:P1');
        $sheet->mergeCells('A2:P2');
        $sheet->mergeCells('A3:P3');

        $sheet->getStyle('A1:A2')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1:A3')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        // ── Table Headers ───────────────────────────────────────────────────────
        $headers = [
            'A5' => 'NO',
            'B5' => 'NOMOR SP',
            'C5' => 'NIK',
            'D5' => 'NAMA KARYAWAN',
            'E5' => 'DEPT / BAGIAN',
            'F5' => 'GROUP',
            'G5' => 'JENIS SP',
            'H5' => 'TGL KEJADIAN',
            'I5' => 'BENTUK PELANGGARAN',
            'J5' => 'STATUS DEPT HEAD',
            'K5' => 'TGL APPROVE DH',
            'L5' => 'STATUS IR STAFF',
            'M5' => 'STATUS IR HEAD',
            'N5' => 'TGL TERBIT (IR HEAD)',
            'O5' => 'BERLAKU SAMPAI',
            'P5' => 'KLASIFIKASI STATUS',
        ];

        foreach ($headers as $cell => $val) {
            $sheet->setCellValue($cell, $val);
        }

        $headerStyle = [
            'font'      => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill'      => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => '1E3C72']],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'wrapText' => true],
            'borders'   => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN]],
        ];
        $sheet->getStyle('A5:P5')->applyFromArray($headerStyle);
        $sheet->getRowDimension(5)->setRowHeight(30);

        // ── Status Label Maps ───────────────────────────────────────────────────
        $statusDhMap = [
            'PENDING_DH'       => 'Menunggu Persetujuan',
            'APPROVED_BY_DH'   => 'Disetujui',
            'APPROVED'         => 'Disetujui',
            'REJECTED'         => 'Ditolak',
            'CANCELLED'        => 'Dibatalkan',
        ];
        $statusIrStaffMap = [
            'PENDING_IR'       => 'Menunggu Review',
            'PENDING_IR_HEAD'  => 'Diteruskan ke IR Head',
            'APPROVED'         => 'Disetujui',
            'REJECTED'         => 'Ditolak',
        ];
        $statusIrHeadMap = [
            'PENDING_IR_HEAD'  => 'Menunggu Persetujuan',
            'APPROVED'         => 'Disetujui',
            'REJECTED'         => 'Ditolak',
        ];

        // ── Data Rows ───────────────────────────────────────────────────────────
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

            // ── Approval Status Per Level ──────────────────────────────────────
            $currentStatus = $sp->current_status;

            // Dept Head status
            if (in_array($currentStatus, ['DRAFT', 'PENDING_DH'])) {
                $statusDh = $currentStatus === 'PENDING_DH' ? 'Menunggu Persetujuan' : 'Belum Diajukan';
            } elseif (in_array($currentStatus, ['REJECTED', 'CANCELLED'])) {
                // Check at which level it was rejected from approval logs
                $dhLog = $sp->approvalLogs->where('action', 'REJECT')->first();
                $statusDh = $dhLog ? 'Ditolak' : 'Telah Diproses';
            } else {
                $statusDh = 'Disetujui ✓';
            }

            $tglApproveDh = $sp->dept_head_approved_at
                ? \Carbon\Carbon::parse($sp->dept_head_approved_at)->format('d/m/Y')
                : '-';

            // IR Staff status
            if (in_array($currentStatus, ['DRAFT', 'PENDING_DH'])) {
                $statusIrStaff = 'Belum Sampai';
            } elseif ($currentStatus === 'PENDING_IR') {
                $statusIrStaff = 'Sedang Direview';
            } elseif (in_array($currentStatus, ['PENDING_IR_HEAD', 'APPROVED'])) {
                $statusIrStaff = 'Selesai Review ✓';
            } elseif ($currentStatus === 'REJECTED') {
                $irStaffRejectLog = $sp->approvalLogs->where('action', 'REJECT')->first();
                $statusIrStaff = $irStaffRejectLog ? 'Ditolak' : 'Belum Sampai';
            } else {
                $statusIrStaff = '-';
            }

            // IR Head status
            if ($currentStatus === 'APPROVED') {
                $statusIrHead = 'Disetujui ✓';
            } elseif ($currentStatus === 'PENDING_IR_HEAD') {
                $statusIrHead = 'Menunggu Persetujuan';
            } elseif ($currentStatus === 'REJECTED') {
                $irHeadLog = $sp->approvalLogs->where('action', 'REJECT')->where('role', 'ir_head')->first();
                $statusIrHead = $irHeadLog ? 'Ditolak' : 'Belum Sampai';
            } elseif (in_array($currentStatus, ['DRAFT', 'PENDING_DH', 'PENDING_IR'])) {
                $statusIrHead = 'Belum Sampai';
            } else {
                $statusIrHead = '-';
            }

            // ── Tanggal Terbit = ir_head_approved_at ──────────────────────────
            $tglTerbit = $sp->ir_head_approved_at
                ? \Carbon\Carbon::parse($sp->ir_head_approved_at)->format('d/m/Y')
                : '-';

            // ── Berlaku Sampai ────────────────────────────────────────────────
            $berlakuSampai = $sp->masa_berlaku_sampai
                ? \Carbon\Carbon::parse($sp->masa_berlaku_sampai)->format('d/m/Y')
                : '-';

            // ── Bentuk Pelanggaran ────────────────────────────────────────────
            $bentukPelanggaran = $sp->alasan ?: '-';

            // ── Write Cells ────────────────────────────────────────────────────
            $sheet->setCellValue('A' . $row, $idx + 1);
            $sheet->setCellValue('B' . $row, $sp->nomor_sp_generated ?: ($sp->no_sp ?: 'DRAFT'));
            $sheet->setCellValue('C' . $row, $emp->nik ?? '-');
            $sheet->setCellValue('D' . $row, $emp->nama ?? '-');
            $sheet->setCellValue('E' . $row, $deptBagian);
            $sheet->setCellValue('F' . $row, $groupVal ?: '-');
            $sheet->setCellValue('G' . $row, $sp->jenis_pelanggaran ?: '-');

            // Format H: Tgl Kejadian
            if ($sp->tanggal_pelanggaran) {
                $dtH = \Carbon\Carbon::parse($sp->tanggal_pelanggaran);
                $sheet->setCellValue('H' . $row, \PhpOffice\PhpSpreadsheet\Shared\Date::PHPToExcel($dtH));
                $sheet->getStyle('H' . $row)->getNumberFormat()->setFormatCode('[$-id-ID]d/mmmm/yyyy');
            } else {
                $sheet->setCellValue('H' . $row, '-');
            }

            $sheet->setCellValue('I' . $row, $bentukPelanggaran);
            $sheet->setCellValue('J' . $row, $statusDh);

            // Format K: Tgl Approve DH
            if ($sp->dept_head_approved_at) {
                $dtK = \Carbon\Carbon::parse($sp->dept_head_approved_at);
                $sheet->setCellValue('K' . $row, \PhpOffice\PhpSpreadsheet\Shared\Date::PHPToExcel($dtK));
                $sheet->getStyle('K' . $row)->getNumberFormat()->setFormatCode('[$-id-ID]d/mmmm/yyyy');
            } else {
                $sheet->setCellValue('K' . $row, '-');
            }

            $sheet->setCellValue('L' . $row, $statusIrStaff);
            $sheet->setCellValue('M' . $row, $statusIrHead);

            // Format N: Tgl Terbit (IR Head)
            if ($sp->ir_head_approved_at) {
                $dtN = \Carbon\Carbon::parse($sp->ir_head_approved_at);
                $sheet->setCellValue('N' . $row, \PhpOffice\PhpSpreadsheet\Shared\Date::PHPToExcel($dtN));
                $sheet->getStyle('N' . $row)->getNumberFormat()->setFormatCode('[$-id-ID]d/mmmm/yyyy');
            } else {
                $sheet->setCellValue('N' . $row, '-');
            }

            // Format O: Berlaku Sampai
            if ($sp->masa_berlaku_sampai) {
                $dtO = \Carbon\Carbon::parse($sp->masa_berlaku_sampai);
                $sheet->setCellValue('O' . $row, \PhpOffice\PhpSpreadsheet\Shared\Date::PHPToExcel($dtO));
                $sheet->getStyle('O' . $row)->getNumberFormat()->setFormatCode('[$-id-ID]d/mmmm/yyyy');
            } else {
                $sheet->setCellValue('O' . $row, '-');
            }

            $sheet->setCellValue('P' . $row, $sp->kategori_sp ?: $sp->current_status);

            // Borders & alignment
            $sheet->getStyle('A' . $row . ':P' . $row)->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
            $sheet->getStyle('A' . $row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('C' . $row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('F' . $row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('H' . $row . ':N' . $row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('I' . $row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT)->setWrapText(true);

            // Color coding per klasifikasi
            $bgColor = null;
            if ($sp->kategori_sp === 'SP3') {
                $bgColor = 'FEE2E2'; // merah muda
            } elseif ($sp->kategori_sp === 'AKTIF') {
                $bgColor = 'DCFCE7'; // hijau muda
            } elseif ($sp->kategori_sp === 'EXPIRED') {
                $bgColor = 'F1F5F9'; // abu muda
            } elseif (in_array($sp->current_status, ['CANCELLED', 'REJECTED'])) {
                $bgColor = 'FEF3C7'; // kuning muda
            }
            if ($bgColor) {
                $sheet->getStyle('A' . $row . ':P' . $row)->getFill()
                    ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                    ->getStartColor()->setRGB($bgColor);
            }

            $row++;
        }

        // ── Column Auto Width ───────────────────────────────────────────────────
        foreach (range('A', 'P') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
        // Bentuk pelanggaran beri lebar manual agar tidak terlalu lebar
        $sheet->getColumnDimension('I')->setWidth(40);
        $sheet->getColumnDimension('D')->setWidth(25);
        $sheet->getColumnDimension('E')->setWidth(28);

        // ── Legend Section ──────────────────────────────────────────────────────
        $legendRow = $row + 2;
        $sheet->setCellValue('A' . $legendRow, 'Keterangan Warna:');
        $sheet->getStyle('A' . $legendRow)->getFont()->setBold(true);

        $legends = [
            ['DCFCE7', 'SP Aktif (Berlaku <= 6 Bulan)'],
            ['FEE2E2', 'SP Berat / SP+3'],
            ['F1F5F9', 'SP Expired (> 6 Bulan)'],
            ['FEF3C7', 'SP Ditolak / Dibatalkan'],
        ];
        foreach ($legends as $i => $leg) {
            $lr = $legendRow + 1 + $i;
            $sheet->setCellValue('A' . $lr, '  ' . $leg[1]);
            $sheet->getStyle('A' . $lr)->getFill()
                ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                ->getStartColor()->setRGB($leg[0]);
            $sheet->getStyle('A' . $lr)->getBorders()->getAllBorders()
                ->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
        }

        $fileName = 'Rekapitulasi_SP_' . $kategori . '_' . date('Ymd_His') . '.xlsx';

        if (ob_get_length()) {
            @ob_end_clean();
        }

        return response()->streamDownload(function () use ($spreadsheet) {
            $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
            $writer->save('php://output');
        }, $fileName, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Cache-Control' => 'max-age=0, no-cache, must-revalidate',
            'Pragma' => 'public',
        ]);
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

                $sheetNameClean = strtolower(trim($sheetName));
                $cat = ($sheetNameClean === 'kode_admin') ? 'ADMIN' : (($sheetNameClean === 'kode_ir') ? 'IR' : null);

                if (!$cat) continue;

                foreach ($rows as $idx => $row) {
                    if ($idx === 1) continue; // Skip header

                    $kodeVal = trim($row['A'] ?? '');
                    $bentuk = trim($row['B'] ?? '');
                    $dasar = trim($row['C'] ?? '');
                    $tingkatSpRaw = trim($row['D'] ?? '');

                    if (empty($kodeVal) || strtolower($kodeVal) === 'kode_admin' || strtolower($kodeVal) === 'kode_ir') {
                        continue;
                    }

                    $jenisSp = $tingkatSpRaw ?: 'SP I';

                    $existing = SpKodePelanggaran::where('kode', $kodeVal)
                        ->where('kategori_kode', $cat)
                        ->first();

                    if ($existing) {
                        $existing->update([
                            'nama_pelanggaran' => $kodeVal,
                            'bentuk_pelanggaran' => $bentuk,
                            'dasar_pertimbangan' => $dasar,
                            'pasal_dilanggar' => $dasar,
                            'deskripsi' => $bentuk,
                            'jenis_sp' => $jenisSp,
                        ]);
                        $updatedCount++;
                    } else {
                        SpKodePelanggaran::create([
                            'kategori_kode' => $cat,
                            'kode' => $kodeVal,
                            'nama_pelanggaran' => $kodeVal,
                            'bentuk_pelanggaran' => $bentuk,
                            'dasar_pertimbangan' => $dasar,
                            'pasal_dilanggar' => $dasar,
                            'deskripsi' => $bentuk,
                            'jenis_sp' => $jenisSp,
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

    /**
     * Menu & View for Upload / View Hasil Konseling SP (Pelanggaran & Mangkir)
     * Hanya SP yang sudah terbit (current_status = 'APPROVED') yang ditampilkan.
     * Admin dapat mengunggah file PDF konseling.
     * IR Staff, IR Head, dan Dept Head hanya dapat melihat/mengunduh file konseling.
     */
    public function uploadKonselingIndex(Request $request)
    {
        if (!Auth::check() && !session('login') && !session('username')) {
            return redirect('/login');
        }

        $user = Auth::user();
        $permissions = view()->shared('permissions') ?: [];
        $isIrRole = in_array('sp_pelanggaran_ir_staff', $permissions) || in_array('sp_pelanggaran_ir_head', $permissions);
        $isAdminRole = in_array('sp_pelanggaran_admin', $permissions) || ($user && in_array($user->user_role, ['admin', 'superadmin']));
        $userDept = ($user ? $user->dept_id : null) ?: session('kode_department');
        $deptCodes = $this->getDeptCodes($userDept);

        // Query SP yang sudah terbit (APPROVED) baik SP Pelanggaran maupun SP Mangkir
        $query = SpPelanggaran::with(['employee', 'creator', 'uploaderKonseling', 'dates'])
            ->where('current_status', SpPelanggaran::STATUS_APPROVED)
            ->orderBy('updated_at', 'desc');

        // Filter Dept Head (non-IR role)
        if (!$isIrRole && !$isAdminRole && !empty($deptCodes)) {
            $query->whereHas('employee', function ($empQ) use ($deptCodes) {
                $empQ->whereIn('kode_divisi', $deptCodes)
                    ->orWhereIn('kode_bagian', $deptCodes);
            });
        }

        // Search Filter (Nomor SP, Kode Admin, NIK, Nama Karyawan)
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

        // Filter Sumber Data (PELANGGARAN / MANGKIR)
        if ($request->filled('sumber')) {
            $sumber = $request->sumber;
            if ($sumber === 'PELANGGARAN') {
                $query->where(function ($q) {
                    $q->where('sumber_data', 'PELANGGARAN')
                        ->orWhereNull('sumber_data');
                });
            } elseif ($sumber === 'MANGKIR') {
                $query->where('sumber_data', 'MANGKIR');
            }
        }

        // Filter Status Upload Konseling
        if ($request->filled('status_konseling')) {
            $stKonseling = $request->status_konseling;
            if ($stKonseling === 'SUDAH') {
                $query->whereNotNull('file_konseling');
            } elseif ($stKonseling === 'BELUM') {
                $query->whereNull('file_konseling');
            }
        }

        // Hitung statistik untuk cards/badges
        $totalTerbit = (clone $query)->count();
        $countSudah  = (clone $query)->whereNotNull('file_konseling')->count();
        $countBelum  = (clone $query)->whereNull('file_konseling')->count();

        $spRecords = $query->paginate(10)->appends($request->query());

        // Hak akses upload hanya untuk Admin
        $canUpload = $isAdminRole || in_array('sp_pelanggaran_admin', $permissions);

        return view('sp_pelanggaran.upload_konseling', compact(
            'spRecords',
            'totalTerbit',
            'countSudah',
            'countBelum',
            'canUpload'
        ));
    }

    /**
     * Admin Upload / Update File PDF Konseling SP
     */
    public function storeKonseling(Request $request, $id)
    {
        $permissions = view()->shared('permissions') ?: [];
        $user = Auth::user();
        $isAdmin = in_array('sp_pelanggaran_admin', $permissions) || ($user && in_array($user->user_role, ['admin', 'superadmin']));

        if (!$isAdmin) {
            return response()->json([
                'status' => 'error',
                'message' => 'Hanya Admin yang memiliki hak akses untuk mengunggah file PDF konseling.'
            ], 403);
        }

        $sp = SpPelanggaran::findOrFail($id);

        if ($sp->current_status !== SpPelanggaran::STATUS_APPROVED) {
            return response()->json([
                'status' => 'error',
                'message' => 'Upload konseling hanya dapat dilakukan untuk SP yang telah resmi terbit (Approved).'
            ], 422);
        }

        $request->validate([
            'file_konseling' => 'required|file|mimes:pdf|max:2048', // Max 2MB PDF
        ], [
            'file_konseling.required' => 'File PDF konseling wajib diunggah.',
            'file_konseling.mimes' => 'Format file konseling harus berupa PDF.',
            'file_konseling.max' => 'Ukuran file PDF konseling maksimal 2 MB. Silakan kompres file terlebih dahulu jika terlalu besar.',
        ]);

        if ($request->hasFile('file_konseling')) {
            // Delete old file if exists
            if ($sp->file_konseling && file_exists(public_path($sp->file_konseling))) {
                @unlink(public_path($sp->file_konseling));
            }

            $file = $request->file('file_konseling');
            $filename = 'konseling_' . $sp->id . '_' . time() . '_' . preg_replace('/[^A-Za-z0-9\._-]/', '', $file->getClientOriginalName());
            $uploadPath = public_path('uploads/sp_konseling');

            if (!file_exists($uploadPath)) {
                mkdir($uploadPath, 0777, true);
            }

            $file->move($uploadPath, $filename);
            $filePath = 'uploads/sp_konseling/' . $filename;

            $sp->update([
                'file_konseling' => $filePath,
                'uploaded_konseling_at' => now(),
                'uploaded_konseling_by' => Auth::id() ?: session('user_id'),
            ]);

            SpApprovalLog::logAction(
                $sp->id,
                Auth::id() ?: session('user_id'),
                'UPLOAD_KONSELING',
                'Admin mengunggah file PDF hasil konseling: ' . $filename
            );

            return response()->json([
                'status' => 'success',
                'message' => 'File PDF hasil konseling berhasil diunggah!',
                'file_path' => asset($filePath)
            ]);
        }

        return response()->json([
            'status' => 'error',
            'message' => 'Gagal mengunggah file.'
        ], 400);
    }

    /**
     * Admin Delete File PDF Konseling SP
     */
    public function deleteKonseling($id)
    {
        $permissions = view()->shared('permissions') ?: [];
        $user = Auth::user();
        $isAdmin = in_array('sp_pelanggaran_admin', $permissions) || ($user && in_array($user->user_role, ['admin', 'superadmin']));

        if (!$isAdmin) {
            return response()->json([
                'status' => 'error',
                'message' => 'Hanya Admin yang memiliki hak akses untuk menghapus file PDF konseling.'
            ], 403);
        }

        $sp = SpPelanggaran::findOrFail($id);

        if ($sp->file_konseling && file_exists(public_path($sp->file_konseling))) {
            @unlink(public_path($sp->file_konseling));
        }

        $sp->update([
            'file_konseling' => null,
            'uploaded_konseling_at' => null,
            'uploaded_konseling_by' => null,
        ]);

        SpApprovalLog::logAction(
            $sp->id,
            Auth::id() ?: session('user_id'),
            'DELETE_KONSELING',
            'Admin menghapus file PDF hasil konseling'
        );

        return response()->json([
            'status' => 'success',
            'message' => 'File PDF hasil konseling berhasil dihapus.'
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
