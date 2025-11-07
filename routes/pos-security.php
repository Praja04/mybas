<?php

use Illuminate\Support\Facades\Route;

Route::prefix('pos-security')->group(function () {
    Route::group(['middleware' => ['auth', 'access_log', 'rules']], function () {
        Route::get('/dashboard', 'PosSecurityController@index')->name('pos-security.dashboard');

        Route::get('/formulir', 'PosSecurityController@form')->name('pos-security.formulir');

        Route::get('/formulir/tamu', 'PosSecurity\Web\Form\TamuFormController@index')->name('pos-security.formulir.tamu');

        Route::get('/formulir/supplier', 'PosSecurityController@formSupplier')->name('pos-security.formulir.supplier');

        Route::get('/absensi/display', 'PosSecurityController@display')->name('pos-security.absensi.display');

        Route::get('/absensi/visitor', 'PosSecurityController@absensiVisitor')->name('pos-security.absensi.visitor');
    });
});
