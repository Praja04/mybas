<?php

namespace App\Http\Controllers\HR;

use App\Http\Controllers\Controller;
use App\Jobs\HR\SendIppApprovalRequestJob;
use App\Jobs\HR\SendIppDiskusiNotificationJob;
use App\Models\HR\HrIpp;
use App\Models\HR\HrIppAktual;
use App\Models\HR\HrIppApprovalNote;
use App\Models\HR\HrIppBukti;
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
use App\Support\PmsSelfPageGate;
use Barryvdh\DomPDF\Facade as PDF;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class IppAktualController extends Controller
{
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

    private function currentNik(): ?string
    {
        $nik = Auth::user()->nik ?? null;
        $nik = $nik !== null ? trim((string) $nik) : '';
        return $nik !== '' ? $nik : null;
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
     * Tolak perubahan kolom Aktual (Karyawan) saat tahapan terkunci (LOCKING IPP).
     * Nilai yang tidak berubah tetap boleh disimpan (mis. hanya update review).
     */
    private function guardLockedAktualKaryawan(string $tahapan, ?HrIppAktual $existing, $aktual, $aktualVarchar): void
    {
        if ($this->canBypassIppLock() || !HrPmsLockingIpp::isLocked($tahapan, 'karyawan')) {
            return;
        }

        $changed = $existing
            ? HrPmsLockingIpp::valueChanged($existing->aktual, $existing->aktual_varchar, $aktual, $aktualVarchar)
            : ($aktual !== null || $aktualVarchar !== null);

        if ($changed) {
            abort(422, 'Aktual (Karyawan) tahapan ' . $this->tahapanLabel($tahapan)
                . ' sedang dikunci (LOCKING IPP). Hubungi Admin HR untuk membuka kunci.');
        }
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

    private function filledCounts(int $otorisasiId, array $tahapanList): array
    {
        if (empty($tahapanList)) {
            return [];
        }

        return HrIppAktual::where('ipp_otorisasi_id', $otorisasiId)
            ->whereIn('tahapan', $tahapanList)
            ->where(function ($q) {
                $q->whereNotNull('aktual')->orWhereNotNull('aktual_varchar');
            })
            ->get(['tahapan', 'ipp_detail_id'])
            ->groupBy('tahapan')
            ->map(fn ($rows) => $rows->pluck('ipp_detail_id')->unique()->count())
            ->all();
    }

    /**
     * Normalisasi input Aktual (Karyawan): mode `==` memakai teks (label), selain itu wajib angka.
     *
     * @param array<int,array>|null $tiers
     * @return array{0:?float,1:?string} [angka, teks]
     */
    private function normalizeAktualActual(array $item, ?array $tiers, $kpiNo = null): array
    {
        $kpiSuffix = ($kpiNo === null || $kpiNo === '') ? '' : " KPI #{$kpiNo}";

        if (\App\Support\IppRangeScale::isTextTiers($tiers)) {
            $raw = $item['aktual_varchar'] ?? null;
            $text = is_string($raw) ? trim($raw) : '';
            if ($text === '') {
                return [null, null];
            }
            if (mb_strlen($text) > 100
                || \App\Support\IppRangeScale::matchTierText($tiers ?? [], $text) === null
            ) {
                abort(422, 'Aktual' . $kpiSuffix . ' harus salah satu label Range yang tersedia.');
            }
            return [null, $text];
        }

        $raw = $item['aktual'] ?? null;
        if ($raw === null || $raw === '') {
            return [null, null];
        }
        if (!is_numeric($raw)) {
            abort(422, 'Aktual' . $kpiSuffix . ' harus berupa angka.');
        }
        $num = (float) $raw;
        if ($num > 9999999999) {
            abort(422, 'Aktual' . $kpiSuffix . ' melebihi batas maksimum.');
        }
        $scaleDecimals = \App\Support\IppRangeScale::scaleDecimals($tiers);
        if ($scaleDecimals !== null) {
            $num = round($num, $scaleDecimals);
        }
        if (!\App\Support\IppRangeScale::matchesAnyTier($tiers, $num)) {
            abort(422, 'Aktual' . $kpiSuffix . ' tidak masuk rentang skor mana pun.');
        }
        return [$num, null];
    }

    private function notifyApprover(HrIpp $ipp, HrIppOtorisasi $otorisasi, string $tahapan): void
    {
        if (!HrPmsControlNotifikasi::isActive()) {
            return;
        }

        $assocNik = trim((string) $otorisasi->nik_associate_manager);
        $managerNik = trim((string) $otorisasi->nik_manager);

        if ($assocNik !== '') {
            $recipientNik = $assocNik;
            $recipientNama = (string) $otorisasi->nama_associate_manager;
            $recipientRole = 'Associate Manager';
        } elseif ($managerNik !== '') {
            $recipientNik = $managerNik;
            $recipientNama = (string) $otorisasi->nama_manager;
            $recipientRole = 'Manager';
        } else {
            return;
        }

        SendIppApprovalRequestJob::dispatch(
            $ipp->periode,
            $tahapan,
            $this->tahapanLabel($tahapan),
            (string) $otorisasi->nik_yang_bersangkutan,
            (string) $otorisasi->nama_yang_bersangkutan,
            $recipientNik,
            $recipientNama,
            $recipientRole
        );
    }

    private function otorisasiForUser(int $ippId): HrIppOtorisasi
    {
        $nik = $this->currentNik();
        if (!$nik) {
            abort(403);
        }

        $otorisasi = HrIppOtorisasi::where('ipp_id', $ippId)
            ->where('nik_yang_bersangkutan', $nik)
            ->orderBy('id')
            ->first();

        if (!$otorisasi) {
            abort(403);
        }

        return $otorisasi;
    }

    private function stageStatus(\Illuminate\Support\Collection $rows, string $column): ?string
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

    private function hasDistinctAm($oto): bool
    {
        $am = trim((string) ($oto->nik_associate_manager ?? ''));
        if ($am === '') {
            return false;
        }

        return $am !== trim((string) ($oto->nik_manager ?? ''));
    }

    private function aggregateStatusOfRows(\Illuminate\Support\Collection $rows): ?string
    {
        $mgr = $this->stageStatus($rows, 'approval_manager');
        $assoc = $this->stageStatus($rows, 'approval_associate_manager');

        if ($mgr === 'rejected' || $assoc === 'rejected') {
            return 'rejected';
        }
        if ($mgr === 'approved') {
            return 'approved';
        }
        if ($assoc === 'approved') {
            return 'associate_approved';
        }
        return null;
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
        abort_unless(PmsSelfPageGate::allows(), 403, 'Halaman ini hanya untuk karyawan Group 4-5 dan 6-7.');

        return view('hr.ipp-aktual.ipp-aktual', [
            'userName' => Auth::user()->name ?? '',
            'userNik' => $this->currentNik() ?? '',
        ]);
    }

    public function list()
    {
        $nik = $this->currentNik();
        if (!$nik) {
            return response()->json(['data' => []]);
        }

        $otorisasiRows = HrIppOtorisasi::where('nik_yang_bersangkutan', $nik)
            ->orderBy('id')
            ->get();

        // IPP rubrik 2-3: peserta = baris snapshot saya pada periode tsb.
        $rubricPeriodes = HrPmsEmployeeSnapshot::where('nik', $nik)
            ->where('level_group', '2-3')
            ->distinct()
            ->pluck('periode')
            ->filter()
            ->values();

        $rubricIpps = collect();
        if ($rubricPeriodes->isNotEmpty()) {
            $rubricIpps = HrIpp::where('template_type', 'rubric')
                ->whereIn('periode', $rubricPeriodes->all())
                ->orderBy('id')
                ->get();
        }

        if ($otorisasiRows->isEmpty() && $rubricIpps->isEmpty()) {
            return response()->json(['data' => []]);
        }

        $ippIds = $otorisasiRows->pluck('ipp_id')
            ->merge($rubricIpps->pluck('id'))
            ->unique()
            ->sort()
            ->values();
        $ippMap = HrIpp::with('details')->whereIn('id', $ippIds)->get()->keyBy('id');

        $aktualRows = $otorisasiRows->isEmpty()
            ? collect()
            : HrIppAktual::whereIn('ipp_otorisasi_id', $otorisasiRows->pluck('id'))
                ->get(['ipp_otorisasi_id', 'ipp_detail_id', 'tahapan', 'aktual', 'aktual_varchar', 'approval_associate_manager', 'approval_manager']);

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
            )->where('nik', $nik)->get(['hr_ipp_foreman_id', 'nik', 'tahapan', 'nilai', 'approval_associate_manager', 'approval_manager']);

        $diskusiRows = $otorisasiRows->isEmpty()
            ? collect()
            : DB::table('hr_ipp_diskusi')
                ->whereIn('ipp_otorisasi_id', $otorisasiRows->pluck('id'))
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

        $uploaderMap = $this->resolveUploaders($ippMap->pluck('send_by_username'));

        $rows = [];

        foreach ($ippIds as $ippId) {
            $ipp = $ippMap->get($ippId);
            if (!$ipp) {
                continue;
            }

            $isRubric = $ipp->isRubric();
            $detailCount = $isRubric ? (int) ($foremanCountMap[$ippId] ?? 0) : (int) $ipp->details()->count();
            $tahapanList = $this->buildTahapan($ipp->periode);

            if ($isRubric) {
                $snap = IppForemanPeserta::snapshot((string) $ipp->periode, (string) $nik);
                if (!$snap) {
                    continue;
                }

                $tahapanInfo = [];
                foreach ($tahapanList as $tahapan) {
                    $tRows = $rubricNilaiRows->where('tahapan', $tahapan);
                    $filled = $tRows->filter(fn ($r) => $r->nilai !== null)
                        ->pluck('hr_ipp_foreman_id')->unique()->count();
                    $tahapanInfo[$tahapan] = [
                        'filled' => $filled,
                        'total' => $detailCount,
                        'approval_associate_manager' => $this->stageStatus($tRows, 'approval_associate_manager'),
                        'approval_manager' => $this->stageStatus($tRows, 'approval_manager'),
                        'two_stage' => IppForemanPeserta::hasDistinctAm($snap),
                        'diskusi_open' => 0,
                        'diskusi_total' => 0,
                    ];
                }

                $uploader = $ipp->send_by_username ? ($uploaderMap[$ipp->send_by_username] ?? null) : null;

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
                    'karyawan' => [[
                        'id' => (string) $nik,
                        'nik_yang_bersangkutan' => (string) $nik,
                        'nama_yang_bersangkutan' => (string) $snap->nama,
                        'has_am' => IppForemanPeserta::hasDistinctAm($snap),
                        'tahapan' => $tahapanInfo,
                    ]],
                ];
                continue;
            }

            $myOto = $otorisasiRows->where('ipp_id', $ippId)->values();

            $karyawan = [];
            foreach ($myOto as $o) {
                $tahapanInfo = [];
                foreach ($tahapanList as $tahapan) {
                    $dk = $diskusiMap[$o->id . '|' . $tahapan] ?? ['open' => 0, 'total' => 0];

                    $tRows = $aktualRows->where('ipp_otorisasi_id', $o->id)->where('tahapan', $tahapan);
                    $filled = $tRows->filter(fn ($r) => $r->aktual !== null || $r->aktual_varchar !== null)
                        ->pluck('ipp_detail_id')->unique()->count();
                    $tahapanInfo[$tahapan] = [
                        'filled' => $filled,
                        'total' => $detailCount,
                        'approval_associate_manager' => $this->stageStatus($tRows, 'approval_associate_manager'),
                        'approval_manager' => $this->stageStatus($tRows, 'approval_manager'),
                        'two_stage' => in_array(trim((string) $ipp->golongan), ['4', '5'], true) && $this->hasDistinctAm($o),
                        'diskusi_open' => $dk['open'],
                        'diskusi_total' => $dk['total'],
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

            $uploader = $ipp->send_by_username ? ($uploaderMap[$ipp->send_by_username] ?? null) : null;

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
                'karyawan' => $karyawan,
            ];
        }

        return response()->json(['data' => $rows]);
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

    public function show($ippId)
    {
        $ipp = HrIpp::with(['details.masterTargets'])->findOrFail($ippId);

        if ($ipp->isRubric()) {
            $snap = IppForemanPeserta::snapshot((string) $ipp->periode, (string) $this->currentNik());
            if (!$snap) {
                abort(403);
            }

            return $this->showRubricKaryawan($ipp, $snap);
        }

        $otorisasi = $this->otorisasiForUser((int) $ippId);

        $detailIds = $ipp->details->pluck('id');

        $aktual = [];
        $aktualVarchar = [];
        $aktualAtasan = [];
        $aktualAtasanVarchar = [];
        $aktualAtasanBy = [];
        $aktualDepartemen = [];
        $aktualDepartemenVarchar = [];
        $aktualDepartemenBy = [];
        $achievement = [];
        $achievementScore = [];
        $scoreScale = [];
        $review = [];
        $aktualRows = HrIppAktual::where('ipp_otorisasi_id', $otorisasi->id)->get();

        // Bukti file per detail × tahapan
        $processedTahapanBukti = [];
        foreach ($aktualRows as $a) {
            if ($a->approval_associate_manager !== null || $a->approval_manager !== null) {
                $processedTahapanBukti[$a->tahapan] = true;
            }
        }
        $myNikBukti = $this->currentNik();
        $buktiMap = [];
        $buktiRows = HrIppBukti::where('ipp_otorisasi_id', $otorisasi->id)
            ->orderBy('created_at')
            ->orderBy('id')
            ->get();
        foreach ($buktiRows as $b) {
            $buktiMap[$b->ipp_detail_id][$b->tahapan][] = [
                'id' => $b->id,
                'original_name' => $b->original_name,
                'mime_type' => $b->mime_type,
                'file_size' => $b->file_size,
                'uploaded_by' => $b->uploaded_by,
                'created_at' => optional($b->created_at)->format('d M Y H:i'),
                'view_url' => url('/hr/ipp-bukti/' . $b->id),
                'download_url' => url('/hr/ipp-bukti/' . $b->id . '/download'),
                'can_delete' => !isset($processedTahapanBukti[$b->tahapan]) && $myNikBukti !== null && $b->uploaded_by === $myNikBukti,
            ];
        }

        foreach ($aktualRows as $a) {
            $aktual[$a->ipp_detail_id][$a->tahapan] = $a->aktual;
            $aktualVarchar[$a->ipp_detail_id][$a->tahapan] = $a->aktual_varchar;
            $aktualAtasan[$a->ipp_detail_id][$a->tahapan] = $a->aktual_atasan;
            $aktualAtasanVarchar[$a->ipp_detail_id][$a->tahapan] = $a->aktual_atasan_varchar;
            $aktualAtasanBy[$a->ipp_detail_id][$a->tahapan] = $a->aktual_atasan_by;
            $aktualDepartemen[$a->ipp_detail_id][$a->tahapan] = $a->aktual_departemen;
            $aktualDepartemenVarchar[$a->ipp_detail_id][$a->tahapan] = $a->aktual_departemen_varchar;
            $aktualDepartemenBy[$a->ipp_detail_id][$a->tahapan] = $a->aktual_departemen_by;
            $achievement[$a->ipp_detail_id][$a->tahapan] = $a->achievement_category;
            $achievementScore[$a->ipp_detail_id][$a->tahapan] = $a->achievement_score;
            $scoreScale[$a->ipp_detail_id][$a->tahapan] = $a->score_scale;
            $review[$a->ipp_detail_id][$a->tahapan] = (bool) $a->review;
        }

        $tahapanList = $this->buildTahapan($ipp->periode);

        $approvalStatus = [];
        $approvalInfo = [];
        $approverMap = $this->resolveApprovers($aktualRows);
        foreach ($tahapanList as $tahapan) {
            $tRows = $aktualRows->where('tahapan', $tahapan);
            $approvalStatus[$tahapan] = $this->aggregateStatusOfRows($tRows);

            $info = ['associate' => null, 'manager' => null];
            $assocFirst = $tRows->firstWhere('approval_associate_manager', '!=', null);
            if ($assocFirst && $assocFirst->approval_associate_manager) {
                $res = $approverMap[$assocFirst->approval_by_associate_manager] ?? null;
                $info['associate'] = [
                    'status' => $assocFirst->approval_associate_manager,
                    'by' => $assocFirst->approval_by_associate_manager,
                    'by_label' => $res
                        ? ($res['nik'] . ' - ' . $res['name'])
                        : ($assocFirst->approval_by_associate_manager ?? '-'),
                    'at' => optional($assocFirst->approval_at_associate_manager)->format('d M Y H:i'),
                ];
            }
            $mgrFirst = $tRows->firstWhere('approval_manager', '!=', null);
            if ($mgrFirst && $mgrFirst->approval_manager) {
                $res = $approverMap[$mgrFirst->approval_by_manager] ?? null;
                $info['manager'] = [
                    'status' => $mgrFirst->approval_manager,
                    'by' => $mgrFirst->approval_by_manager,
                    'by_label' => $res
                        ? ($res['nik'] . ' - ' . $res['name'])
                        : ($mgrFirst->approval_by_manager ?? '-'),
                    'at' => optional($mgrFirst->approval_at_manager)->format('d M Y H:i'),
                ];
            }
            $approvalInfo[$tahapan] = $info;
        }

        $snap = PmsEmployeeSnapshot::attributesFor(
            (string) $ipp->periode,
            (string) $otorisasi->nik_yang_bersangkutan
        );

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
                'approval_status' => $approvalStatus,
                'approval_info' => $approvalInfo,
                'otorisasi' => [
                    'id' => $otorisasi->id,
                    'nik_yang_bersangkutan' => $otorisasi->nik_yang_bersangkutan,
                    'nama_yang_bersangkutan' => $otorisasi->nama_yang_bersangkutan,
                    'nik_manager' => $otorisasi->nik_manager,
                    'nama_manager' => $otorisasi->nama_manager,
                    'nik_associate_manager' => $otorisasi->nik_associate_manager,
                    'nama_associate_manager' => $otorisasi->nama_associate_manager,
                    'jumlah_bawahan' => ($snap && $snap->jumlah_bawahan !== null)
                        ? $snap->jumlah_bawahan
                        : $otorisasi->jumlah_bawahan,
                ],
                'details' => $ipp->details->map(function ($d) use ($aktual, $aktualVarchar, $aktualAtasan, $aktualAtasanVarchar, $aktualAtasanBy, $aktualDepartemen, $aktualDepartemenVarchar, $aktualDepartemenBy, $achievement, $achievementScore, $scoreScale, $review, $buktiMap) {
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
                        'aktual' => $aktual[$d->id] ?? [],
                        'aktual_varchar' => $aktualVarchar[$d->id] ?? [],
                        'aktual_atasan' => $aktualAtasan[$d->id] ?? [],
                        'aktual_atasan_varchar' => $aktualAtasanVarchar[$d->id] ?? [],
                        'aktual_atasan_by' => $aktualAtasanBy[$d->id] ?? [],
                        'aktual_departemen' => $aktualDepartemen[$d->id] ?? [],
                        'aktual_departemen_varchar' => $aktualDepartemenVarchar[$d->id] ?? [],
                        'aktual_departemen_by' => $aktualDepartemenBy[$d->id] ?? [],
                        'achievement_category' => $achievement[$d->id] ?? [],
                        'achievement_score' => array_map(fn ($s) => $s !== null ? (float) $s : null, $achievementScore[$d->id] ?? []),
                        'score_scale' => array_map(fn ($s) => $s !== null ? (float) $s : null, $scoreScale[$d->id] ?? []),
                        'review' => array_map(fn ($rv) => (bool) $rv, $review[$d->id] ?? []),
                        'bukti' => $buktiMap[$d->id] ?? [],
                    ];
                }),
            ],
        ]);
    }

    /**
     * Detail rubrik IPP Level 2-3 untuk karyawan (read-only, nilai diisi atasan).
     */
    private function showRubricKaryawan(HrIpp $ipp, $snap)
    {
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

        $nilaiRows = HrIppForemanNilai::whereIn('hr_ipp_foreman_id', $ipp->foremanRows()->pluck('id'))
            ->where('nik', (string) $snap->nik)
            ->get();

        $nilai = [];
        foreach ($nilaiRows as $n) {
            $nilai[$n->hr_ipp_foreman_id][$n->tahapan] = [
                'nilai' => $n->nilai !== null ? (int) $n->nilai : null,
                'filled_by' => $n->filled_by,
            ];
        }

        $tahapanList = $this->buildTahapan($ipp->periode);

        $approvalStatus = [];
        $approvalInfo = [];
        $approverMap = $this->resolveApprovers($nilaiRows);
        foreach ($tahapanList as $tahapan) {
            $tRows = $nilaiRows->where('tahapan', $tahapan);
            $approvalStatus[$tahapan] = $this->aggregateStatusOfRows($tRows);

            $info = ['associate' => null, 'manager' => null];
            $assocFirst = $tRows->firstWhere('approval_associate_manager', '!=', null);
            if ($assocFirst && $assocFirst->approval_associate_manager) {
                $res = $approverMap[$assocFirst->approval_by_associate_manager] ?? null;
                $info['associate'] = [
                    'status' => $assocFirst->approval_associate_manager,
                    'by' => $assocFirst->approval_by_associate_manager,
                    'by_label' => $res
                        ? ($res['nik'] . ' - ' . $res['name'])
                        : ($assocFirst->approval_by_associate_manager ?? '-'),
                    'at' => optional($assocFirst->approval_at_associate_manager)->format('d M Y H:i'),
                ];
            }
            $mgrFirst = $tRows->firstWhere('approval_manager', '!=', null);
            if ($mgrFirst && $mgrFirst->approval_manager) {
                $res = $approverMap[$mgrFirst->approval_by_manager] ?? null;
                $info['manager'] = [
                    'status' => $mgrFirst->approval_manager,
                    'by' => $mgrFirst->approval_by_manager,
                    'by_label' => $res
                        ? ($res['nik'] . ' - ' . $res['name'])
                        : ($mgrFirst->approval_by_manager ?? '-'),
                    'at' => optional($mgrFirst->approval_at_manager)->format('d M Y H:i'),
                ];
            }
            $approvalInfo[$tahapan] = $info;
        }

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
                'approval_status' => $approvalStatus,
                'approval_info' => $approvalInfo,
                'otorisasi' => [
                    'id' => null,
                    'nik_yang_bersangkutan' => (string) $snap->nik,
                    'nama_yang_bersangkutan' => (string) $snap->nama,
                    'nik_manager' => $snap->manager_nik,
                    'nama_manager' => $snap->manager_nama,
                    'nik_associate_manager' => $snap->associate_manager_nik,
                    'nama_associate_manager' => $snap->associate_manager_nama,
                    'jumlah_bawahan' => $snap->jumlah_bawahan,
                ],
                'rubric' => $rubric,
                'nilai' => $nilai,
            ],
        ]);
    }

    public function save($ippId, Request $request)
    {
        $ipp = HrIpp::with(['details.masterTargets'])->findOrFail($ippId);

        if ($ipp->isRubric()) {
            abort(422, 'IPP Level 2-3 (rubrik) diisi oleh atasan (Leader Assessment), tidak dapat diisi karyawan.');
        }

        $otorisasi = $this->otorisasiForUser((int) $ippId);

        $allowedDetailIds = $ipp->details->pluck('id')->map(fn ($v) => (int) $v)->all();
        $allowedTahapan = $this->buildTahapan($ipp->periode);

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

        $validated = $request->validate([
            'values' => 'required|array',
            'values.*.detail_id' => 'required|integer',
            'values.*.tahapan' => 'required|string|max:20',
            'values.*.aktual' => 'nullable',
            'values.*.aktual_varchar' => 'nullable|string|max:100',
            'values.*.review' => 'nullable|boolean',
            'jumlah_bawahan' => 'nullable|integer|min:0|max:999999',
        ]);

        $totalDetails = $ipp->details->count();
        $tahapanDiPayload = collect($validated['values'])->pluck('tahapan')->unique()->values()->all();
        $filledBefore = $totalDetails > 0 ? $this->filledCounts($otorisasi->id, $tahapanDiPayload) : [];

        $username = Auth::user()->username ?? (Auth::user()->name ?? 'system');
        $jumlahBawahan = ($request->input('jumlah_bawahan') === null || $request->input('jumlah_bawahan') === '')
            ? null
            : (int) $request->input('jumlah_bawahan');

        $processedTahapan = HrIppAktual::where('ipp_otorisasi_id', $otorisasi->id)
            ->where(function ($q) {
                $q->whereNotNull('approval_associate_manager')
                    ->orWhereNotNull('approval_manager');
            })
            ->pluck('tahapan')
            ->unique()
            ->values();

        $reviewedDetailIds = HrIppAktual::where('ipp_otorisasi_id', $otorisasi->id)
            ->where('review', true)
            ->pluck('ipp_detail_id')
            ->map(fn ($v) => (int) $v)
            ->unique()
            ->values()
            ->all();

        DB::transaction(function () use ($validated, $otorisasi, $allowedDetailIds, $allowedTahapan, $username, $processedTahapan, $reviewedDetailIds, $jumlahBawahan, $ipp, $detailTiers, $detailNoUrut) {
            HrIppOtorisasi::where('id', $otorisasi->id)->update(['jumlah_bawahan' => $jumlahBawahan]);

            PmsEmployeeSnapshot::capture((string) $ipp->periode, (string) $otorisasi->nik_yang_bersangkutan, [
                'jumlah_bawahan' => $jumlahBawahan,
                'source' => PmsEmployeeSnapshot::SOURCE_IPP,
            ]);

            PmsEmployeeSnapshot::syncJumlahBawahan(
                (string) $ipp->periode,
                (string) $otorisasi->nik_yang_bersangkutan,
                $jumlahBawahan
            );

            foreach ($validated['values'] as $item) {
                $detailId = (int) $item['detail_id'];
                $tahapan = $item['tahapan'];
                [$aktual, $aktualVarchar] = $this->normalizeAktualActual($item, $detailTiers[$detailId] ?? null, $detailNoUrut[$detailId] ?? null);
                $review = !empty($item['review']) || in_array($detailId, $reviewedDetailIds, true);

                if (!in_array($detailId, $allowedDetailIds, true)) {
                    abort(422, "Detail ID {$detailId} bukan bagian dari IPP ini.");
                }

                if (!in_array($tahapan, $allowedTahapan, true)) {
                    abort(422, "Tahapan {$tahapan} tidak dikenali.");
                }

                if ($processedTahapan->contains($tahapan)) {
                    abort(422, "Tahapan {$tahapan} sudah disetujui/ditolak, tidak dapat diubah.");
                }

                $existing = HrIppAktual::where('ipp_otorisasi_id', $otorisasi->id)
                    ->where('ipp_detail_id', $detailId)
                    ->where('tahapan', $tahapan)
                    ->first();

                $this->guardLockedAktualKaryawan($tahapan, $existing, $aktual, $aktualVarchar);

                if ($aktual === null && $aktualVarchar === null) {
                    if ($existing && !$existing->review) {
                        $existing->delete();
                    }
                    continue;
                }

                $reviewFields = $review
                    ? [
                        'review' => true,
                        'reviewed_by' => $username,
                        'reviewed_at' => now(),
                    ]
                    : [
                        'review' => false,
                        'reviewed_by' => null,
                        'reviewed_at' => null,
                    ];

                if ($existing) {
                    $existing->update(array_merge([
                        'aktual' => $aktual,
                        'aktual_varchar' => $aktualVarchar,
                        'send_by_username' => $username,
                    ], $reviewFields));
                } else {
                    HrIppAktual::create(array_merge([
                        'ipp_otorisasi_id' => $otorisasi->id,
                        'ipp_detail_id' => $detailId,
                        'tahapan' => $tahapan,
                        'aktual' => $aktual,
                        'aktual_varchar' => $aktualVarchar,
                        'send_by_username' => $username,
                    ], $reviewFields));
                }
            }
        });

        if ($totalDetails > 0) {
            $filledAfter = $this->filledCounts($otorisasi->id, $tahapanDiPayload);
            foreach ($tahapanDiPayload as $tahapan) {
                if ((int) ($filledAfter[$tahapan] ?? 0) < $totalDetails) {
                    continue;
                }
                if ((int) ($filledBefore[$tahapan] ?? 0) >= $totalDetails) {
                    continue;
                }
                $this->notifyApprover($ipp, $otorisasi, $tahapan);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Data AKTUAL berhasil disimpan.',
        ]);
    }

    public function review($ippId, Request $request)
    {
        $ipp = HrIpp::with('details')->findOrFail($ippId);

        if ($ipp->isRubric()) {
            abort(422, 'IPP Level 2-3 (rubrik) tidak memerlukan review karyawan.');
        }

        $otorisasi = $this->otorisasiForUser((int) $ippId);

        $validated = $request->validate([
            'detail_id' => 'required|integer',
            'tahapan' => 'required|string|max:20',
        ]);

        $detailId = (int) $validated['detail_id'];
        $tahapan = $validated['tahapan'];

        $allowedDetailIds = $ipp->details->pluck('id')->map(fn ($v) => (int) $v)->all();
        if (!in_array($detailId, $allowedDetailIds, true)) {
            abort(422, "Detail ID {$detailId} bukan bagian dari IPP ini.");
        }

        $allowedTahapan = $this->buildTahapan($ipp->periode);
        if (!in_array($tahapan, $allowedTahapan, true)) {
            abort(422, "Tahapan {$tahapan} tidak dikenali.");
        }

        $processedTahapan = HrIppAktual::where('ipp_otorisasi_id', $otorisasi->id)
            ->where(function ($q) {
                $q->whereNotNull('approval_associate_manager')
                    ->orWhereNotNull('approval_manager');
            })
            ->pluck('tahapan')
            ->unique()
            ->values();
        if ($processedTahapan->contains($tahapan)) {
            abort(422, "Tahapan {$tahapan} sudah disetujui/ditolak, tidak dapat diubah.");
        }

        $username = Auth::user()->username ?? (Auth::user()->name ?? 'system');

        DB::transaction(function () use ($otorisasi, $detailId, $allowedTahapan, $username) {
            foreach ($allowedTahapan as $t) {
                $row = HrIppAktual::firstOrNew([
                    'ipp_otorisasi_id' => $otorisasi->id,
                    'ipp_detail_id' => $detailId,
                    'tahapan' => $t,
                ]);

                if ($row->exists && $row->review) {
                    continue;
                }

                $row->review = true;
                $row->reviewed_by = $username;
                $row->reviewed_at = now();
                if (!$row->exists) {
                    $row->send_by_username = $username;
                }
                $row->save();
            }
        });

        return response()->json([
            'success' => true,
            'message' => 'Review tersimpan. Anda sudah memahami IPP ini.',
            'review' => array_fill_keys($allowedTahapan, true),
        ]);
    }

    public function export($ippId, Request $request)
    {
        $ipp = HrIpp::with('details')->findOrFail($ippId);

        if ($ipp->isRubric()) {
            abort(422, 'Export Excel IPP Level 2-3 belum tersedia. Gunakan export PDF.');
        }

        $otorisasi = $this->otorisasiForUser((int) $ippId);

        $tahapanList = $this->buildTahapan($ipp->periode);
        $filterTahapan = trim((string) $request->get('tahapan', ''));
        if ($filterTahapan !== '' && !in_array($filterTahapan, $tahapanList, true)) {
            abort(422, 'Tahapan tidak dikenali.');
        }

        $details = $ipp->details->sortBy('no_urut')->values();
        if ($details->isEmpty()) {
            abort(422, 'IPP tidak memiliki detail KPI.');
        }

        $aktualRows = HrIppAktual::where('ipp_otorisasi_id', $otorisasi->id)->get();
        $aktualMap = [];
        foreach ($aktualRows as $a) {
            $aktualMap[$a->ipp_detail_id][$a->tahapan] = $a;
        }

        $master = DB::table('hr_master_employee')
            ->where('NIK', $otorisasi->nik_yang_bersangkutan)
            ->first();

        $spreadsheet = $this->buildPmsSpreadsheet($details, $aktualMap, $tahapanList, $filterTahapan, $otorisasi, $ipp, $master);

        $writer = new Xlsx($spreadsheet);
        $writer->setPreCalculateFormulas(false);

        $fileName = $this->pmsFileName($ipp, $otorisasi, $filterTahapan, 'xlsx');

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $fileName, ['Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet']);
    }

    public function exportPdf($ippId, Request $request)
    {
        $ipp = HrIpp::with('details')->findOrFail($ippId);

        $filterTahapan = trim((string) $request->get('tahapan', ''));

        if ($ipp->isRubric()) {
            $snap = IppForemanPeserta::snapshot((string) $ipp->periode, (string) $this->currentNik());
            if (!$snap) {
                abort(403);
            }

            return $this->generatePmsPdf(IppForemanPeserta::otoObject($snap), $ipp, $filterTahapan);
        }

        $otorisasi = $this->otorisasiForUser((int) $ippId);

        return $this->generatePmsPdf($otorisasi, $ipp, $filterTahapan);
    }

    public function generatePmsPdf($otorisasi, $ipp, $filterTahapan)
    {
        if ($ipp->isRubric()) {
            $data = $this->pmsPdfDataRubric($otorisasi, $ipp, $filterTahapan);

            $pdf = PDF::loadView('hr.ipp-aktual.export-pdf-foreman', $data)->setPaper('A4', 'landscape');

            return $pdf->download($this->pmsFileName($ipp, $otorisasi, $filterTahapan, 'pdf'));
        }

        $data = $this->pmsPdfData($otorisasi, $ipp, $filterTahapan);

        $pdf = PDF::loadView('hr.ipp-aktual.export-pdf', $data)->setPaper('A4', 'landscape');

        return $pdf->download($this->pmsFileName($ipp, $otorisasi, $filterTahapan, 'pdf'));
    }

    /**
     * Data PDF IPP Level 2-3 (rubrik): baris rubrik + nilai Leader Assessment per tahapan.
     */
    private function pmsPdfDataRubric($otorisasi, $ipp, $filterTahapan): array
    {
        $filterTahapan = trim((string) $filterTahapan);

        $tahapanList = $this->buildTahapan($ipp->periode);
        if ($filterTahapan !== '' && !in_array($filterTahapan, $tahapanList, true)) {
            abort(422, 'Tahapan tidak dikenali.');
        }

        $rubric = $ipp->foremanRows()->get();
        if ($rubric->isEmpty()) {
            abort(422, 'IPP Level 2-3 tidak memiliki baris rubrik.');
        }

        $nilaiRows = HrIppForemanNilai::whereIn('hr_ipp_foreman_id', $ipp->foremanRows()->pluck('id'))
            ->where('nik', (string) $otorisasi->nik_yang_bersangkutan)
            ->get();
        $nilaiMap = [];
        foreach ($nilaiRows as $n) {
            $nilaiMap[$n->hr_ipp_foreman_id][$n->tahapan] = $n->nilai !== null ? (int) $n->nilai : null;
        }

        $midT = null;
        $endT = null;
        foreach ($tahapanList as $t) {
            if (strpos($t, 'MID ') === 0 && $midT === null) {
                $midT = $t;
            }
            if (strpos($t, 'END ') === 0 && $endT === null) {
                $endT = $t;
            }
        }

        $fillMid = $filterTahapan === '' || $filterTahapan === $midT;
        $fillEnd = $filterTahapan === '' || $filterTahapan === $endT;

        $rows = [];
        $sumMid = 0;
        $countMid = 0;
        $sumEnd = 0;
        $countEnd = 0;

        foreach ($rubric as $r) {
            $mid = $fillMid ? ($nilaiMap[$r->id][$midT] ?? null) : null;
            $end = $fillEnd ? ($nilaiMap[$r->id][$endT] ?? null) : null;

            if ($mid !== null) {
                $sumMid += $mid;
                $countMid++;
            }
            if ($end !== null) {
                $sumEnd += $end;
                $countEnd++;
            }

            $rows[] = [
                'no_urut' => $r->no_urut,
                'section' => $r->section,
                'area' => $r->area ?? '',
                'competency_behavior' => $r->competency_behavior ?? '',
                'key_behavior' => $r->key_behavior ?? '',
                'kriteria_1' => $r->kriteria_1 ?? '',
                'kriteria_2' => $r->kriteria_2 ?? '',
                'kriteria_3' => $r->kriteria_3 ?? '',
                'kriteria_4' => $r->kriteria_4 ?? '',
                'kriteria_5' => $r->kriteria_5 ?? '',
                'mid_nilai' => $mid,
                'end_nilai' => $end,
            ];
        }

        $totalMidScore = $countMid > 0 ? round($sumMid / $countMid, 2) : null;
        $totalEndScore = $countEnd > 0 ? round($sumEnd / $countEnd, 2) : null;
        $midPa = $totalMidScore !== null ? $this->paGradeFor($totalMidScore) : null;
        $endPa = $totalEndScore !== null ? $this->paGradeFor($totalEndScore) : null;

        $master = DB::table('hr_master_employee')
            ->where('NIK', $otorisasi->nik_yang_bersangkutan)
            ->first();

        $header = $this->pmsHeaderData($otorisasi, $ipp, $master);

        $notes = [];
        HrIppApprovalNote::where('ipp_otorisasi_id', $otorisasi->id)
            ->get(['tahapan', 'role', 'note'])
            ->each(function ($n) use (&$notes) {
                if ($n->note !== null) {
                    $notes[$n->tahapan][$n->role] = $n->note;
                }
            });

        $currentNik = trim((string) (Auth::user()->nik ?? ''));
        $isManager = $currentNik !== '' && $currentNik === trim((string) ($otorisasi->nik_manager ?? ''));
        $associateManagerNik = trim((string) ($otorisasi->nik_associate_manager ?? ''));
        $isAssociateManager = $currentNik !== '' && $associateManagerNik !== '' && $currentNik === $associateManagerNik;
        $currentLevel = null;
        if ($currentNik !== '') {
            $lv = DB::table('hr_master_employee')->where('NIK', $currentNik)->value('Level');
            $currentLevel = $lv === null ? null : (int) $lv;
        }
        $isAdmin = in_array('hr_upload_file_template_ipp', Auth::user()->getAllPermissionCodenames(), true);
        $showScore = $isManager || $isAssociateManager || ($currentLevel !== null && $currentLevel >= 8) || $isAdmin;
        $showTotal = $isManager || ($currentLevel !== null && $currentLevel >= 8) || $isAdmin;

        $data = compact(
            'header',
            'rows',
            'totalMidScore',
            'totalEndScore',
            'midPa',
            'endPa',
            'fillMid',
            'fillEnd',
            'showScore',
            'showTotal',
            'notes',
            'midT',
            'endT'
        );
        $data['logoSrc'] = $this->pmsLogoDataUri('hr_ipp/Logo_pas.png');
        $data['logoWingsSrc'] = $this->pmsLogoDataUri('hr_ipp/Logo_wings.png');
        $data['approval'] = $this->approvalStatusData($nilaiRows, $otorisasi, $tahapanList, $filterTahapan);

        return $data;
    }

    private function pmsPdfData($otorisasi, $ipp, $filterTahapan): array
    {
        $filterTahapan = trim((string) $filterTahapan);

        $tahapanList = $this->buildTahapan($ipp->periode);
        if ($filterTahapan !== '' && !in_array($filterTahapan, $tahapanList, true)) {
            abort(422, 'Tahapan tidak dikenali.');
        }

        $details = $ipp->details->loadMissing('masterTargets')->sortBy('no_urut')->values();
        if ($details->isEmpty()) {
            abort(422, 'IPP tidak memiliki detail KPI.');
        }

        $aktualRows = HrIppAktual::where('ipp_otorisasi_id', $otorisasi->id)->get();
        $aktualMap = [];
        foreach ($aktualRows as $a) {
            $aktualMap[$a->ipp_detail_id][$a->tahapan] = $a;
        }

        $master = DB::table('hr_master_employee')
            ->where('NIK', $otorisasi->nik_yang_bersangkutan)
            ->first();

        $midT = null;
        $endT = null;
        foreach ($tahapanList as $t) {
            if (strpos($t, 'MID ') === 0 && $midT === null) {
                $midT = $t;
            }
            if (strpos($t, 'END ') === 0 && $endT === null) {
                $endT = $t;
            }
        }

        $fillMid = $filterTahapan === '' || $filterTahapan === $midT;
        $fillEnd = $filterTahapan === '' || $filterTahapan === $endT;

        $rows = [];
        $totalWeight = 0.0;
        $totalMidScore = 0.0;
        $totalEndScore = 0.0;
        $countMidScale = 0;
        $countEndScale = 0;

        foreach ($details as $d) {
            $mid = $aktualMap[$d->id][$midT] ?? null;
            $end = $aktualMap[$d->id][$endT] ?? null;

            $weight = $d->weight !== null && $d->weight !== '' ? (float) $d->weight : null;
            if ($weight !== null) {
                $totalWeight += $weight;
            }

            $midActual = $fillMid && $mid && $mid->aktual_atasan !== null ? $mid->aktual_atasan : null;
            $midAchievement = $fillMid && $mid && $mid->achievement_category ? $mid->achievement_category : null;
            $midAchievementScore = $fillMid && $mid && $mid->achievement_score !== null ? $mid->achievement_score : null;
            $midScoreScale = $fillMid && $mid && $mid->score_scale !== null ? $mid->score_scale : null;

            $midDepartemen = $fillMid && $mid && $mid->aktual_departemen !== null ? $mid->aktual_departemen : null;
            $midKaryawan = $fillMid && $mid && $mid->aktual !== null ? $mid->aktual : null;

            $endActual = $fillEnd && $end && $end->aktual_atasan !== null ? $end->aktual_atasan : null;
            $endAchievement = $fillEnd && $end && $end->achievement_category ? $end->achievement_category : null;
            $endAchievementScore = $fillEnd && $end && $end->achievement_score !== null ? $end->achievement_score : null;
            $endScoreScale = $fillEnd && $end && $end->score_scale !== null ? $end->score_scale : null;
            $endDepartemen = $fillEnd && $end && $end->aktual_departemen !== null ? $end->aktual_departemen : null;
            $endKaryawan = $fillEnd && $end && $end->aktual !== null ? $end->aktual : null;

            $pickText = fn ($varchar, $numeric) => ($varchar !== null && $varchar !== '') ? $varchar : $numeric;
            $midActualText = $pickText($mid ? $mid->aktual_atasan_varchar : null, $midActual);
            $midDepartemenText = $pickText($mid ? $mid->aktual_departemen_varchar : null, $midDepartemen);
            $midKaryawanText = $pickText($mid ? $mid->aktual_varchar : null, $midKaryawan);
            $endActualText = $pickText($end ? $end->aktual_atasan_varchar : null, $endActual);
            $endDepartemenText = $pickText($end ? $end->aktual_departemen_varchar : null, $endDepartemen);
            $endKaryawanText = $pickText($end ? $end->aktual_varchar : null, $endKaryawan);

            if ($midScoreScale !== null) {
                $totalMidScore += $midScoreScale;
                $countMidScale++;
            }
            if ($endScoreScale !== null) {
                $totalEndScore += $endScoreScale;
                $countEndScale++;
            }

            $rows[] = [
                'no_urut' => $d->no_urut,
                'objective' => $d->objective ?? '',
                'kpa' => $d->kpa ?? '',
                'kpi' => $d->kpi ?? '',
                'classification' => $d->classification ?? '',
                'uom' => $d->uom ?? '',
                'weight' => $weight,
                'target' => $d->target !== null && $d->target !== '' ? (string) $d->target : '',
                'target_display' => $this->rangeTargetDisplay($d),
                'due_date' => $d->due_date ?? '',
                'mid_actual' => $midActual,
                'mid_actual_text' => $midActualText,
                'mid_departemen' => $midDepartemen,
                'mid_departemen_text' => $midDepartemenText,
                'mid_karyawan' => $midKaryawan,
                'mid_karyawan_text' => $midKaryawanText,
                'mid_achievement_category' => $midAchievement,
                'mid_achievement_score' => $midAchievementScore,
                'mid_score_scale' => $midScoreScale,
                'end_actual' => $endActual,
                'end_actual_text' => $endActualText,
                'end_departemen' => $endDepartemen,
                'end_departemen_text' => $endDepartemenText,
                'end_karyawan' => $endKaryawan,
                'end_karyawan_text' => $endKaryawanText,
                'end_achievement_category' => $endAchievement,
                'end_achievement_score' => $endAchievementScore,
                'end_score_scale' => $endScoreScale,
            ];
        }

        $midPa = $countMidScale > 0 ? $this->paGradeFor($totalMidScore / $countMidScale) : null;
        $endPa = $countEndScale > 0 ? $this->paGradeFor($totalEndScore / $countEndScale) : null;
        $totalMidScore = $countMidScale > 0 ? round($totalMidScore / $countMidScale, 2) : null;
        $totalEndScore = $countEndScale > 0 ? round($totalEndScore / $countEndScale, 2) : null;

        $currentNik = trim((string) (Auth::user()->nik ?? ''));
        $isManager = $currentNik !== '' && $currentNik === trim((string) ($otorisasi->nik_manager ?? ''));
        $currentLevel = null;
        if ($currentNik !== '') {
            $lv = DB::table('hr_master_employee')->where('NIK', $currentNik)->value('Level');
            $currentLevel = $lv === null ? null : (int) $lv;
        }
        $isAdmin = in_array('hr_upload_file_template_ipp', Auth::user()->getAllPermissionCodenames(), true);
        $showScore = $isManager || ($currentLevel !== null && $currentLevel >= 8) || $isAdmin;

        $header = $this->pmsHeaderData($otorisasi, $ipp, $master);

        $notes = [];
        HrIppApprovalNote::where('ipp_otorisasi_id', $otorisasi->id)
            ->get(['tahapan', 'role', 'note'])
            ->each(function ($n) use (&$notes) {
                if ($n->note !== null) {
                    $notes[$n->tahapan][$n->role] = $n->note;
                }
            });

        $data = compact(
            'header',
            'rows',
            'totalWeight',
            'totalMidScore',
            'totalEndScore',
            'midPa',
            'endPa',
            'fillMid',
            'fillEnd',
            'showScore',
            'notes',
            'midT',
            'endT'
        );
        $data['logoSrc'] = $this->pmsLogoDataUri('hr_ipp/Logo_pas.png');
        $data['logoWingsSrc'] = $this->pmsLogoDataUri('hr_ipp/Logo_wings.png');
        $data['approval'] = $this->approvalStatusData($aktualRows, $otorisasi, $tahapanList, $filterTahapan);

        return $data;
    }

    private function approvalStatusData($aktualRows, $otorisasi, array $tahapanList, string $filterTahapan): array
    {
        $approverMap = $this->resolveApprovers($aktualRows);

        $tahapanShow = $filterTahapan !== ''
            ? array_values(array_filter($tahapanList, fn ($t) => $t === $filterTahapan))
            : $tahapanList;

        $out = [];
        foreach ($tahapanShow as $t) {
            $rows = $aktualRows->where('tahapan', $t);

            $out[] = [
                'tahap' => $t,
                'associate_manager' => $this->buildApprovalRow($rows, 'associate_manager', 'Associate Manager', $otorisasi, $approverMap),
                'manager' => $this->buildApprovalRow($rows, 'manager', 'Manager', $otorisasi, $approverMap),
            ];
        }

        return $out;
    }

    private function buildApprovalRow($rows, string $role, string $label, $otorisasi, array $approverMap): array
    {
        $approvedRow = $rows->whereNotNull('approval_' . $role)
            ->sortByDesc('approval_at_' . $role)
            ->first();

        $status = $approvedRow ? $approvedRow->{'approval_' . $role} : null;
        $at = $approvedRow ? $approvedRow->{'approval_at_' . $role} : null;
        $by = $approvedRow ? trim((string) $approvedRow->{'approval_by_' . $role}) : '';

        $nama = trim((string) ($otorisasi->{'nama_' . $role} ?? ''));
        $nik = trim((string) ($otorisasi->{'nik_' . $role} ?? ''));

        if ($status !== null) {
            $res = $by !== '' ? ($approverMap[$by] ?? null) : null;
            if ($res) {
                $nama = trim((string) ($res['name'] ?? '')) !== '' ? $res['name'] : $by;
                $nik = trim((string) ($res['nik'] ?? '')) !== '' ? $res['nik'] : '-';
            } else {
                $nama = $by !== '' ? $by : '-';
                $nik = '-';
            }
        }

        return [
            'label' => $label,
            'exists' => $nama !== '' || $nik !== '',
            'nama' => $nama !== '' ? $nama : '-',
            'nik' => $nik !== '' ? $nik : '-',
            'status' => $status,
            'at' => $at,
        ];
    }

    private function rangeTargetDisplay($d): ?string
    {
        if ($d->classification !== 'Range') {
            return null;
        }
        $tiers = $d->relationLoaded('masterTargets')
            ? $d->masterTargets
            : $d->masterTargets()->get();
        if ($tiers->isEmpty()) {
            return null;
        }
        if ($tiers->contains(fn ($t) => ($t->match_type ?? null) === \App\Support\IppRangeScale::MATCH_EQUALS)) {
            return $tiers->sortBy('score')
                ->map(fn ($t) => \App\Support\IppRangeScale::targetLine($t->score, (string) ($t->label ?? '')))
                ->values()->implode("\n");
        }
        if ($tiers->contains(fn ($t) => \App\Support\IppRangeScale::isCustomMatchType($t->match_type ?? null))) {
            $rows = $tiers->map(fn ($t) => [
                'score' => (int) $t->score,
                'match_type' => $t->match_type,
                'min_value' => $t->min_value !== null ? (float) $t->min_value : null,
                'max_value' => $t->max_value !== null ? (float) $t->max_value : null,
            ])->all();
            $decimals = \App\Support\IppRangeScale::customScaleDecimals($rows);
            return $tiers->sortBy('score')
                ->map(fn ($t) => \App\Support\IppRangeScale::targetLine($t->score, \App\Support\IppRangeScale::customTierLabel([
                    'score' => (int) $t->score,
                    'match_type' => $t->match_type,
                    'min_value' => $t->min_value !== null ? (float) $t->min_value : null,
                    'max_value' => $t->max_value !== null ? (float) $t->max_value : null,
                ], $decimals)))
                ->values()->implode("\n");
        }
        $boundaries = [];
        foreach ($tiers as $t) {
            if ($t->min_value !== null) {
                $boundaries[] = $t->min_value;
            }
            if ($t->max_value !== null) {
                $boundaries[] = $t->max_value;
            }
        }
        $sorted = $tiers->sortBy('score');
        return $sorted->map(function ($t) use ($boundaries) {
            $range = \App\Support\IppRangeScale::tierRangeLabel([
                'min_value' => $t->min_value !== null ? (float) $t->min_value : null,
                'max_value' => $t->max_value !== null ? (float) $t->max_value : null,
            ], $boundaries);
            return \App\Support\IppRangeScale::targetLine($t->score, $range);
        })->values()->implode("\n");
    }

    private function paGradeFor($score): ?string
    {
        if ($score === null) {
            return null;
        }
        $s = (float) $score;
        if ($s >= 4.2) {
            return 'A';
        }
        if ($s >= 3.8) {
            return 'B';
        }
        if ($s >= 3) {
            return 'C';
        }
        if ($s >= 2) {
            return 'D';
        }
        if ($s >= 1) {
            return 'E';
        }
        return null;
    }

    private function pmsLogoDataUri($relPath)
    {
        $path = public_path($relPath);
        if (!is_file($path)) {
            return '';
        }
        $mime = function_exists('mime_content_type') ? mime_content_type($path) : 'image/png';
        return 'data:' . ($mime ?: 'image/png') . ';base64,' . base64_encode((string) file_get_contents($path));
    }

    private function pmsHeaderData($otorisasi, $ipp, $master)
    {
        preg_match_all('/\d{4}/', (string) $ipp->periode, $m);
        $year = $m[0][0] ?? date('Y');

        $snap = PmsEmployeeSnapshot::attributesFor(
            (string) $ipp->periode,
            (string) ($otorisasi->nik_yang_bersangkutan ?? '')
        );

        $manager = ($snap && $snap->manager_nama) ? $snap->manager_nama : ($otorisasi->nama_manager ?? '');
        $assocNama = ($snap && $snap->associate_manager_nama)
            ? $snap->associate_manager_nama
            : trim((string) ($otorisasi->nama_associate_manager ?? ''));

        $departemen = ($snap && $snap->departemen) ? $snap->departemen : ($master->Departmen ?? '');
        $subDepartemen = ($snap && $snap->sub_departemen) ? $snap->sub_departemen : ($master->{'Sub Departmen'} ?? '');
        $section = ($snap && $snap->section) ? $snap->section : ($master->Section ?? '');
        $isFactory = $departemen && strtolower(trim((string) $departemen)) === 'factory';

        if ($isFactory) {
            $displayDepartemen = $subDepartemen !== '' ? $subDepartemen : (string) $departemen;
            $displayBagian = $section !== '' ? $section : (string) $subDepartemen;
        } else {
            $displayDepartemen = (string) $departemen;
            $displayBagian = $subDepartemen !== '' ? $subDepartemen : (string) $section;
        }

        $tglJoin = '';
        if ($snap && $snap->tanggal_join) {
            $tglJoin = $snap->tanggal_join->format('d M Y');
        } elseif ($master && $master->{'Tgl Masuk'}) {
            $tglJoin = \Carbon\Carbon::parse($master->{'Tgl Masuk'})->format('d M Y');
        }

        $usia = '';
        if ($snap && $snap->usia !== null) {
            $usia = $snap->usia;
        } elseif ($master && $master->{'Tgl Lahir'}) {
            $usia = \Carbon\Carbon::parse($master->{'Tgl Lahir'})->age;
        }

        return [
            'year' => (int) $year,
            'nama' => ($snap && $snap->nama) ? $snap->nama : ($master->Nama ?? ($otorisasi->nama_yang_bersangkutan ?? '')),
            'nik' => $otorisasi->nik_yang_bersangkutan ?? '',
            'jabatan' => ($snap && $snap->jabatan) ? $snap->jabatan : ($master->Jabatan ?? ''),
            'departemen' => $displayDepartemen,
            'bagian' => $displayBagian,
            'level' => ($snap && $snap->level) ? $snap->level : ($master->Level ?? ''),
            'manager' => $manager,
            'atasan_langsung' => $assocNama !== '' ? $assocNama : $manager,
            'jumlah_bawahan' => ($snap && $snap->jumlah_bawahan !== null)
                ? (string) $snap->jumlah_bawahan
                : (($otorisasi->jumlah_bawahan !== null && $otorisasi->jumlah_bawahan !== '')
                    ? (string) $otorisasi->jumlah_bawahan
                    : ''),
            'pendidikan' => ($snap && $snap->pendidikan)
                ? $snap->pendidikan
                : (string) (DB::table('hr_master_employee_pendidikan')
                    ->where('nik', $otorisasi->nik_yang_bersangkutan)
                    ->value('pendidikan') ?? ''),
            'tgl_join' => $tglJoin,
            'usia' => $usia,
        ];
    }

    private function buildPmsSpreadsheet($details, $aktualMap, $tahapanList, $filterTahapan, $otorisasi, $ipp, $master)
    {
        $spreadsheet = IOFactory::load(storage_path('app/templates/ipp_form_pms_template.xlsx'));
        $sheet = $spreadsheet->getActiveSheet();

        $midT = null;
        $endT = null;
        foreach ($tahapanList as $t) {
            if (strpos($t, 'MID ') === 0 && $midT === null) {
                $midT = $t;
            }
            if (strpos($t, 'END ') === 0 && $endT === null) {
                $endT = $t;
            }
        }

        // === Header form ===
        $h = $this->pmsHeaderData($otorisasi, $ipp, $master);

        $sheet->setCellValue('O4', $h['year']);
        $sheet->setCellValue('E6', $h['nama']);
        $sheet->setCellValue('E7', $h['nik']);
        $sheet->setCellValue('E8', $h['jabatan']);
        $sheet->setCellValue('E9', $h['departemen']);
        $sheet->setCellValue('E10', $h['bagian']);
        $sheet->setCellValue('E11', $h['level']);
        $sheet->setCellValue('O6', $h['manager']);
        $sheet->setCellValue('O7', $h['atasan_langsung']);
        $sheet->setCellValue('O8', $h['jumlah_bawahan']);
        $sheet->setCellValue('O9', $h['pendidikan']);
        $sheet->setCellValue('O10', $h['tgl_join']);
        $sheet->setCellValue('O11', $h['usia']);

        // === Resize baris data (template: 6 baris sample 16..21 + total 22) ===
        $n = count($details);
        $delta = $n - 6;
        if ($delta > 0) {
            $sheet->insertNewRowBefore(22, $delta);
        } elseif ($delta < 0) {
            $sheet->removeRows(22 + $delta, -$delta);
        }

        $totalRow = 16 + $n;

        // === Baris data ===
        $r = 16;
        foreach ($details as $i => $d) {
            $mid = $aktualMap[$d->id][$midT] ?? null;
            $end = $aktualMap[$d->id][$endT] ?? null;

            $sheet->setCellValue('B' . $r, $d->no_urut);
            $sheet->setCellValue('C' . $r, $d->objective ?? '');
            $sheet->setCellValue('F' . $r, $d->kpa ?? '');
            $sheet->setCellValue('G' . $r, $d->kpi ?? '');
            $sheet->setCellValue('I' . $r, $d->classification ?? '');
            $sheet->setCellValue('K' . $r, $d->uom ?? '');
            $sheet->setCellValue('L' . $r, $d->weight !== null && $d->weight !== '' ? (float) $d->weight : null);
            $sheet->setCellValue('M' . $r, $d->target !== null && $d->target !== '' ? (string) $d->target : '');
            $sheet->setCellValue('O' . $r, $d->due_date ?? '');

            $fillMid = $filterTahapan === '' || $filterTahapan === $midT;
            $fillEnd = $filterTahapan === '' || $filterTahapan === $endT;

            if ($fillMid) {
                $midAtasan = ($mid && $mid->aktual_atasan_varchar !== null && $mid->aktual_atasan_varchar !== '')
                    ? $mid->aktual_atasan_varchar
                    : ($mid && $mid->aktual_atasan !== null ? (float) $mid->aktual_atasan : '');
                $sheet->setCellValue('P' . $r, $midAtasan);
                $sheet->setCellValue('Q' . $r, $mid && $mid->achievement_category ? $mid->achievement_category : '');
                $sheet->setCellValue('R' . $r, $mid && $mid->score_scale !== null ? (float) $mid->score_scale : '');
            } else {
                $sheet->setCellValue('P' . $r, '');
                $sheet->setCellValue('Q' . $r, '');
                $sheet->setCellValue('R' . $r, '');
            }
            if ($fillEnd) {
                $endAtasan = ($end && $end->aktual_atasan_varchar !== null && $end->aktual_atasan_varchar !== '')
                    ? $end->aktual_atasan_varchar
                    : ($end && $end->aktual_atasan !== null ? (float) $end->aktual_atasan : '');
                $sheet->setCellValue('S' . $r, $endAtasan);
                $sheet->setCellValue('T' . $r, $end && $end->achievement_category ? $end->achievement_category : '');
                $sheet->setCellValue('U' . $r, $end && $end->score_scale !== null ? (float) $end->score_scale : '');
            } else {
                $sheet->setCellValue('S' . $r, '');
                $sheet->setCellValue('T' . $r, '');
                $sheet->setCellValue('U' . $r, '');
            }

            $r++;
        }

        // === Baris insert baru: salin pola merge & validasi dari baris sample ===
        if ($delta > 0) {
            for ($r = 22; $r <= $totalRow - 1; $r++) {
                $sheet->mergeCells('C' . $r . ':E' . $r);
                $sheet->mergeCells('G' . $r . ':H' . $r);
                $sheet->mergeCells('I' . $r . ':J' . $r);
                $sheet->mergeCells('M' . $r . ':N' . $r);

                $dv = new \PhpOffice\PhpSpreadsheet\Cell\DataValidation();
                $dv->setType(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_LIST);
                $dv->setErrorStyle(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::STYLE_STOP);
                $dv->setAllowBlank(true);
                $dv->setShowDropDown(false);
                $dv->setFormula1('"High Is Good,High Is Bad,Max,Min,Standard Score,Range"');
                $sheet->setDataValidation('I' . $r, $dv);
            }
        }

        // === Style baris data (samakan dengan baris sample) ===
        $sheet->duplicateStyle($sheet->getStyle('B16:U16'), 'B16:U' . ($totalRow - 1));

        // Format kolom aktual (P & S) sampai 10 desimal
        $sheet->getStyle('P16:P' . ($totalRow - 1))->getNumberFormat()->setFormatCode('0.##########');
        $sheet->getStyle('S16:S' . ($totalRow - 1))->getNumberFormat()->setFormatCode('0.##########');

        // === Total row ===
        $sheet->setCellValue('L' . $totalRow, '=SUM(L16:L' . ($totalRow - 1) . ')');
        $sheet->setCellValue('R' . $totalRow, '=SUM(R16:R' . ($totalRow - 1) . ')');
        $sheet->setCellValue('U' . $totalRow, '=SUM(U16:U' . ($totalRow - 1) . ')');

        // === Blok score di bawah total (referensi mutlak perlu ditulis ulang) ===
        $sheet->setCellValue('R' . ($totalRow + 6), '=R' . $totalRow);
        $sheet->setCellValue('U' . ($totalRow + 6), '=U' . $totalRow);

        // === Section IPP ACHIEVEMENT RESULT (COUNTIF) ===
        $sheet->setCellValue('D' . ($totalRow + 19), '=COUNTIF($Q$16:$Q$' . ($totalRow - 1) . ',"Exceed")');
        $sheet->setCellValue('F' . ($totalRow + 19), '=COUNTIF($T$16:$T$' . ($totalRow - 1) . ',"Exceed")');
        $sheet->setCellValue('D' . ($totalRow + 20), '=COUNTIF($Q$16:$Q$' . ($totalRow - 1) . ',"Achieve")');
        $sheet->setCellValue('F' . ($totalRow + 20), '=COUNTIF($T$16:$T$' . ($totalRow - 1) . ',"Achieve")');
        $sheet->setCellValue('D' . ($totalRow + 21), '=COUNTIF($Q$16:$Q$' . ($totalRow - 1) . ',"Not Achieve")');
        $sheet->setCellValue('F' . ($totalRow + 21), '=COUNTIF($T$16:$T$' . ($totalRow - 1) . ',"Not Achieve")');

        return $spreadsheet;
    }

    private function pmsFileName($ipp, $otorisasi, $filterTahapan, $ext)
    {
        $periode = preg_replace('/[^A-Za-z0-9]+/', '_', trim((string) $ipp->periode));
        $nik = preg_replace('/[^A-Za-z0-9]+/', '_', trim((string) $otorisasi->nik_yang_bersangkutan));
        $t = $filterTahapan !== '' ? '_' . preg_replace('/[^A-Za-z0-9]+/', '', $filterTahapan) : '';
        return 'IPP_Form_' . $nik . '_' . $periode . $t . '.' . $ext;
    }

    public function diskusiList(Request $request)
    {
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
        if (!$nik) {
            return response()->json(['success' => true, 'data' => [], 'meta' => $meta]);
        }

        $q = DB::table('hr_ipp_diskusi as d')
            ->join('hr_ipp_otorisasi as o', 'o.id', '=', 'd.ipp_otorisasi_id')
            ->join('hr_ipp as i', 'i.id', '=', 'o.ipp_id')
            ->leftJoin('hr_ipp_detail as dt', 'dt.id', '=', 'd.ipp_detail_id')
            ->where('o.nik_yang_bersangkutan', $nik)
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
                    'ipp_detail_id' => $r->ipp_detail_id ? (int) $r->ipp_detail_id : null,
                    'periode' => $r->periode,
                    'no_urut' => $r->no_urut,
                    'kpi' => $r->kpi,
                    'tahapan' => $r->tahapan,
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

    public function diskusiIndex($otoId)
    {
        $otorisasi = HrIppOtorisasi::findOrFail((int) $otoId);
        if ($otorisasi->nik_yang_bersangkutan !== $this->currentNik()) {
            abort(403);
        }

        $rows = HrIppDiskusi::where('ipp_otorisasi_id', $otorisasi->id)
            ->orderBy('id')
            ->get()
            ->map(fn ($d) => $this->diskusiPayload($d));

        return response()->json(['success' => true, 'data' => $rows]);
    }

    public function diskusiStore($otoId, Request $request)
    {
        $otorisasi = HrIppOtorisasi::findOrFail((int) $otoId);
        if ($otorisasi->nik_yang_bersangkutan !== $this->currentNik()) {
            abort(403);
        }

        $validated = $request->validate([
            'ipp_detail_id' => 'nullable|integer',
            'tahapan' => 'nullable|string|max:20',
            'pesan' => 'required|string|max:1000',
        ]);

        $d = HrIppDiskusi::create([
            'ipp_otorisasi_id' => $otorisasi->id,
            'ipp_detail_id' => $validated['ipp_detail_id'] ?? null,
            'tahapan' => $validated['tahapan'] ?? null,
            'nik_pengaju' => $this->currentNik(),
            'nama_pengaju' => Auth::user()->name ?? '',
            'pesan' => trim($validated['pesan']),
            'status' => 'open',
        ]);

        $this->notifyDiskusiBaru($otorisasi, $validated);
        IppDiskusiBroadcast::send('created', $d);

        return response()->json([
            'success' => true,
            'message' => 'Pertanyaan diskusi berhasil dikirim.',
        ]);
    }

    private function notifyDiskusiBaru(HrIppOtorisasi $otorisasi, array $validated): void
    {
        if (!HrPmsControlNotifikasi::isActive()) {
            return;
        }

        try {
            $ipp = HrIpp::find($otorisasi->ipp_id);

            $pesertaNik = trim((string) $otorisasi->nik_yang_bersangkutan);
            $pesertaNama = trim((string) $otorisasi->nama_yang_bersangkutan);
            if ($pesertaNama === '') {
                $pesertaNama = trim((string) (Auth::user()->name ?? ''));
            }

            $amNik = trim((string) $otorisasi->nik_associate_manager);
            $amNama = trim((string) $otorisasi->nama_associate_manager);
            $managerNik = trim((string) $otorisasi->nik_manager);
            $managerNama = trim((string) $otorisasi->nama_manager);

            $recipients = [];
            if ($amNik !== '' && $amNik !== $pesertaNik) {
                $recipients[$amNik] = [
                    'nik' => $amNik,
                    'nama' => $amNama,
                    'role' => 'Associate Manager',
                ];
            }
            if ($managerNik !== '' && $managerNik !== $pesertaNik && !isset($recipients[$managerNik])) {
                $recipients[$managerNik] = [
                    'nik' => $managerNik,
                    'nama' => $managerNama,
                    'role' => 'Manager',
                ];
            }

            if (empty($recipients)) {
                return;
            }

            $kpiNo = null;
            $detailId = (int) ($validated['ipp_detail_id'] ?? 0);
            if ($detailId > 0) {
                $kpiNo = DB::table('hr_ipp_detail')->where('id', $detailId)->value('no_urut');
            }

            $tahapan = trim((string) ($validated['tahapan'] ?? ''));

            SendIppDiskusiNotificationJob::dispatch(
                (string) optional($ipp)->periode,
                $tahapan,
                $tahapan !== '' ? $this->tahapanLabel($tahapan) : '-',
                $kpiNo,
                $pesertaNik,
                $pesertaNama !== '' ? $pesertaNama : $pesertaNik,
                trim((string) ($validated['pesan'] ?? '')),
                $pesertaNama !== '' ? $pesertaNama : $pesertaNik,
                $amNama !== '' ? $amNama : $amNik,
                $managerNama !== '' ? $managerNama : $managerNik,
                $pesertaNik,
                $amNik,
                $managerNik,
                array_values($recipients)
            );
        } catch (\Throwable $e) {
            Log::error('IPP diskusi notification dispatch failed', [
                'otorisasi_id' => $otorisasi->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    public function diskusiUpdate($id, Request $request)
    {
        $diskusi = HrIppDiskusi::findOrFail((int) $id);
        if ($diskusi->nik_pengaju !== $this->currentNik()) {
            abort(403);
        }

        $validated = $request->validate([
            'pesan' => 'required|string|max:1000',
        ]);

        $update = [
            'pesan' => trim($validated['pesan']),
        ];
        if ($diskusi->status === 'answered') {
            $update['status'] = 'open';
            $update['answered_at'] = null;
        }
        $diskusi->update($update);

        IppDiskusiBroadcast::send('updated', $diskusi);

        return response()->json([
            'success' => true,
            'message' => 'Pertanyaan diskusi berhasil diperbarui.',
        ]);
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
