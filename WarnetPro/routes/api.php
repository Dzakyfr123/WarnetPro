<?php

use App\Http\Controllers\Api\ClientApiController;
use App\Http\Controllers\ClientSessionController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Client API Routes
|--------------------------------------------------------------------------
|
| These routes are used by the WarnetPro client application running on
| each PC. They are stateless (no session/CSRF) and do not require
| authentication.
|
*/

Route::prefix('client')->group(function () {
    // Heartbeat — client sends every 5 seconds
    Route::post('/heartbeat', [ClientApiController::class, 'heartbeat']);

    // Status — client polls every 3 seconds
    Route::get('/status/{pcName}', [ClientApiController::class, 'status']);

    // PC offline notification
    Route::post('/offline', [ClientApiController::class, 'offline']);

    // Command queue (shutdown, restart, message, lock, unlock, screenshot_request)
    Route::get('/commands/{pcName}', [ClientApiController::class, 'getCommands']);
    Route::post('/commands/{id}/ack', [ClientApiController::class, 'acknowledgeCommand']);

    // Screenshot — client upload hasil capture; operator poll untuk ambil URL terbaru
    Route::post('/screenshot/upload', [ClientApiController::class, 'uploadScreenshot']);
    Route::get('/screenshot/{pcName}', [ClientApiController::class, 'getLatestScreenshot']);
});
