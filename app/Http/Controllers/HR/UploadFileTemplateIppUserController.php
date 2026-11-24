<?php

namespace App\Http\Controllers\HR;

use App\Http\Controllers\Controller;
use App\Imports\HR\TemplateIppImport;
use App\Jobs\HR\SendIppUserNotificationJob;
use App\Models\HR\HrIpp;
use App\Models\HR\HrIppAktual;
use App\Models\HR\HrIppDetail;
use App\Models\HR\HrIppManagerMapping;
use App\Models\HR\HrIppMasterTarget;
use App\Models\HR\HrIppOtorisasi;
use App\Models\HR\HrPk;
use App\Models\HR\HrPkOtorisasi;
use App\Models\HR\HrPmsControlNotifikasi;
use App\Models\HR\HrPmsEmployeeSnapshot;
use App\Support\IppRangeScale;
use App\Support\PkAmMapping;
use App\Support\PkAtasan;
use App\Support\PmsEmployeeSnapshot;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\IOFactory;

class UploadFileTemplateIppUserController extends Controller
{
    /**
     * Halaman khusus user dengan level > 5 (6 - 13).
     * Level user didapat dari users.nik -> hr_master_employee.Level.
     */
    private function userLevel(): int
    {
        $nik = trim((string) (Auth::user()->nik ?? ''));
        if ($nik === '') {
            return 0;
        }

        $level = DB::table('hr_master_employee')
            ->where('NIK', $nik)
            ->value('Level');

        return $level === null ? 0 : (int) $level;
    }

    private function checkAccess(): void
    {
        $level = $this->userLevel();
        if ($level <= 5 && !PkAmMapping::isAssociateManager($this->currentNik())) {
            abort(403);
        }
    }

    private function userDept(): string
    {
        $nik = trim((string) (Auth::user()->nik ?? ''));
        if ($nik === '') {
            return '';
        }

        $emp = DB::table('hr_master_employee')
            ->where('NIK', $nik)
            ->first(['Departmen', 'Sub Departmen']);

        if (!$emp) {
            return '';
        }

        return $this->effectiveDept(
            (string) $emp->Departmen,
            (string) ($emp->{'Sub Departmen'} ?? '')
        );
    }

    /**
     * Dept efektif: karyawan factory memakai Sub Departmen, lainnya memakai Departmen.
     */
    private function effectiveDept(string $dept, string $sub): string
    {
        $dept = trim($dept);
        if (strtolower($dept) === 'factory') {
            $sub = trim($sub);
            return $sub !== '' ? $sub : $dept;
        }

        return $dept;
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

    public function index()
    {
        $this->checkAccess();

        $bawahanPeriodes = DB::table('hr_pms_employee_snapshot')->distinct()->pluck('periode')
            ->map(fn ($p) => PmsEmployeeSnapshot::normalizePeriode((string) $p))
            ->filter()
            ->unique()
            ->sortByDesc(fn ($p) => (int) $p)
            ->values()
            ->all();
        $pkPeriode = HrPk::singleYearPeriodes()[0] ?? '';
        $bawahanDefault = in_array($pkPeriode, $bawahanPeriodes, true)
            ? $pkPeriode
            : ($bawahanPeriodes[0] ?? '');

        return view('hr.upload-file-template-ipp-user.upload-file-template-ipp-user', [
            'userNik' => trim((string) (Auth::user()->nik ?? '')),
            'userName' => Auth::user()->name ?? '',
            'userDept' => $this->userDept(),
            'userLevel' => $this->userLevel(),
            'isAssociateManager' => PkAmMapping::isAssociateManager($this->currentNik()),
            'deptOptions' => $this->deptOptions(),
            'positionOptions' => $this->positionOptions(),
            'golonganOptions' => $this->golonganOptions(),
            'periodeOptions' => HrPk::singleYearPeriodes(),
            'bawahanPeriodeOptions' => $bawahanPeriodes,
            'bawahanPeriodeDefault' => $bawahanDefault,
        ]);
    }

    private function deptOptions(): array
    {
        return collect($this->teamMembers())
            ->map(fn ($m) => $this->effectiveDept(
                (string) ($m['departemen'] ?? ''),
                (string) ($m['sub_departemen'] ?? '')
            ))
            ->push($this->userDept())
            ->filter()
            ->unique()
            ->sort()
            ->values()
            ->all();
    }

    private function positionOptions(): array
    {
        return collect($this->teamMembers())
            ->pluck('jabatan')
            ->map(fn ($v) => trim((string) $v))
            ->filter()
            ->unique()
            ->sort()
            ->values()
            ->all();
    }

    public function positionByDept(Request $request)
    {
        $this->checkAccess();

        $dept = trim((string) $request->get('dept', ''));
        if ($dept === '') {
            return response()->json(['data' => []]);
        }

        $positions = collect($this->teamMembers())
            ->filter(fn ($m) => $this->effectiveDept(
                (string) ($m['departemen'] ?? ''),
                (string) ($m['sub_departemen'] ?? '')
            ) === $dept)
            ->pluck('jabatan')
            ->map(fn ($v) => trim((string) $v))
            ->filter()
            ->unique()
            ->sort()
            ->values()
            ->all();

        return response()->json(['data' => $positions]);
    }

    public function positionLevel(Request $request)
    {
        $this->checkAccess();

        $position = trim((string) $request->get('position', ''));
        if ($position === '') {
            return response()->json(['level' => null]);
        }

        $levels = collect($this->teamMembers())
            ->filter(fn ($m) => trim((string) ($m['jabatan'] ?? '')) === $position)
            ->countBy(fn ($m) => (string) ((int) ($m['level'] ?? 0)));

        if ($levels->isEmpty()) {
            return response()->json(['level' => null]);
        }

        $max = $levels->max();
        $level = $levels->filter(fn ($c) => $c === $max)->keys()->sort()->first();

        return response()->json(['level' => $level]);
    }

    private function golonganOptions(): array
    {
        return collect($this->teamMembers())
            ->pluck('level')
            ->map(fn ($v) => (string) ((int) $v))
            ->unique()
            ->sortBy(fn ($v) => (int) $v)
            ->values()
            ->all();
    }

    public function snapshotOptions(Request $request)
    {
        $this->checkAccess();

        $periode = PmsEmployeeSnapshot::normalizePeriode((string) $request->get('periode', ''));
        if ($periode === '') {
            return response()->json([
                'success' => true,
                'has_snapshot' => false,
                'periode' => '',
                'total' => 0,
                'dept' => [],
                'positions' => [],
                'levels' => [],
                'peserta' => [],
                'message' => 'Periode belum dipilih.',
            ]);
        }

        $set = PmsEmployeeSnapshot::optionSetForTeam($periode, $this->currentNik());

        return response()->json([
            'success' => true,
            'has_snapshot' => $set['total'] > 0,
            'periode' => $periode,
            'total' => $set['total'],
            'dept' => $set['dept'],
            'positions' => $set['positions'],
            'levels' => $set['levels'],
            'peserta' => $set['peserta'],
            'message' => $set['total'] > 0
                ? ''
                : 'Peserta periode ' . $periode . ' untuk tim Anda belum di-capture. Silakan buat/upload template PK periode tersebut agar peserta tercapture, lalu ulangi.',
        ]);
    }

    public function downloadTemplate(Request $request)
    {
        $this->checkAccess();

        $template = public_path('templates/hr/template-ipp.xlsx');
        if (!file_exists($template)) {
            abort(404, 'File template tidak ditemukan.');
        }

        $period = trim((string) $request->get('periode', ''));
        $periodeList = HrPk::singleYearPeriodes();
        if (preg_match('/^\d{4}$/', $period) !== 1 || !in_array($period, $periodeList, true)) {
            abort(422, 'Periode Penilaian wajib dipilih dari periode PK yang tersedia: '
                . (implode(', ', $periodeList) ?: '(belum ada)') . '.');
        }

        $niks = $request->input('niks', []);
        if (is_string($niks)) {
            $niks = [$niks];
        }
        $niks = collect($niks)
            ->map(fn ($v) => trim((string) $v))
            ->filter(fn ($v) => $v !== '')
            ->values()
            ->all();

        if (empty($niks)) {
            $single = trim((string) $request->get('nik', ''));
            if ($single !== '') {
                $niks = [$single];
            }
        }

        if (empty($niks)) {
            return response()->download($template, 'template-ipp.xlsx');
        }

        $set = $this->snapshotTeamSet($period);
        if ($set['total'] === 0) {
            abort(422, 'Peserta periode ' . $period . ' untuk tim Anda belum di-capture. Silakan buat/upload template PK periode tersebut agar peserta tercapture, lalu ulangi.');
        }

        $namaByNik = array_column($set['peserta'], 'nama', 'nik');
        $names = [];
        $labels = [];
        foreach ($niks as $nik) {
            if (!isset($namaByNik[$nik])) {
                abort(422, "Peserta '" . $nik . "' tidak terdaftar pada snapshot periode " . $period . ' untuk tim Anda.');
            }
            $nama = trim((string) $namaByNik[$nik]);
            $names[] = $nama;
            $labels[] = $nama . ' - ' . $nik;
        }

        $namaLabel = implode(', ', $labels);
        $fileLabel = count($names) > 1
            ? 'Template IPP - (' . count($names) . ' peserta).xlsx'
            : 'Template IPP - ' . $names[0] . '.xlsx';

        $spreadsheet = IOFactory::load($template);
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('B1', $namaLabel);
        if ($period !== '') {
            $sheet->setCellValue('B2', $period);
        }

        $tmp = tempnam(sys_get_temp_dir(), 'ipp') . '.xlsx';
        IOFactory::createWriter($spreadsheet, 'Xlsx')->save($tmp);

        return response()->download($tmp, $fileLabel)->deleteFileAfterSend(true);
    }

    public function parse(Request $request)
    {
        $this->checkAccess();

        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls',
        ]);

        $file = $request->file('file');

        $dir = 'uploads/hr-ipp-template';
        $filename = time() . '_' . preg_replace('/\s+/', '_', $file->getClientOriginalName());
        $path = $file->storeAs($dir, $filename, 'public');
        $fullPath = storage_path('app/public/' . $path);

        try {
            $import = new TemplateIppImport();
            Excel::import($import, $fullPath);
            $parsed = $import->parsed;
        } catch (\Throwable $e) {
            Storage::disk('public')->delete($path);
            return response()->json([
                'success' => false,
                'message' => 'Gagal membaca file: ' . $e->getMessage(),
            ], 422);
        } finally {
            Storage::disk('public')->delete($path);
        }

        if (empty($parsed['periode'])) {
            return response()->json([
                'success' => false,
                'message' => 'Periode Penilaian tidak ditemukan. Pastikan baris 2 berisi PERIODE PENILAIAN sesuai Template IPP.',
            ], 422);
        }

        $periodeList = HrPk::singleYearPeriodes();
        $periodeParsed = trim((string) $parsed['periode']);
        if (preg_match('/^\d{4}$/', $periodeParsed) !== 1 || !in_array($periodeParsed, $periodeList, true)) {
            return response()->json([
                'success' => false,
                'message' => "Periode Penilaian '{$periodeParsed}' tidak valid. Gunakan satu tahun yang terdaftar pada PK: "
                    . (implode(', ', $periodeList) ?: '(belum ada)') . '.',
            ], 422);
        }

        if (empty($parsed['details'])) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak ada data KPI yang ditemukan pada file. Pastikan format mengikuti Template IPP.',
            ], 422);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'periode' => $parsed['periode'],
                'nama' => $parsed['nama'] ?? null,
                'nik_peserta' => $parsed['nik_peserta'] ?? [],
                'details' => $parsed['details'],
            ],
        ]);
    }

    public function store(Request $request)
    {
        $this->checkAccess();

        $data = $this->validatePayload($request);

        $uploaderNik = trim((string) (Auth::user()->nik ?? ''));
        $username = Auth::user()->username ?? (Auth::user()->name ?? 'system');
        $sendBy = $uploaderNik !== '' ? $uploaderNik : $username;

        $nikPesertaInput = $request->input('nik_peserta', []);
        if (is_string($nikPesertaInput)) {
            $nikPesertaInput = [$nikPesertaInput];
        }
        $nikPesertaList = collect($nikPesertaInput)
            ->map(fn ($v) => trim((string) $v))
            ->filter(fn ($v) => $v !== '')
            ->unique()
            ->values()
            ->all();

        $pisahkan = filter_var($request->input('pisahkan_template', false), FILTER_VALIDATE_BOOLEAN);

        $this->assertSnapshotTeamPayload($data, $nikPesertaList, true);

        $ipp = null;
        $registered = [];
        $createdIds = [];
        $notifPeserta = [];
        DB::transaction(function () use ($data, $sendBy, $uploaderNik, $nikPesertaList, $pisahkan, &$ipp, &$registered, &$createdIds, &$notifPeserta) {
            $header = function () use ($data, $sendBy) {
                return HrIpp::create([
                    'periode' => $data['periode'],
                    'dept' => ($data['dept'] !== null && trim($data['dept']) !== '') ? $data['dept'] : $this->userDept(),
                    'position' => $data['position'],
                    'golongan' => $data['golongan'],
                    'send_by_username' => $sendBy,
                ]);
            };

            $userLevel = $this->userLevel();
            $me = ['nik' => $uploaderNik, 'nama' => Auth::user()->name ?? null];

            $registerTo = function (HrIpp $target, string $nikPeserta) use ($data, $uploaderNik, $userLevel, $me, $sendBy, &$registered, &$notifPeserta): bool {
                $snap = PmsEmployeeSnapshot::attributesFor($data['periode'], $nikPeserta);
                if ($snap) {
                    $ybs = [
                        'nik' => trim((string) $snap->nik),
                        'name' => trim((string) $snap->nama),
                        'level' => (int) $snap->level,
                    ];
                } else {
                    $ybs = $this->resolveSubordinateByNik($nikPeserta, $userLevel);
                    if (!$ybs) {
                        return false;
                    }
                }

                $needAssoc = !in_array($ybs['level'], [6, 7], true);
                $manager = null;
                $associate = null;

                if ($snap) {
                    $manager = $this->snapshotAtasanPair($snap, 'manager_nik', 'manager_nama');
                    $associate = $needAssoc
                        ? $this->snapshotAtasanPair($snap, 'associate_manager_nik', 'associate_manager_nama')
                        : null;
                } else {
                    $suggest = $this->suggestAtasanFor($nikPeserta);

                    if (($userLevel >= 6 && $userLevel <= 7) || PkAmMapping::isAssociateManager($uploaderNik)) {
                        $associate = $needAssoc ? $me : null;
                        $manager = $suggest['manager'][0] ?? null;
                    } else {
                        $manager = $me;
                        $associate = $needAssoc ? ($suggest['associate_manager'][0] ?? null) : null;
                    }
                }

                $oto = HrIppOtorisasi::create([
                    'ipp_id' => $target->id,
                    'nik_yang_bersangkutan' => $ybs['nik'],
                    'nama_yang_bersangkutan' => $ybs['name'],
                    'nik_manager' => $manager ? $manager['nik'] : null,
                    'nama_manager' => $manager ? $manager['nama'] : null,
                    'nik_associate_manager' => $associate ? $associate['nik'] : null,
                    'nama_associate_manager' => $associate ? $associate['nama'] : null,
                    'send_by_username' => $sendBy,
                ]);

                $this->snapshotIppParticipant($target, $oto);

                $registered[] = $ybs['nik'] . ' - ' . $ybs['name'];
                $notifPeserta[$ybs['nik']] = ['nik' => $ybs['nik'], 'nama' => $ybs['name']];
                return true;
            };

            if (!empty($nikPesertaList) && $pisahkan) {
                foreach ($nikPesertaList as $nikPeserta) {
                    $ippX = $header();
                    if (!$registerTo($ippX, $nikPeserta)) {
                        $ippX->delete();
                        continue;
                    }
                    $this->saveDetails($ippX->id, $data['details']);
                    $createdIds[] = $ippX->id;
                }
                $ipp = $createdIds ? HrIpp::find($createdIds[0]) : null;
            } else {
                $ipp = $header();
                $this->saveDetails($ipp->id, $data['details']);
                if (!empty($nikPesertaList)) {
                    foreach ($nikPesertaList as $nikPeserta) {
                        $registerTo($ipp, $nikPeserta);
                    }
                }
            }
        });

        if (!empty($notifPeserta) && HrPmsControlNotifikasi::isActive()) {
            SendIppUserNotificationJob::dispatch($data['periode'], array_values($notifPeserta), Auth::user()->name);
        }

        $autoMsg = '';
        if (!empty($registered)) {
            if (!empty($createdIds)) {
                $autoMsg = ' ' . count($createdIds) . ' IPP terpisah dibuat: ' . implode(', ', $registered) . '.';
            } else {
                $autoMsg = ' ' . count($registered) . ' Peserta didaftarkan otomatis: ' . implode(', ', $registered) . '.';
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Data IPP berhasil disimpan.' . $autoMsg,
            'warnings' => $data['warnings'] ?? [],
            'id' => $ipp ? $ipp->id : null,
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

        $perPage = (int) $request->get('per_page', 10);
        $page = (int) $request->get('page', 1);
        $search = trim((string) $request->get('search', ''));

        $uploaderNik = trim((string) (Auth::user()->nik ?? ''));
        $username = Auth::user()->username ?? (Auth::user()->name ?? 'system');

        $connectedIppIds = HrIppOtorisasi::where(function ($q) use ($uploaderNik) {
            $q->where('nik_manager', $uploaderNik)
                ->orWhere('nik_associate_manager', $uploaderNik);
        })
            ->pluck('ipp_id');

        $query = HrIpp::query()->withCount('details')
            ->where(function ($q) use ($uploaderNik, $username, $connectedIppIds) {
                $q->where('send_by_username', $username)
                    ->orWhere('send_by_username', $uploaderNik);
                if ($connectedIppIds->isNotEmpty()) {
                    $q->orWhereIn('id', $connectedIppIds->all());
                }
            });

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('periode', 'like', "%{$search}%")
                    ->orWhere('dept', 'like', "%{$search}%")
                    ->orWhere('position', 'like', "%{$search}%")
                    ->orWhere('golongan', 'like', "%{$search}%")
                    ->orWhere('send_by_username', 'like', "%{$search}%")
                    ->orWhereHas('otorisasi', function ($q2) use ($search) {
                        $q2->where('nik_yang_bersangkutan', 'like', "%{$search}%")
                            ->orWhere('nama_yang_bersangkutan', 'like', "%{$search}%");
                    })
                    ->orWhereIn('send_by_username', DB::table('users')
                        ->where('name', 'like', "%{$search}%")
                        ->whereNotNull('nik')
                        ->pluck('nik')
                        ->merge(DB::table('users')->where('name', 'like', "%{$search}%")->pluck('username'))
                        ->filter()
                        ->unique()
                        ->all());
            });
        }

        $total = (clone $query)->count();
        $lastPage = max(1, (int) ceil($total / $perPage));

        $rows = $query->orderBy('id', 'desc')
            ->forPage($page, $perPage)
            ->get();

        $pageIds = $rows->pluck('id')->all();
        $pesertaByIpp = [];
        if (!empty($pageIds)) {
            $otoRows = HrIppOtorisasi::whereIn('ipp_id', $pageIds)
                ->orderBy('id')
                ->get(['ipp_id', 'nik_yang_bersangkutan', 'nama_yang_bersangkutan']);
            foreach ($otoRows as $o) {
                $pesertaByIpp[$o->ipp_id][] = [
                    'nik' => $o->nik_yang_bersangkutan,
                    'nama' => $o->nama_yang_bersangkutan,
                ];
            }
        }

        $uploaderMap = $this->resolveUploaders($rows->pluck('send_by_username'));

        $rows = $rows->map(function ($ipp) use ($uploaderMap, $pesertaByIpp) {
            $peserta = $pesertaByIpp[$ipp->id] ?? [];
            $uploader = $ipp->send_by_username ? ($uploaderMap[$ipp->send_by_username] ?? null) : null;

            return [
                'id' => $ipp->id,
                'periode' => $ipp->periode,
                'dept' => $ipp->dept,
                'position' => $ipp->position,
                'golongan' => $ipp->golongan,
                'send_by_username' => $ipp->send_by_username,
                'uploaded_by' => $uploader
                    ? ($uploader['nik'] . ' - ' . $uploader['name'])
                    : ($ipp->send_by_username ?? '-'),
                'detail_count' => (int) $ipp->details_count,
                'otorisasi_count' => count($peserta),
                'peserta' => $peserta,
                'created_at' => optional($ipp->created_at)->format('d M Y H:i'),
            ];
        });

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

    public function show($id)
    {
        $this->checkAccess();

        $ipp = HrIpp::with(['details.masterTargets'])->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $ipp->id,
                'periode' => $ipp->periode,
                'dept' => $ipp->dept,
                'position' => $ipp->position,
                'golongan' => $ipp->golongan,
                'send_by_username' => $ipp->send_by_username,
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
            ],
        ]);
    }

    public function update($id, Request $request)
    {
        $this->checkAccess();

        $ipp = HrIpp::findOrFail($id);

        $data = $this->validatePayload($request);

        $this->assertSnapshotTeamPayload($data, $this->requestedNikPeserta($request), false);

        DB::transaction(function () use ($ipp, $data) {
            $ipp->update([
                'periode' => $data['periode'],
                'dept' => ($data['dept'] !== null && trim($data['dept']) !== '') ? $data['dept'] : $this->userDept(),
                'position' => $data['position'],
                'golongan' => $data['golongan'],
            ]);

            $this->saveDetails($ipp->id, $data['details']);
        });

        return response()->json([
            'success' => true,
            'message' => 'Data IPP berhasil diperbarui.',
            'warnings' => $data['warnings'] ?? [],
        ]);
    }

    public function delete($id)
    {
        $this->checkAccess();

        $ipp = HrIpp::findOrFail($id);
        $ipp->delete();

        return response()->json([
            'success' => true,
            'message' => 'Data IPP berhasil dihapus.',
        ]);
    }

    private function validatePayload(Request $request): array
    {
        $periodeList = HrPk::singleYearPeriodes();

        $validated = $request->validate([
            'periode' => ['required', 'string', 'max:50', 'regex:/^\d{4}$/', Rule::in($periodeList)],
            'dept' => 'nullable|string|max:150',
            'position' => 'nullable|string|max:200',
            'golongan' => 'nullable|string|max:50',
            'details' => 'required|array|min:1',
            'details.*.no_urut' => 'required|integer',
            'details.*.objective' => 'nullable|string|max:255',
            'details.*.kpa' => 'nullable|string|max:255',
            'details.*.kpi' => 'required|string|max:255',
            'details.*.classification' => 'required|string|in:High Is Good,High Is Bad,Max,Min,Standard Score,Range',
            'details.*.uom' => 'nullable|string|max:100',
            'details.*.weight' => 'nullable|numeric|min:0|max:100',
            'details.*.target' => 'nullable|string|max:255',
            'details.*.due_date' => 'nullable|string|max:100',
            'details.*.range_boundaries' => 'nullable|array',
            'details.*.range_boundaries.*' => 'nullable|numeric',
            'details.*.range_scores' => 'nullable|array',
            'details.*.range_scores.*' => 'nullable|integer|between:1,5',
            'details.*.range_direction' => 'nullable|string|in:high_is_good,low_is_good',
            'details.*.range_match_type' => 'nullable|string|in:range,equals,custom',
            'details.*.range_labels' => 'nullable|array',
            'details.*.range_labels.*' => 'nullable|string|max:100',
            'details.*.range_tiers' => 'nullable|array',
            'details.*.range_tiers.*.score' => 'nullable|integer|between:1,5',
            'details.*.range_tiers.*.match_type' => 'nullable|string|in:range,equals,gte,lte,eq,between',
            'details.*.range_tiers.*.min_value' => 'nullable|numeric',
            'details.*.range_tiers.*.max_value' => 'nullable|numeric',
        ], [
            'periode.required' => 'Periode Penilaian (Tahun) wajib dipilih.',
            'periode.regex' => 'Periode Penilaian harus satu tahun (contoh: 2026), tidak boleh rentang.',
            'periode.in' => 'Periode Penilaian harus salah satu periode PK yang tersedia: '
                . (implode(', ', $periodeList) ?: '(belum ada)') . '.',
            'details.*.classification.required' => 'Classification detail wajib diisi.',
            'details.*.classification.in' => 'Classification detail harus salah satu dari: High Is Good, High Is Bad, Max, Min, Standard Score, Range.',
            'details.*.range_boundaries.*.numeric' => 'Boundary Range baris terkait harus berupa angka.',
            'details.*.range_scores.*.integer' => 'Skor Range baris terkait harus berupa angka bulat.',
            'details.*.range_scores.*.between' => 'Skor Range baris terkait harus bernilai 1-5.',
            'details.*.range_direction.in' => 'Arah skala Range tidak dikenali.',
            'details.*.range_match_type.in' => 'Mode Range baris terkait tidak dikenali.',
            'details.*.range_labels.*.max' => 'Label Range baris terkait maksimal 100 karakter.',
            'details.*.range_tiers.*.match_type.in' => 'Operator Custom baris terkait tidak dikenali.',
            'details.*.range_tiers.*.min_value.numeric' => 'Nilai Custom baris terkait harus berupa angka.',
            'details.*.range_tiers.*.max_value.numeric' => 'Nilai Custom baris terkait harus berupa angka.',
        ]);

        $warnings = [];
        $details = $this->validateRangeTargets(array_values($validated['details']), $warnings);
        $this->assertTotalWeight($details);

        return [
            'periode' => $validated['periode'],
            'dept' => $validated['dept'] ?? null,
            'position' => $validated['position'] ?? null,
            'golongan' => $validated['golongan'] ?? null,
            'details' => $details,
            'warnings' => $warnings,
        ];
    }

    private function assertTotalWeight(array $details): void
    {
        $total = 0.0;
        foreach ($details as $detail) {
            $weight = $detail['weight'] ?? null;
            if ($weight === null || $weight === '') {
                continue;
            }
            $total += (float) $weight;
        }

        if (abs($total - 100.0) < 1e-9) {
            return;
        }

        $diff = round(abs(100.0 - $total), 2);
        $kata = $total > 100.0 ? 'kelebihan' : 'kekurangan';

        abort(422, 'Total Weight ' . $this->formatWeight($total) . '% — ' . $kata . ' '
            . $this->formatWeight($diff) . '%. Total weight harus 100%.');
    }

    private function formatWeight(float $value): string
    {
        return rtrim(rtrim(number_format($value, 2, '.', ''), '0'), '.');
    }

    private function snapshotTeamSet(string $periode): array
    {
        return PmsEmployeeSnapshot::optionSetForTeam($periode, $this->currentNik());
    }

    private function requestedNikPeserta(Request $request): array
    {
        $input = $request->input('nik_peserta', []);
        if (is_string($input)) {
            $input = [$input];
        }

        return collect((array) $input)
            ->map(fn ($v) => trim((string) $v))
            ->filter(fn ($v) => $v !== '')
            ->unique()
            ->values()
            ->all();
    }

    private function assertSnapshotTeamPayload(array $data, array $nikPesertaList, bool $wajibSnapshotTim): void
    {
        $periode = PmsEmployeeSnapshot::normalizePeriode((string) ($data['periode'] ?? ''));
        if ($periode === '') {
            return;
        }

        $set = $this->snapshotTeamSet($periode);

        if ($set['total'] === 0) {
            if ($wajibSnapshotTim) {
                abort(422, 'Peserta periode ' . $periode . ' untuk tim Anda belum di-capture. Silakan buat/upload template PK periode tersebut agar peserta tercapture, lalu ulangi.');
            }

            return;
        }

        $dept = trim((string) ($data['dept'] ?? ''));
        if ($dept !== '' && !in_array($dept, $set['dept'], true)) {
            abort(422, 'Dept "' . $dept . '" tidak ada pada snapshot periode ' . $periode . ' untuk tim Anda.');
        }

        $position = trim((string) ($data['position'] ?? ''));
        if ($position !== '' && !in_array($position, array_column($set['positions'], 'v'), true)) {
            abort(422, 'Position "' . $position . '" tidak ada pada snapshot periode ' . $periode . ' untuk tim Anda.');
        }

        $golongan = trim((string) ($data['golongan'] ?? ''));
        if ($golongan !== '' && !in_array($golongan, $set['levels'], true)) {
            abort(422, 'Golongan "' . $golongan . '" tidak ada pada snapshot periode ' . $periode . ' untuk tim Anda.');
        }

        if ($nikPesertaList) {
            $known = array_flip(array_column($set['peserta'], 'nik'));
            foreach ($nikPesertaList as $nik) {
                if (!isset($known[$nik])) {
                    abort(422, "Peserta '" . $nik . "' tidak terdaftar pada snapshot periode " . $periode . ' untuk tim Anda.');
                }
            }
        }
    }

    private function assertNikInTeamSnapshot(string $periode, string $nik): void
    {
        $set = $this->snapshotTeamSet($periode);
        if ($set['total'] === 0) {
            return;
        }

        if (!isset(array_flip(array_column($set['peserta'], 'nik'))[$nik])) {
            abort(422, "Peserta '" . $nik . "' tidak terdaftar pada snapshot periode "
                . PmsEmployeeSnapshot::normalizePeriode($periode) . ' untuk tim Anda.');
        }
    }

    private function snapshotAtasanPair($snap, string $nikField, string $namaField): ?array
    {
        $nik = trim((string) ($snap->{$nikField} ?? ''));
        if ($nik === '') {
            return null;
        }

        $nama = trim((string) ($snap->{$namaField} ?? ''));
        if ($nama === '') {
            $nama = trim((string) DB::table('hr_master_employee')->where('NIK', $nik)->value('Nama'));
        }

        return ['nik' => $nik, 'nama' => $nama];
    }

    private function validateRangeTargets(array $details, ?array &$warnings = null): array
    {
        foreach ($details as $i => $detail) {
            $line = $i + 1;
            if (($detail['classification'] ?? '') !== 'Range') {
                $target = trim((string) ($detail['target'] ?? ''));
                if ($target !== '' && !is_numeric(str_replace(',', '.', $target))) {
                    $kpi = trim((string) ($detail['kpi'] ?? ''));
                    $warnings[] = 'Baris ke-' . $line . ($kpi !== '' ? ' (' . $kpi . ')' : '')
                        . ': Target sebaiknya berupa angka (contoh: 10.2); perhitungan achievement bisa tidak akurat.';
                }
                continue;
            }

            // Mode `==`: 5 label teks (skor 1-5) wajib terisi semua.
            if (($detail['range_match_type'] ?? null) === IppRangeScale::MATCH_EQUALS) {
                $labels = $detail['range_labels'] ?? null;
                $tiers = is_array($labels) ? IppRangeScale::buildTextTiers($labels) : [];
                if (!empty($tiers)) {
                    $details[$i]['range_tiers'] = $tiers;
                    $details[$i]['range_direction'] = null;
                    $details[$i]['target'] = IppRangeScale::summarizeText(array_map(fn ($t) => $t['label'], $tiers));
                    continue;
                }

                $details[$i]['target'] = null;
                $details[$i]['range_tiers'] = null;
                $kpi = trim((string) ($detail['kpi'] ?? ''));
                $warnings[] = 'Baris ke-' . $line . ($kpi !== '' ? ' (' . $kpi . ')' : '')
                    . ': Target Range tidak disimpan — mode == wajib mengisi 5 label teks pada editor Range.';
                continue;
            }

            // Mode Custom: aturan per skor (operator = / => / =< / rentang) wajib rapat & tidak tumpang tindih.
            if (($detail['range_match_type'] ?? null) === 'custom') {
                $rows = $detail['range_tiers'] ?? null;
                $rows = is_array($rows) ? array_values($rows) : [];
                $rangeError = IppRangeScale::validateCustomRows($rows);
                if ($rangeError === null) {
                    $tiers = IppRangeScale::buildCustomTiers($rows);
                    $details[$i]['range_tiers'] = $tiers;
                    $details[$i]['range_scores'] = array_map(fn ($t) => (int) $t['score'], $tiers);
                    $details[$i]['range_direction'] = null;
                    $details[$i]['target'] = IppRangeScale::summarizeCustom($tiers);
                    continue;
                }

                $details[$i]['target'] = null;
                $details[$i]['range_tiers'] = null;
                $kpi = trim((string) ($detail['kpi'] ?? ''));
                $warnings[] = 'Baris ke-' . $line . ($kpi !== '' ? ' (' . $kpi . ')' : '')
                    . ': Target Range tidak disimpan — ' . $rangeError;
                continue;
            }

            // Wajib: N-1 boundary + daftar skor aktif (mis. [1,3,5]) dari editor.
            $boundaries = $detail['range_boundaries'] ?? null;
            $boundaries = is_array($boundaries) ? array_values($boundaries) : [];
            $scores = IppRangeScale::normalizeScores($detail['range_scores'] ?? null);
            if (empty($scores) && count($boundaries) === 4) {
                // Kompatibilitas payload lama: 4 boundary tanpa daftar skor.
                $scores = [1, 2, 3, 4, 5];
            }

            if (empty($scores) || count($boundaries) === 0) {
                $details[$i]['target'] = null;
                $details[$i]['range_tiers'] = null;
                $kpi = trim((string) ($detail['kpi'] ?? ''));
                $warnings[] = 'Baris ke-' . $line . ($kpi !== '' ? ' (' . $kpi . ')' : '')
                    . ': Target Range tidak disimpan — wajib mengisi boundary pada editor Range.';
                continue;
            }

            $rangeError = IppRangeScale::validateRangeSet($scores, $boundaries);
            if ($rangeError === null) {
                $direction = IppRangeScale::normalizeDirection($detail['range_direction'] ?? null);
                $tiers = IppRangeScale::buildTiersForScores($scores, $boundaries, $direction);
                $details[$i]['range_tiers'] = $tiers;
                $details[$i]['range_scores'] = $scores;
                $details[$i]['range_direction'] = $direction;
                $details[$i]['target'] = IppRangeScale::summarize($boundaries, $direction, $scores);
                continue;
            }

            // Format salah/kurang: baris tetap disimpan, target TIDAK disimpan, tier dibersihkan.
            $details[$i]['target'] = null;
            $details[$i]['range_tiers'] = null;
            $kpi = trim((string) ($detail['kpi'] ?? ''));
            $warnings[] = 'Baris ke-' . $line . ($kpi !== '' ? ' (' . $kpi . ')' : '')
                . ': Target Range tidak disimpan — ' . $rangeError;
        }
        return $details;
    }

    public function aktualList($ippId)
    {
        $this->checkAccess();

        $ipp = HrIpp::with(['details.masterTargets'])->findOrFail($ippId);

        $detailIds = $ipp->details->pluck('id');

        $otorisasiRows = HrIppOtorisasi::where('ipp_id', $ippId)->orderBy('id')->get();

        $aktual = [];
        $aktualVarchar = [];
        $aktualAtasan = [];
        $aktualAtasanVarchar = [];
        $aktualAtasanBy = [];
        $achievement = [];
        $achievementScore = [];
        $scoreScale = [];
        $aktualRows = HrIppAktual::whereIn('ipp_otorisasi_id', $otorisasiRows->pluck('id'))->get();
        foreach ($aktualRows as $a) {
            $aktual[$a->ipp_otorisasi_id][$a->ipp_detail_id][$a->tahapan] = $a->aktual;
            $aktualVarchar[$a->ipp_otorisasi_id][$a->ipp_detail_id][$a->tahapan] = $a->aktual_varchar;
            $aktualAtasan[$a->ipp_otorisasi_id][$a->ipp_detail_id][$a->tahapan] = $a->aktual_atasan;
            $aktualAtasanVarchar[$a->ipp_otorisasi_id][$a->ipp_detail_id][$a->tahapan] = $a->aktual_atasan_varchar;
            $aktualAtasanBy[$a->ipp_otorisasi_id][$a->ipp_detail_id][$a->tahapan] = $a->aktual_atasan_by;
            $achievement[$a->ipp_otorisasi_id][$a->ipp_detail_id][$a->tahapan] = $a->achievement_category;
            $achievementScore[$a->ipp_otorisasi_id][$a->ipp_detail_id][$a->tahapan] = $a->achievement_score;
            $scoreScale[$a->ipp_otorisasi_id][$a->ipp_detail_id][$a->tahapan] = $a->score_scale;
        }

        return response()->json([
            'success' => true,
            'data' => [
                'ipp_id' => $ipp->id,
                'periode' => $ipp->periode,
                'dept' => $ipp->dept,
                'position' => $ipp->position,
                'golongan' => $ipp->golongan,
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
                'otorisasi' => $otorisasiRows->map(function ($o) {
                    return [
                        'id' => $o->id,
                        'nik_yang_bersangkutan' => $o->nik_yang_bersangkutan,
                        'nama_yang_bersangkutan' => $o->nama_yang_bersangkutan,
                        'nik_manager' => $o->nik_manager,
                        'nama_manager' => $o->nama_manager,
                        'nik_associate_manager' => $o->nik_associate_manager,
                        'nama_associate_manager' => $o->nama_associate_manager,
                    ];
                }),
                'aktual' => $aktual,
                'aktual_varchar' => $aktualVarchar,
                'aktual_atasan' => $aktualAtasan,
                'aktual_atasan_varchar' => $aktualAtasanVarchar,
                'aktual_atasan_by' => $aktualAtasanBy,
                'achievement_category' => $achievement,
                'achievement_score' => $achievementScore,
                'score_scale' => $scoreScale,
                'tahapan' => $this->buildTahapan($ipp->periode),
            ],
        ]);
    }

    // === Otorisasi (NIK yang bersangkutan dari hr_master_employee, NIK atasan = user login) ===

    private function currentNik(): string
    {
        return trim((string) (Auth::user()->nik ?? ''));
    }

    private function mappedSubDepartments(): array
    {
        $nik = $this->currentNik();
        if ($nik === '') {
            return [];
        }

        return HrIppManagerMapping::where('nik_manager', $nik)
            ->whereNotNull('sub_departmen')
            ->pluck('sub_departmen')
            ->map(fn ($v) => trim((string) $v))
            ->filter()
            ->unique()
            ->values()
            ->all();
    }

    private function resolveSubordinateByNik(string $nik, int $userLevel): ?array
    {
        $nik = trim($nik);
        if ($nik === '') {
            return null;
        }

        $emp = DB::table('hr_master_employee')
            ->where('NIK', $nik)
            ->where('Aktif', 'Y')
            ->whereRaw('CAST(Level AS UNSIGNED) > 3')
            ->whereRaw('CAST(Level AS UNSIGNED) < ?', [$userLevel])
            ->first(['NIK', 'Nama', 'Level']);

        if (!$emp) {
            $mapped = $this->pkMappedRow($nik);
            if (!$mapped) {
                return null;
            }

            $emp = DB::table('hr_master_employee')
                ->where('NIK', $nik)
                ->first(['NIK', 'Nama', 'Level']);

            if (!$emp) {
                $name = trim((string) $mapped->nama_yang_bersangkutan);
                if ($name === '') {
                    return null;
                }

                return ['nik' => $nik, 'name' => $name, 'level' => 0];
            }
        }

        return [
            'nik' => $emp->NIK,
            'name' => $emp->Nama,
            'level' => (int) $emp->Level,
        ];
    }

    private function pkMappedRow(string $nik): ?HrPkOtorisasi
    {
        $me = $this->currentNik();
        if ($me === '') {
            return null;
        }

        return HrPkOtorisasi::where('nik_yang_bersangkutan', $nik)
            ->where(function ($q) use ($me) {
                $q->where('nik_manager', $me)
                    ->orWhere('nik_associate_manager', $me);
            })
            ->first();
    }

    private function pkMappedNiks(): array
    {
        $me = $this->currentNik();
        if ($me === '') {
            return [];
        }

        return HrPkOtorisasi::where(function ($q) use ($me) {
            $q->where('nik_manager', $me)
                ->orWhere('nik_associate_manager', $me);
        })
            ->pluck('nik_yang_bersangkutan')
            ->map(fn ($v) => trim((string) $v))
            ->filter()
            ->unique()
            ->values()
            ->all();
    }

    public function searchNik(Request $request)
    {
        $this->checkAccess();

        $userLevel = $this->userLevel();
        if ($userLevel <= 0) {
            return response()->json(['data' => []]);
        }

        $nik = trim((string) (Auth::user()->nik ?? ''));
        if ($nik === '') {
            return response()->json(['data' => []]);
        }

        $me = DB::table('hr_master_employee')
            ->where('NIK', $nik)
            ->first(['Departmen', 'Sub Departmen']);
        if (!$me) {
            return response()->json(['data' => []]);
        }

        $sub = trim((string) ($me->{'Sub Departmen'} ?? ''));
        $dept = trim((string) ($me->Departmen ?? ''));

        $term = trim((string) $request->get('term', ''));
        $role = trim((string) $request->get('role', ''));
        $ippDept = trim((string) $request->get('dept', ''));

        if ($role === '' && $term === '') {
            $rows = collect($this->teamMembers())
                ->take(50)
                ->map(fn ($m) => ['nik' => $m['nik'], 'name' => $m['nama']])
                ->values();

            return response()->json(['data' => $rows]);
        }

        $scopeDept = $ippDept !== '' ? $ippDept : $dept;
        $scopeSub = $ippDept !== '' ? '' : $sub;
        $scopeSubs = [];
        if ($ippDept === '' && strtolower($dept) === 'factory') {
            $scopeSubs = collect($sub !== '' ? [$sub] : [])->merge($this->mappedSubDepartments())->unique()->values()->all();
        }

        $query = DB::table('hr_master_employee')
            ->select('NIK as nik', 'Nama as name')
            ->where('Aktif', 'Y')
            ->whereIn('NIK', DB::table('users')->select('nik'))
            ->when($term === '', function ($q) use ($scopeSub, $scopeDept, $role, $ippDept, $scopeSubs) {
                $q->where(function ($q2) use ($scopeSub, $scopeDept, $role, $ippDept, $scopeSubs) {
                    if ($role !== '') {
                        if ($ippDept !== '') {
                            $q2->where(function ($q3) use ($scopeDept) {
                                $q3->where('Departmen', $scopeDept)
                                    ->orWhere('Sub Departmen', $scopeDept);
                            });
                        } else {
                            $q2->where('Departmen', $scopeDept);
                        }
                    } elseif ($scopeSub !== '') {
                        if ($scopeSubs !== []) {
                            $q2->whereIn('Sub Departmen', $scopeSubs);
                        } else {
                            $q2->where('Sub Departmen', $scopeSub);
                        }
                    } else {
                        $q2->where(function ($q3) use ($scopeDept) {
                            $q3->where('Departmen', $scopeDept)
                                ->orWhere('Sub Departmen', $scopeDept);
                        });
                    }
                });
            });

        if ($role === 'manager') {
            $query->whereRaw('CAST(Level AS UNSIGNED) BETWEEN 8 AND 13');
        } elseif ($role === 'associate') {
            $query->whereRaw('CAST(Level AS UNSIGNED) BETWEEN 5 AND 7');
        } else {
            $eligible = $this->snapshotEligibleNiks();
            $query->where(function ($q) use ($userLevel, $eligible) {
                $q->where(function ($q2) use ($userLevel) {
                    $q2->whereRaw('CAST(Level AS UNSIGNED) > 3')
                        ->whereRaw('CAST(Level AS UNSIGNED) < ?', [$userLevel]);
                });
                if (!empty($eligible)) {
                    $q->orWhereIn('NIK', $eligible);
                }
            });
        }

        if ($term !== '') {
            $query->where(function ($q) use ($term) {
                $q->where('NIK', 'like', "%{$term}%")
                    ->orWhere('Nama', 'like', "%{$term}%");
            });
        }

        $rows = $query
            ->orderBy('NIK')
            ->limit($term !== '' ? 10 : 50)
            ->get()
            ->map(function ($u) {
                return [
                    'nik' => $u->nik,
                    'name' => $u->name,
                ];
            });

        if ($role === '') {
            $mappedNiks = $this->pkMappedNiks();
            if (!empty($mappedNiks)) {
                $mappedRows = DB::table('hr_master_employee')
                    ->select('NIK as nik', 'Nama as name')
                    ->whereIn('NIK', $mappedNiks)
                    ->when($term !== '', function ($q) use ($term) {
                        $q->where(function ($q2) use ($term) {
                            $q2->where('NIK', 'like', "%{$term}%")
                                ->orWhere('Nama', 'like', "%{$term}%");
                        });
                    })
                    ->get()
                    ->map(function ($u) {
                        return [
                            'nik' => $u->nik,
                            'name' => $u->name,
                        ];
                    });

                $rows = $rows->concat($mappedRows)
                    ->unique('nik')
                    ->sortBy('nik')
                    ->take($term !== '' ? 10 : 50)
                    ->values();
            }
        }

        return response()->json(['data' => $rows]);
    }

    public function suggestAtasan(Request $request)
    {
        $this->checkAccess();

        $nik = trim((string) $request->get('nik', ''));

        return response()->json($this->suggestAtasanFor($nik));
    }

    private function suggestAtasanFor(string $nik): array
    {
        $managers = [];
        $associates = [];

        if ($nik !== '') {
            $emp = DB::table('hr_master_employee')
                ->where('NIK', $nik)
                ->where('Aktif', 'Y')
                ->first(['NIK', 'Departmen', 'Sub Departmen', 'Section', 'Level']);

            if (!$emp && $this->pkMappedRow($nik)) {
                $emp = DB::table('hr_master_employee')
                    ->where('NIK', $nik)
                    ->first(['NIK', 'Departmen', 'Sub Departmen', 'Section', 'Level']);
            }

            if ($emp) {
                $dept = trim((string) $emp->Departmen);
                $sub = trim((string) ($emp->{'Sub Departmen'} ?? ''));
                $userNiks = DB::table('users')->whereNotNull('nik')->where('nik', '!=', '')->pluck('nik');

                $otoManager = (new PkAtasan())->resolveManager($emp);
                if ($otoManager && ($otoManager['source'] ?? '') === 'otorisasi') {
                    $managers = [['nik' => $otoManager['nik'], 'nama' => $otoManager['nama']]];
                } elseif (strtolower($dept) === 'factory') {
                    $map = HrIppManagerMapping::where('sub_departmen', $sub)->first();
                    if ($map && trim((string) $map->nik_manager) !== '') {
                        $managers = $this->resolveSuggestUser((string) $map->nik_manager);
                    } elseif ($sub !== '') {
                        $managers = DB::table('hr_master_employee')
                            ->select('NIK as nik', 'Nama as nama', 'Level as level')
                            ->where('Aktif', 'Y')
                            ->where('Sub Departmen', $sub)
                            ->whereIn('NIK', $userNiks)
                            ->whereRaw('CAST(Level AS UNSIGNED) BETWEEN 8 AND 13')
                            ->orderByRaw('CAST(Level AS UNSIGNED) ASC, NIK ASC')
                            ->limit(20)
                            ->get()
                            ->map(fn ($r) => ['nik' => $r->nik, 'nama' => $r->nama])
                            ->values()
                            ->all();
                    }
                } elseif ($dept !== '') {
                    $managers = DB::table('hr_master_employee')
                        ->select('NIK as nik', 'Nama as nama', 'Level as level')
                        ->where('Aktif', 'Y')
                        ->where('Departmen', $dept)
                        ->whereIn('NIK', $userNiks)
                        ->whereRaw('CAST(Level AS UNSIGNED) BETWEEN 8 AND 13')
                        ->orderByRaw('CAST(Level AS UNSIGNED) ASC, NIK ASC')
                        ->limit(20)
                        ->get()
                        ->map(fn ($r) => ['nik' => $r->nik, 'nama' => $r->nama])
                        ->values()
                        ->all();
                }

                $am = (new PkAtasan())->resolveAssociateManager($emp);
                if ($am) {
                    $associates = [['nik' => $am['nik'], 'nama' => $am['nama']]];
                }
            }
        }

        return [
            'manager' => $managers,
            'associate_manager' => $associates,
        ];
    }

    private function resolveSuggestUser(string $nik): array
    {
        $row = DB::table('users')->where('nik', $nik)->first(['nik', 'name']);
        if (!$row) {
            return [];
        }

        return [['nik' => $row->nik, 'nama' => $row->name]];
    }

    public function suggestPeserta(Request $request)
    {
        $this->checkAccess();

        $position = trim((string) $request->get('position', ''));

        return response()->json(['data' => $this->suggestPesertaByPosition($position)]);
    }

    private function suggestPesertaByPosition(string $position): array
    {
        $position = trim($position);
        if ($position === '') {
            return [];
        }

        return collect($this->teamMembers())
            ->filter(fn ($m) => trim((string) ($m['jabatan'] ?? '')) === $position)
            ->take(20)
            ->map(fn ($m) => ['nik' => $m['nik'], 'nama' => $m['nama'], 'level' => (int) $m['level']])
            ->values()
            ->all();
    }

    public function bawahan(Request $request)
    {
        $this->checkAccess();

        $periode = PmsEmployeeSnapshot::normalizePeriode((string) $request->get('periode', ''));
        $me = $this->currentNik();
        if ($periode === '' || $me === '') {
            return response()->json(['data' => [], 'periode' => $periode]);
        }

        $rows = HrPmsEmployeeSnapshot::where('periode', $periode)
            ->where(function ($q) use ($me) {
                $q->where('manager_nik', $me)
                    ->orWhere('associate_manager_nik', $me);
            })
            ->orderByRaw('CAST(level AS UNSIGNED) ASC, nik ASC')
            ->get(['nik', 'nama', 'departemen', 'sub_departemen', 'section', 'level']);

        return response()->json(['data' => $rows, 'periode' => $periode]);
    }

    private ?array $teamCache = null;

    private ?array $eligibleNiksCache = null;

    private function currentPeriode(): string
    {
        return PmsEmployeeSnapshot::normalizePeriode((string) (HrPk::singleYearPeriodes()[0] ?? ''));
    }

    /**
     * Map NIK => level_group dari snapshot periode PK berjalan.
     */
    private function snapshotLevelGroupByNik(array $niks): array
    {
        $niks = collect($niks)
            ->map(fn ($n) => trim((string) $n))
            ->filter()
            ->unique()
            ->values()
            ->all();
        if (empty($niks)) {
            return [];
        }

        $periode = $this->currentPeriode();
        if ($periode === '') {
            return [];
        }

        return HrPmsEmployeeSnapshot::where('periode', $periode)
            ->whereIn('nik', $niks)
            ->pluck('level_group', 'nik')
            ->all();
    }

    /**
     * NIK peserta level_group 4-5 & 6-7 pada snapshot periode PK berjalan.
     */
    private function snapshotEligibleNiks(): array
    {
        if ($this->eligibleNiksCache === null) {
            $periode = $this->currentPeriode();
            $this->eligibleNiksCache = $periode === ''
                ? []
                : HrPmsEmployeeSnapshot::where('periode', $periode)
                    ->where(function ($q) {
                        $q->whereIn('level_group', ['4-5', '6-7'])
                            ->orWhereRaw("COALESCE(level_group, '') = '' AND CAST(level AS UNSIGNED) BETWEEN 4 AND 7");
                    })
                    ->pluck('nik')
                    ->map(fn ($v) => trim((string) $v))
                    ->filter()
                    ->unique()
                    ->values()
                    ->all();
        }

        return $this->eligibleNiksCache;
    }

    private function teamMembers(): array
    {
        if ($this->teamCache === null) {
            $team = (new PkAtasan())->teamOf($this->currentNik());
            $groupByNik = $this->snapshotLevelGroupByNik(array_column($team, 'nik'));

            $this->teamCache = collect($team)
                ->filter(function ($m) use ($groupByNik) {
                    $group = trim((string) ($groupByNik[$m['nik']] ?? ''));
                    if ($group === '') {
                        $group = (string) ($m['group'] ?? '');
                    }

                    return in_array($group, ['4-5', '6-7'], true);
                })
                ->values()
                ->all();
        }

        return $this->teamCache;
    }

    public function otorisasiList($ippId)
    {
        $this->checkAccess();

        HrIpp::findOrFail($ippId);

        $rows = HrIppOtorisasi::where('ipp_id', $ippId)
            ->orderBy('id', 'asc')
            ->get()
            ->map(function ($o) {
                return [
                    'id' => $o->id,
                    'nik_yang_bersangkutan' => $o->nik_yang_bersangkutan,
                    'nama_yang_bersangkutan' => $o->nama_yang_bersangkutan,
                    'nik_manager' => $o->nik_manager,
                    'nama_manager' => $o->nama_manager,
                    'nik_associate_manager' => $o->nik_associate_manager,
                    'nama_associate_manager' => $o->nama_associate_manager,
                    'created_at' => optional($o->created_at)->format('d M Y H:i'),
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $rows,
        ]);
    }

    private function atasanForRole(int $userLevel): array
    {
        $nik = $this->currentNik();
        $name = Auth::user()->name ?? null;

        if (($userLevel >= 6 && $userLevel <= 7) || PkAmMapping::isAssociateManager($nik)) {
            return ['associate_manager', $nik, $name];
        }

        return ['manager', $nik, $name];
    }

    public function otorisasiStore($ippId, Request $request)
    {
        $this->checkAccess();

        $ipp = HrIpp::findOrFail($ippId);

        $userLevel = $this->userLevel();
        $nikAtasan = $this->currentNik();
        if ($nikAtasan === '') {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki NIK di tabel users.',
            ], 422);
        }

        $validated = $request->validate([
            'nik_yang_bersangkutan' => 'required|string|max:50',
            'nik_manager' => 'nullable|string|max:50',
            'nik_associate_manager' => 'nullable|string|max:50',
        ]);

        $nikInput = trim((string) $validated['nik_yang_bersangkutan']);
        $this->assertNikInTeamSnapshot($ipp->periode, $nikInput);
        $ybs = $this->resolveSubordinateByNik($nikInput, $userLevel);
        if (!$ybs) {
            return response()->json([
                'success' => false,
                'message' => "NIK peserta '{$nikInput}' tidak ditemukan atau bukan bawahan Anda / peserta Mapping SPV Anda (level harus di atas 3 dan di bawah level Anda).",
            ], 422);
        }

        $username = Auth::user()->username ?? (Auth::user()->name ?? 'system');

        [$role, $myNik, $myName] = $this->atasanForRole($userLevel);
        $otherRole = $role === 'manager' ? 'associate_manager' : 'manager';
        $manualNik = trim((string) ($validated['nik_' . $otherRole] ?? ''));
        if ($otherRole === 'associate_manager' && in_array($ybs['level'], [6, 7], true)) {
            $manualNik = '';
        }
        $manualUser = null;
        if ($manualNik !== '') {
            $manualUser = DB::table('users')->where('nik', $manualNik)->first(['nik', 'name']);
            if (!$manualUser) {
                return response()->json([
                    'success' => false,
                    'message' => "NIK " . ($otherRole === 'manager' ? 'manager' : 'associate manager') . " '{$manualNik}' tidak ditemukan di tabel users.",
                ], 422);
            }
        }

        $otorisasi = HrIppOtorisasi::create([
            'ipp_id' => $ippId,
            'nik_yang_bersangkutan' => $ybs['nik'],
            'nama_yang_bersangkutan' => $ybs['name'],
            'nik_' . $role => $myNik,
            'nama_' . $role => $myName,
            'nik_' . $otherRole => $manualNik !== '' ? $manualNik : null,
            'nama_' . $otherRole => $manualUser ? $manualUser->name : null,
            'send_by_username' => $username,
        ]);

        $this->snapshotIppParticipant($ipp, $otorisasi);

        if (HrPmsControlNotifikasi::isActive()) {
            SendIppUserNotificationJob::dispatch($ipp->periode, [
                ['nik' => $ybs['nik'], 'nama' => $ybs['name']],
            ], Auth::user()->name);
        }

        return response()->json([
            'success' => true,
            'message' => 'Peserta berhasil ditambahkan.',
            'id' => $otorisasi->id,
        ]);
    }

    public function otorisasiUpdate($id, Request $request)
    {
        $this->checkAccess();

        $otorisasi = HrIppOtorisasi::findOrFail($id);

        $userLevel = $this->userLevel();
        $nikAtasan = $this->currentNik();
        if ($nikAtasan === '') {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki NIK di tabel users.',
            ], 422);
        }

        $validated = $request->validate([
            'nik_yang_bersangkutan' => 'required|string|max:50',
            'nik_manager' => 'nullable|string|max:50',
            'nik_associate_manager' => 'nullable|string|max:50',
        ]);

        $nikInput = trim((string) $validated['nik_yang_bersangkutan']);
        if ($nikInput !== trim((string) $otorisasi->nik_yang_bersangkutan)) {
            $ippForGuard = HrIpp::find($otorisasi->ipp_id);
            if ($ippForGuard) {
                $this->assertNikInTeamSnapshot($ippForGuard->periode, $nikInput);
            }
        }
        if ($nikInput === trim((string) $otorisasi->nik_yang_bersangkutan)) {
            $ybs = [
                'nik' => $nikInput,
                'name' => trim((string) $otorisasi->nama_yang_bersangkutan),
                'level' => (int) DB::table('hr_master_employee')->where('NIK', $nikInput)->value('Level'),
            ];
        } else {
            $ybs = $this->resolveSubordinateByNik($nikInput, $userLevel);
        }
        if (!$ybs) {
            return response()->json([
                'success' => false,
                'message' => "NIK peserta '{$nikInput}' tidak ditemukan atau bukan bawahan Anda / peserta Mapping SPV Anda (level harus di atas 3 dan di bawah level Anda).",
            ], 422);
        }

        [$role, $myNik, $myName] = $this->atasanForRole($userLevel);
        $otherRole = $role === 'manager' ? 'associate_manager' : 'manager';
        $manualNik = trim((string) ($validated['nik_' . $otherRole] ?? ''));
        if ($otherRole === 'associate_manager' && in_array($ybs['level'], [6, 7], true)) {
            $manualNik = '';
        }
        $manualUser = null;
        if ($manualNik !== '') {
            $manualUser = DB::table('users')->where('nik', $manualNik)->first(['nik', 'name']);
            if (!$manualUser) {
                return response()->json([
                    'success' => false,
                    'message' => "NIK " . ($otherRole === 'manager' ? 'manager' : 'associate manager') . " '{$manualNik}' tidak ditemukan di tabel users.",
                ], 422);
            }
        }

        $otorisasi->update([
            'nik_yang_bersangkutan' => $ybs['nik'],
            'nama_yang_bersangkutan' => $ybs['name'],
            'nik_' . $role => $myNik,
            'nama_' . $role => $myName,
            'nik_' . $otherRole => $manualNik !== '' ? $manualNik : null,
            'nama_' . $otherRole => $manualUser ? $manualUser->name : null,
        ]);

        $ippTarget = HrIpp::find($otorisasi->ipp_id);
        if ($ippTarget) {
            $this->snapshotIppParticipant($ippTarget, $otorisasi);
        }

        return response()->json([
            'success' => true,
            'message' => 'Peserta berhasil diperbarui.',
        ]);
    }

    public function otorisasiDelete($id)
    {
        $this->checkAccess();

        $otorisasi = HrIppOtorisasi::findOrFail($id);
        $otorisasi->delete();

        return response()->json([
            'success' => true,
            'message' => 'Peserta berhasil dihapus.',
        ]);
    }

    /**
     * Simpan snapshot atribut peserta IPP (freeze saat assign).
     */
    private function snapshotIppParticipant(HrIpp $ipp, HrIppOtorisasi $oto): void
    {
        $nik = trim((string) $oto->nik_yang_bersangkutan);
        if ($nik === '') {
            return;
        }

        $emp = DB::table('hr_master_employee')->where('NIK', $nik)->first([
            'NIK', 'Nama', 'Jabatan', 'Departmen', 'Sub Departmen', 'Section', 'Level',
        ]);

        $atasanOpts = $emp
            ? PmsEmployeeSnapshot::atasanOptsFor($emp, null, (string) $ipp->periode)
            : [];

        PmsEmployeeSnapshot::capture((string) $ipp->periode, $nik, array_merge($atasanOpts, [
            'nama' => $oto->nama_yang_bersangkutan,
            'source' => PmsEmployeeSnapshot::SOURCE_IPP,
        ]));
    }

    private function saveDetails(int $ippId, array $details): void
    {
        $existingNos = HrIppDetail::where('ipp_id', $ippId)->pluck('no_urut')->all();
        $incomingNos = array_column($details, 'no_urut');
        $toDelete = array_diff($existingNos, $incomingNos);
        if (!empty($toDelete)) {
            HrIppDetail::where('ipp_id', $ippId)->whereIn('no_urut', $toDelete)->delete();
        }

        foreach ($details as $detail) {
            $model = HrIppDetail::updateOrCreate(
                ['ipp_id' => $ippId, 'no_urut' => $detail['no_urut']],
                [
                    'objective' => $detail['objective'] ?? null,
                    'kpa' => $detail['kpa'] ?? null,
                    'kpi' => $detail['kpi'],
                    'classification' => $detail['classification'] ?? null,
                    'uom' => $detail['uom'] ?? null,
                    'weight' => ($detail['weight'] === null || $detail['weight'] === '') ? null : (float) $detail['weight'],
                    'target' => ($detail['target'] === null || $detail['target'] === '') ? null : (string) $detail['target'],
                    'due_date' => $detail['due_date'] ?? null,
                ]
            );

            $this->syncRangeTiers($model, $detail['range_tiers'] ?? null);
        }
    }

    private function syncRangeTiers(HrIppDetail $detail, ?array $tiers): void
    {
        if (($detail->classification ?? '') !== 'Range' || empty($tiers)) {
            HrIppMasterTarget::where('ipp_detail_id', $detail->id)->delete();
            return;
        }

        $payload = [];
        foreach ($tiers as $t) {
            $score = (int) ($t['score'] ?? 0);
            if ($score < 1 || $score > 5) {
                continue;
            }
            $matchType = in_array($t['match_type'] ?? null, [
                IppRangeScale::MATCH_EQUALS,
                IppRangeScale::MATCH_GTE,
                IppRangeScale::MATCH_LTE,
                IppRangeScale::MATCH_EQ,
                IppRangeScale::MATCH_BETWEEN,
            ], true)
                ? $t['match_type']
                : IppRangeScale::MATCH_RANGE;
            $payload[$score] = [
                'score' => $score,
                'match_type' => $matchType,
                'min_value' => ($t['min_value'] === null || $t['min_value'] === '') ? null : (float) $t['min_value'],
                'max_value' => ($t['max_value'] === null || $t['max_value'] === '') ? null : (float) $t['max_value'],
                'label' => $t['label'] ?? null,
            ];
        }

        if (!empty($payload)) {
            HrIppMasterTarget::where('ipp_detail_id', $detail->id)
                ->whereNotIn('score', array_keys($payload))
                ->delete();
            foreach ($payload as $row) {
                HrIppMasterTarget::updateOrCreate(
                    ['ipp_detail_id' => $detail->id, 'score' => $row['score']],
                    [
                        'match_type' => $row['match_type'],
                        'min_value' => $row['min_value'],
                        'max_value' => $row['max_value'],
                        'label' => $row['label'],
                    ]
                );
            }
        }
    }
}
