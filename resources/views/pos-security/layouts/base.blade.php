<?php

$menus = [
    [
        'label' => 'Formulir',
        'menu' => [
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
        'label' => 'Display',
        'menu' => [
            [
                'path' => 'pos-security/absensi/display',
                'label' => 'Display Tapping',
                'icon' => 'mdi-monitor',
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
        ],
    ],
    [
        'label' => 'Riwayat',
        'menu' => [
            [
                'path' => 'pos-security/absensi/visitor',
                'label' => 'Riwayat Tapping Pengunjung',
                'icon' => 'mdi-barcode-scan',
                'submenu' => [],
            ],
            [
                'path' => 'pos-security/absensi/gate',
                'label' => 'Riwayat Tapping Security',
                'icon' => 'mdi-barcode-scan',
                'submenu' => [],
            ],
            [
                'path' => 'sidebarRiwayatTamuBAS',
                'label' => 'Riwayat Tamu PT BAS',
                'icon' => 'mdi-account-group-outline',
                'submenu' => [
                    [
                        'path' => 'pos-security/history/supplier',
                        'label' => 'Supplier',
                    ],
                    [
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
        'label' => 'Data',
        'menu' => [
            [
                'path' => 'pos-security/master/security',
                'label' => 'Data Security',
                'icon' => 'mdi-human-male',
                'permission' => 'pos-security_data_security',
                'submenu' => [],
            ],

            [
                'path' => 'sidebarKartu',
                'label' => 'Kartu',
                'icon' => 'mdi-card-account-details-outline',
                'submenu' => [
                    [
                        'path' => 'pos-security/kartu/kartu-aktif',
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
    // [
    //     'label' => 'Bantuan',
    //     'menu' => [
    //         [
    //             'path' => 'sidebarBantuan',
    //             'label' => 'Bantuan',
    //             'icon' => 'mdi-help-circle-outline',
    //             'submenu' => [],
    //         ],
    //     ],
    // ],
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

        <script>
            // Session Keeper - Menjaga session tetap aktif dengan ping ke server setiap 5 menit
            setInterval(function() {
                fetch("{{ route('pos-security.session-keeper') }}")
                    .then(response => response.json())
                    .then(data => console.log('Session Keeper: ' + data.status))
                    .catch(error => console.log('Session Keeper Error'));
            }, 5 * 60 * 1000); 
        </script>
    </x-slot>
</x-templates.velzon-hs.base>
