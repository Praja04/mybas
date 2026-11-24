<?php

namespace App\Support;

use App\Events\HR\IppDiskusiChanged;
use App\Models\HR\HrIpp;
use App\Models\HR\HrIppDiskusi;
use App\Models\HR\HrIppOtorisasi;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Broadcast realtime thread diskusi IPP (Diskusi - KPI).
 *
 * Dipakai IppAktualController (peserta bertanya/mengubah) dan
 * IppUserApprovalController (atasan menjawab/mengubah jawaban).
 * Payload viewer-agnostic; kanal privat per-NIK ditentukan di sini.
 */
class IppDiskusiBroadcast
{
    /**
     * Kirim event perubahan diskusi ke penerima yang relevan.
     *
     * @param string $type created|updated|answered
     */
    public static function send(string $type, HrIppDiskusi $d, bool $edited = false): void
    {
        try {
            $targets = static::targets($type, $d);
            if (!$targets) {
                return;
            }

            event(new IppDiskusiChanged($type, $targets, static::payload($type, $d, $edited)));
        } catch (\Throwable $e) {
            Log::error('IPP diskusi broadcast failed', [
                'diskusi_id' => $d->id,
                'type' => $type,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * NIK penerima:
     * - created/updated  : manager + associate manager + pengaju (aktor)
     * - answered         : peserta + atasan counterpart + penjawab (aktor)
     *
     * Aktor ikut dikirim agar badge/section di halamannya sendiri ikut
     * ter-update (toast di-skip client-side lewat aktor_nik).
     *
     * @return array<int, string>
     */
    private static function targets(string $type, HrIppDiskusi $d): array
    {
        $oto = HrIppOtorisasi::find($d->ipp_otorisasi_id);
        if (!$oto) {
            return [];
        }

        $peserta = trim((string) $oto->nik_yang_bersangkutan);
        $amNik = trim((string) $oto->nik_associate_manager);
        $managerNik = trim((string) $oto->nik_manager);

        $niks = [];

        if ($type === 'answered') {
            $penjawab = trim((string) $d->nik_penjawab);
            $niks[] = $peserta;
            if ($penjawab === $amNik) {
                $niks[] = $managerNik;
            } elseif ($penjawab === $managerNik) {
                $niks[] = $amNik;
            } else {
                $niks[] = $amNik;
                $niks[] = $managerNik;
            }
            $niks[] = $penjawab;
        } else {
            $niks[] = $amNik;
            $niks[] = $managerNik;
            $niks[] = trim((string) $d->nik_pengaju);
        }

        return array_values(array_unique(array_filter(array_map(function ($nik) {
            return trim((string) $nik);
        }, $niks))));
    }

    /**
     * @return array<string, mixed>
     */
    private static function payload(string $type, HrIppDiskusi $d, bool $edited): array
    {
        $oto = HrIppOtorisasi::find($d->ipp_otorisasi_id);
        $ipp = $oto ? HrIpp::find($oto->ipp_id) : null;

        $detailId = (int) ($d->ipp_detail_id ?? 0);
        $detail = $detailId > 0
            ? DB::table('hr_ipp_detail')->where('id', $detailId)->first(['no_urut', 'kpi'])
            : null;

        $counts = HrIppDiskusi::where('ipp_otorisasi_id', $d->ipp_otorisasi_id)
            ->where('tahapan', $d->tahapan)
            ->selectRaw("COUNT(*) as total, SUM(CASE WHEN status = 'open' THEN 1 ELSE 0 END) as open_total")
            ->first();

        $aktorNik = $type === 'answered'
            ? trim((string) $d->nik_penjawab)
            : trim((string) $d->nik_pengaju);

        return [
            'type' => $type,
            'edited' => $edited,
            'id' => (int) $d->id,
            'ipp_id' => $oto ? (int) $oto->ipp_id : null,
            'otorisasi_id' => (int) $d->ipp_otorisasi_id,
            'ipp_detail_id' => $detailId > 0 ? $detailId : null,
            'tahapan' => (string) ($d->tahapan ?? ''),
            'no_urut' => $detail ? $detail->no_urut : null,
            'kpi' => $detail ? (string) $detail->kpi : '',
            'periode' => $ipp ? (string) $ipp->periode : '',
            'nik_yang_bersangkutan' => $oto ? (string) $oto->nik_yang_bersangkutan : '',
            'nama_yang_bersangkutan' => $oto ? (string) $oto->nama_yang_bersangkutan : '',
            'nik_pengaju' => (string) $d->nik_pengaju,
            'nama_pengaju' => (string) $d->nama_pengaju,
            'pesan' => (string) $d->pesan,
            'jawaban' => (string) ($d->jawaban ?? ''),
            'nik_penjawab' => (string) ($d->nik_penjawab ?? ''),
            'nama_penjawab' => (string) ($d->nama_penjawab ?? ''),
            'status' => (string) $d->status,
            'created_at' => optional($d->created_at)->format('d M Y H:i'),
            'answered_at' => optional($d->answered_at)->format('d M Y H:i'),
            'open_count' => (int) ($counts->open_total ?? 0),
            'total_count' => (int) ($counts->total ?? 0),
            'aktor_nik' => $aktorNik,
        ];
    }
}
