<?php

use Illuminate\Support\Facades\Route;

Route::prefix('halo-security')->group(function () {
    //  'https'
    Route::group(['middleware' => ['auth', 'access_log', 'rules']], function () {
        // Menu Dashboard
        Route::get('/', 'HaloSecurity\DashboardController@index')->name('halo-security.dashboard');

        // Menu BA SOP Karyawan List
        Route::get('/ba-sop-karyawan/listkaryawan', 'HaloSecurity\BaSopKaryawanController@index')->name('ba-sop-list-karyawan');
        // Menu Tambah Data BA SOP Karyawan List
        Route::get('/ba-sop-karyawan/createkaryawan', 'HaloSecurity\CreateBaSopKaryawanController@index');
        // Menu Ubah Data BA SOP Karyawan List
        Route::get('/ba-sop-karyawan/editkaryawan/{basopkaryawan_id}', '\App\Http\Livewire\Editbasopkaryawan@render')->name('edit-ba-sop-karyawan');
        // Proses Ubah Data BA SOP Karyawan List
        Route::post('/ba-sop-karyawan/proseseditkaryawan/{id}', '\App\Http\Livewire\Editbasopkaryawan@EditBaSopKaryawan')->name('basopkaryawan.editkaryawan');
        // Cetak satu data karyawan ke dalam pdf
        Route::get('/ba-sop-karyawan/listpdfkaryawan/{id}', '\App\Http\Livewire\Listbasopkaryawan@print_pdf')->name('printpdf.karyawan');
        // Proses Report Excel Karyawan
        Route::get('/export-excel/excelkaryawan', '\App\Http\Livewire\Listbasopkaryawan@exportexcelkaryawan')->name('excel-report-karyawan');

        // Menu BA SOP Supir List
        Route::get('/ba-sop-supir/listsupir', 'HaloSecurity\BaSopSupirController@index')->name('ba-sop-list-supir');
        // Menu Tambah Data BA SOP Supir List
        Route::get('/ba-sop-supir/createsupir', 'HaloSecurity\CreateBaSopSupirController@index');
        // Menu Ubah Data BA SOP Supir List
        Route::get('/ba-sop-supir/editsupir/{basopsupir_id}', '\App\Http\Livewire\Editbasopsupir@render')->name('edit-ba-sop-supir');
        // Proses Ubah Data BA SOP Supir List
        Route::post('/ba-sop-supir/proseseditsupir/{id}', '\App\Http\Livewire\Editbasopsupir@EditBaSopSupir')->name('basopsupir.editsupir');
        // Cetak satu data supir ke dalam pdf
        Route::get('/ba-sop-supir/listpdfsupir/{id}', '\App\Http\Livewire\Listbasopsupir@print_pdf')->name('printpdf.supir');
        // Proses Report Excel Supir
        Route::get('/export-excel/excelsupir', '\App\Http\Livewire\Listbasopsupir@exportexcelsupir')->name('excel-report-supir');

        // Menu BA Laporan Kejadian
        Route::get('/laporan-kejadian/listlaporankejadian', 'HaloSecurity\BaLaporanKejadianController@index')->name('ba-list-laporankejadian');
        // Menu Tambah Data BA Laporan Kejadian
        Route::get('/laporan-kejadian/createlaporankejadian', 'HaloSecurity\CreateBaLaporanKejadianController@index');
        // Proses Tambah Data BA Laporan Kejadian
        Route::post('/laporan-kejadian/prosescreatelaporankejadian', '\App\Http\Livewire\Createbalaporankejadian@AddBaLaporanKejadian')->name('create-laporan-kejadian');
        // Menu Ubah Data BA Laporan Kejadian
        Route::get('/laporan-kejadian/editlaporankejadian/{lk_id}', '\App\Http\Livewire\Editbalaporankejadian@render')->name('edit-laporan-kejadian');
        // Proses Ubah Data BA Laporan Kejadian
        Route::post('/laporan-kejadian/proseseditlaporankejadian/{lk_id}', '\App\Http\Livewire\Editbalaporankejadian@EditBaLaporanKejadian')->name('balaporankejadian.editlaporankejadian');
        // Proses Hapus Data BA Laporan Kejadian
        Route::delete('/laporan-kejadian/deletelaporankejadian/{lk_id}', '\App\Http\Livewire\Listlaporankejadian@deleteLk')->name('hapus-kejadian');
        // Cetak satu data laporan kejadian ke dalam pdf
        Route::get('/laporan-kejadian/listpdflaporankejadian/{lk_id}', '\App\Http\Livewire\Listlaporankejadian@print_pdf')->name('printpdf.laporankejadian');
        // Proses Report Excel Laporan Kejadian
        Route::get('/export-excel/excelkejadian', '\App\Http\Livewire\Listlaporankejadian@exportexcelkejadian')->name('excel-report-kejadian');

        // Menu BA Introgasi
        Route::get('/bai/listintrogasi', 'HaloSecurity\BaIntrogasiController@index')->name('ba-list-introgasi');
        // Menu Tambah Data BA Introgasi
        Route::get('/bai/createintrogasi', 'HaloSecurity\CreateBaIntrogasiController@index')->name('tambah-introgasi');
        // Proses Tambah Data BA Introgasi
        Route::post('/bai/prosescreateintrogasi', '\App\Http\Livewire\Createbaintrogasi@AddBaIntrogasi')->name('create-introgasi');
        // Menu Ubah Data BA Introgasi
        Route::get('/bai/editintrogasi/{bai_id}', '\App\Http\Livewire\Editbaintrogasi@render')->name('edit-introgasi');
        // Proses Ubah Data BA Introgasi
        Route::post('/bai/proseseditintrogasi/{bai_id}', '\App\Http\Livewire\Editbaintrogasi@EditBaIntrogasi')->name('baintrogasi.editintrogasi');
        // Proses Hapus Data BA Introgasi
        Route::delete('/bai/deleteintrogasi/{bai_id}', '\App\Http\Livewire\Listbaintrogasi@deleteBai')->name('hapus-introgasi');
        // Cetak satu data BA Introgasi ke dalam pdf
        Route::get('/bai/listpdfintrogasi/{bai_id}', '\App\Http\Livewire\Listbaintrogasi@print_pdf')->name('printpdf.introgasi');
        // Cetak satu data BA Introgasi satu halaman ke dalam pdf
        Route::get('/bai/listpdfonepageintrogasi/{bai_id}', '\App\Http\Livewire\Listbaintrogasi@print_pdfonepage')->name('printpdfonepage.introgasi');
        // Proses Report Excel BA Introgasi
        Route::get('/export-excel/excelintrogasi', '\App\Http\Livewire\Listbaintrogasi@exportexcelintrogasi')->name('excel-reportbai');
        // Upload Dokumen Satu Halaman
        Route::post('/bai/dokumenttd', '\App\Http\Livewire\Listbaintrogasi@upload_dokumenttd')->name('upload-dokumen-ttd');
        // Proses mengambil data untuk diubah
        Route::get('/bai/introgasi/{bai_id}','\App\Http\Livewire\Listbaintrogasi@get_introgasi');
        // Cetak Dokumen ttd BA Introgasi
        Route::get('/bai/pdfdokumenttd/{bai_id}', '\App\Http\Livewire\Listbaintrogasi@print_dokumenttd')->name('printdokumenttd.introgasi');
        // Read Data Laporan Kejadian
        Route::get('/getKejadian', '\App\Http\Livewire\Createbaintrogasi@getkejadian');
        // Simpan Draft Laporan Kejadian
        Route::post('/bai/prosessimpanlk', '\App\Http\Livewire\Createbaintrogasi@saveDraftIntrogasi')->name('save-draft-introgasi');
        // Preview Draft Laporan Kejadian
        Route::get('/bai/prosespreviewlk/{bai_id}', '\App\Http\Livewire\Createbaintrogasi@previewDraftIntrogasi')->name('preview-draft-introgasi');

        // Datatable CRUD Template Tanya Jawab BA Introgasi
        // Proses read data
        Route::get('/getTemplates', '\App\Http\Livewire\Createbaintrogasi@fetchtemplate');
        // Proses tambah data
        Route::post('/addtemplate', '\App\Http\Livewire\Createbaintrogasi@AddTemplateBaIntrogasi')->name('create-templateba');
        // Proses mengambil data
        Route::post('/getTemplateById', '\App\Http\Livewire\Createbaintrogasi@edit');
        // Proses mengubah data
        Route::post('/updateTemplate', '\App\Http\Livewire\Createbaintrogasi@update');
        // Proses menghapus data
        Route::post('/deleteTemplateById', '\App\Http\Livewire\Createbaintrogasi@destroy');

        // Menu Security User GA
        // Menu List Security User GA
        Route::get('/bai/listsecurityuserga', 'HaloSecurity\SecurityUserGaController@index')->name('security');
        // Menu Tambah Data Security User GA
        Route::get('/bai/createsecurityuserga', 'HaloSecurity\SecurityUserGaController@create')->name('create-security');
        // Proses Tambah Data Security User GA
        Route::post('/bai/storesecurityuserga', 'HaloSecurity\SecurityUserGaController@store')->name('store-security');
        // Menu Ubah Data Security User GA
        Route::get('/bai/edituser/{user_id}', 'HaloSecurity\SecurityUserGaController@edit')->name('edit-security');
        // Proses Ubah Data Security User GA
        Route::post('/bai/updatesecurityuserga/{user_id}', 'HaloSecurity\SecurityUserGaController@update')->name('update.security');
        // Proses Hapus Data Security User GA
        Route::delete('/bai/destroysecurityuserga/{user_id}', 'HaloSecurity\SecurityUserGaController@destroy')->name('destroy-security');
        // Proses Report Excel Security User GA
        Route::get('/export-excel/excelsecurity', 'HaloSecurity\SecurityUserGaController@exportexcelsecurity')->name('excel-report-security');
        // Halaman Cek Pengajuan Izin Karyawan
        Route::get('/cek_pengajuan_izin', 'HaloSecurity\SecurityUserGaController@cek_pengajuan_izin')->name('cek-pengajuan-izin');
        Route::post('/cek_pengajuan_izin/scan', 'HaloSecurity\SecurityUserGaController@proses_scan')->name('proses-pengajuan-izin');
        Route::get('/cek_pengajuan_izin/get-by-nik/{nik}', 'HaloSecurity\SecurityUserGaController@getByNIK')->name('get-pengajuan-izin.get-by-nik');
        Route::get('/json', 'HaloSecurity\SecurityUserGaController@getKaryawan')->name('get-all-karyawan.data');

        // Soft Recycling Delete BA S.O.P Supir
        Route::get('/ba-sop-supir/listtrash', 'HaloSecurity\BaSopSupirController@trash')->name('listsupir.trash');
        Route::get('/ba-sop-supir/listtrash/{id}', 'HaloSecurity\BaSopSupirController@kembalikan')->name('listsupir.kembalikan');
        Route::get('/ba-sop-supir/kembalikan_semua', 'HaloSecurity\BaSopSupirController@kembalikan_semua')->name('listsupir.kembalikan_semua');
        Route::get('/ba-sop-supir/hapus_permanen/{id}', 'HaloSecurity\BaSopSupirController@hapus_permanen')->name('listsupir.hapus_permanen');
        Route::get('/ba-sop-supir/hapus_permanen_semua', 'HaloSecurity\BaSopSupirController@hapus_permanen_semua')->name('listsupir.hapus_permanen_semua');

        // Soft Recycling Delete BA S.O.P Karyawan
        Route::get('/ba-sop-karyawan/listtrash', 'HaloSecurity\BaSopKaryawanController@trash')->name('listkaryawan.trash');
        Route::get('/ba-sop-karyawan/listtrash/{id}', 'HaloSecurity\BaSopKaryawanController@kembalikan')->name('listkaryawan.kembalikan');
        Route::get('/ba-sop-karyawan/kembalikan_semua', 'HaloSecurity\BaSopKaryawanController@kembalikan_semua')->name('listkaryawan.kembalikan_semua');
        Route::get('/ba-sop-karyawan/hapus_permanen/{id}', 'HaloSecurity\BaSopKaryawanController@hapus_permanen')->name('listkaryawan.hapus_permanen');
        Route::get('/ba-sop-karyawan/hapus_permanen_semua', 'HaloSecurity\BaSopKaryawanController@hapus_permanen_semua')->name('listkaryawan.hapus_permanen_semua');

        // Soft Recycling Delete BA Laporan Kejadian
        Route::get('/laporan-kejadian/listtrash', 'HaloSecurity\BaLaporanKejadianController@trash')->name('listlaporankejadian.trash');
        Route::get('/laporan-kejadian/listtrash/{lk_id}', 'HaloSecurity\BaLaporanKejadianController@kembalikan')->name('listlaporankejadian.kembalikan');
        Route::get('/laporan-kejadian/kembalikan_semua', 'HaloSecurity\BaLaporanKejadianController@kembalikan_semua')->name('listlaporankejadian.kembalikan_semua');
        Route::get('/laporan-kejadian/hapus_permanen/{lk_id}', 'HaloSecurity\BaLaporanKejadianController@hapus_permanen')->name('listlaporankejadian.hapus_permanen');
        Route::get('/laporan-kejadian/hapus_permanen_semua', 'HaloSecurity\BaLaporanKejadianController@hapus_permanen_semua')->name('listlaporankejadian.hapus_permanen_semua');

        // Soft Recycling Delete BA Introgasi
        Route::get('/bai/listtrash', 'HaloSecurity\BaIntrogasiController@trash')->name('listbai.trash');
        Route::get('/bai/listtrash/{bai_id}', 'HaloSecurity\BaIntrogasiController@kembalikan')->name('listbai.kembalikan');
        Route::get('/bai/kembalikan_semua', 'HaloSecurity\BaIntrogasiController@kembalikan_semua')->name('listbai.kembalikan_semua');
        Route::get('/bai/hapus_permanen/{bai_id}', 'HaloSecurity\BaIntrogasiController@hapus_permanen')->name('listbai.hapus_permanen');
        Route::get('/bai/hapus_permanen_semua', 'HaloSecurity\BaIntrogasiController@hapus_permanen_semua')->name('listbai.hapus_permanen_semua');

        // Percobaan
        // CRUD Template Pertanyaan dan jawaban BA Introgasi
        // Proses read data
        Route::get('/bai/listtemplate', 'HaloSecurity\TemplateController@index')->name('template');
        // Menu Tambah Data BA Introgasi
        Route::get('/bai/createtemplate', 'HaloSecurity\TemplateController@create')->name('create-template');
        // Proses Tambah Data BA Introgasi
        Route::post('/bai/storetemplate', 'HaloSecurity\TemplateController@store')->name('store-template');
        // Menu Ubah Data BA Introgasi
        Route::get('/bai/edittemplate/{id}', 'HaloSecurity\TemplateController@edit')->name('edit-template');
        // Proses Ubah Data BA Introgasi
        Route::post('/bai/updatetemplate/{id}', 'HaloSecurity\TemplateController@update')->name('update.template');
        // Proses Hapus Data BA Introgasi
        Route::delete('/bai/destroytemplate/{id}', 'HaloSecurity\TemplateController@destroy')->name('destroy-template');

        // Refresh Token Ajax
        Route::post('/keep-token-alive', function() {
            return 'Token must have been valid, and the session expiration has been extended.'; //https://stackoverflow.com/q/31449434/470749
        });

        // Untuk Ajax
        // Route::post('/addtemplate', '\App\Http\Livewire\CreateBaIntrogasi@AddTemplateBaIntrogasi')->name('create-templateba');
        // Route::get('/bai/destroytemplate/{id}', 'HaloSecurity\TemplateController@destroy')->name('destroy-template');
        // Route::get('/edittemplate/{id}', '\App\Http\Livewire\CreateBaIntrogasi@edittemplate');
        // Route::put('/templates', '\App\Http\Livewire\CreateBaIntrogasi@updateTemplate')->name('template.update');

        // Pengecekan
        // Route::get('/ba-sop-karyawan/list', function(){
        //    return 'test';
        // });
    });
});
