<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Email Khusus IR Staff (Hardcoded)
    |--------------------------------------------------------------------------
    | Alamat email IR Staff yang akan menerima tembusan / salinan SP resmi.
    | Anda dapat mengubah email ini langsung di file ini.
    */
    'ir_staff' => [
        'redyadhi.saputra@pt-pas-id.com',
        'gilang.amballo@pt-pas-id.com',
        'okianabudi.ashari@pt-pas-id.com',
        'desy.amalia@pt-pas-id.com',
    ],

    /*
    |--------------------------------------------------------------------------
    | Mapping Email Dept Head per Departemen (Hardcoded)
    |--------------------------------------------------------------------------
    | Sesuaikan alamat email masing-masing Kepala Departemen (Dept Head).
    | Key berupa kode divisi / departemen (misal: ENG, ITE, PRD, QCB, WRH, dll).
    | Anda dapat menambahkan, mengubah, atau menyesuaikan email di bawah ini.
    */
    'dept_heads' => [
        'ENG' => env('SP_DH_EMAIL_ENG', 'edy.santoso@pt-bas-id.com'),
        'ITE' => env('SP_DH_EMAIL_ITE', 'edy.santoso@pt-bas-id.com'),
        'PRD' => env('SP_DH_EMAIL_PRD', 'indra.prasasti2@pt-bas-id.com'),
        'QCB' => env('SP_DH_EMAIL_QCB', 'sriwahyuningsih.ongkokusumo@pt-bas-id.com'),
        'WRH' => env('SP_DH_EMAIL_WRH', 'endro.juniarto2@pt-bas-id.com'),
        'GA'  => env('SP_DH_EMAIL_GA',  'yongki.yeremia@pt-pas-id.com'),
        'HSE' => env('SP_DH_EMAIL_HSE', 'sutopo.sejati@pt-bas-id.com'),
        'XPE' => env('SP_DH_EMAIL_XPE', 'linawati.l@pt-pas-id.com'),
        'EXP' => env('SP_DH_EMAIL_EXP', 'linawati.l@pt-pas-id.com'),
        'HRD' => env('SP_DH_EMAIL_HRD', 'yongki.yeremia@pt-pas-id.com'),
        'RND' => env('SP_DH_EMAIL_RND', 'sutopo.sejati@pt-bas-id.com'),
        'IMS' => env('SP_DH_EMAIL_IMS', 'sutopo.sejati@pt-bas-id.com'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Mapping Alias Kode Bagian ke Kode Divisi Induk
    |--------------------------------------------------------------------------
    | Memastikan jika karyawan memiliki kode_bagian, otomatis dipetakan ke
    | Dept Head divisi induknya.
    */
    'section_to_division_mapping' => [
        'ITS'     => 'ITE',
        'QRT'     => 'QCB',
        'QPX'     => 'QCB',
        'QLB'     => 'QCB',
        'QRP'     => 'QCB',
        'QPO'     => 'QCB',
        'PFL'     => 'PRD',
        'PRS'     => 'PRD',
        'EUT'     => 'ENG',
        'ENG_PRD' => 'ENG',
        'WFG'     => 'WRH',
        'WRM'     => 'WRH',
        'XTB'     => 'XPE',
        'GA'      => 'GA',
        'HSE'     => 'HSE',
    ],

];
