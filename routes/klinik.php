<?php

use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['auth', 'rules', 'access_log']], function () {

    Route::get('/klinik/master/edit/{id}', 'KlinikController@Edit');
    Route::post('/PostEditObat', 'KlinikController@Update');
    Route::prefix('klinik/')->group(function () {
        Route::get('/dokter', 'KlinikController@dokter');
        Route::post('/save', 'KlinikController@save')->name('klinik.save');
        Route::post('/scan', 'KlinikController@scan');
        Route::get('/dashboard/{nik}', 'KlinikController@dashboard');
        Route::get('/laporan-obat', 'KlinikController@laporanObat');
        Route::get('/master-data-obat', 'KlinikController@MasterDataObat');
        Route::post('/master/import_excel', 'KlinikController@import_excel');
        Route::get('/Delete/{id}', 'KlinikController@Delete');
        Route::get('/laporan-pemeriksaan', 'KlinikController@laporanPemeriksaan');
        Route::get('/get-rekam-medis/{nik}', 'KlinikController@getRekamMedis');
        Route::post('/create-diagnosa', 'KlinikController@createDiagnosa');
        Route::post('/create-faskes', 'KlinikController@createFaskes');
        Route::post('/validate', 'KlinikController@doValidate');
        Route::get('/rekam-medis', 'KlinikController@rekamMedisPage');
        // my task update tindakan dan keterangan
        Route::post('/update-tindakan-keterangan', 'KlinikController@updateTindakanKeterangan')->name('update.tindakan.keterangan');
        Route::get('/get-keterangan/{id}', 'KlinikController@getKeterangan')->name('get.keterangan');
    });
});
