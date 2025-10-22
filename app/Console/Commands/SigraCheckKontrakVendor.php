<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Models\Sigra\KontrakVendor;
use App\Models\Sigra\MasterVendor;
use App\Mail\Sigra\KontrakVendor as EmailKontrakVendor;
use Illuminate\Support\Facades\Log;

class SigraCheckKontrakVendor extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sigra:check-kontrak-vendor';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Untuk cek status expired dari kontrak vendor';

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
            ->where('jenis', 'kontrak_vendor')
            ->where('active', 'Y')
            ->get();

        if ($emails->isEmpty()) {
            Log::warning('Tidak ada penerima email untuk jenis "kontrak_vendor".');
            return;
        }

        foreach ($emails as $email) {
            Mail::to($email->email_penerima)->send(new EmailKontrakVendor($sertifikat));
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

        $kontrak_vendor = MasterVendor::where('status', '!=', 'deleted')
            ->where('status', '!=', 'inactive')->get();

        foreach ($kontrak_vendor as $key => $data) {
            $sertifikasi = KontrakVendor::where('id_vendor', $data->id)
                ->where('status', '!=', 'deleted')
                ->orderBy('tanggal_selesai', 'desc')
                ->first();


            if ($sertifikasi != null) {
                $selisih_hari = $this->expired($sertifikasi->tanggal_selesai);
                if ($selisih_hari <= 45 && $selisih_hari >= -60) {
                    // Kirim email hanya jika selisih kurang dari atau sama dengan 45 hari dan expired belum lewat dari 60 hari
                    $sertifikasi->perusahaan = $data->perusahaan->nama_perusahaan;
                    $sertifikasi->nama_vendor = $data->nama_vendor;
                    $sertifikasi->jenis_pekerjaan = $data->jenis_pekerjaan;
                    $sertifikasi->due_date = $selisih_hari;

                    if ($selisih_hari < 0) {
                        $this->info("[SIGRA Kontrak Vendor] {$sertifikasi->nama_vendor} ({$sertifikasi->jenis_pekerjaan}) sudah berakhir pada {$sertifikasi->tanggal_selesai} (melewati " . abs($selisih_hari) . " hari)");
                    } else {
                        $this->info("[SIGRA Kontrak Vendor] {$sertifikasi->nama_vendor} ({$sertifikasi->jenis_pekerjaan}) akan berakhir pada {$sertifikasi->tanggal_selesai} (dalam {$selisih_hari} hari)");
                    }

                    $certificates[] = $sertifikasi;
                }
            }
        }

        if (!empty($certificates)) {
            $this->sendEmail($certificates);
            $this->info('Email notifikasi pengingat kontrak vendor telah dikirim.');
        } else {
            $this->info('Tidak ada kontrak vendor yang akan atau sudah expired. Tidak ada email dikirim.');
        }
    }
}
