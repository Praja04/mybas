<?php

$menus = [
    [
        'label' => '',
        'menu' => [
            [
                'path' => 'halo-security',
                'label' => 'Dashboard',
                'icon' => 'mdi-monitor-dashboard',
                'submenu' => []
            ]
        ],
    ],
    ['label' => 'Master',
        'menu' => [
            [
                'path' => 'halo-security/bai/listsecurityuserga',
                'label' => 'Security User GA',
                'icon' => 'mdi-police-badge',
                'permission' => 'hs_listsug',
                'submenu' => []
            ],
        ]
    ],
    [
        'label' => '',
        'menu' => [
            [
                'path' => 'halo-security/cek_pengajuan_izin',
                'label' => 'Cek Izin Karyawan',
                'icon' => 'mdi-card-account-details-star',
                'permission' => 'cek_pengajuan_izin',
                'submenu' => []
            ]
        ],
    ],
    [
        'label' => 'LAPORAN KEJADIAN',
        'permission' => 'hs_kejadian',
        'menu' => [
            [
                'path' => 'sidebarLaporanKejadian',
                'label' => 'Laporan Kejadian',
                'icon' => 'mdi-laptop',
                'submenu' => [
                    [
                        'path' => 'halo-security/laporan-kejadian/listlaporankejadian',
                        'label' => 'List LK',
                    ],
                    [
                        'path' => 'halo-security/laporan-kejadian/createlaporankejadian',
                        'label' => 'Buat LK',
                        'permission' => 'hs_buat_lk',
                    ],
                ]
            ],
        ],
    ],
    [
        'label' => 'BA S.O.P',
        'permission' => 'hs_sop',
        'menu' => [
            [
                'path' => 'sidebarBeritaAcaraKaryawan',
                'label' => 'BA S.O.P Karyawan',
                'icon' => 'mdi-office-building-outline',
                'submenu' => [
                    [
                        'path' => 'halo-security/ba-sop-karyawan/listkaryawan',
                        'label' => 'List BA S.O.P Karyawan',
                    ],
                    [
                        'path' => 'halo-security/ba-sop-karyawan/createkaryawan',
                        'label' => 'Buat BA S.O.P Karyawan',
                        'permission' => 'hs_buat_sopkaryawan',
                    ],
                ]
            ],
            [
                'path' => 'sidebarBeritaAcaraSupir',
                'label' => 'BA S.O.P Supir',
                'icon' => 'mdi-truck-check-outline',
                'submenu' => [
                    [
                        'path' => 'halo-security/ba-sop-supir/listsupir',
                        'label' => 'List BA S.O.P Supir',
                    ],
                    [
                        'path' => 'halo-security/ba-sop-supir/createsupir',
                        'label' => 'Buat BA S.O.P Supir',
                        'permission' => 'hs_buat_sopsupir',
                    ],
                ]
            ],
        ],
    ],
    ['label' => 'BERITA ACARA INTROGASI',
    // 'permission' => ['hs_listbai', 'hs_createbai'],
        'menu' => [
            [
                'path' => 'sidebarBAI',
                'label' => 'BAI',
                'icon' => 'mdi-google-classroom',
                'submenu' => [
                    [
                        'path' => 'halo-security/bai/listintrogasi',
                        'label' => 'List BA Introgasi',
                        'permission' => 'hs_listbai'
                    ],
                    [
                        'path' => 'halo-security/bai/createintrogasi',
                        'label' => 'Buat BA Introgasi',
                        'permission' => 'hs_createbai'
                    ]
                ]
            ],
        ],
    ],
]

?>

<x-templates.velzon-hs.base nameIcon="server-security" menus="{!! json_encode($menus) !!}">

    <x-slot name="title">
        @yield('title', '') Halo Security
    </x-slot>

    <x-slot name="longName">Halo Security</x-slot>
    <x-slot name="shortName">HS</x-slot>

    @yield('content')

    <x-slot name="styles">
        @stack('styles')
    </x-slot>

    <x-slot name="scripts">
        @stack('scripts')
    </x-slot>
</x-templates.velzon-hs.base>