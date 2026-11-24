<?php

namespace App\Http\Controllers\HR;

use App\Events\HR\PkDiskusiChanged;
use App\Http\Controllers\Controller;
use App\Jobs\HR\SendPkApprovalDecisionJob;
use App\Jobs\HR\SendPkDiskusiAnswerNotificationJob;
use App\Jobs\HR\SendPkDiskusiNotificationJob;
use App\Jobs\HR\SendPkSelfAssessmentCompleteJob;
use App\Models\HR\HrPk;
use App\Models\HR\HrPkDiskusi;
use App\Models\HR\HrPkNilai;
use App\Models\HR\HrPmsControlNotifikasi;
use App\Models\HR\HrPmsEmployeeSnapshot;
use App\Models\HR\HrPmsLockingPk;
use App\Support\PkAtasan;
use App\Support\PkBawahan;
use App\Support\PmsEmployeeSnapshot;
use App\Support\PmsNav;
use Barryvdh\DomPDF\Facade as PDF;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PkAssessmentController extends Controller
{
    private $otoNikCache = null;

    /** @var array<string, bool> */
    private array $snapPeriodCache = [];

    private function currentNik(): string
    {
        return trim((string) (Auth::user()->nik ?? ''));
    }

    private function pkOtorisasiNiks(): array
    {
        if ($this->otoNikCache === null) {
            $this->otoNikCache = DB::table('hr_pk_otorisasi')
                ->pluck('nik_yang_bersangkutan')
                ->map(fn ($n) => strtolower(trim((string) $n)))
                ->filter()
                ->flip()
                ->all();
        }

        return $this->otoNikCache;
    }

    private function employee($nik): ?object
    {
        if (!$nik) {
            return null;
        }

        return DB::table('hr_master_employee')
            ->where('NIK', $nik)
            ->first(['NIK', 'Nama', 'Jabatan', 'Departmen', 'Sub Departmen', 'Section', 'Level', 'Aktif']);
    }

    /**
     * Atribut karyawan untuk periode tertentu (snapshot diprioritaskan).
     */
    private function employeeForPeriod(?string $periode, string $nik): ?object
    {
        $emp = $this->employee($nik);
        $snap = PmsEmployeeSnapshot::attributesFor((string) $periode, $nik);
        if (!$snap) {
            return $emp;
        }

        return (object) [
            'NIK' => $nik,
            'Nama' => $snap->nama ?: ($emp->Nama ?? null),
            'Jabatan' => $snap->jabatan ?: ($emp->Jabatan ?? null),
            'Departmen' => $snap->departemen ?: ($emp->Departmen ?? null),
            'Sub Departmen' => $snap->sub_departemen ?: ($emp->{'Sub Departmen'} ?? null),
            'Section' => $snap->section ?: ($emp->Section ?? null),
            'Level' => ($snap->level !== null && $snap->level !== '') ? $snap->level : ($emp->Level ?? null),
            'Aktif' => $emp->Aktif ?? 'Y',
        ];
    }

    /**
     * Pasangan atasan untuk periode tertentu (snapshot diprioritaskan).
     *
     * @return array{manager: ?array, associate_manager: ?array}
     */
    private function atasanPairForPeriod(?string $periode, string $nik): array
    {
        $nik = trim($nik);
        if ($nik === '') {
            return ['manager' => null, 'associate_manager' => null];
        }

        $snap = PmsEmployeeSnapshot::attributesFor((string) $periode, $nik);
        if ($snap) {
            $mgrNik = trim((string) $snap->manager_nik);
            $amNik = trim((string) $snap->associate_manager_nik);
            if ($mgrNik !== '' || $amNik !== '') {
                return [
                    'manager' => $mgrNik !== '' ? ['nik' => $mgrNik, 'nama' => $snap->manager_nama] : null,
                    'associate_manager' => $amNik !== '' ? ['nik' => $amNik, 'nama' => $snap->associate_manager_nama] : null,
                ];
            }
        }

        $emp = $this->employeeForPeriod($periode, $nik);
        if (!$emp) {
            return ['manager' => null, 'associate_manager' => null];
        }

        return (new PkAtasan())->resolveAll($emp);
    }

    private function isAssociateManagerForPeriod(?string $periode, string $nik): bool
    {
        $normPeriode = PmsEmployeeSnapshot::normalizePeriode((string) $periode);
        $nik = trim($nik);
        if ($normPeriode !== '' && $nik !== '') {
            $hasSnap = HrPmsEmployeeSnapshot::where('periode', $normPeriode)->exists();
            if ($hasSnap) {
                return HrPmsEmployeeSnapshot::where('periode', $normPeriode)
                    ->where('associate_manager_nik', $nik)
                    ->exists();
            }
        }

        $team = (new PkAtasan())->teamOf($nik);

        return collect($team)->contains(fn ($m) => $m['role'] === 'associate_manager');
    }

    private function identities(array $usernames): array
    {
        $usernames = array_values(array_unique(array_filter($usernames, fn ($v) => $v !== null && $v !== '')));
        if (!$usernames) {
            return [];
        }

        $users = DB::table('users')->whereIn('username', $usernames)->pluck('nik', 'username');
        $emps = DB::table('hr_master_employee')
            ->whereIn('NIK', $users->filter()->values()->all())
            ->pluck('Nama', 'NIK');

        $out = [];
        foreach ($users as $username => $nik) {
            $nama = ($nik && isset($emps[$nik])) ? $emps[$nik] : DB::table('users')->where('username', $username)->value('name');
            $out[$username] = ['nik' => trim((string) $nik), 'nama' => (string) $nama];
        }

        return $out;
    }

    private function isParticipant(?object $emp): bool
    {
        if (!$emp) {
            return false;
        }
        $nik = strtolower(trim((string) ($emp->NIK ?? '')));
        if ($nik !== '' && isset($this->pkOtorisasiNiks()[$nik])) {
            return true;
        }
        if (trim((string) $emp->Aktif) !== 'Y') {
            return false;
        }
        $level = (int) $emp->Level;

        return $level >= 4 && $level <= 7;
    }

    private function snapshotExistsForPeriod(?string $periode): bool
    {
        $norm = PmsEmployeeSnapshot::normalizePeriode((string) $periode);
        if ($norm === '') {
            return false;
        }
        if (!array_key_exists($norm, $this->snapPeriodCache)) {
            $this->snapPeriodCache[$norm] = HrPmsEmployeeSnapshot::where('periode', $norm)->exists();
        }

        return $this->snapPeriodCache[$norm];
    }

    /**
     * Peserta periode: snapshot adalah kebenaran bila periode sudah punya snapshot.
     * Fallback live hanya untuk periode yang belum punya snapshot sama sekali.
     */
    private function isParticipantForPeriod(?string $periode, ?object $emp): bool
    {
        if (!$emp) {
            return false;
        }
        $nik = trim((string) ($emp->NIK ?? ''));
        if ($nik === '') {
            return false;
        }

        if ($this->snapshotExistsForPeriod($periode)) {
            $norm = PmsEmployeeSnapshot::normalizePeriode((string) $periode);

            return HrPmsEmployeeSnapshot::where('periode', $norm)->where('nik', $nik)->exists();
        }

        return $this->isParticipant($emp);
    }

    private function groupOf($level): string
    {
        return PmsEmployeeSnapshot::groupOf($level) ?? '4-5';
    }

    private function groupForPeriod(?string $periode, string $nik, $level): string
    {
        $snap = PmsEmployeeSnapshot::attributesFor((string) $periode, $nik);
        $group = $snap ? trim((string) $snap->level_group) : '';

        return $group !== '' ? $group : $this->groupOf($level);
    }

    private function jumlahBawahanData($pkId, $nik): array
    {
        return PkBawahan::resolveForTarget($pkId, $nik);
    }

    private function effectiveDetailIds(HrPk $pk, string $group, bool $noLeadership): array
    {
        return $pk->details
            ->filter(fn ($d) => in_array($d->level_group, ['all', $group], true))
            ->reject(fn ($d) => $noLeadership && PkBawahan::isLeadershipDetail($d))
            ->pluck('id')
            ->map(fn ($v) => (int) $v)
            ->values()
            ->all();
    }

    private function leadershipDetailIds(HrPk $pk, string $group): array
    {
        return $pk->details
            ->filter(fn ($d) => in_array($d->level_group, ['all', $group], true))
            ->filter(fn ($d) => PkBawahan::isLeadershipDetail($d))
            ->pluck('id')
            ->map(fn ($v) => (int) $v)
            ->values()
            ->all();
    }

    private function teamProgress(HrPk $pk, array $member, ?object $agg): array
    {
        $noLeadership = $this->isNoLeadershipForPeriod($pk->periode, $pk->id, $member['nik'], $member['group']);
        $total = count($this->effectiveDetailIds($pk, $member['group'], $noLeadership));

        if (!$noLeadership) {
            return [
                'total' => $total,
                'self_filled' => $agg ? (int) $agg->self_filled : 0,
                'leader_filled' => $agg ? (int) $agg->leader_filled : 0,
            ];
        }

        $leadIds = $this->leadershipDetailIds($pk, $member['group']);
        $leadSelf = 0;
        $leadLeader = 0;
        if (!empty($leadIds)) {
            $lc = HrPkNilai::where('hr_pk_id', $pk->id)->where('nik', $member['nik'])
                ->whereIn('hr_pk_detail_id', $leadIds)
                ->selectRaw('COUNT(nilai_self) as s, COUNT(nilai_leader) as l')
                ->first();
            $leadSelf = (int) ($lc->s ?? 0);
            $leadLeader = (int) ($lc->l ?? 0);
        }

        return [
            'total' => $total,
            'self_filled' => max(0, ($agg ? (int) $agg->self_filled : 0) - $leadSelf),
            'leader_filled' => max(0, ($agg ? (int) $agg->leader_filled : 0) - $leadLeader),
        ];
    }

    private ?array $teamCache = null;

    /** @var array<string, array> */
    private array $teamPeriodCache = [];

    private function atasanRelationForPeriod(?string $periode, string $targetNik): ?string
    {
        return $this->relationFrom($this->teamMembersForPeriod($periode), $targetNik);
    }

    private function relationFrom(array $team, string $targetNik): ?string
    {
        $me = $this->currentNik();
        if ($me === '' || $me === $targetNik) {
            return null;
        }
        foreach ($team as $m) {
            if ($m['nik'] === $targetNik) {
                return $m['role'];
            }
        }

        return null;
    }

    private function teamMembers(): array
    {
        if ($this->teamCache !== null) {
            return $this->teamCache;
        }

        return $this->teamCache = (new PkAtasan())->teamOf($this->currentNik());
    }

    /**
     * Anggota tim untuk periode tertentu berdasarkan snapshot.
     * Periode tanpa snapshot sama sekali fallback ke resolusi live.
     */
    private function teamMembersForPeriod(?string $periode): array
    {
        $periode = PmsEmployeeSnapshot::normalizePeriode((string) $periode);
        $me = $this->currentNik();
        if ($me === '') {
            return [];
        }
        $cacheKey = $me . '|' . $periode;
        if ($periode !== '' && array_key_exists($cacheKey, $this->teamPeriodCache)) {
            return $this->teamPeriodCache[$cacheKey];
        }

        if ($periode === '' || !$this->snapshotExistsForPeriod($periode)) {
            $live = $this->teamMembers();

            return $periode !== '' ? ($this->teamPeriodCache[$cacheKey] = $live) : $live;
        }

        $members = [];

        $rows = HrPmsEmployeeSnapshot::where('periode', $periode)
            ->where(function ($q) use ($me) {
                $q->where('manager_nik', $me)->orWhere('associate_manager_nik', $me);
            })
            ->orderBy('nik')
            ->get();

        foreach ($rows as $r) {
            $nik = trim((string) $r->nik);
            if ($nik === '' || $nik === $me) {
                continue;
            }
            $level = (int) $r->level;
            $members[] = [
                'nik' => $nik,
                'nama' => $r->nama,
                'jabatan' => $r->jabatan,
                'departemen' => trim((string) $r->departemen),
                'sub_departemen' => trim((string) $r->sub_departemen),
                'section' => trim((string) $r->section),
                'level' => $level,
                'group' => $r->level_group ?: PmsEmployeeSnapshot::groupOf($level),
                'role' => trim((string) $r->manager_nik) === $me ? 'manager' : 'associate_manager',
                'has_am' => trim((string) $r->associate_manager_nik) !== '',
            ];
        }

        return $this->teamPeriodCache[$cacheKey] = $members;
    }

    private function jumlahBawahanForPeriod(?string $periode, $pkId, string $nik): array
    {
        $snap = PmsEmployeeSnapshot::attributesFor((string) $periode, $nik);
        if ($snap && $snap->jumlah_bawahan !== null) {
            return [(int) $snap->jumlah_bawahan, 'snapshot'];
        }

        return $this->jumlahBawahanData($pkId, $nik);
    }

    private function isNoLeadershipForPeriod(?string $periode, $pkId, string $nik, string $group): bool
    {
        if (!in_array(trim((string) $group), ['2-3', '4-5'], true)) {
            return false;
        }

        return $this->jumlahBawahanForPeriod($periode, $pkId, $nik)[0] === 0;
    }

    public function index()
    {
        // Peserta self ATAU atasan/manager yang menilai tim (snapshot).
        $nav = PmsNav::context();

        abort_unless(
            !empty($nav['self']) || !empty($nav['leader']),
            403,
            'Halaman ini hanya untuk peserta PK (Group 4-5 / 6-7) atau atasan/manager yang menilai.'
        );

        return view('hr.pk-assessment.pk-assessment', [
            'userName' => Auth::user()->name,
            'userNik' => $this->currentNik(),
        ]);
    }

    private function detailPayload($d)
    {
        return [
            'id' => $d->id,
            'no_urut' => $d->no_urut,
            'section' => $d->section,
            'category' => $d->category,
            'level_group' => $d->level_group,
            'area' => $d->area,
            'competency_behavior' => $d->competency_behavior,
            'key_behavior' => $d->key_behavior,
            'label' => $this->detailLabel($d),
            'kriteria' => [$d->kriteria_1, $d->kriteria_2, $d->kriteria_3, $d->kriteria_4, $d->kriteria_5],
        ];
    }

    private function detailLabel($d): string
    {
        $text = trim((string) ($d->competency_behavior ?? ''));
        if ($text === '') {
            $text = trim((string) ($d->area ?? ''));
        }
        $first = $text !== '' ? trim(preg_split('/\r\n|\r|\n/', $text)[0]) : '';
        return trim(($d->no_urut ?? '') . '. ' . $first, '. ');
    }

    private function buildTeamRows(string $me): array
    {
        $pks = HrPk::orderByRaw('CAST(periode AS UNSIGNED) DESC, id DESC')->with('details')->get();

        $rows = [];
        foreach ($pks as $pk) {
            $team = $this->teamMembersForPeriod($pk->periode);
            if (!$team) {
                continue;
            }
            $teamNiks = array_map(fn ($m) => $m['nik'], $team);
            $bawahanMap = PkBawahan::bulkResolve([$pk->id], $teamNiks);
            $snapBawahan = HrPmsEmployeeSnapshot::where('periode', PmsEmployeeSnapshot::normalizePeriode((string) $pk->periode))
                ->whereIn('nik', $teamNiks)
                ->pluck('jumlah_bawahan', 'nik');

            $agg = HrPkNilai::where('hr_pk_id', $pk->id)
                ->whereIn('nik', $teamNiks)
                ->selectRaw('nik, COUNT(nilai_self) as self_filled, COUNT(nilai_leader) as leader_filled')
                ->groupBy('nik')
                ->get()
                ->keyBy('nik');
            $open = HrPkDiskusi::where('hr_pk_id', $pk->id)
                ->whereIn('nik_yang_dibahas', $teamNiks)
                ->where('status', 'open')
                ->selectRaw('nik_yang_dibahas, COUNT(*) as c')
                ->groupBy('nik_yang_dibahas')
                ->pluck('c', 'nik_yang_dibahas');
            $appr = HrPkNilai::where('hr_pk_id', $pk->id)
                ->whereIn('nik', $teamNiks)
                ->selectRaw('nik, MAX(am_approved_by IS NOT NULL) as am_ok, MAX(mgr_approved_by IS NOT NULL) as mgr_ok')
                ->groupBy('nik')
                ->get()
                ->keyBy('nik');

            foreach ($team as $m) {
                $a = $agg[$m['nik']] ?? null;
                $p = $appr[$m['nik']] ?? null;
                $prog = $this->teamProgress($pk, $m, $a);
                $rows[] = array_merge($m, [
                    'pkId' => $pk->id,
                    'periode' => (string) $pk->periode,
                    'master' => (string) ($pk->file_name ?? ''),
                    'total' => $prog['total'],
                    'self_filled' => $prog['self_filled'],
                    'leader_filled' => $prog['leader_filled'],
                    'diskusi_open' => (int) ($open[$m['nik']] ?? 0),
                    'am_approved' => (bool) ($p->am_ok ?? false),
                    'mgr_approved' => (bool) ($p->mgr_ok ?? false),
                    'jumlah_bawahan' => $snapBawahan[$m['nik']] ?? $bawahanMap[$pk->id . '|' . $m['nik']] ?? null,
                ]);
            }
        }

        return $rows;
    }

    private function matchTeamApproval(array $m, string $a): bool
    {
        if ($a === 'mgr_done') {
            return !empty($m['mgr_approved']);
        }
        if ($a === 'mgr_pending') {
            return empty($m['mgr_approved']);
        }
        if ($a === 'am_wait') {
            return !empty($m['has_am']) && empty($m['am_approved']);
        }
        if ($a === 'am_done') {
            return !empty($m['has_am']) && !empty($m['am_approved']);
        }

        return true;
    }

    private function selfRequiredFor(array $m): bool
    {
        return (string) ($m['group'] ?? '') !== '2-3';
    }

    private function matchTeamProgress(array $m, string $p): bool
    {
        if ($p === 'no_rubric') {
            return !((int) $m['total'] > 0);
        }
        if (!((int) $m['total'] > 0)) {
            return false;
        }
        if ($p === 'both_done') {
            $selfOk = !$this->selfRequiredFor($m) || (int) $m['self_filled'] >= (int) $m['total'];

            return $selfOk && (int) $m['leader_filled'] >= (int) $m['total'];
        }
        if ($p === 'self_todo') {
            return $this->selfRequiredFor($m) && (int) $m['self_filled'] < (int) $m['total'];
        }
        if ($p === 'leader_todo') {
            return (int) $m['leader_filled'] < (int) $m['total'];
        }

        return true;
    }

    private function teamSearchHaystack(array $m): string
    {
        $prog = function ($filled, $total) {
            return (int) $total === 0 ? 'tanpa rubrik' : ((int) $filled . '/' . (int) $total);
        };
        $am = !empty($m['has_am']) ? (!empty($m['am_approved']) ? 'AM ✓ approved' : 'AM -') : '';
        $mgr = !empty($m['mgr_approved']) ? 'Mgr ✓ approved' : 'Mgr -';
        $selfProg = $this->selfRequiredFor($m) ? $prog($m['self_filled'], $m['total']) : 'tidak perlu';

        return strtolower(implode(' ', [
            (string) $m['periode'],
            (string) $m['nik'],
            (string) $m['nama'],
            (string) ($m['group'] ?? ''),
            ($m['jumlah_bawahan'] === null ? '' : (string) $m['jumlah_bawahan']),
            $selfProg,
            $prog($m['leader_filled'], $m['total']),
            ((int) $m['diskusi_open'] > 0 ? ((int) $m['diskusi_open'] . ' menunggu') : '-'),
            $am,
            $mgr,
        ]));
    }

    public function teamList(Request $request)
    {
        $me = $this->currentNik();
        $all = $this->buildTeamRows($me);
        $totalAll = count($all);

        $periods = [];
        $groups = [];
        foreach ($all as $r) {
            $p = (string) $r['periode'];
            if ($p !== '' && !in_array($p, $periods, true)) {
                $periods[] = $p;
            }
            $g = trim((string) ($r['group'] ?? ''));
            if ($g !== '' && !in_array($g, $groups, true)) {
                $groups[] = $g;
            }
        }
        sort($groups, SORT_STRING);

        $periode = trim((string) $request->query('periode', 'all'));
        $approval = trim((string) $request->query('approval', 'all'));
        $progress = trim((string) $request->query('progress', 'all'));
        $diskusi = trim((string) $request->query('diskusi', 'all'));
        $group = trim((string) $request->query('group', 'all'));
        $q = strtolower(trim((string) $request->query('q', '')));

        $rows = array_values(array_filter($all, function ($r) use ($periode, $approval, $progress, $diskusi, $group, $q) {
            if ($periode !== '' && $periode !== 'all' && (string) $r['periode'] !== $periode) {
                return false;
            }
            if ($approval !== 'all' && !$this->matchTeamApproval($r, $approval)) {
                return false;
            }
            if ($progress !== 'all' && !$this->matchTeamProgress($r, $progress)) {
                return false;
            }
            if ($diskusi !== 'all') {
                $isOpen = (int) $r['diskusi_open'] > 0;
                if ($diskusi === 'open' && !$isOpen) {
                    return false;
                }
                if ($diskusi === 'none' && $isOpen) {
                    return false;
                }
            }
            if ($group !== 'all' && (string) $r['group'] !== $group) {
                return false;
            }
            if ($q !== '' && strpos($this->teamSearchHaystack($r), $q) === false) {
                return false;
            }

            return true;
        }));

        $total = count($rows);
        $perPage = (int) $request->query('per_page', 10);
        if ($perPage < 1) {
            $perPage = 10;
        }
        if ($perPage > 100) {
            $perPage = 100;
        }
        $lastPage = max(1, (int) ceil($total / $perPage));
        $page = (int) $request->query('page', 1);
        if ($page < 1) {
            $page = 1;
        }
        if ($page > $lastPage) {
            $page = $lastPage;
        }

        return response()->json([
            'success' => true,
            'data' => array_values(array_slice($rows, ($page - 1) * $perPage, $perPage)),
            'meta' => [
                'page' => $page,
                'per_page' => $perPage,
                'last_page' => $lastPage,
                'total' => $total,
                'total_all' => $totalAll,
            ],
            'options' => [
                'periods' => $periods,
                'groups' => $groups,
            ],
        ]);
    }

    public function list()
    {
        $me = $this->currentNik();
        $myEmp = $this->employee($me);

        $pks = HrPk::orderByRaw('CAST(periode AS UNSIGNED) DESC, id DESC')->with('details')->get();

        $lockMap = HrPmsLockingPk::lockedMap($pks->pluck('periode')->all());

        $data = $pks->map(function ($pk) use ($me, $lockMap) {
            $myEmpPeriod = $this->employeeForPeriod($pk->periode, $me);
            $amIPeriod = $this->isParticipantForPeriod($pk->periode, $myEmpPeriod);
            $myGroupPeriod = $amIPeriod ? $this->groupForPeriod($pk->periode, $me, $myEmpPeriod->Level) : null;

            $self = null;
            if ($amIPeriod) {
                $effectiveIds = $this->effectiveDetailIds($pk, $myGroupPeriod, $this->isNoLeadershipForPeriod($pk->periode, $pk->id, $me, $myGroupPeriod));
                $total = count($effectiveIds);
                $filled = HrPkNilai::where('hr_pk_id', $pk->id)
                    ->where('nik', $me)
                    ->whereIn('hr_pk_detail_id', $effectiveIds)
                    ->whereNotNull('nilai_self')
                    ->count();
                $ownAppr = HrPkNilai::where('hr_pk_id', $pk->id)->where('nik', $me)
                    ->selectRaw('MAX(am_approved_by IS NOT NULL) as am_ok, MAX(mgr_approved_by IS NOT NULL) as mgr_ok')
                    ->first();
                $self = [
                    'group' => $myGroupPeriod,
                    'filled' => $filled,
                    'total' => $total,
                    'readonly' => $myGroupPeriod === '2-3',
                    'am_approved' => (bool) ($ownAppr->am_ok ?? false),
                    'mgr_approved' => (bool) ($ownAppr->mgr_ok ?? false),
                ];
            }

            return [
                'id' => $pk->id,
                'periode' => $pk->periode,
                'file_name' => $pk->file_name,
                'self' => $self,
                'input_locked' => (bool) ($lockMap[trim((string) $pk->periode)]['input_karyawan'] ?? false),
            ];
        })->values();

        $periods = [];
        $groups = [];
        foreach ($pks as $pk) {
            $team = $this->teamMembersForPeriod($pk->periode);
            if (!$team) {
                continue;
            }
            $p = (string) $pk->periode;
            if ($p !== '' && !in_array($p, $periods, true)) {
                $periods[] = $p;
            }
            foreach ($team as $m) {
                $g = trim((string) ($m['group'] ?? ''));
                if ($g !== '' && !in_array($g, $groups, true)) {
                    $groups[] = $g;
                }
            }
        }
        sort($groups, SORT_STRING);

        return response()->json([
            'success' => true,
            'my' => [
                'nik' => $me,
                'nama' => $myEmp->Nama ?? (Auth::user()->name ?? ''),
                'level' => $myEmp ? (int) $myEmp->Level : null,
                'is_participant' => $data->contains(fn ($row) => $row['self'] !== null),
                'has_team' => count($periods) > 0,
            ],
            'filter_options' => [
                'periods' => $periods,
                'groups' => $groups,
            ],
            'data' => $data,
        ]);
    }

    public function show($pkId)
    {
        $pk = HrPk::with('details')->findOrFail($pkId);
        $me = $this->currentNik();
        $myEmp = $this->employeeForPeriod($pk->periode, $me);
        $amIParticipant = $this->isParticipantForPeriod($pk->periode, $myEmp);
        $myGroup = $amIParticipant ? $this->groupForPeriod($pk->periode, $me, $myEmp->Level) : null;

        $rubric = ['2-3' => [], '4-5' => [], '6-7' => []];
        foreach (['2-3', '4-5', '6-7'] as $g) {
            $rubric[$g] = $pk->details
                ->filter(fn ($d) => in_array($d->level_group, ['all', $g], true))
                ->map(fn ($d) => $this->detailPayload($d))
                ->values()
                ->all();
        }

        $team = $this->teamMembersForPeriod($pk->periode);
        $team = array_values(array_filter($team, fn ($m) => $m['nik'] !== $me));

        $niks = array_merge([$me], array_map(fn ($m) => $m['nik'], $team));
        $nilaiRows = HrPkNilai::where('hr_pk_id', $pk->id)->whereIn('nik', $niks)->get()->groupBy('nik');

        $identities = $this->identities(
            $nilaiRows->flatMap(fn ($g) => $g->pluck('leader_by'))->filter()->unique()->values()->all()
        );
        $normPeriode = PmsEmployeeSnapshot::normalizePeriode((string) $pk->periode);
        $snapRoles = PmsEmployeeSnapshot::mapFor([$pk->periode], $niks);
        $roleMap = [];
        foreach ($niks as $nik) {
            $roles = [];
            $snap = $snapRoles[$normPeriode . '|' . $nik] ?? null;
            if ($snap) {
                $amNik = trim((string) $snap->associate_manager_nik);
                $mgrNik = trim((string) $snap->manager_nik);
            } else {
                $emp = $this->employee($nik);
                if (!$emp) {
                    continue;
                }
                $atasan = (new PkAtasan())->resolveAll($emp);
                $amNik = trim((string) ($atasan['associate_manager']['nik'] ?? ''));
                $mgrNik = trim((string) ($atasan['manager']['nik'] ?? ''));
            }
            if ($amNik !== '') {
                $roles[$amNik] = 'Associate Manager';
            }
            if ($mgrNik !== '') {
                $roles[$mgrNik] = 'Manager';
            }
            $roleMap[$nik] = $roles;
        }

        $diskusiByDetail = HrPkDiskusi::where('hr_pk_id', $pk->id)
            ->whereIn('nik_yang_dibahas', $niks)
            ->where('status', 'open')
            ->get(['nik_yang_dibahas', 'hr_pk_detail_id'])
            ->groupBy('nik_yang_dibahas')
            ->map(fn ($g) => $g->groupBy('hr_pk_detail_id')->map->count());

        $mapOf = function ($nik) use ($nilaiRows, $diskusiByDetail, $identities, $roleMap) {
            $openByDetail = $diskusiByDetail->get($nik, collect());
            $roles = $roleMap[$nik] ?? [];
            $map = [];
            foreach ($nilaiRows->get($nik, collect()) as $n) {
                $ident = $n->leader_by ? ($identities[$n->leader_by] ?? null) : null;
                $editorNik = $ident['nik'] ?? '';
                $byLabel = null;
                if ($n->leader_by) {
                    $byLabel = $ident
                        ? trim(($ident['nik'] !== '' ? $ident['nik'] . ' · ' : '') . $ident['nama'])
                        : (string) $n->leader_by;
                }
                $map[$n->hr_pk_detail_id] = [
                    'nilai_self' => $n->nilai_self,
                    'self_by' => $n->self_by,
                    'self_at' => optional($n->self_at)->format('d-m-Y H:i'),
                    'nilai_leader' => $n->nilai_leader,
                    'leader_by' => $n->leader_by,
                    'leader_by_label' => $byLabel,
                    'leader_by_role' => ($editorNik !== '' && isset($roles[$editorNik])) ? $roles[$editorNik] : null,
                    'leader_at' => optional($n->leader_at)->format('d-m-Y H:i'),
                    'diskusi_open' => (int) ($openByDetail[$n->hr_pk_detail_id] ?? 0),
                ];
            }

            return $map;
        };

        $diskusiOpen = HrPkDiskusi::where('hr_pk_id', $pk->id)
            ->whereIn('nik_yang_dibahas', $niks)
            ->where('status', 'open')
            ->selectRaw('nik_yang_dibahas, COUNT(*) as c')
            ->groupBy('nik_yang_dibahas')
            ->pluck('c', 'nik_yang_dibahas');

        $jbMap = PkBawahan::bulkResolve([$pk->id], $niks);
        $snapJb = HrPmsEmployeeSnapshot::where('periode', $normPeriode)
            ->whereIn('nik', $niks)
            ->pluck('jumlah_bawahan', 'nik');

        $self = null;
        if ($amIParticipant) {
            [$jb, $jbDari] = $this->jumlahBawahanForPeriod($pk->periode, $pk->id, $me);
            $selfRows = $nilaiRows->get($me, collect());
            $self = [
                'group' => $myGroup,
                'jumlah_bawahan' => $jb,
                'jumlah_bawahan_dari' => $jbDari,
                'no_leadership' => in_array(trim((string) $myGroup), ['2-3', '4-5'], true) && $jb === 0,
                'am_approved' => $selfRows->contains(fn ($n) => $n->am_approved_by !== null),
                'mgr_approved' => $selfRows->contains(fn ($n) => $n->mgr_approved_by !== null),
                'nilai' => $mapOf($me),
            ];
        }

        $teamView = [];
        foreach ($team as $m) {
            $m['nilai'] = $mapOf($m['nik']);
            $m['diskusi_open'] = (int) ($diskusiOpen[$m['nik']] ?? 0);
            $jb = $snapJb[$m['nik']] ?? $jbMap[$pk->id . '|' . $m['nik']] ?? null;
            $m['jumlah_bawahan'] = $jb;
            $m['no_leadership'] = in_array(trim((string) $m['group']), ['2-3', '4-5'], true) && ($jb === 0);
            $teamView[] = $m;
        }

        return response()->json([
            'success' => true,
            'pk' => ['id' => $pk->id, 'periode' => $pk->periode, 'file_name' => $pk->file_name],
            'my' => [
                'nik' => $me,
                'nama' => $myEmp->Nama ?? (Auth::user()->name ?? ''),
                'level' => $myEmp ? (int) $myEmp->Level : null,
                'is_participant' => $amIParticipant,
            ],
            'rubric' => $rubric,
            'self' => $self,
            'team' => $teamView,
            'locks' => HrPmsLockingPk::flagsFor((string) $pk->periode),
        ]);
    }

    /**
     * Tolak aksi PK saat kolom LOCKING PK periode ini sedang dikunci.
     */
    private function guardLockedPk(?string $periode, string $column): void
    {
        if (HrPmsLockingPk::isLocked($periode, $column)) {
            abort(422, HrPmsLockingPk::label($column) . ' periode ' . trim((string) $periode) . ' sedang dikunci (LOCKING PK).');
        }
    }

    public function save(Request $request, $pkId)
    {
        $pk = HrPk::findOrFail($pkId);
        $me = $this->currentNik();
        $myEmp = $this->employeeForPeriod($pk->periode, $me);

        $mode = $request->input('mode', 'self');
        $targetNik = trim((string) $request->input('nik', ''));
        $relation = null;
        if ($mode === 'self') {
            abort_unless($this->isParticipantForPeriod($pk->periode, $myEmp), 403, 'Anda bukan peserta Penilaian Karya.');
            $targetNik = $me;
        } else {
            abort_unless($mode === 'leader', 422, 'Mode tidak dikenali.');
            $relation = $targetNik !== '' ? $this->atasanRelationForPeriod($pk->periode, $targetNik) : null;
            abort_unless($relation !== null, 403, 'Anda bukan atasan peserta ini.');
        }

        $this->guardLockedPk($pk->periode, $mode === 'self' ? 'input_karyawan' : 'input_atasan');

        $target = $this->employeeForPeriod($pk->periode, $targetNik);
        $group = $this->groupForPeriod($pk->periode, $targetNik, $target->Level);
        if ($mode === 'self' && $group === '2-3') {
            abort(422, 'Self Assessment group 2-3 tidak diisi oleh karyawan; penilaian diberikan atasan pada kolom Nilai Atasan.');
        }

        $approvals = HrPkNilai::where('hr_pk_id', $pk->id)->where('nik', $targetNik)
            ->selectRaw('MAX(am_approved_by IS NOT NULL) as am_ok, MAX(mgr_approved_by IS NOT NULL) as mgr_ok')
            ->first();
        $amOk = (bool) ($approvals->am_ok ?? false);
        $mgrOk = (bool) ($approvals->mgr_ok ?? false);
        $locked = $mgrOk || ($amOk && ($mode === 'self' || $relation === 'associate_manager'));
        if ($locked) {
            $stage = $mgrOk ? 'Manager' : 'Associate Manager';
            abort(422, 'Penilaian peserta ini sudah di-approve ' . $stage . '. Lakukan Unapprove terlebih dahulu untuk mengubah nilai.');
        }

        $rules = [
            'items' => 'required|array',
            'items.*.detail_id' => 'required|integer',
            'items.*.nilai' => 'nullable|integer|between:1,5',
            'jumlah_bawahan' => 'required|integer|min:0|max:999999',
        ];
        $payload = $request->validate($rules);

        $username = Auth::user()->username ?? (Auth::user()->name ?? $me);

        if ($mode === 'self') {
            PmsEmployeeSnapshot::capture((string) $pk->periode, (string) $targetNik, [
                'jumlah_bawahan' => (int) $payload['jumlah_bawahan'],
                'source' => PmsEmployeeSnapshot::SOURCE_PK,
            ]);
        }
        PmsEmployeeSnapshot::syncJumlahBawahan((string) $pk->periode, (string) $targetNik, (int) $payload['jumlah_bawahan']);

        $noLeadership = $this->isNoLeadershipForPeriod($pk->periode, $pk->id, $targetNik, $group);
        $leadershipIds = $noLeadership ? $this->leadershipDetailIds($pk, $group) : [];
        $effectiveIds = $this->effectiveDetailIds($pk, $group, $noLeadership);
        $total = count($effectiveIds);
        $countSelfFilled = fn () => HrPkNilai::where('hr_pk_id', $pk->id)->where('nik', $targetNik)
            ->whereIn('hr_pk_detail_id', $effectiveIds)
            ->whereNotNull('nilai_self')
            ->count();
        $selfFilledBefore = $mode === 'self' ? $countSelfFilled() : 0;

        $ids = array_values(array_unique(array_map(fn ($i) => (int) $i['detail_id'], $payload['items'])));
        $details = $pk->details()->whereIn('id', $ids)->get()->keyBy('id');
        $existing = HrPkNilai::where('hr_pk_id', $pk->id)->where('nik', $targetNik)->whereIn('hr_pk_detail_id', $ids)->get()->keyBy('hr_pk_detail_id');

        DB::transaction(function () use ($payload, $pk, $details, $existing, $targetNik, $target, $group, $mode, $username, $noLeadership, $leadershipIds) {
            foreach ($payload['items'] as $item) {
                $d = $details->get((int) $item['detail_id']);
                abort_unless($d && in_array($d->level_group, ['all', $group], true), 422, 'Item rubrik tidak valid untuk group ' . $group . '.');

                if ($noLeadership && in_array((int) $d->id, $leadershipIds, true)) {
                    continue;
                }

                $row = $existing->get((int) $item['detail_id']) ?: new HrPkNilai([
                    'hr_pk_id' => $pk->id,
                    'hr_pk_detail_id' => $d->id,
                    'nik' => $targetNik,
                ]);

                $nilai = $item['nilai'];
                $nilai = ($nilai === '' || $nilai === null) ? null : (int) $nilai;

                $col = $mode === 'self' ? 'nilai_self' : 'nilai_leader';
                $byCol = $mode === 'self' ? 'self_by' : 'leader_by';
                $atCol = $mode === 'self' ? 'self_at' : 'leader_at';

                if ($row->exists) {
                    $current = $row->{$col} === null ? null : (int) $row->{$col};
                    $blankRow = $row->nilai_self === null && $row->nilai_leader === null;
                    $stampMatched = $nilai === null ? $row->{$byCol} === null : $row->{$byCol} !== null;
                    if (!$blankRow && $current === $nilai && $stampMatched) {
                        continue;
                    }
                } elseif ($nilai === null) {
                    continue;
                }

                $row->{$col} = $nilai;
                $row->{$byCol} = $nilai === null ? null : $username;
                $row->{$atCol} = $nilai === null ? null : now();

                if ($row->exists && $row->nilai_self === null && $row->nilai_leader === null) {
                    $row->delete();
                    continue;
                }
                if (!$row->exists) {
                    $row->nama = $target->Nama;
                }
                $row->save();
            }

            if ($noLeadership && !empty($leadershipIds)) {
                HrPkNilai::where('hr_pk_id', $pk->id)->where('nik', $targetNik)
                    ->whereIn('hr_pk_detail_id', $leadershipIds)
                    ->delete();
            }
        });

        if ($mode === 'self' && $total > 0 && $selfFilledBefore < $total) {
            if ($countSelfFilled() >= $total) {
                $this->notifySelfAssessmentComplete($pk, $target, $group);
            }
        }

        return response()->json(['success' => true, 'message' => 'Nilai berhasil disimpan.']);
    }

    private function notifySelfAssessmentComplete(HrPk $pk, object $target, string $group): void
    {
        if (!HrPmsControlNotifikasi::isActive()) {
            return;
        }

        $isAssociateManager = $this->isAssociateManagerForPeriod($pk->periode, (string) $target->NIK);
        $atasan = $this->atasanPairForPeriod($pk->periode, (string) $target->NIK);

        $recipients = [];

        $manager = $atasan['manager'] ?? null;
        $managerNik = trim((string) ($manager['nik'] ?? ''));
        if ($managerNik !== '' && $managerNik !== $target->NIK) {
            $recipients[$managerNik] = [
                'nik' => $managerNik,
                'nama' => (string) ($manager['nama'] ?? ''),
                'role' => 'Manager',
            ];
        }

        $am = $atasan['associate_manager'] ?? null;
        $amNik = trim((string) ($am['nik'] ?? ''));
        if (!$isAssociateManager && $amNik !== '' && $amNik !== $target->NIK && !isset($recipients[$amNik])) {
            $recipients[$amNik] = [
                'nik' => $amNik,
                'nama' => (string) ($am['nama'] ?? ''),
                'role' => 'Associate Manager',
            ];
        }

        if (empty($recipients)) {
            return;
        }

        SendPkSelfAssessmentCompleteJob::dispatch(
            $pk->periode,
            $group,
            (string) $target->NIK,
            (string) $target->Nama,
            array_values($recipients)
        );
    }

    public function approve(Request $request, $pkId, $nik)
    {
        $pk = HrPk::findOrFail($pkId);
        $me = $this->currentNik();
        abort_unless($me !== '', 403);

        $nik = trim((string) $nik);
        $payload = $request->validate([
            'stage' => 'required|in:am,manager',
            'action' => 'required|in:approve,unapprove',
        ]);

        if ($payload['action'] === 'unapprove') {
            $this->guardLockedPk($pk->periode, 'unapproval');
        } else {
            $this->guardLockedPk($pk->periode, $payload['stage'] === 'am' ? 'approval_am' : 'approval_manager');
        }

        $target = $this->employeeForPeriod($pk->periode, $nik);
        abort_unless($target && $this->isParticipantForPeriod($pk->periode, $target), 404, 'Peserta tidak ditemukan.');

        $member = collect($this->teamMembersForPeriod($pk->periode))->first(fn ($m) => $m['nik'] === $nik);
        abort_unless($member !== null, 403, 'Anda bukan atasan peserta ini.');
        $rel = $this->atasanRelationForPeriod($pk->periode, $nik);
        $hasAm = (bool) ($member['has_am'] ?? false);
        $group = $this->groupForPeriod($pk->periode, $nik, $target->Level);

        if ($payload['stage'] === 'am') {
            abort_unless($hasAm, 422, 'Peserta ini tidak memiliki Associate Manager.');
            abort_unless($rel === 'associate_manager', 403, 'Hanya Associate Manager peserta ini yang dapat menyetujui tahap AM.');
        } else {
            abort_unless($rel === 'manager', 403, 'Hanya Manager peserta ini yang dapat menyetujui tahap Manager.');
        }

        $rows = HrPkNilai::where('hr_pk_id', $pk->id)->where('nik', $nik)->get();
        abort_if($rows->isEmpty(), 422, 'Belum ada nilai untuk peserta ini.');
        $amApproved = $rows->contains(fn ($r) => $r->am_approved_by !== null);
        $mgrApproved = $rows->contains(fn ($r) => $r->mgr_approved_by !== null);

        if ($payload['action'] === 'approve') {
            $requiredIds = $this->effectiveDetailIds($pk, $group, $this->isNoLeadershipForPeriod($pk->periode, $pk->id, $nik, $group));
            $total = count($requiredIds);
            $requiredRows = $rows->whereIn('hr_pk_detail_id', $requiredIds);
            abort_if($total === 0 || $requiredRows->count() < $total, 422, 'Nilai belum lengkap untuk disetujui.');
            if ($group !== '2-3') {
                abort_if($requiredRows->whereNotNull('nilai_self')->count() < $total, 422, 'Self Assessment peserta belum lengkap.');
            }
            abort_if($requiredRows->whereNotNull('nilai_leader')->count() < $total, 422, 'Leader Assessment belum lengkap.');
            abort_if($payload['stage'] === 'am' && $amApproved, 422, 'Tahap AM sudah disetujui.');
            if ($payload['stage'] === 'manager') {
                abort_if($mgrApproved, 422, 'Tahap Manager sudah disetujui.');
                abort_if($hasAm && !$amApproved, 422, 'Tahap Associate Manager harus disetujui terlebih dahulu.');
            }
        } else {
            if ($payload['stage'] === 'am') {
                abort_if(!$amApproved, 422, 'Tahap AM belum disetujui.');
                abort_if($mgrApproved, 422, 'Buka persetujuan Manager terlebih dahulu.');
            } else {
                abort_if(!$mgrApproved, 422, 'Tahap Manager belum disetujui.');
            }
        }

        $username = Auth::user()->username ?? (Auth::user()->name ?? $me);
        $byCol = $payload['stage'] === 'am' ? 'am_approved_by' : 'mgr_approved_by';
        $atCol = $payload['stage'] === 'am' ? 'am_approved_at' : 'mgr_approved_at';

        HrPkNilai::where('hr_pk_id', $pk->id)->where('nik', $nik)->update([
            $byCol => $payload['action'] === 'approve' ? $username : null,
            $atCol => $payload['action'] === 'approve' ? now() : null,
        ]);

        if ($payload['action'] === 'approve') {
            $this->notifyPkApprovalDecision($pk, $target, $group, $payload['stage'], $hasAm);
        }

        $label = $payload['stage'] === 'am' ? 'Associate Manager' : 'Manager';

        return response()->json([
            'success' => true,
            'message' => 'Persetujuan ' . $label . ' ' . ($payload['action'] === 'approve' ? 'berhasil disimpan.' : 'berhasil dibatalkan.'),
        ]);
    }

    private function notifyPkApprovalDecision(HrPk $pk, object $target, string $group, string $stage, bool $hasAm): void
    {
        if (!HrPmsControlNotifikasi::isActive()) {
            return;
        }

        $me = $this->currentNik();
        $pesertaNik = trim((string) $target->NIK);
        if ($pesertaNik === '') {
            return;
        }

        $recipients = [];
        if ($pesertaNik !== $me) {
            $recipients[$pesertaNik] = [
                'nik' => $pesertaNik,
                'nama' => (string) $target->Nama,
                'type' => 'peserta',
            ];
        }

        $atasan = $this->atasanPairForPeriod($pk->periode, $pesertaNik);

        if ($stage === 'am') {
            $manager = $atasan['manager'] ?? null;
            $managerNik = trim((string) ($manager['nik'] ?? ''));
            if ($managerNik !== '' && $managerNik !== $pesertaNik && $managerNik !== $me && !isset($recipients[$managerNik])) {
                $recipients[$managerNik] = [
                    'nik' => $managerNik,
                    'nama' => (string) ($manager['nama'] ?? ''),
                    'type' => 'atasan',
                    'must_approve' => true,
                ];
            }
        } elseif ($hasAm) {
            $am = $atasan['associate_manager'] ?? null;
            $amNik = trim((string) ($am['nik'] ?? ''));
            if ($amNik !== '' && $amNik !== $pesertaNik && $amNik !== $me && !isset($recipients[$amNik])) {
                $recipients[$amNik] = [
                    'nik' => $amNik,
                    'nama' => (string) ($am['nama'] ?? ''),
                    'type' => 'atasan',
                    'must_approve' => false,
                ];
            }
        }

        if (empty($recipients)) {
            return;
        }

        SendPkApprovalDecisionJob::dispatch(
            $pk->periode,
            $group,
            $pesertaNik,
            (string) $target->Nama,
            (string) (Auth::user()->name ?? $me),
            $stage === 'am' ? 'Associate Manager' : 'Manager',
            array_values($recipients)
        );
    }

    private function diskusiGuard($pkId, $nik)
    {
        $me = $this->currentNik();
        abort_unless($me !== '', 403);
        if ($me === $nik) {
            return 'self';
        }
        $periode = HrPk::where('id', $pkId)->value('periode');
        $rel = $this->atasanRelationForPeriod($periode, $nik);
        abort_if($rel === null, 403, 'Anda tidak berwenang atas diskusi ini.');

        return $rel;
    }

    public function diskusiList(Request $request)
    {
        $perPage = (int) $request->get('per_page', 10);
        $perPage = max(1, min(100, $perPage));
        $page = max(1, (int) $request->get('page', 1));
        $me = $this->currentNik();
        $scope = $request->get('scope') === 'team' ? 'team' : 'self';

        $q = HrPkDiskusi::query()
            ->orderByRaw("CASE WHEN status = 'open' THEN 0 ELSE 1 END")
            ->orderBy('created_at', 'desc');

        if ($scope === 'team') {
            $teamPairs = [];
            foreach (HrPk::orderByRaw('CAST(periode AS UNSIGNED) DESC, id DESC')->get(['id', 'periode']) as $pkRow) {
                $niks = array_values(array_unique(array_filter(array_map(
                    fn ($m) => trim((string) ($m['nik'] ?? '')),
                    $this->teamMembersForPeriod((string) $pkRow->periode)
                ))));
                if (!$niks) {
                    continue;
                }
                $periode = (string) $pkRow->periode;
                if (!isset($teamPairs[$periode])) {
                    $teamPairs[$periode] = ['ids' => [], 'niks' => []];
                }
                $teamPairs[$periode]['ids'][] = $pkRow->id;
                $teamPairs[$periode]['niks'] = array_values(array_unique(array_merge($teamPairs[$periode]['niks'], $niks)));
            }

            $q->where('nik_yang_dibahas', '!=', $me)
                ->where(function ($qq) use ($teamPairs, $me) {
                    foreach ($teamPairs as $pair) {
                        $qq->orWhere(function ($w) use ($pair) {
                            $w->whereIn('hr_pk_id', $pair['ids'])
                                ->whereIn('nik_yang_dibahas', $pair['niks']);
                        });
                    }
                    $qq->orWhere('nik_pengaju', $me);
                });
        } else {
            $q->where('nik_yang_dibahas', $me);
        }

        $total = (clone $q)->count();
        $lastPage = max(1, (int) ceil($total / $perPage));
        $page = min($page, $lastPage);
        $rows = $q->forPage($page, $perPage)->get();

        $pkIds = $rows->pluck('hr_pk_id')->unique()->all();
        $pks = HrPk::whereIn('id', $pkIds)->pluck('periode', 'id');
        $detailIds = $rows->pluck('hr_pk_detail_id')->filter()->unique()->all();
        $details = DB::table('hr_pk_detail')->whereIn('id', $detailIds)->get(['id', 'no_urut', 'area', 'competency_behavior'])->keyBy('id');
        $niks = $rows->pluck('nik_yang_dibahas')->unique()->all();
        $names = DB::table('hr_master_employee')->whereIn('NIK', $niks)->pluck('Nama', 'NIK');
        $snapNames = PmsEmployeeSnapshot::mapFor($pks->values()->all(), $niks);

        $data = $rows->map(function ($r) use ($pks, $details, $names, $snapNames, $me) {
            $d = $r->hr_pk_detail_id ? ($details[$r->hr_pk_detail_id] ?? null) : null;
            $periode = $pks[$r->hr_pk_id] ?? null;
            $snap = $periode !== null
                ? ($snapNames[PmsEmployeeSnapshot::normalizePeriode((string) $periode) . '|' . $r->nik_yang_dibahas] ?? null)
                : null;

            return [
                'id' => $r->id,
                'hr_pk_id' => $r->hr_pk_id,
                'periode' => $periode ?? '-',
                'nik_yang_dibahas' => $r->nik_yang_dibahas,
                'nama_yang_dibahas' => ($snap && trim((string) $snap->nama) !== '') ? $snap->nama : ($names[$r->nik_yang_dibahas] ?? $r->nik_yang_dibahas),
                'item' => $d ? $this->detailLabel($d) : 'Umum',
                'pesan' => $r->pesan,
                'jawaban' => $r->jawaban,
                'status' => $r->status,
                'is_pengaju' => $r->nik_pengaju === $me,
                'can_answer' => $r->status === 'open' && $this->atasanRelationForPeriod($periode, $r->nik_yang_dibahas) !== null,
                'created_at' => optional($r->created_at)->format('d-m-Y H:i'),
                'answered_at' => optional($r->answered_at)->format('d-m-Y H:i'),
            ];
        })->values();

        return response()->json([
            'success' => true,
            'data' => $data,
            'meta' => ['page' => $page, 'per_page' => $perPage, 'total' => $total, 'last_page' => $lastPage, 'scope' => $scope],
        ]);
    }

    public function diskusiIndex($pkId, $nik)
    {
        $this->diskusiGuard($pkId, $nik);
        $me = $this->currentNik();
        $periode = HrPk::where('id', $pkId)->value('periode');

        $rows = HrPkDiskusi::where('hr_pk_id', $pkId)
            ->where('nik_yang_dibahas', $nik)
            ->orderBy('created_at', 'asc')
            ->get();

        $detailIds = $rows->pluck('hr_pk_detail_id')->filter()->unique()->all();
        $details = DB::table('hr_pk_detail')->whereIn('id', $detailIds)->get(['id', 'no_urut', 'area', 'competency_behavior'])->keyBy('id');

        $data = $rows->map(function ($r) use ($details, $me, $periode) {
            $d = $r->hr_pk_detail_id ? ($details[$r->hr_pk_detail_id] ?? null) : null;

            return [
                'id' => $r->id,
                'hr_pk_detail_id' => $r->hr_pk_detail_id,
                'item' => $d ? $this->detailLabel($d) : 'Umum',
                'nik_pengaju' => $r->nik_pengaju,
                'nama_pengaju' => $r->nama_pengaju,
                'pesan' => $r->pesan,
                'jawaban' => $r->jawaban,
                'nama_penjawab' => $r->nama_penjawab,
                'status' => $r->status,
                'can_edit' => $r->nik_pengaju === $me,
                'can_answer' => $r->status === 'open' && $this->atasanRelationForPeriod($periode, $r->nik_yang_dibahas) !== null,
                'created_at' => optional($r->created_at)->format('d-m-Y H:i'),
                'answered_at' => optional($r->answered_at)->format('d-m-Y H:i'),
            ];
        })->values();

        return response()->json(['success' => true, 'data' => $data, 'my_nik' => $me]);
    }

    public function diskusiStore(Request $request, $pkId, $nik)
    {
        $rel = $this->diskusiGuard($pkId, $nik);
        abort_unless($rel === 'self', 403, 'Hanya peserta yang dapat memulai diskusi.');

        $payload = $request->validate([
            'hr_pk_detail_id' => 'nullable|integer',
            'pesan' => 'required|string|max:1000',
        ]);

        if (!empty($payload['hr_pk_detail_id'])) {
            $exists = DB::table('hr_pk_detail')->where('id', $payload['hr_pk_detail_id'])->where('hr_pk_id', $pkId)->exists();
            abort_unless($exists, 422, 'Item rubrik tidak dikenali.');
        }

        $emp = $this->employee($this->currentNik());

        $d = HrPkDiskusi::create([
            'hr_pk_id' => $pkId,
            'nik_yang_dibahas' => $nik,
            'hr_pk_detail_id' => $payload['hr_pk_detail_id'] ?: null,
            'nik_pengaju' => $this->currentNik(),
            'nama_pengaju' => $emp->Nama ?? (Auth::user()->name ?? ''),
            'pesan' => $payload['pesan'],
            'status' => 'open',
        ]);

        $this->notifyDiskusiBaru($d);
        $this->broadcastDiskusi('created', $d);

        return response()->json(['success' => true, 'id' => $d->id]);
    }

    public function diskusiUpdate(Request $request, $id)
    {
        $d = HrPkDiskusi::findOrFail($id);
        abort_unless($d->nik_pengaju === $this->currentNik(), 403, 'Hanya pesan Anda sendiri yang bisa diedit.');

        $payload = $request->validate([
            'pesan' => 'required|string|max:1000',
        ]);

        $d->pesan = $payload['pesan'];
        if ($d->status === 'answered') {
            $d->status = 'open';
            $d->answered_at = null;
        }
        $d->save();

        $this->broadcastDiskusi('updated', $d);

        return response()->json(['success' => true]);
    }

    public function diskusiAnswer(Request $request, $id)
    {
        $d = HrPkDiskusi::findOrFail($id);
        $periode = HrPk::where('id', $d->hr_pk_id)->value('periode');
        $rel = $this->atasanRelationForPeriod($periode, $d->nik_yang_dibahas);
        abort_unless($rel !== null, 403, 'Hanya atasan peserta yang dapat menjawab.');

        $payload = $request->validate([
            'jawaban' => 'required|string|max:1000',
        ]);

        $statusBefore = $d->status;
        $emp = $this->employee($this->currentNik());
        $d->jawaban = $payload['jawaban'];
        $d->nik_penjawab = $this->currentNik();
        $d->nama_penjawab = $emp->Nama ?? (Auth::user()->name ?? '');
        $d->status = 'answered';
        $d->answered_at = now();
        $d->save();

        if ($statusBefore !== 'answered') {
            $this->notifyDiskusiDijawab($d, $rel);
        }

        $this->broadcastDiskusi('answered', $d);

        return response()->json(['success' => true]);
    }

    private function diskusiItemData(HrPkDiskusi $d): array
    {
        $itemNo = null;
        $itemLabel = '';
        if (!empty($d->hr_pk_detail_id)) {
            $det = DB::table('hr_pk_detail')->where('id', $d->hr_pk_detail_id)
                ->first(['id', 'no_urut', 'area', 'competency_behavior']);
            if ($det) {
                $itemNo = $det->no_urut;
                $itemLabel = $this->detailLabel($det);
            }
        }

        return [$itemNo, $itemLabel];
    }

    /**
     * NIK penerima event realtime diskusi: peserta + pengaju + penjawab +
     * manager/associate manager periode tersebut (sudah didedupe).
     */
    private function diskusiBroadcastTargets(HrPkDiskusi $d): array
    {
        $targets = [
            (string) $d->nik_yang_dibahas,
            (string) $d->nik_pengaju,
            (string) $d->nik_penjawab,
        ];

        $periode = (string) HrPk::where('id', $d->hr_pk_id)->value('periode');
        $atasan = $this->atasanPairForPeriod($periode, (string) $d->nik_yang_dibahas);
        foreach (['manager', 'associate_manager'] as $role) {
            $nik = trim((string) ($atasan[$role]['nik'] ?? ''));
            if ($nik !== '') {
                $targets[] = $nik;
            }
        }

        return array_values(array_unique(array_filter(array_map(function ($nik) {
            return trim((string) $nik);
        }, $targets), function ($nik) {
            return $nik !== '';
        })));
    }

    /**
     * Payload viewer-agnostic: klien menghitung sendiri can_edit/can_answer
     * dari nik_pengaju dan answerer_niks.
     */
    private function diskusiEventPayload(HrPkDiskusi $d, string $type): array
    {
        $periode = (string) HrPk::where('id', $d->hr_pk_id)->value('periode');
        $peserta = $this->employeeForPeriod($periode, (string) $d->nik_yang_dibahas);
        $itemLabel = !empty($d->hr_pk_detail_id) ? $this->diskusiItemData($d)[1] : '';

        $atasan = $this->atasanPairForPeriod($periode, (string) $d->nik_yang_dibahas);
        $answerers = [];
        foreach (['manager', 'associate_manager'] as $role) {
            $nik = trim((string) ($atasan[$role]['nik'] ?? ''));
            if ($nik !== '') {
                $answerers[] = $nik;
            }
        }

        return [
            'type' => $type,
            'id' => (int) $d->id,
            'hr_pk_id' => (int) $d->hr_pk_id,
            'hr_pk_detail_id' => $d->hr_pk_detail_id ? (int) $d->hr_pk_detail_id : null,
            'periode' => $periode,
            'nik_yang_dibahas' => (string) $d->nik_yang_dibahas,
            'nama_yang_dibahas' => trim((string) ($peserta->Nama ?? '')) !== ''
                ? (string) $peserta->Nama
                : (string) $d->nik_yang_dibahas,
            'item' => $itemLabel !== '' ? $itemLabel : 'Umum',
            'pesan' => (string) $d->pesan,
            'jawaban' => (string) ($d->jawaban ?? ''),
            'nik_pengaju' => (string) $d->nik_pengaju,
            'nama_pengaju' => (string) $d->nama_pengaju,
            'nik_penjawab' => (string) ($d->nik_penjawab ?? ''),
            'nama_penjawab' => (string) ($d->nama_penjawab ?? ''),
            'status' => (string) $d->status,
            'open_count' => (int) HrPkDiskusi::where('hr_pk_id', $d->hr_pk_id)
                ->where('nik_yang_dibahas', $d->nik_yang_dibahas)
                ->where('status', 'open')
                ->count(),
            'answerer_niks' => array_values(array_unique($answerers)),
            'created_at' => optional($d->created_at)->format('d-m-Y H:i'),
            'answered_at' => optional($d->answered_at)->format('d-m-Y H:i'),
        ];
    }

    private function broadcastDiskusi(string $type, HrPkDiskusi $d): void
    {
        try {
            $targets = $this->diskusiBroadcastTargets($d);
            if (!$targets) {
                return;
            }

            event(new PkDiskusiChanged($type, $targets, $this->diskusiEventPayload($d, $type)));
        } catch (\Throwable $e) {
            Log::error('PK diskusi broadcast failed', [
                'diskusi_id' => $d->id,
                'type' => $type,
                'error' => $e->getMessage(),
            ]);
        }
    }

    private function notifyDiskusiBaru(HrPkDiskusi $d): void
    {
        if (!HrPmsControlNotifikasi::isActive()) {
            return;
        }

        try {
            $periode = (string) HrPk::where('id', $d->hr_pk_id)->value('periode');
            $peserta = $this->employeeForPeriod($periode, (string) $d->nik_yang_dibahas);
            if (!$peserta) {
                return;
            }

            $pesertaNik = (string) $peserta->NIK;
            $pesertaNama = (string) $peserta->Nama;
            $atasan = $this->atasanPairForPeriod($periode, $pesertaNik);

            $manager = $atasan['manager'] ?? null;
            $managerNik = trim((string) ($manager['nik'] ?? ''));
            $managerNama = trim((string) ($manager['nama'] ?? ''));
            $am = $atasan['associate_manager'] ?? null;
            $amNik = trim((string) ($am['nik'] ?? ''));
            $amNama = trim((string) ($am['nama'] ?? ''));

            $recipients = [];
            if ($managerNik !== '' && $managerNik !== $pesertaNik) {
                $recipients[$managerNik] = [
                    'nik' => $managerNik,
                    'nama' => $managerNama,
                    'role' => 'Manager',
                    'type' => 'atasan',
                ];
            }
            if ($amNik !== '' && $amNik !== $pesertaNik && !isset($recipients[$amNik])) {
                $recipients[$amNik] = [
                    'nik' => $amNik,
                    'nama' => $amNama,
                    'role' => 'Associate Manager',
                    'type' => 'atasan',
                ];
            }

            if (empty($recipients)) {
                return;
            }

            [$itemNo, $itemLabel] = $this->diskusiItemData($d);
            $pengajuNama = trim((string) ($d->nama_pengaju !== '' ? $d->nama_pengaju : $pesertaNama));

            SendPkDiskusiNotificationJob::dispatch(
                $periode,
                $pesertaNik,
                $pesertaNama !== '' ? $pesertaNama : $pesertaNik,
                $itemNo,
                $itemLabel,
                $pengajuNama !== '' ? $pengajuNama : $pesertaNik,
                'Peserta',
                (string) $d->pesan,
                $pesertaNama !== '' ? $pesertaNama : $pesertaNik,
                $amNama !== '' ? $amNama : $amNik,
                $managerNama !== '' ? $managerNama : $managerNik,
                $pesertaNik,
                $amNik,
                $managerNik,
                array_values($recipients)
            );
        } catch (\Throwable $e) {
            Log::error('PK diskusi notification dispatch failed', [
                'diskusi_id' => $d->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    private function notifyDiskusiDijawab(HrPkDiskusi $d, string $penjawabRole): void
    {
        if (!HrPmsControlNotifikasi::isActive()) {
            return;
        }

        try {
            $periode = (string) HrPk::where('id', $d->hr_pk_id)->value('periode');
            $peserta = $this->employeeForPeriod($periode, (string) $d->nik_yang_dibahas);
            if (!$peserta) {
                return;
            }

            $pesertaNik = (string) $peserta->NIK;
            $pesertaNama = (string) $peserta->Nama;
            $atasan = $this->atasanPairForPeriod($periode, $pesertaNik);

            $manager = $atasan['manager'] ?? null;
            $managerNik = trim((string) ($manager['nik'] ?? ''));
            $managerNama = trim((string) ($manager['nama'] ?? ''));
            $am = $atasan['associate_manager'] ?? null;
            $amNik = trim((string) ($am['nik'] ?? ''));
            $amNama = trim((string) ($am['nama'] ?? ''));

            $penjawabNik = trim((string) $d->nik_penjawab);
            if ($penjawabNik === '') {
                $penjawabNik = $this->currentNik();
            }

            $recipients = [];
            if ($pesertaNik !== '' && $pesertaNik !== $penjawabNik) {
                $recipients[$pesertaNik] = [
                    'nik' => $pesertaNik,
                    'nama' => $pesertaNama,
                    'role' => 'Peserta',
                    'type' => 'peserta',
                ];
            }

            if ($penjawabRole === 'associate_manager') {
                if ($managerNik !== '' && $managerNik !== $penjawabNik && !isset($recipients[$managerNik])) {
                    $recipients[$managerNik] = [
                        'nik' => $managerNik,
                        'nama' => $managerNama,
                        'role' => 'Manager',
                        'type' => 'atasan',
                    ];
                }
            } else {
                if ($amNik !== '' && $amNik !== $penjawabNik && !isset($recipients[$amNik])) {
                    $recipients[$amNik] = [
                        'nik' => $amNik,
                        'nama' => $amNama,
                        'role' => 'Associate Manager',
                        'type' => 'atasan',
                    ];
                }
            }

            if (empty($recipients)) {
                return;
            }

            [$itemNo, $itemLabel] = $this->diskusiItemData($d);

            $penjawabNama = trim((string) $d->nama_penjawab);
            if ($penjawabNama === '') {
                $penjawabNama = trim((string) (Auth::user()->name ?? ''));
            }

            SendPkDiskusiAnswerNotificationJob::dispatch(
                $periode,
                $pesertaNik,
                $pesertaNama !== '' ? $pesertaNama : $pesertaNik,
                $itemNo,
                $itemLabel,
                (string) $d->pesan,
                (string) $d->jawaban,
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
            Log::error('PK diskusi answer notification dispatch failed', [
                'diskusi_id' => $d->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    private function exportData($pkId, $nik)
    {
        $pk = HrPk::with('details')->findOrFail($pkId);
        $me = $this->currentNik();
        $isSelf = $me === $nik;
        $isAdmin = in_array('hr_upload_file_template_ipp', Auth::user()->getAllPermissionCodenames(), true);
        $relation = $isSelf ? null : $this->atasanRelationForPeriod($pk->periode, $nik);
        abort_unless($relation !== null || $isSelf || $isAdmin, 403, 'Anda tidak berwenang mengunduh nilai ini.');

        $snap = PmsEmployeeSnapshot::attributesFor((string) $pk->periode, (string) $nik);
        $target = DB::table('hr_master_employee')
            ->where('NIK', $nik)
            ->first(['NIK', 'Nama', 'Jabatan', 'Departmen', 'Sub Departmen', 'Section', 'Level', 'Aktif', 'Tgl Masuk', 'Tgl Lahir']);
        if ($snap) {
            $target = (object) [
                'NIK' => $nik,
                'Nama' => $snap->nama ?: ($target->Nama ?? null),
                'Jabatan' => $snap->jabatan ?: ($target->Jabatan ?? null),
                'Departmen' => $snap->departemen ?: ($target->Departmen ?? null),
                'Sub Departmen' => $snap->sub_departemen ?: ($target->{'Sub Departmen'} ?? null),
                'Section' => $snap->section ?: ($target->Section ?? null),
                'Level' => ($snap->level !== null && $snap->level !== '') ? $snap->level : ($target->Level ?? null),
                'Aktif' => $target->Aktif ?? 'Y',
                'Tgl Masuk' => $snap->tanggal_join ? $snap->tanggal_join->format('Y-m-d') : ($target->{'Tgl Masuk'} ?? null),
                'Tgl Lahir' => $snap->tanggal_lahir ? $snap->tanggal_lahir->format('Y-m-d') : ($target->{'Tgl Lahir'} ?? null),
            ];
        }
        abort_if(!$target || !$this->isParticipantForPeriod($pk->periode, $target), 422, 'Karyawan bukan peserta Penilaian Karya.');
        $group = $this->groupForPeriod($pk->periode, $nik, $target->Level);

        $details = $pk->details
            ->filter(fn ($d) => in_array($d->level_group, ['all', $group], true))
            ->values();
        abort_if($details->isEmpty(), 422, 'Master PK tidak memiliki rubrik untuk group ' . $group . '.');

        $nilai = HrPkNilai::where('hr_pk_id', $pk->id)->where('nik', $nik)->get()->keyBy('hr_pk_detail_id');

        $rows = $details->map(function ($d) use ($nilai) {
            $n = $nilai[$d->id] ?? null;

            return [
                'no_urut' => $d->no_urut,
                'section' => $d->section,
                'category' => $d->category,
                'area' => $d->area,
                'competency_behavior' => $d->competency_behavior,
                'key_behavior' => $d->key_behavior,
                'label' => $this->detailLabel($d),
                'kriteria_1' => $d->kriteria_1,
                'kriteria_2' => $d->kriteria_2,
                'kriteria_3' => $d->kriteria_3,
                'kriteria_4' => $d->kriteria_4,
                'kriteria_5' => $d->kriteria_5,
                'nilai_self' => $n ? $n->nilai_self : null,
                'nilai_leader' => $n ? $n->nilai_leader : null,
            ];
        })->all();

        $jumlahBawahan = ($snap && $snap->jumlah_bawahan !== null)
            ? (int) $snap->jumlah_bawahan
            : $this->jumlahBawahanData($pk->id, $nik)[0];
        $noLeadership = in_array(trim((string) $group), ['2-3', '4-5'], true) && $jumlahBawahan === 0;

        $avgRows = $noLeadership
            ? array_filter($rows, fn ($r) => !PkBawahan::isLeadershipDetail((object) $r))
            : $rows;

        $selfScores = array_filter(array_map(fn ($r) => $r['nilai_self'], $avgRows), fn ($v) => $v !== null);
        $leaderScores = array_filter(array_map(fn ($r) => $r['nilai_leader'], $avgRows), fn ($v) => $v !== null);

        $showLeader = true;

        // Hanya Manager / Admin yang dapat melihat akumulasi penilaian;
        // peserta (self) dan Associate Manager tidak menampilkan accumulation.
        $showAccumulation = !$isSelf && $relation !== 'associate_manager';

        $wingsRows = array_filter($rows, function ($r) {
            $cat = strtoupper(trim((string) ($r['category'] ?? '')));
            $sec = strtoupper(trim((string) ($r['section'] ?? '')));
            return str_contains($cat, 'CORE VALUE') || str_contains($sec, 'CORE VALUE');
        });

        $leadRows = array_filter($rows, function ($r) {
            $cat = strtoupper(trim((string) ($r['category'] ?? '')));
            $sec = strtoupper(trim((string) ($r['section'] ?? '')));
            return str_contains($cat, 'LEADERSHIP') || str_contains($sec, 'LEADERSHIP');
        });

        $behaviorRows = array_filter($rows, function ($r) {
            $cat = strtoupper(trim((string) ($r['category'] ?? '')));
            $sec = strtoupper(trim((string) ($r['section'] ?? '')));
            return str_contains($cat, 'BEHAVIOR') || str_contains($cat, 'SIKAP KERJA')
                || str_contains($sec, 'BEHAVIOR') || str_contains($sec, 'SIKAP KERJA');
        });

        $calcAvg = function ($items, $field) {
            $scores = array_filter(array_map(fn ($r) => $r[$field], $items), fn ($v) => $v !== null && $v !== '');
            if (empty($scores)) {
                return null;
            }
            return array_sum($scores) / count($scores);
        };

        $wingsSelfAvg = $calcAvg($wingsRows, 'nilai_self');
        $wingsLeaderAvg = $calcAvg($wingsRows, 'nilai_leader');
        $behaviorSelfAvg = $calcAvg($behaviorRows, 'nilai_self');
        $behaviorLeaderAvg = $calcAvg($behaviorRows, 'nilai_leader');
        $leadSelfAvg = $calcAvg($leadRows, 'nilai_self');
        $leadLeaderAvg = $calcAvg($leadRows, 'nilai_leader');

        if ($noLeadership) {
            $leadSelfAvg = null;
            $leadLeaderAvg = null;
        }

        $finalSelfNumeric = null;
        if ($wingsSelfAvg !== null && $leadSelfAvg !== null) {
            $finalSelfNumeric = round(($wingsSelfAvg * 0.60) + ($leadSelfAvg * 0.40), 2);
        } elseif ($wingsSelfAvg !== null) {
            $finalSelfNumeric = round($wingsSelfAvg, 2);
        }

        $finalLeaderNumeric = null;
        if ($wingsLeaderAvg !== null && $leadLeaderAvg !== null) {
            $finalLeaderNumeric = round(($wingsLeaderAvg * 0.60) + ($leadLeaderAvg * 0.40), 2);
        } elseif ($wingsLeaderAvg !== null) {
            $finalLeaderNumeric = round($wingsLeaderAvg, 2);
        }

        $calcGrade = function ($score) {
            if ($score === null) return '-';
            if ($score >= 4.2) return 'A';
            if ($score >= 3.8) return 'B';
            if ($score >= 3.0) return 'C';
            if ($score >= 2.0) return 'D';
            if ($score >= 1.0) return 'E';
            return '-';
        };

        $accumulation = [
            'wings_self' => $wingsSelfAvg !== null ? round($wingsSelfAvg, 2) : null,
            'wings_leader' => $wingsLeaderAvg !== null ? round($wingsLeaderAvg, 2) : null,
            'behavior_self' => $behaviorSelfAvg !== null ? round($behaviorSelfAvg, 2) : null,
            'behavior_leader' => $behaviorLeaderAvg !== null ? round($behaviorLeaderAvg, 2) : null,
            'leadership_self' => $leadSelfAvg !== null ? round($leadSelfAvg, 2) : null,
            'leadership_leader' => $leadLeaderAvg !== null ? round($leadLeaderAvg, 2) : null,
            'final_score_self' => $finalSelfNumeric,
            'final_score_leader' => $finalLeaderNumeric,
            'final_grade_self' => $calcGrade($finalSelfNumeric),
            'final_grade_leader' => $calcGrade($finalLeaderNumeric),
        ];

        $atasan = (new PkAtasan())->resolveAll($target);
        if ($snap && ($snap->manager_nik || $snap->manager_nama)) {
            $atasan['manager'] = ['nik' => $snap->manager_nik, 'nama' => $snap->manager_nama];
            $atasan['associate_manager'] = $snap->associate_manager_nik
                ? ['nik' => $snap->associate_manager_nik, 'nama' => $snap->associate_manager_nama]
                : null;
        }

        $amApprovedRow = $nilai->filter(fn ($n) => $n->am_approved_by !== null)->sortByDesc('am_approved_at')->first();
        $mgrApprovedRow = $nilai->filter(fn ($n) => $n->mgr_approved_by !== null)->sortByDesc('mgr_approved_at')->first();

        $approverUsernames = array_values(array_filter([
            $amApprovedRow->am_approved_by ?? null,
            $mgrApprovedRow->mgr_approved_by ?? null,
        ]));
        $approverRows = $approverUsernames
            ? DB::table('users')->whereIn('username', $approverUsernames)->get(['username', 'name', 'nik'])->keyBy('username')
            : collect();

        $buildApproval = function (?array $atasanRow, $approvedRow, string $byCol, string $atCol) use ($approverRows): array {
            $approved = $approvedRow !== null;
            $username = $approved ? trim((string) $approvedRow->{$byCol}) : '';
            $approver = $username !== '' ? ($approverRows[$username] ?? null) : null;
            $approverName = trim((string) ($approver->name ?? ''));
            $approverNik = trim((string) ($approver->nik ?? ''));

            return [
                'nama' => $approved
                    ? ($approverName !== '' ? $approverName : $username)
                    : ($atasanRow['nama'] ?? null),
                'nik' => $approved
                    ? ($approverNik !== '' ? $approverNik : '-')
                    : ($atasanRow['nik'] ?? null),
                'approved' => $approved,
                'approved_at' => $approved ? $approvedRow->{$atCol} : null,
            ];
        };

        $approval = [
            'am' => $buildApproval($atasan['associate_manager'] ?? null, $amApprovedRow, 'am_approved_by', 'am_approved_at'),
            'manager' => $buildApproval($atasan['manager'] ?? null, $mgrApprovedRow, 'mgr_approved_by', 'mgr_approved_at'),
        ];

        $rawDept = trim((string) ($target->Departmen ?? ''));
        $rawSub = trim((string) ($target->{'Sub Departmen'} ?? ''));
        $rawSection = trim((string) ($target->Section ?? ''));

        $isFactory = strcasecmp($rawDept, 'factory') === 0;

        if ($isFactory) {
            $displayDept = $rawSub !== '' ? $rawSub : $rawDept;
            $displayBagian = $rawSection !== '' ? $rawSection : ($rawSub !== '' ? $rawSub : '-');
        } else {
            $displayDept = $rawDept !== '' ? $rawDept : '-';
            $displayBagian = $rawSub !== '' ? $rawSub : ($rawSection !== '' ? $rawSection : '-');
        }

        return [
            'header' => [
                'periode' => $pk->periode,
                'nik' => $target->NIK,
                'nama' => $target->Nama,
                'jabatan' => $target->Jabatan,
                'departemen' => $displayDept,
                'sub_departemen' => $displayBagian,
                'bagian' => $displayBagian,
                'level' => (int) $target->Level,
                'group' => $group,
                'jumlah_bawahan' => $jumlahBawahan,
                'manager' => $atasan['manager']['nama'] ?? null,
                'manager_nik' => $atasan['manager']['nik'] ?? null,
                'associate_manager' => in_array($group, ['2-3', '4-5'], true) ? ($atasan['associate_manager']['nama'] ?? null) : null,
                'pendidikan' => ($snap && $snap->pendidikan)
                    ? $snap->pendidikan
                    : (string) (DB::table('hr_master_employee_pendidikan')
                        ->where('nik', $target->NIK)
                        ->value('pendidikan') ?? ''),
                'tgl_masuk' => !empty($target->{'Tgl Masuk'}) ? \Carbon\Carbon::parse($target->{'Tgl Masuk'})->format('d M Y') : '-',
                'usia' => ($snap && $snap->usia !== null)
                    ? $snap->usia . ' Thn'
                    : (!empty($target->{'Tgl Lahir'}) ? \Carbon\Carbon::parse($target->{'Tgl Lahir'})->age . ' Thn' : '-'),
            ],
            'rows' => $rows,
            'avg_self' => $selfScores ? round(array_sum($selfScores) / count($selfScores), 2) : null,
            'avg_leader' => $showLeader && $leaderScores ? round(array_sum($leaderScores) / count($leaderScores), 2) : null,
            'showLeader' => $showLeader,
            'showAccumulation' => $showAccumulation,
            'no_leadership' => $noLeadership,
            'isFinal' => $nilai->contains(fn ($n) => $n->mgr_approved_by !== null),
            'accumulation' => $accumulation,
            'approval' => $approval,
        ];
    }

    public function exportPdf($pkId, $nik)
    {
        $data = $this->exportData($pkId, $nik);
        $data['logoSrc'] = $this->logoDataUri('hr_ipp/Logo_pas.png');
        $data['logoWingsSrc'] = $this->logoDataUri('hr_ipp/Logo_wings.png');

        $pdf = PDF::loadView('hr.pk-assessment.export-pdf', $data)->setPaper('A4', 'landscape');

        return $pdf->download('PK_' . $data['header']['nik'] . '_' . preg_replace('/[^A-Za-z0-9]+/', '_', $data['header']['nama']) . '_' . $data['header']['periode'] . '.pdf');
    }

    private function logoDataUri($relPath)
    {
        $path = public_path($relPath);
        if (!is_file($path)) {
            return '';
        }
        $mime = function_exists('mime_content_type') ? mime_content_type($path) : 'image/png';

        return 'data:' . $mime . ';base64,' . base64_encode(file_get_contents($path));
    }
}
