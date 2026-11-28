<?php

use Illuminate\Support\Facades\DB;

$nik = auth()->user()->username;
$isAdminAllowed = DB::table('admin_departments')->where('nik_admin', $nik)->exists();

$menus = [
    // --- DASHBOARD ---
    [
        'label' => '',
        'menu' => [
            [
                'path' => 'hr-connect',
                'label' => 'Dashboard',
                'icon' => 'mdi-monitor-dashboard',
                'submenu' => [],
            ],
        ],
    ],

    [
        'label' => 'Master',
        'permission' => ['hr_connect_master'],
        'menu' => [
            [
                'path' => 'hr-connect/masters/admin',
                'label' => 'Master Admin Dept',
                'permission' => 'hr_connect_master',
                'icon' => 'mdi-database-cog-outline',
                'submenu' => [],
            ],
            [
                'path' => 'hr-connect/masters/reason',
                'label' => 'Master Alasan Keluar',
                'permission' => 'hr_connect_master',
                'icon' => 'mdi-account-off',
                'submenu' => [],
            ],
        ],
    ],

    // --- ADMIN DEPARTMENT ---
    $isAdminAllowed
        ? [
            'label' => 'Admin Dept',
            'permission' => ['hr_connect_admin'],
            'menu' => [
                [
                    'path' => 'hr-connect/dept-adm/data-karyawan',
                    'label' => 'Proses Data Karyawan', // Dibuat lebih jelas karena ini tempat checkout & plot
                    'permission' => 'hr_connect_admin',
                    'icon' => 'mdi-account-details',
                    'submenu' => [],
                ],
            ],
        ]
        : [],

    // --- DEPT GA ---
    [
        'label' => 'Dept. GA',
        'permission' => ['hr_connect_ga'],
        'menu' => [
            [
                'path' => 'hr-connect/dept-ga/karyawan-masuk',
                'label' => 'Karyawan Masuk',
                'permission' => 'hr_connect_ga',
                'icon' => 'mdi-account-check',
                'submenu' => [],
            ],
            [
                'path' => 'hr-connect/dept-ga/perlengkapan-goodie-apd',
                'label' => 'Konfirmasi Persiapan Goodie Bag',
                'permission' => 'hr_connect_ga',
                'icon' => 'mdi-checkbox-marked',
                'submenu' => [],
            ],
            [
                'path' => 'hr-connect/dept-ga/karyawan-keluar',
                'label' => 'Clearance Keluar', // Diganti biar berasa fungsinya (Clearance Fasilitas)
                'permission' => 'hr_connect_ga',
                'icon' => 'mdi-account-remove',
                'submenu' => [],
            ],
        ],
    ],

    // --- HRD IR ---
    [
        'label' => 'HRD IR',
        'permission' => ['hr_connect_ir'],
        'menu' => [
            [
                'path' => 'hr-connect/dept-hrd/shift-out-karyawan',
                'label' => 'Master Karyawan Aktif', // SOLUSI: Ganti nama agar HRD paham ini cuma untuk lihat (Read-Only)
                'permission' => 'hr_connect_ir',
                'icon' => 'mdi-account-group', // Icon grup karyawan
                'submenu' => [],
            ],
            [
                'path' => 'hr-connect/dept-hrd/karyawan-keluar',
                'label' => 'Finalisasi Offboarding', // SOLUSI: Menggunakan istilah profesional
                'permission' => 'hr_connect_ir',
                'icon' => 'mdi-shield-check', // Icon divalidasi/shield
                'submenu' => [],
            ],
            [
                'path' => 'hr-connect/dept-hrd/pemulihan-data',
                'label' => 'Pemulihan Data',
                'permission' => 'hr_connect_ir',
                'icon' => 'mdi-restore',
                'submenu' => [],
            ],
        ],
    ],

    // --- REPORT & ANALYTICS ---
    [
        'label' => 'Report & Master',
        'permission' => ['hr_connect_report', 'hr_connect_master'],
        'menu' => [
            [
                'path' => 'hr-connect/report/karyawan-masuk',
                'label' => 'Report Karyawan Masuk',
                'permission' => 'hr_connect_report',
                'icon' => 'mdi-file-document-outline',
                'submenu' => [],
            ],
            [
                'path' => 'hr-connect/report/karyawan-keluar',
                'label' => 'Report Karyawan Keluar',
                'permission' => 'hr_connect_report',
                'icon' => 'mdi-file-document-outline',
                'submenu' => [],
            ],
            // Memindahkan report HRD ke dalam grup ini biar nyatu
            [
                'path' => 'hr-connect/dept-hrd/report-karyawan-keluar',
                'label' => 'Report HRD (Finalisasi)',
                'permission' => 'hr_connect_ir',
                'icon' => 'mdi-folder-account',
                'submenu' => [],
            ],
            [
                'path' => 'hr-connect/report/kalender-karyawan',
                'label' => 'Kalender Karyawan',
                'permission' => 'hr_connect_report',
                'icon' => 'mdi-calendar-clock',
                'submenu' => [],
            ],
        ],
    ],
];

// array_filter memastikan kalau ada elemen array kosong (seperti Admin Dept jika user bukan admin) tidak ikut terender
$menus = array_values(array_filter($menus));

?>
<x-templates.velzon-hs.base :nameIcon="'user'" :menus="json_encode($menus)">
    <x-slot name="title">HRConnect</x-slot>

    {{-- Ini kontennya --}}
    @yield('content')

    <x-slot name="styles">@stack('styles')</x-slot>
    <x-slot name="scripts">@stack('scripts')</x-slot>
</x-templates.velzon-hs.base>
