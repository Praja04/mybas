<?php

use App\Http\Controllers\PosSecurity\Datatable\Absensi\AbsensiDatatable;
use App\Http\Controllers\PosSecurity\Datatable\Absensi\AbsensiGateDatatable;
use App\Http\Controllers\PosSecurity\Datatable\Blacklist\BlacklistDatatable;
use App\Http\Controllers\PosSecurity\Datatable\FormCekKendaraan\FormInDatatable;
use App\Http\Controllers\PosSecurity\Datatable\FormCekKendaraan\FormOutDatatable;
use App\Http\Controllers\PosSecurity\Datatable\History\HistoryCekKendaraanDatatable;
use App\Http\Controllers\PosSecurity\Datatable\History\HistorySupplierDatatable;
use App\Http\Controllers\PosSecurity\Datatable\History\HistoryVendorDatatable;
use App\Http\Controllers\PosSecurity\Datatable\KartuAktif\KartuAktifDatatable;
use App\Http\Controllers\PosSecurity\Datatable\KartuAktif\KartuAktifDetailDatatable;
use App\Http\Controllers\PosSecurity\Datatable\Security\SecurityDatatable;
use Illuminate\Support\Facades\Route;

// Route::group(['middleware' => ['secure.auth', 'secure.auth.rules', 'access_log']], function () {
// Route::prefix('datatable')->group(function () {
// Route::get('/kartuqr', [KartuQRSupplierDatatable::class, 'index'])
//     ->name("datatable.pos-security.kartuqr");

// SMU
// Route::prefix('history/smu')->group(function () {
//     Route::get('/vendor', [HistoryVendorSMUDatatable::class, 'index'])
//         ->name("datatable.pos-security.history.visitor.vendor.smu");
//     Route::get('/supplier', [HistorySupplierSMUDatadotable::class, 'index'])
//         ->name("datatable.pos-security.history.visitor.supplier.smu");
// });

Route::group(
    ['middleware' => ['auth', 'rules', 'access_log']],
    function () {

        Route::prefix('history')->group(function () {
            Route::get('/supplier', [HistorySupplierDatatable::class, 'index'])
                ->name("datatable.pos-security.history.visitor.supplier");
            Route::get('/tamu', [HistoryVendorDatatable::class, 'index'])
                ->name("datatable.pos-security.history.visitor.vendor");
            Route::get('/kendaraan', [HistoryCekKendaraanDatatable::class, 'index'])
                ->name("datatable.pos-security.history.visitor.kendaraan");
        });

        Route::prefix('cek-kendaraan')->group(function () {
            Route::get('/in', [FormInDatatable::class, 'index'])
                ->name("datatable.pos-security.cek-kendaraan.in");
            Route::get('/out', [FormOutDatatable::class, 'index'])
                ->name("datatable.pos-security.cek-kendaraan.out");
        });

        Route::prefix('blacklist')->group(function () {
            Route::get('/', [BlacklistDatatable::class, 'index'])
                ->name("datatable.pos-security.blacklist.supplier.pas");
        });

        Route::prefix('absensi')->group(function () {
            Route::get('/', [AbsensiDatatable::class, 'index'])
                ->name("datatable.pos-security.absensi.log.index");
            Route::get('/gate', [AbsensiGateDatatable::class, 'index'])
                ->name("datatable.pos-security.absensi.log.gate.index");
        });

        Route::prefix('kartu')->group(function () {
            Route::get('/kartu-aktif', [KartuAktifDatatable::class, 'index'])
                ->name("datatable.pos-security.kartu-aktif.index");
            Route::get('/active-list', [\App\Http\Controllers\PosSecurity\Datatable\KartuAktif\ActiveCardListDatatable::class, 'index'])
                ->name("datatable.pos-security.kartu.active-list");
            Route::get('/kartu-aktif/detail', [KartuAktifDetailDatatable::class, 'index'])
                ->name("datatable.pos-security.kartu-aktif.detail.index");
        });

        Route::prefix('master')->group(function () {
            Route::prefix('security')->group(function () {
                Route::get('/', [SecurityDatatable::class, 'index'])
                    ->name("datatable.pos-security.master.security.index");
            });
        });
    }
);

// });
// });
