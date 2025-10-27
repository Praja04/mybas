<?php

use Illuminate\Support\Facades\Route;


Route::prefix('/edoc')->group(function () {
    Route::group(['middleware' => ['auth', 'access_log', 'rules']], function () {
        Route::get('/pengiriman', 'EDocLogController@pengiriman');
        Route::post('/post_pengiriman', 'EDocLogController@post_pengiriman');
    });

    //  !PRODUCTION WAJIB tambah middleware 'https'
    Route::group(['middleware' => ['auth', 'access_log', 'rules']], function () {
        Route::get('/', 'EDocLogController@index');
        Route::post('/post_kedatangan', 'EDocLogController@post_kedatangan');
        Route::get('/masterpic', 'EDocLogController@masterpic');
        Route::get('/history', 'EDocLogController@history');
        Route::get('/history/data-kedatangan', 'EDocLogController@historyDataKedatangan');
        Route::post('/post_pic', 'EDocLogController@post_pic');
        Route::get('/deletepic/{id}', 'EDocLogController@deletepic');
        Route::get('/ScanPengambilan/{rfid}', 'EDocLogController@ScanPengambilan');
        Route::get('/GetListBarang/{dept}/{jenis}', 'EDocLogController@GetListBarang');
        Route::POST('/post_pengambilan', 'EDocLogController@post_pengambilan');

        Route::get('/detailKedatangan/{id}/', 'EDocLogController@detailKedatangan');
        Route::get('/detailPengiriman/{id}/', 'EDocLogController@detailPengiriman');

        //pengiriman
        Route::get('/ScanPengiriman/{rfid}', 'EDocLogController@ScanPengiriman');
        Route::get('/ShowListPengiriman/{nik}', 'EDocLogController@ShowListPengiriman');
        Route::get('/ShowListPengiriman/{nik}', 'EDocLogController@ShowListPengiriman');
        Route::POST('/KonfirmasiPengiriman', 'EDocLogController@KonfirmasiPengiriman');

        //serah terima kurir
        Route::get('/ListSerahTerimaKurir', 'EDocLogController@ListSerahTerimaKurir');
        Route::POST('/PostSerahTerima', 'EDocLogController@PostSerahTerima');

        Route::post('/postChangeDeprtPenerima', 'EDocLogController@postChangeDeprtPenerima');
        Route::post('/postReturnBarang', 'EDocLogController@postReturnBarang');

        Route::get('remainder', function () {
            return view('mail.edoc.edoc_remainder');
        });
    });
});
