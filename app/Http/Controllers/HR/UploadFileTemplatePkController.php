<?php

namespace App\Http\Controllers\HR;

use App\Http\Controllers\Controller;
use App\Imports\HR\TemplatePkImport;
use App\Jobs\HR\SendPkMasterNotificationJob;
use App\Support\PkAtasan;
use App\Support\PmsEmployeeSnapshot;
use App\Models\HR\HrPk;
use App\Models\HR\HrPkAssociateManagerMapping;
use App\Models\HR\HrPkOtorisasi;
use App\Models\HR\HrPmsControlNotifikasi;
use App\Models\HR\HrPmsEmployeeSnapshot;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class UploadFileTemplatePkController extends Controller
{
    private function checkPermission(string $codename): void
    {
        $userCodenames = Auth::user()->getAllPermissionCodenames();
        if (!in_array($codename, $userCodenames, true)) {
            abort(403);
        }
    }

    private function participantStats(): array
    {
        $active23 = DB::table('hr_master_employee')
            ->where('Aktif', 'Y')
            ->whereRaw('CAST(Level AS UNSIGNED) BETWEEN 2 AND 3')
            ->pluck('NIK')
            ->map(fn ($n) => strtolower(trim((string) $n)))
            ->filter()
            ->flip();

        $active45 = DB::table('hr_master_employee')
            ->where('Aktif', 'Y')
            ->whereRaw('CAST(Level AS UNSIGNED) BETWEEN 4 AND 5')
            ->pluck('NIK')
            ->map(fn ($n) => strtolower(trim((string) $n)))
            ->filter()
            ->flip();

        $active67 = DB::table('hr_master_employee')
            ->where('Aktif', 'Y')
            ->whereRaw('CAST(Level AS UNSIGNED) BETWEEN 6 AND 7')
            ->pluck('NIK')
            ->map(fn ($n) => strtolower(trim((string) $n)))
            ->filter()
            ->flip();

        // Peserta Mapping SPV (hr_pk_otorisasi) yang belum terhitung di master aktif masuk badge 4-5.
        $extra = 0;
        foreach (HrPkOtorisasi::query()->distinct()->pluck('nik_yang_bersangkutan') as $nik) {
            $key = strtolower(trim((string) $nik));
            if ($key !== '' && !isset($active23[$key]) && !isset($active45[$key]) && !isset($active67[$key])) {
                $extra++;
            }
        }

        $lvl23 = $active23->count();
        $lvl45 = $active45->count() + $extra;
        $lvl67 = $active67->count();

        return ['lvl_2_3' => $lvl23, 'lvl_4_5' => $lvl45, 'lvl_6_7' => $lvl67, 'total' => $lvl23 + $lvl45 + $lvl67];
    }

    public function index()
    {
        $this->checkPermission('hr_upload_file_template_ipp');

        return view('hr.upload-file-template-pk.upload-file-template-pk', [
            'participantStats' => $this->participantStats(),
            'subDeptOptions' => DB::table('hr_master_employee')
                ->where('Aktif', 'Y')
                ->whereRaw('CAST(Level AS UNSIGNED) BETWEEN 2 AND 7')
                ->whereNotNull('Sub Departmen')
                ->where('Sub Departmen', '!=', '')
                ->distinct()
                ->pluck('Sub Departmen')
                ->sort()
                ->values()
                ->all(),
        ]);
    }

    public function downloadTemplate()
    {
        $this->checkPermission('hr_upload_file_template_ipp');

        $template = public_path('templates/hr/template-pk.xlsx');
        if (!file_exists($template)) {
            abort(404, 'File template tidak ditemukan.');
        }

        return response()->download($template, 'template-pk.xlsx');
    }

    public function parse(Request $request)
    {
        $this->checkPermission('hr_upload_file_template_ipp');

        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls',
            'periode' => 'nullable|string|max:10',
        ]);

        $file = $request->file('file');
        $originalName = $file->getClientOriginalName();

        $dir = 'uploads/hr-pk-template';
        $filename = time() . '_' . preg_replace('/\s+/', '_', $originalName);
        $path = $file->storeAs($dir, $filename, 'public');
        $fullPath = storage_path('app/public/' . $path);

        try {
            $import = new TemplatePkImport();
            $import->parse($fullPath);
            $parsed = $import->parsed;
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal membaca file: ' . $e->getMessage(),
            ], 422);
        } finally {
            Storage::disk('public')->delete($path);
        }

        if (empty($parsed['details'])) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak ada data aspek Penilaian Karya yang ditemukan. Pastikan format sesuai Template PK.',
            ], 422);
        }

        $periode = !empty($parsed['periode']) ? $parsed['periode'] : trim((string) $request->input('periode', ''));

        return response()->json([
            'success' => true,
            'data' => [
                'periode' => $periode !== '' ? $periode : null,
                'file_name' => $originalName,
                'details' => $parsed['details'],
            ],
            'participants' => $this->participantStats(),
        ]);
    }

    public function store(Request $request)
    {
        $this->checkPermission('hr_upload_file_template_ipp');

        $data = $this->validatePayload($request);
        $periode = trim((string) ($data['periode'] ?? ''));
        if ($periode === '') {
            return response()->json([
                'success' => false,
                'message' => 'Periode (tahun) wajib diisi.',
            ], 422);
        }

        $nik = trim((string) (Auth::user()->nik ?? ''));
        $username = Auth::user()->username ?? (Auth::user()->name ?? 'system');
        $sendBy = $nik !== '' ? $nik : $username;

        $pk = DB::transaction(function () use ($data, $sendBy, $periode) {
            // Hapus hanya master dengan periode yang sama jika sudah ada (periode lain tetap tersimpan)
            HrPk::where('periode', $periode)->delete();

            $pk = HrPk::create([
                'periode' => $periode,
                'file_name' => $data['file_name'],
                'send_by_username' => $sendBy,
            ]);

            foreach ($data['details'] as $d) {
                $pk->details()->create([
                    'section' => $d['section'],
                    'category' => $d['category'] ?? null,
                    'level_group' => $d['level_group'] ?? null,
                    'no_urut' => $d['no_urut'],
                    'area' => $d['area'] ?? null,
                    'competency_behavior' => $d['competency_behavior'] ?? null,
                    'key_behavior' => $d['key_behavior'] ?? null,
                    'kriteria_1' => $d['kriteria_1'] ?? null,
                    'kriteria_2' => $d['kriteria_2'] ?? null,
                    'kriteria_3' => $d['kriteria_3'] ?? null,
                    'kriteria_4' => $d['kriteria_4'] ?? null,
                    'kriteria_5' => $d['kriteria_5'] ?? null,
                ]);
            }

            return $pk;
        });

        if ($pk) {
            $participants = $this->resolvePkParticipants();

            $otoSources = HrPkOtorisasi::query()
                ->get(['nik_yang_bersangkutan', 'kategori'])
                ->mapWithKeys(function ($o) {
                    $key = strtolower(trim((string) $o->nik_yang_bersangkutan));

                    return $key === '' ? [] : [$key => trim((string) $o->kategori) === 'foreman'
                        ? PmsEmployeeSnapshot::SOURCE_PK_FOREMAN
                        : PmsEmployeeSnapshot::SOURCE_PK_OTORISASI];
                });

            $resolver = new PkAtasan();
            $masterMap = DB::table('hr_master_employee')
                ->whereIn('NIK', $participants->pluck('nik')->all())
                ->get(['NIK', 'Nama', 'Jabatan', 'Departmen', 'Sub Departmen', 'Section', 'Level'])
                ->keyBy(fn ($e) => trim((string) $e->NIK));

            $niks = [];
            $optsByNik = [];
            foreach ($participants as $row) {
                $nik = trim((string) ($row['nik'] ?? ''));
                if ($nik === '') {
                    continue;
                }
                $niks[] = $nik;

                $manager = $row['manager'] ?? null;
                $am = $row['associate_manager'] ?? null;

                $opts = [
                    'nama' => $row['nama'] ?? null,
                    'jabatan' => $row['jabatan'] ?? null,
                    'departemen' => $row['departemen'] ?? null,
                    'sub_departemen' => $row['sub_departemen'] ?? null,
                    'section' => $row['section'] ?? null,
                    'level' => $row['level'] ?? null,
                    'level_group' => $row['group'] ?? null,
                    'manager_nik' => trim((string) ($manager['nik'] ?? '')) !== '' ? $manager['nik'] : null,
                    'manager_nama' => $manager['nama'] ?? null,
                    'associate_manager_nik' => trim((string) ($am['nik'] ?? '')) !== '' ? $am['nik'] : null,
                    'associate_manager_nama' => $am['nama'] ?? null,
                ];

                if (!$manager && !$am) {
                    $emp = $masterMap->get($nik);
                    if ($emp) {
                        $opts = array_merge($opts, PmsEmployeeSnapshot::atasanOptsFor($emp, $resolver, $periode));
                    }
                }

                if (isset($otoSources[strtolower($nik)])) {
                    $opts['source'] = $otoSources[strtolower($nik)];
                }

                $optsByNik[$nik] = $opts;
            }

            PmsEmployeeSnapshot::captureForPeriod($periode, $niks, [
                'source' => PmsEmployeeSnapshot::SOURCE_PK,
            ], $optsByNik);
        }

        return response()->json([
            'success' => true,
            'message' => "Master Penilaian Karya periode {$periode} berhasil disimpan.",
            'id' => $pk ? $pk->id : null,
        ]);
    }

    public function sendNotification($id)
    {
        $this->checkPermission('hr_upload_file_template_ipp');

        $pk = HrPk::find($id);
        if (!$pk) {
            return response()->json([
                'success' => false,
                'message' => 'Master Penilaian Karya tidak ditemukan.',
            ], 404);
        }

        if (!HrPmsControlNotifikasi::isActive()) {
            return response()->json([
                'success' => false,
                'message' => 'Notifikasi email PMS sedang dinonaktifkan. Aktifkan kembali di /hr/pms-total-scoring.',
            ], 422);
        }

        SendPkMasterNotificationJob::dispatch($pk->periode);

        return response()->json([
            'success' => true,
            'message' => "Email notifikasi Penilaian Karya periode {$pk->periode} sedang dikirim ke peserta level 4-7.",
        ]);
    }

    public function list(Request $request)
    {
        $this->checkPermission('hr_upload_file_template_ipp');

        $perPage = (int) $request->get('per_page', 10);
        $perPage = max(1, min(100, $perPage));
        $page = max(1, (int) $request->get('page', 1));
        $search = trim((string) $request->get('search', ''));

        $q = HrPk::query()->withCount('details');

        if ($search !== '') {
            $q->where(function ($qq) use ($search) {
                $qq->where('periode', 'like', "%{$search}%")
                    ->orWhere('file_name', 'like', "%{$search}%")
                    ->orWhere('send_by_username', 'like', "%{$search}%")
                    ->orWhereHas('details', function ($qd) use ($search) {
                        $qd->where('competency_behavior', 'like', "%{$search}%")
                            ->orWhere('area', 'like', "%{$search}%")
                            ->orWhere('no_urut', 'like', "%{$search}%");
                    });
            });
        }

        $q->orderByDesc('created_at');

        $total = (clone $q)->count();
        $lastPage = max(1, (int) ceil($total / $perPage));
        $page = min($page, $lastPage);

        $rows = $q->forPage($page, $perPage)->get()->map(function ($pk) {
            return [
                'id' => $pk->id,
                'periode' => $pk->periode,
                'file_name' => $pk->file_name,
                'send_by_username' => $pk->send_by_username,
                'details_count' => $pk->details_count,
                'created_at' => $pk->created_at ? $pk->created_at->format('d M Y H:i') : '',
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $rows,
            'meta' => [
                'page' => $page,
                'per_page' => $perPage,
                'total' => $total,
                'last_page' => $lastPage,
            ],
            'participants' => $this->participantStats(),
        ]);
    }

    /**
     * Daftar peserta PK: master aktif Level 2-7 + override Mapping SPV (hr_pk_otorisasi).
     *
     * @return \Illuminate\Support\Collection<int, array>
     */
    private function resolvePkParticipants(): \Illuminate\Support\Collection
    {
        $pkAtasan = new PkAtasan();

        $rows = DB::table('hr_master_employee')
            ->where('Aktif', 'Y')
            ->whereRaw('CAST(Level AS UNSIGNED) BETWEEN 2 AND 7')
            ->orderByRaw('CAST(Level AS UNSIGNED) ASC, NIK ASC')
            ->get(['NIK', 'Nama', 'Jabatan', 'Departmen', 'Sub Departmen', 'Section', 'Level']);

        $merged = [];
        foreach ($rows as $r) {
            $level = (int) $r->Level;
            $atasan = $pkAtasan->resolveAll($r);
            $merged[strtolower(trim((string) $r->NIK))] = [
                'nik' => $r->NIK,
                'nama' => $r->Nama,
                'jabatan' => $r->Jabatan,
                'departemen' => trim((string) $r->Departmen),
                'sub_departemen' => trim((string) ($r->{'Sub Departmen'} ?? '')),
                'section' => trim((string) ($r->Section ?? '')),
                'level' => $level,
                'group' => PmsEmployeeSnapshot::groupOf($level),
                'manager' => $atasan['manager'],
                'associate_manager' => $atasan['associate_manager'],
            ];
        }

        $otorisasiRows = HrPkOtorisasi::orderBy('id')->get();
        if ($otorisasiRows->isNotEmpty()) {
            $niks = $otorisasiRows->pluck('nik_yang_bersangkutan')
                ->map(fn ($n) => trim((string) $n))
                ->filter()
                ->unique()
                ->values()
                ->all();

            $emps = DB::table('hr_master_employee')
                ->whereIn('NIK', $niks)
                ->get(['NIK', 'Nama', 'Jabatan', 'Departmen', 'Sub Departmen', 'Section', 'Level'])
                ->keyBy(fn ($e) => strtolower(trim((string) $e->NIK)));

            $users = DB::table('users')
                ->whereIn('nik', $niks)
                ->get(['nik', 'name'])
                ->keyBy(fn ($u) => strtolower(trim((string) $u->nik)));

            foreach ($otorisasiRows as $o) {
                $nik = trim((string) $o->nik_yang_bersangkutan);
                $key = strtolower($nik);
                if ($key === '') {
                    continue;
                }
                $emp = $emps->get($key);
                $level = $emp ? (int) $emp->Level : 0;
                $user = $users->get($key);
                $kategori = trim((string) ($o->kategori ?? 'spv'));
                $group = $kategori === 'foreman'
                    ? (PmsEmployeeSnapshot::groupOf($level) ?? '2-3')
                    : ($level >= 6 ? '6-7' : '4-5');
                $row = $merged[$key] ?? [
                    'nik' => $nik,
                    'nama' => $emp ? $emp->Nama : ($o->nama_yang_bersangkutan ?? ($user->name ?? '')),
                    'jabatan' => $emp ? $emp->Jabatan : '-',
                    'departemen' => $emp ? trim((string) $emp->Departmen) : '',
                    'sub_departemen' => $emp ? trim((string) ($emp->{'Sub Departmen'} ?? '')) : '',
                    'section' => $emp ? trim((string) ($emp->Section ?? '')) : '',
                    'level' => $level,
                    'group' => $group,
                    'manager' => null,
                    'associate_manager' => null,
                ];
                $row['group'] = $group;
                $srcOtorisasi = $kategori === 'foreman' ? 'foreman' : 'otorisasi';
                $row['manager'] = trim((string) $o->nik_manager) !== ''
                    ? ['nik' => $o->nik_manager, 'nama' => $o->nama_manager, 'source' => $srcOtorisasi]
                    : null;
                $row['associate_manager'] = trim((string) $o->nik_associate_manager) !== ''
                    ? ['nik' => $o->nik_associate_manager, 'nama' => $o->nama_associate_manager, 'source' => $srcOtorisasi]
                    : null;
                $merged[$key] = $row;
            }
        }

        return collect($merged)->values();
    }

    public function participants(Request $request)
    {
        $this->checkPermission('hr_upload_file_template_ipp');

        $perPage = (int) $request->get('per_page', 10);
        if ($perPage < 1) {
            $perPage = 10;
        }
        if ($perPage > 100) {
            $perPage = 100;
        }
        $page = max(1, (int) $request->get('page', 1));
        $search = trim((string) $request->get('search', ''));

        $data = $this->resolvePkParticipants();

        if ($search !== '') {
            $needle = mb_strtolower($search);
            $data = $data->filter(function ($x) use ($needle) {
                foreach (['nik', 'nama', 'jabatan', 'departemen', 'sub_departemen', 'section'] as $f) {
                    if (mb_stripos((string) ($x[$f] ?? ''), $needle) !== false) {
                        return true;
                    }
                }

                return false;
            })->values();
        }

        $data = $data->sort(function ($a, $b) {
            $cmp = $a['level'] <=> $b['level'];

            return $cmp !== 0 ? $cmp : strcmp((string) $a['nik'], (string) $b['nik']);
        })->values();

        $total = $data->count();
        $lastPage = max(1, (int) ceil($total / $perPage));
        if ($page > $lastPage) {
            $page = $lastPage;
        }
        $data = $data->forPage($page, $perPage)->values();

        return response()->json([
            'success' => true,
            'data' => $data,
            'meta' => [
                'page' => $page,
                'per_page' => $perPage,
                'total' => $total,
                'last_page' => $lastPage,
            ],
        ]);
    }

    /**
     * Daftar Peserta (history) dari hr_pms_employee_snapshot per periode.
     */
    public function participantsHistory(Request $request)
    {
        $this->checkPermission('hr_upload_file_template_ipp');

        $perPage = (int) $request->get('per_page', 10);
        if ($perPage < 1) {
            $perPage = 10;
        }
        if ($perPage > 100) {
            $perPage = 100;
        }
        $page = max(1, (int) $request->get('page', 1));
        $search = trim((string) $request->get('search', ''));

        $periods = $this->historyPeriodes();

        $periode = trim((string) $request->get('periode', ''));
        if ($periode === '' && $periods) {
            $periode = $periods[0];
        }
        if ($periode !== '') {
            $periode = PmsEmployeeSnapshot::normalizePeriode($periode);
        }

        $rows = HrPmsEmployeeSnapshot::query()
            ->where('periode', $periode)
            ->orderByRaw('CAST(level AS UNSIGNED) ASC, nik ASC')
            ->get(['id', 'periode', 'nik', 'nama', 'jabatan', 'departemen', 'sub_departemen', 'section', 'level', 'level_group', 'manager_nik', 'manager_nama', 'associate_manager_nik', 'associate_manager_nama', 'jumlah_bawahan', 'pendidikan', 'tanggal_join', 'tanggal_lahir', 'usia', 'source']);

        $data = $rows->map(function ($r) {
            $managerNik = trim((string) $r->manager_nik);

            return [
                'id' => $r->id,
                'periode' => (string) $r->periode,
                'nik' => $r->nik,
                'nama' => $r->nama,
                'jabatan' => $r->jabatan,
                'departemen' => $r->departemen,
                'sub_departemen' => $r->sub_departemen,
                'section' => $r->section,
                'level' => (int) $r->level,
                'group' => $r->level_group ?: PmsEmployeeSnapshot::groupOf($r->level),
                'manager' => $managerNik !== ''
                    ? ['nik' => $managerNik, 'nama' => $r->manager_nama, 'source' => $r->source]
                    : null,
                'associate_manager' => trim((string) $r->associate_manager_nik) !== ''
                    ? ['nik' => $r->associate_manager_nik, 'nama' => $r->associate_manager_nama, 'source' => $r->source]
                    : null,
                'jumlah_bawahan' => $r->jumlah_bawahan,
                'pendidikan' => $r->pendidikan,
                'tanggal_join' => $r->tanggal_join ? $r->tanggal_join->format('Y-m-d') : null,
                'tanggal_lahir' => $r->tanggal_lahir ? $r->tanggal_lahir->format('Y-m-d') : null,
                'usia' => $r->usia,
                'source' => $r->source,
            ];
        });

        if ($search !== '') {
            $needle = mb_strtolower($search);
            $data = $data->filter(function ($x) use ($needle) {
                foreach (['nik', 'nama', 'jabatan', 'departemen', 'sub_departemen', 'section'] as $f) {
                    if (mb_stripos((string) ($x[$f] ?? ''), $needle) !== false) {
                        return true;
                    }
                }
                foreach ([$x['manager'] ?? null, $x['associate_manager'] ?? null] as $atasan) {
                    if ($atasan && (mb_stripos((string) ($atasan['nik'] ?? ''), $needle) !== false
                        || mb_stripos((string) ($atasan['nama'] ?? ''), $needle) !== false)) {
                        return true;
                    }
                }

                return false;
            })->values();
        }

        $total = $data->count();
        $lastPage = max(1, (int) ceil($total / $perPage));
        if ($page > $lastPage) {
            $page = $lastPage;
        }
        $data = $data->forPage($page, $perPage)->values();

        return response()->json([
            'success' => true,
            'data' => $data,
            'meta' => [
                'page' => $page,
                'per_page' => $perPage,
                'total' => $total,
                'last_page' => $lastPage,
            ],
            'periods' => $periods,
            'periode' => $periode,
        ]);
    }

    public function participantsHistoryStore(Request $request)
    {
        $this->checkPermission('hr_upload_file_template_ipp');

        $validated = $request->validate([
            'periode' => 'required|string|max:10',
            'nik' => 'required|string|max:50',
            'nama' => 'nullable|string|max:150',
            'jabatan' => 'nullable|string|max:200',
            'departemen' => 'nullable|string|max:150',
            'sub_departemen' => 'nullable|string|max:150',
            'section' => 'nullable|string|max:150',
            'level' => 'nullable|string|max:10',
            'level_group' => 'nullable|string|max:10',
            'manager_nik' => 'nullable|string|max:50',
            'manager_nama' => 'nullable|string|max:150',
            'associate_manager_nik' => 'nullable|string|max:50',
            'associate_manager_nama' => 'nullable|string|max:150',
            'jumlah_bawahan' => 'nullable|integer|min:0',
            'pendidikan' => 'nullable|string|max:150',
            'tanggal_join' => 'nullable|date',
            'tanggal_lahir' => 'nullable|date',
            'usia' => 'nullable|integer|min:0|max:150',
        ]);

        $periode = PmsEmployeeSnapshot::normalizePeriode($validated['periode']);
        if ($periode === '' || !in_array($periode, $this->historyPeriodes(), true)) {
            return response()->json(['success' => false, 'message' => 'Periode tidak valid.'], 422);
        }

        $nik = trim((string) $validated['nik']);
        if ($nik === '') {
            return response()->json(['success' => false, 'message' => 'NIK wajib diisi.'], 422);
        }
        if (HrPmsEmployeeSnapshot::where('periode', $periode)->where('nik', $nik)->exists()) {
            return response()->json(['success' => false, 'message' => "NIK {$nik} sudah terdaftar pada periode {$periode}."], 422);
        }

        $emp = DB::table('hr_master_employee')->where('NIK', $nik)->first([
            'NIK', 'Nama', 'Jabatan', 'Departmen', 'Sub Departmen', 'Section', 'Level', 'Tgl Masuk', 'Tgl Lahir',
        ]);

        $nama = $this->valueOr($validated['nama'] ?? null, $emp->Nama ?? null);
        if ($nama === null) {
            return response()->json(['success' => false, 'message' => 'Nama wajib diisi untuk NIK di luar master karyawan.'], 422);
        }

        $level = $this->valueOr($validated['level'] ?? null, $emp->Level ?? null);
        $tanggalLahir = $this->valueOr($validated['tanggal_lahir'] ?? null, $emp->{'Tgl Lahir'} ?? null);
        $tanggalJoin = $this->valueOr($validated['tanggal_join'] ?? null, $emp->{'Tgl Masuk'} ?? null);

        $usia = $validated['usia'] ?? null;
        if (($usia === null || $usia === '') && $tanggalLahir) {
            $usia = (int) Carbon::parse($tanggalLahir)->age;
        }

        $managerNik = $this->nullableTrim($validated['manager_nik'] ?? null);
        $amNik = $this->nullableTrim($validated['associate_manager_nik'] ?? null);

        $row = HrPmsEmployeeSnapshot::create([
            'periode' => $periode,
            'nik' => $nik,
            'nama' => $nama,
            'jabatan' => $this->valueOr($validated['jabatan'] ?? null, $emp->Jabatan ?? null),
            'departemen' => $this->valueOr($validated['departemen'] ?? null, $emp->Departmen ?? null),
            'sub_departemen' => $this->valueOr($validated['sub_departemen'] ?? null, $emp->{'Sub Departmen'} ?? null),
            'section' => $this->valueOr($validated['section'] ?? null, $emp->Section ?? null),
            'level' => $level,
            'level_group' => $this->valueOr($validated['level_group'] ?? null, PmsEmployeeSnapshot::groupOf($level)),
            'manager_nik' => $managerNik,
            'manager_nama' => $this->valueOr($validated['manager_nama'] ?? null, $managerNik ? $this->resolveNamaByNik($managerNik) : null),
            'associate_manager_nik' => $amNik,
            'associate_manager_nama' => $this->valueOr($validated['associate_manager_nama'] ?? null, $amNik ? $this->resolveNamaByNik($amNik) : null),
            'jumlah_bawahan' => $validated['jumlah_bawahan'] ?? null,
            'pendidikan' => $this->valueOr($validated['pendidikan'] ?? null, PmsEmployeeSnapshot::pendidikanFor($nik)),
            'tanggal_join' => $tanggalJoin,
            'tanggal_lahir' => $tanggalLahir,
            'usia' => $usia,
            'source' => PmsEmployeeSnapshot::SOURCE_MANUAL,
            'captured_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Peserta berhasil ditambahkan.',
            'id' => $row->id,
        ]);
    }

    public function participantsHistoryUpdate($id, Request $request)
    {
        $this->checkPermission('hr_upload_file_template_ipp');

        $row = HrPmsEmployeeSnapshot::find($id);
        if (!$row) {
            return response()->json(['success' => false, 'message' => 'Data peserta tidak ditemukan.'], 404);
        }

        $validated = $request->validate([
            'nik' => 'required|string|max:50',
            'nama' => 'nullable|string|max:150',
            'jabatan' => 'nullable|string|max:200',
            'departemen' => 'nullable|string|max:150',
            'sub_departemen' => 'nullable|string|max:150',
            'section' => 'nullable|string|max:150',
            'level' => 'nullable|string|max:10',
            'level_group' => 'nullable|string|max:10',
            'manager_nik' => 'nullable|string|max:50',
            'manager_nama' => 'nullable|string|max:150',
            'associate_manager_nik' => 'nullable|string|max:50',
            'associate_manager_nama' => 'nullable|string|max:150',
            'jumlah_bawahan' => 'nullable|integer|min:0',
            'pendidikan' => 'nullable|string|max:150',
            'tanggal_join' => 'nullable|date',
            'tanggal_lahir' => 'nullable|date',
            'usia' => 'nullable|integer|min:0|max:150',
        ]);

        $nik = trim((string) $validated['nik']);
        if ($nik === '') {
            return response()->json(['success' => false, 'message' => 'NIK wajib diisi.'], 422);
        }
        if (strcasecmp($nik, (string) $row->nik) !== 0
            && HrPmsEmployeeSnapshot::where('periode', $row->periode)->where('nik', $nik)->exists()) {
            return response()->json(['success' => false, 'message' => "NIK {$nik} sudah terdaftar pada periode {$row->periode}."], 422);
        }

        $emp = DB::table('hr_master_employee')->where('NIK', $nik)->first([
            'NIK', 'Nama', 'Jabatan', 'Departmen', 'Sub Departmen', 'Section', 'Level', 'Tgl Masuk', 'Tgl Lahir',
        ]);

        $level = $validated['level'] ?? null;
        $tanggalLahir = $validated['tanggal_lahir'] ?? null;
        $tanggalJoin = $validated['tanggal_join'] ?? null;

        $usia = $validated['usia'] ?? null;
        if (($usia === null || $usia === '') && $tanggalLahir) {
            $usia = (int) Carbon::parse($tanggalLahir)->age;
        }

        $managerNik = $this->nullableTrim($validated['manager_nik'] ?? null);
        $amNik = $this->nullableTrim($validated['associate_manager_nik'] ?? null);

        $row->update([
            'nik' => $nik,
            'nama' => $this->valueOr($validated['nama'] ?? null, $this->valueOr($emp->Nama ?? null, $row->nama)),
            'jabatan' => $this->nullableTrim($validated['jabatan'] ?? null),
            'departemen' => $this->nullableTrim($validated['departemen'] ?? null),
            'sub_departemen' => $this->nullableTrim($validated['sub_departemen'] ?? null),
            'section' => $this->nullableTrim($validated['section'] ?? null),
            'level' => $level,
            'level_group' => $this->valueOr($validated['level_group'] ?? null, PmsEmployeeSnapshot::groupOf($level)),
            'manager_nik' => $managerNik,
            'manager_nama' => $this->valueOr($validated['manager_nama'] ?? null, $managerNik ? $this->resolveNamaByNik($managerNik) : null),
            'associate_manager_nik' => $amNik,
            'associate_manager_nama' => $this->valueOr($validated['associate_manager_nama'] ?? null, $amNik ? $this->resolveNamaByNik($amNik) : null),
            'jumlah_bawahan' => $validated['jumlah_bawahan'] ?? null,
            'pendidikan' => $this->nullableTrim($validated['pendidikan'] ?? null),
            'tanggal_join' => $tanggalJoin,
            'tanggal_lahir' => $tanggalLahir,
            'usia' => $usia,
        ]);

        return response()->json(['success' => true, 'message' => 'Peserta berhasil diperbarui.']);
    }

    public function participantsHistoryDestroy($id)
    {
        $this->checkPermission('hr_upload_file_template_ipp');

        $row = HrPmsEmployeeSnapshot::find($id);
        if (!$row) {
            return response()->json(['success' => false, 'message' => 'Data peserta tidak ditemukan.'], 404);
        }

        $row->delete();

        return response()->json(['success' => true, 'message' => 'Peserta berhasil dihapus dari history.']);
    }

    public function participantsHistorySearchNik(Request $request)
    {
        $this->checkPermission('hr_upload_file_template_ipp');

        $term = trim((string) $request->get('term', ''));
        if ($term === '') {
            return response()->json(['data' => []]);
        }

        $rows = DB::table('hr_master_employee')
            ->where(function ($q) use ($term) {
                $q->where('NIK', 'like', "%{$term}%")
                    ->orWhere('Nama', 'like', "%{$term}%")
                    ->orWhere('Jabatan', 'like', "%{$term}%")
                    ->orWhere('Departmen', 'like', "%{$term}%")
                    ->orWhere('Sub Departmen', 'like', "%{$term}%");
            })
            ->orderBy('Nama')
            ->limit(10)
            ->get(['NIK', 'Nama', 'Jabatan', 'Departmen', 'Sub Departmen', 'Section', 'Level', 'Aktif'])
            ->map(function ($r) {
                return [
                    'nik' => $r->NIK,
                    'name' => (string) $r->Nama . ($r->Aktif === 'N' ? ' (non-aktif)' : ''),
                    'row' => [
                        'nama' => $r->Nama,
                        'jabatan' => $r->Jabatan,
                        'departemen' => trim((string) $r->Departmen),
                        'sub_departemen' => trim((string) ($r->{'Sub Departmen'} ?? '')),
                        'section' => trim((string) ($r->Section ?? '')),
                        'level' => $r->Level,
                    ],
                ];
            })
            ->values();

        return response()->json(['data' => $rows]);
    }

    private function historyPeriodes(): array
    {
        return HrPmsEmployeeSnapshot::query()
            ->distinct()
            ->pluck('periode')
            ->merge(HrPk::query()->distinct()->pluck('periode'))
            ->map(fn ($p) => PmsEmployeeSnapshot::normalizePeriode((string) $p))
            ->filter()
            ->unique()
            ->sortByDesc(fn ($p) => (int) $p)
            ->values()
            ->all();
    }

    private function valueOr($value, $fallback = null)
    {
        if ($value !== null && $value !== '') {
            return $value;
        }

        return $fallback;
    }

    private function nullableTrim($value): ?string
    {
        $value = trim((string) $value);

        return $value === '' ? null : $value;
    }

    private function resolveNamaByNik(string $nik): ?string
    {
        $nik = trim($nik);
        if ($nik === '') {
            return null;
        }

        $name = DB::table('users')
            ->where('nik', $nik)
            ->where('status', '1')
            ->value('name');
        if ($name !== null && trim((string) $name) !== '') {
            return (string) $name;
        }

        $name = DB::table('hr_master_employee')->where('NIK', $nik)->value('Nama');

        return $name !== null && trim((string) $name) !== '' ? (string) $name : null;
    }

    public function show($id)
    {
        $this->checkPermission('hr_upload_file_template_ipp');

        $pk = HrPk::find($id);
        if (!$pk) {
            return response()->json(['success' => false, 'message' => 'Data tidak ditemukan.'], 404);
        }

        // Urutkan sesuai urutan tersimpan (hasil upload/editor), bukan urutan numerik
        // no_urut saja, agar varian level_group tidak saling menyelip di modal.
        $details = $pk->details()->reorder('id')->get();

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $pk->id,
                'periode' => $pk->periode,
                'file_name' => $pk->file_name,
                'send_by_username' => $pk->send_by_username,
                'created_at' => $pk->created_at ? $pk->created_at->format('d M Y H:i') : '',
                'details' => $details->map(function ($d) {
                    return [
                        'id' => $d->id,
                        'section' => $d->section,
                        'category' => $d->category,
                        'level_group' => $d->level_group,
                        'no_urut' => $d->no_urut,
                        'area' => $d->area,
                        'competency_behavior' => $d->competency_behavior,
                        'key_behavior' => $d->key_behavior,
                        'kriteria_1' => $d->kriteria_1,
                        'kriteria_2' => $d->kriteria_2,
                        'kriteria_3' => $d->kriteria_3,
                        'kriteria_4' => $d->kriteria_4,
                        'kriteria_5' => $d->kriteria_5,
                    ];
                })->values(),
            ],
        ]);
    }

    public function update($id, Request $request)
    {
        $this->checkPermission('hr_upload_file_template_ipp');

        $pk = HrPk::find($id);
        if (!$pk) {
            return response()->json(['success' => false, 'message' => 'Data tidak ditemukan.'], 404);
        }

        $data = $this->validatePayload($request);
        $periode = trim((string) $data['periode']);

        DB::transaction(function () use ($pk, $data, $periode) {
            // Jika periode diubah dan bertabrakan dengan master lain, bersihkan duplikat
            HrPk::where('periode', $periode)->where('id', '!=', $pk->id)->delete();

            $pk->periode = $periode;
            if (!empty($data['file_name'])) {
                $pk->file_name = $data['file_name'];
            }
            $pk->save();

            $pk->details()->delete();
            foreach ($data['details'] as $d) {
                $pk->details()->create([
                    'section' => $d['section'],
                    'category' => $d['category'] ?? null,
                    'level_group' => $d['level_group'] ?? null,
                    'no_urut' => $d['no_urut'],
                    'area' => $d['area'] ?? null,
                    'competency_behavior' => $d['competency_behavior'] ?? null,
                    'key_behavior' => $d['key_behavior'] ?? null,
                    'kriteria_1' => $d['kriteria_1'] ?? null,
                    'kriteria_2' => $d['kriteria_2'] ?? null,
                    'kriteria_3' => $d['kriteria_3'] ?? null,
                    'kriteria_4' => $d['kriteria_4'] ?? null,
                    'kriteria_5' => $d['kriteria_5'] ?? null,
                ]);
            }
        });

        return response()->json([
            'success' => true,
            'message' => "Master Penilaian Karya periode {$periode} berhasil diperbarui.",
            'id' => $pk->id,
        ]);
    }

    public function delete($id)
    {
        $this->checkPermission('hr_upload_file_template_ipp');

        $pk = HrPk::find($id);
        if (!$pk) {
            return response()->json(['success' => false, 'message' => 'Data tidak ditemukan.'], 404);
        }

        $periode = $pk->periode;
        $pk->delete();

        return response()->json([
            'success' => true,
            'message' => 'Master Penilaian Karya' . ($periode ? " periode {$periode}" : '') . ' berhasil dihapus.',
        ]);
    }

    private function validatePayload(Request $request): array
    {
        $validated = $request->validate([
            'periode' => 'required|string|max:10',
            'file_name' => 'nullable|string|max:255',
            'details' => 'required|array|min:1',
            'details.*.section' => 'required|string|max:150',
            'details.*.category' => 'nullable|string|max:100',
            'details.*.level_group' => 'nullable|string|max:10',
            'details.*.no_urut' => 'required|string|max:10',
            'details.*.area' => 'nullable|string',
            'details.*.competency_behavior' => 'nullable|string',
            'details.*.key_behavior' => 'nullable|string',
            'details.*.kriteria_1' => 'nullable|string',
            'details.*.kriteria_2' => 'nullable|string',
            'details.*.kriteria_3' => 'nullable|string',
            'details.*.kriteria_4' => 'nullable|string',
            'details.*.kriteria_5' => 'nullable|string',
        ]);

        return [
            'periode' => trim((string) $validated['periode']),
            'file_name' => $validated['file_name'] ?? null,
            'details' => $validated['details'],
        ];
    }

    private function amUserByNik(string $nik): ?array
    {
        $nik = trim($nik);
        if ($nik === '') {
            return null;
        }
        if (!DB::table('users')->where('nik', $nik)->exists()) {
            return null;
        }
        $nama = DB::table('hr_master_employee')->where('NIK', $nik)->value('Nama');

        return ['nik' => $nik, 'name' => $nama ?? ''];
    }

    public function amMappingEmployeeSearch(Request $request)
    {
        $this->checkPermission('hr_upload_file_template_ipp');

        $role = $request->input('role') === 'am' ? 'am' : 'peserta';
        $q = trim((string) $request->input('q', ''));
        $page = max(1, (int) $request->input('page', 1));
        $perPage = 20;

        $levels = $role === 'am' ? ['5', '6', '7'] : ['4', '5'];

        $base = DB::table('hr_master_employee as e')
            ->join('users as u', 'u.nik', '=', 'e.NIK')
            ->where('e.Aktif', 'Y')
            ->whereIn('e.Level', $levels);

        if ($q !== '') {
            $base->where(function ($qq) use ($q) {
                $qq->where('e.NIK', 'like', "%{$q}%")
                    ->orWhere('e.Nama', 'like', "%{$q}%")
                    ->orWhere('e.Jabatan', 'like', "%{$q}%")
                    ->orWhere('e.Departmen', 'like', "%{$q}%")
                    ->orWhere('e.Sub Departmen', 'like', "%{$q}%");
            });
        }

        $total = (clone $base)->count();
        $rows = $base->orderBy('e.Nama')
            ->forPage($page, $perPage)
            ->get(['e.NIK', 'e.Nama', 'e.Jabatan', 'e.Departmen']);

        $results = $rows->map(fn ($r) => [
            'id' => $r->NIK,
            'text' => $r->NIK . ' — ' . $r->Nama . ' · ' . trim((string) $r->Jabatan),
        ])->all();

        return response()->json([
            'results' => $results,
            'pagination' => ['more' => $page * $perPage < $total],
        ]);
    }

    public function amMappingList()
    {
        $this->checkPermission('hr_upload_file_template_ipp');

        $rows = HrPkAssociateManagerMapping::orderBy('nama')
            ->get()
            ->map(fn ($m) => [
                'id' => $m->id,
                'nik' => $m->nik,
                'nama' => $m->nama,
                'nik_associate_manager' => $m->nik_associate_manager,
                'nama_associate_manager' => $m->nama_associate_manager,
            ]);

        return response()->json(['data' => $rows]);
    }

    public function amMappingStore(Request $request)
    {
        $this->checkPermission('hr_upload_file_template_ipp');

        $validated = $request->validate([
            'nik' => 'required|string|max:50',
            'nik_associate_manager' => 'required|string|max:50',
        ]);

        $nik = trim((string) $validated['nik']);
        $nikAm = trim((string) $validated['nik_associate_manager']);

        if (strtolower($nik) === strtolower($nikAm)) {
            return response()->json([
                'success' => false,
                'message' => "NIK peserta dan Associate Manager tidak boleh sama.",
            ], 422);
        }

        $employee = $this->amUserByNik($nik);
        if (!$employee) {
            return response()->json([
                'success' => false,
                'message' => "NIK peserta '{$nik}' tidak ditemukan di tabel users.",
            ], 422);
        }

        $am = $this->amUserByNik($nikAm);
        if (!$am) {
            return response()->json([
                'success' => false,
                'message' => "NIK Associate Manager '{$nikAm}' tidak ditemukan di tabel users.",
            ], 422);
        }

        $mapping = HrPkAssociateManagerMapping::firstOrNew(['nik' => $employee['nik']]);
        $mapping->nama = $employee['name'];
        $mapping->nik_associate_manager = $am['nik'];
        $mapping->nama_associate_manager = $am['name'];
        $mapping->created_by = Auth::user()->username ?? (Auth::user()->name ?? 'system');
        $mapping->save();

        return response()->json([
            'success' => true,
            'message' => 'Mapping Associate Manager berhasil disimpan.',
            'id' => $mapping->id,
        ]);
    }

    public function amMappingDelete($id)
    {
        $this->checkPermission('hr_upload_file_template_ipp');

        HrPkAssociateManagerMapping::findOrFail($id)->delete();

        return response()->json([
            'success' => true,
            'message' => 'Mapping Associate Manager berhasil dihapus.',
        ]);
    }

    private function resolveUserByNik(string $nik): ?array
    {
        $nik = trim($nik);
        if ($nik === '') {
            return null;
        }

        $user = DB::table('users')
            ->where('nik', $nik)
            ->whereNotNull('nik')
            ->where('nik', '!=', '')
            ->where('status', '1')
            ->first(['nik', 'name']);

        if (!$user) {
            return null;
        }

        return [
            'nik' => $user->nik,
            'name' => $user->name,
        ];
    }

    public function spvSearchNik(Request $request)
    {
        $this->checkPermission('hr_upload_file_template_ipp');

        $term = trim((string) $request->get('term', ''));
        $role = trim((string) $request->get('role', ''));

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

    public function spvOtorisasiList()
    {
        $this->checkPermission('hr_upload_file_template_ipp');

        $rows = HrPkOtorisasi::where('kategori', 'spv')
            ->orderBy('nama_yang_bersangkutan')
            ->orderBy('id')
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

    /**
     * Isi atribut peserta PK (hr_pk_otorisasi) yang masih kosong pada snapshot
     * periode yang sudah ada; tidak menambah peserta baru ke periode lama.
     */
    private function capturePkOtorisasiSnapshot(HrPkOtorisasi $otorisasi): void
    {
        $nik = trim((string) $otorisasi->nik_yang_bersangkutan);
        if ($nik === '') {
            return;
        }

        if (!DB::table('hr_master_employee')->where('NIK', $nik)->exists()) {
            return;
        }

        $opts = [
            'manager_nik' => trim((string) $otorisasi->nik_manager) !== '' ? $otorisasi->nik_manager : null,
            'manager_nama' => $otorisasi->nama_manager,
            'associate_manager_nik' => trim((string) $otorisasi->nik_associate_manager) !== '' ? $otorisasi->nik_associate_manager : null,
            'associate_manager_nama' => $otorisasi->nama_associate_manager,
            'jumlah_bawahan' => $otorisasi->jumlah_bawahan !== null ? (int) $otorisasi->jumlah_bawahan : null,
            'source' => PmsEmployeeSnapshot::SOURCE_PK_OTORISASI,
        ];

        foreach (DB::table('hr_pk')->distinct()->pluck('periode') as $periode) {
            PmsEmployeeSnapshot::fillDeferred((string) $periode, $nik, $opts);
        }
    }

    public function spvOtorisasiStore(Request $request)
    {
        $this->checkPermission('hr_upload_file_template_ipp');

        $validated = $request->validate([
            'nik_yang_bersangkutan' => 'required|string|max:50',
            'nik_manager' => 'required|string|max:50',
            'nik_associate_manager' => 'nullable|string|max:50',
        ]);

        $ybs = $this->resolveUserByNik($validated['nik_yang_bersangkutan']);
        if (!$ybs) {
            return response()->json([
                'success' => false,
                'message' => "NIK peserta '{$validated['nik_yang_bersangkutan']}' tidak ditemukan di tabel users.",
            ], 422);
        }

        $nikManager = trim((string) $validated['nik_manager']);
        $nikAssociate = trim((string) ($validated['nik_associate_manager'] ?? ''));

        if (strtolower($nikManager) === strtolower($ybs['nik'])) {
            return response()->json([
                'success' => false,
                'message' => 'NIK manager tidak boleh sama dengan NIK peserta.',
            ], 422);
        }
        if ($nikAssociate !== '' && strtolower($nikAssociate) === strtolower($ybs['nik'])) {
            return response()->json([
                'success' => false,
                'message' => 'NIK Associate Manager tidak boleh sama dengan NIK peserta.',
            ], 422);
        }

        $mgr = $this->resolveUserByNik($nikManager);
        if (!$mgr) {
            return response()->json([
                'success' => false,
                'message' => "NIK manager '{$nikManager}' tidak ditemukan di tabel users.",
            ], 422);
        }

        $asm = null;
        if ($nikAssociate !== '') {
            $asm = $this->resolveUserByNik($nikAssociate);
            if (!$asm) {
                return response()->json([
                    'success' => false,
                    'message' => "NIK Associate Manager '{$nikAssociate}' tidak ditemukan di tabel users.",
                ], 422);
            }
        }

        $existing = HrPkOtorisasi::where('nik_yang_bersangkutan', $ybs['nik'])->first();
        if ($existing && $existing->kategori !== 'spv') {
            return response()->json([
                'success' => false,
                'message' => "NIK '{$ybs['nik']}' sudah terdaftar di Mapping Foreman. Hapus dulu dari sana sebelum menambah Mapping SPV.",
            ], 422);
        }

        $username = Auth::user()->username ?? (Auth::user()->name ?? 'system');

        $otorisasi = HrPkOtorisasi::updateOrCreate(
            ['nik_yang_bersangkutan' => $ybs['nik']],
            [
                'kategori' => 'spv',
                'nama_yang_bersangkutan' => $ybs['name'],
                'nik_manager' => $mgr['nik'],
                'nama_manager' => $mgr['name'],
                'nik_associate_manager' => $asm ? $asm['nik'] : null,
                'nama_associate_manager' => $asm ? $asm['name'] : null,
                'send_by_username' => $username,
            ]
        );

        $this->capturePkOtorisasiSnapshot($otorisasi);

        return response()->json([
            'success' => true,
            'message' => 'Peserta berhasil disimpan.',
            'id' => $otorisasi->id,
        ]);
    }

    public function spvOtorisasiUpdate($id, Request $request)
    {
        $this->checkPermission('hr_upload_file_template_ipp');

        $otorisasi = HrPkOtorisasi::findOrFail($id);
        abort_unless($otorisasi->kategori === 'spv', 404);

        $validated = $request->validate([
            'nik_yang_bersangkutan' => 'required|string|max:50',
            'nik_manager' => 'required|string|max:50',
            'nik_associate_manager' => 'nullable|string|max:50',
        ]);

        $ybs = $this->resolveUserByNik($validated['nik_yang_bersangkutan']);
        if (!$ybs) {
            return response()->json([
                'success' => false,
                'message' => "NIK peserta '{$validated['nik_yang_bersangkutan']}' tidak ditemukan di tabel users.",
            ], 422);
        }

        $nikManager = trim((string) $validated['nik_manager']);
        $nikAssociate = trim((string) ($validated['nik_associate_manager'] ?? ''));

        if (strtolower($nikManager) === strtolower($ybs['nik'])) {
            return response()->json([
                'success' => false,
                'message' => 'NIK manager tidak boleh sama dengan NIK peserta.',
            ], 422);
        }
        if ($nikAssociate !== '' && strtolower($nikAssociate) === strtolower($ybs['nik'])) {
            return response()->json([
                'success' => false,
                'message' => 'NIK Associate Manager tidak boleh sama dengan NIK peserta.',
            ], 422);
        }

        $mgr = $this->resolveUserByNik($nikManager);
        if (!$mgr) {
            return response()->json([
                'success' => false,
                'message' => "NIK manager '{$nikManager}' tidak ditemukan di tabel users.",
            ], 422);
        }

        $asm = null;
        if ($nikAssociate !== '') {
            $asm = $this->resolveUserByNik($nikAssociate);
            if (!$asm) {
                return response()->json([
                    'success' => false,
                    'message' => "NIK Associate Manager '{$nikAssociate}' tidak ditemukan di tabel users.",
                ], 422);
            }
        }

        $duplicate = HrPkOtorisasi::where('nik_yang_bersangkutan', $ybs['nik'])
            ->where('id', '!=', $otorisasi->id)
            ->exists();
        if ($duplicate) {
            return response()->json([
                'success' => false,
                'message' => "Peserta '{$ybs['nik']}' sudah terdaftar pada baris lain.",
            ], 422);
        }

        $otorisasi->update([
            'nik_yang_bersangkutan' => $ybs['nik'],
            'nama_yang_bersangkutan' => $ybs['name'],
            'nik_manager' => $mgr['nik'],
            'nama_manager' => $mgr['name'],
            'nik_associate_manager' => $asm ? $asm['nik'] : null,
            'nama_associate_manager' => $asm ? $asm['name'] : null,
        ]);

        $this->capturePkOtorisasiSnapshot($otorisasi);

        return response()->json([
            'success' => true,
            'message' => 'Peserta berhasil diperbarui.',
        ]);
    }

    public function spvOtorisasiDelete($id)
    {
        $this->checkPermission('hr_upload_file_template_ipp');

        $otorisasi = HrPkOtorisasi::findOrFail($id);
        abort_unless($otorisasi->kategori === 'spv', 404);
        $otorisasi->delete();

        return response()->json([
            'success' => true,
            'message' => 'Peserta berhasil dihapus.',
        ]);
    }

    public function foremanSearchNik(Request $request)
    {
        $this->checkPermission('hr_upload_file_template_ipp');

        $term = trim((string) $request->get('term', ''));

        $query = DB::table('hr_master_employee')
            ->select('NIK as nik', 'Nama as name')
            ->where('Aktif', 'Y')
            ->whereRaw('CAST(Level AS UNSIGNED) IN (2, 3)');

        if ($term !== '') {
            $query->where(function ($q) use ($term) {
                $q->where('NIK', 'like', "%{$term}%")
                    ->orWhere('Nama', 'like', "%{$term}%")
                    ->orWhere('Jabatan', 'like', "%{$term}%");
            });
        }

        $rows = $query
            ->orderBy('NIK')
            ->limit($term !== '' ? 10 : 50)
            ->get();

        return response()->json(['data' => $rows]);
    }

    public function foremanOtorisasiList()
    {
        $this->checkPermission('hr_upload_file_template_ipp');

        $rows = HrPkOtorisasi::where('kategori', 'foreman')
            ->orderBy('nama_yang_bersangkutan')
            ->orderBy('id')
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

    public function foremanOtorisasiStore(Request $request)
    {
        $this->checkPermission('hr_upload_file_template_ipp');

        $validated = $request->validate([
            'nik_yang_bersangkutan' => 'required|string|max:50',
            'nik_manager' => 'required|string|max:50',
            'nik_associate_manager' => 'nullable|string|max:50',
        ]);

        $nik = trim((string) $validated['nik_yang_bersangkutan']);
        $nikManager = trim((string) $validated['nik_manager']);
        $nikAssociate = trim((string) ($validated['nik_associate_manager'] ?? ''));

        $emp = DB::table('hr_master_employee')
            ->where('NIK', $nik)
            ->where('Aktif', 'Y')
            ->whereRaw('CAST(Level AS UNSIGNED) IN (2, 3)')
            ->first(['NIK', 'Nama']);

        if (!$emp) {
            return response()->json([
                'success' => false,
                'message' => "NIK peserta '{$nik}' bukan karyawan aktif Level 2-3.",
            ], 422);
        }

        if (strtolower($nik) === strtolower($nikManager)) {
            return response()->json([
                'success' => false,
                'message' => 'NIK peserta dan Manager tidak boleh sama.',
            ], 422);
        }
        if ($nikAssociate !== '' && strtolower($nikAssociate) === strtolower($nik)) {
            return response()->json([
                'success' => false,
                'message' => 'NIK peserta dan Associate Manager tidak boleh sama.',
            ], 422);
        }

        $mgr = $this->resolveUserByNik($nikManager);
        if (!$mgr) {
            return response()->json([
                'success' => false,
                'message' => "NIK Manager '{$nikManager}' tidak ditemukan di tabel users.",
            ], 422);
        }

        $asm = null;
        if ($nikAssociate !== '') {
            $asm = $this->resolveUserByNik($nikAssociate);
            if (!$asm) {
                return response()->json([
                    'success' => false,
                    'message' => "NIK Associate Manager '{$nikAssociate}' tidak ditemukan di tabel users.",
                ], 422);
            }
        }

        $existing = HrPkOtorisasi::where('nik_yang_bersangkutan', $emp->NIK)->first();
        if ($existing && $existing->kategori !== 'foreman') {
            return response()->json([
                'success' => false,
                'message' => "NIK '{$emp->NIK}' sudah terdaftar di Mapping SPV. Hapus dulu dari sana sebelum menambah Mapping Foreman.",
            ], 422);
        }

        $username = Auth::user()->username ?? (Auth::user()->name ?? 'system');

        $otorisasi = HrPkOtorisasi::updateOrCreate(
            ['nik_yang_bersangkutan' => $emp->NIK],
            [
                'kategori' => 'foreman',
                'nama_yang_bersangkutan' => $emp->Nama,
                'nik_manager' => $mgr['nik'],
                'nama_manager' => $mgr['name'],
                'nik_associate_manager' => $asm ? $asm['nik'] : null,
                'nama_associate_manager' => $asm ? $asm['name'] : null,
                'send_by_username' => $username,
            ]
        );

        $this->capturePkOtorisasiSnapshot($otorisasi);

        return response()->json([
            'success' => true,
            'message' => 'Peserta Mapping Foreman berhasil disimpan.',
            'id' => $otorisasi->id,
        ]);
    }

    public function foremanOtorisasiUpdate($id, Request $request)
    {
        $this->checkPermission('hr_upload_file_template_ipp');

        $otorisasi = HrPkOtorisasi::findOrFail($id);
        abort_unless($otorisasi->kategori === 'foreman', 404);

        $validated = $request->validate([
            'nik_yang_bersangkutan' => 'required|string|max:50',
            'nik_manager' => 'required|string|max:50',
            'nik_associate_manager' => 'nullable|string|max:50',
        ]);

        $nik = trim((string) $validated['nik_yang_bersangkutan']);
        $nikManager = trim((string) $validated['nik_manager']);
        $nikAssociate = trim((string) ($validated['nik_associate_manager'] ?? ''));

        $emp = DB::table('hr_master_employee')
            ->where('NIK', $nik)
            ->where('Aktif', 'Y')
            ->whereRaw('CAST(Level AS UNSIGNED) IN (2, 3)')
            ->first(['NIK', 'Nama']);

        if (!$emp) {
            return response()->json([
                'success' => false,
                'message' => "NIK peserta '{$nik}' bukan karyawan aktif Level 2-3.",
            ], 422);
        }

        if (strtolower($nik) === strtolower($nikManager)) {
            return response()->json([
                'success' => false,
                'message' => 'NIK peserta dan Manager tidak boleh sama.',
            ], 422);
        }
        if ($nikAssociate !== '' && strtolower($nikAssociate) === strtolower($nik)) {
            return response()->json([
                'success' => false,
                'message' => 'NIK peserta dan Associate Manager tidak boleh sama.',
            ], 422);
        }

        $mgr = $this->resolveUserByNik($nikManager);
        if (!$mgr) {
            return response()->json([
                'success' => false,
                'message' => "NIK Manager '{$nikManager}' tidak ditemukan di tabel users.",
            ], 422);
        }

        $asm = null;
        if ($nikAssociate !== '') {
            $asm = $this->resolveUserByNik($nikAssociate);
            if (!$asm) {
                return response()->json([
                    'success' => false,
                    'message' => "NIK Associate Manager '{$nikAssociate}' tidak ditemukan di tabel users.",
                ], 422);
            }
        }

        $duplicate = HrPkOtorisasi::where('nik_yang_bersangkutan', $emp->NIK)
            ->where('id', '!=', $otorisasi->id)
            ->exists();
        if ($duplicate) {
            return response()->json([
                'success' => false,
                'message' => "Peserta '{$emp->NIK}' sudah terdaftar pada baris lain.",
            ], 422);
        }

        $otorisasi->update([
            'kategori' => 'foreman',
            'nik_yang_bersangkutan' => $emp->NIK,
            'nama_yang_bersangkutan' => $emp->Nama,
            'nik_manager' => $mgr['nik'],
            'nama_manager' => $mgr['name'],
            'nik_associate_manager' => $asm ? $asm['nik'] : null,
            'nama_associate_manager' => $asm ? $asm['name'] : null,
        ]);

        $this->capturePkOtorisasiSnapshot($otorisasi);

        return response()->json([
            'success' => true,
            'message' => 'Peserta Mapping Foreman berhasil diperbarui.',
        ]);
    }

    public function foremanOtorisasiDelete($id)
    {
        $this->checkPermission('hr_upload_file_template_ipp');

        $otorisasi = HrPkOtorisasi::findOrFail($id);
        abort_unless($otorisasi->kategori === 'foreman', 404);
        $otorisasi->delete();

        return response()->json([
            'success' => true,
            'message' => 'Peserta Mapping Foreman berhasil dihapus.',
        ]);
    }
}