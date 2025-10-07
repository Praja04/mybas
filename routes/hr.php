<?php

use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['auth', 'rules', 'access_log']], function () {
    Route::get('/hr/pembagian', 'PembagianController@index');
    Route::get('/hr/pembagian-karyawan/get/{id_pembagian}', 'PembagianKaryawanController@get');
    Route::post('/hr/pembagian/create', 'PembagianController@create');
    Route::delete('/hr/pembagian/delete/{id}', 'PembagianController@delete');
    Route::post('/hr/pembagian-karyawan/upload', 'PembagianKaryawanController@upload');
    Route::get('/hr/pembagian-karyawan-data/export/{id}', 'PembagianController@exportPembagianKaryawan');
});
