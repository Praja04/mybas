<?php

use App\Http\Controllers\PosSecurityController;
use Illuminate\Support\Facades\Route;

Route::prefix('pos-security')->group(function () {
    Route::group(['middleware' => ['auth', 'access_log', 'rules']], function () {

        Route::get('/formulir', [PosSecurityController::class, 'form'])->name('pos-security.formulir');
        Route::get('/formulir/supplier', [PosSecurityController::class, 'formSupplier'])->name('pos-security.formulir.supplier');
        Route::get('/formulir/tamu', [PosSecurityController::class, 'formTamu'])->name('pos-security.formulir.tamu');
        Route::get('/formulir/cek-kendaraan', [PosSecurityController::class, 'formCekKendaraan'])->name('pos-security.formulir.cek.kendaraan');

        Route::get('/absensi/visitor', [PosSecurityController::class, 'absensiVisitor'])->name('pos-security.absensi.visitor');
        Route::get('/absensi/gate', [PosSecurityController::class, 'absensiGate'])->name('pos-security.absensi.gate');
        Route::get('/absensi/display', [PosSecurityController::class, 'display'])->name('pos-security.absensi.display');

        Route::get('/history/supplier', [PosSecurityController::class, 'historySupplier'])->name('pos-security.history.supplier');
        Route::get('/history/tamu', [PosSecurityController::class, 'historyVendor'])->name('pos-security.history.vendor');
        Route::get('/history/kendaraan', [PosSecurityController::class, 'historyCekKendaraan'])->name('pos-security.history.cek-kendaraan');

        Route::get('/dashboard', [PosSecurityController::class, 'dashboard'])->name('pos-security.dashboard');
        Route::get('/blacklist', [PosSecurityController::class, 'blacklist'])->name('pos-security.blacklist');
        Route::get('/kartu/kartu-aktif', [PosSecurityController::class, 'kartuAktif'])->name('pos-security.kartu-aktif');
        Route::post('/kartu/reset', [PosSecurityController::class, 'resetKartu'])->name('pos-security.kartu.reset');
        Route::get('/kartu/kartu-aktif/detail/{nomor_kartu}', [PosSecurityController::class, 'kartuAktifDetail'])->name('pos-security.kartu-aktif.detail');

        Route::get('/session-keeper', function () {
            return response()->json(['status' => 'alive', 'timestamp' => now()]);
        })->name('pos-security.session-keeper');

        Route::prefix('master')->group(function () {
            Route::prefix('security')->group(function () {
                Route::get('/', [PosSecurityController::class, 'dataSecurity'])->name('pos-security.data.security');
            });
        });
    });
});
