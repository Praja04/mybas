<?php

namespace App\Http\Controllers\HR;

use App\Http\Controllers\Controller;
use App\Imports\HR\TemplateIppForemanImport;
use App\Models\HR\HrIpp;
use App\Models\HR\HrIppForeman;
use App\Models\HR\HrIppForemanNilai;
use App\Models\HR\HrPk;
use App\Models\HR\HrPmsEmployeeSnapshot;
use App\Support\PmsEmployeeSnapshot;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

/**
 * Template IPP Level 2-3 (rubrik): satu template per periode untuk semua peserta,
 * penilaian Leader Assessment (1-5) diisi atasan pada halaman ipp-user-approval.
 */
class UploadFileTemplateIppForemanController extends Controller
{
    private function checkPermission(string $codename): void
    {
        $userCodenames = Auth::user()->getAllPermissionCodenames();
        if (!in_array($codename, $userCodenames, true)) {
            abort(403);
        }
    }

    public function download()
    {
        $this->checkPermission('hr_upload_file_template_ipp');

        $template = public_path('templates/hr/Template IPP 2-3.xlsx');
        if (!file_exists($template)) {
            abort(404, 'File template tidak ditemukan.');
        }

        return response()->download($template, 'Template IPP 2-3.xlsx');
    }

    public function parse(Request $request)
    {
        $this->checkPermission('hr_upload_file_template_ipp');

        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls',
            'periode' => 'nullable|string|max:10',
        ]);

        $periode = $this->normalizePeriode($request->input('periode'));
        if ($periode === null) {
            return response()->json([
                'success' => false,
                'message' => $this->periodeMessage(),
            ], 422);
        }

        $file = $request->file('file');
        $originalName = $file->getClientOriginalName();

        $dir = 'uploads/hr-ipp-foreman-template';
        $filename = time() . '_' . preg_replace('/\s+/', '_', $originalName);
        $path = $file->storeAs($dir, $filename, 'public');
        $fullPath = storage_path('app/public/' . $path);

        try {
            $import = new TemplateIppForemanImport();
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
                'message' => 'Tidak ada data rubrik yang ditemukan. Pastikan format sesuai Template IPP 2-3.',
            ], 422);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'periode' => $periode,
                'file_name' => $originalName,
                'details' => $parsed['details'],
            ],
        ]);
    }

    public function status(Request $request)
    {
        $this->checkPermission('hr_upload_file_template_ipp');

        $periode = $this->normalizePeriode($request->get('periode'));
        if ($periode === null) {
            return response()->json(['exists' => false]);
        }

        $ipp = HrIpp::where('template_type', 'rubric')->where('periode', $periode)->first();
        if (!$ipp) {
            return response()->json(['exists' => false]);
        }

        $foremanIds = HrIppForeman::where('ipp_id', $ipp->id)->pluck('id');
        $filled = $foremanIds->isNotEmpty()
            ? HrIppForemanNilai::whereIn('hr_ipp_foreman_id', $foremanIds)->whereNotNull('nilai')->count()
            : 0;

        return response()->json([
            'exists' => true,
            'id' => $ipp->id,
            'rows' => $foremanIds->count(),
            'peserta' => HrPmsEmployeeSnapshot::where('periode', $ipp->periode)->where('level_group', '2-3')->count(),
            'filled' => $filled,
            'uploaded_by' => $this->uploaderLabel((string) $ipp->send_by_username),
            'uploaded_at' => optional($ipp->updated_at)->format('d M Y H:i'),
        ]);
    }

    public function show($id)
    {
        $this->checkPermission('hr_upload_file_template_ipp');

        $ipp = HrIpp::findOrFail($id);
        if (!$ipp->isRubric()) {
            abort(404, 'Bukan template rubrik.');
        }

        $rows = $ipp->foremanRows()->get();
        $foremanIds = $rows->pluck('id');

        $filled = $foremanIds->isEmpty()
            ? 0
            : HrIppForemanNilai::whereIn('hr_ipp_foreman_id', $foremanIds->all())
                ->whereNotNull('nilai')
                ->count();

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $ipp->id,
                'periode' => $ipp->periode,
                'uploaded_by' => $this->uploaderLabel((string) $ipp->send_by_username),
                'uploaded_at' => optional($ipp->updated_at)->format('d M Y H:i'),
                'rows' => $rows->count(),
                'peserta' => HrPmsEmployeeSnapshot::where('periode', $ipp->periode)
                    ->where('level_group', '2-3')
                    ->count(),
                'filled' => $filled,
                'details' => $rows->map(function ($r) {
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
                })->values(),
            ],
        ]);
    }

    public function store(Request $request)
    {
        $this->checkPermission('hr_upload_file_template_ipp');

        $validated = $request->validate([
            'periode' => 'required|string|max:10',
            'details' => 'required|array|min:1',
            'details.*.no_urut' => 'required|string|max:10',
            'details.*.section' => 'nullable|string|max:150',
            'details.*.area' => 'nullable|string',
            'details.*.competency_behavior' => 'nullable|string',
            'details.*.key_behavior' => 'nullable|string',
            'details.*.kriteria_1' => 'nullable|string',
            'details.*.kriteria_2' => 'nullable|string',
            'details.*.kriteria_3' => 'nullable|string',
            'details.*.kriteria_4' => 'nullable|string',
            'details.*.kriteria_5' => 'nullable|string',
        ]);

        $periode = $this->normalizePeriode($validated['periode']);
        if ($periode === null) {
            throw ValidationException::withMessages(['periode' => $this->periodeMessage()]);
        }

        if (!HrPmsEmployeeSnapshot::where('periode', $periode)->exists()) {
            throw ValidationException::withMessages([
                'periode' => 'Peserta periode ' . $periode . ' belum di-capture. Silakan buat/upload template PK periode tersebut agar peserta tercapture.',
            ]);
        }

        $pesertaCount = HrPmsEmployeeSnapshot::where('periode', $periode)
            ->where('level_group', '2-3')
            ->count();
        if ($pesertaCount === 0) {
            throw ValidationException::withMessages([
                'periode' => 'Tidak ada peserta Level 2-3 pada snapshot periode ' . $periode . '.',
            ]);
        }

        $uploaderNik = trim((string) (Auth::user()->nik ?? ''));
        $username = Auth::user()->username ?? (Auth::user()->name ?? 'system');
        $sendBy = $uploaderNik !== '' ? $uploaderNik : $username;

        $result = null;

        DB::transaction(function () use ($periode, $validated, $sendBy, $pesertaCount, &$result) {
            $existing = HrIpp::where('template_type', 'rubric')->where('periode', $periode)->first();
            if ($existing) {
                $foremanIds = HrIppForeman::where('ipp_id', $existing->id)->pluck('id');
                $hasNilai = $foremanIds->isNotEmpty()
                    && HrIppForemanNilai::whereIn('hr_ipp_foreman_id', $foremanIds)->exists();
                if ($hasNilai) {
                    throw ValidationException::withMessages([
                        'periode' => 'Template IPP Level 2-3 periode ' . $periode . ' sudah memiliki penilaian. Hapus penilaian terlebih dahulu untuk mengganti template.',
                    ]);
                }
                $existing->delete();
            }

            $ipp = HrIpp::create([
                'periode' => $periode,
                'dept' => null,
                'position' => null,
                'golongan' => '2-3',
                'template_type' => 'rubric',
                'send_by_username' => $sendBy,
            ]);

            foreach ($validated['details'] as $d) {
                HrIppForeman::create([
                    'ipp_id' => $ipp->id,
                    'no_urut' => (string) $d['no_urut'],
                    'section' => $d['section'] ?? null,
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

            $result = [
                'id' => $ipp->id,
                'rows' => count($validated['details']),
                'peserta' => $pesertaCount,
            ];
        });

        return response()->json([
            'success' => true,
            'message' => 'Template IPP Level 2-3 periode ' . $periode . ' tersimpan ('
                . $result['rows'] . ' baris rubrik, ' . $result['peserta'] . ' peserta).',
            'id' => $result['id'],
        ]);
    }

    private function normalizePeriode($periode): ?string
    {
        $periode = trim((string) $periode);
        if (preg_match('/^\d{4}$/', $periode) !== 1) {
            return null;
        }
        if (!in_array($periode, HrPk::singleYearPeriodes(), true)) {
            return null;
        }

        return PmsEmployeeSnapshot::normalizePeriode($periode);
    }

    private function periodeMessage(): string
    {
        $periodeList = HrPk::singleYearPeriodes();

        return 'Periode Penilaian wajib dipilih dari periode PK yang tersedia: ' . (implode(', ', $periodeList) ?: '(belum ada)') . '.';
    }

    private function uploaderLabel(string $sendBy): ?string
    {
        $sendBy = trim($sendBy);
        if ($sendBy === '') {
            return null;
        }

        $user = DB::table('users')
            ->where('username', $sendBy)
            ->orWhere('nik', $sendBy)
            ->first(['nik', 'name']);

        if (!$user) {
            return $sendBy;
        }

        if ($user->nik && $user->name) {
            return $user->nik . ' - ' . $user->name;
        }

        return $user->name ?? $sendBy;
    }
}
