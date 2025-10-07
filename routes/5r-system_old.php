<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::prefix('5r-system')->group(function () {
    Route::group(['middleware' => ['auth', 'access_log', 'rules']], function () {
        Route::get('/', 'System5R\DashboardController@index')->name('5r-system.dashboard');
        Route::get('/master-group', 'System5R\MasterGroupController@index')->name('5r-system.master-group');
        Route::get('/master-group/data', 'System5R\MasterGroupController@data')->name('5r-system.master-group.data');
        Route::post('/master-group/store', 'System5R\MasterGroupController@store')->name('5r-system.master-group.store');
        Route::get('/master-group/by-department/{id_department}', 'System5R\MasterGroupController@byDepartment')->name('5r-system.master-group.by-department');
        Route::post('/master-group/nonaktifkan', 'System5R\MasterGroupController@nonaktifkan')->name('5r-system.master-group.nonaktifkan');

        Route::get('/master-pertanyaan', 'System5R\MasterPertanyaanController@index')->name('5r-system.master-pertanyaan');
        Route::get('/master-pertanyaan/data', 'System5R\MasterPertanyaanController@data')->name('5r-system.master-pertanyaan.data');
        Route::post('/master-pertanyaan/store', 'System5R\MasterPertanyaanController@store')->name('5r-system.master-pertanyaan.store');
        Route::post('/master-pertanyaan/update', 'System5R\MasterPertanyaanController@update')->name('5r-system.master-pertanyaan.update');
        Route::get('/master-pertanyaan/get/{id_pertanyaan}', 'System5R\MasterPertanyaanController@get')->name('5r-system.master-pertanyaan.get');
        Route::post('/master-pertanyaan/delete', 'System5R\MasterPertanyaanController@delete')->name('5r-system.master-pertanyaan.delete');

        Route::get('/penilaian/approval', 'System5R\ApprovalPenilaianController@index')->name('5r-system.approval-penilaian');
        Route::post('/penilaian/approval/submit', 'System5R\ApprovalPenilaianController@submit')->name('5r-system.approval-penilaian.submit');
        Route::get('/penilaian/approval/{id_jawaban_group}', 'System5R\ApprovalPenilaianController@view')->name('5r-system.approval-penilaian.view');
        
        Route::get('/penilaian/komplain', 'System5R\KomplainPenilaianController@index')->name('5r-system.komplain-penilaian');
        Route::post('/penilaian/komplain/submit', 'System5R\KomplainPenilaianController@submit')->name('5r-system.komplain-penilaian.submit');
        Route::get('/penilaian/komplain/{id_jawaban_group}', 'System5R\KomplainPenilaianController@view')->name('5r-system.komplain-penilaian.view');
        Route::get('/penilaian/{id_group?}', 'System5R\PenilaianController@index')->name('5r-system.penilaian');

        Route::get('/report/management', 'System5R\Report\ManagementController@index')->name('5r-system.report.management');
        Route::get('/report/committee', 'System5R\Report\CommitteeController@index')->name('5r-system.report.comittee');
        Route::post('report/detail', 'System5R\Report\ManagementController@detail')->name('5r-system.report.detail');
        
        // page master committee
        Route::get('/report/master-committee', 'System5R\Report\CommitteeController@masterCommitte')->name('5r-system.master-committee');
        // delete master group
        Route::post('/master-group/delete', 'System5R\MasterGroupController@delete')->name('5r-system.master-group.delete');
        // update master group
        Route::post('/master-group/update-persentase', 'System5R\MasterGroupController@updatePersentase')->name('5r-system.master-group.update-persentase');
        // clone master pertanyaan
        Route::post('/master-pertanyaan/clone', 'System5R\MasterPertanyaanController@clone')->name('5r-system.master-pertanyaan.clone');
        // archive master pertanyaan by id
        Route::post('/archive/archive-pertanyaan', 'System5R\MasterPertanyaanController@archiveDataPertanyaan')->name('5r-system.master-pertanyaan.archiveDataPertanyaan');
        // archive all pertanyaan
        Route::post('/archive/all-archive-pertanyaan', 'System5R\MasterPertanyaanController@archiveAllDataPertanyaan')->name('5r-system.master-pertanyaan.all-pertanyaan');
        // import master pertanyaan
        Route::post('/import-master-pertanyaan', 'System5R\MasterPertanyaanController@importMasterPertanyaan')->name('5r-system.master-pertanyaan.import-master-pertanyaan');
        // get all master comittee
        Route::get('/get-all-data-comittee', 'System5R\Report\CommitteeController@getDataComittee')->name('5r-system.get-data.comittee');
        // store data comittee
        Route::post('/data-comittee/create', 'System5R\Report\CommitteeController@storeDataComittee')->name('5r-system.store-data.comittee');
        // delete data comittee
        Route::post('/data-comittee/delete', 'System5R\Report\CommitteeController@deleteComittee')->name('5r-system.delete-data.comittee');
        // edit data comittee
        Route::post('/data-comittee/update', 'System5R\Report\CommitteeController@editDataComitte')->name('5r-system.update-data.comittee');
        // change status comittee
        Route::post('/data-comittee/ubah-status', 'System5R\Report\CommitteeController@ubahStatusComittee')->name('5r-system.ubah-status.comittee');
        

        // buat master
        // index master juri 
        Route::get('/master-juri', 'System5R\MasterJuriController@index');
        // api data juri
        Route::get('/data-juri', 'System5R\MasterJuriController@dataJuri')->name('5r-system.data.juri');
        // create master group juri 
        Route::post('/master-juri/store', 'System5R\MasterJuriController@storeGroupJuri')->name('5r-system.store.group');
        // create post juri anggota
        Route::post('/master-juri/juri-anggota', 'System5R\MasterJuriController@storeGroupAnggota')->name('5r-system.juri.anggota');
        // create post juri departemen
        Route::post('/master-juri/juri-departemen', 'System5R\MasterJuriController@storeGroupJuriDepartment')->name('5r-system.juri.departemen');
        // create post set status
        Route::post('master-juri/set-status', 'System5R\MasterJuriController@setStatus')->name('5r-system.juri.status');
        // create delete anggota juri
        Route::delete('master-juri/anggota-juri/delete', 'System5R\MasterJuriController@deleteJuri')->name('5r-system.juri.delete');
        // create get anggota juri
        Route::get('master-juri/group-juri/get/{id}', 'System5R\MasterJuriController@getJuri')->name('5r-system.juri.data');


        // index master periode
        Route::get('/master-penilaian', 'System5R\MasterPenilaianController@index');
        // store data periode
        Route::post('/master-penilaian/store', 'System5R\MasterPenilaianController@store')->name('5r-system.store.tahun');
        // buat API get all data periode dan penilaian
        Route::get('/master-penilaian/tahun', 'System5R\MasterPenilaianController@dataPeriode')->name('5r-system.data.periode');
        // Buat store periode penilaian
        Route::post('/master-penilaian/periode-penilaian', 'System5R\MasterPenilaianController@storePenilaian')->name('5r-system.tambah.periode');
        

        // buat master departemen
        // page master departemen index
        Route::get('/master-department', 'System5R\MasterDepartmentController@index')->name('5r-system.master-department');
        // membuat route untuk memisahkan workspace
        Route::post('/master-department/create-workspace', 'System5R\MasterDepartmentController@createWorkspace')->name('5r-system.master-department.create-workspace');
        // membuat route untuk menambahkan departemen
        Route::post('/master-department/store', 'System5R\MasterDepartmentController@store')->name('5r-system.master-department.store');
        // membuat route untuk mengaktifkan departemen
        Route::post('/master-department/aktifkan', 'System5R\MasterDepartmentController@aktifkan')->name('5r-system.master-department.aktifkan');
        // membuat route untuk menonaktifkan departemen
        Route::post('/master-department/nonaktifkan', 'System5R\MasterDepartmentController@nonaktifkan')->name('5r-system.master-department.nonaktifkan');
        // menghapus data department by id
        Route::post('/master-department/hapus-department', 'System5R\MasterDepartmentController@deleteDepartment')->name('5r-system.master-department.hapus');
        // edit department by id
        Route::post('/master-department/edit-department', 'System5R\MasterDepartmentController@editDepartment')->name('5r-system.master-department.edit');

    
        // schedule juri
        // membuat route untuk get view halaman index juri
        Route::get('/schedule-juri', 'System5R\ScheduleJuriController@index');
        // membuat route untuk menambahkan data juri
        Route::post('/schedule-juri/add-juri', 'System5R\ScheduleJuriController@addJuri');


        Route::post('do-submit', 'System5R\PenilaianController@doSubmit')->name('5r-system.do-submit');

        Route::get('validate-credentials-comittee/{id_group}', 'System5R\PenilaianController@validateCredentials')->name('5r-system.validate-comittee');

        Route::get('get-periode-by-id-jadwal/{id_jadwal}', 'System5R\PenilaianController@getPeriode');

        Route::get('report/download/{encryptedInfo}', 'System5R\Report\ManagementController@download');
    });
});
