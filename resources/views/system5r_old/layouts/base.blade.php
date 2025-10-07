<?php

$menus = [
    [
        'title' => '',
        'visible' => false, 
        'menu' => [
            [
                'path' => '5r-system',
                'label' => 'Dashboard',
                'icon' => 'mdi-speedometer',
                'submenu' => [],
            ],
        ],
    ],
    [
        'title' => 'Master',
        'menu' => [
            [
                'path' => '5r-system/master-department',
                'label' => 'Master Department',
                'icon' => 'mdi-folder-table',
                'permission' => '5r_organizer',
                'submenu' => [],
            ],
            [
                'path' => '5r-system/master-group',
                'label' => 'Master Group',
                'icon' => 'mdi-folder-table',
                'permission' => '5r_organizer',
                'submenu' => [],
            ],
            [
                'path' => '5r-system/master-pertanyaan',
                'label' => 'Master Pertanyaan',
                'icon' => 'mdi-folder-table',
                'permission' => '5r_organizer',
                'submenu' => [],
            ],
            [
                'path' => '5r-system/master-penilaian',
                'label' => 'Master Penilaian',
                'icon' => 'mdi-folder-table',
                'permission' => '5r_organizer',
                'submenu' => [],
            ],
            [
                'path' => '5r-system/report/master-committee',
                'label' => 'Master Committee',
                'icon' => 'mdi-folder-table',
                'permission' => '5r_organizer',
                'submenu' => [],
            ],
            [
                'path' => '5r-system/master-juri',
                'label' => 'Master Juri',
                'icon' => 'mdi-folder-table',
                'permission' => '5r_organizer',
                'submenu' => [],
            ],
            [
                'path' => '5r-system/schedule-juri',
                'label' => 'Schedule Juri',
                'icon' => 'mdi-folder-table',
                'permission' => '5r_organizer',
                'submenu' => [],
            ],
        ],
    ],
    [
        'title' => 'Penilaian',
        'menu' => [
            [
                'path' => '5r-system/penilaian',
                'label' => 'Penilaian 5R',
                'icon' => 'mdi-clipboard-edit',
                'permission' => '5r_juri',
                'submenu' => [],
            ],
            // [
            //     'path' => '5r-system/penilaian/approval',
            //     'label' => 'Approval Penilaian',
            //     'icon' => 'mdi-clipboard-check',
            //     'permission' => '5r_comittee',
            //     'submenu' => [],
            // ],
            // [
            //     'path' => '5r-system/penilaian/komplain',
            //     'label' => 'Feedback Approval Penilaian',
            //     'icon' => 'mdi-replay',
            //     'permission' => '5r_juri',
            //     'submenu' => []
            // ],
        ],
    ],
    [
        'title' => 'Report',
        'menu' => [
            [
                'path' => '5r-system/report/management',
                'label' => 'Management Report',
                'icon' => 'mdi-chart-box',
                'permission' => '5r_management',
                'submenu' => [],
            ],
            [
                'path' => '5r-system/report/committee',
                'label' => 'Committee Report',
                'icon' => 'mdi-chart-box',
                'permission' => '5r_comittee',
                'submenu' => [],
            ],
        ],
    ],
];



?>

<x-templates.velzon.base nameIcon="" menus="{!! json_encode($menus) !!}">

    <x-slot name="title">
        @yield('title', '') 5R System
    </x-slot>

    <x-slot name="longName">5R System</x-slot>
    <x-slot name="shortName">5R</x-slot>

    @yield('content')

    <x-slot name="styles">
        @stack('styles')
    </x-slot>

    <x-slot name="scripts">
        @stack('scripts')
    </x-slot>
</x-templates.velzon.base>
