<?php

use Illuminate\Support\Facades\Route;

// ==========================================
// POOL QCC ROUTES (INNOVATION & BOOTH CHALLENGE)
// ==========================================
Route::group(['middleware' => ['auth', 'access_log', 'rules:qcc_pool']], function () {
    Route::group(['prefix' => 'pool-qcc', 'as' => 'pool-qcc.'], function () {
        Route::get('/', function () {
            return view('pool_qcc.index');
        })->name('portal-home');

        // Flow Booth: Pilih Booth -> Layar Standby Booth -> Tap ID Card -> Jawab Kuis -> Selesai balik ke Layar Booth
        Route::get('/booths', 'PoolQcc\PoolQccBoothController@index')->name('booths-index');
        Route::get('/booth/{boothId}', 'PoolQcc\PoolQccBoothController@boothStation')->name('booth-station');
        Route::post('/booth/{boothId}/scan-rfid', 'PoolQcc\PoolQccBoothController@scanRfidAtBooth')->name('booth-scan-rfid');
        Route::get('/booth/{boothId}/challenge', 'PoolQcc\PoolQccBoothController@challengeScreen')->name('booth-challenge');
        Route::post('/booth/{boothId}/submit', 'PoolQcc\PoolQccBoothController@submitAnswerAtBooth')->name('booth-submit');
        Route::get('/leaderboard', 'PoolQcc\PoolQccBoothController@leaderboard')->name('leaderboard');
        Route::get('/spin-wheel', 'PoolQcc\PoolQccBoothController@spinWheel')->name('spin-wheel');

        // Compatibility redirects
        Route::get('/kiosk', function () {
            return redirect()->route('pool-qcc.booths-index');
        })->name('kiosk');
        Route::get('/portal', function () {
            return redirect()->route('pool-qcc.booths-index');
        })->name('portal');

        // Penilaian Dewan Juri (Jury Booth Scoring & Recap Pemenang)
        // Dapat diakses oleh Admin Pool QCC maupun Dewan Juri yang login akun BAS
        Route::group(['prefix' => 'jury', 'as' => 'jury.'], function () {
            Route::get('/scoring', 'PoolQcc\PoolQccJuryController@scoringView')->name('scoring');
            Route::get('/api/booths', 'PoolQcc\PoolQccJuryController@getBoothsList')->name('api.booths');
            Route::post('/verify-judge', 'PoolQcc\PoolQccJuryController@verifyJudgeAndGetEvaluation')->name('verify-judge');
            Route::get('/api/scoring-data', 'PoolQcc\PoolQccJuryController@getScoringData')->name('api.scoring-data');
            Route::post('/switch', 'PoolQcc\PoolQccJuryController@switchJudge')->name('switch');
            Route::post('/score', 'PoolQcc\PoolQccJuryController@saveScore')->name('save-score');
            Route::get('/recap', 'PoolQcc\PoolQccJuryController@recapView')->name('recap');
            Route::get('/recap/export', 'PoolQcc\PoolQccJuryController@exportExcel')->name('recap.export');
            Route::get('/api/recap', 'PoolQcc\PoolQccJuryController@getRecapData')->name('api.recap');
        });

        Route::group(['middleware' => ['rules:qcc_pool_admin']], function () {
            // Admin Management (Booth & Soal & Peserta & Dewan Juri)
            Route::group(['prefix' => 'admin', 'as' => 'admin.'], function () {
                Route::get('/', 'PoolQcc\PoolQccAdminController@index')->name('dashboard');
                Route::get('/api/dashboard', 'PoolQcc\PoolQccAdminController@getDashboardData')->name('api.dashboard');

                // Booths
                Route::get('/api/booths', 'PoolQcc\PoolQccAdminController@getBoothsData')->name('api.booths');
                Route::get('/booths', 'PoolQcc\PoolQccAdminController@booths')->name('booths');
                Route::get('/booths/template', 'PoolQcc\PoolQccAdminController@downloadBoothTemplate')->name('booths.template');
                Route::post('/booths/import', 'PoolQcc\PoolQccAdminController@importBooths')->name('booths.import');
                Route::post('/booths', 'PoolQcc\PoolQccAdminController@storeBooth')->name('booths.store');
                Route::put('/booths/{id}', 'PoolQcc\PoolQccAdminController@updateBooth')->name('booths.update');
                Route::delete('/booths/{id}', 'PoolQcc\PoolQccAdminController@destroyBooth')->name('booths.destroy');

                // Questions
                Route::get('/api/questions', 'PoolQcc\PoolQccAdminController@getQuestionsData')->name('api.questions');
                Route::get('/questions', 'PoolQcc\PoolQccAdminController@questions')->name('questions');
                Route::get('/questions/template', 'PoolQcc\PoolQccAdminController@downloadQuestionTemplate')->name('questions.template');
                Route::post('/questions/import', 'PoolQcc\PoolQccAdminController@importQuestions')->name('questions.import');
                Route::post('/questions', 'PoolQcc\PoolQccAdminController@storeQuestion')->name('questions.store');
                Route::put('/questions/{id}', 'PoolQcc\PoolQccAdminController@updateQuestion')->name('questions.update');
                Route::delete('/questions/{id}', 'PoolQcc\PoolQccAdminController@destroyQuestion')->name('questions.destroy');

                // Dewan Juri & Manajemen VIP
                Route::get('/api/judges', 'PoolQcc\PoolQccAdminController@getJudgesData')->name('api.judges');
                Route::get('/judges', 'PoolQcc\PoolQccAdminController@judges')->name('judges');
                Route::post('/judges/lookup', 'PoolQcc\PoolQccAdminController@lookupJudge')->name('judges.lookup');
                Route::post('/judges', 'PoolQcc\PoolQccAdminController@storeJudge')->name('judges.store');
                Route::put('/judges/{id}', 'PoolQcc\PoolQccAdminController@updateJudge')->name('judges.update');
                Route::post('/judges/{id}/toggle', 'PoolQcc\PoolQccAdminController@toggleJudgeStatus')->name('judges.toggle');
                Route::delete('/judges/{id}', 'PoolQcc\PoolQccAdminController@destroyJudge')->name('judges.destroy');


                // Kriteria Penilaian Booth Dewan Juri
                Route::get('/api/criteria', 'PoolQcc\PoolQccAdminController@getCriteriaData')->name('api.criteria');
                Route::get('/criteria', 'PoolQcc\PoolQccAdminController@criteria')->name('criteria');
                Route::post('/criteria', 'PoolQcc\PoolQccAdminController@storeCriterion')->name('criteria.store');
                Route::put('/criteria/{id}', 'PoolQcc\PoolQccAdminController@updateCriterion')->name('criteria.update');
                Route::post('/criteria/{id}/toggle', 'PoolQcc\PoolQccAdminController@toggleCriterionStatus')->name('criteria.toggle');
                Route::delete('/criteria/{id}', 'PoolQcc\PoolQccAdminController@destroyCriterion')->name('criteria.destroy');

                // Participants & Votes Log
                Route::get('/api/participants', 'PoolQcc\PoolQccAdminController@getParticipantsData')->name('api.participants');
                Route::get('/participants', 'PoolQcc\PoolQccAdminController@participants')->name('participants');
                Route::get('/participants/export-votes', 'PoolQcc\PoolQccAdminController@exportVotes')->name('participants.export-votes');
                Route::get('/participants/template', 'PoolQcc\PoolQccAdminController@downloadParticipantTemplate')->name('participants.template');
                Route::post('/participants/import', 'PoolQcc\PoolQccAdminController@importParticipants')->name('participants.import');
                Route::post('/participants/reset-all', 'PoolQcc\PoolQccAdminController@resetAllParticipantsVotes')->name('participants.reset-all');
                Route::post('/participants/{id}/reset', 'PoolQcc\PoolQccAdminController@resetParticipantVotes')->name('participants.reset')->where('id', '[0-9]+');
                Route::delete('/participants/{id}', 'PoolQcc\PoolQccAdminController@destroyParticipant')->name('participants.destroy')->where('id', '[0-9]+');
                Route::get('/api/votes', 'PoolQcc\PoolQccAdminController@getVotesData')->name('api.votes');
                Route::get('/votes', 'PoolQcc\PoolQccAdminController@votes')->name('votes');
                Route::get('/votes/export', 'PoolQcc\PoolQccAdminController@exportVotes')->name('votes.export');

                // Reset Penilaian Dewan Juri
                Route::delete('/jury/evaluations/{id}', 'PoolQcc\PoolQccJuryController@resetEvaluation')->name('jury.evaluations.destroy');
                Route::post('/jury/booths/{boothId}/reset', 'PoolQcc\PoolQccJuryController@resetBoothEvaluations')->name('jury.booths.reset');
                Route::post('/jury/reset-all', 'PoolQcc\PoolQccJuryController@resetAllEvaluations')->name('jury.reset-all');
            });
        });
    });
});
