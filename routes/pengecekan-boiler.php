<?php

use Illuminate\Support\Facades\Route;

Route::prefix('sistem-plc')->group(function () {
    Route::group(['middleware' => ['auth', 'rules', 'access_log']], function () {
        // view
        Route::get('/boiler/home-page', 'PengecekanBoiler\DashboardController@dashboard')->name('boiler.home-page');
        // speedmeter feed water 
        Route::get('/boiler/level-feed-water', 'PengecekanBoiler\SpeedMeterController@speedMeterLevelFeedWater')->name('boiler.level-feed-water');
        // speedmeter stream pressure
        Route::get('/boiler/stream-pressure', 'PengecekanBoiler\SpeedMeterController@speedMeterStreamPressure')->name('boiler.stream-pressure');
        // speedmeter lh temperature
        Route::get('/boiler/lh-temperature', 'PengecekanBoiler\SpeedMeterController@speedMeterLHTemp')->name('boiler.lh-temp');
        // speedmeter RHTemp
        Route::get('/boiler/rh-temperature', 'PengecekanBoiler\SpeedMeterController@speedMeterRHTemp')->name('boiler.rh-temp');
        // speedmeter LH HD FAN
        Route::get('/boiler/LHFDFan', 'PengecekanBoiler\SpeedMeterController@speedMeterLHFDFan')->name('boiler.LHFDFan');
        // speedmeter RH FD Fan
        Route::get('/boiler/RHFDFan', 'PengecekanBoiler\SpeedMeterController@speedMeterRHFDFan')->name('boiler.RHFDFan');
        // speedmeter ID FAN
        Route::get('/boiler/IDFan', 'PengecekanBoiler\SpeedMeterController@speedMeterIDFan')->name('boiler.ID-Fan');
        // speedmeter LH Stocker
        Route::get('/boiler/LHStocker', 'PengecekanBoiler\SpeedMeterController@speedMeterLHStocker')->name('boiler.LH-Stocker');
        // speedmeter RH Stocker
        Route::get('/boiler/RHStocker', 'PengecekanBoiler\SpeedMeterController@speedMeterRHStocker')->name('boiler.RH-Stocker');
        // speedmeter LHGuiloutine
        Route::get('/boiler/LHGuiloutine', 'PengecekanBoiler\SpeedMeterController@speedMeterLHGuiloutine')->name('boiler.speedMeterLHGuiloutine');


        // line chart stream pressure
        Route::get('/boiler/line-chart-pvsteam', 'PengecekanBoiler\lineChartController@linechartPvSteam')->name('boiler.line-chart-pvsteam');
        // line chart level feed water
        Route::get('/boiler/line-chart-feedwater', 'PengecekanBoiler\lineChartController@linechartLevelFeedWater')->name('boiler.line-chart-feedwater');
        // line chart lh temp
        Route::get('/boiler/line-chart-LHTemp', 'PengecekanBoiler\lineChartController@linechartLHTemp')->name('boiler.line-chart-LHTemp');
        // line chart RHtemp
        Route::get('/boiler/line-chart-RHTemp', 'PengecekanBoiler\lineChartController@linechartRHTemp')->name('boiler.line-chart-RHTemp');
        // linechart LH HD FAN
        Route::get('/boiler/line-chart-LHFDFan', 'PengecekanBoiler\lineChartController@linechartLHFDFan')->name('boiler.line-chart-LHFDFan');
        // linechart RH HD FAN
        Route::get('/boiler/line-chart-RHFDFan', 'PengecekanBoiler\lineChartController@linechartRHFDFan')->name('boiler.line-chart-RHFDFan');
        // linechart ID FAN
        Route::get('/boiler/line-chart-IDFan', 'PengecekanBoiler\lineChartController@linechartIDFan')->name('boiler.line-chart-ID-Fan');
        // linechart LHStoker
        Route::get('/boiler/line-chart-LHStoker', 'PengecekanBoiler\lineChartController@linechartLHStoker')->name('boiler.line-chart-LH-Stoker');
        // linechart RHStoker
        Route::get('/boiler/line-chart-RHStoker', 'PengecekanBoiler\lineChartController@linechartRHStoker')->name('boiler.line-chart-RH-Stoker');
    });
});
