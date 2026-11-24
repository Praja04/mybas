<?php

use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['auth', 'rules', 'access_log']], function () {
    Route::get('/hr/upload-file-template-ipp', 'HR\UploadFileTemplateIppController@index');
    Route::get('/hr/upload-file-template-ipp/download-template', 'HR\UploadFileTemplateIppController@downloadTemplate');
    Route::post('/hr/upload-file-template-ipp/parse', 'HR\UploadFileTemplateIppController@parse');
    Route::post('/hr/upload-file-template-ipp/store', 'HR\UploadFileTemplateIppController@store');
    Route::get('/hr/upload-file-template-ipp/list', 'HR\UploadFileTemplateIppController@list');
    Route::get('/hr/upload-file-template-ipp/{id}', 'HR\UploadFileTemplateIppController@show')->where('id', '[0-9]+');
    Route::post('/hr/upload-file-template-ipp/update/{id}', 'HR\UploadFileTemplateIppController@update')->where('id', '[0-9]+');
    Route::delete('/hr/upload-file-template-ipp/delete/{id}', 'HR\UploadFileTemplateIppController@delete')->where('id', '[0-9]+');

    Route::get('/hr/upload-file-template-ipp/otorisasi/check-nik/{nik}', 'HR\UploadFileTemplateIppController@checkNik');
    Route::get('/hr/upload-file-template-ipp/otorisasi/search-nik', 'HR\UploadFileTemplateIppController@searchNik');
    Route::get('/hr/upload-file-template-ipp/otorisasi/peserta', 'HR\UploadFileTemplateIppController@otorisasiPeserta');
    Route::get('/hr/upload-file-template-ipp/otorisasi/{id}', 'HR\UploadFileTemplateIppController@otorisasiList')->where('id', '[0-9]+');
    Route::post('/hr/upload-file-template-ipp/otorisasi/{id}', 'HR\UploadFileTemplateIppController@otorisasiStore')->where('id', '[0-9]+');
    Route::post('/hr/upload-file-template-ipp/otorisasi/update/{id}', 'HR\UploadFileTemplateIppController@otorisasiUpdate')->where('id', '[0-9]+');
    Route::delete('/hr/upload-file-template-ipp/otorisasi/delete/{id}', 'HR\UploadFileTemplateIppController@otorisasiDelete')->where('id', '[0-9]+');

    Route::get('/hr/upload-file-template-ipp/aktual/{id}', 'HR\UploadFileTemplateIppController@aktualList')->where('id', '[0-9]+');
    Route::get('/hr/upload-file-template-ipp/position-level', 'HR\UploadFileTemplateIppController@positionLevel');
    Route::get('/hr/upload-file-template-ipp/snapshot-options', 'HR\UploadFileTemplateIppController@snapshotOptions');
    Route::get('/hr/upload-file-template-ipp/mapping', 'HR\UploadFileTemplateIppController@mappingList');
    Route::post('/hr/upload-file-template-ipp/mapping', 'HR\UploadFileTemplateIppController@mappingStore');
    Route::delete('/hr/upload-file-template-ipp/mapping/{id}', 'HR\UploadFileTemplateIppController@mappingDelete')->where('id', '[0-9]+');

    Route::get('/hr/upload-file-template-ipp/rubric/download', 'HR\UploadFileTemplateIppForemanController@download');
    Route::get('/hr/upload-file-template-ipp/rubric/status', 'HR\UploadFileTemplateIppForemanController@status');
    Route::get('/hr/upload-file-template-ipp/rubric/{id}', 'HR\UploadFileTemplateIppForemanController@show')->where('id', '[0-9]+');
    Route::post('/hr/upload-file-template-ipp/rubric/parse', 'HR\UploadFileTemplateIppForemanController@parse');
    Route::post('/hr/upload-file-template-ipp/rubric/store', 'HR\UploadFileTemplateIppForemanController@store');

    Route::get('/hr/upload-file-template-ipp-user', 'HR\UploadFileTemplateIppUserController@index');
    Route::get('/hr/upload-file-template-ipp-user/download-template', 'HR\UploadFileTemplateIppUserController@downloadTemplate');
    Route::post('/hr/upload-file-template-ipp-user/parse', 'HR\UploadFileTemplateIppUserController@parse');
    Route::post('/hr/upload-file-template-ipp-user/store', 'HR\UploadFileTemplateIppUserController@store');
    Route::get('/hr/upload-file-template-ipp-user/list', 'HR\UploadFileTemplateIppUserController@list');
    Route::get('/hr/upload-file-template-ipp-user/position-by-dept', 'HR\UploadFileTemplateIppUserController@positionByDept');
    Route::get('/hr/upload-file-template-ipp-user/position-level', 'HR\UploadFileTemplateIppUserController@positionLevel');
    Route::get('/hr/upload-file-template-ipp-user/snapshot-options', 'HR\UploadFileTemplateIppUserController@snapshotOptions');
    Route::get('/hr/upload-file-template-ipp-user/{id}', 'HR\UploadFileTemplateIppUserController@show')->where('id', '[0-9]+');
    Route::post('/hr/upload-file-template-ipp-user/update/{id}', 'HR\UploadFileTemplateIppUserController@update')->where('id', '[0-9]+');
    Route::delete('/hr/upload-file-template-ipp-user/delete/{id}', 'HR\UploadFileTemplateIppUserController@delete')->where('id', '[0-9]+');

    Route::get('/hr/upload-file-template-ipp-user/otorisasi/suggest-atasan', 'HR\UploadFileTemplateIppUserController@suggestAtasan');
    Route::get('/hr/upload-file-template-ipp-user/bawahan', 'HR\UploadFileTemplateIppUserController@bawahan');
    Route::get('/hr/upload-file-template-ipp-user/otorisasi/suggest-peserta', 'HR\UploadFileTemplateIppUserController@suggestPeserta');
    Route::get('/hr/upload-file-template-ipp-user/otorisasi/search-nik', 'HR\UploadFileTemplateIppUserController@searchNik');
    Route::get('/hr/upload-file-template-ipp-user/otorisasi/{id}', 'HR\UploadFileTemplateIppUserController@otorisasiList')->where('id', '[0-9]+');
    Route::post('/hr/upload-file-template-ipp-user/otorisasi/{id}', 'HR\UploadFileTemplateIppUserController@otorisasiStore')->where('id', '[0-9]+');
    Route::post('/hr/upload-file-template-ipp-user/otorisasi/update/{id}', 'HR\UploadFileTemplateIppUserController@otorisasiUpdate')->where('id', '[0-9]+');
    Route::delete('/hr/upload-file-template-ipp-user/otorisasi/delete/{id}', 'HR\UploadFileTemplateIppUserController@otorisasiDelete')->where('id', '[0-9]+');

    Route::get('/hr/upload-file-template-ipp-user/aktual/{id}', 'HR\UploadFileTemplateIppUserController@aktualList')->where('id', '[0-9]+');

    Route::get('/hr/upload-file-template-pk', 'HR\UploadFileTemplatePkController@index');
    Route::get('/hr/upload-file-template-pk/download-template', 'HR\UploadFileTemplatePkController@downloadTemplate');
    Route::post('/hr/upload-file-template-pk/parse', 'HR\UploadFileTemplatePkController@parse');
    Route::post('/hr/upload-file-template-pk/store', 'HR\UploadFileTemplatePkController@store');
    Route::get('/hr/upload-file-template-pk/list', 'HR\UploadFileTemplatePkController@list');
    Route::get('/hr/upload-file-template-pk/participants', 'HR\UploadFileTemplatePkController@participants');
    Route::get('/hr/upload-file-template-pk/participants-history', 'HR\UploadFileTemplatePkController@participantsHistory');
    Route::get('/hr/upload-file-template-pk/participants-history/search-nik', 'HR\UploadFileTemplatePkController@participantsHistorySearchNik');
    Route::post('/hr/upload-file-template-pk/participants-history', 'HR\UploadFileTemplatePkController@participantsHistoryStore');
    Route::post('/hr/upload-file-template-pk/participants-history/update/{id}', 'HR\UploadFileTemplatePkController@participantsHistoryUpdate')->where('id', '[0-9]+');
    Route::delete('/hr/upload-file-template-pk/participants-history/delete/{id}', 'HR\UploadFileTemplatePkController@participantsHistoryDestroy')->where('id', '[0-9]+');
    Route::post('/hr/upload-file-template-pk/update/{id}', 'HR\UploadFileTemplatePkController@update')->where('id', '[0-9]+');
    Route::delete('/hr/upload-file-template-pk/delete/{id}', 'HR\UploadFileTemplatePkController@delete')->where('id', '[0-9]+');
    Route::post('/hr/upload-file-template-pk/{id}/send-notification', 'HR\UploadFileTemplatePkController@sendNotification')->where('id', '[0-9]+');
    Route::get('/hr/upload-file-template-pk/am-mapping', 'HR\UploadFileTemplatePkController@amMappingList');
    Route::get('/hr/upload-file-template-pk/am-mapping/employees', 'HR\UploadFileTemplatePkController@amMappingEmployeeSearch');
    Route::post('/hr/upload-file-template-pk/am-mapping', 'HR\UploadFileTemplatePkController@amMappingStore');
    Route::delete('/hr/upload-file-template-pk/am-mapping/{id}', 'HR\UploadFileTemplatePkController@amMappingDelete')->where('id', '[0-9]+');
    Route::get('/hr/upload-file-template-pk/otorisasi/search-nik', 'HR\UploadFileTemplatePkController@spvSearchNik');
    Route::get('/hr/upload-file-template-pk/otorisasi', 'HR\UploadFileTemplatePkController@spvOtorisasiList');
    Route::post('/hr/upload-file-template-pk/otorisasi', 'HR\UploadFileTemplatePkController@spvOtorisasiStore');
    Route::post('/hr/upload-file-template-pk/otorisasi/update/{id}', 'HR\UploadFileTemplatePkController@spvOtorisasiUpdate')->where('id', '[0-9]+');
    Route::delete('/hr/upload-file-template-pk/otorisasi/delete/{id}', 'HR\UploadFileTemplatePkController@spvOtorisasiDelete')->where('id', '[0-9]+');
    Route::get('/hr/upload-file-template-pk/otorisasi-foreman/search-nik', 'HR\UploadFileTemplatePkController@foremanSearchNik');
    Route::get('/hr/upload-file-template-pk/otorisasi-foreman', 'HR\UploadFileTemplatePkController@foremanOtorisasiList');
    Route::post('/hr/upload-file-template-pk/otorisasi-foreman', 'HR\UploadFileTemplatePkController@foremanOtorisasiStore');
    Route::post('/hr/upload-file-template-pk/otorisasi-foreman/update/{id}', 'HR\UploadFileTemplatePkController@foremanOtorisasiUpdate')->where('id', '[0-9]+');
    Route::delete('/hr/upload-file-template-pk/otorisasi-foreman/delete/{id}', 'HR\UploadFileTemplatePkController@foremanOtorisasiDelete')->where('id', '[0-9]+');
    Route::get('/hr/upload-file-template-pk/{id}', 'HR\UploadFileTemplatePkController@show')->where('id', '[0-9]+');

    // Verifikasi akses PMS. Sengaja di luar group pms.access_otp agar tidak redirect loop.
    Route::get('/hr/pms-access-otp/status', 'HR\PmsAccessOtpController@status')->name('hr.pms-access-otp.status');
    Route::post('/hr/pms-access-otp/send', 'HR\PmsAccessOtpController@send')->name('hr.pms-access-otp.send');
    Route::post('/hr/pms-access-otp/verify', 'HR\PmsAccessOtpController@verify')->name('hr.pms-access-otp.verify');
    Route::get('/hr/pms-access-otp/{requestId}', 'HR\PmsAccessOtpController@show')
        ->where('requestId', '[A-Za-z0-9\-]+')
        ->name('hr.pms-access-otp.show');

    Route::group(['middleware' => ['pms.activity_log']], function () {
        Route::get('/hr/ipp-aktual', 'HR\IppAktualController@index');
        Route::get('/hr/ipp-aktual/list', 'HR\IppAktualController@list');
        Route::get('/hr/ipp-aktual/diskusi-list', 'HR\IppAktualController@diskusiList');
        Route::get('/hr/ipp-aktual/diskusi/{otoId}', 'HR\IppAktualController@diskusiIndex')->where('otoId', '[0-9]+');
        Route::post('/hr/ipp-aktual/diskusi/{otoId}', 'HR\IppAktualController@diskusiStore')->where('otoId', '[0-9]+');
        Route::post('/hr/ipp-aktual/diskusi/update/{id}', 'HR\IppAktualController@diskusiUpdate')->where('id', '[0-9]+');
        Route::get('/hr/ipp-aktual/export/{ippId}', 'HR\IppAktualController@export')->where('ippId', '[0-9]+');
        Route::get('/hr/ipp-aktual/export-pdf/{ippId}', 'HR\IppAktualController@exportPdf')->where('ippId', '[0-9]+');
        Route::get('/hr/ipp-aktual/{id}', 'HR\IppAktualController@show')->where('id', '[0-9]+');
        Route::post('/hr/ipp-aktual/save/{id}', 'HR\IppAktualController@save')->where('id', '[0-9]+');
        Route::post('/hr/ipp-aktual/review/{id}', 'HR\IppAktualController@review')->where('id', '[0-9]+');

        Route::get('/hr/pk-assessment', 'HR\PkAssessmentController@index')
            ->middleware('pms.access_otp:deferred,pk_assessment_page');
        Route::get('/hr/pk-assessment/list', 'HR\PkAssessmentController@list');
        Route::get('/hr/pk-assessment/team-list', 'HR\PkAssessmentController@teamList')
            ->middleware('pms.access_otp:enforce,pk_assessment');
        Route::get('/hr/pk-assessment/diskusi-list', 'HR\PkAssessmentController@diskusiList');
        Route::get('/hr/pk-assessment/diskusi/{pkId}/{nik}', 'HR\PkAssessmentController@diskusiIndex')->where('pkId', '[0-9]+')->where('nik', '[A-Za-z0-9\-]+');
        Route::post('/hr/pk-assessment/diskusi/{pkId}/{nik}', 'HR\PkAssessmentController@diskusiStore')->where('pkId', '[0-9]+')->where('nik', '[A-Za-z0-9\-]+');
        Route::post('/hr/pk-assessment/diskusi/update/{id}', 'HR\PkAssessmentController@diskusiUpdate')->where('id', '[0-9]+');
        Route::post('/hr/pk-assessment/diskusi/answer/{id}', 'HR\PkAssessmentController@diskusiAnswer')->where('id', '[0-9]+');
        Route::get('/hr/pk-assessment/export-pdf/{pkId}/{nik}', 'HR\PkAssessmentController@exportPdf')->where('pkId', '[0-9]+')->where('nik', '[A-Za-z0-9\-]+')
            ->middleware('pms.access_otp:enforce,pk_assessment_export');
        Route::post('/hr/pk-assessment/save/{pkId}', 'HR\PkAssessmentController@save')->where('pkId', '[0-9]+')
            ->middleware('pms.access_otp:enforce,pk_assessment_leader');
        Route::post('/hr/pk-assessment/approve/{pkId}/{nik}', 'HR\PkAssessmentController@approve')->where('pkId', '[0-9]+')->where('nik', '[A-Za-z0-9\-]+')
            ->middleware('pms.access_otp:enforce,pk_assessment');
        Route::get('/hr/pk-assessment/{pkId}', 'HR\PkAssessmentController@show')->where('pkId', '[0-9]+')
            ->middleware('pms.access_otp:enforce,pk_assessment');

        Route::group(['middleware' => ['pms.access_otp:enforce,admin']], function () {
            Route::get('/hr/pk-assessment-hr', 'HR\PkAssessmentHrController@index');
            Route::get('/hr/pk-assessment-hr/list', 'HR\PkAssessmentHrController@list');
            Route::get('/hr/pk-assessment-hr/detail/{pkId}/{nik}', 'HR\PkAssessmentHrController@detail')->where('pkId', '[0-9]+')->where('nik', '[A-Za-z0-9\-]+');
        });

        Route::group(['middleware' => ['pms.access_otp:enforce,pms_total_scoring']], function () {
            Route::get('/hr/pms-total-scoring', 'HR\PmsTotalScoringController@index');
            Route::get('/hr/pms-total-scoring/list', 'HR\PmsTotalScoringController@list');
            Route::get('/hr/pms-total-scoring/export', 'HR\PmsTotalScoringController@export');
            Route::get('/hr/pms-total-scoring/normal-scale', 'HR\PmsTotalScoringController@normalScaleGet');
            Route::post('/hr/pms-total-scoring/normal-scale', 'HR\PmsTotalScoringController@normalScaleSave');
            Route::post('/hr/pms-total-scoring/normal-scale/reset', 'HR\PmsTotalScoringController@normalScaleReset');
            Route::get('/hr/pms-total-scoring/locking-pascale', 'HR\PmsTotalScoringController@lockingPascaleGet');
            Route::post('/hr/pms-total-scoring/locking-pascale', 'HR\PmsTotalScoringController@lockingPascaleSave');
            Route::get('/hr/pms-total-scoring/locking-ipp', 'HR\PmsTotalScoringController@lockingIppGet');
            Route::post('/hr/pms-total-scoring/locking-ipp', 'HR\PmsTotalScoringController@lockingIppSave');
            Route::get('/hr/pms-total-scoring/locking-pk', 'HR\PmsTotalScoringController@lockingPkGet');
            Route::post('/hr/pms-total-scoring/locking-pk', 'HR\PmsTotalScoringController@lockingPkSave');
            Route::post('/hr/pms-total-scoring/notifikasi', 'HR\PmsTotalScoringController@notifikasiSave');
            Route::post('/hr/pms-total-scoring/pa-final', 'HR\PmsTotalScoringController@paFinalSave');
            Route::post('/hr/pms-total-scoring/pa-final-bulk', 'HR\PmsTotalScoringController@paFinalBulkSave');
            Route::get('/hr/pms-total-scoring/export-ipp/{otoId}', 'HR\PmsTotalScoringController@exportIpp')->where('otoId', '[0-9]+');
            Route::get('/hr/pms-total-scoring/export-ipp-foreman/{ippId}/{nik}', 'HR\PmsTotalScoringController@exportIppForeman')->where('ippId', '[0-9]+')->where('nik', '[A-Za-z0-9\-]+');
        });

        Route::get('/hr/ipp-user-approval', 'HR\IppUserApprovalController@index')
            ->middleware('pms.access_otp:deferred,ipp_user_approval_page');
        Route::get('/hr/ipp-user-approval/list', 'HR\IppUserApprovalController@list');
        Route::get('/hr/ipp-user-approval/diskusi-list', 'HR\IppUserApprovalController@diskusiList');
        Route::get('/hr/ipp-user-approval/diskusi/{ippId}', 'HR\IppUserApprovalController@diskusiIndex')->where('ippId', '[0-9]+');
        Route::get('/hr/ipp-user-approval/export-pdf/{otoId}', 'HR\IppUserApprovalController@exportPdf')->where('otoId', '[0-9]+')
            ->middleware('pms.access_otp:enforce,ipp_user_approval_export');
        Route::get('/hr/ipp-user-approval/export-pdf-foreman/{ippId}/{nik}', 'HR\IppUserApprovalController@exportForemanPdf')->where('ippId', '[0-9]+')->where('nik', '[A-Za-z0-9\-]+')
            ->middleware('pms.access_otp:enforce,ipp_user_approval_export');
        Route::post('/hr/ipp-user-approval/diskusi/answer/{id}', 'HR\IppUserApprovalController@diskusiAnswer')->where('id', '[0-9]+');
        Route::post('/hr/ipp-user-approval/diskusi/update/{id}', 'HR\IppUserApprovalController@diskusiAnswerUpdate')->where('id', '[0-9]+');
        Route::get('/hr/ipp-user-approval/{id}', 'HR\IppUserApprovalController@show')->where('id', '[0-9]+')
            ->middleware('pms.access_otp:enforce,ipp_user_approval');
        Route::post('/hr/ipp-user-approval/save-aktual/{id}', 'HR\IppUserApprovalController@saveAktual')->where('id', '[0-9]+')
            ->middleware('pms.access_otp:enforce,ipp_user_approval');
        Route::post('/hr/ipp-user-approval/approve/{id}', 'HR\IppUserApprovalController@approve')->where('id', '[0-9]+')
            ->middleware('pms.access_otp:enforce,ipp_user_approval');

        Route::group(['middleware' => ['pms.access_otp:enforce,admin']], function () {
            Route::get('/hr/ipp-approval', 'HR\IppApprovalController@index');
            Route::get('/hr/ipp-approval/list', 'HR\IppApprovalController@list');
            Route::get('/hr/ipp-approval/diskusi/{ippId}', 'HR\IppApprovalController@diskusiIndex')->where('ippId', '[0-9]+');
            Route::get('/hr/ipp-approval/export-pdf/{otoId}', 'HR\IppApprovalController@exportPdf')->where('otoId', '[0-9]+');
            Route::get('/hr/ipp-approval/export-pdf-foreman/{ippId}/{nik}', 'HR\IppApprovalController@exportForemanPdf')->where('ippId', '[0-9]+')->where('nik', '[A-Za-z0-9\-]+');
            Route::post('/hr/ipp-approval/save-aktual-departemen/{id}', 'HR\IppApprovalController@saveAktualDepartemen')->where('id', '[0-9]+');
            Route::get('/hr/ipp-approval/{id}', 'HR\IppApprovalController@show')->where('id', '[0-9]+');
        });

        // Bukti file IPP (upload: ybs via ipp-aktual, view: ybs/atasan/admin)
        Route::post('/hr/ipp-aktual/bukti/upload', 'HR\HrIppBuktiController@upload');
        Route::get('/hr/ipp-bukti/list', 'HR\HrIppBuktiController@list');
        Route::get('/hr/ipp-bukti/{id}', 'HR\HrIppBuktiController@show')->where('id', '[0-9]+');
        Route::get('/hr/ipp-bukti/{id}/download', 'HR\HrIppBuktiController@download')->where('id', '[0-9]+');
        Route::delete('/hr/ipp-bukti/{id}', 'HR\HrIppBuktiController@delete')->where('id', '[0-9]+');

        // Log aktivitas PMS (audit siapa melihat/mengubah nilai IPP & PK)
        Route::get('/hr/pms-audit-log', 'HR\PmsActivityLogController@index');
        Route::get('/hr/pms-audit-log/list', 'HR\PmsActivityLogController@list');
    });
});
