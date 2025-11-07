<?php

use App\Http\Controllers\PosSecurity\Ajax\Formulir\TamuFormAjax;
use Illuminate\Support\Facades\Route;

// transaksi vendor / tamu
Route::post('/store-vendor-tamu', [TamuFormAjax::class, 'store'])->name("ajax.pos-security.vendor-transaksi.store_vendor");
Route::post('/search-vendor-tamu', [TamuFormAjax::class, 'search'])->name("ajax.pos-security.vendor-transaksi.search_vendor");
Route::post('/kembalikan-vendor-tamu', [TamuFormAjax::class, 'kembali_kartu'])->name("ajax.ga.sistem-tracking.vendor-transaksi.kembali_kartu");

// Route::post('ajax/absensi-rest-log', [AbsensiRestLogAjax::class, 'search'])->name("ajax.ga.sistem-tracking.absensirestlog.search");

// Route::group(['middleware' => ['secure.auth', 'secure.auth.rules', 'access_log']], function () {
//   Route::prefix('ajax')->group(function () {

//     Route::post('/dashboard/filter', [DashboardAjax::class, 'filter'])->name('ajax.ga.sistem-tracking.dashboard.filter');
//     Route::post('/dashboard/statistik', [DashboardAjax::class, 'statistikPerusahaanDepartemen'])->name('ajax.ga.sistem-tracking.dashboard.statistik');

//     // generate
//     Route::post('/generate', [KartuQRAjax::class, 'generateKartu'])->name("ajax.ga.sistem-tracking.kartuqr.generate");
//     Route::post('/in-active', [KartuQRAjax::class, 'inActiveKartu'])->name("ajax.ga.sistem-tracking.kartuqr.inactive");
//     Route::post('/in-blocked', [KartuQRAjax::class, 'inBlockKartu'])->name("ajax.ga.sistem-tracking.kartuqr.inblock");
//     Route::post('/add-kartu', [KartuQRAjax::class, 'addKartu'])->name("ajax.ga.sistem-tracking.kartuqr.add-kartu");
//     Route::get('/get-kartu', [KartuQRAjax::class, 'getKartu'])->name("ajax.ga.sistem-tracking.kartuqr.get-kartu");
//     Route::post('/update-kartu', [KartuQRAjax::class, 'updateKartu'])->name("ajax.ga.sistem-tracking.kartuqr.update-kartu");

//     // select2 Departement
//     Route::get('/get-select2', [DepartementAjax::class, 'get_select2'])->name("ajax.ga.sistem-tracking.departement.get_select2");

//     // transaksi visitor
//     Route::post('/store-supplier', [SupplierFormAjax::class, 'store'])->name("ajax.ga.sistem-tracking.visitor-transaksi.store");
//     Route::post('/search-supplier', [SupplierFormAjax::class, 'search'])->name("ajax.ga.sistem-tracking.visitor-transaksi.search");
//     Route::post('/kembalikan-supplier', [SupplierFormAjax::class, 'kembali_kartu'])->name("ajax.ga.sistem-tracking.visitor-transaksi.kembali_kartu");

//     Route::post('/supplier/block', [SupplierFormAjax::class, 'blacklist'])->name('ajax.ga.sistem-tracking.visitor-transaksi.block');
//     Route::post('/supplier/report-lost', [SupplierFormAjax::class, 'reportLostCard'])->name('ajax.ga.sistem-tracking.visitor-transaksi.reportLost');
//     Route::get('/supplier/detail', [SupplierFormAjax::class, 'getVisitorDetail'])->name('ajax.ga.sistem-tracking.visitor-transaksi.detail');


//     // transaksi vendor / tamu
//     Route::post('/store-vendor-tamu', [TamuFormAjax::class, 'store'])->name("ajax.ga.sistem-tracking.vendor-transaksi.store_vendor");
//     Route::post('/search-vendor-tamu', [TamuFormAjax::class, 'search'])->name("ajax.ga.sistem-tracking.vendor-transaksi.search_vendor");
//     Route::post('/kembalikan-vendor-tamu', [TamuFormAjax::class, 'kembali_kartu'])->name("ajax.ga.sistem-tracking.vendor-transaksi.kembali_kartu");

//     // // absensi rest log
//     // Route::post('/absensi-rest-log', [AbsensiRestLogAjax::class, 'search'])->name("ajax.ga.sistem-tracking.absensirestlog.search");
//     Route::get('/blacklist/show', [BlacklistAjax::class, 'show'])->name('ajax.ga.sistem-tracking.blacklist.show');
//   });
// });
