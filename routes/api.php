<?php

use App\Http\Controllers\BarrierGate\ParkingTapController;
use App\Http\Controllers\TestController;
use App\Http\Controllers\Api\KantongParkirApiController;
use App\Http\Controllers\Api\SupplierApiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

// Route::get('/polling/sigra-sio', [SioPollingController::class, 'checkSio']);
Route::get('/send-email', [TestController::class, 'sendEmail']);

Route::post('/store-card', [ParkingTapController::class, 'storeCard']);
Route::get('/parking-histories', [ParkingTapController::class, 'getData']);
Route::post('/parking-histories', [ParkingTapController::class, 'parkingHistory']);

Route::get('/supplier-data', [SupplierApiController::class, 'getSupplierData']);

// Kantong Parkir & Slot Status API
Route::prefix('kantong-parkir')->group(function () {
    Route::get('/', [KantongParkirApiController::class, 'index']); // Get all master zones + slots + status kosong/terisi
    Route::get('/slots', [KantongParkirApiController::class, 'getSlots']); // Flat list of slots with vehicle details
    Route::get('/zone/{id}', [KantongParkirApiController::class, 'showZone']); // Get specific zone detail
});

