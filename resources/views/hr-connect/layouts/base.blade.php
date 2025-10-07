<?php

use Illuminate\Support\Facades\DB;

$nik = auth()->user()->username;

$isAdminAllowed = DB::table('admin_departments')->where('nik_admin', $nik)->exists();

$menus = [
    [
        'label' => '',
        'menu' => [
            [
                'path' => 'hr-connect',
                'label' => 'Dashboard',
                'icon' => 'mdi-monitor-dashboard',
                'submenu' => []
            ]
        ],
    ],
     
    [
        'label' => 'Master Data',
        'permission' => ['hr_connect_master'],
        'menu' => [
            [
                'path' => 'hr-connect/masters/admin',
                'label' => 'Admin Department',
                'permission' => 'hr_connect_master',
                'icon' => 'mdi-account-cog',
                'submenu' => []
            ]
        ],
    ],
    [
        'label' => 'Dept.GA',
        'permission' => ['hr_connect_ga'],
        'menu' => [
            [
                'path' => 'hr-connect/dept-ga/karyawan-masuk',
                'label' => 'Karyawan Masuk',
                'permission' => 'hr_connect_ga',
                'icon' => 'mdi-account-check',
                'submenu' => []
            ]
        ],
    ],
    [
        'label' => '',
        'menu' => [
            [
                'path' => 'hr-connect/dept-ga/perlengkapan-goodie-apd',
                'label' => 'Persiapan Perlengkapan Goodie Bag & APD',
                'permission' => 'hr_connect_ga',
                'icon' => 'mdi-checkbox-marked',
                'submenu' => []
            ]
        ],
    ],
    [
        'label' => '',
        'menu' => [
            [
                'path' => 'hr-connect/dept-ga/karyawan-keluar',
                'label' => 'Karyawan Keluar',
                'permission' => 'hr_connect_ga',
                'icon' => 'mdi-account-remove',
                'submenu' => []
            ]
        ],
    ],
    [
        'label' => 'HRD IR', 
        'permission' => ['hr_connect_ir'],
        'menu' => [
            [
                'path' => 'hr-connect/dept-hrd/shift-out-karyawan',
                'label' => 'Shift Out Karyawan',
                'permission' => 'hr_connect_ir',
                'icon' => 'mdi-arrow-right',
                'submenu' => []
            ]
        ],
    ],
    [
        'label' => '', 
        'permission' => ['hr_connect_ir'],
        'menu' => [
            [
                'path' => 'hr-connect/dept-hrd/karyawan-keluar',
                'label' => 'Karyawan Keluar',
                'permission' => 'hr_connect_ir',
                'icon' => 'mdi-account-off',
                'submenu' => []
            ]
        ],
    ],
    [
        'label' => '', 
        'permission' => ['hr_connect_ir'],
        'menu' => [
            [
                'path' => 'hr-connect/dept-hrd/pemulihan-data',
                'label' => 'Pemulihan Data',
                'permission' => 'hr_connect_ir',
                'icon' => 'mdi-restore',
                'submenu' => []
            ]
        ],
    ],
    [
        'label' => '', 
        'permission' => ['hr_connect_ir'],
        'menu' => [
            [
                'path' => 'hr-connect/dept-hrd/report-karyawan-keluar',
                'label' => 'Report Karyawan Keluar',
                'permission' => 'hr_connect_ir',
                'icon' => 'mdi-file-document',
                'submenu' => []
            ]
        ],
    ],
    [
        'label' => 'Report', 
        'permission' => ['hr_connect_report'],
        'menu' => [
            [
                'path' => 'hr-connect/report/karyawan-masuk',
                'label' => 'Karyawan Masuk',
                'permission' => 'hr_connect_report',
                'icon' => 'mdi-file-document',
                'submenu' => []
            ]
        ],
    ],
    [
        'label' => '', 
        'permission' => ['hr_connect_report'],
        'menu' => [
            [
                'path' => 'hr-connect/report/karyawan-keluar',
                'label' => 'Karyawan Keluar',
                'permission' => 'hr_connect_report',
                'icon' => 'mdi-file-document',
                'submenu' => []
            ]
        ],
    ],
    [
        'label' => '', 
        'permission' => ['hr_connect_report'],
        'menu' => [
            [
                'path' => 'hr-connect/report/kalender-karyawan',
                'label' => 'Kalender Karyawan',
                'permission' => 'hr_connect_report',
                'icon' => 'mdi-calendar-clock',
                'submenu' => []
            ]
        ],
    ],
    $isAdminAllowed ? [
        'label' => 'Admin', 
        'permission' => ['hr_connect_admin'],
        'menu' => [
            [
                'path' => 'hr-connect/dept-adm/data-karyawan',
                'label' => 'Data Karyawan',
                'permission' => 'hr_connect_admin',
                'icon' => 'mdi-account-details',
                'submenu' => []
            ]
        ],
    ] : [],
];

?>
<x-templates.velzon.base :nameIcon="'user'" :menus="json_encode(array_filter($menus))">
    <x-slot name="title">HRConnect</x-slot>


    {{-- Ini kontennya --}}
    @yield('content')

    <x-slot name="styles">@stack('styles')</x-slot>
    <x-slot name="scripts">@stack('scripts')</x-slot>
</x-templates.velzon.base>