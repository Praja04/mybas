<?php

use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['auth', 'rules', 'access_log']], function () {
    // get halaman kedatangan beras 
    Route::get('/dashboard/master-kedatangan-beras', 'kedatanganBeras\kedatanganBerasController@dashboard')->name('kedatangan-beras.dashboard');
    // get halaman stock beras
    Route::get('/dashboard/stock-kedatangan-beras', 'kedatanganBeras\kedatanganBerasController@index')->name('kedatangan-beras.index');
    // buat create tambah kedatangan beras
    Route::post('/dashboard/tambah-kedatangan-beras', 'kedatanganBeras\kedatanganBerasController@storeStockberas')->name('kedatangan-beras.tambah.stock');
    // get data stock dashboard
    Route::get('/dashboard/get-data-stock', 'kedatanganBeras\kedatanganBerasController@getDatastock')->name('kedatangan-beras.data.stock');
    // delete data stock
    Route::delete('/dashboard/delete-data-stock/{id}', 'kedatanganBeras\kedatanganBerasController@deletestock')->name('kedatangan-beras.data.stock-delete');
    // tambah opsi lainnya satuan berat 
    Route::post('/dashboard/tambah-satuan-berat', 'kedatanganBeras\kedatanganBerasController@tambahSatuanBerat')->name('kedatangan-beras.tambah.satuan');
    // get view jumlah beras
    Route::get('/dashboard/jumlah-stock-beras', 'kedatanganBeras\kedatanganBerasController@jumlahberas')->name('kedatangan-beras.jumlah.beras');
    // get pengambilan beras
    Route::get('/dashboard/halaman-pengambilan-beras', 'kedatanganBeras\pengambilanBerasController@halamanPengambilan')->name('kedatangan-beras.pengambilan');
    // post pengambilan beras
    Route::post('/dashboard/pengambilan-data-beras', 'kedatanganBeras\pengambilanBerasController@ambilstockBeras')->name('kedatangan-beras.ambil.stock');
    // get page pengambilan beras
    Route::get('/dashboard/halaman-pemakaian-beras', 'kedatanganBeras\PemakaianBerasController@homePemakaian')->name('kedatangan-beras.pemakaian');
    

    // ambil pemakaian beras
    Route::post('/dashboard/pengambilan-berat-beras', 'kedatanganBeras\PemakaianBerasController@penguranganStockBeras')->name('kedatangan-beras.ambil.pemakaian');
    // grafik chart report grafikKedatanganBeras
    Route::get('/dashboard/grafik-kedatangan-beras', 'kedatanganBeras\kedatanganBerasController@grafikKedatanganBeras')->name('kedatangan-beras.grafik.beras');
    // halaman table get reporting bulanan
    Route::get('/dashboard/report-table-kedatangan-beras', 'kedatanganBeras\BerasReportingController@getPageTableReporting')->name('kedatangan-beras.get-table-reporting');
    // get all data table reporting bulanan
    Route::get('/dashboard/get-all-table-reporting-beras', 'kedatanganBeras\BerasReportingController@getAllReportingBeras')->name('kedatangan-beras.get-all-beras-reporting');
    // get cetak pdf kedatangan beras
    Route::get('/dashboard/report-pdf-kedatangan-beras/{id_stock}', 'kedatanganBeras\BerasReportingController@reporting')->name('kedatangan-beras.report-pdf');
    // adjustment kedatangan beras
    Route::get('/dashboard/adjustment-laporan-kedatanganberas', 'kedatanganBeras\BerasReportingController@laporanKedatanganBeras')->name('kedatangan-beras.get.adjustment-kedatangan');
    // adjustment pengluaran beras
    Route::get('/dashboard/adjustment-laporan-pengeluaranberas', 'kedatanganBeras\BerasReportingController@laporanPengeluaranBeras')->name('kedatangan-beras.get.adjustment-pengeluaran');
    // get table stock beras by api
    Route::get('/dashboard/get-all-stock-beras-data', 'kedatanganBeras\kedatanganBerasController@getJumlahBeras')->name('cateringbas.get-all-stock-beras');
    // get table stock beras by api petugas
    Route::get('/dashboard/get-all-stock-beras-data-petugas', 'kedatanganBeras\kedatanganBerasController@getJumlahBerasPetugas')->name('cateringbas.get-all-stock-beras-petugas');
    // get table stock pengambilan by api
    Route::get('/dashboard/get-all-stock-beras-pengambilan-data', 'kedatanganBeras\pengambilanBerasController@getPengambilanBeras')->name('cateringbas.get-all-stock-pengambilan');
    // tambah data keterangan kedatangan GA
    Route::post('/dashboard/laporan-ga-adjustment-kedatangan', 'kedatanganBeras\BerasReportingController@adjustmentKedatanganBeras')->name('cateringbas.GAadjustKedatangan');
    // tambah data keterangan pengeluaran GA
    Route::post('/dashboard/laporan-ga-adjustment-pengeluaran', 'kedatanganBeras\BerasReportingController@adjustmentPengeluaranBeras')->name('cateringbas.GAadjustPengeluaran');
    // export stock beras
    Route::get('/dashboard/cetak-stock-beras', 'kedatanganBeras\kedatanganBerasController@formatExcelBeras')->name('kedatangan-beras.cetak-stock-beras');
    // export buffer stock beras
    Route::get('/dashboard/cetak-buffer-stock-beras', 'kedatanganBeras\pengambilanBerasController@formatExcelBufferStockBeras')->name('kedatangan-beras.cetak-buffer-stock-beras');
    // get data laporan GA stock beras
    Route::get('/dashboard/cetak-report-stock-beras', 'kedatanganBeras\BerasReportingController@getDateKedatanganBeras')->name('kedatangan-beras.get.cetak-report-beras');


    Route::get('/dashboard/testing-beras', 'kedatanganBeras\kedatanganBerasController@testingberas')->name('kedatangan-beras.testing-beras');
    // coba testing template email reminder
    Route::get('/dashboard/reminder-email', function () {
        return view('');
    });
});
