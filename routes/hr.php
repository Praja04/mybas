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

    // HR Dashboard
    Route::get('/hr/hrdashboard', 'HR\HrDashboardController@index');
    Route::get('/hr/hrdashboard/data', 'HR\HrDashboardController@data');
    Route::get('/hr/hrdashboard/export', 'HR\HrDashboardController@export');
    Route::get('/hr/hrdashboard/wto-data', 'HR\HrDashboardController@wtoData');
    Route::get('/hr/hrdashboard/wto-export', 'HR\HrDashboardController@wtoExport');
    Route::get('/hr/hrdashboard/wto-chart', 'HR\HrDashboardController@wtoChartData');
    Route::get('/hr/hrdashboard/wto-top-lembur', 'HR\HrDashboardController@wtoTopLembur');
    Route::get('/hr/hrdashboard/wto-names', 'HR\HrDashboardController@wtoNames');
});
