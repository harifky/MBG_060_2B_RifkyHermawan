<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\GudangController;
use App\Http\Controllers\DapurController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Route default redirect ke login
Route::get('/', function () {
    return redirect()->route('login');
});

// Authentication Routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.process');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Routes untuk Role Gudang (Admin)
Route::middleware(['auth', 'role:gudang'])->prefix('gudang')->name('gudang.')->group(function () {

    // Dashboard
    Route::get('/dashboard', [GudangController::class, 'dashboard'])->name('dashboard');

    Route::prefix('bahan-baku')->name('bahan-baku.')->group(function () {
        Route::get('/', [GudangController::class, 'bahanBaku'])->name('index');
    });

    Route::prefix('permintaan')->name('permintaan.')->group(function () {
        Route::get('/', [GudangController::class, 'permintaan'])->name('index');
    });
});

// Routes untuk Role Dapur
Route::middleware(['auth', 'role:dapur'])->prefix('dapur')->name('dapur.')->group(function () {

    // Dashboard
    Route::get('/dashboard', [DapurController::class, 'dashboard'])->name('dashboard');

    Route::prefix('bahan-baku')->name('bahan-baku.')->group(function () {
        Route::get('/', [DapurController::class, 'bahanBaku'])->name('index');
    });

    Route::prefix('permintaan')->name('permintaan.')->group(function () {
        Route::get('/', [DapurController::class, 'permintaan'])->name('index');
    });
});

// Route untuk handle yang tidak punya akses
Route::fallback(function () {
    if (\Illuminate\Support\Facades\Auth::check()) {
        $user = \Illuminate\Support\Facades\Auth::user();
        if ($user->role === 'gudang') {
            return redirect()->route('gudang.dashboard');
        } elseif ($user->role === 'dapur') {
            return redirect()->route('dapur.dashboard');
        }
    }
    return redirect()->route('login');
});
