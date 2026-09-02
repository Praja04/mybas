<?php

use App\Http\Controllers\PosSecurityController;
use App\Http\Controllers\PosSecurity\GaKantongParkirController;
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

            Route::prefix('kantong-parkir')->group(function () {
                Route::get('/', [GaKantongParkirController::class, 'index'])->name('pos-security.master.kantong-parkir.index');

                // Zones API
                Route::get('/zones', [GaKantongParkirController::class, 'getZones'])->name('pos-security.kantong-parkir.zones.get');
                Route::post('/zones/store', [GaKantongParkirController::class, 'storeZone'])->name('pos-security.kantong-parkir.zones.store');
                Route::get('/zones/show/{id}', [GaKantongParkirController::class, 'showZone'])->name('pos-security.kantong-parkir.zones.show');
                Route::delete('/zones/destroy/{id}', [GaKantongParkirController::class, 'destroyZone'])->name('pos-security.kantong-parkir.zones.destroy');

                // Slots API
                Route::get('/slots', [GaKantongParkirController::class, 'getSlots'])->name('pos-security.kantong-parkir.slots.get');
                Route::post('/slots/store', [GaKantongParkirController::class, 'storeSlot'])->name('pos-security.kantong-parkir.slots.store');
                Route::post('/slots/generate', [GaKantongParkirController::class, 'generateSlots'])->name('pos-security.kantong-parkir.slots.generate');
                Route::get('/slots/show/{id}', [GaKantongParkirController::class, 'showSlot'])->name('pos-security.kantong-parkir.slots.show');
                Route::delete('/slots/destroy/{id}', [GaKantongParkirController::class, 'destroySlot'])->name('pos-security.kantong-parkir.slots.destroy');

                // Assignment & Status History API
                Route::post('/assignment/assign', [GaKantongParkirController::class, 'assignParking'])->name('pos-security.kantong-parkir.assignment.assign');
                Route::post('/assignment/release/{id}', [GaKantongParkirController::class, 'releaseParking'])->name('pos-security.kantong-parkir.assignment.release');
            });
        });
    });
});
