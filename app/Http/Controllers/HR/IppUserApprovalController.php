<?php

namespace App\Http\Controllers\HR;

use App\Http\Controllers\Controller;
use App\Jobs\HR\SendIppApprovalDecisionJob;
use App\Jobs\HR\SendIppDiskusiAnswerNotificationJob;
use App\Models\HR\HrIpp;
use App\Models\HR\HrIppAktual;
use App\Models\HR\HrIppApprovalNote;
use App\Models\HR\HrIppDiskusi;
use App\Models\HR\HrIppForeman;
use App\Models\HR\HrIppForemanNilai;
use App\Models\HR\HrIppOtorisasi;
use App\Models\HR\HrPmsControlNotifikasi;
use App\Models\HR\HrPmsEmployeeSnapshot;
use App\Models\HR\HrPmsLockingIpp;
use App\Support\IppDiskusiBroadcast;
use App\Support\IppForemanPeserta;
use App\Support\PmsEmployeeSnapshot;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class IppUserApprovalController extends Controller
{
    private function currentNik(): string
    {
        return trim((string) (Auth::user()->nik ?? ''));
    }

    private function approverLabelForOtorisasi(array $myOto): string
    {
        $nik = $this->currentNik();
        $name = Auth::user()->name ?? '';

        foreach ($myOto as $o) {
            if ($o->nik_manager === $nik) {
                return 'Manager: ' . $nik . ' - ' . $name;
            }
            if ($o->nik_associate_manager === $nik) {
                return 'Associate Manager: ' . $nik . ' - ' . $name;
            }
        }

        return $nik . ' - ' . $name;
    }

    private function roleForOtorisasi($oto): ?string
    {
        $nik = $this->currentNik();
        if ($oto->nik_manager === $nik) {
            return 'manager';
        }
        if ($oto->nik_associate_manager === $nik) {
            return 'associate_manager';
        }
        return null;
    }

    private function stageStatusOfRows(\Illuminate\Support\Collection $rows, string $column): ?string
    {
        $statuses = $rows->pluck($column)->filter()->unique()->values();
        if ($statuses->isEmpty()) {
            return null;
        }
        if ($statuses->contains('rejected')) {
            return 'rejected';
        }
        if ($statuses->contains('approved')) {
            return 'approved';
        }
        return null;
    }

    private function tahapanLabel(string $tahapan): string
    {
        $parts = explode(' ', trim($tahapan));
        if (count($parts) === 2) {
            $kind = strtoupper($parts[0]);
            if ($kind === 'MID') {
                return 'Mid Year ' . $parts[1];
            }
            if ($kind === 'END') {
                return 'End Year ' . $parts[1];
            }
        }

        return $tahapan;
    }

    /**
     * Admin HR (permission hr_upload_file_template_ipp) tidak terhalang LOCKING IPP.
     */
    private function canBypassIppLock(): bool
    {
        $user = Auth::user();

        return $user !== null && in_array(
            'hr_upload_file_template_ipp',
            (array) $user->getAllPermissionCodenames(),
            true
        );
    }

    /**
     * Tolak perubahan kolom Aktual (Atasan) saat tahapan terkunci (LOCKING IPP).
     * Nilai yang tidak berubah tetap boleh disimpan.
     */
    private function guardLockedAktualAtasan(string $tahapan, ?HrIppAktual $existing, $aktualAtasan, $aktualAtasanVarchar): void
    {
        if ($this->canBypassIppLock() || !HrPmsLockingIpp::isLocked($tahapan, 'atasan')) {
            return;
        }

        $changed = $existing
            ? HrPmsLockingIpp::valueChanged($existing->aktual_atasan, $existing->aktual_atasan_varchar, $aktualAtasan, $aktualAtasanVarchar)
            : ($aktualAtasan !== null || $aktualAtasanVarchar !== null);

        if ($changed) {
            abort(422, 'Aktual (Atasan) tahapan ' . $this->tahapanLabel($tahapan)
                . ' sedang dikunci (LOCKING IPP). Hubungi Admin HR untuk membuka kunci.');
        }
    }

    /**
     * Tolak seluruh aksi approval (approve/reject/reset) saat tahapan terkunci (LOCKING IPP).
     * Tanpa bypass: pembukaan kunci hanya lewat Admin HR di /hr/pms-total-scoring.
     */
    private function guardLockedApproval(string $tahapan): void
    {
        if (!HrPmsLockingIpp::isLocked($tahapan, 'batalkan_approval')) {
            return;
        }

        abort(422, 'Approval tahapan ' . $this->tahapanLabel($tahapan)
            . ' sedang dikunci (LOCKING IPP). Hubungi Admin HR untuk membuka kunci.');
    }

    private function notifyApprovalDecision(HrIpp $ipp, $oto, string $tahapan, string $role, string $approval, string $note): void
    {
        if (!HrPmsControlNotifikasi::isActive()) {
            return;
        }

        $recipients = [];

        $pesertaNik = trim((string) $oto->nik_yang_bersangkutan);
        if ($pesertaNik !== '') {
            $recipients[] = [
                'nik' => $pesertaNik,
                'nama' => (string) $oto->nama_yang_bersangkutan,
                'type' => 'peserta',
            ];
        }

        if ($role === 'associate_manager') {
            $counterpartNik = trim((string) $oto->nik_manager);
            $counterpartNama = (string) $oto->nama_manager;
        } else {
            $counterpartNik = trim((string) $oto->nik_associate_manager);
            $counterpartNama = (string) $oto->nama_associate_manager;
        }

        $managerApproved = $role === 'associate_manager'
            && ($ipp->isRubric()
                ? HrIppForemanNilai::whereIn('hr_ipp_foreman_id', $ipp->foremanRows()->pluck('id'))
                    ->where('nik', $oto->nik_yang_bersangkutan)
                    ->where('tahapan', $tahapan)
                    ->where('approval_manager', 'approved')
                    ->exists()
                : HrIppAktual::where('ipp_otorisasi_id', $oto->id)
                    ->where('tahapan', $tahapan)
                    ->where('approval_manager', 'approved')
                    ->exists());
        $mustApprove = $approval === 'approved' && $role === 'associate_manager' && !$managerApproved;

        if ($counterpartNik !== '' && $counterpartNik !== $this->currentNik()) {
            $recipients[] = [
                'nik' => $counterpartNik,
                'nama' => $counterpartNama,
                'type' => 'atasan',
                'must_approve' => $mustApprove,
            ];
        }

        if (empty($recipients)) {
            return;
        }

        SendIppApprovalDecisionJob::dispatch(
            $ipp->periode,
            $this->tahapanLabel($tahapan),
            $pesertaNik,
            (string) $oto->nama_yang_bersangkutan,
            Auth::user()->name ?? $this->currentNik(),
            $role === 'associate_manager' ? 'Associate Manager' : 'Manager',
            $approval,
            $note,
            $recipients
        );
    }

    private function checkAccess(): void
    {
        $nik = $this->currentNik();
        if ($nik === '') {
            abort(403);
        }

        // Peserta manager/associate manager diambil dari hr_ipp_otorisasi;
        // fallback ke hr_pms_employee_snapshot bila belum ada baris otorisasi.
        $isApprover = HrIppOtorisasi::where(function ($q) use ($nik) {
            $q->where('nik_manager', $nik)
                ->orWhere('nik_associate_manager', $nik);
        })->exists();

        if (!$isApprover) {
            $isApprover = HrPmsEmployeeSnapshot::where(function ($q) use ($nik) {
                $q->where('manager_nik', $nik)
                    ->orWhere('associate_manager_nik', $nik);
            })->exists();
        }

        if (!$isApprover) {
            abort(403);
        }
    }

    private function buildTahapan(?string $periode): array
    {
        $tahapan = [];
        if (!$periode) {
            return $tahapan;
        }

        $years = [];
        if (preg_match('/(\d{4})\s*[-–—]\s*(\d{4})/', $periode, $m)) {
            $start = (int) $m[1];
            $end = (int) $m[2];
            if ($end >= $start && $end - $start <= 50) {
                for ($y = $start; $y <= $end; $y++) {
                    $years[] = $y;
                }
            }
        }
        if (empty($years)) {
            preg_match_all('/\d{4}/', $periode, $m);
            $years = array_map('intval', array_values(array_unique($m[0] ?? [])));
        }
        sort($years);

        foreach ($years as $year) {
            $tahapan[] = 'MID ' . $year;
            $tahapan[] = 'END ' . $year;
        }
        return $tahapan;
    }

    private function myOtorisasi(int $ippId): array
    {
        $nik = $this->currentNik();

        return HrIppOtorisasi::where('ipp_id', $ippId)
            ->where(function ($q) use ($nik) {
                $q->where('nik_manager', $nik)
                    ->orWhere('nik_associate_manager', $nik);
            })
            ->orderBy('id')
            ->get()
            ->all();
    }

    public function index()
    {
        $this->checkAccess();

        return view('hr.ipp-user-approval.ipp-user-approval', [
            'userNik' => $this->currentNik(),
            'userName' => Auth::user()->name ?? '',
        ]);
    }

    private function resolveUploaders(\Illuminate\Support\Collection $values): array
    {
        $vals = $values->filter()->unique()->values();
        if ($vals->isEmpty()) {
            return [];
        }

        $users = DB::table('users')
            ->where(function ($q) use ($vals) {
                $q->whereIn('username', $vals->all())
                    ->orWhereIn('nik', $vals->all());
            })
            ->get(['username', 'nik', 'name']);

        $map = [];
        foreach ($users as $u) {
            $info = ['nik' => $u->nik, 'name' => $u->name];
            if ($u->username) {
                $map[$u->username] = $info;
            }
            if ($u->nik) {
                $map[$u->nik] = $info;
            }
        }
        return $map;
    }

    public function list(Request $request)
    {
        $this->checkAccess();

        $nik = $this->currentNik();

        $search = trim((string) $request->get('search', ''));
        $perPage = (int) $request->get('per_page', 10);
        $page = max(1, (int) $request->get('page', 1));
        if ($perPage < 1) {
            $perPage = 10;
        }

        $otorisasiQuery = HrIppOtorisasi::query()
            ->where(function ($q) use ($nik) {
                $q->where('nik_manager', $nik)
                    ->orWhere('nik_associate_manager', $nik);
            });

        if ($search !== '') {
            $otorisasiQuery->where(function ($q) use ($search) {
                $q->whereHas('ipp', function ($q2) use ($search) {
                    $q2->where('periode', 'like', "%{$search}%")
                        ->orWhere('golongan', 'like', "%{$search}%")
                        ->orWhere('dept', 'like', "%{$search}%")
                        ->orWhere('position', 'like', "%{$search}%");
                })
                    ->orWhere('nik_yang_bersangkutan', 'like', "%{$search}%")
                    ->orWhere('nama_yang_bersangkutan', 'like', "%{$search}%");
            });
        }

        $allOtorisasi = $otorisasiQuery->orderBy('id')->get();

        // IPP rubrik 2-3: peserta dari snapshot (periode di mana saya manager/AM).
        $rubricPeriodes = HrPmsEmployeeSnapshot::where(function ($q) use ($nik) {
            $q->where('manager_nik', $nik)
                ->orWhere('associate_manager_nik', $nik);
        })
            ->where('level_group', '2-3')
            ->distinct()
            ->pluck('periode')
            ->filter()
            ->values();

        $rubricIpps = collect();
        if ($rubricPeriodes->isNotEmpty()) {
            $rubricQuery = HrIpp::where('template_type', 'rubric')
                ->whereIn('periode', $rubricPeriodes->all());

            if ($search !== '') {
                $rubricQuery->where(function ($q) use ($search) {
                    $q->where('periode', 'like', "%{$search}%")
                        ->orWhere('golongan', 'like', "%{$search}%");
                });
            }

            $rubricIpps = $rubricQuery->orderBy('id')->get();
        }

        if ($allOtorisasi->isEmpty() && $rubricIpps->isEmpty()) {
            return response()->json(['data' => [], 'meta' => [
                'page' => 1, 'per_page' => $perPage, 'total' => 0, 'last_page' => 1,
            ]]);
        }

        $ippIds = $allOtorisasi->pluck('ipp_id')
            ->merge($rubricIpps->pluck('id'))
            ->unique()
            ->sort()
            ->values();
        $ippMap = HrIpp::withCount('details')->whereIn('id', $ippIds)->get()->keyBy('id');

        $aktualRows = $allOtorisasi->isEmpty()
            ? collect()
            : HrIppAktual::whereIn('ipp_otorisasi_id', $allOtorisasi->pluck('id'))
                ->get(['ipp_otorisasi_id', 'ipp_detail_id', 'tahapan', 'aktual', 'aktual_varchar', 'approval_associate_manager', 'approval_manager', 'review', 'reviewed_by']);

        $rubricIppIds = $ippMap->filter(fn ($i) => $i->isRubric())->keys();
        $foremanCountMap = $rubricIppIds->isEmpty()
            ? collect()
            : HrIppForeman::whereIn('ipp_id', $rubricIppIds->all())
                ->selectRaw('ipp_id, COUNT(*) as total')
                ->groupBy('ipp_id')
                ->pluck('total', 'ipp_id');
        $rubricNilaiRows = $rubricIppIds->isEmpty()
            ? collect()
            : HrIppForemanNilai::whereIn(
                'hr_ipp_foreman_id',
                HrIppForeman::whereIn('ipp_id', $rubricIppIds->all())->pluck('id')
            )->get(['hr_ipp_foreman_id', 'nik', 'tahapan', 'nilai', 'approval_associate_manager', 'approval_manager']);

        $diskusiRows = $allOtorisasi->isEmpty()
            ? collect()
            : DB::table('hr_ipp_diskusi')
                ->whereIn('ipp_otorisasi_id', $allOtorisasi->pluck('id'))
                ->get(['ipp_otorisasi_id', 'tahapan', 'status']);

        $diskusiMap = [];
        foreach ($diskusiRows as $dr) {
            $key = $dr->ipp_otorisasi_id . '|' . $dr->tahapan;
            $diskusiMap[$key] = $diskusiMap[$key] ?? ['open' => 0, 'total' => 0];
            $diskusiMap[$key]['total']++;
            if ($dr->status === 'open') {
                $diskusiMap[$key]['open']++;
            }
        }

        // Entri pagination = peserta (KPI: baris otorisasi; rubrik: nik peserta), urut per template.
        $entries = [];
        foreach ($ippIds as $ippId) {
            $ipp = $ippMap->get($ippId);
            if (!$ipp) {
                continue;
            }
            if ($ipp->isRubric()) {
                foreach (IppForemanPeserta::participants((string) $ipp->periode, $nik) as $p) {
                    $entries[] = ['ipp_id' => (int) $ippId, 'key' => (string) $p->nik];
                }
                continue;
            }
            foreach ($allOtorisasi->where('ipp_id', $ippId) as $o) {
                $entries[] = ['ipp_id' => (int) $ippId, 'key' => (string) $o->id];
            }
        }

        $total = count($entries);
        $lastPage = max(1, (int) ceil($total / $perPage));
        $pageEntries = array_slice($entries, ($page - 1) * $perPage, $perPage);

        $allowedByIpp = [];
        foreach ($pageEntries as $entry) {
            $allowedByIpp[$entry['ipp_id']][(string) $entry['key']] = true;
        }
        $pageIds = collect(array_values(array_unique(array_map(fn ($e) => $e['ipp_id'], $pageEntries))));

        $pageIppMap = $ippMap->only($pageIds->all());
        $uploaderMap = $this->resolveUploaders($pageIppMap->pluck('send_by_username'));
        $reviewerMap = $this->resolveUploaders($aktualRows->pluck('reviewed_by'));

        $rows = [];

        foreach ($pageIds as $ippId) {
            $ipp = $ippMap->get($ippId);
            if (!$ipp) {
                continue;
            }

            $isRubric = $ipp->isRubric();
            $detailCount = $isRubric ? (int) ($foremanCountMap[$ippId] ?? 0) : (int) $ipp->details_count;
            $tahapanList = $this->buildTahapan($ipp->periode);
            $uploader = $ipp->send_by_username ? ($uploaderMap[$ipp->send_by_username] ?? null) : null;

            if ($isRubric) {
                $allowed = $allowedByIpp[$ippId] ?? [];
                $participants = IppForemanPeserta::participants((string) $ipp->periode, $nik);
                $karyawan = [];
                foreach ($participants as $p) {
                    $pNik = (string) $p->nik;
                    if (!isset($allowed[$pNik])) {
                        continue;
                    }
                    $tahapanInfo = [];
                    foreach ($tahapanList as $tahapan) {
                        $tRows = $rubricNilaiRows->where('nik', $pNik)->where('tahapan', $tahapan);
                        $filled = $tRows->filter(fn ($r) => $r->nilai !== null)
                            ->pluck('hr_ipp_foreman_id')->unique()->count();
                        $tahapanInfo[$tahapan] = [
                            'filled' => $filled,
                            'total' => $detailCount,
                            'approval_associate_manager' => $this->stageStatusOfRows($tRows, 'approval_associate_manager'),
                            'approval_manager' => $this->stageStatusOfRows($tRows, 'approval_manager'),
                            'two_stage' => IppForemanPeserta::hasDistinctAm($p),
                            'diskusi_open' => 0,
                            'diskusi_total' => 0,
                            'reviewed_filled' => 0,
                            'reviewed_total' => $detailCount,
                            'reviewed_by' => null,
                            'reviewed_by_label' => null,
                        ];
                    }

                    $karyawan[] = [
                        'id' => $pNik,
                        'nik_yang_bersangkutan' => $pNik,
                        'nama_yang_bersangkutan' => (string) $p->nama,
                        'my_role' => IppForemanPeserta::role($p, $nik),
                        'has_am' => IppForemanPeserta::hasDistinctAm($p),
                        'tahapan' => $tahapanInfo,
                    ];
                }

                if (!$karyawan) {
                    continue;
                }

                $rows[] = [
                    'ipp_id' => $ipp->id,
                    'periode' => $ipp->periode,
                    'dept' => $ipp->dept,
                    'position' => $ipp->position,
                    'golongan' => $ipp->golongan,
                    'template_type' => $ipp->template_type,
                    'is_rubric' => true,
                    'uploaded_by' => $uploader
                        ? ($uploader['nik'] . ' - ' . $uploader['name'])
                        : ($ipp->send_by_username ?? '-'),
                    'detail_count' => $detailCount,
                    'karyawan' => $karyawan,
                ];
                continue;
            }

            $myOto = $allOtorisasi->where('ipp_id', $ippId)->values();
            $allowed = $allowedByIpp[$ippId] ?? [];
            $karyawan = [];
            foreach ($myOto as $o) {
                if (!isset($allowed[(string) $o->id])) {
                    continue;
                }
                $otoRows = $aktualRows->where('ipp_otorisasi_id', $o->id);

                $tahapanInfo = [];
                foreach ($tahapanList as $tahapan) {
                    $dk = $diskusiMap[$o->id . '|' . $tahapan] ?? ['open' => 0, 'total' => 0];

                    $tRows = $otoRows->where('tahapan', $tahapan);
                    $filled = $tRows->filter(fn ($r) => $r->aktual !== null || $r->aktual_varchar !== null)
                        ->pluck('ipp_detail_id')->unique()->count();
                    $revRows = $tRows->where('review', true);
                    $reviewedFilled = $revRows->count();
                    $reviewedBy = $revRows->pluck('reviewed_by')->first();
                    $tahapanInfo[$tahapan] = [
                        'filled' => $filled,
                        'total' => $detailCount,
                        'approval_associate_manager' => $this->stageStatusOfRows($tRows, 'approval_associate_manager'),
                        'approval_manager' => $this->stageStatusOfRows($tRows, 'approval_manager'),
                        'two_stage' => $this->isTwoStageApproval($ipp->golongan) && $this->hasDistinctAm($o),
                        'diskusi_open' => $dk['open'],
                        'diskusi_total' => $dk['total'],
                        'reviewed_filled' => $reviewedFilled,
                        'reviewed_total' => $detailCount,
                        'reviewed_by' => $reviewedBy,
                        'reviewed_by_label' => $reviewedBy
                            ? ($reviewerMap[$reviewedBy] ?? null
                                ? ($reviewerMap[$reviewedBy]['nik'] . ' - ' . $reviewerMap[$reviewedBy]['name'])
                                : $reviewedBy)
                            : null,
                    ];
                }

                $karyawan[] = [
                    'id' => $o->id,
                    'nik_yang_bersangkutan' => $o->nik_yang_bersangkutan,
                    'nama_yang_bersangkutan' => $o->nama_yang_bersangkutan,
                    'my_role' => $this->roleForOtorisasi($o),
                    'has_am' => $this->hasDistinctAm($o),
                    'tahapan' => $tahapanInfo,
                ];
            }

            if (!$karyawan) {
                continue;
            }

            $rows[] = [
                'ipp_id' => $ipp->id,
                'periode' => $ipp->periode,
                'dept' => $ipp->dept,
                'position' => $ipp->position,
                'golongan' => $ipp->golongan,
                'template_type' => $ipp->template_type,
                'is_rubric' => false,
                'uploaded_by' => $uploader
                    ? ($uploader['nik'] . ' - ' . $uploader['name'])
                    : ($ipp->send_by_username ?? '-'),
                'detail_count' => $detailCount,
                'karyawan' => $karyawan,
            ];
        }

        return response()->json([
            'data' => $rows,
            'meta' => [
                'page' => $page,
                'per_page' => $perPage,
                'total' => $total,
                'last_page' => $lastPage,
            ],
        ]);
    }

    private function resolveApprovers(\Illuminate\Support\Collection $rows): array
    {
        $values = $rows->pluck('approval_by_associate_manager')
            ->merge($rows->pluck('approval_by_manager'))
            ->filter()
            ->unique()
            ->values();
        if ($values->isEmpty()) {
            return [];
        }

        $users = DB::table('users')
            ->where(function ($q) use ($values) {
                $q->whereIn('username', $values->all())
                    ->orWhereIn('nik', $values->all());
            })
            ->get(['username', 'nik', 'name']);

        $map = [];
        foreach ($users as $u) {
            $info = ['nik' => $u->nik, 'name' => $u->name];
            if ($u->username) {
                $map[$u->username] = $info;
            }
            if ($u->nik) {
                $map[$u->nik] = $info;
            }
        }
        return $map;
    }

    public function show($ippId)
    {
        $this->checkAccess();

        $ipp = HrIpp::with(['details.masterTargets'])->findOrFail($ippId);

        if ($ipp->isRubric()) {
            return $this->showRubric($ipp);
        }

        $myOto = $this->myOtorisasi((int) $ippId);
        if (empty($myOto)) {
            abort(403);
        }

        $otoIds = array_map(fn ($o) => $o->id, $myOto);
        $otoById = collect($myOto)->keyBy('id');
        $currentNik = $this->currentNik();

        $aktual = [];
        $aktualRows = HrIppAktual::whereIn('ipp_otorisasi_id', $otoIds)->get();
        foreach ($aktualRows as $a) {
            $row = [
                'aktual' => $a->aktual,
                'aktual_varchar' => $a->aktual_varchar,
                'aktual_atasan' => $a->aktual_atasan,
                'aktual_atasan_varchar' => $a->aktual_atasan_varchar,
                'aktual_atasan_by' => $a->aktual_atasan_by,
                'aktual_departemen' => $a->aktual_departemen,
                'aktual_departemen_varchar' => $a->aktual_departemen_varchar,
                'aktual_departemen_by' => $a->aktual_departemen_by,
                'achievement_category' => $a->achievement_category,
                'achievement_score' => $a->achievement_score !== null ? (float) $a->achievement_score : null,
                'score_scale' => $a->score_scale !== null ? (float) $a->score_scale : null,
                'review' => (bool) $a->review,
                'reviewed_by' => $a->reviewed_by,
                'approval_associate_manager' => $a->approval_associate_manager,
                'approval_manager' => $a->approval_manager,
                'approval_at_associate_manager' => optional($a->approval_at_associate_manager)->format('d M Y H:i'),
                'approval_at_manager' => optional($a->approval_at_manager)->format('d M Y H:i'),
            ];

            // AM tidak dikenai OTP, jadi jangan kirim kolom achievement ke response-nya.
            $oto = $otoById->get($a->ipp_otorisasi_id);
            $isManagerRow = $oto && strcasecmp(trim((string) $oto->nik_manager), $currentNik) === 0;
            if (!$isManagerRow) {
                unset($row['achievement_category'], $row['achievement_score'], $row['score_scale']);
            }

            $aktual[$a->ipp_otorisasi_id][$a->ipp_detail_id][$a->tahapan] = $row;
        }

        $approverMap = $this->resolveApprovers($aktualRows);

        $noteMap = [];
        HrIppApprovalNote::whereIn('ipp_otorisasi_id', $otoIds)
            ->get(['ipp_otorisasi_id', 'tahapan', 'role', 'note'])
            ->each(function ($n) use (&$noteMap) {
                $noteMap[$n->ipp_otorisasi_id][$n->tahapan][$n->role] = $n->note;
            });

        $approvalInfo = [];
        foreach ($aktualRows as $a) {
            $assoc = $a->approval_associate_manager;
            $mgr = $a->approval_manager;
            if (!$assoc && !$mgr) {
                continue;
            }
            $info = ['associate' => null, 'manager' => null];
            if ($assoc) {
                $res = $approverMap[$a->approval_by_associate_manager] ?? null;
                $info['associate'] = [
                    'status' => $assoc,
                    'by_label' => $res
                        ? ($res['nik'] . ' - ' . $res['name'])
                        : ($a->approval_by_associate_manager ?? '-'),
                    'at' => optional($a->approval_at_associate_manager)->format('d M Y H:i'),
                    'note' => $noteMap[$a->ipp_otorisasi_id][$a->tahapan]['associate_manager'] ?? null,
                ];
            }
            if ($mgr) {
                $res = $approverMap[$a->approval_by_manager] ?? null;
                $info['manager'] = [
                    'status' => $mgr,
                    'by_label' => $res
                        ? ($res['nik'] . ' - ' . $res['name'])
                        : ($a->approval_by_manager ?? '-'),
                    'at' => optional($a->approval_at_manager)->format('d M Y H:i'),
                    'note' => $noteMap[$a->ipp_otorisasi_id][$a->tahapan]['manager'] ?? null,
                ];
            }
            $approvalInfo[$a->ipp_otorisasi_id][$a->tahapan] = $info;
        }

        $tahapanList = $this->buildTahapan($ipp->periode);

        return response()->json([
            'success' => true,
            'data' => [
                'ipp_id' => $ipp->id,
                'periode' => $ipp->periode,
                'dept' => $ipp->dept,
                'position' => $ipp->position,
                'golongan' => $ipp->golongan,
                'tahapan' => $tahapanList,
                'locking' => HrPmsLockingIpp::lockedMap($tahapanList),
                'can_edit_locked' => $this->canBypassIppLock(),
                'otorisasi' => array_map(function ($o) {
                    return [
                        'id' => $o->id,
                        'nik_yang_bersangkutan' => $o->nik_yang_bersangkutan,
                        'nama_yang_bersangkutan' => $o->nama_yang_bersangkutan,
                        'nik_manager' => $o->nik_manager,
                        'nama_manager' => $o->nama_manager,
                        'nik_associate_manager' => $o->nik_associate_manager,
                        'nama_associate_manager' => $o->nama_associate_manager,
                        'my_role' => $this->roleForOtorisasi($o),
                    ];
                }, $myOto),
                'details' => $ipp->details->map(function ($d) {
                    $tiers = $d->relationLoaded('masterTargets')
                        ? $d->masterTargets->sortBy('score')
                        : $d->masterTargets()->orderBy('score')->get();
                    return [
                        'id' => $d->id,
                        'no_urut' => $d->no_urut,
                        'objective' => $d->objective,
                        'kpa' => $d->kpa,
                        'kpi' => $d->kpi,
                        'classification' => $d->classification,
                        'uom' => $d->uom,
                        'weight' => $d->weight !== null ? (float) $d->weight : null,
                        'target' => $d->target !== null ? (string) $d->target : null,
                        'due_date' => $d->due_date,
                        'range_tiers' => $tiers->map(fn ($t) => [
                            'score' => (int) $t->score,
                            'match_type' => $t->match_type ?: 'range',
                            'min_value' => $t->min_value !== null ? (float) $t->min_value : null,
                            'max_value' => $t->max_value !== null ? (float) $t->max_value : null,
                            'label' => $t->label,
                        ])->values()->all(),
                    ];
                }),
                'aktual' => $aktual,
                'approval_info' => $approvalInfo,
            ],
        ]);
    }

    /**
     * Detail IPP rubrik Level 2-3: baris rubrik + nilai (Leader Assessment) per peserta per tahapan.
     */
    private function showRubric(HrIpp $ipp)
    {
        $me = $this->currentNik();
        $participants = IppForemanPeserta::participants((string) $ipp->periode, $me);
        if ($participants->isEmpty()) {
            abort(403);
        }

        $rubric = $ipp->foremanRows()->get()->map(function ($r) {
            return [
                'id' => $r->id,
                'no_urut' => $r->no_urut,
                'section' => $r->section,
                'area' => $r->area,
                'competency_behavior' => $r->competency_behavior,
                'key_behavior' => $r->key_behavior,
                'kriteria_1' => $r->kriteria_1,
                'kriteria_2' => $r->kriteria_2,
                'kriteria_3' => $r->kriteria_3,
                'kriteria_4' => $r->kriteria_4,
                'kriteria_5' => $r->kriteria_5,
            ];
        });

        $rowIds = $rubric->pluck('id')->map(fn ($v) => (int) $v)->all();
        $niks = $participants->pluck('nik')->map(fn ($v) => (string) $v)->all();

        $nilaiRows = ($rowIds && $niks)
            ? HrIppForemanNilai::whereIn('hr_ipp_foreman_id', $rowIds)->whereIn('nik', $niks)->get()
            : collect();

        $nilai = [];
        foreach ($nilaiRows as $n) {
            $nilai[(string) $n->nik][$n->hr_ipp_foreman_id][$n->tahapan] = [
                'nilai' => $n->nilai !== null ? (int) $n->nilai : null,
                'filled_by' => $n->filled_by,
                'approval_associate_manager' => $n->approval_associate_manager,
                'approval_manager' => $n->approval_manager,
            ];
        }

        $approverMap = $this->resolveApprovers($nilaiRows);

        $approvalInfo = [];
        foreach ($nilaiRows as $n) {
            $assoc = $n->approval_associate_manager;
            $mgr = $n->approval_manager;
            if (!$assoc && !$mgr) {
                continue;
            }
            $key = (string) $n->nik;
            if (isset($approvalInfo[$key][$n->tahapan])) {
                continue;
            }
            $info = ['associate' => null, 'manager' => null];
            if ($assoc) {
                $res = $approverMap[$n->approval_by_associate_manager] ?? null;
                $info['associate'] = [
                    'status' => $assoc,
                    'by_label' => $res
                        ? ($res['nik'] . ' - ' . $res['name'])
                        : ($n->approval_by_associate_manager ?? '-'),
                    'at' => optional($n->approval_at_associate_manager)->format('d M Y H:i'),
                    'note' => $n->note_associate_manager,
                ];
            }
            if ($mgr) {
                $res = $approverMap[$n->approval_by_manager] ?? null;
                $info['manager'] = [
                    'status' => $mgr,
                    'by_label' => $res
                        ? ($res['nik'] . ' - ' . $res['name'])
                        : ($n->approval_by_manager ?? '-'),
                    'at' => optional($n->approval_at_manager)->format('d M Y H:i'),
                    'note' => $n->note_manager,
                ];
            }
            $approvalInfo[$key][$n->tahapan] = $info;
        }

        $tahapanList = $this->buildTahapan($ipp->periode);

        return response()->json([
            'success' => true,
            'data' => [
                'template_type' => 'rubric',
                'ipp_id' => $ipp->id,
                'periode' => $ipp->periode,
                'dept' => $ipp->dept,
                'position' => $ipp->position,
                'golongan' => $ipp->golongan,
                'tahapan' => $tahapanList,
                'locking' => HrPmsLockingIpp::lockedMap($tahapanList),
                'can_edit_locked' => $this->canBypassIppLock(),
                'otorisasi' => $participants->map(function ($p) use ($me) {
                    return IppForemanPeserta::payload($p, IppForemanPeserta::role($p, $me));
                })->values()->all(),
                'rubric' => $rubric,
                'nilai' => $nilai,
                'approval_info' => $approvalInfo,
            ],
        ]);
    }

    private function saveAktualRubric(HrIpp $ipp, Request $request)
    {
        $validated = $request->validate([
            'values' => 'nullable|array',
            'values.*.nik' => 'required|string|max:50',
            'values.*.hr_ipp_foreman_id' => 'required|integer',
            'values.*.tahapan' => 'required|string|max:20',
            'values.*.nilai' => 'nullable|integer|between:1,5',
            'nik' => 'required|string|max:50',
            'jumlah_bawahan' => 'required|integer|min:0|max:999999',
        ]);

        $nik = trim((string) $validated['nik']);
        $snap = IppForemanPeserta::snapshot((string) $ipp->periode, $nik);
        $role = IppForemanPeserta::role($snap, $this->currentNik());
        if (!$snap || $role === null) {
            abort(422, "Peserta '{$nik}' bukan bawahan Anda pada periode ini.");
        }

        $this->persistRubricValues($ipp, $validated['values'] ?? []);

        PmsEmployeeSnapshot::syncJumlahBawahan(
            (string) $ipp->periode,
            $nik,
            (int) $validated['jumlah_bawahan']
        );

        return response()->json([
            'success' => true,
            'message' => 'Nilai IPP Level 2-3 berhasil disimpan.',
        ]);
    }

    /**
     * Simpan nilai Leader Assessment (1-5) IPP rubrik Level 2-3 per peserta (nik) per baris per tahapan.
     * Peserta & peran atasan dibaca dari hr_pms_employee_snapshot (tanpa hr_ipp_otorisasi).
     */
    private function persistRubricValues(HrIpp $ipp, array $values, ?string $forceNik = null, ?string $forceTahapan = null): void
    {
        $me = $this->currentNik();
        $userName = Auth::user()->name ?? '';
        $rowIds = $ipp->foremanRows()->pluck('id')->map(fn ($v) => (int) $v)->all();
        $allowedTahapan = $this->buildTahapan($ipp->periode);

        DB::transaction(function () use ($values, $rowIds, $allowedTahapan, $ipp, $me, $userName, $forceNik, $forceTahapan) {
            foreach ($values as $item) {
                $nik = trim((string) ($item['nik'] ?? $forceNik ?? ''));
                $tahapan = (string) ($item['tahapan'] ?? $forceTahapan ?? '');
                $rowId = (int) ($item['hr_ipp_foreman_id'] ?? 0);
                $nilaiRaw = $item['nilai'] ?? null;
                $nilaiBaru = ($nilaiRaw === null || $nilaiRaw === '') ? null : (int) $nilaiRaw;

                if ($nik === '') {
                    abort(422, 'NIK peserta tidak dikenali.');
                }
                if (!in_array($rowId, $rowIds, true)) {
                    abort(422, "Baris rubrik ID {$rowId} bukan bagian dari IPP ini.");
                }
                if (!in_array($tahapan, $allowedTahapan, true)) {
                    abort(422, "Tahapan {$tahapan} tidak dikenali.");
                }

                $snap = IppForemanPeserta::snapshot((string) $ipp->periode, $nik);
                $role = IppForemanPeserta::role($snap, $me);
                if (!$snap || $role === null) {
                    abort(422, "Peserta '{$nik}' bukan bawahan Anda pada periode ini.");
                }

                $approverLabel = ($role === 'associate_manager' ? 'Associate Manager: ' : 'Manager: ')
                    . $me . ' - ' . $userName;

                $existing = HrIppForemanNilai::where('hr_ipp_foreman_id', $rowId)
                    ->where('nik', $nik)
                    ->where('tahapan', $tahapan)
                    ->first();

                $nilaiLama = ($existing && $existing->nilai !== null) ? (int) $existing->nilai : null;

                if (!$this->canBypassIppLock() && HrPmsLockingIpp::isLocked($tahapan, 'atasan') && $nilaiLama !== $nilaiBaru) {
                    abort(422, 'Nilai IPP Level 2-3 tahapan ' . $this->tahapanLabel($tahapan)
                        . ' sedang dikunci (LOCKING IPP). Hubungi Admin HR untuk membuka kunci.');
                }

                if ($existing) {
                    $myCol = 'approval_' . $role;
                    if ($existing->approval_manager === 'approved' || $existing->{$myCol} === 'approved') {
                        abort(422, "Tahapan {$tahapan} sudah di-approve, tidak dapat diubah. Batalkan approval terlebih dahulu.");
                    }

                    if ($nilaiLama !== $nilaiBaru) {
                        $existing->update([
                            'nilai' => $nilaiBaru,
                            'filled_by' => $nilaiBaru !== null ? $approverLabel : null,
                            'filled_at' => $nilaiBaru !== null ? now() : null,
                        ]);
                    }
                } elseif ($nilaiBaru !== null) {
                    HrIppForemanNilai::create([
                        'hr_ipp_foreman_id' => $rowId,
                        'nik' => $nik,
                        'nama' => (string) $snap->nama,
                        'tahapan' => $tahapan,
                        'nilai' => $nilaiBaru,
                        'filled_by' => $approverLabel,
                        'filled_at' => now(),
                    ]);
                }
            }
        });
    }

    private function approveRubric(HrIpp $ipp, Request $request)
    {
        $allowedTahapan = $this->buildTahapan($ipp->periode);
        $me = $this->currentNik();

        $validated = $request->validate([
            'nik' => 'required|string|max:50',
            'tahapan' => 'required|string|max:20',
            'action' => 'required|in:approve,reject,reset',
            'note' => 'nullable|string|max:1000',
            'values' => 'nullable|array',
            'values.*.hr_ipp_foreman_id' => 'required_with:values|integer',
            'values.*.nilai' => 'nullable|integer|between:1,5',
            'jumlah_bawahan' => 'nullable|integer|min:0|max:999999',
        ]);

        $nik = trim((string) $validated['nik']);
        $tahapan = $validated['tahapan'];
        $action = $validated['action'];

        if (!in_array($tahapan, $allowedTahapan, true)) {
            return response()->json([
                'success' => false,
                'message' => "Tahapan {$tahapan} tidak dikenali.",
            ], 422);
        }

        $snap = IppForemanPeserta::snapshot((string) $ipp->periode, $nik);
        $role = IppForemanPeserta::role($snap, $me);
        if (!$snap || $role === null) {
            return response()->json([
                'success' => false,
                'message' => 'Peserta tersebut bukan bawahan Anda pada periode ini.',
            ], 422);
        }

        $this->guardLockedApproval($tahapan);

        if ($action !== 'reset' && ($validated['jumlah_bawahan'] ?? null) !== null) {
            PmsEmployeeSnapshot::syncJumlahBawahan((string) $ipp->periode, $nik, (int) $validated['jumlah_bawahan']);
            $snap->jumlah_bawahan = (int) $validated['jumlah_bawahan'];
        }

        if ($action !== 'reset' && $snap->jumlah_bawahan === null) {
            return response()->json([
                'success' => false,
                'message' => 'Isi Jumlah Bawahan Langsung (bawahan peserta) terlebih dahulu sebelum Approve/Reject Tahapan.',
            ], 422);
        }

        if ($action === 'approve' || $action === 'reject') {
            $note = trim((string) ($validated['note'] ?? ''));
            if ($note === '') {
                return response()->json([
                    'success' => false,
                    'message' => 'Note wajib diisi. Jika tidak ada catatan, isi "-".',
                ], 422);
            }
        }

        if ($action !== 'reset' && !empty($validated['values'])) {
            $this->persistRubricValues($ipp, $validated['values'], $nik, $tahapan);
        }

        $rowIds = $ipp->foremanRows()->pluck('id')->map(fn ($v) => (int) $v)->all();

        if (IppForemanPeserta::hasDistinctAm($snap) && $role === 'manager' && $action === 'approve') {
            $assocRows = HrIppForemanNilai::whereIn('hr_ipp_foreman_id', $rowIds)
                ->where('nik', $nik)
                ->where('tahapan', $tahapan)
                ->get();
            if ($this->stageStatusOfRows($assocRows, 'approval_associate_manager') !== 'approved') {
                return response()->json([
                    'success' => false,
                    'message' => 'Tahapan ' . $tahapan . ' belum disetujui Associate Manager. Approval Manager menunggu tahap Associate Manager.',
                ], 422);
            }
        }

        $col = 'approval_' . $role;
        $colBy = 'approval_by_' . $role;
        $colAt = 'approval_at_' . $role;
        $noteCol = 'note_' . $role;

        if ($action === 'reset') {
            HrIppForemanNilai::whereIn('hr_ipp_foreman_id', $rowIds)
                ->where('nik', $nik)
                ->where('tahapan', $tahapan)
                ->update([
                    $col => null,
                    $colBy => null,
                    $colAt => null,
                    $noteCol => null,
                ]);

            $roleLabel = $role === 'manager' ? 'Manager' : 'Associate Manager';

            return response()->json([
                'success' => true,
                'message' => 'Approval ' . $roleLabel . ' tahapan ' . $tahapan . ' dibatalkan.',
            ]);
        }

        $rows = HrIppForemanNilai::whereIn('hr_ipp_foreman_id', $rowIds)
            ->where('nik', $nik)
            ->where('tahapan', $tahapan)
            ->get();

        $statusBefore = $this->stageStatusOfRows($rows, $col);

        if ($action === 'approve') {
            $filledIds = $rows->filter(fn ($r) => $r->nilai !== null)
                ->pluck('hr_ipp_foreman_id')->map(fn ($v) => (int) $v)->all();
            $missing = array_diff($rowIds, $filledIds);
            if (!empty($missing)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Nilai Leader Assessment belum lengkap untuk tahapan ' . $tahapan . '. Isi semua baris rubrik terlebih dahulu.',
                ], 422);
            }
        }

        $approverNik = $this->currentNik();
        $username = Auth::user()->username ?? (Auth::user()->name ?? 'system');
        $approvalBy = $approverNik !== '' ? $approverNik : $username;
        $approval = $action === 'approve' ? 'approved' : 'rejected';
        $message = $action === 'approve'
            ? 'Tahapan ' . $tahapan . ' berhasil disetujui.'
            : 'Tahapan ' . $tahapan . ' ditolak.';

        HrIppForemanNilai::whereIn('hr_ipp_foreman_id', $rowIds)
            ->where('nik', $nik)
            ->where('tahapan', $tahapan)
            ->update([
                $col => $approval,
                $colBy => $approvalBy,
                $colAt => now(),
                $noteCol => trim((string) ($validated['note'] ?? '')),
            ]);

        if ($statusBefore !== $approval) {
            $this->notifyApprovalDecision($ipp, IppForemanPeserta::otoObject($snap), $tahapan, $role, $approval, trim((string) ($validated['note'] ?? '')));
        }

        return response()->json([
            'success' => true,
            'message' => $message,
        ]);
    }

    public function exportPdf($otoId, Request $request)
    {
        $this->checkAccess();

        $oto = HrIppOtorisasi::findOrFail((int) $otoId);
        if ($this->roleForOtorisasi($oto) === null) {
            abort(403);
        }

        $ipp = HrIpp::with('details')->findOrFail($oto->ipp_id);

        $filterTahapan = trim((string) $request->get('tahapan', ''));

        return app(\App\Http\Controllers\HR\IppAktualController::class)
            ->generatePmsPdf($oto, $ipp, $filterTahapan);
    }

    public function exportForemanPdf($ippId, $nik, Request $request)
    {
        $this->checkAccess();

        $ipp = HrIpp::findOrFail((int) $ippId);
        if (!$ipp->isRubric()) {
            abort(404);
        }

        $snap = IppForemanPeserta::snapshot((string) $ipp->periode, (string) $nik);
        $role = IppForemanPeserta::role($snap, $this->currentNik());
        if (!$snap || $role === null) {
            abort(403);
        }

        $filterTahapan = trim((string) $request->get('tahapan', ''));

        return app(\App\Http\Controllers\HR\IppAktualController::class)
            ->generatePmsPdf(IppForemanPeserta::otoObject($snap), $ipp, $filterTahapan);
    }

    public function saveAktual($ippId, Request $request)
    {
        $this->checkAccess();

        $ipp = HrIpp::with('details')->findOrFail($ippId);

        if ($ipp->isRubric()) {
            return $this->saveAktualRubric($ipp, $request);
        }

        $myOto = $this->myOtorisasi((int) $ippId);
        if (empty($myOto)) {
            abort(403);
        }

        $myOtoIds = array_map(fn ($o) => $o->id, $myOto);
        $allowedDetailIds = $ipp->details->pluck('id')->map(fn ($v) => (int) $v)->all();
        $allowedTahapan = $this->buildTahapan($ipp->periode);

        $validated = $request->validate([
            'values' => 'required|array',
            'values.*.otorisasi_id' => 'required|integer',
            'values.*.detail_id' => 'required|integer',
            'values.*.tahapan' => 'required|string|max:20',
            'values.*.aktual_atasan' => 'nullable',
            'values.*.aktual_atasan_varchar' => 'nullable|string|max:100',
        ]);

        $username = Auth::user()->username ?? (Auth::user()->name ?? 'system');
        $approverLabel = $this->approverLabelForOtorisasi($myOto);
        $detailMeta = $this->detailMetaMap($ipp->details);

        DB::transaction(function () use ($validated, $myOtoIds, $allowedDetailIds, $allowedTahapan, $username, $approverLabel, $myOto, $detailMeta) {
            foreach ($validated['values'] as $item) {
                $otoId = (int) $item['otorisasi_id'];
                $detailId = (int) $item['detail_id'];
                $tahapan = $item['tahapan'];
                $classification = $detailMeta[$detailId]['classification'] ?? null;
                $target = $detailMeta[$detailId]['target'] ?? null;
                $tiers = $detailMeta[$detailId]['range_tiers'] ?? null;
                [$aktualAtasan, $aktualAtasanVarchar, $actualForCalc] = $this->normalizeAtasanActual($item, $tiers, $detailMeta[$detailId]['no_urut'] ?? null);
                [$achievement, $achievementScore, $scoreScale] = $this->calcAchievement($classification, $target, $actualForCalc, $tiers);

                if (!in_array($otoId, $myOtoIds, true)) {
                    abort(422, "Otorisasi {$otoId} bukan otorisasi Anda.");
                }
                if (!in_array($detailId, $allowedDetailIds, true)) {
                    abort(422, "Detail ID {$detailId} bukan bagian dari IPP ini.");
                }
                if (!in_array($tahapan, $allowedTahapan, true)) {
                    abort(422, "Tahapan {$tahapan} tidak dikenali.");
                }

                $existing = HrIppAktual::where('ipp_otorisasi_id', $otoId)
                    ->where('ipp_detail_id', $detailId)
                    ->where('tahapan', $tahapan)
                    ->first();

                $this->guardLockedAktualAtasan($tahapan, $existing, $aktualAtasan, $aktualAtasanVarchar);

                $oto = collect($myOto)->firstWhere('id', $otoId);
                $role = $oto ? $this->roleForOtorisasi($oto) : null;
                $myCol = $role ? 'approval_' . $role : null;

                if ($existing && (
                    $existing->approval_manager === 'approved'
                    || ($myCol && $existing->{$myCol} === 'approved')
                )) {
                    abort(422, "Tahapan {$tahapan} sudah di-approve, tidak dapat diubah. Batalkan approval terlebih dahulu.");
                }

                $filled = $aktualAtasan !== null || $aktualAtasanVarchar !== null;

                if ($existing) {
                    $valueChanged = HrPmsLockingIpp::valueChanged(
                        $existing->aktual_atasan,
                        $existing->aktual_atasan_varchar,
                        $aktualAtasan,
                        $aktualAtasanVarchar
                    );
                    if ($valueChanged) {
                        $existing->update([
                            'aktual_atasan' => $aktualAtasan,
                            'aktual_atasan_varchar' => $aktualAtasanVarchar,
                            'aktual_atasan_by' => $filled ? $approverLabel : null,
                            'achievement_category' => $achievement,
                            'achievement_score' => $achievementScore,
                            'score_scale' => $scoreScale,
                        ]);
                    }
                } else {
                    HrIppAktual::create([
                        'ipp_otorisasi_id' => $otoId,
                        'ipp_detail_id' => $detailId,
                        'tahapan' => $tahapan,
                        'aktual_atasan' => $aktualAtasan,
                        'aktual_atasan_varchar' => $aktualAtasanVarchar,
                        'aktual_atasan_by' => $filled ? $approverLabel : null,
                        'achievement_category' => $achievement,
                        'achievement_score' => $achievementScore,
                        'score_scale' => $scoreScale,
                    ]);
                }
            }
        });

        return response()->json([
            'success' => true,
            'message' => 'Data AKTUAL berhasil disimpan.',
        ]);
    }

    private function achievementFor(?string $classification, $target, $actual): ?string
    {
        $class = trim((string) $classification);
        if ($class === '' || $target === null || $target === '' || $actual === null) {
            return null;
        }

        $score = $this->achievementScoreFor($classification, $target, $actual);
        if ($score === null) {
            return null;
        }

        // HIGH IS GOOD & HIGH IS BAD: >= 120% Exceed, 100%-119% Achieve, < 100% Not Achieve
        if (in_array($class, ['High Is Good', 'High Is Bad'], true)) {
            if ($score >= 1.20) {
                return 'Exceed';
            }
            if ($score >= 1.00) {
                return 'Achieve';
            }
            return 'Not Achieve';
        }

        // Min, Max, Standard Score, Range: Capped max 100%, no Exceed (Max Achieve)
        if ($class === 'Range') {
            $range = $this->parseRange((string) $target);
            if ($range === null) {
                return null;
            }
            $a = (float) $actual;
            return ($a >= $range[0] && $a <= $range[1]) ? 'Achieve' : 'Not Achieve';
        }

        $t = (float) $target;
        $a = (float) $actual;

        if ($class === 'Max') {
            return ($a <= $t || $score >= 0.96) ? 'Achieve' : 'Not Achieve';
        }

        // Standard Score: exact match (== target Achieve, selain itu Not Achieve)
        if ($class === 'Standard Score') {
            return $score >= 1.0 ? 'Achieve' : 'Not Achieve';
        }

        // Min
        return ($a >= $t || $score >= 0.96) ? 'Achieve' : 'Not Achieve';
    }

    private function parseRange(string $target): ?array
    {
        $target = trim($target);
        $parts = preg_split('/\s*[-–—;]\s*/', $target);
        if (count($parts) !== 2) {
            return null;
        }
        $min = (float) $parts[0];
        $max = (float) $parts[1];
        if (!is_numeric($parts[0]) || !is_numeric($parts[1]) || $min > $max) {
            return null;
        }
        return [$min, $max];
    }

    private function achievementScoreFor(?string $classification, $target, $actual): ?float
    {
        $class = trim((string) $classification);
        if ($class === '' || $target === null || $target === '' || $actual === null) {
            return null;
        }

        if ($class === 'Range') {
            $range = $this->parseRange((string) $target);
            if ($range === null) {
                return null;
            }
            $a = (float) $actual;
            return ($a >= $range[0] && $a <= $range[1]) ? 1.0 : 0.0;
        }

        $t = (float) $target;
        $a = (float) $actual;

        switch ($class) {
            case 'High Is Good':
                return $t != 0 ? round($a / $t, 4) : null;
            case 'High Is Bad':
                return $a == 0 ? 1.0 : round($t / $a, 4);
            case 'Min':
                return $a >= $t ? 1.0 : ($t != 0 ? round($a / $t, 4) : null);
            case 'Standard Score':
                return abs($a - $t) < 0.0001 ? 1.0 : 0.0;
            case 'Max':
                if ($a <= $t) {
                    return 1.0;
                }
                return $a == 0 ? 1.0 : round($t / $a, 4);
            default:
                return null;
        }
    }

    private function scoreScaleFor(?string $classification, ?string $achievement, $achievementScore): ?float
    {
        $class = trim((string) $classification);
        if ($class === '' || $achievement === null || $achievementScore === null) {
            return null;
        }

        $s = (float) $achievementScore;

        // HIGH IS GOOD & HIGH IS BAD: >=120% = 5, 100%-119% = 4, 80%-99% = 3, 70%-79% = 2, <=69% = 1
        if (in_array($class, ['High Is Good', 'High Is Bad'], true)) {
            if ($s >= 1.20 || $achievement === 'Exceed') {
                return 5.0;
            }
            if ($s >= 1.00 || $achievement === 'Achieve') {
                return 4.0;
            }
            if ($s >= 0.80) {
                return 3.0;
            }
            if ($s >= 0.70) {
                return 2.0;
            }
            return 1.0;
        }

        // Min, Max, Standard Score, Range (Capped): 96%-100% = 5, 91%-95% = 4, 81%-90% = 3, 71%-80% = 2, <=70% = 1
        if ($achievement === 'Achieve' || $s >= 0.96) {
            return 5.0;
        }
        if ($s >= 0.91) {
            return 4.0;
        }
        if ($s >= 0.81) {
            return 3.0;
        }
        if ($s >= 0.71) {
            return 2.0;
        }
        return 1.0;
    }

    private function detailMetaMap(\Illuminate\Support\Collection $details): array
    {
        $map = [];
        $ids = $details->pluck('id')->filter()->all();
        $tiersByDetail = [];
        if (!empty($ids)) {
            $tiers = \App\Models\HR\HrIppMasterTarget::whereIn('ipp_detail_id', $ids)
                ->orderBy('score')->get()->groupBy('ipp_detail_id');
            foreach ($tiers as $detailId => $group) {
                $tiersByDetail[$detailId] = $group->map(fn ($t) => [
                    'score' => (int) $t->score,
                    'match_type' => $t->match_type ?: 'range',
                    'min_value' => $t->min_value !== null ? (float) $t->min_value : null,
                    'max_value' => $t->max_value !== null ? (float) $t->max_value : null,
                    'label' => $t->label,
                ])->all();
            }
        }
        foreach ($details as $d) {
            $map[(int) $d->id] = [
                'classification' => $d->classification,
                'target' => $d->target,
                'weight' => $d->weight,
                'no_urut' => $d->no_urut,
                'range_tiers' => $tiersByDetail[(int) $d->id] ?? null,
            ];
        }
        return $map;
    }

    /**
     * Hitung achievement_category, achievement_score, dan score_scale untuk satu baris.
     * Untuk classification Range dengan 5 tier dinamis (hr_ipp_master_target), skor diambil
     * dari tier yang memuat aktual; jika tidak ada tier, fallback ke logika min-max lama.
     *
     * @return array{0:?string,1:?float,2:?float}
     */
    private function calcAchievement(?string $classification, $target, $actual, ?array $tiers = null): array
    {
        $class = trim((string) $classification);
        if ($class === 'Range' && !empty($tiers)) {
            if (\App\Support\IppRangeScale::isTextTiers($tiers)) {
                if ($actual === null || $actual === '') {
                    return [null, null, null];
                }
                $tier = \App\Support\IppRangeScale::matchTierText($tiers, (string) $actual);
                if ($tier === null) {
                    return [null, null, null];
                }
                $score = (int) $tier['score'];
                return [\App\Support\IppRangeScale::achievementFromScore($score), null, (float) $score];
            }
            if (\App\Support\IppRangeScale::isCustomTiers($tiers)) {
                if ($actual !== null && $actual !== '') {
                    $tier = \App\Support\IppRangeScale::matchTierCustom($tiers, (float) $actual);
                    if ($tier === null) {
                        return [null, null, null];
                    }
                    $score = (int) $tier['score'];
                    return [\App\Support\IppRangeScale::achievementFromScore($score), null, (float) $score];
                }
            }
            if ($actual !== null && $actual !== '') {
                $tier = \App\Support\IppRangeScale::matchTier($tiers, (float) $actual);
                if ($tier === null) {
                    return [null, null, null];
                }
                $score = (int) $tier['score'];
                return [\App\Support\IppRangeScale::achievementFromScore($score), null, (float) $score];
            }
        }

        $achievement = $this->achievementFor($classification, $target, $actual);
        $achievementScore = $this->achievementScoreFor($classification, $target, $actual);
        $scoreScale = $this->scoreScaleFor($classification, $achievement, $achievementScore);
        return [$achievement, $achievementScore, $scoreScale];
    }

    /**
     * Normalisasi input Aktual (Atasan): mode `==` memakai teks (label), selain itu wajib angka.
     *
     * @return array{0:?float,1:?string,2:?string} [angka, teks, nilai untuk perhitungan skor]
     */
    private function normalizeAtasanActual(array $item, ?array $tiers, $kpiNo = null): array
    {
        $kpiSuffix = ($kpiNo === null || $kpiNo === '') ? '' : " KPI #{$kpiNo}";

        if (\App\Support\IppRangeScale::isTextTiers($tiers)) {
            $raw = $item['aktual_atasan_varchar'] ?? null;
            $text = is_string($raw) ? trim($raw) : '';
            if ($text === '') {
                return [null, null, null];
            }
            if (mb_strlen($text) > 100
                || \App\Support\IppRangeScale::matchTierText($tiers ?? [], $text) === null
            ) {
                abort(422, 'Aktual (Atasan)' . $kpiSuffix . ' harus salah satu label Range yang tersedia.');
            }
            return [null, $text, $text];
        }

        $raw = $item['aktual_atasan'] ?? null;
        if ($raw === null || $raw === '') {
            return [null, null, null];
        }
        if (!is_numeric($raw)) {
            abort(422, 'Aktual (Atasan)' . $kpiSuffix . ' harus berupa angka.');
        }
        $num = (float) $raw;
        if ($num > 9999999999) {
            abort(422, 'Aktual (Atasan)' . $kpiSuffix . ' melebihi batas maksimum.');
        }
        $scaleDecimals = \App\Support\IppRangeScale::scaleDecimals($tiers);
        if ($scaleDecimals !== null) {
            $num = round($num, $scaleDecimals);
        }
        if (!\App\Support\IppRangeScale::matchesAnyTier($tiers, $num)) {
            abort(422, 'Aktual (Atasan)' . $kpiSuffix . ' tidak masuk rentang skor mana pun.');
        }
        return [$num, null, $num];
    }

    public function approve($ippId, Request $request)
    {
        $this->checkAccess();

        $ipp = HrIpp::with('details')->findOrFail($ippId);

        if ($ipp->isRubric()) {
            return $this->approveRubric($ipp, $request);
        }

        $myOto = $this->myOtorisasi((int) $ippId);
        if (empty($myOto)) {
            abort(403);
        }

        $myOtoIds = array_map(fn ($o) => $o->id, $myOto);
        $allowedTahapan = $this->buildTahapan($ipp->periode);

        $validated = $request->validate([
            'otorisasi_id' => 'required|integer',
            'tahapan' => 'required|string|max:20',
            'action' => 'required|in:approve,reject,reset',
            'note' => 'nullable|string|max:1000',
            'values' => 'nullable|array',
            'values.*.detail_id' => 'required_with:values|integer',
            'values.*.aktual_atasan' => 'nullable',
            'values.*.aktual_atasan_varchar' => 'nullable|string|max:100',
        ]);

        $otoId = (int) $validated['otorisasi_id'];
        $tahapan = $validated['tahapan'];
        $action = $validated['action'];

        if (!in_array($otoId, $myOtoIds, true)) {
            return response()->json([
                'success' => false,
                'message' => 'Otorisasi tersebut bukan otorisasi Anda.',
            ], 422);
        }
        if (!in_array($tahapan, $allowedTahapan, true)) {
            return response()->json([
                'success' => false,
                'message' => "Tahapan {$tahapan} tidak dikenali.",
            ], 422);
        }

        $oto = collect($myOto)->firstWhere('id', $otoId);
        $role = $oto ? $this->roleForOtorisasi($oto) : null;
        if (!$role) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki peran approval pada otorisasi ini.',
            ], 422);
        }

        $this->guardLockedApproval($tahapan);

        if ($action === 'approve' || $action === 'reject') {
            $note = trim((string) ($validated['note'] ?? ''));
            if ($note === '') {
                return response()->json([
                    'success' => false,
                    'message' => 'Note wajib diisi. Jika tidak ada catatan, isi "-".',
                ], 422);
            }
        }

        $myCol = 'approval_' . $role;
        $allowedDetailIds = $ipp->details->pluck('id')->map(fn ($v) => (int) $v)->all();
        $approverLabel = $this->approverLabelForOtorisasi($myOto);
        $detailMeta = $this->detailMetaMap($ipp->details);

        if ($action !== 'reset' && !empty($validated['values'])) {
            foreach ($validated['values'] as $item) {
                $detailId = (int) $item['detail_id'];
                $classification = $detailMeta[$detailId]['classification'] ?? null;
                $target = $detailMeta[$detailId]['target'] ?? null;
                $tiers = $detailMeta[$detailId]['range_tiers'] ?? null;
                [$aktualAtasan, $aktualAtasanVarchar, $actualForCalc] = $this->normalizeAtasanActual($item, $tiers, $detailMeta[$detailId]['no_urut'] ?? null);
                [$achievement, $achievementScore, $scoreScale] = $this->calcAchievement($classification, $target, $actualForCalc, $tiers);

                if (!in_array($detailId, $allowedDetailIds, true)) {
                    abort(422, "Detail ID {$detailId} bukan bagian dari IPP ini.");
                }

                $existing = HrIppAktual::where('ipp_otorisasi_id', $otoId)
                    ->where('ipp_detail_id', $detailId)
                    ->where('tahapan', $tahapan)
                    ->first();

                $this->guardLockedAktualAtasan($tahapan, $existing, $aktualAtasan, $aktualAtasanVarchar);

                if ($existing && (
                    $existing->approval_manager === 'approved'
                    || $existing->{$myCol} === 'approved'
                )) {
                    abort(422, "Tahapan {$tahapan} sudah di-approve, tidak dapat diubah. Batalkan approval terlebih dahulu.");
                }

                $filled = $aktualAtasan !== null || $aktualAtasanVarchar !== null;

                if ($existing) {
                    $valueChanged = HrPmsLockingIpp::valueChanged(
                        $existing->aktual_atasan,
                        $existing->aktual_atasan_varchar,
                        $aktualAtasan,
                        $aktualAtasanVarchar
                    );
                    if ($valueChanged) {
                        $existing->update([
                            'aktual_atasan' => $aktualAtasan,
                            'aktual_atasan_varchar' => $aktualAtasanVarchar,
                            'aktual_atasan_by' => $filled ? $approverLabel : null,
                            'achievement_category' => $achievement,
                            'achievement_score' => $achievementScore,
                            'score_scale' => $scoreScale,
                        ]);
                    }
                } else {
                    HrIppAktual::create([
                        'ipp_otorisasi_id' => $otoId,
                        'ipp_detail_id' => $detailId,
                        'tahapan' => $tahapan,
                        'aktual_atasan' => $aktualAtasan,
                        'aktual_atasan_varchar' => $aktualAtasanVarchar,
                        'aktual_atasan_by' => $filled ? $approverLabel : null,
                        'achievement_category' => $achievement,
                        'achievement_score' => $achievementScore,
                        'score_scale' => $scoreScale,
                    ]);
                }
            }
        }

        if ($this->hasDistinctAm($oto) && $role === 'manager' && $action === 'approve') {
            $assocRows = HrIppAktual::where('ipp_otorisasi_id', $otoId)
                ->where('tahapan', $tahapan)
                ->get();
            $assocStatus = $this->stageStatusOfRows($assocRows, 'approval_associate_manager');
            if ($assocStatus !== 'approved') {
                return response()->json([
                    'success' => false,
                    'message' => 'Tahapan ' . $tahapan . ' belum disetujui Associate Manager. Approval Manager menunggu tahap Associate Manager.',
                ], 422);
            }
        }

        $rows = HrIppAktual::where('ipp_otorisasi_id', $otoId)
            ->where('tahapan', $tahapan)
            ->get();

        $statusBefore = $this->stageStatusOfRows($rows, $myCol);

        if ($action === 'approve') {
            $detailIds = $ipp->details->pluck('id')->map(fn ($v) => (int) $v)->all();
            $filledIds = $rows->filter(fn ($r) => $r->aktual !== null || $r->aktual_varchar !== null)
                ->pluck('ipp_detail_id')->map(fn ($v) => (int) $v)->all();
            $missing = array_diff($detailIds, $filledIds);
            if (!empty($missing)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Aktual belum lengkap untuk tahapan ' . $tahapan . '. Lengkapi semua KPI terlebih dahulu.',
                ], 422);
            }

            $filledAtasanIds = $rows->filter(fn ($r) => $r->aktual_atasan !== null || $r->aktual_atasan_varchar !== null)
                ->pluck('ipp_detail_id')->map(fn ($v) => (int) $v)->all();
            $missingAtasan = array_diff($detailIds, $filledAtasanIds);
            if (!empty($missingAtasan)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Aktual atasan belum lengkap untuk tahapan ' . $tahapan . '. Isi semua nilai Aktual (Atasan) terlebih dahulu, atau gunakan tombol "Salin dari Aktual Karyawan".',
                ], 422);
            }
        }

        $approverNik = $this->currentNik();
        $username = Auth::user()->username ?? (Auth::user()->name ?? 'system');
        $approvalBy = $approverNik !== '' ? $approverNik : $username;

        $col = 'approval_' . $role;
        $colBy = 'approval_by_' . $role;
        $colAt = 'approval_at_' . $role;

        if ($action === 'reset') {
            HrIppAktual::where('ipp_otorisasi_id', $otoId)
                ->where('tahapan', $tahapan)
                ->update([
                    $col => null,
                    $colBy => null,
                    $colAt => null,
                ]);

            HrIppApprovalNote::where('ipp_otorisasi_id', $otoId)
                ->where('tahapan', $tahapan)
                ->where('role', $role)
                ->delete();

            $roleLabel = $role === 'manager' ? 'Manager' : 'Associate Manager';

            return response()->json([
                'success' => true,
                'message' => 'Approval ' . $roleLabel . ' tahapan ' . $tahapan . ' dibatalkan.',
            ]);
        }

        $approval = $action === 'approve' ? 'approved' : 'rejected';
        $message = $action === 'approve'
            ? 'Tahapan ' . $tahapan . ' berhasil disetujui.'
            : 'Tahapan ' . $tahapan . ' ditolak.';

        if ($action === 'approve') {
            foreach ($rows as $r) {
                $classification = $detailMeta[$r->ipp_detail_id]['classification'] ?? null;
                $target = $detailMeta[$r->ipp_detail_id]['target'] ?? null;
                $tiers = $detailMeta[$r->ipp_detail_id]['range_tiers'] ?? null;
                $actualForCalc = \App\Support\IppRangeScale::isTextTiers($tiers)
                    ? $r->aktual_atasan_varchar
                    : $r->aktual_atasan;
                [$achievement, $achievementScore, $scoreScale] = $this->calcAchievement($classification, $target, $actualForCalc, $tiers);

                $r->update([
                    'achievement_category' => $achievement,
                    'achievement_score' => $achievementScore,
                    'score_scale' => $scoreScale,
                    $col => $approval,
                    $colBy => $approvalBy,
                    $colAt => now(),
                ]);
            }
        } else {
            HrIppAktual::where('ipp_otorisasi_id', $otoId)
                ->where('tahapan', $tahapan)
                ->update([
                    $col => $approval,
                    $colBy => $approvalBy,
                    $colAt => now(),
                ]);
        }

        HrIppApprovalNote::updateOrCreate(
            [
                'ipp_otorisasi_id' => $otoId,
                'tahapan' => $tahapan,
                'role' => $role,
            ],
            ['note' => trim((string) ($validated['note'] ?? ''))]
        );

        if ($statusBefore !== $approval) {
            $this->notifyApprovalDecision($ipp, $oto, $tahapan, $role, $approval, trim((string) ($validated['note'] ?? '')));
        }

        return response()->json([
            'success' => true,
            'message' => $message,
        ]);
    }

    public function diskusiList(Request $request)
    {
        $this->checkAccess();

        $nik = $this->currentNik();
        $perPage = (int) $request->get('per_page', 10);
        if ($perPage < 1) {
            $perPage = 10;
        }
        if ($perPage > 100) {
            $perPage = 100;
        }
        $page = max(1, (int) $request->get('page', 1));

        $meta = ['page' => 1, 'per_page' => $perPage, 'total' => 0, 'last_page' => 1];
        if ($nik === '') {
            return response()->json(['success' => true, 'data' => [], 'meta' => $meta]);
        }

        $q = DB::table('hr_ipp_diskusi as d')
            ->join('hr_ipp_otorisasi as o', 'o.id', '=', 'd.ipp_otorisasi_id')
            ->join('hr_ipp as i', 'i.id', '=', 'o.ipp_id')
            ->leftJoin('hr_ipp_detail as dt', 'dt.id', '=', 'd.ipp_detail_id')
            ->where(function ($qq) use ($nik) {
                $qq->where('o.nik_manager', $nik)
                    ->orWhere('o.nik_associate_manager', $nik);
            })
            ->orderByRaw("CASE WHEN d.status = 'open' THEN 0 ELSE 1 END")
            ->orderByDesc('d.created_at');

        $total = (clone $q)->count();
        $lastPage = max(1, (int) ceil($total / $perPage));
        if ($page > $lastPage) {
            $page = $lastPage;
        }

        $rows = $q->forPage($page, $perPage)->get([
            'd.id',
            'd.ipp_detail_id',
            'd.tahapan',
            'd.pesan',
            'd.jawaban',
            'd.status',
            'd.nama_pengaju',
            'd.nama_penjawab',
            'd.created_at',
            'd.answered_at',
            'o.ipp_id',
            'o.id as otorisasi_id',
            'o.nik_yang_bersangkutan',
            'o.nama_yang_bersangkutan',
            'i.periode',
            'dt.no_urut',
            'dt.kpi',
        ]);

        return response()->json([
            'success' => true,
            'data' => $rows->map(function ($r) {
                return [
                    'id' => (int) $r->id,
                    'ipp_id' => (int) $r->ipp_id,
                    'otorisasi_id' => (int) $r->otorisasi_id,
                    'ipp_detail_id' => $r->ipp_detail_id ? (int) $r->ipp_detail_id : null,
                    'periode' => $r->periode,
                    'nik_karyawan' => $r->nik_yang_bersangkutan,
                    'nama_karyawan' => $r->nama_yang_bersangkutan,
                    'tahapan' => $r->tahapan,
                    'no_urut' => $r->no_urut,
                    'kpi' => $r->kpi,
                    'pesan' => $r->pesan,
                    'jawaban' => $r->jawaban,
                    'status' => $r->status,
                    'nama_pengaju' => $r->nama_pengaju,
                    'nama_penjawab' => $r->nama_penjawab,
                    'created_at' => $r->created_at ? \Carbon\Carbon::parse($r->created_at)->format('d M Y H:i') : '',
                    'answered_at' => $r->answered_at ? \Carbon\Carbon::parse($r->answered_at)->format('d M Y H:i') : '',
                ];
            })->values(),
            'meta' => [
                'page' => $page,
                'per_page' => $perPage,
                'total' => $total,
                'last_page' => $lastPage,
            ],
        ]);
    }

    public function diskusiIndex($ippId)
    {
        $this->checkAccess();

        $ipp = HrIpp::find((int) $ippId);
        if ($ipp && $ipp->isRubric()) {
            return response()->json(['success' => true, 'data' => []]);
        }

        $myOto = $this->myOtorisasi((int) $ippId);
        if (empty($myOto)) {
            abort(403);
        }

        $otoIds = array_map(fn ($o) => $o->id, $myOto);

        $rows = HrIppDiskusi::whereIn('ipp_otorisasi_id', $otoIds)
            ->orderBy('id')
            ->get()
            ->map(fn ($d) => $this->diskusiPayload($d));

        return response()->json(['success' => true, 'data' => $rows]);
    }

    public function diskusiAnswer($id, Request $request)
    {
        $this->checkAccess();

        $diskusi = HrIppDiskusi::findOrFail((int) $id);
        $oto = HrIppOtorisasi::find($diskusi->ipp_otorisasi_id);
        $role = $oto ? $this->roleForOtorisasi($oto) : null;
        if (!$oto || $role === null) {
            abort(403);
        }

        $validated = $request->validate([
            'jawaban' => 'required|string|max:1000',
        ]);

        $statusBefore = $diskusi->status;

        $diskusi->update([
            'jawaban' => trim($validated['jawaban']),
            'nik_penjawab' => $this->currentNik(),
            'nama_penjawab' => Auth::user()->name ?? '',
            'status' => 'answered',
            'answered_at' => now(),
        ]);

        if ($statusBefore !== 'answered') {
            $this->notifyDiskusiDijawab($oto, $diskusi, $role);
        }

        IppDiskusiBroadcast::send('answered', $diskusi);

        return response()->json([
            'success' => true,
            'message' => 'Diskusi dijawab & ditandai sudah dijawab.',
        ]);
    }

    public function diskusiAnswerUpdate($id, Request $request)
    {
        $this->checkAccess();

        $diskusi = HrIppDiskusi::findOrFail((int) $id);
        if ($diskusi->nik_penjawab !== $this->currentNik()) {
            abort(403);
        }

        $validated = $request->validate([
            'jawaban' => 'required|string|max:1000',
        ]);

        $diskusi->update([
            'jawaban' => trim($validated['jawaban']),
            'status' => 'answered',
            'answered_at' => now(),
        ]);

        IppDiskusiBroadcast::send('answered', $diskusi, true);

        return response()->json([
            'success' => true,
            'message' => 'Jawaban diskusi berhasil diperbarui.',
        ]);
    }

    private function notifyDiskusiDijawab(HrIppOtorisasi $oto, $diskusi, string $penjawabRole): void
    {
        if (!HrPmsControlNotifikasi::isActive()) {
            return;
        }

        try {
            $ipp = HrIpp::find($oto->ipp_id);

            $penjawabNik = $this->currentNik();
            $penjawabNama = trim((string) (Auth::user()->name ?? ''));
            if ($penjawabNama === '') {
                $penjawabNama = trim((string) $diskusi->nama_penjawab);
            }

            $pesertaNik = trim((string) $oto->nik_yang_bersangkutan);
            $pesertaNama = trim((string) $oto->nama_yang_bersangkutan);
            $amNik = trim((string) $oto->nik_associate_manager);
            $amNama = trim((string) $oto->nama_associate_manager);
            $managerNik = trim((string) $oto->nik_manager);
            $managerNama = trim((string) $oto->nama_manager);

            $recipients = [];
            if ($pesertaNik !== '' && $pesertaNik !== $penjawabNik) {
                $recipients[$pesertaNik] = [
                    'nik' => $pesertaNik,
                    'nama' => $pesertaNama,
                    'type' => 'peserta',
                    'role' => 'Peserta',
                ];
            }

            if ($penjawabRole === 'associate_manager') {
                if ($managerNik !== '' && $managerNik !== $penjawabNik && !isset($recipients[$managerNik])) {
                    $recipients[$managerNik] = [
                        'nik' => $managerNik,
                        'nama' => $managerNama,
                        'type' => 'atasan',
                        'role' => 'Manager',
                    ];
                }
            } else {
                if ($amNik !== '' && $amNik !== $penjawabNik && !isset($recipients[$amNik])) {
                    $recipients[$amNik] = [
                        'nik' => $amNik,
                        'nama' => $amNama,
                        'type' => 'atasan',
                        'role' => 'Associate Manager',
                    ];
                }
            }

            if (empty($recipients)) {
                return;
            }

            $kpiNo = null;
            $detailId = (int) ($diskusi->ipp_detail_id ?? 0);
            if ($detailId > 0) {
                $kpiNo = DB::table('hr_ipp_detail')->where('id', $detailId)->value('no_urut');
            }

            $tahapan = trim((string) $diskusi->tahapan);

            SendIppDiskusiAnswerNotificationJob::dispatch(
                (string) optional($ipp)->periode,
                $tahapan,
                $tahapan !== '' ? $this->tahapanLabel($tahapan) : '-',
                $kpiNo,
                $pesertaNik,
                $pesertaNama !== '' ? $pesertaNama : $pesertaNik,
                (string) $diskusi->pesan,
                (string) $diskusi->jawaban,
                $penjawabNik,
                $penjawabNama !== '' ? $penjawabNama : $penjawabNik,
                $penjawabRole === 'associate_manager' ? 'Associate Manager' : 'Manager',
                $pesertaNama !== '' ? $pesertaNama : $pesertaNik,
                $amNama !== '' ? $amNama : $amNik,
                $managerNama !== '' ? $managerNama : $managerNik,
                $pesertaNik,
                $amNik,
                $managerNik,
                array_values($recipients)
            );
        } catch (\Throwable $e) {
            Log::error('IPP diskusi answer notification dispatch failed', [
                'diskusi_id' => $diskusi->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    private function diskusiPayload($d)
    {
        return [
            'id' => $d->id,
            'ipp_detail_id' => $d->ipp_detail_id,
            'tahapan' => $d->tahapan,
            'nik_pengaju' => $d->nik_pengaju,
            'nama_pengaju' => $d->nama_pengaju,
            'pesan' => $d->pesan,
            'jawaban' => $d->jawaban,
            'nik_penjawab' => $d->nik_penjawab,
            'nama_penjawab' => $d->nama_penjawab,
            'status' => $d->status,
            'answered_at' => optional($d->answered_at)->format('d M Y H:i'),
            'created_at' => optional($d->created_at)->format('d M Y H:i'),
        ];
    }

    private function isTwoStageApproval(?string $golongan): bool
    {
        $g = trim((string) $golongan);
        return ($g === '4' || $g === '5');
    }

    private function hasDistinctAm($oto): bool
    {
        $am = trim((string) ($oto->nik_associate_manager ?? ''));
        if ($am === '') {
            return false;
        }

        return $am !== trim((string) ($oto->nik_manager ?? ''));
    }
}
