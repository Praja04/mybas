<?php

namespace App\Http\Controllers\HR;

use App\Exports\HR\PmsTotalScoringExport;
use App\Http\Controllers\Controller;
use App\Models\HR\HrIpp;
use App\Models\HR\HrIppOtorisasi;
use App\Models\HR\HrPk;
use App\Models\HR\HrPmsEmployeeSnapshot;
use App\Models\HR\HrPmsControlNotifikasi;
use App\Models\HR\HrPmsLockingIpp;
use App\Models\HR\HrPmsLockingPascale;
use App\Models\HR\HrPmsLockingPk;
use App\Models\HR\HrPmsNormalScale;
use App\Models\HR\HrPmsPascoreFinal;
use App\Support\IppTahapan;
use App\Support\PaScaleNormalizer;
use App\Support\PkBawahan;
use App\Support\PmsEmployeeSnapshot;
use App\Support\IppForemanPeserta;
use App\Support\PkComponentScore;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Facades\Excel;

class PmsTotalScoringController extends Controller
{
    /**
     * Bobot Hasil Formulasi: 70% IPP + 30% PK.
     */
    private const FINAL_IPP_WEIGHT = 0.7;
    private const FINAL_PK_WEIGHT = 0.3;

    /**
     * Kolom tabel yang dapat diurutkan dari header (asc/desc).
     */
    private const SORTABLE_COLUMNS = ['final_score', 'pa_scale', 'pa_scale_rank', 'pa_scale_final'];

    /**
     * Cache per NIK: instance controller dapat dipakai ulang antar request
     * (Laravel 7 meng-cache controller di objek Route), jadi cache tidak boleh
     * hanya disimpan per instance tanpa membedakan user login.
     */
    private $scoringPermissionCache = [];
    private $pmsManagerCache = [];
    private $managerTeamKeysCache = [];

    private function checkPermission(string $codename): void
    {
        $userCodenames = Auth::user()->getAllPermissionCodenames();
        if (!in_array($codename, $userCodenames, true)) {
            abort(403);
        }
    }

    private function currentNik(): string
    {
        return trim((string) (Auth::user()->nik ?? ''));
    }

    /**
     * Admin HR (punya permission halaman ini) melihat seluruh karyawan.
     */
    private function hasScoringPermission(): bool
    {
        $nik = $this->currentNik();
        if (!array_key_exists($nik, $this->scoringPermissionCache)) {
            $this->scoringPermissionCache[$nik] = in_array(
                'hr_upload_file_template_ipp',
                (array) Auth::user()->getAllPermissionCodenames(),
                true
            );
        }

        return $this->scoringPermissionCache[$nik];
    }

    /**
     * User login terdaftar sebagai manager_nik di snapshot PMS.
     */
    private function isPmsManager(): bool
    {
        $nik = $this->currentNik();
        if (!array_key_exists($nik, $this->pmsManagerCache)) {
            $this->pmsManagerCache[$nik] = $nik !== ''
                && HrPmsEmployeeSnapshot::where('manager_nik', $nik)->exists();
        }

        return $this->pmsManagerCache[$nik];
    }

    /**
     * Bukan admin, hanya atasan: daftar dibatasi ke anak buah langsung.
     */
    private function isManagerView(): bool
    {
        return !$this->hasScoringPermission() && $this->isPmsManager();
    }

    /**
     * Kunci "periode|nik" anak buah langsung (manager_nik) milik user login.
     * Null bila bukan manager view (admin / bukan atasan).
     */
    private function managerTeamKeys(): ?array
    {
        $nik = $this->currentNik();
        if (array_key_exists($nik, $this->managerTeamKeysCache)) {
            return $this->managerTeamKeysCache[$nik];
        }

        if (!$this->isManagerView()) {
            return $this->managerTeamKeysCache[$nik] = null;
        }

        $keys = [];
        foreach (HrPmsEmployeeSnapshot::where('manager_nik', $nik)->get(['periode', 'nik']) as $row) {
            $periode = PmsEmployeeSnapshot::normalizePeriode((string) $row->periode);
            $teamNik = trim((string) $row->nik);
            if ($periode !== '' && $teamNik !== '') {
                $keys[$periode . '|' . $teamNik] = true;
            }
        }

        return $this->managerTeamKeysCache[$nik] = $keys;
    }

    /**
     * Admin HR ATAU atasan (manager_nik) yang boleh mengisi PA Scale Calibrated timnya.
     */
    private function ensureScoringAccess(): void
    {
        if ($this->hasScoringPermission() || $this->isPmsManager()) {
            return;
        }

        abort(403);
    }

    private function groupOf($level): string
    {
        return PmsEmployeeSnapshot::groupOf($level) ?? '4-5';
    }

    private function paGrade($score): ?string
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

    /**
     * Peringkat mutu grade untuk pengurutan: E paling rendah, A paling tinggi.
     * Grade tak dikenal dianggap paling rendah.
     */
    private function gradeOrder($grade): int
    {
        $map = ['E' => 0, 'D' => 1, 'C' => 2, 'B' => 3, 'A' => 4];

        return $map[strtoupper(trim((string) $grade))] ?? -1;
    }

    /**
     * Semua tahun yang tersedia (dari periode IPP maupun PK), urut menurun.
     */
    private function periodeOptions(): array
    {
        $years = [];

        foreach (DB::table('hr_ipp_aktual')->distinct()->pluck('tahapan') as $tahapan) {
            if (preg_match('/(\d{4})/', (string) $tahapan, $m)) {
                $years[$m[1]] = true;
            }
        }
        foreach (DB::table('hr_pk')->pluck('periode') as $periode) {
            if (preg_match('/(\d{4})/', (string) $periode, $m)) {
                $years[$m[1]] = true;
            }
        }

        $out = array_map('strval', array_keys($years));
        rsort($out);

        return $out;
    }

    /**
     * Tahun yang benar-benar punya data (IPP END atau nilai PK).
     */
    private function dataYears(): array
    {
        $years = [];

        foreach (DB::table('hr_ipp_aktual')
            ->whereNotNull('score_scale')
            ->where('tahapan', 'like', 'END %')
            ->distinct()
            ->pluck('tahapan') as $tahapan) {
            if (preg_match('/(\d{4})/', (string) $tahapan, $m)) {
                $years[$m[1]] = true;
            }
        }

        $pkIds = DB::table('hr_pk_nilai')->distinct()->pluck('hr_pk_id')->all();
        if ($pkIds) {
            foreach (DB::table('hr_pk')->whereIn('id', $pkIds)->pluck('periode') as $periode) {
                if (preg_match('/(\d{4})/', (string) $periode, $m)) {
                    $years[$m[1]] = true;
                }
            }
        }

        $out = array_map('strval', array_keys($years));
        rsort($out);

        return $out;
    }

    /**
     * Tahun yang punya skor IPP END (sumber utama halaman ini).
     */
    private function ippEndYears(): array
    {
        $years = [];

        foreach (DB::table('hr_ipp_aktual')
            ->whereNotNull('score_scale')
            ->where('tahapan', 'like', 'END %')
            ->distinct()
            ->pluck('tahapan') as $tahapan) {
            if (preg_match('/(\d{4})/', (string) $tahapan, $m)) {
                $years[$m[1]] = true;
            }
        }

        $out = array_map('strval', array_keys($years));
        rsort($out);

        return $out;
    }

    private function defaultPeriode(): string
    {
        $ippEnd = $this->ippEndYears();
        if ($ippEnd) {
            return $ippEnd[0];
        }

        $withData = $this->dataYears();
        if ($withData) {
            return $withData[0];
        }

        $all = $this->periodeOptions();

        return $all[0] ?? (string) date('Y');
    }

    public function index()
    {
        $this->ensureScoringAccess();

        $canManageNotifications = $this->hasScoringPermission();

        return view('hr.pms-total-scoring.pms-total-scoring', [
            'periodes' => $this->periodeOptions(),
            'defaultPeriode' => $this->defaultPeriode(),
            'deptOptions' => $this->scopedDeptOptions($this->defaultPeriode()),
            'isManagerView' => $this->isManagerView(),
            'canManageNotifications' => $canManageNotifications,
            'notificationControl' => $canManageNotifications ? HrPmsControlNotifikasi::state() : null,
        ]);
    }

    /**
     * Daftar pilihan departemen/bagian untuk filter.
     * Snapshot periode terpilih diprioritaskan, master hanya pelengkap.
     */
    private function deptOptions(?string $periode = null): array
    {
        return PmsEmployeeSnapshot::deptOptions($periode);
    }

    /**
     * Filter dept mengikuti cakupan user: admin melihat semua departemen,
     * atasan hanya departemen/bagian anak buah langsungnya.
     */
    private function scopedDeptOptions(?string $periode = null): array
    {
        if ($this->isManagerView()) {
            return PmsEmployeeSnapshot::deptOptionsForManager($this->currentNik(), $periode);
        }

        return $this->deptOptions($periode);
    }

    public function list(Request $request)
    {
        $this->ensureScoringAccess();

        $perPage = max(1, min(100, (int) $request->get('per_page', 20)));
        $page = max(1, (int) $request->get('page', 1));

        $built = $this->buildScoringData($request);
        $rows = $built['rows'];
        $total = count($rows);
        $lastPage = max(1, (int) ceil($total / $perPage));
        $page = min($page, $lastPage);

        return response()->json([
            'success' => true,
            'periode' => $built['periode'],
            'data' => array_slice($rows, ($page - 1) * $perPage, $perPage),
            'summary' => $built['summary'],
            'dept_options' => $built['dept_options'],
            'locking' => $built['locking'],
            'can_edit_locked' => $this->hasScoringPermission(),
            'meta' => [
                'page' => $page,
                'per_page' => $perPage,
                'total' => $total,
                'last_page' => $lastPage,
            ],
        ]);
    }

    /**
     * Normalisasi filter PA Scale (jenis + grade) dari request.
     * Jenis kosong + grade terisi diartikan "salah satu kolom PA Scale".
     *
     * @return array{type: string, grade: string}
     */
    private function paScaleFilters(Request $request): array
    {
        $type = strtolower(trim((string) $request->get('pa_scale_type', '')));
        if (!in_array($type, ['original', 'normalize', 'calibrated'], true)) {
            $type = '';
        }

        $grade = strtoupper(trim((string) $request->get('pa_scale_grade', '')));
        if (!in_array($grade, ['A', 'B', 'C', 'D', 'E'], true)) {
            $grade = '';
        }

        return ['type' => $type, 'grade' => $grade];
    }

    /**
     * Kumpulkan seluruh baris Total Scoring (tanpa paginasi) beserta ringkasannya.
     *
     * @return array{periode: string, rows: array, summary: ?array, dept_options: array}
     */
    private function buildScoringData(Request $request): array
    {
        $search = trim((string) $request->get('search', ''));
        $fPeriode = trim((string) $request->get('periode', ''));
        $fGroup = trim((string) $request->get('group', ''));
        $fDept = trim((string) $request->get('departemen', ''));
        $fDeptType = $request->get('dept_type') === 'sub' ? 'sub' : 'dept';
        $fPa = $this->paScaleFilters($request);
        $fPaType = $fPa['type'];
        $fPaGrade = $fPa['grade'];

        $periodes = $this->periodeOptions();
        if ($fPeriode !== '') {
            $years = in_array($fPeriode, $periodes, true) ? [$fPeriode] : [$this->defaultPeriode()];
        } else {
            $years = $periodes;
        }

        $deptOptions = $this->scopedDeptOptions(
            ($fPeriode !== '' && in_array($fPeriode, $periodes, true)) ? $fPeriode : null
        );

        $blank = [
            'periode' => $fPeriode,
            'rows' => [],
            'summary' => null,
            'dept_options' => $deptOptions,
            'locking' => [],
        ];

        if (!$years) {
            return $blank;
        }

        $pkIdByYear = [];
        $candByYear = [];
        $allNiks = [];
        $rubricIppIdByYear = DB::table('hr_ipp')
            ->where('template_type', 'rubric')
            ->whereIn('periode', $years)
            ->orderBy('id')
            ->get(['id', 'periode'])
            ->groupBy('periode')
            ->map(fn ($rows) => (int) $rows->last()->id)
            ->all();
        foreach ($years as $year) {
            $pkId = DB::table('hr_pk')->where('periode', $year)->orderByDesc('id')->value('id');
            $pkIdByYear[$year] = $pkId !== null ? (int) $pkId : null;

            $ippNiks = DB::table('hr_ipp_aktual as a')
                ->join('hr_ipp_otorisasi as o', 'o.id', '=', 'a.ipp_otorisasi_id')
                ->where('a.tahapan', 'END ' . $year)
                ->whereNotNull('a.score_scale')
                ->distinct()
                ->pluck('o.nik_yang_bersangkutan')
                ->all();

            $ippRubricNiks = DB::table('hr_ipp_foreman_nilai')
                ->where('tahapan', 'END ' . $year)
                ->whereNotNull('nilai')
                ->distinct()
                ->pluck('nik')
                ->all();

            $pkNiks = $pkIdByYear[$year]
                ? DB::table('hr_pk_nilai')->where('hr_pk_id', $pkIdByYear[$year])->distinct()->pluck('nik')->all()
                : [];

            $candByYear[$year] = array_values(array_unique(array_filter(
                array_merge($ippNiks, $ippRubricNiks, $pkNiks),
                fn ($n) => trim((string) $n) !== ''
            )));
            $allNiks = array_merge($allNiks, $candByYear[$year]);
        }

        $allNiks = array_values(array_unique($allNiks));
        if (!$allNiks) {
            return $blank;
        }

        $snapMap = PmsEmployeeSnapshot::mapFor($years, $allNiks);
        $paFinalMap = HrPmsPascoreFinal::mapFor($years, $allNiks);
        $lockMap = HrPmsLockingPascale::lockedMap($years);

        $emps = DB::table('hr_master_employee')->whereIn('NIK', $allNiks)
            ->get(['NIK', 'Nama', 'Jabatan', 'Departmen', 'Sub Departmen', 'Section', 'Level'])
            ->keyBy('NIK');

        $knownNiks = [];
        foreach ($allNiks as $n) {
            $knownNiks[$n] = true;
        }
        foreach ($snapMap as $snapRow) {
            $knownNiks[$snapRow->nik] = true;
        }
        $knownNiks = array_keys($knownNiks);

        $rows = [];
        foreach ($years as $year) {
            $pkId = $pkIdByYear[$year];
            $yearNiks = array_values(array_intersect($knownNiks, $candByYear[$year]));
            if (!$yearNiks) {
                continue;
            }

            $ippAgg = DB::table('hr_ipp_aktual as a')
                ->join('hr_ipp_otorisasi as o', 'o.id', '=', 'a.ipp_otorisasi_id')
                ->whereIn('o.nik_yang_bersangkutan', $yearNiks)
                ->where('a.tahapan', 'END ' . $year)
                ->whereNotNull('a.score_scale')
                ->selectRaw('o.nik_yang_bersangkutan as nik, MAX(o.id) as oto_id, AVG(a.score_scale) as avg_score')
                ->groupBy('o.nik_yang_bersangkutan')
                ->get()
                ->keyBy('nik');

            $ippForemanAgg = DB::table('hr_ipp_foreman_nilai')
                ->whereIn('nik', $yearNiks)
                ->where('tahapan', 'END ' . $year)
                ->whereNotNull('nilai')
                ->selectRaw('nik, AVG(nilai) as avg_score')
                ->groupBy('nik')
                ->get()
                ->keyBy('nik');

            $pkDetails = collect();
            $pkNilaiMap = collect();
            if ($pkId) {
                $pkDetails = DB::table('hr_pk_detail')
                    ->where('hr_pk_id', $pkId)
                    ->get(['id', 'category', 'section', 'level_group'])
                    ->keyBy('id');

                $pkNilaiMap = DB::table('hr_pk_nilai')
                    ->where('hr_pk_id', $pkId)
                    ->whereIn('nik', $yearNiks)
                    ->get(['nik', 'hr_pk_detail_id', 'nilai_self', 'nilai_leader'])
                    ->groupBy('nik');
            }

            foreach ($yearNiks as $nik) {
                $snap = $snapMap[$year . '|' . $nik] ?? null;
                $paFinal = $paFinalMap[$year . '|' . $nik] ?? null;
                $emp = $emps[$nik] ?? null;
                if (!$snap && !$emp) {
                    continue;
                }

                $level = ($snap && $snap->level !== null && $snap->level !== '')
                    ? $snap->level
                    : ($emp->Level ?? null);
                $group = ($snap && trim((string) $snap->level_group) !== '')
                    ? trim((string) $snap->level_group)
                    : $this->groupOf($level);
                $bawahan = ($snap && $snap->jumlah_bawahan !== null)
                    ? (int) $snap->jumlah_bawahan
                    : PkBawahan::resolveForTarget($pkId ?? 0, $nik)[0];
                $noLeadership = in_array(trim((string) $group), ['2-3', '4-5'], true) && $bawahan === 0;

                $rubricRow = $ippForemanAgg[$nik] ?? null;
                $kpiRow = $ippAgg[$nik] ?? null;
                $ipp = $rubricRow
                    ? round((float) $rubricRow->avg_score, 2)
                    : ($kpiRow ? round((float) $kpiRow->avg_score, 2) : null);
                $otoId = (!$rubricRow && $kpiRow) ? (int) $kpiRow->oto_id : null;
                $rubricIppId = $rubricRow ? ($rubricIppIdByYear[$year] ?? null) : null;

                $pkComponents = PkComponentScore::components($pkNilaiMap[$nik] ?? collect(), $pkDetails, $group, $noLeadership);
                $pk = PkComponentScore::score($pkComponents, $group);

                $final = ($ipp !== null && $pk !== null)
                    ? round(($ipp * self::FINAL_IPP_WEIGHT) + ($pk * self::FINAL_PK_WEIGHT), 2)
                    : null;

                $departemen = ($snap && $snap->departemen) ? $snap->departemen : ($emp->Departmen ?? '');
                $subDepartemen = ($snap && $snap->sub_departemen) ? $snap->sub_departemen : ($emp->{'Sub Departmen'} ?? '');
                $section = ($snap && $snap->section) ? $snap->section : ($emp->Section ?? '');
                $display = PmsEmployeeSnapshot::displayDeptBagian($departemen, $subDepartemen, $section);

                $rows[] = [
                    'periode' => $year,
                    'nik' => $nik,
                    'nama' => ($snap && $snap->nama) ? $snap->nama : ($emp->Nama ?? ''),
                    'jabatan' => ($snap && $snap->jabatan) ? $snap->jabatan : ($emp->Jabatan ?? ''),
                    'departemen' => $departemen,
                    'sub_departemen' => $subDepartemen,
                    'dept_label' => $display['dept'],
                    'bagian' => $display['bagian'],
                    'section' => $section,
                    'level' => $level,
                    'group' => $group,
                    'punya_bawahan' => $bawahan,
                    'no_leadership' => $noLeadership,
                    'ipp_score' => $ipp,
                    'pk_score' => $pk,
                    'pk_components' => $pkComponents,
                    'final_score' => $final,
                    'pa_scale' => $this->paGrade($final),
                    'pa_scale_rank' => null,
                    'pa_scale_final' => $paFinal ? $paFinal->pa_scale_final : null,
                    'pa_final_locked' => !empty($lockMap[$year]) && !$this->hasScoringPermission(),
                    'pa_final_by' => $paFinal ? $paFinal->updated_by : null,
                    'pa_final_at' => ($paFinal && $paFinal->updated_at) ? $paFinal->updated_at->format('d/m/Y H:i') : null,
                    'form_ipp_url' => ($rubricRow && $rubricIppId)
                        ? url('/hr/pms-total-scoring/export-ipp-foreman/' . $rubricIppId . '/' . $nik)
                        : ($otoId ? url('/hr/pms-total-scoring/export-ipp/' . $otoId) : null),
                    'form_pk_url' => $pkId ? url('/hr/pk-assessment/export-pdf/' . $pkId . '/' . $nik) : null,
                ];
            }
        }

        $rankStats = $this->applyRankDistribution($rows);

        $teamKeys = $this->managerTeamKeys();

        if ($teamKeys !== null || $fGroup !== '' || $fDept !== '' || $search !== '' || $fPaGrade !== '') {
            $needle = $search !== '' ? mb_strtolower($search) : null;
            $rows = array_values(array_filter($rows, function ($r) use ($teamKeys, $fGroup, $fDept, $fDeptType, $fPaType, $fPaGrade, $needle) {
                if ($teamKeys !== null && !isset($teamKeys[$r['periode'] . '|' . $r['nik']])) {
                    return false;
                }
                if ($fGroup !== '') {
                    $rowGroup = (string) $r['group'];
                    $groupMatch = $fGroup === '4-7'
                        ? in_array($rowGroup, ['4-5', '6-7'], true)
                        : $rowGroup === $fGroup;

                    if (!$groupMatch) {
                        return false;
                    }
                }
                if ($fDept !== '') {
                    $dept = trim((string) $r['departemen']);
                    if ($fDeptType === 'sub') {
                        if (strtolower($dept) !== 'factory' || trim((string) $r['sub_departemen']) !== $fDept) {
                            return false;
                        }
                    } elseif (strtolower($dept) === 'factory' || $dept !== $fDept) {
                        return false;
                    }
                }
                if ($fPaGrade !== '') {
                    $paColumns = $fPaType === 'original'
                        ? ['pa_scale']
                        : ($fPaType === 'normalize'
                            ? ['pa_scale_rank']
                            : ($fPaType === 'calibrated'
                                ? ['pa_scale_final']
                                : ['pa_scale', 'pa_scale_rank', 'pa_scale_final']));

                    $paMatch = false;
                    foreach ($paColumns as $column) {
                        if (strtoupper(trim((string) ($r[$column] ?? ''))) === $fPaGrade) {
                            $paMatch = true;
                            break;
                        }
                    }
                    if (!$paMatch) {
                        return false;
                    }
                }
                if ($needle !== null) {
                    $hay = mb_strtolower(implode(' ', [
                        (string) $r['nik'],
                        (string) $r['nama'],
                        (string) $r['jabatan'],
                        (string) $r['departemen'],
                        (string) $r['section'],
                        (string) $r['sub_departemen'],
                    ]));
                    if (mb_strpos($hay, $needle) === false) {
                        return false;
                    }
                }

                return true;
            }));
        }

        $defaultCompare = function ($a, $b) {
            $pa = (int) $a['periode'];
            $pb = (int) $b['periode'];
            if ($pa !== $pb) {
                return $pb <=> $pa;
            }

            $la = (int) $a['level'];
            $lb = (int) $b['level'];
            if ($la !== $lb) {
                return $la <=> $lb;
            }

            return strcmp((string) $a['nama'], (string) $b['nama']);
        };

        $sortKey = trim((string) $request->get('sort', ''));
        $sortDesc = $request->get('dir') === 'desc';

        if (!in_array($sortKey, self::SORTABLE_COLUMNS, true)) {
            usort($rows, $defaultCompare);
        } else {
            usort($rows, function ($a, $b) use ($sortKey, $sortDesc, $defaultCompare) {
                $va = $a[$sortKey];
                $vb = $b[$sortKey];

                $na = $va === null || $va === '';
                $nb = $vb === null || $vb === '';
                if ($na || $nb) {
                    if ($na && $nb) {
                        return $defaultCompare($a, $b);
                    }

                    return $na ? 1 : -1;
                }

                if ($sortKey === 'final_score') {
                    $cmp = (float) $va <=> (float) $vb;
                } else {
                    $cmp = $this->gradeOrder($va) <=> $this->gradeOrder($vb);
                }

                if ($cmp === 0) {
                    return $defaultCompare($a, $b);
                }

                return $sortDesc ? -$cmp : $cmp;
            });
        }

        $summaryPeriode = ($fPeriode !== '' && in_array($fPeriode, $periodes, true))
            ? $fPeriode
            : $this->defaultPeriode();

        $summary = $this->rankSummary($rows, $summaryPeriode, $rankStats[$summaryPeriode] ?? []);
        $summary['filtered'] = $teamKeys !== null || $fGroup !== '' || $fDept !== '' || $search !== '' || $fPaGrade !== '';

        return [
            'periode' => $fPeriode,
            'rows' => $rows,
            'summary' => $summary,
            'dept_options' => $deptOptions,
            'locking' => $lockMap,
        ];
    }

    /**
     * Export Excel seluruh baris (mengikuti filter aktif) + ringkasan PA Scale.
     */
    public function export(Request $request)
    {
        $this->checkPermission('hr_upload_file_template_ipp');

        $built = $this->buildScoringData($request);

        $periode = trim((string) $request->get('periode', ''));
        $periodeLabel = ($periode !== '' && in_array($periode, $this->periodeOptions(), true))
            ? $periode
            : 'Semua';

        $paFilters = $this->paScaleFilters($request);

        $filters = [
            'group' => trim((string) $request->get('group', '')),
            'departemen' => trim((string) $request->get('departemen', '')),
            'dept_type' => $request->get('dept_type') === 'sub' ? 'sub' : 'dept',
            'search' => trim((string) $request->get('search', '')),
            'pa_scale_type' => $paFilters['grade'] !== '' ? $paFilters['type'] : '',
            'pa_scale_grade' => $paFilters['grade'],
        ];

        $filename = 'Hasil-Total-Scoring_Periode-' . $periodeLabel . '_' . now()->format('Ymd-His') . '.xlsx';

        return Excel::download(
            new PmsTotalScoringExport($built['rows'], $built['summary'], $periodeLabel, $filters),
            $filename
        );
    }

    /**
     * PA Scale Normalize: forced distribution dari ranking final_score per periode
     * dan per group (Level 2-3, Level 4-7/4-5+6-7).
     * Populasi = seluruh baris periode pada group tersebut (tidak terpengaruh filter),
     * kuota memakai persen kurva group yang sama; diterapkan bila N >= minimum peserta,
     * jika tidak PA Scale Normalize mengikuti PA Scale Original.
     *
     * @return array<string, array<string, array<string, mixed>>>
     */
    private function applyRankDistribution(array &$rows): array
    {
        $byPeriod = [];
        foreach ($rows as $i => $row) {
            if ($row['final_score'] === null) {
                continue;
            }
            $levelGroup = HrPmsNormalScale::bucketForGroup($row['group'] ?? null);
            $byPeriod[$row['periode']][$levelGroup][$i] = (float) $row['final_score'];
        }

        $periodes = array_keys($byPeriod);
        $distMaps = [];
        $minMaps = [];
        foreach (HrPmsNormalScale::GROUPS as $levelGroup) {
            $distMaps[$levelGroup] = HrPmsNormalScale::distributionMap($periodes, $levelGroup);
            $minMaps[$levelGroup] = HrPmsNormalScale::minimumMap($periodes, $levelGroup);
        }

        $stats = [];
        foreach ($byPeriod as $periode => $groups) {
            foreach ($groups as $levelGroup => $indexedScores) {
                $dist = $distMaps[$levelGroup][$periode] ?? null;
                $minN = $minMaps[$levelGroup][$periode] ?? PaScaleNormalizer::MIN_N;
                $n = count($indexedScores);
                $quota = PaScaleNormalizer::largestRemainderQuota($n, $dist);
                $applied = $n >= $minN;
                $grades = $applied ? PaScaleNormalizer::rankGrades(array_values($indexedScores), $dist) : [];

                foreach (array_keys($indexedScores) as $pos => $rowIndex) {
                    $rows[$rowIndex]['pa_scale_rank'] = $applied
                        ? ($grades[$pos] ?? null)
                        : $rows[$rowIndex]['pa_scale'];
                    $rows[$rowIndex]['normalize_group'] = $levelGroup;
                }

                $stats[$periode][$levelGroup] = [
                    'level_group' => $levelGroup,
                    'label' => HrPmsNormalScale::groupLabel($levelGroup),
                    'applied' => $applied,
                    'reason' => $applied ? null : 'min_n',
                    'n' => $n,
                    'minimum_n' => $minN,
                    'quota' => $quota,
                    'distribution' => PaScaleNormalizer::distribution($dist),
                    'percent' => PaScaleNormalizer::percentages($dist),
                    'is_custom' => isset($distMaps[$levelGroup][$periode]),
                ];
            }
        }

        return $stats;
    }

    /**
     * Ringkasan gabungan kedua group. Kuota = jumlah kursi kedua group;
     * persen = distribusi efektif berbobot jumlah peserta masing-masing group.
     *
     * @param  array<string, array<string, mixed>>  $groupStats
     */
    private function rankSummary(array $rows, string $periode, array $groupStats): array
    {
        $converted = PaScaleNormalizer::emptyCounts();
        $rank = PaScaleNormalizer::emptyCounts();
        $final = PaScaleNormalizer::emptyCounts();
        $numPeople = 0;

        foreach ($rows as $row) {
            if ($row['periode'] !== $periode || $row['final_score'] === null) {
                continue;
            }
            $numPeople++;
            if ($row['pa_scale'] !== null) {
                $converted[$row['pa_scale']] = ($converted[$row['pa_scale']] ?? 0) + 1;
            }
            if (!empty($row['pa_scale_rank']) && isset($rank[$row['pa_scale_rank']])) {
                $rank[$row['pa_scale_rank']]++;
            }
            if (!empty($row['pa_scale_final']) && isset($final[$row['pa_scale_final']])) {
                $final[$row['pa_scale_final']]++;
            }
        }

        $quota = PaScaleNormalizer::emptyCounts();
        $weighted = PaScaleNormalizer::emptyCounts();
        $groups = [];
        $activeGroups = 0;
        $appliedGroups = 0;
        $customGroups = [];
        $minimums = [];

        foreach (HrPmsNormalScale::GROUPS as $levelGroup) {
            $stat = $groupStats[$levelGroup] ?? null;
            if ($stat === null) {
                continue;
            }

            $n = (int) ($stat['n'] ?? 0);
            if ($n > 0) {
                $activeGroups++;
                $minimums[] = (int) ($stat['minimum_n'] ?? PaScaleNormalizer::MIN_N);
            }
            if (!empty($stat['applied'])) {
                $appliedGroups++;
            }
            if (!empty($stat['is_custom'])) {
                $customGroups[] = $levelGroup;
            }

            foreach (PaScaleNormalizer::grades() as $grade) {
                $quota[$grade] += (int) (($stat['quota'][$grade] ?? 0));
                $weighted[$grade] += ((float) (($stat['distribution'][$grade] ?? 0))) * $n;
            }

            $groups[$levelGroup] = [
                'level_group' => $levelGroup,
                'label' => $stat['label'] ?? HrPmsNormalScale::groupLabel($levelGroup),
                'n' => $n,
                'minimum_n' => (int) ($stat['minimum_n'] ?? PaScaleNormalizer::MIN_N),
                'applied' => (bool) ($stat['applied'] ?? false),
                'quota' => $stat['quota'] ?? PaScaleNormalizer::emptyCounts(),
                'distribution' => PaScaleNormalizer::distribution($stat['distribution'] ?? null),
                'percent' => PaScaleNormalizer::percentages($stat['distribution'] ?? null),
                'is_custom' => (bool) ($stat['is_custom'] ?? false),
            ];
        }

        $totalN = array_sum(array_map(function ($group) {
            return (int) ($group['n'] ?? 0);
        }, $groups));

        $blended = PaScaleNormalizer::emptyCounts();
        if ($totalN > 0) {
            foreach (PaScaleNormalizer::grades() as $grade) {
                $blended[$grade] = $weighted[$grade] / $totalN;
            }
        }

        $applied = $activeGroups > 0 && $appliedGroups === $activeGroups;
        $partial = $appliedGroups > 0 && $appliedGroups < $activeGroups;

        return [
            'periode' => $periode,
            'num_people' => $numPeople,
            'distribution' => PaScaleNormalizer::distribution($blended),
            'percent' => PaScaleNormalizer::percentages($blended),
            'converted' => $converted,
            'rank' => $rank,
            'rank_quota' => $quota,
            'rank_n' => $totalN,
            'rank_applied' => $applied,
            'rank_applied_partial' => $partial,
            'rank_reason' => $applied ? null : ($partial ? 'partial' : 'min_n'),
            'rank_minimum_n' => $minimums ? min($minimums) : PaScaleNormalizer::MIN_N,
            'rank_is_custom' => (bool) $customGroups,
            'rank_custom_groups' => $customGroups,
            'groups' => $groups,
            'final' => $final,
        ];
    }

    /**
     * Payload setelan kuota satu group (default bila belum pernah disimpan).
     *
     * @return array<string, mixed>
     */
    private function normalScaleGroupPayload(string $periode, ?string $levelGroup = null): array
    {
        $levelGroup = HrPmsNormalScale::bucketForGroup($levelGroup);
        $row = HrPmsNormalScale::where('periode', $periode)
            ->where('level_group', $levelGroup)
            ->first();

        $dist = $row ? $row->distribution() : null;
        $isCustom = PaScaleNormalizer::isValidDistribution($dist);
        $eff = $isCustom ? $dist : null;

        return [
            'level_group' => $levelGroup,
            'label' => HrPmsNormalScale::groupLabel($levelGroup),
            'distribution' => PaScaleNormalizer::distribution($eff),
            'percent' => PaScaleNormalizer::percentages($eff),
            'is_custom' => $isCustom,
            'minimum_peserta' => ($row && $row->minimum_peserta !== null)
                ? max(1, (int) $row->minimum_peserta)
                : PaScaleNormalizer::MIN_N,
            'updated_by' => $row ? $row->updated_by : null,
            'updated_at' => $row && $row->updated_at ? $row->updated_at->format('d/m/Y H:i') : null,
        ];
    }

    /**
     * Setting kurva normal (persen grade A-E) per periode untuk kedua group.
     */
    public function normalScaleGet(Request $request)
    {
        $this->checkPermission('hr_upload_file_template_ipp');

        $periodes = $this->periodeOptions();
        $periode = trim((string) $request->get('periode', ''));
        if ($periode === '' || !in_array($periode, $periodes, true)) {
            $periode = $this->defaultPeriode();
        }

        $groups = [];
        foreach (HrPmsNormalScale::GROUPS as $levelGroup) {
            $groups[$levelGroup] = $this->normalScaleGroupPayload($periode, $levelGroup);
        }

        return response()->json([
            'success' => true,
            'periode' => $periode,
            'periodes' => $periodes,
            'default_periode' => $this->defaultPeriode(),
            'groups' => $groups,
        ] + $groups[HrPmsNormalScale::GROUP_47]);
    }

    public function normalScaleSave(Request $request)
    {
        $this->checkPermission('hr_upload_file_template_ipp');

        if (is_array($request->input('groups'))) {
            return $this->normalScaleSaveGroups($request);
        }

        $validated = $request->validate([
            'periode' => 'required|regex:/^\d{4}$/',
            'level_group' => ['nullable', Rule::in(HrPmsNormalScale::GROUPS)],
            'grade_a' => 'required|integer|min:0|max:100',
            'grade_b' => 'required|integer|min:0|max:100',
            'grade_c' => 'required|integer|min:0|max:100',
            'grade_d' => 'required|integer|min:0|max:100',
            'minimum_peserta' => 'nullable|integer|min:1|max:1000',
        ]);

        $levelGroup = HrPmsNormalScale::bucketForGroup($validated['level_group'] ?? null);

        $a = (int) $validated['grade_a'];
        $b = (int) $validated['grade_b'];
        $c = (int) $validated['grade_c'];
        $d = (int) $validated['grade_d'];
        $minN = isset($validated['minimum_peserta']) && $validated['minimum_peserta'] !== ''
            ? max(1, (int) $validated['minimum_peserta'])
            : PaScaleNormalizer::MIN_N;

        if (($a + $b + $c + $d) > 100) {
            return response()->json([
                'success' => false,
                'message' => 'Total persen A + B + C + D tidak boleh lebih dari 100%.',
            ], 422);
        }

        $e = 100 - ($a + $b + $c + $d);

        $row = HrPmsNormalScale::updateOrCreate(
            ['periode' => $validated['periode'], 'level_group' => $levelGroup],
            [
                'grade_a' => $a,
                'grade_b' => $b,
                'grade_c' => $c,
                'grade_d' => $d,
                'grade_e' => $e,
                'minimum_peserta' => $minN,
                'updated_by' => Auth::user()->username ?? (Auth::user()->name ?? 'system'),
            ]
        );

        $dist = $row->distribution();

        return response()->json([
            'success' => true,
            'message' => 'Kuota Normalize ' . HrPmsNormalScale::groupLabel($levelGroup) . ' periode ' . $row->periode . ' disimpan (A ' . $a . '%, B ' . $b . '%, C ' . $c . '%, D ' . $d . '%, E ' . $e . '%; minimum peserta ' . $minN . ' orang).',
            'periode' => $row->periode,
            'level_group' => $levelGroup,
            'label' => HrPmsNormalScale::groupLabel($levelGroup),
            'distribution' => $dist,
            'percent' => PaScaleNormalizer::percentages($dist),
            'is_custom' => true,
            'minimum_peserta' => (int) $row->minimum_peserta,
            'updated_by' => $row->updated_by,
            'updated_at' => $row->updated_at ? $row->updated_at->format('d/m/Y H:i') : null,
        ]);
    }

    /**
     * Simpan setelan beberapa group sekaligus dalam satu transaksi.
     */
    private function normalScaleSaveGroups(Request $request)
    {
        $validated = $request->validate([
            'periode' => 'required|regex:/^\d{4}$/',
            'groups' => 'required|array',
            'groups.*.grade_a' => 'required|integer|min:0|max:100',
            'groups.*.grade_b' => 'required|integer|min:0|max:100',
            'groups.*.grade_c' => 'required|integer|min:0|max:100',
            'groups.*.grade_d' => 'required|integer|min:0|max:100',
            'groups.*.minimum_peserta' => 'nullable|integer|min:1|max:1000',
        ]);

        $prepared = [];
        foreach (HrPmsNormalScale::GROUPS as $levelGroup) {
            if (!isset($validated['groups'][$levelGroup])) {
                continue;
            }

            $values = $validated['groups'][$levelGroup];
            $a = (int) $values['grade_a'];
            $b = (int) $values['grade_b'];
            $c = (int) $values['grade_c'];
            $d = (int) $values['grade_d'];

            if (($a + $b + $c + $d) > 100) {
                return response()->json([
                    'success' => false,
                    'message' => 'Total persen A + B + C + D ' . HrPmsNormalScale::groupLabel($levelGroup) . ' tidak boleh lebih dari 100%.',
                ], 422);
            }

            $prepared[$levelGroup] = [
                'grade_a' => $a,
                'grade_b' => $b,
                'grade_c' => $c,
                'grade_d' => $d,
                'grade_e' => 100 - ($a + $b + $c + $d),
                'minimum_peserta' => isset($values['minimum_peserta']) && $values['minimum_peserta'] !== ''
                    ? max(1, (int) $values['minimum_peserta'])
                    : PaScaleNormalizer::MIN_N,
            ];
        }

        if (!$prepared) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak ada setelan group yang bisa disimpan.',
            ], 422);
        }

        $updater = Auth::user()->username ?? (Auth::user()->name ?? 'system');

        DB::transaction(function () use ($validated, $prepared, $updater) {
            foreach ($prepared as $levelGroup => $values) {
                HrPmsNormalScale::updateOrCreate(
                    ['periode' => $validated['periode'], 'level_group' => $levelGroup],
                    $values + ['updated_by' => $updater]
                );
            }
        });

        $groups = [];
        foreach (HrPmsNormalScale::GROUPS as $levelGroup) {
            $groups[$levelGroup] = $this->normalScaleGroupPayload($validated['periode'], $levelGroup);
        }

        $labels = [];
        foreach (array_keys($prepared) as $levelGroup) {
            $labels[] = HrPmsNormalScale::groupLabel($levelGroup);
        }

        return response()->json([
            'success' => true,
            'message' => 'Kuota Normalize ' . implode(' & ', $labels) . ' periode ' . $validated['periode'] . ' disimpan.',
            'periode' => $validated['periode'],
            'groups' => $groups,
        ] + $groups[HrPmsNormalScale::GROUP_47]);
    }

    public function normalScaleReset(Request $request)
    {
        $this->checkPermission('hr_upload_file_template_ipp');

        $validated = $request->validate([
            'periode' => 'required|regex:/^\d{4}$/',
            'level_group' => ['nullable', Rule::in(HrPmsNormalScale::GROUPS)],
            'all_groups' => 'nullable|boolean',
        ]);

        if (!empty($validated['all_groups'])) {
            HrPmsNormalScale::where('periode', $validated['periode'])->delete();

            $groups = [];
            foreach (HrPmsNormalScale::GROUPS as $levelGroup) {
                $groups[$levelGroup] = $this->normalScaleGroupPayload($validated['periode'], $levelGroup);
            }

            return response()->json([
                'success' => true,
                'message' => 'Kuota Normalize Level 2-3 dan Level 4-7 (4-5 & 6-7) periode ' . $validated['periode'] . ' dikembalikan ke default (A 5%, B 15%, C 65%, D 10%, E 5%).',
                'periode' => $validated['periode'],
                'groups' => $groups,
            ] + $groups[HrPmsNormalScale::GROUP_47]);
        }

        $levelGroup = HrPmsNormalScale::bucketForGroup($validated['level_group'] ?? null);

        HrPmsNormalScale::where('periode', $validated['periode'])
            ->where('level_group', $levelGroup)
            ->delete();

        $dist = PaScaleNormalizer::DEFAULT_DISTRIBUTION;

        return response()->json([
            'success' => true,
            'message' => 'Kuota Normalize ' . HrPmsNormalScale::groupLabel($levelGroup) . ' periode ' . $validated['periode'] . ' dikembalikan ke default (A 5%, B 15%, C 65%, D 10%, E 5%).',
            'periode' => $validated['periode'],
            'level_group' => $levelGroup,
            'label' => HrPmsNormalScale::groupLabel($levelGroup),
            'distribution' => $dist,
            'percent' => PaScaleNormalizer::percentages($dist),
            'is_custom' => false,
            'minimum_peserta' => PaScaleNormalizer::MIN_N,
        ]);
    }

    /**
     * Status LOCKING PA SCALE CALIBRATED per periode.
     * Admin HR mengelola; manager (view tim) hanya melihat status.
     */
    public function lockingPascaleGet(Request $request)
    {
        $this->ensureScoringAccess();

        $periodes = $this->periodeOptions();
        $periode = trim((string) $request->get('periode', ''));
        if ($periode === '' || !in_array($periode, $periodes, true)) {
            $periode = $this->defaultPeriode();
        }

        $state = HrPmsLockingPascale::stateFor($periode);

        return response()->json([
            'success' => true,
            'periode' => $periode,
            'periodes' => $periodes,
            'default_periode' => $this->defaultPeriode(),
        ] + $state);
    }

    /**
     * Kunci / buka kunci input PA Scale Calibrated satu periode (admin HR).
     */
    public function lockingPascaleSave(Request $request)
    {
        $this->checkPermission('hr_upload_file_template_ipp');

        $validated = $request->validate([
            'periode' => 'required|regex:/^\d{4}$/',
            'is_locked' => 'required|boolean',
        ]);

        $periode = trim((string) $validated['periode']);
        $isLocked = (bool) $validated['is_locked'];
        $updater = $this->currentUpdaterName();

        $row = HrPmsLockingPascale::updateOrCreate(
            ['periode' => $periode],
            [
                'is_locked' => $isLocked,
                'locked_by' => $isLocked ? $updater : null,
                'locked_at' => $isLocked ? now() : null,
                'updated_by' => $updater,
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'PA Scale Calibrated periode ' . $periode . ($isLocked ? ' dikunci' : ' dibuka') . '.',
            'periode' => $periode,
            'periodes' => $this->periodeOptions(),
            'default_periode' => $this->defaultPeriode(),
        ] + HrPmsLockingPascale::stateFor($row->periode));
    }

    /**
     * Semua tahapan IPP (MID/END per tahun) dari periode IPP, urut menurun.
     */
    private function ippTahapanOptions(): array
    {
        $set = [];
        foreach (DB::table('hr_ipp')
            ->whereNotNull('periode')
            ->where('periode', '<>', '')
            ->distinct()
            ->pluck('periode') as $periode) {
            foreach (IppTahapan::build((string) $periode) as $tahapan) {
                $set[$tahapan] = true;
            }
        }

        $list = array_keys($set);
        usort($list, function ($a, $b) {
            $ya = preg_match('/(\d{4})/', $a, $ma) ? (int) $ma[1] : 0;
            $yb = preg_match('/(\d{4})/', $b, $mb) ? (int) $mb[1] : 0;
            if ($ya !== $yb) {
                return $yb <=> $ya;
            }

            $ka = strpos($a, 'MID') === 0 ? 0 : 1;
            $kb = strpos($b, 'MID') === 0 ? 0 : 1;

            return $ka <=> $kb;
        });

        return array_values($list);
    }

    /**
     * Status LOCKING IPP AKTUAL (per tahapan x kolom: Departemen/Karyawan/Atasan).
     * Admin HR mengelola; manager (view tim) hanya melihat status.
     */
    public function lockingIppGet(Request $request)
    {
        $this->ensureScoringAccess();

        $tahapanOptions = $this->ippTahapanOptions();
        $tahapan = trim((string) $request->get('tahapan', ''));
        if ($tahapan === '' || !in_array($tahapan, $tahapanOptions, true)) {
            $tahapan = $tahapanOptions[0] ?? '';
        }

        $labels = [];
        foreach ($tahapanOptions as $option) {
            $labels[$option] = IppTahapan::label($option);
        }

        return response()->json([
            'success' => true,
            'tahapan' => $tahapan,
            'tahapan_options' => $tahapanOptions,
            'tahapan_labels' => $labels,
            'can_manage' => $this->hasScoringPermission(),
        ] + HrPmsLockingIpp::stateFor($tahapan));
    }

    /**
     * Kunci / buka kunci salah satu kolom Aktual IPP (Departemen/Karyawan/Atasan)
     * pada satu tahapan (admin HR).
     */
    public function lockingIppSave(Request $request)
    {
        $this->checkPermission('hr_upload_file_template_ipp');

        $validated = $request->validate([
            'tahapan' => 'required|string|max:20',
            'column' => ['required', Rule::in(array_keys(HrPmsLockingIpp::COLUMNS))],
            'is_locked' => 'required|boolean',
        ]);

        $tahapan = trim((string) $validated['tahapan']);
        $column = strtolower(trim((string) $validated['column']));
        $columnKey = HrPmsLockingIpp::columnKey($column);
        if ($columnKey === null) {
            return response()->json([
                'success' => false,
                'message' => 'Kolom Aktual IPP tidak dikenali.',
            ], 422);
        }

        if (!in_array($tahapan, $this->ippTahapanOptions(), true)) {
            return response()->json([
                'success' => false,
                'message' => 'Tahapan ' . IppTahapan::label($tahapan) . ' tidak tersedia pada IPP.',
            ], 422);
        }

        $isLocked = (bool) $validated['is_locked'];
        $updater = $this->currentUpdaterName();

        $row = HrPmsLockingIpp::firstOrNew(['tahapan' => $tahapan]);
        $row->{$columnKey} = $isLocked;
        $row->updated_by = $updater;

        $flags = [];
        foreach (HrPmsLockingIpp::COLUMNS as $key) {
            $flags[] = (bool) $row->{$key};
        }
        $anyLocked = in_array(true, $flags, true);

        $row->locked_by = $anyLocked ? $updater : null;
        $row->locked_at = $anyLocked ? ($row->locked_at ?: now()) : null;
        $row->save();

        return response()->json([
            'success' => true,
            'message' => HrPmsLockingIpp::label($column) . ' tahapan ' . IppTahapan::label($tahapan)
                . ($isLocked ? ' dikunci' : ' dibuka') . '.',
            'tahapan' => $tahapan,
            'tahapan_options' => $this->ippTahapanOptions(),
            'can_manage' => true,
        ] + HrPmsLockingIpp::stateFor($tahapan));
    }

    /**
     * Semua periode PK (satu tahun saja, contoh: 2026), urut menurun.
     */
    private function pkPeriodeOptions(): array
    {
        return HrPk::singleYearPeriodes();
    }

    /**
     * Status LOCKING PK per periode x kolom (Input Karyawan/Atasan,
     * Approval AM/Manager, Unapproval).
     * Admin HR mengelola; manager (view tim) hanya melihat status.
     */
    public function lockingPkGet(Request $request)
    {
        $this->ensureScoringAccess();

        $periodes = $this->pkPeriodeOptions();
        $periode = trim((string) $request->get('periode', ''));
        if ($periode === '' || !in_array($periode, $periodes, true)) {
            $periode = $periodes[0] ?? '';
        }

        return response()->json([
            'success' => true,
            'periode' => $periode,
            'periodes' => $periodes,
            'can_manage' => $this->hasScoringPermission(),
        ] + HrPmsLockingPk::stateFor($periode));
    }

    /**
     * Kunci / buka kunci salah satu kolom LOCKING PK pada satu periode (admin HR).
     */
    public function lockingPkSave(Request $request)
    {
        $this->checkPermission('hr_upload_file_template_ipp');

        $validated = $request->validate([
            'periode' => 'required|string|max:10',
            'column' => ['required', Rule::in(array_keys(HrPmsLockingPk::COLUMNS))],
            'is_locked' => 'required|boolean',
        ]);

        $periode = trim((string) $validated['periode']);
        $column = strtolower(trim((string) $validated['column']));
        $columnKey = HrPmsLockingPk::columnKey($column);
        if ($columnKey === null) {
            return response()->json([
                'success' => false,
                'message' => 'Kolom LOCKING PK tidak dikenali.',
            ], 422);
        }

        if (!in_array($periode, $this->pkPeriodeOptions(), true)) {
            return response()->json([
                'success' => false,
                'message' => 'Periode PK ' . $periode . ' tidak tersedia.',
            ], 422);
        }

        $isLocked = (bool) $validated['is_locked'];
        $updater = $this->currentUpdaterName();

        $row = HrPmsLockingPk::firstOrNew(['periode' => $periode]);
        $row->{$columnKey} = $isLocked;
        $row->updated_by = $updater;

        $flags = [];
        foreach (HrPmsLockingPk::COLUMNS as $key) {
            $flags[] = (bool) $row->{$key};
        }
        $anyLocked = in_array(true, $flags, true);

        $row->locked_by = $anyLocked ? $updater : null;
        $row->locked_at = $anyLocked ? ($row->locked_at ?: now()) : null;
        $row->save();

        return response()->json([
            'success' => true,
            'message' => HrPmsLockingPk::label($column) . ' periode ' . $periode
                . ($isLocked ? ' dikunci' : ' dibuka') . '.',
            'periode' => $periode,
            'periodes' => $this->pkPeriodeOptions(),
            'can_manage' => true,
        ] + HrPmsLockingPk::stateFor($periode));
    }

    /**
     * Aktif / nonaktifkan seluruh email notifikasi PMS (IPP & PK) — admin HR.
     */
    public function notifikasiSave(Request $request)
    {
        $this->checkPermission('hr_upload_file_template_ipp');

        $validated = $request->validate([
            'is_active' => 'required|boolean',
        ]);

        $active = (bool) $validated['is_active'];
        HrPmsControlNotifikasi::setActive($active, $this->currentUpdaterName());

        return response()->json([
            'success' => true,
            'message' => 'Notifikasi email PMS (IPP & PK) ' . ($active ? 'diaktifkan' : 'dinonaktifkan') . '.',
            'state' => HrPmsControlNotifikasi::state(),
        ]);
    }

    /**
     * Nama pengubah dari hr_master_employee.Nama (via NIK user login),
     * fallback ke users.name / username / 'system'.
     */
    private function currentUpdaterName(): string
    {
        $nikUser = trim((string) (Auth::user()->nik ?? ''));

        if ($nikUser !== '') {
            $nama = DB::table('hr_master_employee')->where('NIK', $nikUser)->value('Nama');
            if ($nama !== null && trim((string) $nama) !== '') {
                return (string) $nama;
            }
        }

        return (string) (Auth::user()->name ?? (Auth::user()->username ?? 'system'));
    }

    /**
     * Simpan PA Scale Calibrated per karyawan per periode (diisi manual oleh HRD).
     */
    public function paFinalSave(Request $request)
    {
        $this->ensureScoringAccess();

        $periodes = $this->periodeOptions();

        $validated = $request->validate([
            'periode' => 'required|regex:/^\d{4}$/',
            'nik' => 'required|string|max:50',
            'pa_scale_final' => 'nullable|string|in:A,B,C,D,E,a,b,c,d,e',
        ]);

        $periode = trim((string) $validated['periode']);
        $nik = trim((string) $validated['nik']);

        if (!in_array($periode, $periodes, true)) {
            return response()->json([
                'success' => false,
                'message' => 'Periode ' . $periode . ' tidak tersedia pada Total Scoring.',
            ], 422);
        }

        if (HrPmsLockingPascale::isLocked($periode) && !$this->hasScoringPermission()) {
            return response()->json([
                'success' => false,
                'message' => 'PA Scale CALIBRATED periode ' . $periode . ' sedang dikunci (LOCKING PA SCALE CALIBRATED). Buka kunci terlebih dahulu.',
            ], 422);
        }

        if ($nik === '') {
            return response()->json([
                'success' => false,
                'message' => 'NIK wajib diisi.',
            ], 422);
        }

        $teamKeys = $this->managerTeamKeys();
        if ($teamKeys !== null && !isset($teamKeys[PmsEmployeeSnapshot::normalizePeriode($periode) . '|' . $nik])) {
            return response()->json([
                'success' => false,
                'message' => 'Anda hanya dapat mengisi PA Scale Calibrated untuk anggota tim Anda.',
            ], 422);
        }

        $grade = isset($validated['pa_scale_final'])
            ? strtoupper(trim((string) $validated['pa_scale_final']))
            : '';

        if ($grade === '') {
            HrPmsPascoreFinal::where('periode', $periode)->where('nik', $nik)->delete();

            return response()->json([
                'success' => true,
                'message' => 'PA Scale Calibrated ' . $nik . ' periode ' . $periode . ' dikosongkan.',
                'periode' => $periode,
                'nik' => $nik,
                'pa_scale_final' => null,
                'updated_by' => null,
                'updated_at' => null,
            ]);
        }

        $row = HrPmsPascoreFinal::updateOrCreate(
            ['periode' => $periode, 'nik' => $nik],
            [
                'pa_scale_final' => $grade,
                'updated_by' => $this->currentUpdaterName(),
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'PA Scale Calibrated ' . $nik . ' periode ' . $periode . ' disimpan: ' . $grade . '.',
            'periode' => $periode,
            'nik' => $nik,
            'pa_scale_final' => $row->pa_scale_final,
            'updated_by' => $row->updated_by,
            'updated_at' => $row->updated_at ? $row->updated_at->format('d/m/Y H:i') : null,
        ]);
    }

    /**
     * Salin PA Scale Normalize ke PA Scale Calibrated secara massal
     * untuk seluruh baris sesuai filter aktif. Hanya baris yang PA Scale Calibrated
     * masih kosong yang diisi; baris di luar wewenang manager dilewati.
     */
    public function paFinalBulkSave(Request $request)
    {
        $this->ensureScoringAccess();

        $periodes = $this->periodeOptions();

        $validated = $request->validate([
            'periode' => 'required|regex:/^\d{4}$/',
            'group' => 'nullable|string|max:20',
            'departemen' => 'nullable|string|max:150',
            'dept_type' => 'nullable|in:dept,sub',
            'search' => 'nullable|string|max:150',
            'pa_scale_type' => ['nullable', Rule::in(['original', 'normalize', 'calibrated'])],
            'pa_scale_grade' => ['nullable', Rule::in(['A', 'B', 'C', 'D', 'E'])],
            'source' => 'nullable|in:rank',
        ]);

        $periode = trim((string) $validated['periode']);

        if (!in_array($periode, $periodes, true)) {
            return response()->json([
                'success' => false,
                'message' => 'Periode ' . $periode . ' tidak tersedia pada Total Scoring.',
            ], 422);
        }

        if (HrPmsLockingPascale::isLocked($periode) && !$this->hasScoringPermission()) {
            return response()->json([
                'success' => false,
                'message' => 'PA Scale Calibrated periode ' . $periode . ' sedang dikunci (LOCKING PA SCALE CALIBRATED). Buka kunci terlebih dahulu.',
            ], 422);
        }

        $source = 'rank';
        $column = 'pa_scale_rank';
        $label = 'Rank';

        $built = $this->buildScoringData($request);
        $rows = $built['rows'];

        $updater = $this->currentUpdaterName();
        $filled = 0;
        $skipped = 0;

        foreach ($rows as $row) {
            $suggested = trim((string) ($row[$column] ?? ''));
            $current = trim((string) ($row['pa_scale_final'] ?? ''));

            if ($suggested === '' || $current !== '' || $row['periode'] !== $periode) {
                $skipped++;
                continue;
            }

            HrPmsPascoreFinal::updateOrCreate(
                ['periode' => $row['periode'], 'nik' => $row['nik']],
                ['pa_scale_final' => $suggested, 'updated_by' => $updater]
            );
            $filled++;
        }

        if ($filled === 0) {
            return response()->json([
                'success' => true,
                'filled' => 0,
                'skipped' => $skipped,
                'total' => count($rows),
                'periode' => $periode,
                'source' => $source,
                'message' => 'Tidak ada kolom PA Scale Calibrated kosong yang bisa diisi dari ' . $label . ' pada periode ' . $periode . '.',
            ]);
        }

        return response()->json([
            'success' => true,
            'filled' => $filled,
            'skipped' => $skipped,
            'total' => count($rows),
            'periode' => $periode,
            'source' => $source,
            'message' => $filled . ' kolom PA Scale Calibrated diisi dari ' . $label . ' periode ' . $periode . '.',
        ]);
    }

    /**
     * Export PDF form IPP per otorisasi (admin HR). Endpoint asli menolak admin,
     * jadi route ini memakai generatePmsPdf langsung setelah cek permission.
     */
    public function exportIppForeman($ippId, $nik, Request $request)
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

        $tahapan = trim((string) $request->get('tahapan', ''));

        return app(IppAktualController::class)->generatePmsPdf(IppForemanPeserta::otoObject($snap), $ipp, $tahapan);
    }

    public function exportIpp($otoId, Request $request)
    {
        $this->checkPermission('hr_upload_file_template_ipp');

        $otorisasi = HrIppOtorisasi::findOrFail((int) $otoId);
        $ipp = HrIpp::with('details')->findOrFail($otorisasi->ipp_id);

        $tahapan = trim((string) $request->get('tahapan', ''));

        return app(IppAktualController::class)->generatePmsPdf($otorisasi, $ipp, $tahapan);
    }
}
