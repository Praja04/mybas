<?php

namespace App\Http\Controllers\HR;

use App\Http\Controllers\Controller;
use App\Models\HR\HrPk;
use App\Models\HR\HrPkDetail;
use App\Models\HR\HrPkDiskusi;
use App\Models\HR\HrPkNilai;
use App\Models\HR\HrPmsEmployeeSnapshot;
use App\Support\PkComponentScore;
use App\Support\PmsEmployeeSnapshot;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PkAssessmentHrController extends Controller
{
    private function checkPermission(string $codename): void
    {
        $userCodenames = Auth::user()->getAllPermissionCodenames();
        if (!in_array($codename, $userCodenames, true)) {
            abort(403);
        }
    }

    private function groupOf($level): string
    {
        return PmsEmployeeSnapshot::groupOf($level) ?? '4-5';
    }

    private function byLabels(array $usernames): array
    {
        $usernames = array_values(array_unique(array_filter($usernames, fn ($v) => $v !== null && $v !== '')));
        if (!$usernames) {
            return [];
        }
        $users = DB::table('users')
            ->whereIn('username', $usernames)
            ->pluck('nik', 'username');
        $emps = DB::table('hr_master_employee')
            ->whereIn('NIK', $users->filter()->values()->all())
            ->pluck('Nama', 'NIK');
        $labels = [];
        foreach ($users as $username => $nik) {
            $nama = ($nik && isset($emps[$nik])) ? $emps[$nik] : null;
            if (!$nama) {
                $nama = DB::table('users')->where('username', $username)->value('name');
            }
            $labels[$username] = trim((string) $nik) !== '' && $nama ? $nik . ' · ' . $nama : (string) $username;
        }
        return $labels;
    }

    public function index()
    {
        $this->checkPermission('hr_upload_file_template_ipp');

        return view('hr.pk-assessment-hr.pk-assessment-hr', [
            'periodes' => HrPk::orderByRaw('CAST(periode AS UNSIGNED) DESC, id DESC')->pluck('periode', 'id'),
        ]);
    }

    public function list(Request $request)
    {
        $this->checkPermission('hr_upload_file_template_ipp');

        $perPage = (int) $request->get('per_page', 20);
        $perPage = max(1, min(100, $perPage));
        $page = max(1, (int) $request->get('page', 1));
        $search = trim((string) $request->get('search', ''));
        $fPeriode = trim((string) $request->get('periode', ''));
        $fGroup = trim((string) $request->get('group', ''));
        $fDept = trim((string) $request->get('departemen', ''));
        $fDeptType = $request->get('dept_type') === 'sub' ? 'sub' : 'dept';
        $fStatus = trim((string) $request->get('status', ''));

        $pks = HrPk::orderByRaw('CAST(periode AS UNSIGNED) DESC, id DESC')->get(['id', 'periode', 'file_name']);
        if ($fPeriode !== '') {
            $pks = $pks->filter(fn ($p) => (string) $p->periode === $fPeriode);
        }
        $pkIds = $pks->pluck('id')->all();
        $pkPeriodes = $pks->pluck('periode')
            ->map(fn ($p) => PmsEmployeeSnapshot::normalizePeriode((string) $p))
            ->filter()
            ->unique()
            ->values()
            ->all();

        $emps = DB::table('hr_master_employee')
            ->where('Aktif', 'Y')
            ->whereRaw("Level IN ('2', '3', '4', '5', '6', '7')")
            ->orderByRaw('CAST(Level AS UNSIGNED) ASC, NIK ASC')
            ->get(['NIK', 'Nama', 'Jabatan', 'Departmen', 'Sub Departmen', 'Level']);

        $extraNiks = [];
        $mergeNiks = function ($niks) use (&$extraNiks) {
            foreach ($niks as $n) {
                $n = trim((string) $n);
                if ($n !== '') {
                    $extraNiks[$n] = true;
                }
            }
        };

        $snapNiksByPeriode = [];
        if ($pkPeriodes) {
            foreach (HrPmsEmployeeSnapshot::whereIn('periode', $pkPeriodes)->get(['periode', 'nik']) as $snapRow) {
                $norm = PmsEmployeeSnapshot::normalizePeriode((string) $snapRow->periode);
                $key = strtolower(trim((string) $snapRow->nik));
                if ($norm !== '' && $key !== '') {
                    $snapNiksByPeriode[$norm][$key] = true;
                }
            }
            $mergeNiks(HrPmsEmployeeSnapshot::whereIn('periode', $pkPeriodes)->distinct()->pluck('nik')->all());
        }

        $existing = $emps->pluck('NIK')->map(fn ($n) => strtolower(trim((string) $n)))->flip();
        $missingNiks = array_values(array_filter(
            array_keys($extraNiks),
            fn ($n) => !isset($existing[strtolower($n)])
        ));
        if ($missingNiks) {
            $extraEmps = DB::table('hr_master_employee')
                ->whereIn('NIK', $missingNiks)
                ->get(['NIK', 'Nama', 'Jabatan', 'Departmen', 'Sub Departmen', 'Level']);
            $found = [];
            foreach ($extraEmps as $oe) {
                $found[strtolower(trim((string) $oe->NIK))] = true;
                $emps->push($oe);
            }
            foreach ($missingNiks as $nik) {
                if (isset($found[strtolower($nik)])) {
                    continue;
                }
                $snapFallback = HrPmsEmployeeSnapshot::query();
                if ($pkPeriodes) {
                    $snapFallback->whereIn('periode', $pkPeriodes);
                }
                $snap = $snapFallback->where('nik', $nik)
                    ->orderByDesc('periode')
                    ->first(['nik', 'nama', 'jabatan', 'departemen', 'sub_departemen', 'level']);
                if (!$snap) {
                    continue;
                }
                $emps->push((object) [
                    'NIK' => $snap->nik,
                    'Nama' => $snap->nama,
                    'Jabatan' => $snap->jabatan,
                    'Departmen' => $snap->departemen,
                    'Sub Departmen' => $snap->sub_departemen,
                    'Level' => $snap->level,
                ]);
            }
        }

        $empNiks = $emps->pluck('NIK')->all();
        $snapMap = PmsEmployeeSnapshot::mapFor($pks->pluck('periode')->all(), $empNiks);

        $totals = [];
        $agg = [];
        $open = [];
        $leadDetailMeta = [];
        $leadFilled = [];
        if ($pkIds && $empNiks) {
            $dts = DB::table('hr_pk_detail')
                ->whereIn('hr_pk_id', $pkIds)
                ->selectRaw('hr_pk_id, level_group, COUNT(*) as c')
                ->groupBy('hr_pk_id', 'level_group')
                ->get();
            foreach ($dts as $t) {
                $totals[$t->hr_pk_id]['all'] = ($totals[$t->hr_pk_id]['all'] ?? 0) + ($t->level_group === 'all' ? $t->c : 0);
                if (in_array($t->level_group, ['2-3', '4-5', '6-7'], true)) {
                    $totals[$t->hr_pk_id][$t->level_group] = $t->c;
                }
            }

            $leadDetails = DB::table('hr_pk_detail')
                ->whereIn('hr_pk_id', $pkIds)
                ->whereIn('level_group', ['4-5', '6-7'])
                ->where(function ($w) {
                    $w->where('section', 'like', '%LEADERSHIP%')->orWhere('category', 'like', '%LEADERSHIP%');
                })
                ->get(['id', 'hr_pk_id', 'level_group']);
            foreach ($leadDetails as $ld) {
                $leadDetailMeta[(int) $ld->id] = ['pk' => (int) $ld->hr_pk_id, 'group' => $ld->level_group];
            }

            if (!empty($leadDetailMeta)) {
                $leadRows = HrPkNilai::whereIn('hr_pk_id', $pkIds)
                    ->whereIn('nik', $empNiks)
                    ->whereIn('hr_pk_detail_id', array_keys($leadDetailMeta))
                    ->get(['hr_pk_id', 'nik', 'hr_pk_detail_id', 'nilai_self', 'nilai_leader']);
                foreach ($leadRows as $lr) {
                    $key = $lr->hr_pk_id . '|' . $lr->nik . '|' . $lr->hr_pk_detail_id;
                    $leadFilled[$key] = [
                        's' => $lr->nilai_self !== null ? 1 : 0,
                        'l' => $lr->nilai_leader !== null ? 1 : 0,
                    ];
                }
            }

            $agg = HrPkNilai::whereIn('hr_pk_id', $pkIds)
                ->whereIn('nik', $empNiks)
                ->selectRaw("hr_pk_id, nik, COUNT(nilai_self) as sf, COUNT(nilai_leader) as lf,
                    MAX(am_approved_by IS NOT NULL) as am_ok, MAX(mgr_approved_by IS NOT NULL) as mgr_ok,
                    MAX(am_approved_by) as am_by, MAX(am_approved_at) as am_at,
                    MAX(mgr_approved_by) as mgr_by, MAX(mgr_approved_at) as mgr_at")
                ->groupBy('hr_pk_id', 'nik')
                ->get()
                ->keyBy(fn ($r) => $r->hr_pk_id . '|' . $r->nik);

            $open = HrPkDiskusi::whereIn('hr_pk_id', $pkIds)
                ->whereIn('nik_yang_dibahas', $empNiks)
                ->where('status', 'open')
                ->selectRaw('hr_pk_id, nik_yang_dibahas, COUNT(*) as c')
                ->groupBy('hr_pk_id', 'nik_yang_dibahas')
                ->get()
                ->mapWithKeys(fn ($r) => [$r->hr_pk_id . '|' . $r->nik_yang_dibahas => (int) $r->c])
                ->all();
        }

        $rows = [];
        $needle = $search !== '' ? mb_strtolower($search) : '';
        foreach ($pks as $pk) {
            $tAll = $totals[$pk->id]['all'] ?? 0;
            $normPeriode = PmsEmployeeSnapshot::normalizePeriode((string) $pk->periode);
            foreach ($emps as $e) {
                if (!isset($snapNiksByPeriode[$normPeriode][strtolower(trim((string) $e->NIK))])) {
                    continue;
                }
                $snap = $snapMap[$normPeriode . '|' . $e->NIK] ?? null;
                $level = ($snap && $snap->level !== null && $snap->level !== '') ? $snap->level : $e->Level;
                $group = ($snap && trim((string) $snap->level_group) !== '') ? trim((string) $snap->level_group) : $this->groupOf($level);
                $namaVal = ($snap && $snap->nama) ? $snap->nama : $e->Nama;
                $jabatanVal = ($snap && $snap->jabatan) ? $snap->jabatan : $e->Jabatan;
                $deptVal = trim((string) (($snap && $snap->departemen) ? $snap->departemen : $e->Departmen));
                $subVal = trim((string) (($snap && $snap->sub_departemen) ? $snap->sub_departemen : $e->{'Sub Departmen'}));

                if ($needle !== '') {
                    $hay = mb_strtolower(implode(' ', [$e->NIK, $namaVal, $jabatanVal, $deptVal, $subVal]));
                    if (mb_stripos($hay, $needle) === false) {
                        continue;
                    }
                }
                if ($fDept !== '') {
                    if ($fDeptType === 'sub') {
                        if (!(strtolower($deptVal) === 'factory' && $subVal === $fDept)) {
                            continue;
                        }
                    } elseif (!(strtolower($deptVal) !== 'factory' && $deptVal === $fDept)) {
                        continue;
                    }
                }
                if ($fGroup !== '' && $group !== $fGroup) {
                    continue;
                }

                $bawahan = ($snap && $snap->jumlah_bawahan !== null)
                    ? (int) $snap->jumlah_bawahan
                    : null;
                $noLeadership = in_array(trim((string) $group), ['2-3', '4-5'], true) && $bawahan === 0;
                $leadS = 0;
                $leadL = 0;
                $leadCount = 0;
                if ($noLeadership && !empty($leadDetailMeta)) {
                    foreach ($leadDetailMeta as $ldId => $meta) {
                        if ($meta['pk'] !== (int) $pk->id || $meta['group'] !== $group) {
                            continue;
                        }
                        $leadCount++;
                        $filled = $leadFilled[$pk->id . '|' . $e->NIK . '|' . $ldId] ?? null;
                        if ($filled) {
                            $leadS += $filled['s'];
                            $leadL += $filled['l'];
                        }
                    }
                }

                $total = $tAll + ($totals[$pk->id][$group] ?? 0) - $leadCount;
                $a = $agg->get($pk->id . '|' . $e->NIK);
                $sf = max(0, ($a ? (int) $a->sf : 0) - $leadS);
                $lf = max(0, ($a ? (int) $a->lf : 0) - $leadL);

                $selfRequired = $group !== '2-3';
                if ($total === 0) {
                    $status = 'tanpa_rubrik';
                } elseif ($sf === 0 && $lf === 0) {
                    $status = 'belum';
                } elseif ($lf >= $total && (!$selfRequired || $sf >= $total)) {
                    $status = 'selesai';
                } elseif ($selfRequired && $sf >= $total) {
                    $status = 'self_selesai';
                } else {
                    $status = 'proses';
                }

                if ($fStatus !== '' && $status !== $fStatus) {
                    continue;
                }

                $atasan = [
                    'manager' => ($snap && (trim((string) $snap->manager_nik) !== '' || trim((string) $snap->manager_nama) !== ''))
                        ? ['nik' => $snap->manager_nik, 'nama' => $snap->manager_nama]
                        : null,
                    'associate_manager' => ($snap && trim((string) $snap->associate_manager_nik) !== '')
                        ? ['nik' => $snap->associate_manager_nik, 'nama' => $snap->associate_manager_nama]
                        : null,
                ];

                $rows[] = [
                    'hr_pk_id' => $pk->id,
                    'periode' => $pk->periode,
                    'nik' => $e->NIK,
                    'nama' => $namaVal,
                    'jabatan' => $jabatanVal,
                    'departemen' => $deptVal,
                    'sub_departemen' => $subVal,
                    'level' => (int) $level,
                    'group' => $group,
                    'manager' => $atasan['manager']['nama'] ?? null,
                    'manager_nik' => $atasan['manager']['nik'] ?? null,
                    'associate_manager' => $atasan['associate_manager']['nama'] ?? null,
                    'associate_manager_nik' => $atasan['associate_manager']['nik'] ?? null,
                    'has_am' => in_array($group, ['2-3', '4-5'], true) && isset($atasan['associate_manager']),
                    'am_approved' => $a ? (bool) $a->am_ok : false,
                    'mgr_approved' => $a ? (bool) $a->mgr_ok : false,
                    'am_approved_by' => $a && $a->am_ok ? $a->am_by : null,
                    'am_approved_at' => $a && $a->am_ok && $a->am_at ? (string) $a->am_at : null,
                    'mgr_approved_by' => $a && $a->mgr_ok ? $a->mgr_by : null,
                    'mgr_approved_at' => $a && $a->mgr_ok && $a->mgr_at ? (string) $a->mgr_at : null,
                    'total' => $total,
                    'self_filled' => $sf,
                    'leader_filled' => $lf,
                    'no_leadership' => $noLeadership,
                    'diskusi_open' => (int) ($open[$pk->id . '|' . $e->NIK] ?? 0),
                    'status' => $status,
                ];
            }
        }

        $total = count($rows);
        $lastPage = max(1, (int) ceil($total / $perPage));
        $page = min($page, $lastPage);
        $data = array_slice($rows, ($page - 1) * $perPage, $perPage);

        if ($data) {
            $dataPkIds = array_values(array_unique(array_column($data, 'hr_pk_id')));
            $dataNiks = array_values(array_unique(array_column($data, 'nik')));
            $nilaiByKey = HrPkNilai::whereIn('hr_pk_id', $dataPkIds)
                ->whereIn('nik', $dataNiks)
                ->get(['hr_pk_id', 'nik', 'hr_pk_detail_id', 'nilai_self', 'nilai_leader'])
                ->groupBy(fn ($r) => $r->hr_pk_id . '|' . $r->nik);
            $detailById = DB::table('hr_pk_detail')
                ->whereIn('hr_pk_id', $dataPkIds)
                ->get(['id', 'level_group', 'category', 'section'])
                ->keyBy('id');

            foreach ($data as &$dr) {
                $comp = PkComponentScore::components(
                    $nilaiByKey->get($dr['hr_pk_id'] . '|' . $dr['nik'], collect()),
                    $detailById,
                    $dr['group'],
                    (bool) $dr['no_leadership']
                );
                $dr['avg_self'] = PkComponentScore::score($comp, $dr['group'], 'self');
                $dr['avg_leader'] = PkComponentScore::score($comp, $dr['group']);
            }
            unset($dr);
        }

        $labels = $this->byLabels(array_merge(
            array_column($data, 'am_approved_by'),
            array_column($data, 'mgr_approved_by')
        ));
        foreach ($data as &$dr) {
            $dr['am_approved_by_label'] = $dr['am_approved_by'] ? ($labels[$dr['am_approved_by']] ?? $dr['am_approved_by']) : null;
            $dr['mgr_approved_by_label'] = $dr['mgr_approved_by'] ? ($labels[$dr['mgr_approved_by']] ?? $dr['mgr_approved_by']) : null;
        }
        unset($dr);

        $depts = [];
        $subs = [];

        $snapQuery = HrPmsEmployeeSnapshot::query();
        if ($fPeriode !== '') {
            $snapQuery->where('periode', PmsEmployeeSnapshot::normalizePeriode($fPeriode));
        }
        foreach ($snapQuery->distinct()->get(['departemen', 'sub_departemen']) as $o) {
            $d = trim((string) $o->departemen);
            if (strtolower($d) === 'factory') {
                $s = trim((string) $o->sub_departemen);
                if ($s !== '') {
                    $subs[$s] = true;
                }
            } elseif ($d !== '') {
                $depts[$d] = true;
            }
        }

        $depts = array_keys($depts);
        $subs = array_keys($subs);
        sort($depts);
        sort($subs);
        $filterOptions = array_merge(
            array_map(fn ($d) => ['v' => $d, 't' => 'dept'], $depts),
            array_map(fn ($s) => ['v' => $s, 't' => 'sub'], $subs)
        );

        return response()->json([
            'success' => true,
            'data' => $data,
            'meta' => ['page' => $page, 'per_page' => $perPage, 'total' => $total, 'last_page' => $lastPage],
            'filterOptions' => $filterOptions,
        ]);
    }

    public function detail($pkId, $nik)
    {
        $this->checkPermission('hr_upload_file_template_ipp');

        $pk = HrPk::with('details')->findOrFail($pkId);
        $snap = PmsEmployeeSnapshot::attributesFor((string) $pk->periode, (string) $nik);
        abort_unless($snap, 404, 'Karyawan bukan peserta periode ini.');
        $target = DB::table('hr_master_employee')->where('NIK', $nik)->first(['NIK', 'Nama', 'Jabatan', 'Departmen', 'Sub Departmen', 'Level']);
        $targetLevel = ($snap && $snap->level !== null && $snap->level !== '')
            ? $snap->level
            : ($target->Level ?? null);
        $group = trim((string) $snap->level_group) !== '' ? trim((string) $snap->level_group) : $this->groupOf($targetLevel);

        $details = $pk->details
            ->filter(fn ($d) => in_array($d->level_group, ['all', $group], true));

        $nilai = HrPkNilai::where('hr_pk_id', $pkId)->where('nik', $nik)->get()->keyBy('hr_pk_detail_id');
        $rows = $details->map(function ($d) use ($nilai) {
            $n = $nilai[$d->id] ?? null;

            return [
                'id' => $d->id,
                'no_urut' => $d->no_urut,
                'section' => $d->section,
                'category' => $d->category,
                'label' => $d->itemLabel(),
                'area' => $d->area,
                'competency_behavior' => $d->competency_behavior,
                'key_behavior' => $d->key_behavior,
                'kriteria' => [$d->kriteria_1, $d->kriteria_2, $d->kriteria_3, $d->kriteria_4, $d->kriteria_5],
                'nilai_self' => $n ? $n->nilai_self : null,
                'self_by' => $n ? $n->self_by : null,
                'self_at' => $n ? optional($n->self_at)->format('d-m-Y H:i') : null,
                'nilai_leader' => $n ? $n->nilai_leader : null,
                'leader_by' => $n ? $n->leader_by : null,
                'leader_at' => $n ? optional($n->leader_at)->format('d-m-Y H:i') : null,
            ];
        })->values();

        $threads = HrPkDiskusi::where('hr_pk_id', $pkId)
            ->where('nik_yang_dibahas', $nik)
            ->orderBy('created_at', 'asc')
            ->get();
        $detailRef = HrPkDetail::whereIn('id', $threads->pluck('hr_pk_detail_id')->filter()->unique()->all())
            ->get(['id', 'no_urut', 'area', 'competency_behavior'])->keyBy('id');
        $diskusi = $threads->map(function ($t) use ($detailRef) {
            $d = $t->hr_pk_detail_id ? ($detailRef[$t->hr_pk_detail_id] ?? null) : null;

            return [
                'item' => $d ? $d->itemLabel() : 'Umum',
                'nama_pengaju' => $t->nama_pengaju,
                'pesan' => $t->pesan,
                'status' => $t->status,
                'jawaban' => $t->jawaban,
                'nama_penjawab' => $t->nama_penjawab,
                'created_at' => optional($t->created_at)->format('d-m-Y H:i'),
                'answered_at' => optional($t->answered_at)->format('d-m-Y H:i'),
            ];
        })->values();

        $jumlahBawahan = $snap->jumlah_bawahan !== null ? (int) $snap->jumlah_bawahan : null;
        $noLeadership = in_array(trim((string) $group), ['2-3', '4-5'], true) && $jumlahBawahan === 0;
        $pkComponents = PkComponentScore::components($nilai->values(), $details->keyBy('id'), $group, $noLeadership);
        $pkScore = PkComponentScore::score($pkComponents, $group);
        $appr = HrPkNilai::where('hr_pk_id', $pkId)->where('nik', $nik)
            ->selectRaw('MAX(am_approved_by IS NOT NULL) as am_ok, MAX(mgr_approved_by IS NOT NULL) as mgr_ok,
                MAX(am_approved_by) as am_by, MAX(am_approved_at) as am_at,
                MAX(mgr_approved_by) as mgr_by, MAX(mgr_approved_at) as mgr_at')
            ->first();
        $labels = $this->byLabels(array_merge(
            $rows->pluck('self_by')->all(),
            $rows->pluck('leader_by')->all(),
            [$appr->am_by ?? null, $appr->mgr_by ?? null]
        ));
        $rows = $rows->map(function ($r) use ($labels) {
            $r['self_by_label'] = $r['self_by'] ? ($labels[$r['self_by']] ?? $r['self_by']) : null;
            $r['leader_by_label'] = $r['leader_by'] ? ($labels[$r['leader_by']] ?? $r['leader_by']) : null;
            return $r;
        });
        $atasan = [
            'manager' => (trim((string) $snap->manager_nik) !== '' || trim((string) $snap->manager_nama) !== '')
                ? ['nik' => $snap->manager_nik, 'nama' => $snap->manager_nama]
                : null,
            'associate_manager' => trim((string) $snap->associate_manager_nik) !== ''
                ? ['nik' => $snap->associate_manager_nik, 'nama' => $snap->associate_manager_nama]
                : null,
        ];
        $approval = [
            'has_am' => in_array($group, ['2-3', '4-5'], true) && isset($atasan['associate_manager']),
            'am_approved' => (bool) ($appr->am_ok ?? false),
            'mgr_approved' => (bool) ($appr->mgr_ok ?? false),
            'am_approved_by' => $appr && $appr->am_ok ? $appr->am_by : null,
            'am_approved_by_label' => $appr && $appr->am_ok ? ($labels[$appr->am_by] ?? $appr->am_by) : null,
            'am_approved_at' => $appr && $appr->am_ok && $appr->am_at ? date('d-m-Y H:i', strtotime($appr->am_at)) : null,
            'mgr_approved_by' => $appr && $appr->mgr_ok ? $appr->mgr_by : null,
            'mgr_approved_by_label' => $appr && $appr->mgr_ok ? ($labels[$appr->mgr_by] ?? $appr->mgr_by) : null,
            'mgr_approved_at' => $appr && $appr->mgr_ok && $appr->mgr_at ? date('d-m-Y H:i', strtotime($appr->mgr_at)) : null,
        ];

        return response()->json([
            'success' => true,
            'pk' => ['id' => $pk->id, 'periode' => $pk->periode, 'file_name' => $pk->file_name],
            'header' => [
                'nik' => $target->NIK ?? $nik,
                'nama' => ($snap && $snap->nama) ? $snap->nama : ($target->Nama ?? ''),
                'jabatan' => ($snap && $snap->jabatan) ? $snap->jabatan : ($target->Jabatan ?? ''),
                'departemen' => trim((string) (($snap && $snap->departemen) ? $snap->departemen : ($target->Departmen ?? ''))),
                'sub_departemen' => trim((string) (($snap && $snap->sub_departemen) ? $snap->sub_departemen : ($target->{'Sub Departmen'} ?? ''))),
                'level' => (int) $targetLevel,
                'group' => $group,
                'jumlah_bawahan' => $jumlahBawahan,
                'no_leadership' => $noLeadership,
                'manager' => isset($atasan['manager']) ? $atasan['manager']['nama'] . ' (' . $atasan['manager']['nik'] . ')' : '-',
                    'associate_manager' => isset($atasan['associate_manager']) ? $atasan['associate_manager']['nama'] . ' (' . $atasan['associate_manager']['nik'] . ')' : '-',
                ],
                'approval' => $approval,
            'rows' => $rows,
            'diskusi' => $diskusi,
            'pk_score' => $pkScore,
            'pk_components' => $pkComponents,
        ]);
    }
}
