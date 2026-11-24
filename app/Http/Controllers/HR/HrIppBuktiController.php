<?php

namespace App\Http\Controllers\HR;

use App\Http\Controllers\Controller;
use App\Models\HR\HrIpp;
use App\Models\HR\HrIppAktual;
use App\Models\HR\HrIppBukti;
use App\Models\HR\HrIppOtorisasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class HrIppBuktiController extends Controller
{
    private const ALLOWED_MIMES = 'pdf,jpg,jpeg,png,doc,docx,xls,xlsx,xlsb,csv';
    private const CSV_TEXT_MIMES = ['text/plain', 'text/csv', 'application/csv', 'application/vnd.ms-excel'];
    private const PERMISSION_ADMIN = 'hr_upload_file_template_ipp';

    private function currentNik(): ?string
    {
        $nik = Auth::user()->nik ?? null;
        $nik = $nik !== null ? trim((string) $nik) : '';
        return $nik !== '' ? $nik : null;
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

    private function isAdmin(): bool
    {
        $user = Auth::user();
        if (!$user || !method_exists($user, 'getAllPermissionCodenames')) {
            return false;
        }
        return in_array(self::PERMISSION_ADMIN, (array) $user->getAllPermissionCodenames(), true);
    }

    /**
     * Dokumen pendukung hanya boleh diunggah oleh yang bersangkutan (ybs).
     */
    private function authorizeUpload(HrIppOtorisasi $oto): void
    {
        $nik = $this->currentNik();
        if (!$nik || $oto->nik_yang_bersangkutan !== $nik) {
            abort(403);
        }
    }

    /**
     * Dokumen pendukung dapat dilihat oleh: ybs, manager, associate manager, atau admin HR.
     */
    private function authorizeView(HrIppOtorisasi $oto): void
    {
        if ($this->isAdmin()) {
            return;
        }

        $nik = $this->currentNik();
        if (!$nik) {
            abort(403);
        }

        $related = $oto->nik_yang_bersangkutan === $nik
            || $oto->nik_manager === $nik
            || $oto->nik_associate_manager === $nik;

        if (!$related) {
            abort(403);
        }
    }

    /**
     * Tahapan terkunci jika ada baris aktual pada oto+tahapan tsb yang sudah di-approve/reject.
     */
    private function tahapanProcessed(int $otoId, string $tahapan): bool
    {
        return HrIppAktual::where('ipp_otorisasi_id', $otoId)
            ->where('tahapan', $tahapan)
            ->where(function ($q) {
                $q->whereNotNull('approval_associate_manager')
                    ->orWhereNotNull('approval_manager');
            })
            ->exists();
    }

    public function upload(Request $request)
    {
        $validated = $request->validate([
            'ipp_otorisasi_id' => 'required|integer',
            'ipp_detail_id' => 'required|integer',
            'tahapan' => 'required|string|max:20',
            'files' => 'required|array|min:1|max:20',
            'files.*' => [
                'required',
                'file',
                function ($attribute, $value, $fail) {
                    $allowed = explode(',', self::ALLOWED_MIMES);
                    $guessed = strtolower((string) $value->guessExtension());
                    if ($guessed !== '' && in_array($guessed, $allowed, true)) {
                        return;
                    }
                    $extension = strtolower((string) ($value->getClientOriginalExtension() ?: $value->extension()));
                    $mime = strtolower((string) $value->getMimeType());
                    if ($extension === 'csv' && in_array($mime, self::CSV_TEXT_MIMES, true)) {
                        return;
                    }
                    $fail('Dokumen pendukung harus berupa PDF, JPG, PNG, DOC, DOCX, XLS, XLSX, XLSB, atau CSV.');
                },
            ],
        ]);

        $oto = HrIppOtorisasi::findOrFail((int) $validated['ipp_otorisasi_id']);
        $this->authorizeUpload($oto);

        $detailId = (int) $validated['ipp_detail_id'];
        $tahapan = $validated['tahapan'];

        $ipp = HrIpp::with('details')->findOrFail($oto->ipp_id);
        if (!$ipp->details->pluck('id')->contains($detailId)) {
            abort(422, 'Detail ID bukan bagian dari IPP ini.');
        }
        if (!in_array($tahapan, $this->buildTahapan($ipp->periode), true)) {
            abort(422, 'Tahapan tidak dikenali.');
        }
        if ($this->tahapanProcessed($oto->id, $tahapan)) {
            abort(422, 'Tahapan ini sudah disetujui/ditolak, dokumen pendukung tidak dapat diunggah.');
        }

        $disk = Storage::disk('ipp_private');
        $directory = 'ipp/bukti/' . $oto->id;
        $nik = $this->currentNik();

        $stored = [];
        foreach ($request->file('files') as $file) {
            if (!$file->isValid()) {
                continue;
            }

            $extension = strtolower($file->getClientOriginalExtension() ?: $file->extension());
            $filename = 'bukti-' . date('YmdHis') . '-' . uniqid() . '.' . $extension;
            $path = $disk->putFileAs($directory, $file, $filename);
            if ($path === false) {
                continue;
            }

            $stored[] = HrIppBukti::create([
                'ipp_otorisasi_id' => $oto->id,
                'ipp_detail_id' => $detailId,
                'tahapan' => $tahapan,
                'file_path' => $path,
                'original_name' => $file->getClientOriginalName(),
                'mime_type' => substr((string) $file->getMimeType(), 0, 100),
                'file_size' => $file->getSize(),
                'uploaded_by' => $nik,
            ]);
        }

        if (empty($stored)) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak ada file yang berhasil disimpan.',
            ], 422);
        }

        return response()->json([
            'success' => true,
                'message' => count($stored) . ' dokumen pendukung berhasil diunggah.',
            'data' => collect($stored)->map(fn ($b) => $this->payloadFor($b))->all(),
        ]);
    }

    public function list(Request $request)
    {
        $request->validate([
            'ipp_otorisasi_id' => 'required|integer',
            'ipp_detail_id' => 'nullable|integer',
            'tahapan' => 'nullable|string|max:20',
        ]);

        $oto = HrIppOtorisasi::findOrFail((int) $request->get('ipp_otorisasi_id'));
        $this->authorizeView($oto);

        $query = HrIppBukti::where('ipp_otorisasi_id', $oto->id);
        if ($request->filled('ipp_detail_id')) {
            $query->where('ipp_detail_id', (int) $request->get('ipp_detail_id'));
        }
        if ($request->filled('tahapan')) {
            $query->where('tahapan', $request->get('tahapan'));
        }

        $rows = $query->orderBy('created_at')->orderBy('id')->get();

        $niks = $rows->pluck('uploaded_by')->filter()->unique()->values();
        $nameMap = $niks->isEmpty()
            ? []
            : DB::table('users')->whereIn('nik', $niks->all())->pluck('name', 'nik')->all();

        $processedCache = [];
        $unlockedFor = function (string $tahapan) use (&$processedCache, $oto): bool {
            if (!array_key_exists($tahapan, $processedCache)) {
                $processedCache[$tahapan] = !$this->tahapanProcessed($oto->id, $tahapan);
            }
            return $processedCache[$tahapan];
        };
        $myNik = $this->currentNik();

        return response()->json([
            'success' => true,
            'data' => $rows->map(function (HrIppBukti $b) use ($nameMap, $myNik, $unlockedFor) {
                $payload = $this->payloadFor($b);
                $payload['uploaded_by_name'] = $nameMap[$b->uploaded_by] ?? ($b->uploaded_by ?? '-');
                $payload['can_delete'] = $unlockedFor($b->tahapan) && $myNik !== null && $b->uploaded_by === $myNik;
                return $payload;
            })->all(),
        ]);
    }

    public function show($id)
    {
        [$bukti, $absolutePath] = $this->resolveAccessibleFile((int) $id);

        $inlineTypes = ['application/pdf', 'image/jpeg', 'image/png', 'image/jpg'];

        if (in_array($bukti->mime_type, $inlineTypes, true)) {
            return response()->file($absolutePath, [
                'Content-Type' => $bukti->mime_type ?: 'application/octet-stream',
            ]);
        }

        return response()->download($absolutePath, $bukti->original_name, [
            'Content-Type' => $bukti->mime_type ?: 'application/octet-stream',
        ]);
    }

    public function download($id)
    {
        [$bukti, $absolutePath] = $this->resolveAccessibleFile((int) $id);

        return response()->download($absolutePath, $bukti->original_name, [
            'Content-Type' => $bukti->mime_type ?: 'application/octet-stream',
        ]);
    }

    public function delete($id)
    {
        $bukti = HrIppBukti::with('otorisasi')->findOrFail((int) $id);

        $nik = $this->currentNik();
        if (!$nik || $bukti->uploaded_by !== $nik) {
            abort(403);
        }
        if ($bukti->otorisasi && $this->tahapanProcessed($bukti->ipp_otorisasi_id, $bukti->tahapan)) {
            abort(422, 'Tahapan ini sudah disetujui/ditolak, dokumen pendukung tidak dapat dihapus.');
        }

        Storage::disk('ipp_private')->delete($bukti->file_path);
        $bukti->delete();

        return response()->json([
            'success' => true,
            'message' => 'Dokumen pendukung berhasil dihapus.',
        ]);
    }

    private function resolveAccessibleFile(int $id): array
    {
        $bukti = HrIppBukti::with('otorisasi')->findOrFail($id);

        if (!$bukti->otorisasi) {
            abort(404);
        }
        $this->authorizeView($bukti->otorisasi);

        $disk = Storage::disk('ipp_private');
        if (!$disk->exists($bukti->file_path)) {
            abort(404);
        }

        return [$bukti, $disk->path($bukti->file_path)];
    }

    private function payloadFor(HrIppBukti $b): array
    {
        return [
            'id' => $b->id,
            'ipp_otorisasi_id' => $b->ipp_otorisasi_id,
            'ipp_detail_id' => $b->ipp_detail_id,
            'tahapan' => $b->tahapan,
            'original_name' => $b->original_name,
            'mime_type' => $b->mime_type,
            'file_size' => $b->file_size,
            'uploaded_by' => $b->uploaded_by,
            'created_at' => optional($b->created_at)->format('d M Y H:i'),
            'view_url' => url('/hr/ipp-bukti/' . $b->id),
            'download_url' => url('/hr/ipp-bukti/' . $b->id . '/download'),
        ];
    }
}
