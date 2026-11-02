<?php

use Illuminate\Database\Seeder;
use App\Models\PoolQCC\PoolBooth;
use App\Models\PoolQCC\PoolQuestion;

class PoolQccSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // 1. Booth Produksi & Kaizen
        $booth1 = PoolBooth::firstOrCreate(
            ['code' => 'B01'],
            [
                'name' => 'Circle Kaizen Line Produksi',
                'category' => 'Produksi',
                'description' => 'Inovasi reduksi reject dan peningkatan throughput mesin packing melalui modifikasi sensor inner.',
                'location' => 'Area Hall A - No. 01',
                'pic_name' => 'Budi Santoso',
                'pic_contact' => '081234567801',
                'is_active' => true,
            ]
        );

        $questionsB1 = [
            [
                'question_text' => 'Apa kepanjangan dan tujuan utama dari siklus PDCA dalam aktivitas QCC?',
                'option_a' => 'Plan, Do, Check, Act untuk perbaikan berkelanjutan',
                'option_b' => 'Process, Delivery, Control, Action',
                'option_c' => 'Produce, Distribute, Check, Analyze',
                'option_d' => 'Program, Develop, Count, Assess',
                'correct_answer' => 'A',
                'points' => 10,
                'explanation' => 'PDCA (Plan, Do, Check, Act) adalah model manajemen berulang untuk continuous improvement.',
            ],
            [
                'question_text' => 'Manakah dari berikut ini yang BUKAN termasuk dalam 7 Pemborosan (7 Wastes / Muda)?',
                'option_a' => 'Overproduction (Produksi Berlebih)',
                'option_b' => 'Waiting (Waktu Menunggu)',
                'option_c' => 'High Quality (Kualitas Terlalu Tinggi)',
                'option_d' => 'Defects (Produk Cacat)',
                'correct_answer' => 'C',
                'points' => 10,
                'explanation' => '7 Wastes adalah TIMWOOD: Transport, Inventory, Motion, Waiting, Overproduction, Overprocessing, Defects.',
            ],
            [
                'question_text' => 'Alat bantu kualitas (QC 7 Tools) yang digunakan untuk mengidentifikasi 80% masalah dari 20% penyebab adalah?',
                'option_a' => 'Diagram Ishikawa (Fishbone)',
                'option_b' => 'Diagram Pareto',
                'option_c' => 'Histogram',
                'option_d' => 'Control Chart',
                'correct_answer' => 'B',
                'points' => 10,
                'explanation' => 'Diagram Pareto berlandaskan prinsip aturan 80/20 untuk menentukan prioritas perbaikan.',
            ],
            [
                'question_text' => 'Dalam metode 5 Mengapa (5 Why Analysis), apa tujuan utamanya?',
                'option_a' => 'Mencari siapa yang bersalah atas kesalahan',
                'option_b' => 'Menemukan akar penyebab utama (root cause) dari suatu masalah',
                'option_c' => 'Membuat laporan yang panjang kepada atasan',
                'option_d' => 'Menghitung kerugian finansial per jam',
                'correct_answer' => 'B',
                'points' => 10,
                'explanation' => '5 Why bertujuan menelusuri rantai sebab-akibat hingga ke akar penyebab terdalam.',
            ],
            [
                'question_text' => 'Apa manfaat utama dari penerapan Poka-Yoke di lini packing produksi?',
                'option_a' => 'Mencegah terjadinya kesalahan manusia (mistake-proofing)',
                'option_b' => 'Mempercepat kecepatan mesin hingga batas maksimal',
                'option_c' => 'Mengurangi jumlah operator menjadi nol',
                'option_d' => 'Menambah jam lembur karyawan',
                'correct_answer' => 'A',
                'points' => 10,
                'explanation' => 'Poka-Yoke adalah mekanisme pencegahan kesalahan (mistake-proofing) secara otomatis.',
            ],
        ];

        foreach ($questionsB1 as $q) {
            PoolQuestion::firstOrCreate(
                ['booth_id' => $booth1->id, 'question_text' => $q['question_text']],
                array_merge($q, ['booth_id' => $booth1->id, 'is_active' => true])
            );
        }

        // 2. Booth Maintenance & Utility
        $booth2 = PoolBooth::firstOrCreate(
            ['code' => 'B02'],
            [
                'name' => 'Circle Maintenance Power & Boiler',
                'category' => 'Utility / Maintenance',
                'description' => 'Optimasi konsumsi bahan bakar boiler dan digital monitoring PLC tekanan uap bertekanan.',
                'location' => 'Area Hall A - No. 02',
                'pic_name' => 'Agus Prayitno',
                'pic_contact' => '081234567802',
                'is_active' => true,
            ]
        );

        $questionsB2 = [
            [
                'question_text' => 'Apa kepanjangan dari TPM dalam strategi pemeliharaan mesin?',
                'option_a' => 'Total Productive Maintenance',
                'option_b' => 'Temporary Production Method',
                'option_c' => 'Time Process Management',
                'option_d' => 'Technical Plant Monitoring',
                'correct_answer' => 'A',
                'points' => 10,
                'explanation' => 'Total Productive Maintenance adalah pendekatan terintegrasi untuk memaksimalkan efektivitas peralatan.',
            ],
            [
                'question_text' => 'Pilar TPM yang menitikberatkan pada perawatan mandiri oleh operator mesin disebut?',
                'option_a' => 'Autonomous Maintenance (Jishu Hozen)',
                'option_b' => 'Planned Maintenance',
                'option_c' => 'Quality Maintenance',
                'option_d' => 'Office TPM',
                'correct_answer' => 'A',
                'points' => 10,
                'explanation' => 'Jishu Hozen melatih operator merawat, melumasi, membersihkan, dan menginspeksi mesin sendiri.',
            ],
            [
                'question_text' => 'Indikator kunci OEE (Overall Equipment Effectiveness) dihitung dari perkalian tiga faktor utama yaitu?',
                'option_a' => 'Cost, Delivery, Safety',
                'option_b' => 'Availability, Performance, Quality',
                'option_c' => 'Speed, Pressure, Temperature',
                'option_d' => 'Power, Downtime, Output',
                'correct_answer' => 'B',
                'points' => 10,
                'explanation' => 'OEE = Availability Rate x Performance Rate x Quality Rate.',
            ],
            [
                'question_text' => 'Apa fungsi utama dari steam trap pada sistem distribusi uap boiler?',
                'option_a' => 'Membuang kondensat air tanpa membuang uap steam berharga',
                'option_b' => 'Mendinginkan uap menjadi air dingin secepatnya',
                'option_c' => 'Mengatur arah aliran angin kompresor',
                'option_d' => 'Menyaring kotoran oli motor penggerak',
                'correct_answer' => 'A',
                'points' => 10,
                'explanation' => 'Steam trap berfungsi membuang kondensat dan udara secara otomatis tanpa membiarkan uap live keluar.',
            ],
            [
                'question_text' => 'Preventive Maintenance bertujuan untuk:',
                'option_a' => 'Memperbaiki mesin hanya setelah mesin benar-benar rusak total',
                'option_b' => 'Mencegah kerusakan mendadak melalui inspeksi dan penggantian terjadwal',
                'option_c' => 'Menghilangkan kebutuhan suku cadang di gudang',
                'option_d' => 'Menambah waktu jeda breakdown',
                'correct_answer' => 'B',
                'points' => 10,
                'explanation' => 'Preventive maintenance adalah pemeliharaan terencana berkala untuk mencegah kerusakan fatal.',
            ],
        ];

        foreach ($questionsB2 as $q) {
            PoolQuestion::firstOrCreate(
                ['booth_id' => $booth2->id, 'question_text' => $q['question_text']],
                array_merge($q, ['booth_id' => $booth2->id, 'is_active' => true])
            );
        }

        // 3. Booth QA & Safety (SHE)
        $booth3 = PoolBooth::firstOrCreate(
            ['code' => 'B03'],
            [
                'name' => 'Circle Zero Defect QA & Food Safety',
                'category' => 'Quality Assurance',
                'description' => 'Sistem monitoring kontaminasi fisik dan verifikasi CCP otomatis berbasis QR dan sensor berat.',
                'location' => 'Area Hall B - No. 01',
                'pic_name' => 'Dewi Rahmawati',
                'pic_contact' => '081234567803',
                'is_active' => true,
            ]
        );

        $questionsB3 = [
            [
                'question_text' => 'Apa kepanjangan dari HACCP dalam standar mutu pangan?',
                'option_a' => 'Hazard Analysis and Critical Control Points',
                'option_b' => 'Health Assessment and Clean Cooking Process',
                'option_c' => 'Hygiene Assurance for Chemical and Physical Control',
                'option_d' => 'High Action for Clean Commodity Production',
                'correct_answer' => 'A',
                'points' => 10,
                'explanation' => 'HACCP adalah sistem manajemen keamanan pangan yang sistematis dan preventif.',
            ],
            [
                'question_text' => 'Apa langkah pertama (Ringkas / Seiri) dalam metode 5R di tempat kerja?',
                'option_a' => 'Memisahkan barang yang diperlukan dengan yang tidak diperlukan lalu menyingkirkannya',
                'option_b' => 'Membersihkan lantai dengan sapu dan pel setiap pagi',
                'option_c' => 'Membuat garis marka cat kuning di lantai',
                'option_d' => 'Mengecat dinding ruangan agar indah',
                'correct_answer' => 'A',
                'points' => 10,
                'explanation' => 'Ringkas (Seiri) adalah memilah dan membuang barang yang tidak terpakai dari area kerja.',
            ],
            [
                'question_text' => 'Berikut adalah bahaya keselamatan kerja kategori ergonomi, KECUALI:',
                'option_a' => 'Postur kerja membungkuk dalam durasi lama',
                'option_b' => 'Gerakan berulang (repetitive motion) tanpa jeda',
                'option_c' => 'Kebisingan melebihi 85 dB',
                'option_d' => 'Pengangkatan beban berat secara manual dengan posisi salah',
                'correct_answer' => 'C',
                'points' => 10,
                'explanation' => 'Kebisingan termasuk dalam kategori bahaya fisik (physical hazard), bukan ergonomi.',
            ],
            [
                'question_text' => 'Metode penataan barang agar mudah diambil dan dikembalikan ke tempat semula adalah prinsip 5R:',
                'option_a' => 'Rapi (Seiton)',
                'option_b' => 'Resik (Seiso)',
                'option_c' => 'Rawat (Seiketsu)',
                'option_d' => 'Rajin (Shitsuke)',
                'correct_answer' => 'A',
                'points' => 10,
                'explanation' => 'Rapi (Seiton) adalah menata barang sesuai tempatnya dengan pelabelan yang jelas.',
            ],
            [
                'question_text' => 'Dalam segitiga api (Fire Triangle), 3 unsur yang memicu kebakaran adalah:',
                'option_a' => 'Bahan bakar, Panas/Sumber Api, dan Oksigen',
                'option_b' => 'Air, Minyak, dan Asap',
                'option_c' => 'Listrik, Karbon, dan Debu',
                'option_d' => 'Kayu, Besi, dan Belerang',
                'correct_answer' => 'A',
                'points' => 10,
                'explanation' => 'Segitiga api terdiri atas Fuel (Bahan Bakar), Heat (Panas), dan Oxygen (Oksigen).',
            ],
        ];

        foreach ($questionsB3 as $q) {
            PoolQuestion::firstOrCreate(
                ['booth_id' => $booth3->id, 'question_text' => $q['question_text']],
                array_merge($q, ['booth_id' => $booth3->id, 'is_active' => true])
            );
        }
    }
}
