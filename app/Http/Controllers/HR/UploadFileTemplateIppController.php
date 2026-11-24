<?php

namespace App\Http\Controllers\HR;

use App\Http\Controllers\Controller;
use App\Imports\HR\TemplateIppImport;
use App\Jobs\HR\SendIppUserNotificationJob;
use App\Models\HR\HrIpp;
use App\Models\HR\HrIppAktual;
use App\Models\HR\HrIppDetail;
use App\Models\HR\HrIppForeman;
use App\Models\HR\HrIppForemanNilai;
use App\Models\HR\HrIppManagerMapping;
use App\Models\HR\HrIppMasterTarget;
use App\Models\HR\HrIppOtorisasi;
use App\Models\HR\HrPk;
use App\Models\HR\HrPmsControlNotifikasi;
use App\Models\HR\HrPmsEmployeeSnapshot;
use App\Support\IppOtorisasiResolver;
use App\Support\IppRangeScale;
use App\Support\PmsEmployeeSnapshot;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\IOFactory;

class UploadFileTemplateIppController extends Controller
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

    public function index()
    {
        $this->checkPermission('hr_upload_file_template_ipp');
        return view('hr.upload-file-template-ipp.upload-file-template-ipp', [
            'deptOptions' => $this->deptOptions(),
            'subDeptOptions' => $this->subDeptOptions(),
            'positionOptions' => $this->positionOptions(),
            'golonganOptions' => $this->golonganOptions(),
            'periodeOptions' => HrPk::singleYearPeriodes(),
        ]);
    }

    private function subDeptOptions(): array
    {
        return DB::table('hr_master_employee')
            ->where('Aktif', 'Y')
            ->whereNotNull('Sub Departmen')
            ->where('Sub Departmen', '!=', '')
            ->distinct()
            ->pluck('Sub Departmen')
            ->sort()
            ->values()
            ->all();
    }

    private function deptOptions(): array
    {
        $depts = DB::table('hr_master_employee')->distinct()->pluck('Departmen')->filter()->values();
        $subs = DB::table('hr_master_employee')
            ->whereRaw('LOWER(TRIM(Departmen)) = ?', ['factory'])
            ->distinct()
            ->pluck('Sub Departmen')
            ->filter()->values();

        return $depts->merge($subs)->unique()->sort()->values()->all();
    }

    private function positionOptions(): array
    {
        return DB::table('hr_master_employee')
            ->where('Aktif', 'Y')
            ->whereRaw('CAST(Level AS UNSIGNED) > 3 AND CAST(Level AS UNSIGNED) < 8')
            ->distinct()
            ->pluck('Jabatan')
            ->filter()
            ->sort()
            ->values()
            ->all();
    }

    public function positionLevel(Request $request)
    {
        $this->checkPermission('hr_upload_file_template_ipp');

        $position = trim((string) $request->get('position', ''));
        if ($position === '') {
            return response()->json(['level' => null]);
        }

        $row = DB::table('hr_master_employee')
            ->where('Jabatan', $position)
            ->where('Aktif', 'Y')
            ->whereRaw('CAST(Level AS UNSIGNED) > 3 AND CAST(Level AS UNSIGNED) < 8')
            ->selectRaw('Level, COUNT(*) as cnt')
            ->groupBy('Level')
            ->orderByDesc('cnt')
            ->orderByRaw('CAST(Level AS UNSIGNED) ASC')
            ->first();

        return response()->json(['level' => $row ? trim((string) $row->Level) : null]);
    }

    public function snapshotOptions(Request $request)
    {
        $this->checkPermission('hr_upload_file_template_ipp');

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

        $set = PmsEmployeeSnapshot::optionSetFor($periode);

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
                : 'Peserta periode ' . $periode . ' belum di-capture. Silakan buat/upload template PK periode tersebut agar peserta tercapture, lalu ulangi.',
        ]);
    }

    private function golonganOptions(): array
    {
        return DB::table('hr_master_employee')
            ->whereRaw('CAST(Level AS UNSIGNED) > 3 AND CAST(Level AS UNSIGNED) < 8')
            ->distinct()
            ->pluck('Level')
            ->map(fn ($v) => (string) $v)
            ->sort()
            ->values()
            ->all();
    }

    public function downloadTemplate(Request $request)
    {
        $this->checkPermission('hr_upload_file_template_ipp');

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

        $spreadsheet = IOFactory::load($template);
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('B2', $period);

        $tmp = tempnam(sys_get_temp_dir(), 'ipp') . '.xlsx';
        IOFactory::createWriter($spreadsheet, 'Xlsx')->save($tmp);

        return response()->download($tmp, 'template-ipp.xlsx')->deleteFileAfterSend(true);
    }

    public function parse(Request $request)
    {
        $this->checkPermission('hr_upload_file_template_ipp');

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
                'details' => $parsed['details'],
            ],
        ]);
    }

    public function store(Request $request)
    {
        $this->checkPermission('hr_upload_file_template_ipp');

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

        $pisahkan = $request->boolean('pisahkan_template');

        $otoByNik = [];
        if (!empty($nikPesertaList)) {
            $periode = PmsEmployeeSnapshot::normalizePeriode((string) $data['periode']);
            if (!HrPmsEmployeeSnapshot::where('periode', $periode)->exists()) {
                throw ValidationException::withMessages([
                    'nik_peserta' => 'Peserta periode ' . $periode . ' belum di-capture. Silakan buat/upload template PK periode tersebut agar peserta tercapture.',
                ]);
            }

            $dummy = new HrIpp();
            $dummy->periode = $data['periode'];
            foreach ($nikPesertaList as $nik) {
                $resolved = $this->resolveOtorisasiSnapshot($dummy, ['nik_yang_bersangkutan' => $nik], $error);
                if ($error) {
                    throw ValidationException::withMessages(['nik_peserta' => $error]);
                }
                $otoByNik[$nik] = $resolved;
            }
        }

        $ipp = null;
        $createdIds = [];
        $registered = [];
        $notifPeserta = [];

        DB::transaction(function () use ($data, $sendBy, $nikPesertaList, $pisahkan, $otoByNik, &$ipp, &$createdIds, &$registered, &$notifPeserta) {
            $header = function () use ($data, $sendBy) {
                return HrIpp::create([
                    'periode' => $data['periode'],
                    'dept' => $data['dept'],
                    'position' => $data['position'],
                    'golongan' => $data['golongan'],
                    'send_by_username' => $sendBy,
                ]);
            };

            $registerTo = function (HrIpp $target, string $nik) use ($sendBy, $otoByNik, &$registered, &$notifPeserta) {
                $row = $otoByNik[$nik];
                HrIppOtorisasi::create([
                    'ipp_id' => $target->id,
                    'nik_yang_bersangkutan' => $row['nik'],
                    'nama_yang_bersangkutan' => $row['name'],
                    'nik_manager' => $row['nik_manager'],
                    'nama_manager' => $row['nama_manager'],
                    'nik_associate_manager' => $row['nik_associate_manager'],
                    'nama_associate_manager' => $row['nama_associate_manager'],
                    'send_by_username' => $sendBy,
                ]);
                $registered[] = $row['nik'] . ' - ' . $row['name'];
                $notifPeserta[$row['nik']] = ['nik' => $row['nik'], 'nama' => $row['name']];
            };

            if (!empty($nikPesertaList) && $pisahkan) {
                foreach ($nikPesertaList as $nik) {
                    $ippX = $header();
                    $this->saveDetails($ippX->id, $data['details']);
                    $registerTo($ippX, $nik);
                    $createdIds[] = $ippX->id;
                }
                $ipp = $createdIds ? HrIpp::find($createdIds[0]) : null;
            } else {
                $ipp = $header();
                $this->saveDetails($ipp->id, $data['details']);
                foreach ($nikPesertaList as $nik) {
                    $registerTo($ipp, $nik);
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
        $this->checkPermission('hr_upload_file_template_ipp');

        $perPage = (int) $request->get('per_page', 10);
        $page = (int) $request->get('page', 1);
        $search = trim((string) $request->get('search', ''));

        $query = HrIpp::query()->withCount('details');

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

        $rubricIppIds = $rows->filter(fn ($i) => $i->isRubric())->pluck('id');
        $rubrikCountMap = $rubricIppIds->isEmpty()
            ? collect()
            : HrIppForeman::whereIn('ipp_id', $rubricIppIds->all())
                ->selectRaw('ipp_id, COUNT(*) as total')
                ->groupBy('ipp_id')
                ->pluck('total', 'ipp_id');

        // Peserta rubrik dibaca dari snapshot level 2-3, bukan hr_ipp_otorisasi.
        $rubrikPesertaMap = [];
        $rubrikFilledMap = [];
        if ($rubricIppIds->isNotEmpty()) {
            $rubricPeriodes = $rows->filter(fn ($i) => $i->isRubric())->pluck('periode')->unique()->all();
            foreach (HrPmsEmployeeSnapshot::whereIn('periode', $rubricPeriodes)
                ->where('level_group', '2-3')
                ->selectRaw('periode, COUNT(*) as total')
                ->groupBy('periode')
                ->pluck('total', 'periode') as $periode => $total) {
                $rubrikPesertaMap[$periode] = (int) $total;
            }

            $foremanRows = HrIppForeman::whereIn('ipp_id', $rubricIppIds->all())->get(['id', 'ipp_id']);
            $foremanToIpp = $foremanRows->pluck('ipp_id', 'id');
            if ($foremanRows->isNotEmpty()) {
                $filledByForeman = HrIppForemanNilai::whereIn('hr_ipp_foreman_id', $foremanRows->pluck('id')->all())
                    ->whereNotNull('nilai')
                    ->selectRaw('hr_ipp_foreman_id, COUNT(*) as total')
                    ->groupBy('hr_ipp_foreman_id')
                    ->pluck('total', 'hr_ipp_foreman_id');
                foreach ($filledByForeman as $foremanId => $total) {
                    $ippId = $foremanToIpp[$foremanId] ?? null;
                    if ($ippId !== null) {
                        $rubrikFilledMap[$ippId] = ($rubrikFilledMap[$ippId] ?? 0) + (int) $total;
                    }
                }
            }
        }

        $rows = $rows->map(function ($ipp) use ($uploaderMap, $pesertaByIpp, $rubrikCountMap, $rubrikPesertaMap, $rubrikFilledMap) {
            $peserta = $pesertaByIpp[$ipp->id] ?? [];
            $uploader = $ipp->send_by_username ? ($uploaderMap[$ipp->send_by_username] ?? null) : null;
            $isRubric = $ipp->isRubric();
            $rubrikPeserta = $isRubric ? (int) ($rubrikPesertaMap[$ipp->periode] ?? 0) : 0;
            $rubrikRows = $isRubric ? (int) ($rubrikCountMap[$ipp->id] ?? 0) : 0;

            return [
                'id' => $ipp->id,
                'periode' => $ipp->periode,
                'dept' => $ipp->dept,
                'position' => $ipp->position,
                'golongan' => $ipp->golongan,
                'send_by_username' => $ipp->send_by_username,
                'is_rubric' => $isRubric,
                'uploaded_by' => $uploader
                    ? ($uploader['nik'] . ' - ' . $uploader['name'])
                    : ($ipp->send_by_username ?? '-'),
                'detail_count' => $isRubric ? $rubrikRows : (int) $ipp->details_count,
                'otorisasi_count' => count($peserta),
                'peserta' => $peserta,
                'rubrik_peserta' => $isRubric ? $rubrikPeserta : null,
                'rubrik_filled' => $isRubric ? (int) ($rubrikFilledMap[$ipp->id] ?? 0) : null,
                'rubrik_total' => $isRubric ? $rubrikRows * $rubrikPeserta * 2 : null,
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
        $this->checkPermission('hr_upload_file_template_ipp');

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
            ],
        ]);
    }

    public function update($id, Request $request)
    {
        $this->checkPermission('hr_upload_file_template_ipp');

        $ipp = HrIpp::findOrFail($id);

        $data = $this->validatePayload($request);

        DB::transaction(function () use ($ipp, $data) {
            $ipp->update([
                'periode' => $data['periode'],
                'dept' => $data['dept'],
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
        $this->checkPermission('hr_upload_file_template_ipp');

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
            'manual' => 'nullable|boolean',
            'nik_peserta' => 'nullable|array|max:50',
            'nik_peserta.*' => 'nullable|string|max:50',
            'pisahkan_template' => 'nullable|boolean',
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

        if ($request->boolean('manual')) {
            $this->validateManualSnapshot($validated);
        }

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

    /**
     * Validasi IPP manual: periode wajib sudah punya snapshot, dan dept/position/golongan
     * harus berasal dari opsi snapshot periode tersebut.
     */
    private function validateManualSnapshot(array $validated): void
    {
        $periode = PmsEmployeeSnapshot::normalizePeriode((string) $validated['periode']);
        $set = PmsEmployeeSnapshot::optionSetFor($periode);

        if ($set['total'] === 0) {
            throw ValidationException::withMessages([
                'periode' => 'Peserta periode ' . $periode . ' belum di-capture. Silakan buat/upload template PK periode tersebut agar peserta tercapture, lalu ulangi.',
            ]);
        }

        $dept = trim((string) ($validated['dept'] ?? ''));
        if ($dept !== '' && !in_array($dept, $set['dept'], true)) {
            throw ValidationException::withMessages([
                'dept' => 'Dept "' . $dept . '" tidak ada pada snapshot periode ' . $periode . '.',
            ]);
        }

        $position = trim((string) ($validated['position'] ?? ''));
        if ($position !== '') {
            $positions = array_column($set['positions'], 'v');
            if (!in_array($position, $positions, true)) {
                throw ValidationException::withMessages([
                    'position' => 'Position "' . $position . '" tidak ada pada snapshot periode ' . $periode . '.',
                ]);
            }
        }

        $golongan = trim((string) ($validated['golongan'] ?? ''));
        if ($golongan !== '' && !in_array($golongan, $set['levels'], true)) {
            throw ValidationException::withMessages([
                'golongan' => 'Golongan "' . $golongan . '" tidak ada pada snapshot periode ' . $periode . '.',
            ]);
        }
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
        $this->checkPermission('hr_upload_file_template_ipp');

        $ipp = HrIpp::with(['details.masterTargets'])->findOrFail($ippId);

        $detailIds = $ipp->details->pluck('id');

        $otorisasiRows = HrIppOtorisasi::where('ipp_id', $ippId)->orderBy('id')->get();

        $aktual = [];
        $aktualVarchar = [];
        $aktualAtasan = [];
        $aktualAtasanVarchar = [];
        $aktualAtasanBy = [];
        $aktualDepartemen = [];
        $aktualDepartemenVarchar = [];
        $aktualDepartemenBy = [];
        $achievement = [];
        $aktualRows = HrIppAktual::whereIn('ipp_otorisasi_id', $otorisasiRows->pluck('id'))->get();
        foreach ($aktualRows as $a) {
            $aktual[$a->ipp_otorisasi_id][$a->ipp_detail_id][$a->tahapan] = $a->aktual;
            $aktualVarchar[$a->ipp_otorisasi_id][$a->ipp_detail_id][$a->tahapan] = $a->aktual_varchar;
            $aktualAtasan[$a->ipp_otorisasi_id][$a->ipp_detail_id][$a->tahapan] = $a->aktual_atasan;
            $aktualAtasanVarchar[$a->ipp_otorisasi_id][$a->ipp_detail_id][$a->tahapan] = $a->aktual_atasan_varchar;
            $aktualAtasanBy[$a->ipp_otorisasi_id][$a->ipp_detail_id][$a->tahapan] = $a->aktual_atasan_by;
            $aktualDepartemen[$a->ipp_otorisasi_id][$a->ipp_detail_id][$a->tahapan] = $a->aktual_departemen;
            $aktualDepartemenVarchar[$a->ipp_otorisasi_id][$a->ipp_detail_id][$a->tahapan] = $a->aktual_departemen_varchar;
            $aktualDepartemenBy[$a->ipp_otorisasi_id][$a->ipp_detail_id][$a->tahapan] = $a->aktual_departemen_by;
            $achievement[$a->ipp_otorisasi_id][$a->ipp_detail_id][$a->tahapan] = $a->achievement_category;
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
                'aktual_departemen' => $aktualDepartemen,
                'aktual_departemen_varchar' => $aktualDepartemenVarchar,
                'aktual_departemen_by' => $aktualDepartemenBy,
                'achievement_category' => $achievement,
                'tahapan' => $this->buildTahapan($ipp->periode),
            ],
        ]);
    }

    // === Otorisasi (NIK yang bersangkutan + NIK atasan) ===

    private function resolveUserByNik(string $nik): ?array
    {
        return IppOtorisasiResolver::resolveUserByNik($nik);
    }

    public function searchNik(Request $request)
    {
        $this->checkPermission('hr_upload_file_template_ipp');

        $term = trim((string) $request->get('term', ''));
        $role = trim((string) $request->get('role', ''));
        $periode = PmsEmployeeSnapshot::normalizePeriode((string) $request->get('periode', ''));

        if ($periode !== '') {
            if (!HrPmsEmployeeSnapshot::where('periode', $periode)->exists()) {
                return response()->json([
                    'data' => [],
                    'has_snapshot' => false,
                    'message' => 'Peserta periode ' . $periode . ' belum di-capture. Silakan buat/upload template PK periode tersebut agar peserta tercapture.',
                ]);
            }

            $snapQuery = HrPmsEmployeeSnapshot::where('periode', $periode)
                ->where(function ($q) {
                    $q->whereIn('level_group', ['4-5', '6-7'])
                        ->orWhereRaw("COALESCE(level_group, '') = '' AND CAST(level AS UNSIGNED) BETWEEN 4 AND 7");
                });

            if ($role === 'associate') {
                $snapQuery->whereRaw('CAST(level AS UNSIGNED) BETWEEN 5 AND 7');
            } elseif ($role === 'manager') {
                $snapQuery->whereRaw('CAST(level AS UNSIGNED) BETWEEN 8 AND 13');
            }

            if ($term !== '') {
                $snapQuery->where(function ($q) use ($term) {
                    $q->where('nik', 'like', "%{$term}%")
                        ->orWhere('nama', 'like', "%{$term}%");
                });
            }

            $rows = $snapQuery
                ->orderBy('nik')
                ->limit($term !== '' ? 10 : 50)
                ->get(['nik', 'nama'])
                ->map(function ($r) {
                    return [
                        'nik' => $r->nik,
                        'name' => $r->nama,
                    ];
                });

            return response()->json(['data' => $rows, 'has_snapshot' => true]);
        }

        $query = DB::table('users')
            ->select('nik', 'name')
            ->distinct()
            ->whereNotNull('nik')
            ->where('nik', '!=', '')
            ->where('status', '1');

        if ($role === 'manager') {
            $query->whereIn('nik', DB::table('hr_master_employee')->whereRaw('CAST(Level AS UNSIGNED) BETWEEN 8 AND 13')->select('NIK'));
        } elseif ($role === 'associate') {
            $query->whereIn('nik', DB::table('hr_master_employee')->whereRaw('CAST(Level AS UNSIGNED) BETWEEN 5 AND 7')->select('NIK'));
        }

        if ($term !== '') {
            $query->where(function ($q) use ($term) {
                $q->where('nik', 'like', "%{$term}%")
                    ->orWhere('name', 'like', "%{$term}%");
            });
        }

        $rows = $query
            ->orderBy('nik')
            ->limit($term !== '' ? 10 : 50)
            ->get()
            ->map(function ($u) {
                return [
                    'nik' => $u->nik,
                    'name' => $u->name,
                ];
            });

        return response()->json(['data' => $rows]);
    }

    public function checkNik($nik)
    {
        $this->checkPermission('hr_upload_file_template_ipp');

        $user = $this->resolveUserByNik($nik);

        if (!$user) {
            return response()->json([
                'valid' => false,
                'message' => 'NIK tidak ditemukan di tabel users.',
            ]);
        }

        return response()->json([
            'valid' => true,
            'nik' => $user['nik'],
            'name' => $user['name'],
        ]);
    }

    public function otorisasiPeserta(Request $request)
    {
        $this->checkPermission('hr_upload_file_template_ipp');

        $periode = PmsEmployeeSnapshot::normalizePeriode((string) $request->get('periode', ''));
        $nik = trim((string) $request->get('nik', ''));

        if ($periode === '' || $nik === '') {
            return response()->json([
                'success' => false,
                'found' => false,
                'message' => 'Periode dan NIK wajib diisi.',
            ], 422);
        }

        $hasSnapshot = HrPmsEmployeeSnapshot::where('periode', $periode)->exists();
        $row = $hasSnapshot ? PmsEmployeeSnapshot::attributesFor($periode, $nik) : null;

        if (!$row) {
            return response()->json([
                'success' => true,
                'found' => false,
                'has_snapshot' => $hasSnapshot,
                'message' => $hasSnapshot
                    ? 'NIK ' . $nik . ' tidak terdaftar pada snapshot periode ' . $periode . '.'
                    : 'Peserta periode ' . $periode . ' belum di-capture. Silakan buat/upload template PK periode tersebut agar peserta tercapture.',
            ]);
        }

        $level = (int) $row->level;

        return response()->json([
            'success' => true,
            'found' => true,
            'has_snapshot' => true,
            'peserta' => [
                'nik' => $row->nik,
                'nama' => $row->nama,
                'jabatan' => $row->jabatan,
                'departemen' => $row->departemen,
                'sub_departemen' => $row->sub_departemen,
                'level' => $level,
                'golongan' => (string) $level,
                'manager_nik' => $row->manager_nik,
                'manager_nama' => $row->manager_nama,
                'associate_manager_nik' => $row->associate_manager_nik,
                'associate_manager_nama' => $row->associate_manager_nama,
            ],
        ]);
    }

    public function otorisasiList($ippId)
    {
        $this->checkPermission('hr_upload_file_template_ipp');

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

    public function mappingList()
    {
        $this->checkPermission('hr_upload_file_template_ipp');

        $rows = HrIppManagerMapping::orderBy('sub_departmen')
            ->get()
            ->map(fn ($m) => [
                'id' => $m->id,
                'sub_departmen' => $m->sub_departmen,
                'nik_manager' => $m->nik_manager,
                'nama_manager' => $m->nama_manager,
            ]);

        return response()->json(['data' => $rows]);
    }

    public function mappingStore(Request $request)
    {
        $this->checkPermission('hr_upload_file_template_ipp');

        $validated = $request->validate([
            'sub_departmen' => 'required|string|max:150',
            'nik_manager' => 'nullable|string|max:50',
        ]);

        $sub = trim((string) $validated['sub_departmen']);
        $nikManager = trim((string) ($validated['nik_manager'] ?? ''));

        $manager = null;
        if ($nikManager !== '') {
            $manager = $this->resolveUserByNik($nikManager);
            if (!$manager) {
                return response()->json([
                    'success' => false,
                    'message' => "NIK manager '{$nikManager}' tidak ditemukan di tabel users.",
                ], 422);
            }
        }

        $mapping = HrIppManagerMapping::firstOrNew(['sub_departmen' => $sub]);
        $mapping->nik_manager = $manager ? $manager['nik'] : null;
        $mapping->nama_manager = $manager ? $manager['name'] : null;
        $mapping->created_by = Auth::user()->username ?? (Auth::user()->name ?? 'system');
        $mapping->save();

        return response()->json([
            'success' => true,
            'message' => 'Mapping berhasil disimpan.',
            'id' => $mapping->id,
        ]);
    }

    public function mappingDelete($id)
    {
        $this->checkPermission('hr_upload_file_template_ipp');

        HrIppManagerMapping::findOrFail($id)->delete();

        return response()->json([
            'success' => true,
            'message' => 'Mapping berhasil dihapus.',
        ]);
    }

    /**
     * Resolve data otorisasi dari snapshot periode IPP.
     * Manager/Associate Manager diambil dari snapshot (fallback manual bila kosong di snapshot).
     *
     * @return array{nik: string, name: string, level: int, nik_manager: ?string, nama_manager: ?string, nik_associate_manager: ?string, nama_associate_manager: ?string}|null
     */
    private function resolveOtorisasiSnapshot(HrIpp $ipp, array $validated, ?string &$error = null): ?array
    {
        return IppOtorisasiResolver::resolve($ipp, $validated, $error);
    }

    public function otorisasiStore($ippId, Request $request)
    {
        $this->checkPermission('hr_upload_file_template_ipp');

        $ipp = HrIpp::findOrFail($ippId);

        $validated = $request->validate([
            'nik_yang_bersangkutan' => 'required|string|max:50',
            'nik_manager' => 'nullable|string|max:50',
            'nik_associate_manager' => 'nullable|string|max:50',
        ]);

        $data = $this->resolveOtorisasiSnapshot($ipp, $validated, $error);
        if ($error) {
            return response()->json([
                'success' => false,
                'message' => $error,
            ], 422);
        }

        $username = Auth::user()->username ?? (Auth::user()->name ?? 'system');

        $otorisasi = HrIppOtorisasi::create([
            'ipp_id' => $ippId,
            'nik_yang_bersangkutan' => $data['nik'],
            'nama_yang_bersangkutan' => $data['name'],
            'nik_manager' => $data['nik_manager'],
            'nama_manager' => $data['nama_manager'],
            'nik_associate_manager' => $data['nik_associate_manager'],
            'nama_associate_manager' => $data['nama_associate_manager'],
            'send_by_username' => $username,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Peserta berhasil ditambahkan.',
            'id' => $otorisasi->id,
        ]);
    }

    public function otorisasiUpdate($id, Request $request)
    {
        $this->checkPermission('hr_upload_file_template_ipp');

        $otorisasi = HrIppOtorisasi::findOrFail($id);
        $ipp = HrIpp::findOrFail($otorisasi->ipp_id);

        $validated = $request->validate([
            'nik_yang_bersangkutan' => 'required|string|max:50',
            'nik_manager' => 'nullable|string|max:50',
            'nik_associate_manager' => 'nullable|string|max:50',
        ]);

        $data = $this->resolveOtorisasiSnapshot($ipp, $validated, $error);
        if ($error) {
            return response()->json([
                'success' => false,
                'message' => $error,
            ], 422);
        }

        $otorisasi->update([
            'nik_yang_bersangkutan' => $data['nik'],
            'nama_yang_bersangkutan' => $data['name'],
            'nik_manager' => $data['nik_manager'],
            'nama_manager' => $data['nama_manager'],
            'nik_associate_manager' => $data['nik_associate_manager'],
            'nama_associate_manager' => $data['nama_associate_manager'],
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Peserta berhasil diperbarui.',
        ]);
    }

    public function otorisasiDelete($id)
    {
        $this->checkPermission('hr_upload_file_template_ipp');

        $otorisasi = HrIppOtorisasi::findOrFail($id);
        $otorisasi->delete();

        return response()->json([
            'success' => true,
            'message' => 'Otorisasi berhasil dihapus.',
        ]);
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
                    'weight' => (($detail['weight'] ?? null) === null || ($detail['weight'] ?? null) === '') ? null : (float) $detail['weight'],
                    'target' => (($detail['target'] ?? null) === null || ($detail['target'] ?? null) === '') ? null : (string) $detail['target'],
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
