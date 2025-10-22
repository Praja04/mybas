<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Models\Sigra\Operasional;
use App\Models\Sigra\SertifikasiOperasional;
use App\Mail\Sigra\Operasional as EmailOperasional;
use Illuminate\Support\Facades\Log;

class SigraCheckOperasional extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sigra:check-operasional';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Untuk cek status expired dari operasional';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    function expired($expired_date)
    {
        return (strtotime($expired_date) - strtotime(date('Y-m-d'))) / 86400;
    }

    function sendEmail($sertifikat)
    {
        $emails = DB::table('sigra_email_penerima')
            ->where('jenis', 'operasional')
            ->where('active', 'Y')
            ->get();

        if ($emails->isEmpty()) {
            Log::warning('Tidak ada penerima email untuk jenis "operasional".');
            return;
        }

        foreach ($emails as $email) {
            // Mail::mailer(setEmail($email->email_penerima))->to($email->email_penerima)->send(new EmailOperasional($sertifikat));
            Mail::to($email->email_penerima)->send(new EmailOperasional($sertifikat));
        }
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $certificates = [];

        $operasional = Operasional::where('status', '!=', 'deleted')
            ->where('status', '!=', 'inactive')->get();

        foreach ($operasional as $key => $data) {
            $sertifikasi = SertifikasiOperasional::where('id_operasional', $data->id)->where('status', '!=', 'deleted')->orderBy('tahun', 'desc')->first();

            // if ($sertifikasi != null) {
            //     if ($this->expired($sertifikasi->tanggal_expired) <= 30) {

            if ($sertifikasi != null) {
                $selisih_hari = $this->expired($sertifikasi->tanggal_expired);
                // buat kondisi kurang dari 45 hari dan tidak melewati dari 60 hari
                if ($selisih_hari <= 45 && $selisih_hari >= -60) {
                    // Di sini dilakukan send notifikasi
                    $sertifikasi->perusahaan = $data->perusahaan->nama_perusahaan;
                    $sertifikasi->nama_perizinan = $data->nama_perizinan;
                    $sertifikasi->nomor_perizinan = $data->nomor_perizinan;
                    $sertifikasi->due_date = $this->expired($sertifikasi->tanggal_expired);

                    if ($selisih_hari < 0) {
                        $this->info("[SIGRA Operasional] {$sertifikasi->nama_perizinan} sudah berakhir pada {$sertifikasi->tanggal_expired} (melewati " . abs($selisih_hari) . " hari)");
                    } else {
                        $this->info("[SIGRA Operasional] {$sertifikasi->nama_perizinan} akan berakhir pada {$sertifikasi->tanggal_expired} (dalam {$selisih_hari} hari)");
                    }

                    $certificates[] = $sertifikasi;
                }
            }
        }

        if (!empty($certificates)) {
            $this->sendEmail($certificates);
            $this->info('Email notifikasi pengingat sertifikasi operasional telah dikirim.');
        } else {
            $this->info('Tidak ada sertifikasi operasional yang akan atau sudah expired. Tidak ada email dikirim.');
        }
    }
}
