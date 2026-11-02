<?php

use Illuminate\Support\Facades\Route;

// ==========================================
// POOL QCC ROUTES (INNOVATION & BOOTH CHALLENGE)
// ==========================================
Route::group(['prefix' => 'pool-qcc', 'as' => 'pool-qcc.'], function () {
    Route::group(['middleware' => ['auth', 'access_log', 'rules']], function () {
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

        // Compatibility redirects
        Route::get('/kiosk', function () {
            return redirect()->route('pool-qcc.booths-index');
        })->name('kiosk');
        Route::get('/portal', function () {
            return redirect()->route('pool-qcc.booths-index');
        })->name('portal');

        // Admin Management (Booth & Soal & Peserta)
        Route::group(['prefix' => 'admin', 'as' => 'admin.'], function () {
            Route::get('/', 'PoolQcc\PoolQccAdminController@index')->name('dashboard');
            Route::get('/api/dashboard', 'PoolQcc\PoolQccAdminController@getDashboardData')->name('api.dashboard');

            // Booths
            Route::get('/api/booths', 'PoolQcc\PoolQccAdminController@getBoothsData')->name('api.booths');
            Route::get('/booths', 'PoolQcc\PoolQccAdminController@booths')->name('booths');
            Route::post('/booths', 'PoolQcc\PoolQccAdminController@storeBooth')->name('booths.store');
            Route::put('/booths/{id}', 'PoolQcc\PoolQccAdminController@updateBooth')->name('booths.update');
            Route::delete('/booths/{id}', 'PoolQcc\PoolQccAdminController@destroyBooth')->name('booths.destroy');

            // Questions
            Route::get('/api/questions', 'PoolQcc\PoolQccAdminController@getQuestionsData')->name('api.questions');
            Route::get('/questions', 'PoolQcc\PoolQccAdminController@questions')->name('questions');
            Route::post('/questions', 'PoolQcc\PoolQccAdminController@storeQuestion')->name('questions.store');
            Route::put('/questions/{id}', 'PoolQcc\PoolQccAdminController@updateQuestion')->name('questions.update');
            Route::delete('/questions/{id}', 'PoolQcc\PoolQccAdminController@destroyQuestion')->name('questions.destroy');

            // Participants & Votes Log
            Route::get('/api/participants', 'PoolQcc\PoolQccAdminController@getParticipantsData')->name('api.participants');
            Route::get('/participants', 'PoolQcc\PoolQccAdminController@participants')->name('participants');
            Route::post('/participants/{id}/reset', 'PoolQcc\PoolQccAdminController@resetParticipantVotes')->name('participants.reset');
            Route::get('/api/votes', 'PoolQcc\PoolQccAdminController@getVotesData')->name('api.votes');
            Route::get('/votes', 'PoolQcc\PoolQccAdminController@votes')->name('votes');
        });
    });
});
