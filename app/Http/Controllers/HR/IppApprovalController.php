<?php

namespace App\Http\Controllers\HR;

use App\Http\Controllers\Controller;
use App\Models\HR\HrIpp;
use App\Models\HR\HrIppAktual;
use App\Models\HR\HrIppDiskusi;
use App\Models\HR\HrIppForeman;
use App\Models\HR\HrIppForemanNilai;
use App\Models\HR\HrIppOtorisasi;
use App\Models\HR\HrPmsEmployeeSnapshot;
use App\Models\HR\HrPmsLockingIpp;
use App\Support\IppForemanPeserta;
use App\Support\IppTahapan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class IppApprovalController extends Controller
{
    private function checkPermission(string $codename): void
    {
        $userCodenames = Auth::user()->getAllPermissionCodenames();
        if (!in_array($codename, $userCodenames, true)) {
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
     * Tolak perubahan kolom Aktual (Departemen) saat tahapan terkunci (LOCKING IPP).
     * Nilai yang tidak berubah tetap boleh disimpan.
     */
    private function guardLockedAktualDepartemen(string $tahapan, ?HrIppAktual $existing, $aktualDepartemen, $aktualDepartemenVarchar): void
    {
        if ($this->canBypassIppLock() || !HrPmsLockingIpp::isLocked($tahapan, 'departemen')) {
            return;
        }

        $changed = $existing
            ? HrPmsLockingIpp::valueChanged($existing->aktual_departemen, $existing->aktual_departemen_varchar, $aktualDepartemen, $aktualDepartemenVarchar)
            : ($aktualDepartemen !== null || $aktualDepartemenVarchar !== null);

        if ($changed) {
            abort(422, 'Aktual (Departemen) tahapan ' . IppTahapan::label($tahapan)
                . ' sedang dikunci (LOCKING IPP). Hubungi Admin HR untuk membuka kunci.');
        }
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

    public function index()
    {
        $this->checkPermission('hr_upload_file_template_ipp');

        return view('hr.ipp-approval.ipp-approval', [
            'userNik' => trim((string) (Auth::user()->nik ?? '')),
            'userName' => Auth::user()->name ?? '',
            'periodeOptions' => $this->periodeOptions(),
        ]);
    }

    /**
     * Daftar periode IPP yang tersedia (urut menurun).
     */
    private function periodeOptions(): array
    {
        $periodes = HrIpp::query()
            ->whereNotNull('periode')
            ->where('periode', '<>', '')
            ->distinct()
            ->pluck('periode')
            ->all();

        usort($periodes, function ($a, $b) {
            return strnatcmp((string) $b, (string) $a);
        });

        return array_values($periodes);
    }

    public function list(Request $request)
    {
        $this->checkPermission('hr_upload_file_template_ipp');

        $search = trim((string) $request->get('search', ''));
        $fPeriode = trim((string) $request->get('periode', ''));
        $perPage = (int) $request->get('per_page', 10);
        $perPage = max(1, min(100, $perPage));
        $page = max(1, (int) $request->get('page', 1));

        $otorisasiQuery = HrIppOtorisasi::query()
            ->whereHas('ipp', function ($q) {
                $q->where('template_type', '!=', 'rubric');
            });

        if ($fPeriode !== '') {
            $otorisasiQuery->whereHas('ipp', function ($q) use ($fPeriode) {
                $q->where('periode', $fPeriode);
            });
        }

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

        $allOto = $otorisasiQuery->orderBy('ipp_id')->orderBy('id')
            ->get(['id', 'ipp_id', 'nik_yang_bersangkutan', 'nama_yang_bersangkutan', 'nik_manager', 'nik_associate_manager']);

        $rubricIpps = $this->rubricIppsForList($fPeriode, $search);

        // Entri pagination = peserta (KPI: baris otorisasi; rubrik: nik peserta), urut per template.
        $entries = [];
        foreach ($allOto as $o) {
            $entries[] = ['ipp_id' => (int) $o->ipp_id, 'key' => (string) $o->id];
        }
        foreach ($rubricIpps as $ipp) {
            foreach ($this->rubricParticipantsForList($ipp, $search) as $p) {
                $entries[] = ['ipp_id' => (int) $ipp->id, 'key' => (string) $p->nik];
            }
        }

        $total = count($entries);
        $lastPage = max(1, (int) ceil($total / $perPage));
        $page = min($page, $lastPage);

        if ($total === 0) {
            return response()->json(['data' => [], 'meta' => [
                'page' => 1, 'per_page' => $perPage, 'total' => 0, 'last_page' => 1,
                'periode' => $fPeriode,
            ]]);
        }

        $pageEntries = array_slice($entries, ($page - 1) * $perPage, $perPage);

        $allowedByIpp = [];
        foreach ($pageEntries as $entry) {
            $allowedByIpp[$entry['ipp_id']][(string) $entry['key']] = true;
        }

        $pageIppIds = collect(array_map(fn ($e) => (int) $e['ipp_id'], $pageEntries))->unique()->values();
        $ippMap = HrIpp::withCount('details')->whereIn('id', $pageIppIds->all())->get()->keyBy('id');

        $pageOto = $allOto->filter(function ($o) use ($allowedByIpp) {
            return isset($allowedByIpp[(int) $o->ipp_id][(string) $o->id]);
        })->values();

        $aktualRows = HrIppAktual::whereIn('ipp_otorisasi_id', $pageOto->pluck('id'))
            ->get(['ipp_otorisasi_id', 'ipp_detail_id', 'tahapan', 'aktual', 'aktual_varchar', 'aktual_departemen', 'aktual_departemen_varchar', 'approval_associate_manager', 'approval_manager', 'review', 'reviewed_by']);

        $diskusiRows = DB::table('hr_ipp_diskusi')
            ->whereIn('ipp_otorisasi_id', $pageOto->pluck('id'))
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

        // Data rubrik Level 2-3 halaman ini: baris rubrik + nilai peserta.
        $rubricPesertaByIpp = [];
        $rubricPesertaNiks = [];
        foreach ($rubricIpps as $ipp) {
            $ippId = (int) $ipp->id;
            if (!$ippMap->has($ippId)) {
                continue;
            }
            $allowed = $allowedByIpp[$ippId] ?? [];
            $peserta = $this->rubricParticipantsForList($ipp, $search)
                ->filter(fn ($p) => isset($allowed[(string) $p->nik]))
                ->values();
            $rubricPesertaByIpp[$ippId] = $peserta;
            foreach ($peserta as $p) {
                $rubricPesertaNiks[] = (string) $p->nik;
            }
        }
        $rubricPesertaNiks = array_values(array_unique($rubricPesertaNiks));

        $rubricNilaiRows = collect();
        $foremanCountMap = collect();
        if (!empty($rubricPesertaByIpp)) {
            $foremanRows = HrIppForeman::whereIn('ipp_id', array_keys($rubricPesertaByIpp))
                ->orderBy('id')
                ->get(['id', 'ipp_id']);
            $foremanCountMap = $foremanRows->groupBy('ipp_id')->map(function ($rows) {
                return $rows->count();
            });

            $foremanIds = $foremanRows->pluck('id')->all();
            if ($foremanIds && $rubricPesertaNiks) {
                $rubricNilaiRows = HrIppForemanNilai::whereIn('hr_ipp_foreman_id', $foremanIds)
                    ->whereIn('nik', $rubricPesertaNiks)
                    ->get(['hr_ipp_foreman_id', 'nik', 'tahapan', 'nilai', 'approval_associate_manager', 'approval_manager']);
            }
        }

        $uploaderMap = $this->resolveUploaders($ippMap->pluck('send_by_username'));
        $reviewerMap = $this->resolveUploaders($aktualRows->pluck('reviewed_by'));

        $rows = [];

        foreach ($pageIppIds as $ippId) {
            $ipp = $ippMap->get($ippId);
            if (!$ipp) {
                continue;
            }

            $tahapanList = $this->buildTahapan($ipp->periode);
            $uploader = $ipp->send_by_username ? ($uploaderMap[$ipp->send_by_username] ?? null) : null;
            $uploadedBy = $uploader
                ? ($uploader['nik'] . ' - ' . $uploader['name'])
                : ($ipp->send_by_username ?? '-');

            if ($ipp->isRubric()) {
                $detailCount = (int) ($foremanCountMap[$ippId] ?? 0);
                $karyawan = [];
                foreach (($rubricPesertaByIpp[$ippId] ?? collect()) as $p) {
                    $pNik = (string) $p->nik;
                    $tahapanInfo = [];
                    foreach ($tahapanList as $tahapan) {
                        $tRows = $rubricNilaiRows->where('nik', $pNik)->where('tahapan', $tahapan);
                        $filled = $tRows->filter(fn ($r) => $r->nilai !== null)
                            ->pluck('hr_ipp_foreman_id')->unique()->count();
                        $tahapanInfo[$tahapan] = [
                            'filled' => $filled,
                            'total' => $detailCount,
                            'dept_filled' => 0,
                            'dept_total' => 0,
                            'approval_associate_manager' => $this->stageStatusOfRows($tRows, 'approval_associate_manager'),
                            'approval_manager' => $this->stageStatusOfRows($tRows, 'approval_manager'),
                            'two_stage' => IppForemanPeserta::hasDistinctAm($p),
                            'diskusi_open' => 0,
                            'diskusi_total' => 0,
                            'reviewed_filled' => 0,
                            'reviewed_total' => 0,
                            'reviewed_by' => null,
                            'reviewed_by_label' => null,
                        ];
                    }

                    $karyawan[] = [
                        'id' => $pNik,
                        'nik_yang_bersangkutan' => $pNik,
                        'nama_yang_bersangkutan' => (string) $p->nama,
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
                    'uploaded_by' => $uploadedBy,
                    'detail_count' => $detailCount,
                    'karyawan' => $karyawan,
                ];
                continue;
            }

            $otoList = $pageOto->where('ipp_id', $ippId)->values();
            if ($otoList->isEmpty()) {
                continue;
            }

            $detailCount = (int) $ipp->details_count;
            $karyawan = [];
            foreach ($otoList as $o) {
                $otoRows = $aktualRows->where('ipp_otorisasi_id', $o->id);

                $tahapanInfo = [];
                foreach ($tahapanList as $tahapan) {
                    $tRows = $otoRows->where('tahapan', $tahapan);
                    $filled = $tRows->filter(fn ($r) => $r->aktual !== null || $r->aktual_varchar !== null)
                        ->pluck('ipp_detail_id')->unique()->count();
                    $deptFilled = $tRows->filter(fn ($r) => $r->aktual_departemen !== null || $r->aktual_departemen_varchar !== null)
                        ->pluck('ipp_detail_id')->unique()->count();
                    $dk = $diskusiMap[$o->id . '|' . $tahapan] ?? ['open' => 0, 'total' => 0];
                    $revRows = $tRows->where('review', true);
                    $reviewedFilled = $revRows->count();
                    $reviewedBy = $revRows->pluck('reviewed_by')->first();
                    $tahapanInfo[$tahapan] = [
                        'filled' => $filled,
                        'total' => $detailCount,
                        'dept_filled' => $deptFilled,
                        'dept_total' => $detailCount,
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
                'uploaded_by' => $uploadedBy,
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
                'periode' => $fPeriode,
            ],
        ]);
    }

    /**
     * IPP rubrik (Level 2-3) yang cocok filter periode & pencarian.
     */
    private function rubricIppsForList(string $fPeriode, string $search)
    {
        $query = HrIpp::where('template_type', 'rubric');
        if ($fPeriode !== '') {
            $query->where('periode', $fPeriode);
        }
        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('periode', 'like', "%{$search}%")
                    ->orWhere('golongan', 'like', "%{$search}%");
            });
        }

        $ipps = $query->orderBy('id')->get()->keyBy('id');

        if ($search !== '') {
            // Pencarian NIK/Nama peserta → ikut sertakan IPP rubrik pada periode terkait.
            $periodes = HrPmsEmployeeSnapshot::where('level_group', '2-3')
                ->where(function ($q) use ($search) {
                    $q->where('nik', 'like', "%{$search}%")
                        ->orWhere('nama', 'like', "%{$search}%");
                })
                ->distinct()
                ->pluck('periode')
                ->filter()
                ->values();
            if ($fPeriode !== '') {
                $periodes = $periodes->filter(fn ($p) => (string) $p === $fPeriode)->values();
            }
            if ($periodes->isNotEmpty()) {
                foreach (HrIpp::where('template_type', 'rubric')->whereIn('periode', $periodes->all())->orderBy('id')->get() as $ipp) {
                    $ipps->put($ipp->id, $ipp);
                }
            }
        }

        return $ipps->sortBy('id')->values();
    }

    /**
     * Peserta rubrik untuk list: semua peserta periode, atau hanya yang cocok
     * NIK/Nama bila pencarian tidak mengenai periode/golongan IPP itu sendiri.
     */
    private function rubricParticipantsForList(HrIpp $ipp, string $search)
    {
        $participants = IppForemanPeserta::participants((string) $ipp->periode);
        if ($search === '') {
            return $participants;
        }

        $ippMatches = mb_stripos((string) $ipp->periode, $search) !== false
            || mb_stripos((string) $ipp->golongan, $search) !== false;
        if ($ippMatches) {
            return $participants;
        }

        return $participants->filter(function ($p) use ($search) {
            return mb_stripos((string) $p->nik, $search) !== false
                || mb_stripos((string) $p->nama, $search) !== false;
        })->values();
    }

    public function show($ippId)
    {
        $this->checkPermission('hr_upload_file_template_ipp');

        $ipp = HrIpp::with(['details.masterTargets'])->findOrFail($ippId);

        if ($ipp->isRubric()) {
            return $this->showRubricHr($ipp);
        }

        $otoList = HrIppOtorisasi::where('ipp_id', (int) $ippId)
            ->orderBy('id')
            ->get()
            ->all();
        if (empty($otoList)) {
            abort(404);
        }

        $otoIds = array_map(fn ($o) => $o->id, $otoList);

        $aktual = [];
        $aktualRows = HrIppAktual::whereIn('ipp_otorisasi_id', $otoIds)->get();
        foreach ($aktualRows as $a) {
            $aktual[$a->ipp_otorisasi_id][$a->ipp_detail_id][$a->tahapan] = [
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
        }

        $approverMap = $this->resolveApprovers($aktualRows);
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
                    ];
                }, $otoList),
                'details' => $ipp->details->map(function ($d) {
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
                        'range_tiers' => $d->masterTargets->sortBy('score')->map(fn ($t) => [
                            'score' => (int) $t->score,
                            'match_type' => $t->match_type ?: 'range',
                            'min_value' => $t->min_value !== null ? (float) $t->min_value : null,
                            'max_value' => $t->max_value !== null ? (float) $t->max_value : null,
                            'label' => $t->label,
                        ])->values()->all(),
                        'due_date' => $d->due_date,
                    ];
                }),
                'aktual' => $aktual,
                'approval_info' => $approvalInfo,
            ],
        ]);
    }

    /**
     * Detail IPP rubrik Level 2-3 untuk Admin HR (read-only): baris rubrik +
     * nilai Leader Assessment semua peserta periode, tanpa isi/simpan.
     */
    private function showRubricHr(HrIpp $ipp)
    {
        $participants = IppForemanPeserta::participants((string) $ipp->periode);

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
        $nilaiRows = $rowIds
            ? HrIppForemanNilai::whereIn('hr_ipp_foreman_id', $rowIds)->get()
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
                'otorisasi' => $participants->map(function ($p) {
                    return IppForemanPeserta::payload($p, null);
                })->values()->all(),
                'rubric' => $rubric,
                'nilai' => $nilai,
                'approval_info' => $approvalInfo,
                'details' => [],
                'aktual' => [],
            ],
        ]);
    }

    public function exportForemanPdf($ippId, $nik, Request $request)
    {
        $this->checkPermission('hr_upload_file_template_ipp');

        $ipp = HrIpp::findOrFail((int) $ippId);
        if (!$ipp->isRubric()) {
            abort(404);
        }

        $snap = IppForemanPeserta::snapshot((string) $ipp->periode, (string) $nik);
        if (!$snap) {
            abort(404);
        }

        $filterTahapan = trim((string) $request->get('tahapan', ''));

        return app(\App\Http\Controllers\HR\IppAktualController::class)
            ->generatePmsPdf(IppForemanPeserta::otoObject($snap), $ipp, $filterTahapan);
    }

    public function exportPdf($otoId, Request $request)
    {
        $this->checkPermission('hr_upload_file_template_ipp');

        $oto = HrIppOtorisasi::findOrFail((int) $otoId);
        $ipp = HrIpp::with('details')->findOrFail($oto->ipp_id);

        if ($ipp->isRubric()) {
            abort(403, 'IPP Level 2-3 (rubrik) tidak melalui tahap departemen.');
        }

        $filterTahapan = trim((string) $request->get('tahapan', ''));

        return app(\App\Http\Controllers\HR\IppAktualController::class)
            ->generatePmsPdf($oto, $ipp, $filterTahapan);
    }

    /**
     * Normalisasi input Aktual (Departemen): mode `==` memakai teks (label), selain itu wajib angka.
     *
     * @param array<int,array>|null $tiers
     * @return array{0:?float,1:?string} [angka, teks]
     */
    private function normalizeDepartemenActual(array $item, ?array $tiers, $kpiNo = null): array
    {
        $kpiSuffix = ($kpiNo === null || $kpiNo === '') ? '' : " KPI #{$kpiNo}";

        if (\App\Support\IppRangeScale::isTextTiers($tiers)) {
            $raw = $item['aktual_departemen_varchar'] ?? null;
            $text = is_string($raw) ? trim($raw) : '';
            if ($text === '') {
                return [null, null];
            }
            if (mb_strlen($text) > 100
                || \App\Support\IppRangeScale::matchTierText($tiers ?? [], $text) === null
            ) {
                abort(422, 'Aktual (Departemen)' . $kpiSuffix . ' harus salah satu label Range yang tersedia.');
            }
            return [null, $text];
        }

        $raw = $item['aktual_departemen'] ?? null;
        if ($raw === null || $raw === '') {
            return [null, null];
        }
        if (!is_numeric($raw)) {
            abort(422, 'Aktual (Departemen)' . $kpiSuffix . ' harus berupa angka.');
        }
        $num = (float) $raw;
        if ($num > 9999999999) {
            abort(422, 'Aktual (Departemen)' . $kpiSuffix . ' melebihi batas maksimum.');
        }
        $scaleDecimals = \App\Support\IppRangeScale::scaleDecimals($tiers);
        if ($scaleDecimals !== null) {
            $num = round($num, $scaleDecimals);
        }
        if (!\App\Support\IppRangeScale::matchesAnyTier($tiers, $num)) {
            abort(422, 'Aktual (Departemen)' . $kpiSuffix . ' tidak masuk rentang skor mana pun.');
        }
        return [$num, null];
    }

    public function saveAktualDepartemen($ippId, Request $request)
    {
        $this->checkPermission('hr_upload_file_template_ipp');

        $ipp = HrIpp::with(['details.masterTargets'])->findOrFail($ippId);

        if ($ipp->isRubric()) {
            abort(403, 'IPP Level 2-3 (rubrik) tidak melalui tahap departemen.');
        }

        $otoIds = HrIppOtorisasi::where('ipp_id', (int) $ippId)->pluck('id')->map(fn ($v) => (int) $v)->all();
        $allowedDetailIds = $ipp->details->pluck('id')->map(fn ($v) => (int) $v)->all();
        $allowedTahapan = $this->buildTahapan($ipp->periode);

        $validated = $request->validate([
            'values' => 'required|array',
            'values.*.otorisasi_id' => 'required|integer',
            'values.*.detail_id' => 'required|integer',
            'values.*.tahapan' => 'required|string|max:20',
            'values.*.aktual_departemen' => 'nullable',
            'values.*.aktual_departemen_varchar' => 'nullable|string|max:100',
        ]);

        $detailTiers = [];
        foreach ($ipp->details as $d) {
            $detailTiers[(int) $d->id] = $d->masterTargets->map(fn ($t) => [
                'score' => (int) $t->score,
                'match_type' => $t->match_type ?: 'range',
                'min_value' => $t->min_value !== null ? (float) $t->min_value : null,
                'max_value' => $t->max_value !== null ? (float) $t->max_value : null,
                'label' => $t->label,
            ])->all();
        }

        $detailNoUrut = [];
        foreach ($ipp->details as $d) {
            $detailNoUrut[(int) $d->id] = $d->no_urut;
        }

        $label = trim((string) (Auth::user()->nik ?? '')) . ' - ' . (Auth::user()->name ?? '');

        DB::transaction(function () use ($validated, $otoIds, $allowedDetailIds, $allowedTahapan, $ipp, $label, $detailTiers, $detailNoUrut) {
            foreach ($validated['values'] as $item) {
                $otoId = (int) $item['otorisasi_id'];
                $detailId = (int) $item['detail_id'];
                $tahapan = $item['tahapan'];
                [$aktualDepartemen, $aktualDepartemenVarchar] = $this->normalizeDepartemenActual($item, $detailTiers[$detailId] ?? null, $detailNoUrut[$detailId] ?? null);

                if (!in_array($otoId, $otoIds, true)) {
                    abort(422, "Otorisasi {$otoId} bukan bagian dari IPP ini.");
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

                $this->guardLockedAktualDepartemen($tahapan, $existing, $aktualDepartemen, $aktualDepartemenVarchar);

                if ($existing && (
                    $existing->approval_manager === 'approved'
                    || ($this->isTwoStageApproval($ipp->golongan) && $existing->approval_associate_manager === 'approved')
                )) {
                    abort(422, "Tahapan {$tahapan} sudah di-approve, tidak dapat diubah. Batalkan approval terlebih dahulu.");
                }

                $filled = $aktualDepartemen !== null || $aktualDepartemenVarchar !== null;

                if ($existing) {
                    $oldValue = $existing->aktual_departemen;
                    $oldVarchar = $existing->aktual_departemen_varchar;
                    $valueChanged = $oldValue !== $aktualDepartemen
                        || (string) $oldVarchar !== (string) $aktualDepartemenVarchar;
                    if ($valueChanged) {
                        $existing->update([
                            'aktual_departemen' => $aktualDepartemen,
                            'aktual_departemen_varchar' => $aktualDepartemenVarchar,
                            'aktual_departemen_by' => $filled ? $label : null,
                        ]);
                    }
                } else {
                    HrIppAktual::create([
                        'ipp_otorisasi_id' => $otoId,
                        'ipp_detail_id' => $detailId,
                        'tahapan' => $tahapan,
                        'aktual_departemen' => $aktualDepartemen,
                        'aktual_departemen_varchar' => $aktualDepartemenVarchar,
                        'aktual_departemen_by' => $filled ? $label : null,
                    ]);
                }
            }
        });

        return response()->json([
            'success' => true,
            'message' => 'Data AKTUAL DEPARTEMEN berhasil disimpan.',
        ]);
    }

    public function diskusiIndex($ippId)
    {
        $this->checkPermission('hr_upload_file_template_ipp');

        $ipp = HrIpp::findOrFail((int) $ippId);
        if ($ipp->isRubric()) {
            abort(403, 'IPP Level 2-3 (rubrik) tidak melalui tahap departemen.');
        }

        $otoIds = HrIppOtorisasi::where('ipp_id', (int) $ippId)->pluck('id')->all();

        $rows = HrIppDiskusi::whereIn('ipp_otorisasi_id', $otoIds)
            ->orderBy('id')
            ->get()
            ->map(fn ($d) => $this->diskusiPayload($d));

        return response()->json(['success' => true, 'data' => $rows]);
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
}