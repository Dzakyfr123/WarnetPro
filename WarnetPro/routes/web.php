<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ComputerController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\PlaySessionController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\NetworkScannerController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Redirect root to dashboard
Route::get('/', function () {
    return redirect()->route('dashboard');
});

// Authenticated routes
Route::middleware('auth')->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Computers — Network Scanner (harus sebelum resource route!)
    Route::get('computers/scanner', [ComputerController::class, 'scanner'])
        ->name('computers.scanner');
    Route::get('computers/scanner/scan', [ComputerController::class, 'scanNetwork'])
        ->name('computers.scan');
    Route::post('computers/scanner/register', [ComputerController::class, 'registerFromScanner'])
        ->name('computers.register');

    // Computers
    Route::resource('computers', ComputerController::class);
    Route::post('computers/{computer}/toggle', [ComputerController::class, 'toggleStatus'])
        ->name('computers.toggle');
    Route::post('computers/{computer}/shutdown', [ComputerController::class, 'shutdown'])
        ->name('computers.shutdown');
    Route::post('computers/{computer}/restart', [ComputerController::class, 'restart'])
        ->name('computers.restart');
    // Operator controls — lock screen & screenshot
    Route::post('computers/{computer}/lock', [ComputerController::class, 'lock'])
        ->name('computers.lock');
    Route::post('computers/{computer}/unlock', [ComputerController::class, 'unlock'])
        ->name('computers.unlock');
    Route::post('computers/{computer}/screenshot', [ComputerController::class, 'requestScreenshot'])
        ->name('computers.screenshot');

    // Bookings
    Route::resource('bookings', BookingController::class);
    Route::post('bookings/{booking}/cancel', [BookingController::class, 'cancel'])
        ->name('bookings.cancel');
    Route::post('bookings/{booking}/start-session', [BookingController::class, 'startSession'])
        ->name('bookings.startSession');

    // Play Sessions
    Route::resource('sessions', PlaySessionController::class);
    Route::post('sessions/{session}/add-time', [PlaySessionController::class, 'addTime'])
        ->name('sessions.addTime');
    Route::post('sessions/{session}/end', [PlaySessionController::class, 'endSession'])
        ->name('sessions.endSession');


    // Network Scanner
    Route::get('/network-scanner', [NetworkScannerController::class, 'index'])->name('network-scanner.index');
    Route::post('/api/network-scan', [NetworkScannerController::class, 'scanNetwork'])->name('api.network-scan');
    Route::post('/api/network-register', [NetworkScannerController::class, 'registerPC'])->name('api.network-register');
    Route::get('/api/server-ip', [NetworkScannerController::class, 'getServerIP'])->name('api.server-ip');

    // API Endpoints (for real-time polling from admin dashboard)
    Route::get('/api/active-sessions', [PlaySessionController::class, 'getActiveSessionsJson'])
        ->name('api.activeSessions');
    Route::get('/api/computer-stats', [DashboardController::class, 'getStatsJson'])
        ->name('api.computerStats');
});

require __DIR__.'/auth.php';