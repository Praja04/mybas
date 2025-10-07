<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Kreait\Laravel\Firebase\Facades\Firebase;

class SigraCheckUploadedCertificate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sigra:check-uploaded-certificate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check if the data alredy uploaded to firebase by vendor';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('--------------------------');
        $this->info('-> Memulai proses');

        $this->info('Mengambil data tiket dari firestore..');
        $firestore = Firebase::project('firebase_purchasing_certificate_upload')->firestore()->database();  
        $tickets = $firestore->collection('tickets')
        ->where('is_certificate_uploaded', '==', true)
        ->where('is_received', '==', false)
        ->documents();

        $this->info('Mengambil data tiket dari firestore selesai..');
        $this->info('Ditemukan '.$tickets->size().' tiket');

        foreach($tickets as $ticket) {
            $this->info('--------------------------');
            $this->info('-> Memproses tiket '.$ticket->id());
            $this->info('-> Mengambil data tiket dari firestore..');
            $ticketData = $ticket->data();
            $this->info('-> Mengambil data tiket dari firestore selesai..');
            // $this->info('-> Data tiket: '.json_encode($ticketData));
            // $this->info('Url: '.$ticketData['certificate_url']);

            // Save image to local storage
            $file_url = $ticketData['certificate_url'];
            $file_array = explode('?alt=media', $file_url);
            $file_array2 = explode('.appspot.com/o/certificate%2', $file_array[0]);
            $file_name = $file_array2[1];
            // $file_name = str_replace('%20', '_', $file_name);
            // $file_name = str_replace("'", '', $file_name);
            $file_name_array = explode('.', $file_name);
            $file_extension = end($file_name_array);

            $nomor_tiket = $ticketData['ticket_number'];

            $ticket = DB::table('sigra_sertifikat_sh_bahan_baku_expired_tiket')
            ->where('nomor_tiket', $nomor_tiket)
            ->first();

            $file_name = time() . '-' . $ticket->plant . '-' . $ticket->vendor_code . '-' . $ticket->nama_bahan . '-' . $ticket->jenis . '-' . $ticket->tanggal_expired . '.' . $file_extension;

            $this->info('-> Menyimpan foto ke local storage..');
            $file = file_get_contents($file_url);
            file_put_contents(storage_path('app/public/purchasing/sigra-expired-ticket/' . $file_name), $file);
            $this->info('-> Menyimpan foto ke local storage selesai..');


            $this->info('-> Update status tiket..');
            DB::table('sigra_sertifikat_sh_bahan_baku_expired_tiket')
            ->where('nomor_tiket', $nomor_tiket)
            ->update([
                'dokumen' => $file_name,
                'status' => '2',
                'solved_by' => 'Vendor',
                'solved_at' => date('Y-m-d H:i:s')
            ]);

            $firestore->collection('tickets')->document($nomor_tiket)->update([
                [ 'path' => 'is_received', 'value' => true]
            ]);

            // dd('testing');
        }

        // Write log
        \File::append(base_path() . '/storage/logs/sigra/check-uploaded-certificate.log', "-> " . date('Y-m-d H:i:s') . " :: Check Uploaded Certificate succeed. There is " . $tickets->size() . " checked \n");

        return 0;
    }
}
