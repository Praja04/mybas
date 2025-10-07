<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Mail\Sigra\SHBahanBaku;
use App\Models\Sigra\SertifikatSHBahanBaku;

class SigraCheckSHBahanBaku extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sigra:check-sh-bahan-baku';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Untuk cek status expired dari sh bahan baku';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    function expired($expired_date) {
        return (strtotime($expired_date) - strtotime(date('Y-m-d'))) / 86400;
    }

    function sendEmail($emails, $sertifikat)
    {
        foreach($emails as $email)
        {
            Mail::to($email->email_penerima)->send(new SHBahanBaku($sertifikat));
        }
    }

    private function generateID($prefix, $tableName, $columnName)
    {
        $prefixLength = strlen($prefix);

        $numberBefore = DB::table($tableName)
        ->selectRaw('SUBSTR('.$columnName.', '.($prefixLength+1).') as code')
        ->orderByRaw('CAST(SUBSTR('.$columnName.', '.($prefixLength+1).') AS SIGNED) desc')
        ->whereRaw('SUBSTR('.$columnName.',1, '.($prefixLength).') = \''.$prefix.'\'')
        ->first();

        if($numberBefore == null) {
            return $prefix.'00001';
        }

        $currentNumber = (int)$numberBefore->code+1;
        
        switch ($currentNumber) {
            case $currentNumber < 10:
                $currentCode = $prefix.'0000'.$currentNumber;
                break;
            case $currentNumber < 100:
                $currentCode = $prefix.'000'.$currentNumber;
                break;
            case $currentNumber < 1000:
                $currentCode = $prefix.'00'.$currentNumber;
                break;
            case $currentNumber < 10000:
                $currentCode = $prefix.'0'.$currentNumber;
                break;
            default:
                $currentCode = $prefix.$currentNumber;
                break;
        }

        return $currentCode;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $sertifikat_sh = SertifikatSHBahanBaku::where('archive', 'N')
        ->where('status', 'active')
        // ->whereNotNull('tanggal_expired')
        ->get();

        // Get an emails
        // $emails = DB::table('sigra_email_penerima')
        // ->where('jenis', 'sh_bahan_baku')->get();

        foreach($sertifikat_sh as $sertifikat)
        {
            if($sertifikat->vendor_code == null)
            {
                $this->info('-------------------------');
                $this->info('!!!!! Bahan baku '. $sertifikat->bahan_baku. ' di skip, ga ada vendor_code ');
                $this->info('-------------------------');
                continue;
            }

            if($sertifikat->tanggal_expired_halal !=  null) {
                $this->expiredTicket($sertifikat, 'halal', 'Halal', $sertifikat->tanggal_expired_halal);
            }

            if($sertifikat->tanggal_expired_msds !=  null) {
                $this->expiredTicket($sertifikat, 'msds', 'MSDS', $sertifikat->tanggal_expired_msds);
            }
            
            if($sertifikat->tanggal_expired_flowchart !=  null) {
                $this->expiredTicket($sertifikat, 'flowchart', 'Flowchart', $sertifikat->tanggal_expired_flowchart);
            }
        }
    }

    protected function expiredTicket($sertifikat, $jenis, $jenis_label, $tanggal_expired)
    {
        $this->info('-------------------------');
        $this->info('-> Checking expired ' . $jenis . ' for '.$sertifikat->vendor_code);
        $this->info('Document expired due date is '. $this->expired($tanggal_expired));
        // dd($sertifikat);
        if($this->expired($tanggal_expired) <= 60)
        {
            // Di sini dilakukan send notifikasi
            $this->info($jenis_label . ' nya Udah mau expired nih.. '.$sertifikat->nomor_sertifikat.' '. $tanggal_expired);
            $sertifikat->due_date = $this->expired($tanggal_expired);
            // Check if ticket already created
            $this->info('Cek dulu, tiket nya udah ada apa belum ');

            $ticket = DB::table('sigra_sertifikat_sh_bahan_baku_expired_tiket')
            ->where('nomor_sertifikat', $sertifikat->nomor_sertifikat)
            ->where('tanggal_expired', $tanggal_expired)
            ->where('jenis', $jenis)
            ->first();

            if($ticket != null){
                $this->info('Tiket nya udah ada, ga perlu buat tiket');
                $this->info('-------------------------');
                return;
            }

            $this->info('Tiket belum ada, buat tiket nya');
            // Create ticket
            DB::table('sigra_sertifikat_sh_bahan_baku_expired_tiket')->insert([
                'nomor_sertifikat' => $sertifikat->nomor_sertifikat,
                'nomor_tiket' => $this->generateID('ET'.date('Ym'), 'sigra_sertifikat_sh_bahan_baku_expired_tiket', 'nomor_tiket'),
                'tanggal_expired' => $tanggal_expired,
                'plant' => $sertifikat->perusahaan->nama_perusahaan,
                'nama_bahan' => $sertifikat->bahan_baku,
                'nomor_sh' => $sertifikat->no_sh,
                'produsen' => $sertifikat->produsen,
                'pemasok' => $sertifikat->pemasok,
                'lembaga' => $sertifikat->lembaga,
                'keterangan' => $sertifikat->keterangan,
                'vendor_code' => $sertifikat->vendor_code,
                'jenis' => $jenis,
                'status' => '1',
                'created_at' => date('Y-m-d H:i:s')
            ]);

            $this->info('Sipp.. Berhasil buat tiket..');
            $this->info('-------------------------');
        }
    }
}
