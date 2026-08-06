<?php

$permissions = view()->shared('permissions') ?: [];

$menus = [];

$hasAnyPerm = function($perms) use ($permissions) {
    foreach ((array)$perms as $p) {
        if (in_array($p, $permissions)) return true;
    }
    return false;
};

// Monitoring Group
$monitoringItems = [];
if ($hasAnyPerm(['sp_pelanggaran', 'sp_pelanggaran_admin', 'sp_pelanggaran_dh', 'sp_pelanggaran_ir_staff', 'sp_pelanggaran_ir_head'])) {
    $monitoringItems[] = [
        'path' => 'sp-pelanggaran/trace',
        'label' => 'Trace SP',
        'icon' => 'mdi-eye-outline',
        'submenu' => [],
    ];
}
if ($hasAnyPerm(['sp_pelanggaran_ir_staff', 'sp_pelanggaran_ir_head'])) {
    $monitoringItems[] = [
        'path' => 'sp-pelanggaran/dashboard',
        'label' => 'Dashboard SP',
        'icon' => 'mdi-monitor-dashboard',
        'submenu' => [],
    ];
}

if (!empty($monitoringItems)) {
    $menus[] = [
        'label' => 'Monitoring',
        'menu' => $monitoringItems,
    ];
}

// Pelanggaran Karyawan Group
$transaksiItems = [];
if ($hasAnyPerm(['sp_pelanggaran_admin'])) {
    $transaksiItems[] = [
        'path' => 'sp-pelanggaran',
        'label' => 'Input SP Pelanggaran',
        'icon' => 'mdi-file-document-edit-outline',
        'submenu' => [],
    ];
}
if ($hasAnyPerm(['sp_pelanggaran_dh', 'sp_pelanggaran_ir_staff', 'sp_pelanggaran_ir_head'])) {
    $transaksiItems[] = [
        'path' => 'sp-pelanggaran/approval',
        'label' => 'Approval SP',
        'icon' => 'mdi-shield-check-outline',
        'submenu' => [],
    ];
}
if ($hasAnyPerm(['sp_pelanggaran_ir_staff', 'sp_pelanggaran_ir_head'])) {
    $transaksiItems[] = [
        'path' => 'sp-pelanggaran/master-kode',
        'label' => 'Master Kode SP',
        'icon' => 'mdi-book-open-page-variant-outline',
        'submenu' => [],
    ];
}

if (!empty($transaksiItems)) {
    $menus[] = [
        'label' => 'Pelanggaran Karyawan',
        'menu' => $transaksiItems,
    ];
}

?>
<x-templates.velzon-hs.base :nameIcon="'alert-decagram'" :menus="json_encode($menus)">
    <x-slot name="title">SP Pelanggaran Karyawan</x-slot>

    @yield('content')

    <x-slot name="styles">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
        @stack('styles')
    </x-slot>
    <x-slot name="scripts">
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        @stack('scripts')
    </x-slot>
</x-templates.velzon-hs.base>
