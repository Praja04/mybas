<?php

$menus = [
    [
        'label' => '',
        'menu' => [
            [
                'path' => '/',
                'label' => 'Kembali ke MyBAS',
                'icon' => 'mdi-arrow-left',
                'submenu' => [],
            ],
            [
                'path' => 'pos-security/formulir',
                'label' => 'Formulir Security',
                'icon' => 'mdi-file-document-outline',
                'submenu' => [],
            ],
            [
                'path' => 'pos-security/formulir/cek-kendaraan',
                'label' => 'Formulir Pengecekan Kendaraan',
                'icon' => 'mdi-camera-rear',
                'submenu' => [],
            ],
        ],
    ],
    [
        'label' => 'Dashboard',
        'menu' => [
            [
                'path' => 'pos-security/dashboard',
                'label' => 'Dashboard',
                'icon' => 'mdi-view-dashboard',
                'submenu' => [],
            ],
            [
                'path' => 'pos-security/absensi/visitor',
                'label' => 'Absensi Tapping Pengunjung',
                'icon' => 'mdi-barcode-scan',
                'submenu' => [],
            ],
            [
                'path' => 'pos-security/absensi/security',
                'label' => 'Absensi Tapping Security',
                'icon' => 'mdi-barcode-scan',
                'submenu' => [],
            ],
            [
                'path' => 'pos-security/absensi/display',
                'label' => 'Display Tapping',
                'icon' => 'mdi-monitor',
                'submenu' => [],
            ],
        ],
    ],
    [
        'label' => 'Riwayat',
        'menu' => [
            [
                'path' => 'sidebarPortalSecureParking',
                'label' => 'Portal Secure Parking',
                'icon' => 'mdi-car-key',
                'submenu' => [
                    [
                        'path' => 'pos-security/history/supplier/smu',
                        'label' => 'Supplier',
                    ],
                    [
                        'path' => 'pos-security/history/vendor/smu',
                        'label' => 'Vendor/Tamu',
                    ],
                ],
            ],
            [
                'path' => 'sidebarDaftarTamuBAS',
                'label' => 'Daftar Tamu PT BAS',
                'icon' => 'mdi-account-group-outline',
                'submenu' => [
                    [
                        // 'path' => 'pos-security/history/supplier/pas',
                        'path' => 'pos-security/history/supplier',
                        'label' => 'Supplier',
                    ],
                    [
                        // 'path' => 'pos-security/history/vendor/pas',
                        'path' => 'pos-security/history/tamu',
                        'label' => 'Vendor/Tamu',
                    ],
                ],
            ],
            [
                'path' => 'pos-security/history/kendaraan',
                'label' => 'Riwayat Pengecekan Kendaraan',
                'icon' => 'mdi-truck',
                'submenu' => [],
            ],
        ],
    ],
    [
        'label' => 'Report',
        'menu' => [
            [
                'path' => 'sidebarKartu',
                'label' => 'Kartu',
                'icon' => 'mdi-card-account-details-outline',
                'submenu' => [
                    [
                        'path' => 'pos-security/kartu-aktif',
                        'label' => 'List Kartu Aktif',
                    ],
                ],
            ],
            [
                'path' => 'pos-security/blacklist',
                'label' => 'Blacklist Tamu',
                'icon' => 'mdi-account-cancel-outline',
                'submenu' => [],
            ],
        ],
    ],
    [
        'label' => 'Bantuan',
        'menu' => [
            [
                'path' => 'sidebarBantuan',
                'label' => 'Bantuan',
                'icon' => 'mdi-help-circle-outline',
                'submenu' => [],
            ],
        ],
    ],
];

?>

<x-templates.velzon-hs.base nameIcon="" menus="{!! json_encode($menus) !!}">

    <x-slot name="title">
        @yield('title', '')
    </x-slot>

    <x-slot name="longName">Pos Security</x-slot>
    <x-slot name="shortName">POS</x-slot>

    @yield('content')

    <x-slot name="styles">
        @stack('styles')
    </x-slot>

    <x-slot name="scripts">
        @include('pos-security.routes.ajax')
        @include('pos-security.routes.datatable')

        @stack('scripts')
    </x-slot>
</x-templates.velzon-hs.base>
