<?php

use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'rules'])->group(function () {
    Route::redirect('/hr-connect', '/hr-connect/dashboard');
    Route::prefix('/hr-connect')->group(function () {
        Route::get('/dashboard', 'HRConnect\DashboardController@index');
        // Masters Admin
        // [x] Bebenah route Masters Admin Department
        Route::prefix('/masters/admin')->group(function () {
            Route::get('/', 'HRConnect\MastersAdminController@index');
            Route::get('/getData', 'HRConnect\MastersAdminController@getData');
            Route::post('/store', 'HRConnect\MastersAdminController@store');
            Route::get('/show/{id}', 'HRConnect\MastersAdminController@show');
            Route::post('/{id}', 'HRConnect\MastersAdminController@update');
            Route::delete('/{id}', 'HRConnect\MastersAdminController@destroy');
        });

        // Masters User Loker
        // Route::prefix('/masters/loker-user')->group(function () {
        //     Route::get('/', 'HRConnect\LokerMasterUserController@index');
        //     Route::get('/getData', 'HRConnect\LokerMasterUserController@getData');
        //     Route::post('/store', 'HRConnect\LokerMasterUserController@store');
        //     Route::get('/show/{id}', 'HRConnect\LokerMasterUserController@show');
        //     Route::get('/get-by-nik/{nik}', 'HRConnect\LokerMasterUserController@getByNik');
        //     Route::post('/{id}', 'HRConnect\LokerMasterUserController@update');
        //     Route::delete('/{id}', 'HRConnect\LokerMasterUserController@destroy');
        //     Route::delete('/delete-by-nik/{nik}', 'HRConnect\LokerMasterUserController@destroyByNik');
        // });

        // Dep. Admin
        // [x] Bebenah route Admin Department
        Route::prefix('/dept-adm/data-karyawan')->group(function () {
            Route::get('/', 'HRConnect\AdminKaryawanController@index');
            Route::get('/getDataFloting', 'HRConnect\AdminKaryawanController@getDataFloting');
            Route::get('/getDataOkb', 'HRConnect\AdminKaryawanController@getDataOkb');
            Route::get('/getDataCart', 'HRConnect\AdminKaryawanController@getDataCart');
            Route::post('/checkout', 'HRConnect\AdminKaryawanController@checkout');
            Route::post('/setGroupCode', 'HRConnect\AdminKaryawanController@setGroupCode');
            Route::get('/template-plot-karyawan', 'HRConnect\AdminKaryawanController@templatePlotKaryawan')->name('hr-connect.admin.template-masuk');
            Route::post('/uploadExcelKaryawanMasuk', 'HRConnect\AdminKaryawanController@uploadExcelKaryawanMasuk');
            Route::get('/template-keluar', 'HRConnect\AdminKaryawanController@templateCheckoutKaryawan')->name('hr-connect.admin.template-keluar');
            Route::post('/uploadExcelKaryawanKeluar', 'HRConnect\AdminKaryawanController@uploadExcelKaryawanKeluar');
        });
        // Dep. HRD IR
        Route::prefix('/dept-hrd')->group(function () {
            Route::get('/shift-out-karyawan', 'HRConnect\AdminKaryawanController@index');
            Route::get('/pemulihan-data', 'HRConnect\HrdController@restore');
            Route::put('/pemulihan-data', 'HRConnect\HrdController@restore_data');
            Route::get('/pemulihan-data/getData', 'HRConnect\HrdController@getDataPemulihan');
            Route::get('/karyawan-keluar', 'HRConnect\HrdController@index');
            Route::get('/karyawan-keluar/getData', 'HRConnect\HrdController@getData');
            Route::post('/karyawan-keluar/update', 'HRConnect\HrdController@update');
            Route::post('/karyawan-keluar/uploadExcel', 'HRConnect\HrdController@uploadExcel');
            Route::get('/report-karyawan-keluar', 'HRConnect\HrdController@report');
            Route::get('/report-karyawan-keluar/getFilterBulanTahunFinalisasi', 'HRConnect\HrdController@getFilterBulanTahunFinalisasi')->name('hr-connect.hrd.getFilterBulanTahunFinalisasi');
            Route::get('/report-karyawan-keluar/getDataReport', 'HRConnect\HrdController@getDataReport');
        });
        // Dep. GA
        Route::prefix('/dept-ga')->group(function () {
            // Karyawan Masuk
            Route::prefix('/karyawan-masuk')->group(function () {
                Route::get('/', 'HRConnect\GAShiftInController@index');
                Route::get('/getData', 'HRConnect\GAShiftInController@getData');
                // Route::get('/updateStatus', 'HRConnect\GAShiftInController@updateStatus');
                Route::post('/updateStatus', 'HRConnect\GAShiftInController@updateStatus');
                Route::post('/uploadExcel', 'HRConnect\GAShiftInController@uploadExcel');
                Route::post('/export', 'HRConnect\GAShiftInController@exportExcel');
            });

            // [x] Goodie Bag & APD
            Route::prefix('/perlengkapan-goodie-apd')->group(function () {
                Route::get('/getData', 'HRConnect\GAGoodieApdController@getData');
                Route::get('/remaining', 'HRConnect\GAGoodieApdController@remain');
                Route::post('/updateData', 'HRConnect\GAGoodieApdController@updateData');
                Route::post('/confirmAll', 'HRConnect\GAGoodieApdController@confirmAll');
                Route::post('/updateDataDitolak', 'HRConnect\GAGoodieApdController@updateDataDitolak');
                Route::get('/', 'HRConnect\GAGoodieApdController@index')->name('ga.perlengkapan-goodie-apd');
            });

            // Karyawan Keluar
            Route::prefix('/karyawan-keluar')->group(function () {
                Route::get('/', 'HRConnect\GAShiftOutController@index')->name('ga.karyawan-keluar');
                Route::get('/getData', 'HRConnect\GAShiftOutController@getData');
                Route::post('/update', 'HRConnect\GAShiftOutController@update');
                Route::post('/export', 'HRConnect\GAShiftOutController@exportExcel');
            });
        });

        // Report
        Route::prefix('/report')->group(function () {
            Route::get('/getDataKaryawanMasuk', 'HRConnect\ReportKaryawanMasukController@getData');
            Route::get('/getDataKaryawanKeluar', 'HRConnect\ReportKaryawanKeluarController@getData');
            Route::get('/getFilterBulanTahunIn', 'HRConnect\ReportKaryawanMasukController@getFilterBulanTahun')->name('hr-connect.reportKaryawanMasuk.getFilterBulanTahunIn');
            Route::get('/getFilterBulanTahunOut', 'HRConnect\ReportKaryawanKeluarController@getFilterBulanTahun')->name('hr-connect.reportKaryawanKeluar.getFilterBulanTahunOut');
            Route::get('/karyawan-masuk', 'HRConnect\ReportKaryawanMasukController@index');
            Route::get('/karyawan-keluar', 'HRConnect\ReportKaryawanKeluarController@index');
            Route::get('/kalender-karyawan', 'HRConnect\ReportKalenderKaryawanController@index');
            Route::get('/getDataReportIn/{id}', 'HRConnect\ReportKalenderKaryawanController@getReportIn');
            Route::get('/getDataReportOut/{id}', 'HRConnect\ReportKalenderKaryawanController@getReportOut');
        });
    });
});
