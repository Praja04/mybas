<?php

use App\Http\Controllers\PosSecurity\Datatable\Absensi\AbsensiDatatable;
use App\Http\Controllers\PosSecurity\Datatable\Absensi\AbsensiGateDatatable;
use App\Http\Controllers\PosSecurity\Datatable\History\HistorySupplierDatatable;
use App\Http\Controllers\PosSecurity\Datatable\History\HistoryVendorDatatable;
use Illuminate\Support\Facades\Route;

// Route::group(['middleware' => ['secure.auth', 'secure.auth.rules', 'access_log']], function () {
// Route::prefix('datatable')->group(function () {
// Route::get('/kartuqr', [KartuQRSupplierDatatable::class, 'index'])
//     ->name("datatable.pos-security.kartuqr");

// SMU
// Route::prefix('history/smu')->group(function () {
//     Route::get('/vendor', [HistoryVendorSMUDatatable::class, 'index'])
//         ->name("datatable.pos-security.history.visitor.vendor.smu");
//     Route::get('/supplier', [HistorySupplierSMUDatatable::class, 'index'])
//         ->name("datatable.pos-security.history.visitor.supplier.smu");
// });


// BAS
Route::prefix('history/')->group(function () {
    Route::get('/supplier', [HistorySupplierDatatable::class, 'index'])
        ->name("datatable.pos-security.history.visitor.supplier");
    Route::get('/tamu', [HistoryVendorDatatable::class, 'index'])
        ->name("datatable.pos-security.history.visitor.vendor");
});

// Route::prefix('blacklist')->group(function () {
//     Route::get('/', [BlacklistDatatable::class, 'index'])
//         ->name("datatable.pos-security.blacklist.supplier.pas");
// });

Route::prefix('absensi')->group(function () {
    Route::get('/', [AbsensiDatatable::class, 'index'])
        ->name("datatable.pos-security.absensi.log.index");
    Route::get('/gate', [AbsensiGateDatatable::class, 'index'])
        ->name("datatable.pos-security.absensi.log.gate.index");
});

    // Route::prefix('kartu')->group(function () {
    //     Route::get('/kartu-aktif', [KartuAktifDatatable::class, 'index'])
    //         ->name("datatable.pos-security.kartu-aktif.index");
    //     Route::get('/kartu-aktif/detail', [KartuAktifDetailDatatable::class, 'index'])
    //         ->name("datatable.pos-security.kartu-aktif.detail.index");
    // });
// });
// });
