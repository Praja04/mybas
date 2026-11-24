<?php

namespace App\Jobs\HR;

use App\Mail\HR\Pk\NotifikasiPenilaianKarya;
use App\Support\PmsEmployeeSnapshot;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendPkMasterNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 1;
    public $timeout = 900;

    protected $periode;

    public function __construct($periode)
    {
        $this->periode = (string) $periode;
    }

    public function handle()
    {
        try {
            [$recipients, $skipped, $excluded23] = $this->resolveRecipients();

            $sent = 0;
            $failed = 0;

            foreach ($recipients as $nik => $recipient) {
                try {
                    Mail::mailer('smtp')
                        ->to($recipient['email'], $recipient['nama'])
                        ->send(new NotifikasiPenilaianKarya($this->periode, $recipient['nama']));
                    $sent++;
                } catch (\Throwable $e) {
                    $failed++;
                    Log::warning('PK notification email failed', [
                        'nik' => $nik,
                        'email' => $recipient['email'],
                        'periode' => $this->periode,
                        'error' => $e->getMessage(),
                    ]);
                }
            }

            Log::info('PK notification emails processed', [
                'periode' => $this->periode,
                'total' => count($recipients),
                'sent' => $sent,
                'failed' => $failed,
                'skipped' => $skipped,
                'excluded_2_3' => $excluded23,
            ]);
        } catch (\Throwable $e) {
            Log::error('PK notification email job failed', [
                'periode' => $this->periode,
                'error' => $e->getMessage(),
            ]);
        }
    }

    private function resolveRecipients(): array
    {
        $periode = PmsEmployeeSnapshot::normalizePeriode($this->periode);

        $niks = [];
        $snapshotNama = [];
        $excluded23 = 0;
        $snapshotRowsTotal = 0;
        if ($periode !== '') {
            $snapshotRows = DB::table('hr_pms_employee_snapshot')
                ->where('periode', $periode)
                ->get(['nik', 'nama', 'level', 'level_group']);
            $snapshotRowsTotal = $snapshotRows->count();
            foreach ($snapshotRows as $row) {
                $key = strtoupper(trim((string) $row->nik));
                if ($key === '') {
                    continue;
                }
                $group = trim((string) $row->level_group) !== ''
                    ? trim((string) $row->level_group)
                    : PmsEmployeeSnapshot::groupOf($row->level);
                if ($group === '2-3') {
                    $excluded23++;
                    continue;
                }
                $niks[$key] = true;
                if (!isset($snapshotNama[$key]) || trim((string) $snapshotNama[$key]) === '') {
                    $snapshotNama[$key] = trim((string) $row->nama);
                }
            }
        }

        if (empty($niks) && $snapshotRowsTotal === 0) {
            $liveRows = DB::table('hr_master_employee')
                ->where('Aktif', 'Y')
                ->whereRaw('CAST(Level AS UNSIGNED) BETWEEN 2 AND 7')
                ->pluck('NIK');
            foreach ($liveRows as $nik) {
                $key = strtoupper(trim((string) $nik));
                if ($key !== '') {
                    $niks[$key] = true;
                }
            }

            $otoRows = DB::table('hr_pk_otorisasi')->pluck('nik_yang_bersangkutan');
            foreach ($otoRows as $nik) {
                $key = strtoupper(trim((string) $nik));
                if ($key !== '') {
                    $niks[$key] = true;
                }
            }
        }

        if (empty($niks)) {
            return [[], 0, $excluded23];
        }

        $masterRows = DB::table('hr_master_employee')
            ->whereIn('NIK', array_keys($niks))
            ->get(['NIK', 'Nama'])
            ->keyBy(fn ($r) => strtoupper(trim((string) $r->NIK)));

        $userRows = DB::table('users')
            ->whereIn('nik', array_keys($niks))
            ->get(['nik', 'name'])
            ->keyBy(fn ($r) => strtoupper(trim((string) $r->nik)));

        $emails = \App\Support\PmsEmailResolver::mapByNik(array_keys($niks));

        $recipients = [];
        $skippedNiks = [];
        foreach (array_keys($niks) as $key) {
            $master = $masterRows->get($key);
            $user = $userRows->get($key);

            $email = trim((string) ($emails[$key] ?? ''));
            if ($email === '' || filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
                $skippedNiks[] = $key;
                continue;
            }

            $nama = trim((string) ($master->Nama ?? ''));
            if ($nama === '') {
                $nama = trim((string) ($snapshotNama[$key] ?? ''));
            }
            if ($nama === '') {
                $nama = trim((string) ($user->name ?? ''));
            }

            $recipients[$key] = ['nama' => $nama, 'email' => $email];
        }

        if (!empty($skippedNiks)) {
            Log::warning('PK notification recipients skipped (no valid email)', [
                'periode' => $this->periode,
                'niks' => $skippedNiks,
            ]);
        }

        return [$recipients, count($skippedNiks), $excluded23];
    }
}
