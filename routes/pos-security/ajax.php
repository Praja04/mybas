<?php

use App\Http\Controllers\PosSecurity\Ajax\Absensi\AbsensiRestLogAjax;
use App\Http\Controllers\PosSecurity\Ajax\Blacklist\BlacklistAjax;
use App\Http\Controllers\PosSecurity\Ajax\Dashboard\DashboardAjax;
use App\Http\Controllers\PosSecurity\Ajax\Formulir\CekKendaraanFormAjax;
use App\Http\Controllers\PosSecurity\Ajax\Formulir\SupplierFormAjax;
use App\Http\Controllers\PosSecurity\Ajax\Formulir\TamuFormAjax;
use App\Http\Controllers\PosSecurity\Ajax\Security\SecurityFormAjax;
use Illuminate\Support\Facades\Route;

Route::group(
    ['middleware' => ['auth', 'rules', 'access_log']],
    function () {

        // transaksi vendor / tamu
        Route::post('/store-vendor-tamu', [TamuFormAjax::class, 'store'])->name("ajax.pos-security.vendor-transaksi.store_vendor");
        Route::post('/search-vendor-tamu', [TamuFormAjax::class, 'search'])->name("ajax.pos-security.vendor-transaksi.search_vendor");
        Route::post('/kembalikan-vendor-tamu', [TamuFormAjax::class, 'kembali_kartu'])->name("ajax.pos-security.vendor-transaksi.kembali_kartu");

        // transaksi supplier
        Route::post('/store-supplier', [SupplierFormAjax::class, 'store'])->name("ajax.pos-security.visitor-transaksi.store");
        Route::post('/search-supplier', [SupplierFormAjax::class, 'search'])->name("ajax.pos-security.visitor-transaksi.search");
        Route::post('/kembalikan-supplier', [SupplierFormAjax::class, 'kembali_kartu'])->name("ajax.pos-security.visitor-transaksi.kembali_kartu");
        Route::post('/supplier/block', [SupplierFormAjax::class, 'blacklist'])->name('ajax.pos-security.visitor-transaksi.block');
        Route::post('/supplier/report-lost', [SupplierFormAjax::class, 'reportLostCard'])->name('ajax.pos-security.visitor-transaksi.reportLost');
        Route::get('/supplier/detail', [SupplierFormAjax::class, 'getVisitorDetail'])->name('ajax.pos-security.visitor-transaksi.detail');

        // cek kendaraan
        Route::post('/store-kendaraan', [CekKendaraanFormAjax::class, 'store'])->name("ajax.pos-security.cek-kendaraan.store");
        Route::get('/search-kendaraan', [CekKendaraanFormAjax::class, 'search'])->name("ajax.pos-security.cek-kendaraan.search");
        Route::get('/kendaraan/show', [CekKendaraanFormAjax::class, 'show'])->name("ajax.pos-security.cek-kendaraan.show");

        Route::post('/absensi-rest-log', [AbsensiRestLogAjax::class, 'search'])->name("ajax.pos-security.absensirestlog.search");
        Route::get('/blacklist/show', [BlacklistAjax::class, 'show'])->name('ajax.pos-security.blacklist.show');

        Route::post('/dashboard/filter', [DashboardAjax::class, 'filter'])->name('ajax.pos-security.dashboard.filter');
        Route::post('/dashboard/statistik', [DashboardAjax::class, 'statistikPerusahaanDepartemen'])->name('ajax.pos-security.dashboard.statistik');

        Route::prefix('master')->group(function () {
            Route::prefix('security')->group(function () {
                Route::post('/', [SecurityFormAjax::class, 'store'])->name("ajax.pos-security.master.security.store");
                Route::get('/edit/{id}', [SecurityFormAjax::class, 'edit'])->name('ajax.pos-security.master.security.edit');
                Route::put('/update/{id}', [SecurityFormAjax::class, 'update'])->name('ajax.pos-security.master.security.update');
                Route::post('/toggle/{id}', [SecurityFormAjax::class, 'toggle'])->name('ajax.pos-security.master.security.toggle');
            });
        });
    }
);



// Route::group(['middleware' => ['secure.auth', 'secure.auth.rules', 'access_log']], function () {
//   Route::prefix('ajax')->group(function () {

//     Route::post('/dashboard/filter', [DashboardAjax::class, 'filter'])->name('ajax.pos-security.dashboard.filter');
//     Route::post('/dashboard/statistik', [DashboardAjax::class, 'statistikPerusahaanDepartemen'])->name('ajax.pos-security.dashboard.statistik');

//     // generate
//     Route::post('/generate', [KartuQRAjax::class, 'generateKartu'])->name("ajax.pos-security.kartuqr.generate");
//     Route::post('/in-active', [KartuQRAjax::class, 'inActiveKartu'])->name("ajax.pos-security.kartuqr.inactive");
//     Route::post('/in-blocked', [KartuQRAjax::class, 'inBlockKartu'])->name("ajax.pos-security.kartuqr.inblock");
//     Route::post('/add-kartu', [KartuQRAjax::class, 'addKartu'])->name("ajax.pos-security.kartuqr.add-kartu");
//     Route::get('/get-kartu', [KartuQRAjax::class, 'getKartu'])->name("ajax.pos-security.kartuqr.get-kartu");
//     Route::post('/update-kartu', [KartuQRAjax::class, 'updateKartu'])->name("ajax.pos-security.kartuqr.update-kartu");

//     // select2 Departement
//     Route::get('/get-select2', [DepartementAjax::class, 'get_select2'])->name("ajax.pos-security.departement.get_select2");

//     // transaksi visitor
//     Route::post('/store-supplier', [SupplierFormAjax::class, 'store'])->name("ajax.pos-security.visitor-transaksi.store");
//     Route::post('/search-supplier', [SupplierFormAjax::class, 'search'])->name("ajax.pos-security.visitor-transaksi.search");
//     Route::post('/kembalikan-supplier', [SupplierFormAjax::class, 'kembali_kartu'])->name("ajax.pos-security.visitor-transaksi.kembali_kartu");
//     Route::post('/supplier/block', [SupplierFormAjax::class, 'blacklist'])->name('ajax.pos-security.visitor-transaksi.block');
//     Route::post('/supplier/report-lost', [SupplierFormAjax::class, 'reportLostCard'])->name('ajax.pos-security.visitor-transaksi.reportLost');
//     Route::get('/supplier/detail', [SupplierFormAjax::class, 'getVisitorDetail'])->name('ajax.pos-security.visitor-transaksi.detail');


//     // transaksi vendor / tamu
//     Route::post('/store-vendor-tamu', [TamuFormAjax::class, 'store'])->name("ajax.pos-security.vendor-transaksi.store_vendor");
//     Route::post('/search-vendor-tamu', [TamuFormAjax::class, 'search'])->name("ajax.pos-security.vendor-transaksi.search_vendor");
//     Route::post('/kembalikan-vendor-tamu', [TamuFormAjax::class, 'kembali_kartu'])->name("ajax.pos-security.vendor-transaksi.kembali_kartu");

//     // // absensi rest log
//     // Route::post('/absensi-rest-log', [AbsensiRestLogAjax::class, 'search'])->name("ajax.pos-security.absensirestlog.search");
//     Route::get('/blacklist/show', [BlacklistAjax::class, 'show'])->name('ajax.pos-security.blacklist.show');
//   });
// });
