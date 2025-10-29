<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;

Route::group(['middleware' => ['auth', 'rules', 'access_log']], function () {

    Route::get('/', 'HomeController@index')->name('home');
    Route::get('/home', 'HomeController@index')->name('home');

    Route::post('logout', 'Auth\LoginController@logout');

    //	Route::get('/pkw', 'PKWController@belum_terdaftar');
    Route::get('/pkw/pkwt', 'PKWController@pkwt');
    Route::get('/pkw/pkwtt', 'PKWController@pkwtt');

    Route::get('pkw/form-pa', 'PKWFormPAController@index');
    Route::post('pkw/form-pa/create', 'PKWFormPAController@create');
    Route::post('pkw/form-pa/store', 'PKWFormPAController@store');

    Route::get('pkw/form-pa/approve-page', 'PKWFormPAController@approvePage');

    Route::post('pkw/form-pa/approve', 'PKWFormPAController@approve');

    Route::post('pkw/form-pa/get-all', 'PKWFormPAController@getAll');
    Route::post('pkw/form-pa/get-filled', 'PKWFormPAController@getFilled');
    Route::get('pkw/form-pa/get/{id_form}', 'PKWFormPAController@getOne');

    Route::post('/pkw/karyawan/upload', 'PKWKaryawanController@upload');
    Route::post('/pkw/karyawan/upload-images', 'PKWKaryawanController@uploadImages');
    Route::post('/pkw/karyawan/upload-image', 'PKWKaryawanController@uploadImage');

    Route::post('/pkw/get-all/{jenis}', 'PKWController@getAll');
    Route::post('/pkw/start', 'PKWController@start');

    Route::post('/pkw/karyawan/update', 'PKWKaryawanController@update');

    Route::get('/pkw/karyawan/export-payroll/{ids}', 'PKWKaryawanController@exportPayroll');
    Route::get('/pkw/karyawan/export-iris/{ids}', 'PKWKaryawanController@exportIris');
    Route::get('/pkw/karyawan/export-bpjs/{ids}', 'PKWKaryawanController@exportBPJS');
    Route::get('/pkw/karyawan/export-bpjs-tk/{ids}', 'PKWKaryawanController@exportBPJSTK');
    Route::get('/pkw/karyawan/export-bank-mandiri/{ids}', 'PKWKaryawanController@exportBankMandiri');
    Route::get('/pkw/karyawan/export-master-all/{ids}', 'PKWKaryawanController@exportMasterAll');
    Route::get('/pkw/karyawan/export-pkwtt/{ids}', 'PKWKaryawanController@exportPKWTT');
    Route::post('/pkw/karyawan/upload-secure-access', 'PKWKaryawanController@uploadSecureAccess');

    Route::get('/pkw/karyawan', 'PKWKaryawanController@karyawan');
    Route::get('/pkw/karyawan/calon', 'PKWKaryawanController@calon');
    Route::post('/pkw/karyawan/set-divisi-bagian-jabatan', 'PKWKaryawanController@setDivisiBagianJabatan');
    Route::post('/pkw/karyawan/set-group-admin', 'PKWKaryawanController@setGroupAdmin');
    Route::post('/pkw/karyawan/send-to-recruitment', 'PKWKaryawanController@sendToRecruitment');
    Route::post('/pkw/karyawan/{status}', 'PKWKaryawanController@getKaryawan');
    Route::delete('/pkw/karyawan/delete/{id}', 'PKWKaryawanController@deleteKaryawan');

    Route::get('/bagian/get-by-divisi/{divisiId}', 'PKWBagianController@getByDivisi');
    Route::get('/jabatan/get-by-bagian/{bagianId}', 'PKWJabatanController@getByBagian');

    Route::get('/master/approval', 'PKWApprovalController@index');
    Route::post('/master/approval/store', 'PKWApprovalController@store');

    Route::get('/hr/kbbm', 'KbbmController@index');
    Route::post('/hr/kbbm/upload', 'KbbmController@upload');

    Route::get('/hr/data-tamu', 'DataTamuController@manage');
    Route::post('/hr/data-tamu/upload', 'DataTamuController@upload');

    Route::get('/hr/pembagian', 'PembagianController@index');
    Route::get('/hr/pembagian-karyawan/get/{id_pembagian}', 'PembagianKaryawanController@get');
    Route::post('/hr/pembagian/create', 'PembagianController@create');
    Route::delete('/hr/pembagian/delete/{id}', 'PembagianController@delete');
    Route::post('/hr/pembagian-karyawan/upload', 'PembagianKaryawanController@upload');

    Route::get('/hr/karyawan', 'HR\KaryawanController@index');
    Route::post('/hr/karyawan/all', 'HR\KaryawanController@all');
    Route::post('/hr/karyawan/compare-data', 'HR\KaryawanController@compareData');
    Route::post('/hr/karyawan/syncronize-data', 'HR\KaryawanController@syncronizeData');
    Route::post('/hr/karyawan/update/create-batch', 'HR\KaryawanController@createUpdateBatch');
    Route::get('/hr/karyawan/update/get-batch', 'HR\KaryawanController@getUpdateBatch');
    Route::get('/hr/karyawan/update/get-update/{batchId}', 'HR\KaryawanController@getUpdate');
    Route::post('/hr/karyawan/update/generate-update', 'HR\KaryawanController@generateUpdate');

    Route::get('/sigra/master-vendor', 'Sigra\MasterVendorController@index');
    Route::post('/sigra/master-vendor/store', 'Sigra\MasterVendorController@store');
    Route::get('/sigra/master-vendor/get/{id}', 'Sigra\MasterVendorController@get');
    Route::post('/sigra/master-vendor/update', 'Sigra\MasterVendorController@update');
    Route::delete('/sigra/master-vendor/delete/{id}', 'Sigra\MasterVendorController@delete');

    Route::get('/sigra/kontrak-vendor', 'Sigra\KontrakVendorController@index');
    Route::get('/sigra/kontrak-vendor/get-all', 'Sigra\KontrakVendorController@getAll')->name('sigra.kontrak-vendor.get-all');
    Route::post('/sigra/kontrak-vendor/create', 'Sigra\KontrakVendorController@create')->name('sigra.kontrak-vendor.create');
    Route::post('/sigra/kontrak-vendor/update', 'Sigra\KontrakVendorController@update')->name('sigra.kontrak-vendor.update');
    Route::get('/sigra/kontrak-vendor/get-kontrak/{id}', 'Sigra\KontrakVendorController@getKontrak');
    Route::get('/sigra/kontrak-vendor/kontrak/get/{id}', 'Sigra\KontrakVendorController@get');
    Route::delete('/sigra/kontrak-vendor/kontrak/delete/{id}', 'Sigra\KontrakVendorController@delete');
    Route::get('/sigra/kontrak-vendor/get-attachments/{id}', 'Sigra\KontrakVendorController@getAttachments');
    Route::get('/sigra/kontrak-vendor/export', 'Sigra\KontrakVendorController@exportKontrak')->name('sigra.kontrak-vendor.export');
    Route::post('/sigra/kontrak-vendor/import-excel', 'Sigra\KontrakVendorController@import_excel')->name('sigra.kontrak-vendor.import');
    Route::post('/sigra/kontrak-vendor/set-status', 'Sigra\KontrakVendorController@ubahStatus');

    Route::get('/sigra/legalitas', 'Sigra\LegalitasController@index');
    Route::get('/sigra/legalitas/get-sertifikasi/{id}', 'Sigra\LegalitasController@getSertifikasi');
    Route::post('/sigra/legalitas/store', 'Sigra\LegalitasController@store');
    Route::post('/sigra/legalitas/create-sertifikat', 'Sigra\LegalitasController@createSertifikat')->name('sigra.legalitas.create-sertifikat');
    Route::post('/sigra/legalitas/edit-sertifikat', 'Sigra\LegalitasController@editSertifikat')->name('sigra.legalitas.edit-sertifikat');
    Route::get('/sigra/legalitas/get-attachments/{id}', 'Sigra\LegalitasController@getAttachments');
    Route::get('/sigra/legalitas/get-all', 'Sigra\LegalitasController@getAll')->name('sigra.legalitas.get-all');
    Route::get('/sigra/legalitas/get/{id}', 'Sigra\LegalitasController@get');
    Route::post('/sigra/legalitas/update', 'Sigra\LegalitasController@update');
    Route::delete('/sigra/legalitas/delete/{id}', 'Sigra\LegalitasController@delete');
    Route::delete('/sigra/legalitas/sertifikasi/delete/{id}', 'Sigra\SertifikasiLegalitasController@delete');
    Route::post('/sigra/legalitas/set-status', 'Sigra\LegalitasController@setStatus');
    Route::get('/sigra/legalitas/sertifikasi/get/{id}', 'Sigra\SertifikasiLegalitasController@get');
    // sigra legalitas export
    Route::get('/sigra/legalitas/export-legalitas', 'Sigra\LegalitasController@exportLegalitas')->name('sigra.export.legalitas');


    Route::get('/sigra/operasional', 'Sigra\OperasionalController@index');
    Route::get('/sigra/operasional/get-sertifikasi/{id}', 'Sigra\OperasionalController@getSertifikasi');
    Route::post('/sigra/operasional/store', 'Sigra\OperasionalController@store');
    Route::post('/sigra/operasional/create-sertifikat', 'Sigra\OperasionalController@createSertifikat')->name('sigra.operasional.create-sertifikat');
    Route::post('/sigra/operasional/edit-sertifikat', 'Sigra\OperasionalController@editSertifikat')->name('sigra.operasional.edit-sertifikat');
    Route::get('/sigra/operasional/get-attachments/{id}', 'Sigra\OperasionalController@getAttachments');
    Route::get('/sigra/operasional/get-all', 'Sigra\OperasionalController@getAll')->name('sigra.operasional.get-all');
    Route::get('/sigra/operasional/get/{id}', 'Sigra\OperasionalController@get');
    Route::post('/sigra/operasional/update', 'Sigra\OperasionalController@update');
    Route::delete('/sigra/operasional/delete/{id}', 'Sigra\OperasionalController@delete');
    Route::delete('/sigra/operasional/sertifikasi/delete/{id}', 'Sigra\SertifikasiOperasionalController@delete');
    Route::post('/sigra/operasional/set-status', 'Sigra\OperasionalController@setStatus');
    Route::get('/sigra/operasional/sertifikasi/get/{id}', 'Sigra\SertifikasiOperasionalController@get');
    Route::get('/sigra/operasional/export', 'Sigra\OperasionalController@export');

    Route::get('/sigra/sni-mi-instan', 'Sigra\SNIMiInstanController@index');
    Route::get('/sigra/sni-mi-instan/get-all', 'Sigra\SNIMiInstanController@getAll')->name('sigra.sni-mi-instan.get-all');
    Route::post('/sigra/sni-mi-instan/store', 'Sigra\SNIMiInstanController@store')->name('sigra.sni-mi-instan.store');
    Route::get('/sigra/sni-mi-instan/get-sertifikat/{id}', 'Sigra\SNIMiInstanController@getSertifikat');
    Route::post('/sigra/sni-mi-instan/update', 'Sigra\SNIMiInstanController@update');
    Route::get('/sigra/sni-mi-instan/get/{id}', 'Sigra\SNIMiInstanController@get');
    Route::delete('/sigra/sni-mi-instan/delete/{id}', 'Sigra\SNIMiInstanController@delete');
    Route::post('/sigra/sni-mi-instan/create-sertifikat', 'Sigra\SNIMiInstanController@createSertifikat')->name('sigra.sni-mi-instan.create-sertifikat');
    Route::post('/sigra/sni-mi-instan/edit-sertifikat', 'Sigra\SNIMiInstanController@editSertifikat')->name('sigra.sni-mi-instan.edit-sertifikat');
    Route::get('/sigra/sni-mi-instan/get-attachments/{id}', 'Sigra\SNIMiInstanController@getAttachments');
    Route::get('/sigra/sni-mi-instan/sertifikat/get/{id}', 'Sigra\SertifikatSNIMiInstanController@get');
    Route::delete('/sigra/sni-mi-instan/sertifikat/delete/{id}', 'Sigra\SertifikatSNIMiInstanController@delete');
    Route::post('/sigra/sni-mi-instan/set-status', 'Sigra\SNIMiInstanController@setStatus');

    Route::get('/sigra/bdkt-mi-instan', 'Sigra\BDKTMiInstanController@index');
    Route::get('/sigra/bdkt-mi-instan/get-all', 'Sigra\BDKTMiInstanController@getAll')->name('sigra.bdkt-mi-instan.get-all');
    Route::post('/sigra/bdkt-mi-instan/store', 'Sigra\BDKTMiInstanController@store')->name('sigra.bdkt-mi-instan.store');
    Route::get('/sigra/bdkt-mi-instan/get-sertifikat/{id}', 'Sigra\BDKTMiInstanController@getSertifikat');
    Route::post('/sigra/bdkt-mi-instan/update', 'Sigra\BDKTMiInstanController@update');
    Route::get('/sigra/bdkt-mi-instan/get/{id}', 'Sigra\BDKTMiInstanController@get');
    Route::delete('/sigra/bdkt-mi-instan/delete/{id}', 'Sigra\BDKTMiInstanController@delete');
    Route::post('/sigra/bdkt-mi-instan/create-sertifikat', 'Sigra\BDKTMiInstanController@createSertifikat')->name('sigra.bdkt-mi-instan.create-sertifikat');
    Route::post('/sigra/bdkt-mi-instan/edit-sertifikat', 'Sigra\BDKTMiInstanController@editSertifikat')->name('sigra.bdkt-mi-instan.edit-sertifikat');
    Route::get('/sigra/bdkt-mi-instan/get-attachments/{id}', 'Sigra\BDKTMiInstanController@getAttachments');
    Route::get('/sigra/bdkt-mi-instan/sertifikat/get/{id}', 'Sigra\SertifikatBDKTMiInstanController@get');
    Route::delete('/sigra/bdkt-mi-instan/sertifikat/delete/{id}', 'Sigra\SertifikatBDKTMiInstanController@delete');
    Route::post('/sigra/bdkt-mi-instan/set-status', 'Sigra\BDKTMiInstanController@setStatus');

    Route::get('/sigra/sh-bahan-baku', 'Sigra\SHBahanBakuController@index');
    Route::get('/sigra/sh-bahan-baku/get-all', 'Sigra\SHBahanBakuController@getAll')->name('sigra.sh-bahan-baku.get-all');
    Route::post('/sigra/sh-bahan-baku/store', 'Sigra\SHBahanBakuController@store')->name('sigra.sh-bahan-baku.store');
    Route::get('/sigra/sh-bahan-baku/get-sertifikat/{id}', 'Sigra\SHBahanBakuController@getSertifikat');
    Route::post('/sigra/sh-bahan-baku/update', 'Sigra\SHBahanBakuController@update');
    Route::get('/sigra/sh-bahan-baku/get/{id}', 'Sigra\SHBahanBakuController@get');
    Route::delete('/sigra/sh-bahan-baku/delete/{id}', 'Sigra\SHBahanBakuController@delete');
    Route::post('/sigra/sh-bahan-baku/create-sertifikat', 'Sigra\SHBahanBakuController@createSertifikat')->name('sigra.sh-bahan-baku.create-sertifikat');
    Route::post('/sigra/sh-bahan-baku/edit-sertifikat', 'Sigra\SHBahanBakuController@editSertifikat')->name('sigra.sh-bahan-baku.edit-sertifikat');
    Route::get('/sigra/sh-bahan-baku/get-attachments/{id}', 'Sigra\SHBahanBakuController@getAttachments');
    Route::get('/sigra/sh-bahan-baku/sertifikat/get/{id}', 'Sigra\SertifikatSHBahanBakuController@get');
    Route::delete('/sigra/sh-bahan-baku/sertifikat/delete/{id}', 'Sigra\SertifikatSHBahanBakuController@delete');
    Route::post('/sigra/sh-bahan-baku/set-status', 'Sigra\SHBahanBakuController@setStatus');

    Route::get('/sigra/md-mi-instan', 'Sigra\MDMiInstanController@index');
    Route::get('/sigra/md-mi-instan/get-all', 'Sigra\MDMiInstanController@getAll')->name('sigra.md-mi-instan.get-all');
    Route::post('/sigra/md-mi-instan/store', 'Sigra\MDMiInstanController@store')->name('sigra.md-mi-instan.store');
    Route::get('/sigra/md-mi-instan/get-sertifikat/{id}', 'Sigra\MDMiInstanController@getSertifikat');
    Route::post('/sigra/md-mi-instan/update', 'Sigra\MDMiInstanController@update');
    Route::get('/sigra/md-mi-instan/get/{id}', 'Sigra\MDMiInstanController@get');
    Route::delete('/sigra/md-mi-instan/delete/{id}', 'Sigra\MDMiInstanController@delete');
    Route::post('/sigra/md-mi-instan/create-sertifikat', 'Sigra\MDMiInstanController@createSertifikat')->name('sigra.md-mi-instan.create-sertifikat');
    Route::post('/sigra/md-mi-instan/edit-sertifikat', 'Sigra\MDMiInstanController@editSertifikat')->name('sigra.md-mi-instan.edit-sertifikat');
    Route::get('/sigra/md-mi-instan/get-attachments/{id}', 'Sigra\MDMiInstanController@getAttachments');
    Route::get('/sigra/md-mi-instan/sertifikat/get/{id}', 'Sigra\SertifikatMDMiInstanController@get');
    Route::delete('/sigra/md-mi-instan/sertifikat/delete/{id}', 'Sigra\SertifikatMDMiInstanController@delete');
    Route::post('/sigra/md-mi-instan/set-status', 'Sigra\MDMiInstanController@setStatus');

    Route::get('cek-suhu/report/export-excel/{tanggal}', 'CekSuhuController@reportExportExcel')->name('cek-suhu.report.export-excel');
    Route::get('cek-suhu/report/{tanggal?}/{divisi?}/{shift?}', 'CekSuhuController@report')->name('cek-suhu.report');

    // route surat izin operasional SIO
    Route::get('/sigra/sio', 'Sigra\SioController@index');
    Route::post('/sigra/sio/tambah-perizinan', 'Sigra\SioController@tambahPerizinan')->name('sigra.sio.tambah.perizinan');
    Route::get('/sigra/sio/get-all', 'Sigra\SioController@getAll')->name('sigra.sio.get-all');
    Route::post('/sigra/sio/buat-sertifikat', 'Sigra\SioController@buatSertifikat')->name('sigra.sio.buat-sertifikat');
    Route::get('/sigra/sio/get-sertifikat/{id}', 'Sigra\SioController@getSertifikat');
    Route::post('/sigra/sio/ubah-sertifikat', 'Sigra\SioController@ubahSertifikat')->name('sigra.sio.ubah-sertifikat');
    Route::get('/sigra/sio/get-attachments/{id}', 'Sigra\SioController@getAttachments');
    // get sertifikasi sio
    Route::get('/sigra/sio/sertifikasi/get/{id}', 'Sigra\SioController@getSertifikasi');
    // get perizinan sio
    Route::get('/sigra/sio/get/{id}', 'Sigra\SioController@getPerizinan');
    Route::post('/sigra/sio/update', 'Sigra\SioController@update');
    Route::delete('/sigra/sio/delete/{id}', 'Sigra\SioController@deletePerizinan');
    Route::delete('/sigra/sio/sertifikasi/delete/{id}', 'Sigra\SioController@deleteSertifikasi');
    // export bentar dulu
    Route::post('/sigra/sio/set-status', 'Sigra\SioController@setStatus');
    Route::get('/sigra/sio/export-sio', 'Sigra\SioController@exportSio')->name('sigra.export.sio');


    // balikin ke attachmentcontroller kalo mau store ke google
    Route::post('/attachment/upload', 'LocalAttachmentController@upload')->name('attachment.upload');
    Route::get('/attachment/download/{id}', 'LocalAttachmentController@download');
    Route::get('/attachment/generate', 'LocalAttachmentController@generateTransactionId')->name('attachment.generate-transaction-id');

    Route::get('/checklist/schedule', 'ChecklistScheduleController@index');
    Route::post('/master/checklist_schedule/store', 'ChecklistScheduleController@store');

    Route::get('/checklist/fly_catcher/report/', 'ChecklistFlyCatcherController@report');
    Route::get('/checklist/fly_catcher/report/data/{year}/{month}/{week}/{dept?}', 'ChecklistFlyCatcherController@reportData');
    Route::get('/checklist/fly_catcher/report/chart-data/{year}/{month}/{dept?}', 'ChecklistFlyCatcherController@chartData')->name('fly-catcher-report-chart');

    Route::get('/ite/manage/cameras', 'Ite\CameraController@index');
    Route::get('/ite/manage/cameras/get-all-ip', 'Ite\CameraController@get_all_ip');
    Route::post('/ite/manage/camera/change-image', 'Ite\CameraController@change_image');
    Route::get('/ite/manage/cameras/offline-cameras/{check_id}', 'Ite\CameraController@offlineCameras');
    Route::post('/ite/manage/cameras/create-check', 'Ite\CameraController@createCheck');
    Route::post('/ite/manage/cameras/finish-check', 'Ite\CameraController@finishCheck');
    Route::post('/ite/manage/camera/check', 'Ite\CameraController@offline_check');
    Route::get('/ite/manage/camera/{camera_id}/{nvr}/{channel_number}', 'Ite\CameraController@getCamera');
    Route::post('/ite/manage/cameras/import/excel', 'Ite\CameraController@import_excel');
    Route::post('/ite/manage/camera/change', 'Ite\CameraController@changeCamera');
    Route::delete('/ite/manage/camera/delete/{id}', 'Ite\CameraController@delete');

    Route::get('/ite/orders', 'Ite\OrderController@index');
    Route::get('/ite/orders/all', 'Ite\OrderController@all');
    Route::get('/ite/order/item/{id}', 'Ite\OrderItemController@get');
    Route::post('/ite/order/item/submit-pr', 'Ite\OrderItemController@submitPR');
    Route::post('/ite/order/item/submit-po', 'Ite\OrderItemController@submitPO');
    Route::post('/ite/order/item/submit-arrive', 'Ite\OrderItemController@submitArrive');
    Route::post('/ite/order/item/submit-diambil', 'Ite\OrderItemController@submitDiambil');

    Route::get('/ite/projects', 'Ite\ProjectController@index');

    Route::get('/display/pengambilan-id-card', 'DisplayController@pengambilanIdCard');
    Route::post('/display/pengambilan-id-card/scan', 'PKWKaryawanController@pengambilanIdCardScan');
    Route::post('/display/pengambilan-id-card/submit', 'PKWKaryawanController@pengambilanIdCardSubmit');

    Route::get('/permission/auth-permission/', 'AuthPermissionController@index');
    Route::post('/permission/auth-permission/store', 'AuthPermissionController@store');
    Route::get('/permission/auth-group/', 'AuthGroupController@index');
    Route::post('/permission/auth-group/store', 'AuthGroupController@store');
    Route::post('/permission/auth-group/get-permissions', 'AuthGroupController@get_permissions');
    Route::post('/permission/auth-group/change-permissions', 'AuthGroupController@change_permissions');
    // tambah get permissions di master user 
    // Route::post('/permission/auth-group/get-permissions', 'AuthGroupController@get_permissions');

    Route::get('/pme/monthly-bill', 'PME\MonthlyBillController@index');
    Route::post('/pme/monthly-bill/store', 'PME\MonthlyBillController@store');
    Route::delete('/pme/monthly-bill/delete/{id}', 'PME\MonthlyBillController@delete');
    Route::get('/pme/monthly-bill/get/{id}', 'PME\MonthlyBillController@get');
    Route::patch('/pme/monthly-bill/update', 'PME\MonthlyBillController@update');

    Route::get('/pme/transfer-energy', 'PME\TransferEnergyController@index');
    Route::post('/pme/transfer-energy/store', 'PME\TransferEnergyController@store');
    Route::delete('/pme/transfer-energy/delete/{id}', 'PME\TransferEnergyController@delete');

    Route::get('/pme/data-log', 'PME\DataLogController@index');
    Route::get('/pme/data-log/generate/{tanggal}', 'PME\DataLogController@generate');
    Route::get('/pme/data-log/generate2/{tanggal}/{shift}', 'PME\DataLogController@generate2');
    Route::get('/pme/data-log-2', 'PME\DataLogController@dataLog2');

    Route::get('/men/master-material-type', 'MEN\MasterMaterialTypeController@index');
    Route::get('/men/master-material', 'MEN\MasterMaterialController@index');
    Route::post('/men/master-material/upload', 'MEN\MasterMaterialController@upload');
    Route::post('/men/master-material/all', 'MEN\MasterMaterialController@all');
    Route::get('/men/master-material/get-expired', 'MEN\MasterMaterialController@getExpired');

    Route::get('/men/master-group', 'MEN\MasterGroupController@index');
    Route::get('/men/master-notif', 'MEN\MasterNotifController@index');

    //PPIC NOODLE 1 //
    Route::prefix('noodle_1/ppic/')->group(function () {
        Route::get('index', 'Noodle_1\PPICController@index');
        Route::get('supposed', 'Noodle_1\PPICController@supposed');
    });

    //PERIZINAN DOKUMEN //

    Route::post('internal_memo/logout', 'InternalMemo\InternalMemoController@logout');
    Route::get('test', function () {
        return view('internal_memo.notifikasi');
    });

    Route::prefix('internal_memo/menu/')->group(function () {
        Route::get('index', 'InternalMemo\InternalMemoController@index');
        Route::get('buat_dokumen', 'InternalMemo\InternalMemoController@buat_dokumen');
        Route::PATCH('penerima', 'InternalMemo\InternalMemoController@penerima');
        Route::post('post_dokumen', 'InternalMemo\InternalMemoController@post_dokumen');
        Route::get('history_dokumen/{nik}', 'InternalMemo\InternalMemoController@history_dokumen');
        Route::get('detail_dokumen/{nik}/{id}', 'InternalMemo\InternalMemoController@detail_dokumen');
        Route::get('add_email', 'InternalMemo\InternalMemoController@get_email');
        Route::get('outstanding/{nik}', 'InternalMemo\InternalMemoController@outstanding');
        Route::get('form_ttd/{id}/{sub_kategori}/{nik}/{status}', 'InternalMemo\InternalMemoController@form_ttd');
        Route::patch('konfirmasi/{id}', 'InternalMemo\InternalMemoController@konfirmasi');
        Route::get('list_revisi/{status}/{nik}', 'InternalMemo\InternalMemoController@list_revisi');
        Route::get('form_revisi/{id}/{nik}', 'InternalMemo\InternalMemoController@form_revisi');
        Route::patch('update_dokumen/{id}/{nik_tujuan}', 'InternalMemo\InternalMemoController@update_dokumen');
        Route::get('tracking_dokumen/{id_im}', 'InternalMemo\InternalMemoController@tracking_dokumen');
        Route::get('export_pdf/{id_im}', 'InternalMemo\InternalMemoController@export_pdf');
        Route::PATCH('read_notif/{id}', 'InternalMemo\InternalMemoController@read_notif');
        Route::get('read_all_notif/{nik}', 'InternalMemo\InternalMemoController@read_all_notif');
        Route::PATCH('post_email/{id}', 'InternalMemo\InternalMemoController@post_email');
    });

    Route::prefix('internal_memo/ajax/')->group(function () {
        Route::POST('post_nama/{isi}', 'InternalMemo\InternalMemoController@post_nama');
    });
});

Route::group(['middleware' => ['access_log']], function () {

    Route::get('login', 'Auth\LoginController@showLoginForm')->name('login');
    Route::post('login', 'Auth\LoginController@authenticate');

    Route::get('/form-data-karyawan', 'PKWKaryawanController@formDataKaryawan');
    Route::post('/form-data-karyawan/store', 'PKWKaryawanController@storeDataKaryawan');

    Route::get('/indoregion/get-regencies-by-province/{province_id}', 'IndoRegionController@getRegenciesByProvince');
    Route::get('/indoregion/get-districts-by-regency/{regency_id}', 'IndoRegionController@getDistrictsByRegency');
    Route::get('/indoregion/get-villages-by-district/{district_id}', 'IndoRegionController@getVillagesByDistrict');

    Route::get('/display', 'DisplayController@index');
    Route::get('/display/kbbm', 'DisplayController@kbbm');
    Route::get('/display/data-tamu', 'DisplayController@data_tamu');
    Route::post('/display/kbbm/scan', 'KbbmController@scan');
    Route::post('/display/periksa-tamu', 'PerikasTamuController@scan');
    Route::get('/display/secure-access', 'DisplayController@secureAccess');
    Route::get('/display/cek-suhu', 'CekSuhuController@display');
    Route::post('/display/cek-suhu/scan', 'CekSuhuController@scan');
    Route::post('/display/cek-suhu/submit', 'CekSuhuController@submit');

    Route::get('/display/pembagian-checkout', 'PembagianController@checkoutPage');
    Route::post('/display/pembagian-checkout/scan', 'PembagianController@checkoutScan');
    Route::post('/display/pembagian/scan', 'PembagianController@displayScan');
    Route::post('/display/pembagian/confirm', 'PembagianController@displayConfirm');
    Route::get('/display/pembagian/{lokasi?}', 'PembagianController@display');

    Route::get('/test/upload-image', 'TestController@uploadImage');
    Route::post('/test/upload-image/do-upload', 'TestController@doUpload');

    Route::get('/ite/daily-check', 'Ite\CameraController@dailyOfflineCheck');
    Route::get('/ite/camera/view/{camera_id}', 'Ite\CameraController@view');
    Route::get('/display/logging_machine/', 'DisplayController@logging_machine');
    Route::post('/logging_machine/scan', 'LoggingMachineController@scan');

    // // REDIRECT DASHBOARD //
    Route::get('/logging_machine/index/{nik}', 'LoggingMachineController@index');
    Route::get('/logging_machine/pilih_mesin/{nik}', 'LoggingMachineController@pilih_mesin');

    // // REDIRECT DASHBOARD MAINTENANCE //
    Route::get('/engineering/scan/', 'LoggingMachineController@login_engineering');
    Route::post('/engineering/scan_login', 'LoggingMachineController@scan_login');

    // SPV PRODUKSI //
    Route::get('/logging_machine/spv_prod', 'LoggingMachineController@spv_prod_index');
    // !! CHECKLIST SHIFT !! //
    Route::get('/logging_machine/spv_prod/checklist_shift', 'LoggingMachineController@checklist_shift');
    Route::get('/logging_machine/spv_prod/detail_shift/{rasa}/{id}', 'LoggingMachineController@detail_shift');
    Route::POST('/logging_machine/spv_prod/approve', 'LoggingMachineController@spv_approve');
    Route::get('/logging_machine/spv_prod/tracking_shift', 'LoggingMachineController@tracking_shift');

    // !! DOWNTIME MATI LAMPU !! //
    Route::patch('/logging_machine/spv_downtime', 'LoggingMachineController@spv_downtime');
    Route::patch('/logging_machine/stop_downtime', 'LoggingMachineController@stop_downtime');

    // ADMIN PRODUKSI
    Route::get('/logging_machine/adm_prod', 'LoggingMachineController@adm_prod_index');
    // !! MASTER MESIN !! //
    Route::get('/logging_machine/adm_prod/master_mesin', 'LoggingMachineController@index_master_mesin');
    Route::post('/logging_machine/adm_prod/post_master_mesin', 'LoggingMachineController@post_master_mesin');
    Route::get('/logging_machine/adm_prod/get_master_mesin/{id}', 'LoggingMachineController@get_master_mesin');
    Route::patch('/logging_machine/adm_prod/update_master_mesin/{id}', 'LoggingMachineController@update_master_mesin');
    Route::get('/logging_machine/adm_prod/delete_master_mesin/{id}', 'LoggingMachineController@delete_master_mesin');

    // !! MASTER REPAIR MESIN !! //
    Route::get('/logging_machine/adm_prod/master_mesin_repair', 'LoggingMachineController@index_master_repair');
    Route::post('/logging_machine/adm_prod/post_master_mesin_repair', 'LoggingMachineController@post_master_mesin_repair');
    Route::get('/logging_machine/adm_prod/get_master_mesin_repair/{id}/{no_mesin}', 'LoggingMachineController@get_master_mesin_repair');
    Route::patch('/logging_machine/adm_prod/update_master_mesin_repair/{id}/{no_mesin}', 'LoggingMachineController@update_master_mesin_repair');
    Route::get('/logging_machine/adm_prod/delete_master_mesin_repair/{id}', 'LoggingMachineController@delete_master_mesin_repair');

    // !! MASTER VARIAN RASA !! //
    Route::get('/logging_machine/adm_prod/master_varian', 'LoggingMachineController@index_master_varian');
    Route::post('/logging_machine/adm_prod/post_master_varian', 'LoggingMachineController@post_master_varian');
    Route::get('/logging_machine/adm_prod/get_master_varian/{id}', 'LoggingMachineController@get_master_varian');
    Route::patch('/logging_machine/adm_prod/update_master_varian/{id}', 'LoggingMachineController@update_master_varian');
    Route::get('/logging_machine/adm_prod/delete_master_varian/{id}', 'LoggingMachineController@delete_master_varian');

    // !! MASTER REASON !! //
    Route::get('/logging_machine/adm_prod/get_master_reason', 'LoggingMachineController@get_master_reason');
    Route::post('/logging_machine/adm_prod/post_master_reason', 'LoggingMachineController@post_master_reason');
    Route::get('/logging_machine/adm_prod/delete_master_reason/{id}', 'LoggingMachineController@delete_master_reason');

    // !! MASTER SAMPLING !! //
    Route::get('/logging_machine/adm_prod/master_sampling', 'LoggingMachineController@index_master_sampling');
    Route::post('/logging_machine/adm_prod/post_master_sampling', 'LoggingMachineController@post_master_sampling');
    Route::get('/logging_machine/adm_prod/get_master_sampling/{id}', 'LoggingMachineController@get_master_sampling');
    Route::patch('/logging_machine/adm_prod/update_master_sampling/{id}', 'LoggingMachineController@update_master_sampling');
    Route::get('/logging_machine/adm_prod/delete_master_sampling/{id}', 'LoggingMachineController@delete_master_sampling');

    // !! AJAX GET DATA !! //
    Route::get('/logging_machine/get_mesin/{group}', 'LoggingMachineController@get_mesin');
    Route::get('/logging_machine/get_rasa/{group}', 'LoggingMachineController@get_rasa');
    Route::get('/logging_machine/get_repair/{no_mesin}', 'LoggingMachineController@get_repair');
    Route::get('/logging_machine/get_kategori/{jenis_mesin}', 'LoggingMachineController@get_kategori');
    Route::get('/logging_machine/get_maintenance/{no_mesin}', 'LoggingMachineController@get_maintenance');
    Route::get('/logging_machine/get_reason/{kategori}', 'LoggingMachineController@get_reason');
    Route::get('/maintenance/get_data_monitoring/', 'LoggingMachineController@get_data_monitoring');
    Route::get('/maintenance/get_ajax_monitoring/', 'LoggingMachineController@ajax_monitoring');
    Route::get('/logging_machine/get_rasa_before/{no_mesin}', 'LoggingMachineController@get_rasa_before');
    Route::get('/logging_machine/get_rasa_after/{line}', 'LoggingMachineController@get_rasa_after');
    Route::get('/logging_machine/get_nama_login/{line}', 'LoggingMachineController@get_nama_login');

    // !! IMPORT EXCEL !!//
    Route::post('/adm_prod/import_master_mesin', 'LoggingMachineController@import_master_mesin');
    Route::post('/adm_prod/import_master_repair', 'LoggingMachineController@import_master_repair');
    Route::post('/adm_prod/import_master_gramatur', 'LoggingMachineController@import_master_gramatur');

    // !! REPORT MAINTENANCE !!//
    Route::get('/logging_machine/adm_prod/report', 'ReportLoggingMachineController@index');
    Route::get('/logging_machine/adm_prod/report_downtime', 'ReportLoggingMachineController@index_downtime');
    Route::POST('/logging_machine/adm_prod/report_downtime', 'ReportLoggingMachineController@result_downtime');
    Route::get('/logging_machine/adm_prod/report_mesin', 'ReportLoggingMachineController@index_mesin');
    Route::POST('/logging_machine/adm_prod/report_mesin', 'ReportLoggingMachineController@result_mesin');
    Route::get('/logging_machine/adm_prod/report_operator', 'ReportLoggingMachineController@index_operator');
    Route::POST('/logging_machine/adm_prod/report_operator', 'ReportLoggingMachineController@result_operator');
    Route::get('/logging_machine/adm_prod/report_maintenance', 'ReportLoggingMachineController@index_maintenance');
    Route::POST('/logging_machine/adm_prod/report_maintenance', 'ReportLoggingMachineController@result_maintenance');
    Route::get('/logging_machine/adm_prod/report_operator', 'ReportLoggingMachineController@index_operator');
    Route::POST('/logging_machine/adm_prod/report_operator', 'ReportLoggingMachineController@result_operator');

    // !! UPLOAD PRODUCTION NUMBER !!//
    Route::POST('/logging_machine/adm_prod/upload_prod_order', 'ReportLoggingMachineController@upload_prod_order');
    Route::get('/logging_machine/adm_prod/tracking_file', 'ReportLoggingMachineController@tracking_file');
    Route::get('/logging_machine/adm_prod/edit_file/{id}', 'ReportLoggingMachineController@edit_file');
    Route::PATCH('/logging_machine/adm_prod/update_file/{id}', 'ReportLoggingMachineController@update_file');
    Route::get('/logging_machine/adm_prod/hapus_file/{id}', 'ReportLoggingMachineController@hapus_file');
    Route::POST('/logging_machine/adm_prod/cari_file', 'ReportLoggingMachineController@cari_file');

    // !! REPORT PACKING !!//
    Route::get('/logging_machine/adm_prod/report_packing_day', 'ReportLoggingMachineController@packing_harian');
    Route::get('/logging_machine/adm_prod/report_packing_all', 'ReportLoggingMachineController@packing_all_list');
    Route::get('/logging_machine/adm_prod/report_packing_day_detail/{rasa}/{id}', 'ReportLoggingMachineController@show_packing_harian');
    Route::get('/logging_machine/adm_prod/report_varian', 'ReportLoggingMachineController@report_varian');
    Route::POST('/logging_machine/adm_prod/report_varian', 'ReportLoggingMachineController@result_report_varian');

    Route::POST('/logging_machine/adm_prod/pencarian/{kategori}', 'ReportLoggingMachineController@packing_harian_result');
    Route::POST('/logging_machine/adm_prod/pencarian_all_list/{kategori}', 'ReportLoggingMachineController@packing_all_list_result');


    // !! EXPORT EXCEL !!//
    Route::POST('/logging_machine/adm_prod/export_excel_downtime', 'ReportLoggingMachineController@export_excel_downtime');

    // !! LOGGING MACHINE MONITORING  !! //
    Route::get('/logging_machine/downtime/monitoring', 'LoggingMachineController@downtime_monitoring');
    Route::POST('/logging_machine/downtime/ajax_monitoring', 'LoggingMachineController@ajax_monitoring');

    // CRUD DOWNTIME
    Route::get('/logging_machine/downtime/{status_varian}/{no_mesin}/{nik}', 'LoggingMachineController@downtime');
    Route::post('/logging_machine/post_downtime', 'LoggingMachineController@post_downtime');
    Route::get('/logging_machine/history_downtime/{no_mesin}/{nik}', 'LoggingMachineController@history_downtime');
    Route::get('/logging_machine/detail_downtime/{id}', 'LoggingMachineController@detail_downtime');

    // CRUD INNER //
    Route::get('/logging_machine/inner/{id_logging_machine}/{nik}', 'LoggingMachineController@inner');
    Route::post('/logging_machine/post_inner', 'LoggingMachineController@post_inner');
    Route::patch('/logging_machine/update_inner/{id}', 'LoggingMachineController@update_inner');
    Route::get('/logging_machine/detail_inner/{id}', 'LoggingMachineController@detail_inner');
    Route::get('/logging_machine/inner_shift_lain/{no_mesin}', 'LoggingMachineController@inner_shift_lain');
    Route::get('/logging_machine/detail_inner_shift_lain/{id}', 'LoggingMachineController@detail_inner_shift_lain');
    Route::get('/logging_machine/detail_inner_from_history/{nik}/{tgl_pengisian}', 'LoggingMachineController@detail_inner_from_history');

    // CRUD GRAMATUR //
    Route::get('/logging_machine/gramatur/{status_varian}/{no_mesin}/{nik}', 'LoggingMachineController@gramatur');
    Route::get('/logging_machine/gramatur/history/{nik}', 'LoggingMachineController@history_sampling');
    Route::get('/logging_machine/gramatur/list_history/{status_varian}/{id_logging_machine}/{nik}', 'LoggingMachineController@list_history');
    Route::get('/logging_machine/form_input_gramatur/{status_varian}/{no_mesin}/{nik}', 'LoggingMachineController@form_input_gramatur');
    Route::get('/logging_machine/detail_input/{id}', 'LoggingMachineController@detail_input_gramatur');
    Route::post('/logging_machine/post_gramatur', 'LoggingMachineController@post_gramatur');

    // CRUD WIP //
    Route::get('/logging_machine/wip/{status_varian}/{id_logging_machine}/{nik}', 'LoggingMachineController@wip');
    Route::post('/logging_machine/post_wip', 'LoggingMachineController@post_wip');
    Route::get('/logging_machine/history_wip/{no_mesin}/{nik}', 'LoggingMachineController@history_wip');
    Route::get('/logging_machine/get_edit_wip/{id}', 'LoggingMachineController@get_edit_wip');
    Route::patch('/logging_machine/update_wip/{id}', 'LoggingMachineController@update_wip');

    // CRUD KEBERSIHAN //
    Route::get('/logging_machine/kebersihan/{status_varian}/{id_logging_machine}/{nik}', 'LoggingMachineController@kebersihan');
    Route::post('/logging_machine/post_kebersihan', 'LoggingMachineController@post_kebersihan');
    Route::get('/logging_machine/history_kebersihan/{no_mesin}/{nik}', 'LoggingMachineController@history_kebersihan');

    // HISTORY MASTER LOGGING //
    Route::get('/logging_machine/history_inner/{id}', 'LoggingMachineController@history_inner');
    Route::patch('/logging_machine/post_hasil_produksi/{id}', 'LoggingMachineController@post_hasil_produksi');
    Route::get('/logging_machine/get_hasil_produksi/{no_mesin}/{nik}/{status_varian}', 'LoggingMachineController@get_hasil_produksi');

    // MAINTENANCE //
    Route::get('/logging_machine/maintenance/{nik}', 'LoggingMachineController@dashboard_maintenance');
    Route::PATCH('/logging_machine/respon_maintenance/{id}', 'LoggingMachineController@respon_maintenance');
    Route::POST('/logout_maintenance', 'LoggingMachineController@logout_maintenance');
    Route::get('/logging_machine/all_list/{nik}', 'LoggingMachineController@all_list');
    Route::get('/logging_machine/permintaan_baru/{nik}', 'LoggingMachineController@permintaan_baru');
    Route::get('/logging_machine/get_close_request/{id}/{nik}', 'LoggingMachineController@get_close_request');
    Route::patch('/logging_machine/close_request/{id}', 'LoggingMachineController@close_request');

    // !! CLOSE OPERATOR !! //
    Route::patch('/logging_machine/close_operator/{id}', 'LoggingMachineController@close_operator');
    Route::POST('/logging_machine/get_modal_operator/{nik}', 'LoggingMachineController@get_modal_operator');
    Route::get('/logging_machine/list_for_operator/{nik}', 'LoggingMachineController@list_for_operator');
    Route::patch('/logging_machine/close_from_operator/{id}', 'LoggingMachineController@close_from_operator');


    // CRUD MASTER LOGGING //
    Route::post('/logging_machine/post_logging_master', 'LoggingMachineController@post_logging_master');
    Route::post('/logging_machine/pindah_varian_logging_master', 'LoggingMachineController@pindah_varian_logging_master');
    Route::get('/logging_machine/edit_logging_master/{status_varian}/{no_mesin}/{nik}', 'LoggingMachineController@edit_logging_master');
    Route::post('/logging_machine/update_logging_master', 'LoggingMachineController@update_logging_master');
});

Route::get('/bas_login', 'BasLoggerController@showLoginForm');
Route::post('/bas_login', 'BasLoggerController@bas_login');
Route::post('/bas_logout', 'BasLoggerController@bas_logout');
//BAS LOGGER PRODUKSI //
Route::prefix('bas_logger/operator/')->group(function () {
    Route::get('index', 'BasLoggerController@index_operator');
    Route::get('batch_identity', 'BasLoggerController@batch_identity');
    Route::get('detail_batch_identity/{id}', 'BasLoggerController@detail_batch_identity');
    Route::get('destroy_batch_identity/{id}', 'BasLoggerController@destroy_batch_identity');
    Route::POST('post_batch_identity', 'BasLoggerController@post_batch_identity');
    Route::get('batch_history', 'BasLoggerController@batch_history');
});

//BAS LOGGER ANALIS KIMIA //
Route::prefix('bas_logger/analis/')->group(function () {
    Route::get('index', 'BasLoggerController@index_analis');
});

//BAS LOGGER QC //
Route::prefix('bas_logger/qc/')->group(function () {
    Route::get('index', 'BasLoggerController@index_qc');
});

//BAS LOGGER SPV //
Route::prefix('bas_logger/spv/')->group(function () {
    Route::get('index', 'BasLoggerController@index_spv');
    Route::get('sampel_varian', 'BasLoggerController@sampel_varian');
    Route::post('post_sampel_varian', 'BasLoggerController@post_sampel_varian');
    Route::post('import_sampel_varian', 'BasLoggerController@import_sampel_varian');
    Route::post('delete_sampel_varian', 'BasLoggerController@destroy_sampel_varian');
    Route::get('edit_sampel_varian/{id}', 'BasLoggerController@edit_sampel_varian');
    Route::PATCH('update_sampel_varian/{id}', 'BasLoggerController@update_sampel_varian');
    Route::get('parameter_pengecekan', 'BasLoggerController@parameter_pengecekan');
    Route::post('post_parameter_pengecekan', 'BasLoggerController@post_parameter_pengecekan');
    Route::post('import_parameter_pengecekan', 'BasLoggerController@import_parameter_pengecekan');
    Route::get('get_sampel_parameter/{no_standar}', 'BasLoggerController@get_sampel_parameter');
    Route::get('get_kode_warna/{no_standar}', 'BasLoggerController@get_kode_warna');
    Route::get('edit_parameter_pengecekan/{id}', 'BasLoggerController@edit_parameter_pengecekan');
    Route::PATCH('update_parameter_pengecekan/{id}', 'BasLoggerController@update_parameter_pengecekan');
    Route::post('delete_parameter_pengecekan', 'BasLoggerController@destroy_parameter_pengecekan');
});

// ECAFESEDAAP BAS
Route::group(['middleware' => ['auth', 'rules', 'access_log']], function () {
    Route::get('/ecafesedaap/upload-jumlah-pesanan', 'HR\Ecafesedaap\UploadJumlahPesananController@index');
});
// Upload Excel
Route::group(['middleware' => ['auth', 'rules', 'access_log']], function () {
    Route::post('/ecafesedaap/import_excel', 'EcafeSeedapController@import_excel');
});


Route::group(['middleware' => ['auth', 'rules', 'access_log']], function () {
    Route::post('/PostPesananCatering', 'EcafeSeedapController@store');
    Route::post('/PostUpdatePesananCatering', 'EcafeSeedapController@update');
    Route::get('/ecafesedaap/update-jumlah-pesanan', 'EcafeSeedapController@showeditJumlahPesanan');
    Route::get('/ecafesedaap/edit-jumlah-pesanan', 'EcafeSeedapController@editJumlahPesanan');
    Route::post('/ecafesedaap/update-jumlah-pesanan', 'EcafeSeedapController@updateJumlahPesanan');
});

Route::group(['middleware' => ['auth', 'rules', 'access_log']], function () {
    Route::post('/PostPesananCatering', 'EcafeSeedapController@store');
    Route::get('/ecafesedaap/upload-jumlah-pesanan', 'EcafeSeedapController@index');
});
Route::group(['middleware' => ['auth', 'rules', 'access_log']], function () {
    Route::post('/PostPesananCatering', 'EcafeSeedapController@store');
    Route::post('/PostViewCatering', 'EcafeSeedapController@view');
    Route::get('/ecafesedaap/view-jumlah-pesanan', 'EcafeSeedapController@view');
    Route::post('/PencarianPesanan', 'EcafeSeedapController@PencarianPesanan');
});
Route::group(['middleware' => ['auth', 'rules', 'access_log']], function () {
    Route::get('/ecafesedaap/reporting', 'EcafeSeedapController@ReportPesananCatering');
    Route::get('/ecafesedaap/reporting/detail/{id_pesanan}', 'EcafeSeedapController@reportingDetail');
    Route::get('/ReportPesananCatering', 'EcafeSeedapController@ReportPesananCatering');
    Route::post('/PencarianReport', 'EcafeSeedapController@PencarianReport');
});
Route::group(['middleware' => ['auth', 'rules', 'access_log']], function () {
    Route::get('/ecafesedaap-scan', 'EcafeSeedapController@scanPage');
    Route::get('/ecafesedaap-scan/{kategori}', 'EcafeSeedapController@showDisplay');
});

Route::group(['middleware' => ['access_log']], function () {
    Route::get('/ecafesedaap-scan', 'EcafeSeedapController@scanPage');
    Route::get('/ecafesedaap-scan/{kategori}', 'EcafeSeedapController@showDisplay');
    Route::post('/ecafesedaap-scan/do-scan', 'EcafeSeedapController@doScan');
});

Route::group(['middleware' => ['auth', 'rules', 'access_log']], function () {
    Route::get('/ecafesedaap/upload-overtime', 'EcafeSeedapController@uploadOvertime');
    Route::post('/ecafesedaap/upload-overtime', 'EcafeSeedapController@doUploadOvertime');


    Route::prefix('loker/')->group(function () {
        Route::get('/', 'LokerController@index');
        Route::post('/post_user_loker', 'LokerController@post_user_loker');
        Route::get('cari_blok/{kategori}', 'LokerController@cari_blok');
        Route::get('cek_loker_kosong', 'LokerController@cek_loker_kosong');
        Route::get('cari_no_loker/{kode_area}/{kode_blok}', 'LokerController@cari_no_loker');
        Route::get('cek_penghuni_loker/{no_loker}/{kode_area}/{kode_blok}', 'LokerController@cek_penghuni_loker');
        Route::get('get_foto_user/{nik}', 'LokerController@get_foto_user');
        Route::get('tarik_kunci/{kode_loker}/{nik}/{keterangan}/{kode_blok}/{kode_area}', 'LokerController@tarik_kunci');
        Route::get('tarik_kunci_manual/{no_loker}/{kode_blok}/{kode_area}/{nik}/{keterangan}', 'LokerController@tarik_kunci_manual');
        Route::get('history_loker/{kategori}', 'LokerController@history_loker');
        Route::get('history_loker_karyawan/{nik}', 'LokerController@history_loker_karyawan');
        Route::get('export_history_karyawan/{nik}', 'LokerController@export_history_karyawan');
        Route::post('pencarian_history_loker', 'LokerController@pencarian_history_loker');
        Route::get('database/{kategori}', 'LokerController@database');
        Route::post('import_blok_loker', 'LokerController@import_blok_loker');
        Route::post('import_loker_user', 'LokerController@import_loker_user');
        Route::post('post_master_loker', 'LokerController@post_master_loker');
        Route::get('hapus_master_blok/{id}', 'LokerController@hapus_master_blok');
        Route::get('export_loker_spesifik/{kode_area}/{tgl_mulai}/{tgl_selesai}', 'LokerController@export_loker_spesifik');
        Route::get('last_number_loker/{kode_area}/{kode_blok}', 'LokerController@last_number_loker');
        Route::get('tandai_rusak/{kode_blok}/{kode_area}/{no_loker}', 'LokerController@tandai_rusak');
        Route::get('sudah_benar/{kode_blok}/{kode_area}/{no_loker}', 'LokerController@sudah_benar');
        Route::get('edit_loker/{kode_area}', 'LokerController@edit_loker');

        Route::get('export_excel/{kode_area}', 'LokerController@export_excel');

        Route::get('data-karyawan-belum-punya-loker/{jenis_kelamin}', 'LokerController@dataKaryawanBelumPunyaLoker');
        Route::post('data-karyawan-belum-punya-loker/store', 'LokerController@storeKaryawanBelumPunyaLoker');
        Route::get('data-karyawan-keluar-masih-punya-loker', 'LokerController@dataKaryawanMasihPunyaLoker');
        Route::post('data-karyawan-phk/copot', 'LokerController@copotKaryawanPHK');
    });

    Route::group(['middleware' => ['auth', 'rules', 'access_log']], function () {
        Route::get('/masukharilibur/upload-data-karyawan', 'MasukHariLiburController@index');
        Route::post('/masukharilibur/import-data-karyawan', 'MasukHariLiburController@import_excel')->name('import-data-karyawan');
    });

    // yang lagi dikerjakan
    Route::group(['middleware' => ['auth', 'rules', 'access_log']], function () {
        Route::get('/masukharilibur/reporting', 'MasukHariLiburController@ReportMasukHariLibur');
        Route::get('/masukharilibur/approver', 'MasukHariLiburController@Approver');
        Route::get('/masukharilibur/master_approval', 'MasukHariLiburController@masterApproval');
        Route::get('/masukharilibur/tambah_data', 'MasukHariLiburController@tambahDataMaster');
        Route::post('/masukharilibur/store_data', 'MasukHariLiburController@storeDataMaster')->name('storeDataMaster');
        Route::delete('/masukharilibur/delete_data/{id}', 'MasukHariLiburController@deleteData')->name('deleteData');
        Route::post('/masukharilibur/update_data', 'MasukHariLiburController@updateData')->name('updateData');
        Route::get('/masukharilibur/edit_data/{id}', 'MasukHariLiburController@editData')->name('editData');
        Route::post('/masukharilibur/search', 'MasukHariLiburController@masterDate')->name('searchByDate');
        Route::post('/masukharilibur/import_batch', 'MasukHariLiburController@import_batch')->name('importBatch');
        Route::get('/masukharilibur/summary', 'MasukHariLiburController@count_summary')->name('reportingSummary');
        Route::get('/masukharilibur/tambah_data', 'MasukHariLiburController@departemenApproval')->name('deptApproval');
        // import batch
    });
    Route::group(['middleware' => ['auth', 'rules', 'access_log']], function () {
        Route::get('/masukharilibur-scan', 'MasukHariLiburController@scanPage');
        Route::get('/masukharilibur-scan/{rfid}', 'MasukHariLiburController@doScan');
        Route::get('/masukharilibur/reporting/detail/{id_mhl}', 'MasukHariLiburController@reportingDetail');
        Route::get('/masukharilibur/approver/detail/{id_mhl}', 'MasukHariLiburController@ApproverDetail');
        Route::get('/masukharilibur/approve/{id}', 'MasukHariLiburController@Approve');
        Route::get('/masukharilibur/reject/{id}', 'MasukHariLiburController@Reject');
        Route::get('/masukharilibur/reporting/edit/{id}', 'MasukHariLiburController@Edit');
        Route::post('/PostEditKaryawan', 'MasukHariLiburController@update');
    });

    // buat page reset password
    Route::get('/user/edit_user', 'MasukHariLiburController@userEditPassword')->name('user.edit.password');
    Route::post('/user/update', 'MasukHariLiburController@userUpdatePassword')->name('user.update.password');


    Route::group(['middleware' => ['access_log']], function () {
        Route::post('/masukharilibur-scan/do-scan', 'MasukHariLiburController@doScan');
    });
    Route::group(['middleware' => ['access_log']], function () {
        Route::get('kirim-email', 'App\Http\Controllers\MasukHariLiburMailController@index');
    });
});

require base_path('routes/5r-system.php');
require base_path('routes/edoc.php');
require base_path('routes/hr.php');
require base_path('routes/hr-connect.php');
require base_path('routes/klinik.php');
require base_path('routes/master.php');
require base_path('routes/halo-security.php');
require base_path('routes/kedatangan-beras.php');
require base_path('routes/kedatangan-lauk.php');
require base_path('routes/pengecekan-boiler.php');

// local only
require base_path('routes/mail-testing.php');

Route::fallback(function () {
    abort(404);
});
