<?php

use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['auth', 'rules', 'access_log']], function () {
    Route::get('/hr/pembagian', 'PembagianController@index');
    Route::get('/hr/pembagian-karyawan/get/{id_pembagian}', 'PembagianKaryawanController@get');
    Route::post('/hr/pembagian/create', 'PembagianController@create');
    Route::delete('/hr/pembagian/delete/{id}', 'PembagianController@delete');
    Route::post('/hr/pembagian-karyawan/upload', 'PembagianKaryawanController@upload');
    Route::get('/hr/pembagian-karyawan-data/export/{id}', 'PembagianController@exportPembagianKaryawan');

    // Upload File MP (Master Employee)
    Route::get('/hr/upload-file-mp', 'HR\UploadFileMpController@index');
    Route::post('/hr/upload-file-mp/upload', 'HR\UploadFileMpController@upload');
    Route::get('/hr/upload-file-mp/history', 'HR\UploadFileMpController@history');
    Route::get('/hr/upload-file-mp/review/{batchId}', 'HR\UploadFileMpController@review');
    Route::post('/hr/upload-file-mp/confirm/{batchId}', 'HR\UploadFileMpController@confirm');
    Route::get('/hr/upload-file-mp/employees', 'HR\UploadFileMpController@employees');

    // Upload Working Time & Overtime
    Route::get('/hr/upload-working-time-and-overtime', 'HR\UploadWorkingTimeAndOvertimeController@index');
    Route::post('/hr/upload-working-time-and-overtime/upload', 'HR\UploadWorkingTimeAndOvertimeController@upload');
    Route::get('/hr/upload-working-time-and-overtime/history', 'HR\UploadWorkingTimeAndOvertimeController@history');
    Route::get('/hr/upload-working-time-and-overtime/review/{batchId}', 'HR\UploadWorkingTimeAndOvertimeController@review');
    Route::post('/hr/upload-working-time-and-overtime/confirm/{batchId}', 'HR\UploadWorkingTimeAndOvertimeController@confirm');
    Route::get('/hr/upload-working-time-and-overtime/confirm-status/{batchId}', 'HR\UploadWorkingTimeAndOvertimeController@confirmStatus');
    Route::get('/hr/upload-working-time-and-overtime/records', 'HR\UploadWorkingTimeAndOvertimeController@records');

    // Upload File Izin HRDASH
    Route::get('/hr/upload-file-izin-hrdash', 'HR\UploadFileIzinHrdashController@index');
    Route::post('/hr/upload-file-izin-hrdash/upload', 'HR\UploadFileIzinHrdashController@upload');
    Route::get('/hr/upload-file-izin-hrdash/history', 'HR\UploadFileIzinHrdashController@history');
    Route::get('/hr/upload-file-izin-hrdash/review/{batchId}', 'HR\UploadFileIzinHrdashController@review');
    Route::post('/hr/upload-file-izin-hrdash/confirm/{batchId}', 'HR\UploadFileIzinHrdashController@confirm');
    Route::get('/hr/upload-file-izin-hrdash/confirm-status/{batchId}', 'HR\UploadFileIzinHrdashController@confirmStatus');
    Route::get('/hr/upload-file-izin-hrdash/records', 'HR\UploadFileIzinHrdashController@records');

    // Upload File Mangkir HRDASH
    Route::get('/hr/upload-file-mangkir-hrdash', 'HR\UploadFileMangkirHrdashController@index');
    Route::post('/hr/upload-file-mangkir-hrdash/upload', 'HR\UploadFileMangkirHrdashController@upload');
    Route::get('/hr/upload-file-mangkir-hrdash/history', 'HR\UploadFileMangkirHrdashController@history');
    Route::get('/hr/upload-file-mangkir-hrdash/review/{batchId}', 'HR\UploadFileMangkirHrdashController@review');
    Route::post('/hr/upload-file-mangkir-hrdash/confirm/{batchId}', 'HR\UploadFileMangkirHrdashController@confirm');
    Route::get('/hr/upload-file-mangkir-hrdash/confirm-status/{batchId}', 'HR\UploadFileMangkirHrdashController@confirmStatus');
    Route::get('/hr/upload-file-mangkir-hrdash/records', 'HR\UploadFileMangkirHrdashController@records');
    Route::get('/hr/upload-file-mangkir-hrdash/check-orphans/{batchId}', 'HR\UploadFileMangkirHrdashController@checkOrphans');
    Route::post('/hr/upload-file-mangkir-hrdash/delete-orphans/{batchId}', 'HR\UploadFileMangkirHrdashController@deleteOrphans');

    // HR Dashboard
    Route::get('/hr/hrdashboard', 'HR\HrDashboardController@index');
    Route::get('/hr/hrdashboard/data', 'HR\HrDashboardController@data');
    Route::get('/hr/hrdashboard/export', 'HR\HrDashboardController@export');
    Route::get('/hr/hrdashboard/wto-data', 'HR\HrDashboardController@wtoData');
    Route::get('/hr/hrdashboard/wto-export', 'HR\HrDashboardController@wtoExport');
    Route::get('/hr/hrdashboard/wto-chart', 'HR\HrDashboardController@wtoChartData');
    Route::get('/hr/hrdashboard/wto-top-lembur', 'HR\HrDashboardController@wtoTopLembur');
    Route::get('/hr/hrdashboard/wto-names', 'HR\HrDashboardController@wtoNames');
    Route::get('/hr/hrdashboard/pws-groups', 'HR\HrDashboardController@pwsGroups');

    // HR Dashboard — Lost Workdays (Izin)
    Route::get('/hr/hrdashboard/izin-data', 'HR\HrDashboardController@izinData');
    Route::get('/hr/hrdashboard/izin-export', 'HR\HrDashboardController@izinExport');
    Route::get('/hr/hrdashboard/izin-names', 'HR\HrDashboardController@izinNames');
    Route::get('/hr/hrdashboard/izin-chart', 'HR\HrDashboardController@izinChartData');
    Route::get('/hr/hrdashboard/izin-top-sakit', 'HR\HrDashboardController@izinTopSakit');
    Route::get('/hr/hrdashboard/izin-top-mangkir', 'HR\HrDashboardController@izinTopMangkir');
    Route::get('/hr/hrdashboard/izin-sakit-ratio-dept', 'HR\HrDashboardController@izinSakitRatioDept');
    Route::get('/hr/hrdashboard/izin-mangkir-ratio-dept', 'HR\HrDashboardController@izinMangkirRatioDept');
});
