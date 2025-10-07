<?php

use Illuminate\Support\Facades\Route;

Route::prefix('master/user')->group(function () {
    Route::group(['middleware' => ['auth', 'access_log', 'rules']], function () {
        Route::get('/', 'Master\UserController@index')->name('master.user');
        Route::get('/data', 'Master\UserController@data')->name('master.user.data');
        Route::post('/store', 'Master\UserController@store')->name('master.user.store');
        Route::put('/nonaktifkan/{id}', 'Master\UserController@update')->name('master.user.nonaktifkan');
        Route::put('/ubah/{id}', 'Master\UserController@ubah')->name('master.user.ubah');
        Route::put('/prosesUbah/{id}', 'Master\UserController@prosesUbah')->name('master.user.proses');
        // tambahkan function update
    });
});
