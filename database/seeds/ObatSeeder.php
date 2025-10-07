<?php

use Illuminate\Database\Seeder;
use Carbon\Carbon;
use ilLuminate\support\Facades\DB;

class ObatSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $obats = [
            [
                'nama_obat' => 'SELANG OKSIGEN',
                'satuan' => '',
                'harga' => 6.600,
                'active' => 'Y',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'nama_obat' => 'HANSAPLAST PLESTER SEDANG',
                'satuan' => '',
                'harga' => 700,
                'active' => 'Y',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'nama_obat' => 'HANSAPLAST PLESTER KECIL',
                'satuan' => '',
                'harga' => 350,
                'active' => 'Y',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'nama_obat' => 'ERLAMYCETIN SALEP MATA',
                'satuan' => '',
                'harga' => 14.000,
                'active' => 'Y',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'nama_obat' => 'NACL 0,9% 500 ML',
                'satuan' => '',
                'harga' => 7.150,
                'active' => 'Y',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'nama_obat' => 'RANITIDIN 150 MG',
                'satuan' => '',
                'harga' => 350,
                'active' => 'Y',
                'created_at' => Carbon::now(),
                'upadted_at' => Carbon::now(),
            ],
            [
                'nama_obat' => 'SCOPMA',
                'satuan' => '',
                'harga' => 1.650,
                'active' => 'Y',
                'created_at' => Carbon::now(),
                'upadted_at' => Carbon::now(),
            ],
        ];

        DB::table('klinik_obat')->insert($obats);
    }
}
