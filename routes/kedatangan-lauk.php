
<?php

use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['auth', 'rules', 'access_log']], function () {
    // index kedatangan lauk
    Route::get('/cateringbas/kedatangan-lauk', 'CateringController@pengecekanJumlahPesananCatering');
    // get by api
    Route::get('/cateringbas/kedatangan-lauk/get-all/{id}', 'CateringController@getAllPesanan')->name('cateringbas.get.pesanan');
    // upload kedatangan lauk
    Route::post('/cateringbas/kedatangan-lauk/upload', 'CateringController@storeJumlahpesananCatering')->name('cateringbas.tambah.cateringpesanan');
    // hapus kedatangan lauk by id
    Route::delete('/cateringbas/pesanan/delete/{id}', 'CateringController@deletepesananCatering')->name('cateringbas.delete.pesanan');
    // edit pesanan catering
    Route::post('/cateringbas/pesanan/edit/{id}', 'CateringController@editpesananCatering')->name('cateringbas.edit.pesanan');

    Route::get('/cateringbas/edit-pesanan/{id}', 'CateringController@editpesanan')->name('cateringbas.edit.pesanans');
    // pengambilan sample catering
    Route::get('/cateringbas/pengambilan-sampel', 'CateringController@pengambilanSampleCatering');
    // pengecekan kendaraan
    Route::get('/cateringbas/pengecekaan-kendaraan', 'CateringController@cekPengirimCatering')->name('cateringbas.pengecekaan-kendaraan');
    // tambah pengirim catering
    Route::post('/cateringbas/pengirim-catering/tambah', 'CateringController@tambahpengirimCatering')->name('cateringbas.tambah.pesanan');
    // tambah data pengirim catering by api 
    Route::get('/cateringbas/pengirim-catering/get-all', 'CateringController@getdataPengirimCatering')->name('cateringbas.pengirim.catering');
    // delete pengirim catering
    Route::delete('/cateringbas/pengirim-catering/delete/{id}', 'CateringController@deletepengirimCatering')->name('cateringbas.delete.pengirim');
    // get kuesioner by id_transaksi
    Route::get('/cateringbas/kuesioner-kendaraan/{id_transaksi}', 'CateringController@kuesionerPengirimBarang')->name('cateringbas.kuesioner');
    // post kuesioner
    Route::post('/cateringbas/kuesioner-kendaraan/tambah', 'CateringController@storeKuesionerKendaraan')->name('cateringbas.store.kuesioner');
    // get id_transakasi cateringbas jumlahpesanan 
    Route::get('pesanan/{id_transaksi}', 'CateringController@dashboardkedatangancatering')->name('cateringbas.jumlahpesanan');
    // get id_transaksi pengambilan sample
    Route::get('sample/{id_transaksi}', 'CateringController@dashboardSampleCatering')->name('cateringbas.sampelpesanan');
    // get all sample by api
    Route::get('/cateringbas/sample-lauk/get-all/{id}', 'CateringController@getAllSample')->name('cateringbas.get.sample');
    // get all pesanan by api
    Route::get('/cateringbas/kedatangan-kendaraan/get-all/{id}', 'CateringController@getAllKendaraan')->name('cateringbas.get.dataKendaraan');
    // get all keusioner by api
    Route::get('/cateringbas/pengecekan-kendaraan/get-all/{id}', 'CateringController@getAllPengecekanKendaraan')->name('cateringbas.get.pengecekanKendaraan');
    // edit pesanan sampel
    Route::post('/cateringbas/sampel/edit/{id}', 'CateringController@editSampelCatering')->name('cateringbas.edit.sampel');
    // post sampel catering
    Route::post('/cateringbas/sampel-catering/upload', 'CateringController@storeSampelCatering')->name('cateringbas.tambah.sampel');
    // halaman index reporting GA
    Route::get('/cateringbas/reporting-GA', 'CateringController@reportCateringPengecekanKendaraan');
    // halaman reporting pengecekan kendaraan cateringbas
    Route::get('reporting/kendaraan/{id_transaksi}', 'CateringController@ReportKendaraan')->name('cateringbas.report.kendaraan');
    // halaman reporting kedatangan pesanan cateringbas
    Route::get('reporting/pesanan/{id_transaksi}', 'CateringController@ReportPesanan')->name('cateringbas.report.pesanan');
    // halaman reporting pengambilan sample cateringbas
    Route::get('reporting/sampel/{id_transaksi}', 'CateringController@ReportSampel')->name('cateringbas.report.sampel');
    // approve pesanan karyawan
    Route::get('/reporting/approve-pesanan/{id}', 'CateringController@ApproveReportingPesanan')->name('cateringbas.approve.reporting-pesanan');
    // approve pengambilan sample
    Route::get('/reporting/approve-sampel/{id}', 'CateringController@ApproveReportingSample')->name('cateringbas.approve.reporting-sampel');
    // approve semua reporting
    Route::get('/reporting/approve-all-transaksi-catering', 'CateringController@ApproveAll')->name('cateringbas.approve.all-transaksi');
    // approve kedatangan kendaraan
    Route::get('/reporting/approve-pengecekan-kendaraan/{id}', 'CateringController@ApprovePengecekanKendaraan')->name('cateringbas.approve.pengecekan-kendaraan');
    // kirim kedatangan catering
    Route::get('/update/kedatangan-catering/{id}', 'CateringController@kirimKedatanganCatering')->name('cateringbas.update.kedatangan');
    // kirim pengambilan sampel
    Route::get('/update/pengambilan-sampel/{id}', 'CateringController@kirimPengambilanSampel')->name('cateringbas.update.pengambilanSampel');
    // get tanggal reporting pesanan
    Route::get('/reporting/get-tanggal-pesanan', 'CateringController@getDetailDateCatering');
    // get page reporting detail
    Route::get('/cateringbas/reporting-GA-detail', 'CateringController@getapprovalreporting');
    // detail reporting API
    Route::get('/cateringbas/pengirim-catering/get-all-reporting', 'CateringController@getdataPengirimCateringDetail')->name('cateringbas.pengirim.catering-detail');
    // get page reporting detail cek kendaraan
    Route::get('reporting/detail-kendaraan/{id_transaksi}', 'CateringController@ReportKendaraanDetail')->name('cateringbas.report.kendaraan-detail');
    // halaman reporting kedatangan pesanan cateringbas
    Route::get('reporting/detail-pesanan/{id_transaksi}', 'CateringController@ReportPesananDetail')->name('cateringbas.report.pesanan-detail');
    // halaman reporting pengambilan sample cateringbas
    Route::get('reporting/detail-sampel/{id_transaksi}', 'CateringController@ReportSampelDetail')->name('cateringbas.report.sampel-detail');
    // get all data catering reporting
    Route::get('/cateringbas/reporting-kedatangan-catering-Ga', 'CateringController@getReportingGACatering')->name('cateringbas.get.reportingGa');
    // get all data user reporting
    Route::get('/cateringbas/reporting-kedatangan-catering-user', 'CateringController@getReportingUserCatering')->name('cateringbas.get.reporting-user');
    // get all data catering reporting detail
    Route::get('/cateringbas/reporting-kedatangan-catering-Ga-detail', 'CateringController@getReportingGACateringDetail')->name('cateringbas.get.reporting-GA-Detail');
    // halaman upload pesanan
    Route::get('/cateringbas/halaman-upload-pesanan', 'CateringController@halamanUploadJumlahPesanan');
    // get sum tanggal from ecafesedabas
    Route::get('/cateringbas/get-sum-qty-ecafesedap', 'CateringController@getQtyAll')->name('cateringbas.ecafesedap-total');
    // cetak mingguan pdf
    Route::get('/cateringbas/export-report-mingguan', 'CateringController@reportingMingguanBeras')->name('cateringbas.report-mingguan.ga');
    Route::post('/cateringbas/export-excel-report-mingguan', 'CateringController@exportreportingMingguanBeras')->name('cateringbas.export-report-mingguan.ga');
    // cetak api json data mingguan
    Route::get('/cateringbas/data-ecafesedaap-mingguan', 'CateringController@getDatareportingMingguanCatering')->name('cateringbas.data-report-mingguan');
    // cetak pdf
    Route::get('/cateringbas/export-pdf/{tanggal_awal}/{tanggal_akhir}', 'CateringController@exportPdf')->name('cateringbas.export-data-pdf');

    // get page reporting detail cek kedatangan
    // get page reporting detail cek sampel
    // delete pengambilan sampel cateringbas
    Route::delete('/cateringbas/sampel/delete/{id}', 'CateringController@deleteSampel')->name('cateringbas.delete.sampel');
    Route::get('/cateringbas/delete-menu-utama/{id}', 'CateringController@deleteMenuUtama')->name('cateringbas.delete.menuutama');
    Route::get('/cateringbas/delete-menu-pendamping/{id}', 'CateringController@deleteMenuPendamping')->name('cateringbas.delete.menuPendamping');
});
